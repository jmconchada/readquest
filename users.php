<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

require_once 'db.php';

$success = '';
$error = '';

// Handle delete user
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    if ($id !== $_SESSION['user_id']) { // Can't delete yourself
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $success = "User deleted successfully!";
        } else {
            $error = "Failed to delete user";
        }
        $stmt->close();
    } else {
        $error = "You cannot delete yourself!";
    }
}

// Handle toggle role
if (isset($_GET['toggle_role'])) {
    $id = intval($_GET['toggle_role']);
    if ($id !== $_SESSION['user_id']) {
        $conn->query("UPDATE users SET role = IF(role = 'admin', 'user', 'admin') WHERE id = $id");
        $success = "User role updated!";
    } else {
        $error = "You cannot change your own role!";
    }
}

// Get all users
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$role_filter = isset($_GET['role']) ? $_GET['role'] : '';

$sql = "SELECT * FROM users WHERE 1=1";
if ($search) $sql .= " AND (name LIKE '%$search%' OR email LIKE '%$search%')";
if ($role_filter) $sql .= " AND role = '$role_filter'";
$sql .= " ORDER BY created_at DESC";

$result = $conn->query($sql);
$users = [];
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}

// Get stats
$total_users = $conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'user'")->fetch_assoc()['count'];
$total_admins = $conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'admin'")->fetch_assoc()['count'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - ReadQuest Admin</title>
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
            <a href="add_chapter.php" class="nav-item"><i class="fas fa-file-upload"></i><span>Upload Chapters</span></a>
            <a href="users.php" class="nav-item active"><i class="fas fa-users"></i><span>Users</span></a>
        </nav>
        <div class="sidebar-footer">
            <a href="browse.php" class="nav-item"><i class="fas fa-home"></i><span>Back to Website</span></a>
            <a href="logout.php" class="nav-item"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
        </div>
    </div>

    <div class="main-content">
        <div class="top-bar">
            <h1><i class="fas fa-users"></i> Manage Users</h1>
            <div class="user-info">
                <img src="<?php echo htmlspecialchars($_SESSION['profile_picture']); ?>" alt="Profile" class="user-avatar">
                <span><?php echo htmlspecialchars($_SESSION['username']); ?></span>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #3498db, #2980b9);">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $total_users; ?></h3>
                    <p>Total Users</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #e74c3c, #c0392b);">
                    <i class="fas fa-user-shield"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $total_admins; ?></h3>
                    <p>Administrators</p>
                </div>
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
                    <input type="text" name="search" placeholder="Search users..." value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <select name="role" style="padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px;">
                    <option value="">All Roles</option>
                    <option value="user" <?php echo $role_filter === 'user' ? 'selected' : ''; ?>>Users</option>
                    <option value="admin" <?php echo $role_filter === 'admin' ? 'selected' : ''; ?>>Admins</option>
                </select>
                <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Filter</button>
                <a href="users.php" class="btn btn-secondary"><i class="fas fa-redo"></i> Reset</a>
            </form>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Profile</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Joined</th>
                        <th>Last Login</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td><img src="<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Profile" class="user-pic"></td>
                        <td><strong><?php echo htmlspecialchars($user['name']); ?></strong></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><span class="role-badge role-<?php echo $user['role']; ?>"><?php echo ucfirst($user['role']); ?></span></td>
                        <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                        <td><?php echo $user['last_login'] ? date('M d, Y', strtotime($user['last_login'])) : 'Never'; ?></td>
                        <td>
                            <?php if ($user['id'] !== $_SESSION['user_id']): ?>
                            <button onclick="if(confirm('Toggle role for this user?')) window.location.href='?toggle_role=<?php echo $user['id']; ?>'" class="btn-action btn-toggle">
                                <i class="fas fa-user-cog"></i>
                            </button>
                            <button onclick="if(confirm('Delete this user?')) window.location.href='?delete=<?php echo $user['id']; ?>'" class="btn-action btn-delete">
                                <i class="fas fa-trash"></i>
                            </button>
                            <?php else: ?>
                            <span style="color: #95a5a6; font-size: 0.85rem;">You</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>