<?php
require_once 'db.php';

// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Handle Review Submission
$review_message = '';
$review_error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        $story_id_post = intval($_POST['story_id']);
        $rating = intval($_POST['rating']);
        $review_text = isset($_POST['review_text']) ? trim($_POST['review_text']) : '';
        
        // Handle optional research metrics - use NULL if empty
        $comprehension_score = (!empty($_POST['comprehension_score']) && $_POST['comprehension_score'] !== '') ? intval($_POST['comprehension_score']) : null;
        $ease_of_reading = (!empty($_POST['ease_of_reading']) && $_POST['ease_of_reading'] !== '') ? intval($_POST['ease_of_reading']) : null;
        $engagement_level = (!empty($_POST['engagement_level']) && $_POST['engagement_level'] !== '') ? intval($_POST['engagement_level']) : null;
        
        // Validate rating
        if ($rating < 1 || $rating > 5) {
            $review_error = "Please select a rating from 1 to 5 stars";
        } else {
            try {
                // Check if user already reviewed
                $check_stmt = $conn->prepare("SELECT id FROM reviews WHERE user_id = ? AND story_id = ?");
                $check_stmt->bind_param("ii", $user_id, $story_id_post);
                $check_stmt->execute();
                $existing = $check_stmt->get_result();
                
                if ($existing->num_rows > 0) {
                    // Update existing review
                    $stmt = $conn->prepare("UPDATE reviews SET rating=?, review_text=?, comprehension_score=?, ease_of_reading=?, engagement_level=?, updated_at=CURRENT_TIMESTAMP WHERE user_id=? AND story_id=?");
                    $stmt->bind_param("isiiiii", $rating, $review_text, $comprehension_score, $ease_of_reading, $engagement_level, $user_id, $story_id_post);
                    
                    if ($stmt->execute()) {
                        $review_message = "Review updated successfully!";
                    } else {
                        $review_error = "Error updating review: " . $stmt->error;
                    }
                } else {
                    // Insert new review
                    $stmt = $conn->prepare("INSERT INTO reviews (user_id, story_id, rating, review_text, comprehension_score, ease_of_reading, engagement_level) VALUES (?, ?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("iiisiii", $user_id, $story_id_post, $rating, $review_text, $comprehension_score, $ease_of_reading, $engagement_level);
                    
                    if ($stmt->execute()) {
                        $review_message = "Review submitted successfully! Thank you for your feedback.";
                    } else {
                        $review_error = "Error submitting review: " . $stmt->error;
                    }
                }
                $stmt->close();
                $check_stmt->close();
            } catch (Exception $e) {
                $review_error = "Database error: " . $e->getMessage();
            }
        }
    } else {
        $review_error = "Please login to submit a review";
    }
}

// Handle Comment Submission
$comment_message = '';
$comment_error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_comment'])) {
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        $story_id_post = intval($_POST['story_id']);
        $comment_text = trim($_POST['comment_text']);
        
        if (empty($comment_text)) {
            $comment_error = "Comment cannot be empty";
        } else {
            try {
                $stmt = $conn->prepare("INSERT INTO comments (user_id, story_id, comment_text) VALUES (?, ?, ?)");
                $stmt->bind_param("iis", $user_id, $story_id_post, $comment_text);
                
                if ($stmt->execute()) {
                    $comment_message = "Comment posted successfully!";
                } else {
                    $comment_error = "Error posting comment: " . $stmt->error;
                }
                $stmt->close();
            } catch (Exception $e) {
                $comment_error = "Database error: " . $e->getMessage();
            }
        }
    } else {
        $comment_error = "Please login to post a comment";
    }
}

// Get story ID from URL
$story_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($story_id === 0) {
    header('Location: browse.php');
    exit();
}

