<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

require_once 'db.php';

$success = '';
$error = '';

// Get all stories for dropdown
$stories_result = $conn->query("SELECT id, title FROM stories ORDER BY title ASC");
$stories = [];
while ($row = $stories_result->fetch_assoc()) {
    $stories[] = $row;
}

// Get selected story ID
$selected_story = isset($_GET['story_id']) ? intval($_GET['story_id']) : (isset($_POST['story_id']) ? intval($_POST['story_id']) : 0);

// Handle chapter upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_chapter'])) {
    $story_id = intval($_POST['story_id']);
    $chapter_number = floatval($_POST['chapter_number']);
    $chapter_title = trim($_POST['chapter_title']);
    
    if ($story_id && $chapter_number) {
        // Check if chapter already exists
        $check_stmt = $conn->prepare("SELECT id FROM chapters WHERE story_id = ? AND chapter_number = ?");
        $check_stmt->bind_param("id", $story_id, $chapter_number);
        $check_stmt->execute();
        if ($check_stmt->get_result()->num_rows > 0) {
            $error = "Chapter {$chapter_number} already exists for this story!";
        } else {
            // Insert chapter
            $stmt = $conn->prepare("INSERT INTO chapters (story_id, chapter_number, title) VALUES (?, ?, ?)");
            $stmt->bind_param("ids", $story_id, $chapter_number, $chapter_title);
            
            if ($stmt->execute()) {
                $chapter_id = $conn->insert_id;
                
                // Upload pages
                if (isset($_FILES['pages']) && !empty($_FILES['pages']['name'][0])) {
                    $upload_dir = "assets/chapters/story_{$story_id}/chapter_{$chapter_id}/";
                    if (!file_exists($upload_dir)) {
                        mkdir($upload_dir, 0777, true);
                    }
                    
                    // Create array of files with their names for sorting
                    $files_to_upload = [];
                    $total_files = count($_FILES['pages']['name']);
                    
                    for ($i = 0; $i < $total_files; $i++) {
                        if ($_FILES['pages']['error'][$i] === 0) {
                            $files_to_upload[] = [
                                'name' => $_FILES['pages']['name'][$i],
                                'tmp_name' => $_FILES['pages']['tmp_name'][$i],
                                'index' => $i
                            ];
                        }
                    }
                    
                    // Sort files by name (natural sorting for proper numerical order)
                    usort($files_to_upload, function($a, $b) {
                        return strnatcmp($a['name'], $b['name']);
                    });
                    
                    $uploaded_count = 0;
                    $page_number = 1;
                    
                    // Upload files in sorted order
                    foreach ($files_to_upload as $file) {
                        $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                        $filename = "page_" . $page_number . "." . $file_extension;
                        $filepath = $upload_dir . $filename;
                        
                        if (move_uploaded_file($file['tmp_name'], $filepath)) {
                            // Insert page into database
                            $page_stmt = $conn->prepare("INSERT INTO pages (chapter_id, page_number, image_url) VALUES (?, ?, ?)");
                            $page_stmt->bind_param("iis", $chapter_id, $page_number, $filepath);
                            $page_stmt->execute();
                            $page_stmt->close();
                            $uploaded_count++;
                            $page_number++;
                        }
                    }
                    
                    $success = "Chapter {$chapter_number} uploaded successfully with {$uploaded_count} pages!";
                } else {
                    $error = "Please upload at least one page";
                    // Delete the chapter if no pages uploaded
                    $conn->query("DELETE FROM chapters WHERE id = $chapter_id");
                }
            } else {
                $error = "Failed to create chapter: " . $stmt->error;
            }
            $stmt->close();
        }
        $check_stmt->close();
    } else {
        $error = "Please select a story and enter chapter number";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Chapters - ReadQuest Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/admin_theme.css">
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header">
            <i class="fas fa-book-open"></i>
            <h2>ReadQuest Admin</h2>
        </div>
        <nav class="sidebar-nav">
            <a href="admindash.php" class="nav-item"><i class="fas fa-dashboard"></i><span>Dashboard</span></a>
            <a href="add_story.php" class="nav-item"><i class="fas fa-plus-circle"></i><span>Add Story</span></a>
            <a href="manage_stories.php" class="nav-item"><i class="fas fa-book"></i><span>Manage Stories</span></a>
            <a href="add_chapter.php" class="nav-item active"><i class="fas fa-file-upload"></i><span>Upload Chapters</span></a>
            <a href="users.php" class="nav-item"><i class="fas fa-users"></i><span>Users</span></a>
        </nav>
        <div class="sidebar-footer">
            <a href="browse.php" class="nav-item"><i class="fas fa-home"></i><span>Back to Website</span></a>
            <a href="logout.php" class="nav-item"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
        </div>
    </div>

    <div class="main-content">
        <div class="top-bar">
            <h1><i class="fas fa-file-upload"></i> Upload Chapters</h1>
            <div class="user-info">
                <img src="<?php echo htmlspecialchars($_SESSION['profile_picture']); ?>" alt="Profile" class="user-avatar">
                <span><?php echo htmlspecialchars($_SESSION['username']); ?></span>
            </div>
        </div>

        <?php if ($success): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            <span><?php echo htmlspecialchars($success); ?></span>
            <button class="alert-close" onclick="this.parentElement.remove()">×</button>
        </div>
        <?php endif; ?>

        <?php if ($error): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i>
            <span><?php echo htmlspecialchars($error); ?></span>
            <button class="alert-close" onclick="this.parentElement.remove()">×</button>
        </div>
        <?php endif; ?>

        <div class="form-container">
            <form method="POST" enctype="multipart/form-data" id="chapterForm">
                <div class="form-section">
                    <h3><i class="fas fa-info-circle"></i> Chapter Information</h3>
                    
                    <div class="info-box">
                        <i class="fas fa-lightbulb"></i>
                        <strong>Tip:</strong> Upload pages in order. They will be numbered automatically (page_1, page_2, etc.)
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="story_id">Select Story <span class="required">*</span></label>
                            <select id="story_id" name="story_id" required>
                                <option value="">Choose a story...</option>
                                <?php foreach ($stories as $story): ?>
                                <option value="<?php echo $story['id']; ?>" <?php echo $selected_story == $story['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($story['title']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="chapter_number">Chapter Number <span class="required">*</span></label>
                            <input type="number" id="chapter_number" name="chapter_number" step="0.1" placeholder="e.g., 1 or 1.5" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="chapter_title">Chapter Title (Optional)</label>
                        <input type="text" id="chapter_title" name="chapter_title" placeholder="e.g., The Beginning">
                    </div>
                </div>

                <div class="form-section">
                    <h3><i class="fas fa-images"></i> Upload Pages</h3>
                    
                    <div class="upload-zone" id="uploadZone">
                        <input type="file" id="pages" name="pages[]" accept="image/*" multiple hidden>
                        <i class="fas fa-cloud-upload-alt"></i>
                        <h4>Drag & Drop Pages Here</h4>
                        <p>or click to browse (JPG, PNG, WEBP)</p>
                        <button type="button" class="btn-upload" onclick="document.getElementById('pages').click()">
                            Select Pages
                        </button>
                    </div>

                    <div id="previewGrid" class="preview-grid" style="display: none;"></div>
                </div>

                <div style="display: flex; gap: 15px; padding-top: 20px; border-top: 2px solid #e0e0e0;">
                    <button type="submit" name="upload_chapter" class="btn btn-primary">
                        <i class="fas fa-upload"></i> Upload Chapter
                    </button>
                    <button type="reset" class="btn btn-secondary" onclick="clearPreviews()">
                        <i class="fas fa-redo"></i> Reset
                    </button>
                    <a href="admindash.php" class="btn btn-outline">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        const fileInput = document.getElementById('pages');
        const uploadZone = document.getElementById('uploadZone');
        const previewGrid = document.getElementById('previewGrid');
        let selectedFiles = [];

        // Only trigger file input on button click
        document.querySelector('.btn-upload').addEventListener('click', (e) => {
            e.stopPropagation();
            fileInput.click();
        });

        fileInput.addEventListener('change', function() {
            handleFiles(this.files);
        });

        uploadZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            e.stopPropagation();
            uploadZone.classList.add('dragover');
        });

        uploadZone.addEventListener('dragleave', (e) => {
            e.preventDefault();
            e.stopPropagation();
            uploadZone.classList.remove('dragover');
        });

        uploadZone.addEventListener('drop', (e) => {
            e.preventDefault();
            e.stopPropagation();
            uploadZone.classList.remove('dragover');
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                const dt = new DataTransfer();
                Array.from(files).forEach(file => dt.items.add(file));
                fileInput.files = dt.files;
                handleFiles(files);
            }
        });

        function handleFiles(files) {
            selectedFiles = Array.from(files);
            previewGrid.innerHTML = '';
            previewGrid.style.display = 'grid';

            selectedFiles.forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = (e) => {
                    const div = document.createElement('div');
                    div.className = 'preview-item';
                    div.innerHTML = `
                        <img src="${e.target.result}" alt="Page ${index + 1}">
                        <span class="page-num">Page ${index + 1}</span>
                        <button type="button" class="remove-btn" onclick="removePage(${index})">×</button>
                    `;
                    previewGrid.appendChild(div);
                };
                reader.readAsDataURL(file);
            });
        }

        function removePage(index) {
            selectedFiles.splice(index, 1);
            const dt = new DataTransfer();
            selectedFiles.forEach(file => dt.items.add(file));
            fileInput.files = dt.files;
            handleFiles(selectedFiles);
        }

        function clearPreviews() {
            selectedFiles = [];
            previewGrid.innerHTML = '';
            previewGrid.style.display = 'none';
        }

        document.getElementById('chapterForm').addEventListener('submit', function(e) {
            if (selectedFiles.length === 0) {
                e.preventDefault();
                alert('Please upload at least one page!');
                return false;
            }
        });
    </script>
</body>
</html>