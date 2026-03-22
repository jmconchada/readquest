<?php
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

require_once 'db.php';

// Get statistics
$stats = [];

// Total stories
$result = $conn->query("SELECT COUNT(*) as count FROM stories");
$stats['total_stories'] = $result ? $result->fetch_assoc()['count'] : 0;

// Total chapters
$result = $conn->query("SELECT COUNT(*) as count FROM chapters");
$stats['total_chapters'] = $result ? $result->fetch_assoc()['count'] : 0;

// Total users
$result = $conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'user'");
$stats['total_users'] = $result ? $result->fetch_assoc()['count'] : 0;

// Total views
$result = $conn->query("SELECT SUM(views) as total FROM stories");
$stats['total_views'] = $result ? ($result->fetch_assoc()['total'] ?? 0) : 0;

// Recent stories
$recent_stories = [];
$result = $conn->query("SELECT id, title, cover, author, created_at, chapters, views FROM stories ORDER BY created_at DESC LIMIT 5");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $recent_stories[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - ReadQuest</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/admin_theme.css">
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <i class="fas fa-book-open"></i>
            <h2>ReadQuest Admin</h2>
        </div>
        
        <nav class="sidebar-nav">
            <a href="admindash.php" class="nav-item active">
                <i class="fas fa-dashboard"></i>
                <span>Dashboard</span>
            </a>
            <a href="add_story.php" class="nav-item">
                <i class="fas fa-plus-circle"></i>
                <span>Add Story</span>
            </a>
            <a href="manage_stories.php" class="nav-item">
                <i class="fas fa-book"></i>
                <span>Manage Stories</span>
            </a>
            <a href="add_chapter.php" class="nav-item">
                <i class="fas fa-file-upload"></i>
                <span>Upload Chapters</span>
            </a>
            <a href="users.php" class="nav-item">
                <i class="fas fa-users"></i>
                <span>Users</span>
            </a>
            <a href="settings.php" class="nav-item">
                <i class="fas fa-cog"></i>
                <span>Settings</span>
            </a>
        </nav>
        
        <div class="sidebar-footer">
            <a href="browse.php" class="nav-item">
                <i class="fas fa-home"></i>
                <span>Back to Website</span>
            </a>
            <a href="logout.php" class="nav-item">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Bar -->
        <div class="top-bar">
            <h1>Dashboard</h1>
            <div class="user-info">
                <img src="<?php echo htmlspecialchars($_SESSION['profile_picture']); ?>" alt="Profile" class="user-avatar">
                <span><?php echo htmlspecialchars($_SESSION['username']); ?></span>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #667eea, #764ba2);">
                    <i class="fas fa-book"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo number_format($stats['total_stories']); ?></h3>
                    <p>Total Stories</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #f093fb, #f5576c);">
                    <i class="fas fa-list"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo number_format($stats['total_chapters']); ?></h3>
                    <p>Total Chapters</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #4facfe, #00f2fe);">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo number_format($stats['total_users']); ?></h3>
                    <p>Total Users</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #43e97b, #38f9d7);">
                    <i class="fas fa-eye"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo number_format($stats['total_views']); ?></h3>
                    <p>Total Views</p>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="section">
            <h2>Quick Actions</h2>
            <div class="quick-actions">
                <a href="add_story.php" class="action-btn">
                    <i class="fas fa-plus-circle"></i>
                    <span>Add New Story</span>
                </a>
                <a href="add_chapter.php" class="action-btn">
                    <i class="fas fa-file-upload"></i>
                    <span>Upload Chapter</span>
                </a>
                <a href="manage_stories.php" class="action-btn">
                    <i class="fas fa-edit"></i>
                    <span>Manage Stories</span>
                </a>
                <a href="users.php" class="action-btn">
                    <i class="fas fa-user-cog"></i>
                    <span>Manage Users</span>
                </a>
            </div>
        </div>

        <!-- Recent Stories -->
        <div class="section">
            <h2>Recent Stories</h2>
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Cover</th>
                            <th>Title</th>
                            <th>Author</th>
                            <th>Chapters</th>
                            <th>Views</th>
                            <th>Added</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recent_stories)): ?>
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 40px;">
                                <i class="fas fa-inbox" style="font-size: 3rem; color: #ccc; margin-bottom: 10px; display: block;"></i>
                                <p style="color: #999;">No stories yet. <a href="add_story.php">Add your first story!</a></p>
                            </td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($recent_stories as $story): ?>
                        <tr>
                            <td>
                                <img src="<?php echo htmlspecialchars($story['cover']); ?>" alt="Cover" class="table-img">
                            </td>
                            <td><strong><?php echo htmlspecialchars($story['title']); ?></strong></td>
                            <td><?php echo htmlspecialchars($story['author']); ?></td>
                            <td><?php echo $story['chapters']; ?></td>
                            <td><?php echo number_format($story['views']); ?></td>
                            <td><?php echo date('M d, Y', strtotime($story['created_at'])); ?></td>
                            <td>
                                <a href="edit_story.php?id=<?php echo $story['id']; ?>" class="btn-icon" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="add_chapter.php?story_id=<?php echo $story['id']; ?>" class="btn-icon" title="Add Chapter">
                                    <i class="fas fa-plus"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="admindash.js?v=<?php echo time(); ?>"></script>
</body>
</html>