// Fetch story details
try {
    $story_query = "SELECT * FROM stories WHERE id = ?";
    $stmt = $conn->prepare($story_query);
    $stmt->bind_param("i", $story_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        header('Location: browse.php');
        exit();
    }
    
    $story = $result->fetch_assoc();
    
    // Track story view - increment views count
    $update_views = "UPDATE stories SET views = views + 1 WHERE id = ?";
    $view_stmt = $conn->prepare($update_views);
    $view_stmt->bind_param("i", $story_id);
    $view_stmt->execute();
    $view_stmt->close();
    
} catch (Exception $e) {
    header('Location: browse.php');
    exit();
}

// Fetch chapters for this story
$chapters = [];
try {
    $chapters_query = "SELECT * FROM chapters WHERE story_id = ? ORDER BY chapter_number DESC";
    $stmt = $conn->prepare($chapters_query);
    $stmt->bind_param("i", $story_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $chapters[] = $row;
    }
} catch (Exception $e) {
    $chapters = [];
}

// Fetch reviews for this story
$reviews = [];
try {
    $reviews_query = "SELECT r.*, u.name, u.profile_picture FROM reviews r JOIN users u ON r.user_id = u.id WHERE r.story_id = ? ORDER BY r.created_at DESC";
    $stmt = $conn->prepare($reviews_query);
    $stmt->bind_param("i", $story_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $reviews[] = $row;
    }
} catch (Exception $e) {
    $reviews = [];
}

