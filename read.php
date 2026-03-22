<?php
require_once 'db.php';

// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get story ID and chapter ID from URL
$story_id = isset($_GET['id']) ? intval($_GET['id']) : (isset($_GET['story']) ? intval($_GET['story']) : 0);
$chapter_id = isset($_GET['chapter']) ? intval($_GET['chapter']) : 0;

// If only story_id is provided, get the first chapter
if ($story_id > 0 && $chapter_id === 0) {
    try {
        $first_chapter_query = "SELECT id FROM chapters WHERE story_id = ? ORDER BY chapter_number ASC LIMIT 1";
        $stmt = $conn->prepare($first_chapter_query);
        $stmt->bind_param("i", $story_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $first_chapter = $result->fetch_assoc();
            $chapter_id = $first_chapter['id'];
        } else {
            header('Location: browse.php');
            exit();
        }
    } catch (Exception $e) {
        header('Location: browse.php');
        exit();
    }
}

if ($chapter_id === 0) {
    header('Location: browse.php');
    exit();
}

// Fetch chapter details
try {
    $chapter_query = "SELECT c.*, s.title as story_title, s.cover as story_cover 
                      FROM chapters c 
                      JOIN stories s ON c.story_id = s.id 
                      WHERE c.id = ?";
    $stmt = $conn->prepare($chapter_query);
    $stmt->bind_param("i", $chapter_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        header('Location: browse.php');
        exit();
    }
    
    $chapter = $result->fetch_assoc();
    $story_id = $chapter['story_id'];
} catch (Exception $e) {
    header('Location: browse.php');
    exit();
}

// Fetch all pages for this chapter
$pages = [];
try {
    $pages_query = "SELECT * FROM pages WHERE chapter_id = ? ORDER BY page_number ASC";
    $stmt = $conn->prepare($pages_query);
    $stmt->bind_param("i", $chapter_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $pages[] = $row;
    }
} catch (Exception $e) {
    $pages = [];
}

// Get previous and next chapters
$prev_chapter = null;
$next_chapter = null;

try {
    $prev_query = "SELECT id, chapter_number FROM chapters WHERE story_id = ? AND chapter_number < ? ORDER BY chapter_number DESC LIMIT 1";
    $stmt = $conn->prepare($prev_query);
    $stmt->bind_param("ii", $story_id, $chapter['chapter_number']);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $prev_chapter = $result->fetch_assoc();
    }
    
    $next_query = "SELECT id, chapter_number FROM chapters WHERE story_id = ? AND chapter_number > ? ORDER BY chapter_number ASC LIMIT 1";
    $stmt = $conn->prepare($next_query);
    $stmt->bind_param("ii", $story_id, $chapter['chapter_number']);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $next_chapter = $result->fetch_assoc();
    }
} catch (Exception $e) {
    // Ignore errors
}

// Update view count
try {
    $update_views = "UPDATE chapters SET views = views + 1 WHERE id = ?";
    $stmt = $conn->prepare($update_views);
    $stmt->bind_param("i", $chapter_id);
    $stmt->execute();
} catch (Exception $e) {
    // Ignore errors
}

