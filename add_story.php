<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

require_once 'db.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $author = trim($_POST['author']);
    $description = trim($_POST['description']);
    $genre = $_POST['genre'];
    $status = $_POST['status'];
    $featured = isset($_POST['featured']) ? 1 : 0;
    $trending = isset($_POST['trending']) ? 1 : 0;
    
    if (empty($title) || empty($author) || empty($genre)) {
        $error = "Please fill in all required fields";
    } else {
        if (isset($_FILES['cover']) && $_FILES['cover']['error'] === 0) {
            $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
            $file_type = $_FILES['cover']['type'];
            
            if (in_array($file_type, $allowed_types)) {
                $upload_dir = 'assets/covers/';
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                $file_extension = pathinfo($_FILES['cover']['name'], PATHINFO_EXTENSION);
                $filename = uniqid('cover_') . '.' . $file_extension;
                $filepath = $upload_dir . $filename;
                
                if (move_uploaded_file($_FILES['cover']['tmp_name'], $filepath)) {
                    $stmt = $conn->prepare("INSERT INTO stories (title, author, cover, description, genre, status, featured, trending) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("ssssssii", $title, $author, $filepath, $description, $genre, $status, $featured, $trending);
                    
                    if ($stmt->execute()) {
                        $story_id = $conn->insert_id;
                        $success = "Story added successfully! Story ID: " . $story_id;
                        $_POST = array();
                    } else {
                        $error = "Database error: " . $stmt->error;
                    }
                    $stmt->close();
                } else {
                    $error = "Failed to upload cover image";
                }
            } else {
                $error = "Invalid file type. Only JPG, PNG, and WEBP are allowed";
            }
        } else {
            $error = "Please upload a cover image";
        }
    }
}

$genres = ['Slice of Life', 'Science Fiction', 'Romance', 'Mystery', 'Inspirational', 'Horror', 'Historical Fiction', 'Fantasy', 'Comedy', 'Adventure'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Story - ReadQuest Admin</title>
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
            <a href="add_story.php" class="nav-item active"><i class="fas fa-plus-circle"></i><span>Add Story</span></a>
            <a href="manage_stories.php" class="nav-item"><i class="fas fa-book"></i><span>Manage Stories</span></a>
            <a href="add_chapter.php" class="nav-item"><i class="fas fa-file-upload"></i><span>Upload Chapters</span></a>
            <a href="users.php" class="nav-item"><i class="fas fa-users"></i><span>Users</span></a>
        </nav>
        <div class="sidebar-footer">
            <a href="browse.php" class="nav-item"><i class="fas fa-home"></i><span>Back to Website</span></a>
            <a href="logout.php" class="nav-item"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
        </div>
    </div>

    <div class="main-content">
        <div class="top-bar">
            <h1><i class="fas fa-plus-circle"></i> Add New Story</h1>
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
            <form method="POST" enctype="multipart/form-data" id="storyForm">
                <div class="form-grid">
                    <div class="form-section">
                        <h3><i class="fas fa-info-circle"></i> Story Information</h3>
                        
                        <div class="form-group">
                            <label for="title">Story Title <span class="required">*</span></label>
                            <input type="text" id="title" name="title" placeholder="Enter story title" required>
                        </div>

                        <div class="form-group">
                            <label for="author">Author <span class="required">*</span></label>
                            <input type="text" id="author" name="author" placeholder="Enter author name" required>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="genre">Genre <span class="required">*</span></label>
                                <select id="genre" name="genre" required>
                                    <option value="">Select Genre</option>
                                    <?php foreach ($genres as $g): ?>
                                    <option value="<?php echo $g; ?>"><?php echo $g; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="status">Status <span class="required">*</span></label>
                                <select id="status" name="status" required>
                                    <option value="ongoing">Ongoing</option>
                                    <option value="completed">Completed</option>
                                    <option value="hiatus">Hiatus</option>
                                    <option value="cancelled">Cancelled</option>
                                    <option value="coming soon">Coming Soon</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea id="description" name="description" rows="6" placeholder="Enter story description..."></textarea>
                        </div>

                        <div class="form-group">
                            <label class="checkbox-label">
                                <input type="checkbox" name="featured" id="featured">
                                <span>Feature this story on homepage</span>
                            </label>
                        </div>

                        <div class="form-group">
                            <label class="checkbox-label">
                                <input type="checkbox" name="trending" id="trending">
                                <span>Mark as trending</span>
                            </label>
                        </div>
                    </div>

                    <div class="form-section">
                        <h3><i class="fas fa-image"></i> Cover Image</h3>
                        
                        <div class="upload-area" id="uploadArea">
                            <input type="file" id="cover" name="cover" accept="image/*" required hidden>
                            <div class="upload-content" id="uploadContent">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <h4>Click or Drag to Upload Cover</h4>
                                <p>JPG, PNG or WEBP (Max 5MB)</p>
                                <button type="button" class="btn-browse" onclick="document.getElementById('cover').click()">Browse Files</button>
                            </div>
                            <div class="preview-container" id="previewContainer" style="display: none;">
                                <img id="preview" src="" alt="Cover Preview">
                                <button type="button" class="btn-remove" onclick="removeImage()"><i class="fas fa-times"></i> Remove</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Add Story</button>
                    <button type="reset" class="btn btn-secondary"><i class="fas fa-redo"></i> Reset Form</button>
                    <a href="admindash.php" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        const fileInput = document.getElementById('cover');
        const uploadArea = document.getElementById('uploadArea');
        const uploadContent = document.getElementById('uploadContent');
        const previewContainer = document.getElementById('previewContainer');
        const preview = document.getElementById('preview');

        // Only trigger file input on browse button click
        document.querySelector('.btn-browse').addEventListener('click', (e) => {
            e.stopPropagation();
            fileInput.click();
        });

        fileInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                handleFile(this.files[0]);
            }
        });

        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            e.stopPropagation();
            uploadArea.style.borderColor = '#3498db';
            uploadArea.style.background = 'rgba(52,152,219,0.05)';
        });

        uploadArea.addEventListener('dragleave', (e) => {
            e.preventDefault();
            e.stopPropagation();
            uploadArea.style.borderColor = '#cbd5e0';
            uploadArea.style.background = 'white';
        });

        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            e.stopPropagation();
            uploadArea.style.borderColor = '#cbd5e0';
            uploadArea.style.background = 'white';
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                const dt = new DataTransfer();
                dt.items.add(files[0]);
                fileInput.files = dt.files;
                handleFile(files[0]);
            }
        });

        function handleFile(file) {
            const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
            if (!validTypes.includes(file.type)) {
                alert('Please upload a valid image file (JPG, PNG, or WEBP)');
                return;
            }
            if (file.size > 5 * 1024 * 1024) {
                alert('File size must be less than 5MB');
                return;
            }
            const reader = new FileReader();
            reader.onload = (e) => {
                preview.src = e.target.result;
                uploadContent.style.display = 'none';
                previewContainer.style.display = 'block';
            };
            reader.readAsDataURL(file);
        }

        function removeImage() {
            fileInput.value = '';
            uploadContent.style.display = 'block';
            previewContainer.style.display = 'none';
        }
    </script>
</body>
</html>