// Fetch comments for this story
$comments = [];
try {
    $comments_query = "SELECT c.*, u.name, u.profile_picture FROM comments c JOIN users u ON c.user_id = u.id WHERE c.story_id = ? AND c.parent_id IS NULL ORDER BY c.created_at DESC";
    $stmt = $conn->prepare($comments_query);
    $stmt->bind_param("i", $story_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $comments[] = $row;
    }
} catch (Exception $e) {
    $comments = [];
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
    <title><?php echo htmlspecialchars($story['title']); ?> - ReadQuest</title>
    <link rel="stylesheet" href="assets/css/story.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <header id="mainHeader">
        <div class="header-container">
            <div class="back-button">
                <button onclick="window.location.href='browse.php'" class="btn-back-nav">
                    <i class="fas fa-arrow-left"></i>
                </button>
            </div>
            <nav style="display: none;">
            </nav>
            <div class="header-actions">
                <div class="user-menu-wrapper">
                    <div class="user-menu-trigger">
                        <img src="<?php echo htmlspecialchars($profile_pic); ?>" alt="Profile" class="profile-pic" onerror="this.src='assets/images/profile_picture.png'">
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    
                    <div class="mangadex-dropdown">
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
                        <a href="register.php" class="dropdown-btn-register">Register</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Story Hero Banner -->
    <section class="story-hero">
        <div class="hero-background" style="background-image: url('<?php echo htmlspecialchars($story['cover']); ?>');"></div>
        <div class="hero-overlay"></div>
        
        <div class="hero-content">
            <div class="story-cover">
                <img src="<?php echo htmlspecialchars($story['cover']); ?>" alt="<?php echo htmlspecialchars($story['title']); ?>" onerror="this.src='assets/images/placeholder.jpg'">
            </div>
            
            <div class="story-details">
                <h1 class="story-title"><?php echo htmlspecialchars($story['title']); ?></h1>
                
                <div class="story-stats">
                    <span class="stat">
                        <i class="fas fa-book"></i> <?php echo htmlspecialchars($story['genre']); ?>
                    </span>
                    <span class="stat">
                        <i class="fas fa-layer-group"></i> <?php echo count($chapters); ?> Chapters
                    </span>
                    <span class="stat">
                        <i class="fas fa-star"></i> <?php echo number_format($story['rating'], 1); ?>
                    </span>
                    <span class="stat">
                        <i class="fas fa-eye"></i> <?php echo number_format($story['views'] ?? 0); ?>
                    </span>
                    <?php if (isset($story['status']) && !empty($story['status'])): ?>
                    <span class="stat status-badge status-<?php echo str_replace(' ', '-', strtolower(trim($story['status']))); ?>">
                        <?php echo strtoupper($story['status']); ?>
                    </span>
                    <?php endif; ?>
                </div>
                
                <div class="story-tags">
                    <?php 
                    // Display genre as tag
                    $genre = htmlspecialchars($story['genre']);
                    echo "<span class='tag'>" . strtoupper($genre) . "</span>";
                    ?>
                </div>
                
                <p class="story-description">
                    <?php 
                    echo isset($story['description']) && !empty($story['description']) 
                        ? htmlspecialchars($story['description']) 
                        : 'A fantasy manga set in a world where magic is controlled by the voice, following the journey of a mute prince.';
                    ?>
                </p>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <main>
        <!-- Tabs Navigation -->
        <div class="tabs-container">
            <div class="tabs">
                <button class="tab active" data-tab="chapters">
                    <i class="fas fa-list"></i> Chapters
                </button>
                <button class="tab" data-tab="details">
                    <i class="fas fa-info-circle"></i> Details
                </button>
                <button class="tab" data-tab="comments">
                    <i class="fas fa-comments"></i> Comments
                </button>
                <button class="tab" data-tab="reviews">
                    <i class="fas fa-star"></i> Reviews
                </button>
            </div>
        </div>

        <!-- Chapters Tab -->
        <div class="tab-content active" id="chapters-tab">
            <div class="chapters-header">
                <div class="sort-controls">
                    <button class="sort-btn active">
                        <i class="fas fa-sort-amount-down"></i> Descending
                    </button>
                </div>
                <div class="chapter-count">
                    <?php echo count($chapters); ?> results
                </div>
            </div>

            <div class="chapters-list">
                <?php if (empty($chapters)): ?>
                <div class="no-chapters">
                    <i class="fas fa-inbox"></i>
                    <h3>No Chapters Yet</h3>
                    <p>Check back soon for new chapters!</p>
                </div>
                <?php else: ?>
                <?php foreach ($chapters as $chapter): ?>
                <div class="chapter-item" onclick="window.location.href='read.php?story=<?php echo $story_id; ?>&chapter=<?php echo $chapter['id']; ?>'">
                    <div class="chapter-thumbnail">
                        <img src="<?php echo htmlspecialchars($chapter['thumbnail'] ?? $story['cover']); ?>" alt="Chapter <?php echo $chapter['chapter_number']; ?>">
                    </div>
                    
                    <div class="chapter-info">
                        <div class="chapter-title">
                            <i class="fas fa-circle"></i>
                            Chapter <?php echo $chapter['chapter_number']; ?><?php echo !empty($chapter['title']) ? ' - ' . htmlspecialchars($chapter['title']) : ''; ?>
                        </div>
                    </div>
                    
                    <div class="chapter-stats">
                        <span><i class="fas fa-eye"></i> <?php echo number_format($chapter['views'] ?? 0); ?></span>
                        <span><i class="fas fa-heart"></i> <?php echo $chapter['likes'] ?? 0; ?></span>
                        <span><i class="fas fa-comment"></i> <?php echo $chapter['comments'] ?? 0; ?></span>
                        <span><i class="fas fa-clock"></i> <?php echo date('M d, Y', strtotime($chapter['created_at'])); ?></span>
                    </div>
                    
                    <button class="chapter-menu">
                        <i class="fas fa-ellipsis-h"></i>
                    </button>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Details Tab -->
        <div class="tab-content" id="details-tab">
            <div class="details-content">
                <h2>Story Information</h2>
                <div class="detail-row">
                    <span class="detail-label">Author:</span>
                    <span class="detail-value"><?php echo htmlspecialchars($story['author'] ?? 'Unknown'); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Genre:</span>
                    <span class="detail-value"><?php echo htmlspecialchars($story['genre']); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Status:</span>
                    <span class="detail-value">
                        <?php 
                        $status = 'ongoing';
                        if (isset($story['status'])) {
                            $status = $story['status'];
                            echo '<span class="status-' . str_replace(' ', '-', strtolower($status)) . '">' . htmlspecialchars(ucfirst($status)) . '</span>';
                        } elseif (isset($story['is_completed'])) {
                            $status = $story['is_completed'] ? 'completed' : 'ongoing';
                            echo '<span class="status-' . $status . '">' . ucfirst($status) . '</span>';
                        } else {
                            echo '<span class="status-ongoing">Ongoing</span>';
                        }
                        ?>
                    </span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Total Chapters:</span>
                    <span class="detail-value"><?php echo count($chapters); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Rating:</span>
                    <span class="detail-value"><i class="fas fa-star"></i> <?php echo number_format($story['rating'], 1); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Views:</span>
                    <span class="detail-value"><?php echo number_format($story['views'] ?? 0); ?></span>
                </div>
            </div>
        </div>

        <!-- Comments Tab -->
        <div class="tab-content" id="comments-tab">
            <div class="comments-section">
                <h2>Comments</h2>
                
                <?php if ($comment_message): ?>
                <div class="success-message"><?php echo htmlspecialchars($comment_message); ?></div>
                <?php endif; ?>
                
                <?php if ($comment_error): ?>
                <div class="error-message"><?php echo htmlspecialchars($comment_error); ?></div>
                <?php endif; ?>
                
                <?php if ($is_logged_in): ?>
                <form method="POST" class="comment-form">
                    <input type="hidden" name="story_id" value="<?php echo $story_id; ?>">
                    <textarea name="comment_text" placeholder="Write a comment..." rows="3" required></textarea>
                    <button type="submit" name="submit_comment" class="btn-submit"><i class="fas fa-paper-plane"></i> Post Comment</button>
                </form>
                <?php else: ?>
                <p style="text-align:center;padding:20px;"><a href="login.php">Login</a> to post a comment</p>
                <?php endif; ?>
                
                <div class="comments-list">
                    <?php if (empty($comments)): ?>
                    <p style="text-align:center;color:#999;padding:40px;">No comments yet. Be the first to comment!</p>
                    <?php else: ?>
                    <?php foreach ($comments as $comment): ?>
                    <div class="comment-item">
                        <img src="<?php echo htmlspecialchars($comment['profile_picture']); ?>" alt="<?php echo htmlspecialchars($comment['name']); ?>" class="comment-avatar">
                        <div class="comment-content">
                            <div class="comment-header">
                                <strong><?php echo htmlspecialchars($comment['name']); ?></strong>
                                <span class="comment-date"><?php echo date('M d, Y', strtotime($comment['created_at'])); ?></span>
                            </div>
                            <p><?php echo nl2br(htmlspecialchars($comment['comment_text'])); ?></p>
                            <div class="comment-actions">
                                <button class="btn-like"><i class="far fa-heart"></i> <?php echo $comment['likes']; ?></button>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Reviews Tab -->
        <div class="tab-content" id="reviews-tab">
            <div class="reviews-section">
                <h2>Reviews & Ratings</h2>
                
                <?php if ($review_message): ?>
                <div class="success-message"><?php echo htmlspecialchars($review_message); ?></div>
                <?php endif; ?>
                
                <?php if ($review_error): ?>
                <div class="error-message"><?php echo htmlspecialchars($review_error); ?></div>
                <?php endif; ?>
                
                <?php if ($is_logged_in): ?>
                <form method="POST" class="review-form">
                    <input type="hidden" name="story_id" value="<?php echo $story_id; ?>">
                    
                    <div class="form-group">
                        <label>Overall Rating <span class="required">*</span></label>
                        <div class="star-rating">
                            <input type="radio" name="rating" value="5" id="star5" required><label for="star5">★</label>
                            <input type="radio" name="rating" value="4" id="star4"><label for="star4">★</label>
                            <input type="radio" name="rating" value="3" id="star3"><label for="star3">★</label>
                            <input type="radio" name="rating" value="2" id="star2"><label for="star2">★</label>
                            <input type="radio" name="rating" value="1" id="star1"><label for="star1">★</label>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Review</label>
                        <textarea name="review_text" rows="4" placeholder="Share your thoughts about this story..."></textarea>
                    </div>
                    
                    <div class="research-metrics">
                        <h4>📚 Help our research (Optional)</h4>
                        <div class="metrics-grid">
                            <div class="metric-group">
                                <label>Comprehension (1-5)</label>
                                <select name="comprehension_score">
                                    <option value="">Select</option>
                                    <option value="5">5 - Fully understood</option>
                                    <option value="4">4 - Understood well</option>
                                    <option value="3">3 - Understood most</option>
                                    <option value="2">2 - Understood some</option>
                                    <option value="1">1 - Didn't understand</option>
                                </select>
                            </div>
                            <div class="metric-group">
                                <label>Ease of Reading (1-5)</label>
                                <select name="ease_of_reading">
                                    <option value="">Select</option>
                                    <option value="5">5 - Very easy</option>
                                    <option value="4">4 - Fairly easy</option>
                                    <option value="3">3 - Moderate</option>
                                    <option value="2">2 - Somewhat difficult</option>
                                    <option value="1">1 - Very difficult</option>
                                </select>
                            </div>
                            <div class="metric-group">
                                <label>Engagement Level (1-5)</label>
                                <select name="engagement_level">
                                    <option value="">Select</option>
                                    <option value="5">5 - Highly engaged</option>
                                    <option value="4">4 - Quite engaged</option>
                                    <option value="3">3 - Moderately engaged</option>
                                    <option value="2">2 - Slightly engaged</option>
                                    <option value="1">1 - Not engaged</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" name="submit_review" class="btn-submit"><i class="fas fa-star"></i> Submit Review</button>
                </form>
                <?php else: ?>
                <p style="text-align:center;padding:20px;"><a href="login.php">Login</a> to write a review</p>
                <?php endif; ?>
                
                <div class="reviews-list">
                    <?php if (empty($reviews)): ?>
                    <p style="text-align:center;color:#999;padding:40px;">No reviews yet. Be the first to review!</p>
                    <?php else: ?>
                    <?php foreach ($reviews as $review): ?>
                    <div class="review-item">
                        <img src="<?php echo htmlspecialchars($review['profile_picture']); ?>" alt="<?php echo htmlspecialchars($review['name']); ?>" class="review-avatar">
                        <div class="review-content">
                            <div class="review-header">
                                <strong><?php echo htmlspecialchars($review['name']); ?></strong>
                                <div class="review-rating">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="fas fa-star<?php echo $i <= $review['rating'] ? '' : ' inactive'; ?>"></i>
                                    <?php endfor; ?>
                                </div>
                                <span class="review-date"><?php echo date('M d, Y', strtotime($review['created_at'])); ?></span>
                            </div>
                            <?php if (!empty($review['review_text'])): ?>
                            <p><?php echo nl2br(htmlspecialchars($review['review_text'])); ?></p>
                            <?php endif; ?>
                            <?php if ($review['comprehension_score'] || $review['ease_of_reading'] || $review['engagement_level']): ?>
                            <div class="review-metrics">
                                <?php if ($review['comprehension_score']): ?>
                                <span><i class="fas fa-brain"></i> Comprehension: <?php echo $review['comprehension_score']; ?>/5</span>
                                <?php endif; ?>
                                <?php if ($review['ease_of_reading']): ?>
                                <span><i class="fas fa-book-open"></i> Ease: <?php echo $review['ease_of_reading']; ?>/5</span>
                                <?php endif; ?>
                                <?php if ($review['engagement_level']): ?>
                                <span><i class="fas fa-fire"></i> Engagement: <?php echo $review['engagement_level']; ?>/5</span>
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <footer>
        <div class="footer-content">
            <div class="footer-section">
                <h3>About ReadQuest</h3>
                <p>Your premier destination for manga and comics online.</p>
            </div>
            <div class="footer-section">
                <h3>Follow Us</h3>
                <div class="social-links">
                    <a href="https://www.facebook.com/Anaxoxo27?mibextid=ZbWKwL" target="_blank"><i class="fab fa-facebook"></i></a>
                    <a href="https://www.instagram.com/aliyaazsq?igsh=bWhxaDN2cDdrYTZ0" target="_blank"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2025 ReadQuest. All rights reserved.</p>
        </div>
    </footer>

    <script src="assets/js/story.js"></script>
</body>
</html>