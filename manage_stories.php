<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

require_once 'db.php';

$success = '';
$error = '';

// Handle edit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_story'])) {
    $id = intval($_POST['story_id']);
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
        // Check if new cover uploaded
        if (isset($_FILES['cover']) && $_FILES['cover']['error'] === 0) {
            $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
            $file_type = $_FILES['cover']['type'];
            
            if (in_array($file_type, $allowed_types)) {
                $upload_dir = 'assets/covers/';
                $file_extension = pathinfo($_FILES['cover']['name'], PATHINFO_EXTENSION);
                $filename = uniqid('cover_') . '.' . $file_extension;
                $filepath = $upload_dir . $filename;
                
                if (move_uploaded_file($_FILES['cover']['tmp_name'], $filepath)) {
                    // Delete old cover
                    $old_cover_stmt = $conn->prepare("SELECT cover FROM stories WHERE id = ?");
                    $old_cover_stmt->bind_param("i", $id);
                    $old_cover_stmt->execute();
                    $old_result = $old_cover_stmt->get_result();
                    $old_story = $old_result->fetch_assoc();
                    if ($old_story && file_exists($old_story['cover'])) {
                        unlink($old_story['cover']);
                    }
                    $old_cover_stmt->close();
                    
                    $stmt = $conn->prepare("UPDATE stories SET title=?, author=?, cover=?, description=?, genre=?, status=?, featured=?, trending=? WHERE id=?");
                    $stmt->bind_param("sssssssii", $title, $author, $filepath, $description, $genre, $status, $featured, $trending, $id);
                } else {
                    $error = "Failed to upload new cover";
                }
            } else {
                $error = "Invalid file type";
            }
        } else {
            // Update without changing cover
            $stmt = $conn->prepare("UPDATE stories SET title=?, author=?, description=?, genre=?, status=?, featured=?, trending=? WHERE id=?");
            $stmt->bind_param("ssssssii", $title, $author, $description, $genre, $status, $featured, $trending, $id);
        }
        
        if (isset($stmt) && $stmt->execute()) {
            $success = "Story updated successfully!";
        } elseif (!isset($stmt) && !$error) {
            $error = "Failed to update story";
        }
        if (isset($stmt)) $stmt->close();
    }
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("SELECT cover FROM stories WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $story = $result->fetch_assoc();
    
    if ($story) {
        $delete_stmt = $conn->prepare("DELETE FROM stories WHERE id = ?");
        $delete_stmt->bind_param("i", $id);
        if ($delete_stmt->execute()) {
            if (file_exists($story['cover'])) unlink($story['cover']);
            $success = "Story deleted successfully!";
        } else {
            $error = "Failed to delete story";
        }
        $delete_stmt->close();
    }
    $stmt->close();
}

// Handle toggle featured/trending
if (isset($_GET['toggle_featured'])) {
    $id = intval($_GET['toggle_featured']);
    $conn->query("UPDATE stories SET featured = NOT featured WHERE id = $id");
    $success = "Featured status updated!";
}

if (isset($_GET['toggle_trending'])) {
    $id = intval($_GET['toggle_trending']);
    $conn->query("UPDATE stories SET trending = NOT trending WHERE id = $id");
    $success = "Trending status updated!";
}

// Get all stories
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$genre_filter = isset($_GET['genre']) ? $_GET['genre'] : '';

$sql = "SELECT * FROM stories WHERE 1=1";
if ($search) $sql .= " AND (title LIKE '%$search%' OR author LIKE '%$search%')";
if ($genre_filter) $sql .= " AND genre = '$genre_filter'";
$sql .= " ORDER BY created_at DESC";

$result = $conn->query($sql);
$stories = [];
while ($row = $result->fetch_assoc()) {
    // Set default status to 'coming soon' if empty or NULL
    if (empty($row['status'])) {
        $row['status'] = 'coming soon';
    }
    $stories[] = $row;
}