// Check if user info is set
$is_logged_in = isset($_SESSION['user_id']);
$username = $is_logged_in ? $_SESSION['username'] : 'Guest';
$profile_pic = $is_logged_in && isset($_SESSION['profile_picture']) && !empty($_SESSION['profile_picture']) 
    ? $_SESSION['profile_picture'] 
    : 'assets/images/profile_picture.png';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($chapter['story_title']); ?> - Chapter <?php echo $chapter['chapter_number']; ?> - ReadQuest</title>
    <link rel="stylesheet" href="assets/css/read.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Top Navigation Bar -->
    <div class="reader-nav" id="readerNav">
        <div class="nav-left">
            <button class="nav-btn" onclick="window.location.href='story.php?id=<?php echo $story_id; ?>'">
                <i class="fas fa-arrow-left"></i>
            </button>
            <div class="story-info">
                <div class="story-title"><?php echo htmlspecialchars($chapter['story_title']); ?></div>
                <div class="chapter-title">Chapter <?php echo $chapter['chapter_number']; ?><?php echo !empty($chapter['title']) ? ' - ' . htmlspecialchars($chapter['title']) : ''; ?></div>
            </div>
        </div>
        
        <div class="nav-right">
            <div class="page-indicator" id="pageIndicator">
                Page <span id="currentPage">1</span> / <span id="totalPages"><?php echo count($pages); ?></span>
            </div>
            
            <div class="user-menu-wrapper">
                <div class="user-menu-trigger">
                    <img src="<?php echo htmlspecialchars($profile_pic); ?>" alt="Profile" class="profile-pic" onerror="this.src='assets/images/profile_picture.png'">
                </div>
                
                <div class="reader-dropdown">
                    <div class="dropdown-profile-section">
                        <div class="dropdown-avatar-large">
                            <img src="<?php echo htmlspecialchars($profile_pic); ?>" alt="Profile">
                        </div>
                        <h3 class="dropdown-username"><?php echo htmlspecialchars($username); ?></h3>
                    </div>
                    
                    <div class="dropdown-separator"></div>
                    
                    <div class="dropdown-theme-section">
                        <div class="theme-label">
                            <i class="fas fa-palette"></i>
                            <span>Theme</span>
                        </div>
                        <div class="theme-toggle">
                            <button class="theme-btn" data-theme="light">
                                <i class="fas fa-sun"></i> Light
                            </button>
                            <button class="theme-btn active" data-theme="dark">
                                <i class="fas fa-moon"></i> Dark
                            </button>
                        </div>
                    </div>
                    
                    <div class="dropdown-separator"></div>
                    
                    <?php if ($is_logged_in): ?>
                    <a href="logout.php" class="dropdown-btn-signout">
                        <i class="fas fa-sign-out-alt"></i> Sign Out
                    </a>
                    <?php else: ?>
                    <a href="login.php" class="dropdown-btn-signin">
                        <i class="fas fa-sign-in-alt"></i> Sign In
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Reader Container -->
    <div class="reader-container">
        <?php if (empty($pages)): ?>
        <div class="no-pages">
            <i class="fas fa-inbox"></i>
            <h2>No Pages Available</h2>
            <p>This chapter doesn't have any pages yet.</p>
            <button class="btn-primary" onclick="window.location.href='story.php?id=<?php echo $story_id; ?>'">
                <i class="fas fa-arrow-left"></i> Back to Story
            </button>
        </div>
        <?php else: ?>
        <div class="pages-wrapper" id="pagesWrapper">
            <?php foreach ($pages as $index => $page): ?>

            <!-- Page divider with number -->
            <div class="page-divider">
                <div class="page-divider-line"></div>
                <span class="page-divider-label">Page <?php echo $page['page_number']; ?></span>
                <div class="page-divider-line"></div>
            </div>

            <div class="page-item" data-page="<?php echo $index + 1; ?>">
                <img src="<?php echo htmlspecialchars($page['image_url']); ?>" 
                     alt="Page <?php echo $page['page_number']; ?>" 
                     class="page-image"
                     onerror="this.src='assets/images/placeholder.jpg'">
            </div>

            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- Bottom Navigation -->
    <?php if (!empty($pages)): ?>
    <div class="chapter-nav">
        <button class="chapter-nav-btn" id="prevChapter" <?php echo $prev_chapter ? '' : 'disabled'; ?> 
                onclick="<?php echo $prev_chapter ? "window.location.href='read.php?story={$story_id}&chapter={$prev_chapter['id']}'" : ''; ?>">
            <i class="fas fa-chevron-left"></i> <span>Previous Chapter</span>
        </button>
        
        <button class="chapter-nav-btn btn-chapters" onclick="window.location.href='story.php?id=<?php echo $story_id; ?>'">
            <i class="fas fa-list"></i> <span>All Chapters</span>
        </button>
        
        <button class="chapter-nav-btn" id="nextChapter" <?php echo $next_chapter ? '' : 'disabled'; ?>
                onclick="<?php echo $next_chapter ? "window.location.href='read.php?story={$story_id}&chapter={$next_chapter['id']}'" : ''; ?>">
            <span>Next Chapter</span> <i class="fas fa-chevron-right"></i>
        </button>
    </div>
    <?php endif; ?>

    <script src="assets/js/read.js"></script>
</body>
</html>