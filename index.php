<?php
// Start session only if not already started (InfinityFree compatible)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ReadQuest - Your Manga & Comics Destination</title>
    <link rel="stylesheet" href="assets/css/index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="landing-page">
    <section class="hero-landing">
        <div class="hero-background"></div>
        <div class="hero-overlay"></div>
        
        <!-- MangaDex-Style Sticky Navigation -->
        <div class="hero-nav" id="mainNav">
            <div class="logo">
                <h1><i class="fas fa-book-open"></i> ReadQuest</h1>
            </div>
            <nav>
                <a href="index.php" class="active">Home</a>
                <a href="browse.php">Browse</a>
            </nav>
            <div class="user-menu-wrapper">
                <?php 
                $is_logged_in = isset($_SESSION['user_id']);
                $username = $is_logged_in ? $_SESSION['username'] : 'Guest';
                $profile_pic = $is_logged_in && isset($_SESSION['profile_picture']) && !empty($_SESSION['profile_picture']) 
                    ? $_SESSION['profile_picture'] 
                    : 'assets/images/profile_picture.png';
                ?>
                <div class="user-menu-trigger">
                    <img src="<?php echo htmlspecialchars($profile_pic); ?>" alt="Profile" class="profile-pic" onerror="this.src='assets/images/profile_picture.png'">
                    <span class="username-display"><?php echo htmlspecialchars($username); ?></span>
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
        
        <!-- Hero Content -->
        <div class="hero-content-landing">
            <h1>Welcome to ReadQuest</h1>
            <p class="tagline">Your Ultimate Destination for Amazing Stories & Novels</p>
            <p class="description">
                Discover thousands of stories, from action-packed adventures to heartwarming romances. 
                ReadQuest brings you the best stories and novels, all in one place.
            </p>
            <?php if (isset($_SESSION['user_id'])): ?>
            <button class="btn-cta" onclick="window.location.href='browse.php'">
                <i class="fas fa-compass"></i> Start Reading
            </button>
            <?php else: ?>
            <button class="btn-cta" onclick="window.location.href='register.php'">
                <i class="fas fa-rocket"></i> Get Started Free
            </button>
            <?php endif; ?>
        </div>
    </section>

    <main class="landing-main">
        <section class="about-section">
            <h2>What is ReadQuest?</h2>
            <p class="intro-text">
                ReadQuest is your premier online platform for reading amazing stories and novels. 
                We offer a vast collection of stories across all genres, providing readers 
                with an immersive and enjoyable reading experience.
            </p>

            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-book-reader"></i>
                    </div>
                    <h3>Vast Library</h3>
                    <p>Access thousands of amazing stories and novels across multiple genres, from classics to the latest releases.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <h3>Read Anywhere</h3>
                    <p>Enjoy your favorite stories on any device - desktop, tablet, or mobile. Your progress syncs across all devices.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-bookmark"></i>
                    </div>
                    <h3>Personalized Library</h3>
                    <p>Bookmark your favorites, track your reading progress, and get personalized recommendations.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <h3>Quality Content</h3>
                    <p>High-resolution images and optimized reading experience for the best stories and novels enjoyment.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <h3>Latest Updates</h3>
                    <p>Stay up-to-date with new chapters and releases. Never miss an update from your favorite series.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-search"></i>
                    </div>
                    <h3>Easy Discovery</h3>
                    <p>Find new stories easily with our advanced search and filtering system. Explore by genre, rating, and popularity.</p>
                </div>
            </div>
        </section>

        <section class="genres-preview">
            <h2>Explore Popular Genres</h2>
            <div class="genres-grid">
                <a href="browse.php?genre=Slice of Life" class="genre-preview-card" style="background: linear-gradient(135deg, #3498db, #2ecc71);">
                    <i class="fas fa-mug-hot"></i>
                    <span>Slice of Life</span>
                </a>
                <a href="browse.php?genre=Science Fiction" class="genre-preview-card" style="background: linear-gradient(135deg, #3498db, #2980b9);">
                    <i class="fas fa-rocket"></i>
                    <span>Science Fiction</span>
                </a>
                <a href="browse.php?genre=Romance" class="genre-preview-card" style="background: linear-gradient(135deg, #e91e63, #c2185b);">
                    <i class="fas fa-heart"></i>
                    <span>Romance</span>
                </a>
                <a href="browse.php?genre=Mystery" class="genre-preview-card" style="background: linear-gradient(135deg, #1abc9c, #16a085);">
                    <i class="fas fa-search"></i>
                    <span>Mystery</span>
                </a>
                <a href="browse.php?genre=Inspirational" class="genre-preview-card" style="background: linear-gradient(135deg, #f39c12, #e67e22);">
                    <i class="fas fa-lightbulb"></i>
                    <span>Inspirational</span>
                </a>
                <a href="browse.php?genre=Horror" class="genre-preview-card" style="background: linear-gradient(135deg, #34495e, #2c3e50);">
                    <i class="fas fa-ghost"></i>
                    <span>Horror</span>
                </a>
                <a href="browse.php?genre=Historical Fiction" class="genre-preview-card" style="background: linear-gradient(135deg, #e74c3c, #c0392b);">
                    <i class="fas fa-landmark"></i>
                    <span>Historical Fiction</span>
                </a>
                <a href="browse.php?genre=Fantasy" class="genre-preview-card" style="background: linear-gradient(135deg, #9b59b6, #8e44ad);">
                    <i class="fas fa-hat-wizard"></i>
                    <span>Fantasy</span>
                </a>
                <a href="browse.php?genre=Comedy" class="genre-preview-card" style="background: linear-gradient(135deg, #f39c12, #e67e22);">
                    <i class="fas fa-laugh"></i>
                    <span>Comedy</span>
                </a>
                <a href="browse.php?genre=Adventure" class="genre-preview-card" style="background: linear-gradient(135deg, #16a085, #1abc9c);">
                    <i class="fas fa-compass"></i>
                    <span>Adventure</span>
                </a>
            </div>
        </section>

        <section class="cta-section">
            <h2>Ready to Start Your Journey?</h2>
            <p>Join thousands of readers discovering amazing stories every day</p>
            <?php if (isset($_SESSION['user_id'])): ?>
            <button class="btn-cta-secondary" onclick="window.location.href='browse.php'">
                <i class="fas fa-book-open"></i> Browse Stories
            </button>
            <?php else: ?>
            <button class="btn-cta-secondary" onclick="window.location.href='register.php'">
                <i class="fas fa-user-plus"></i> Create Free Account
            </button>
            <?php endif; ?>
        </section>
    </main>

    <footer>
        <div class="footer-content">
            <div class="footer-section">
                <h3>About ReadQuest</h3>
                <p>Your premier destination for amazing stories and novels online. Discover, read, and enjoy thousands of stories.</p>
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

    <script src="assets/js/index.js"></script>
</body>
</html>