$genres = ['Slice of Life', 'Science Fiction', 'Romance', 'Mystery', 'Inspirational', 'Horror', 'Historical Fiction', 'Fantasy', 'Comedy', 'Adventure'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Stories - ReadQuest Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/admin_theme.css">
    <style>
        /* Ensure coming soon badge works - complete override */
        .status.status-coming-soon {
            display: inline-block !important;
            padding: 4px 10px !important;
            border-radius: 12px !important;
            font-size: 11px !important;
            font-weight: 600 !important;
            text-transform: uppercase !important;
            margin-top: 6px !important;
            background: rgba(168, 85, 247, 0.15) !important;
            color: #c084fc !important;
            border: 1px solid rgba(168, 85, 247, 0.3) !important;
        }
    </style>
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
            <a href="manage_stories.php" class="nav-item active"><i class="fas fa-book"></i><span>Manage Stories</span></a>
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
            <h1><i class="fas fa-book"></i> Manage Stories</h1>
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

        <div class="filters-container">
            <form method="GET" class="filters-form">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" placeholder="Search..." value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <select name="genre" class="filter-select">
                    <option value="">All Genres</option>
                    <?php foreach ($genres as $g): ?>
                    <option value="<?php echo $g; ?>" <?php echo $genre_filter === $g ? 'selected' : ''; ?>><?php echo $g; ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Filter</button>
                <a href="manage_stories.php" class="btn btn-secondary"><i class="fas fa-redo"></i> Reset</a>
                <a href="add_story.php" class="btn btn-success"><i class="fas fa-plus"></i> Add New</a>
            </form>
        </div>

        <div class="stories-container">
            <?php if (empty($stories)): ?>
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <h3>No Stories Found</h3>
                <p>Start by adding your first story!</p>
                <a href="add_story.php" class="btn btn-primary"><i class="fas fa-plus-circle"></i> Add Story</a>
            </div>
            <?php else: ?>
            <div class="stories-grid">
                <?php foreach ($stories as $story): ?>
                <div class="story-card">
                    <div class="story-image">
                        <img src="<?php echo htmlspecialchars($story['cover']); ?>" alt="<?php echo htmlspecialchars($story['title']); ?>">
                        <div class="story-badges">
                            <?php if ($story['featured']): ?><span class="badge badge-featured">Featured</span><?php endif; ?>
                            <?php if ($story['trending']): ?><span class="badge badge-trending">Trending</span><?php endif; ?>
                            <?php if ($story['is_new']): ?><span class="badge badge-new">New</span><?php endif; ?>
                        </div>
                    </div>
                    <div class="story-info">
                        <h3><?php echo htmlspecialchars($story['title']); ?></h3>
                        <p><i class="fas fa-user"></i> <?php echo htmlspecialchars($story['author']); ?></p>
                        <p><i class="fas fa-tag"></i> <?php echo htmlspecialchars($story['genre']); ?></p>
                        <div class="story-stats">
                            <span><i class="fas fa-book"></i> <?php echo $story['chapters']; ?></span>
                            <span><i class="fas fa-eye"></i> <?php echo number_format($story['views']); ?></span>
                            <span><i class="fas fa-star"></i> <?php echo number_format($story['rating'], 1); ?></span>
                        </div>
                        <?php 
                        // Handle empty or NULL status
                        $status = !empty($story['status']) ? $story['status'] : 'coming soon';
                        $status_class = str_replace(' ', '-', strtolower(trim($status)));
                        $status_display = ucwords(trim($status));
                        ?>
                        <span class="status status-<?php echo $status_class; ?>" style="<?php if($status_class === 'coming-soon') echo 'background:rgba(168,85,247,0.15)!important;color:#c084fc!important;border:1px solid rgba(168,85,247,0.3)!important;'; ?>"><?php echo $status_display; ?></span>
                    </div>
                    <div class="story-actions">
                        <button onclick="openEditModal(<?php echo htmlspecialchars(json_encode($story)); ?>)" class="btn-action btn-edit" title="Edit"><i class="fas fa-edit"></i></button>
                        <a href="add_chapter.php?story_id=<?php echo $story['id']; ?>" class="btn-action btn-chapter" title="Add Chapter"><i class="fas fa-plus"></i></a>
                        <a href="?toggle_featured=<?php echo $story['id']; ?>" class="btn-action btn-feature" title="Toggle Featured"><i class="fas fa-star"></i></a>
                        <button onclick="if(confirm('Delete this story?')) window.location.href='?delete=<?php echo $story['id']; ?>'" class="btn-action btn-delete" title="Delete"><i class="fas fa-trash"></i></button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal" id="editModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-edit"></i> Edit Story</h2>
                <button class="modal-close" onclick="closeEditModal()">×</button>
            </div>
            <form method="POST" enctype="multipart/form-data" id="editForm">
                <input type="hidden" name="edit_story" value="1">
                <input type="hidden" name="story_id" id="edit_story_id">
                <div class="modal-body">
                    <div class="form-grid">
                        <div>
                            <div class="form-group">
                                <label for="edit_title">Story Title <span class="required">*</span></label>
                                <input type="text" id="edit_title" name="title" required>
                            </div>

                            <div class="form-group">
                                <label for="edit_author">Author <span class="required">*</span></label>
                                <input type="text" id="edit_author" name="author" required>
                            </div>

                            <div class="form-group">
                                <label for="edit_genre">Genre <span class="required">*</span></label>
                                <select id="edit_genre" name="genre" required>
                                    <?php foreach ($genres as $g): ?>
                                    <option value="<?php echo $g; ?>"><?php echo $g; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="edit_status">Status <span class="required">*</span></label>
                                <select id="edit_status" name="status" required>
                                    <option value="ongoing">Ongoing</option>
                                    <option value="completed">Completed</option>
                                    <option value="hiatus">Hiatus</option>
                                    <option value="cancelled">Cancelled</option>
                                    <option value="coming soon">Coming Soon</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="checkbox-label">
                                    <input type="checkbox" name="featured" id="edit_featured">
                                    <span>Feature this story</span>
                                </label>
                            </div>

                            <div class="form-group">
                                <label class="checkbox-label">
                                    <input type="checkbox" name="trending" id="edit_trending">
                                    <span>Mark as trending</span>
                                </label>
                            </div>
                        </div>

                        <div>
                            <div class="form-group">
                                <label for="edit_description">Description</label>
                                <textarea id="edit_description" name="description" rows="5"></textarea>
                            </div>

                            <div class="form-group">
                                <label for="edit_cover">Cover Image (optional - leave empty to keep current)</label>
                                <input type="file" id="edit_cover" name="cover" accept="image/*">
                            </div>

                            <div class="cover-preview">
                                <label>Current Cover:</label>
                                <div><img id="current_cover" src="" alt="Current Cover"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Changes</button>
                    <button type="button" class="btn btn-secondary" onclick="closeEditModal()"><i class="fas fa-times"></i> Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openEditModal(story) {
            document.getElementById('edit_story_id').value = story.id;
            document.getElementById('edit_title').value = story.title;
            document.getElementById('edit_author').value = story.author;
            document.getElementById('edit_genre').value = story.genre;
            document.getElementById('edit_status').value = story.status;
            document.getElementById('edit_description').value = story.description || '';
            document.getElementById('edit_featured').checked = story.featured == 1;
            document.getElementById('edit_trending').checked = story.trending == 1;
            document.getElementById('current_cover').src = story.cover;
            document.getElementById('editModal').classList.add('active');
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.remove('active');
            document.getElementById('editForm').reset();
        }

        // Close modal when clicking outside
        document.getElementById('editModal').addEventListener('click', function(e) {
            if (e.target === this) closeEditModal();
        });
    </script>
</body>
</html>