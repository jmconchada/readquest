<?php
// Start session only if not already started (InfinityFree compatible)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'db.php';

// Set default username and profile picture for guests
if (!isset($_SESSION['username'])) {
    $_SESSION['username'] = 'Guest';
}

if (!isset($_SESSION['profile_picture'])) {
    $_SESSION['profile_picture'] = 'assets/images/profile_picture.png';
}

// Set variables for use in template
$is_logged_in = isset($_SESSION['user_id']);
$username = $is_logged_in ? $_SESSION['username'] : 'Guest';
$profile_pic = $is_logged_in && isset($_SESSION['profile_picture']) && !empty($_SESSION['profile_picture']) 
    ? $_SESSION['profile_picture'] 
    : 'assets/images/profile_picture.png';

// Fetch stories from database
try {
    $featured_query = "SELECT id, title, cover, genre, chapters, rating, author, status FROM stories WHERE featured = 1 ORDER BY rating DESC";
    $featured_result = $conn->query($featured_query);
    $featured_stories = [];
    if ($featured_result && $featured_result->num_rows > 0) {
        while ($row = $featured_result->fetch_assoc()) {
            $featured_stories[] = $row;
        }
    }
} catch (Exception $e) {
    $featured_stories = [];
}

try {
    $trending_query = "SELECT id, title, cover, genre, chapters, rating, author, status FROM stories WHERE trending = 1 ORDER BY views DESC, rating DESC LIMIT 8";
    $trending_result = $conn->query($trending_query);
    $trending_stories = [];
    if ($trending_result && $trending_result->num_rows > 0) {
        while ($row = $trending_result->fetch_assoc()) {
            $trending_stories[] = $row;
        }
    }
} catch (Exception $e) {
    $trending_stories = [];
}

try {
    $new_query = "SELECT id, title, cover, genre, chapters, rating, author, status FROM stories WHERE is_new = 1 ORDER BY created_at DESC LIMIT 4";
    $new_result = $conn->query($new_query);
    $new_releases = [];
    if ($new_result && $new_result->num_rows > 0) {
        while ($row = $new_result->fetch_assoc()) {
            $new_releases[] = $row;
        }
    }
} catch (Exception $e) {
    $new_releases = [];
}

// Get genre filter from URL
$genre_filter = isset($_GET['genre']) ? trim($_GET['genre']) : '';

// Fetch all stories (for genre filtering)
$all_stories = [];
try {
    $all_query = "SELECT id, title, cover, genre, chapters, rating, author, status FROM stories ORDER BY created_at DESC";
    $all_result = $conn->query($all_query);
    if ($all_result && $all_result->num_rows > 0) {
        while ($row = $all_result->fetch_assoc()) {
            $all_stories[] = $row;
        }
    }
} catch (Exception $e) {
    $all_stories = [];
}

// Function to normalize genre for comparison
function normalizeGenre($genre) {
    return strtolower(str_replace([' ', '-'], '', $genre));
}

// Filter stories by genre if needed
$genre_stories = [];
if ($genre_filter) {
    $genre_stories = array_filter($all_stories, function($story) use ($genre_filter) {
        return normalizeGenre($story['genre']) === normalizeGenre($genre_filter);
    });
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse - ReadQuest</title>
    <link rel="stylesheet" href="assets/css/browse.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <header id="mainHeader">
        <div class="header-container">
            <div class="logo">
                <h1 onclick="window.location.href='index.php'" style="cursor: pointer;">
                    <i class="fas fa-book-open"></i> ReadQuest
                </h1>
            </div>
            <nav>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="browse.php" class="active">Browse</a></li>
                    <?php if ($is_logged_in): ?>
                    
                    <?php endif; ?>
                </ul>
            </nav>
            <div class="header-actions">
                <div class="search-box">
                    <input type="text" id="searchInput" placeholder="Search stories..." autocomplete="off">
                    <button class="search-btn" id="searchBtn" onclick="performSearch()">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
                
                <div class="user-menu-wrapper">
                    <img src="<?php echo htmlspecialchars($profile_pic); ?>" alt="Profile" class="profile-pic" onerror="this.src='assets/images/profile_picture.png'">
                    
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
                        
                        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id']): ?>
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

    <!-- Story Carousel (MangaDex Style) -->
    <section class="story-carousel-section">
        <?php if (!empty($featured_stories)): ?>
        <div class="carousel-wrapper">
            <?php foreach ($featured_stories as $index => $story): ?>
            <div class="carousel-slide <?php echo $index === 0 ? 'active' : ''; ?>" data-slide="<?php echo $index; ?>">
                <div class="carousel-bg" style="background-image: url('<?php echo htmlspecialchars($story['cover']); ?>');"></div>
                <div class="carousel-overlay"></div>
                
                <div class="carousel-container">
                    <h2 class="carousel-section-title">Popular New Titles</h2>
                    
                    <div class="carousel-content-wrapper">
                        <div class="carousel-cover">
                            <img src="<?php echo htmlspecialchars($story['cover']); ?>" alt="<?php echo htmlspecialchars($story['title']); ?>" onerror="this.src='assets/images/placeholder.jpg'">
                        </div>
                        
                        <div class="carousel-info">
                            <h3 class="carousel-title"><?php echo htmlspecialchars($story['title']); ?></h3>
                            
                            <div class="carousel-genres">
                                <span class="carousel-genre-tag suggestive">SUGGESTIVE</span>
                                <span class="carousel-genre-tag"><?php echo strtoupper(htmlspecialchars($story['genre'])); ?></span>
                                <?php if (isset($story['rating']) && $story['rating'] > 0): ?>
                                <span class="carousel-rating">
                                    <i class="fas fa-star"></i> <?php echo $story['rating']; ?>
                                </span>
                                <?php endif; ?>
                            </div>
                            
                            <p class="carousel-description">
                                <?php 
                                echo isset($story['description']) && !empty($story['description']) 
                                    ? htmlspecialchars($story['description']) 
                                    : 'An exciting story waiting to be discovered. Dive into this amazing adventure filled with captivating characters and unexpected plot twists.';
                                ?>
                            </p>
                            
                            <div class="carousel-author">
                                <?php echo htmlspecialchars($story['author'] ?? 'Unknown Author'); ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="carousel-counter-wrapper">
                        <div class="carousel-counter">NO. <?php echo $index + 1; ?></div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            
            <div class="carousel-nav-buttons">
                <button class="carousel-nav-btn" id="carouselPrev">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <button class="carousel-nav-btn" id="carouselNext">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>
        <?php endif; ?>
    </section>

    <!-- Compact Genre Filter Section - Right after carousel -->
    <section class="genre-filter-compact">
        <div class="genre-filter-container">
            <h3 class="genre-filter-title"><i class="fas fa-filter"></i> Browse by Genre</h3>
            <div class="genre-chips">
                <a href="browse.php?genre=Slice of Life" class="genre-chip">
                    <i class="fas fa-mug-hot"></i>
                    <span>Slice of Life</span>
                </a>
                <a href="browse.php?genre=Science Fiction" class="genre-chip">
                    <i class="fas fa-rocket"></i>
                    <span>Sci-Fi</span>
                </a>
                <a href="browse.php?genre=Romance" class="genre-chip">
                    <i class="fas fa-heart"></i>
                    <span>Romance</span>
                </a>
                <a href="browse.php?genre=Mystery" class="genre-chip">
                    <i class="fas fa-search"></i>
                    <span>Mystery</span>
                </a>
                <a href="browse.php?genre=Inspirational" class="genre-chip">
                    <i class="fas fa-lightbulb"></i>
                    <span>Inspirational</span>
                </a>
                <a href="browse.php?genre=Horror" class="genre-chip">
                    <i class="fas fa-ghost"></i>
                    <span>Horror</span>
                </a>
                <a href="browse.php?genre=Historical Fiction" class="genre-chip">
                    <i class="fas fa-landmark"></i>
                    <span>Historical</span>
                </a>
                <a href="browse.php?genre=Fantasy" class="genre-chip">
                    <i class="fas fa-hat-wizard"></i>
                    <span>Fantasy</span>
                </a>
                <a href="browse.php?genre=Comedy" class="genre-chip">
                    <i class="fas fa-laugh"></i>
                    <span>Comedy</span>
                </a>
                <a href="browse.php?genre=Adventure" class="genre-chip">
                    <i class="fas fa-compass"></i>
                    <span>Adventure</span>
                </a>
            </div>
        </div>
    </section>

    <div id="searchResultsInfo" style="display: none;">
        <div style="max-width: 1400px; margin: 20px auto; padding: 0 30px;">
            <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px 30px; border-radius: 12px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 4px 15px rgba(0,0,0,0.2);">
                <div>
                    <h3 style="margin: 0; font-size: 1.3rem; display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-search"></i>
                        Search Results: <span id="searchQuery"></span>
                    </h3>
                    <p style="margin: 5px 0 0 0; opacity: 0.9;" id="searchCount">Found 0 stories</p>
                </div>
                <button onclick="clearSearch()" style="background: white; color: #667eea; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px; transition: all 0.3s;">
                    <i class="fas fa-times"></i> Clear Search
                </button>
            </div>
        </div>
    </div>

    <main>
        <?php if ($genre_filter): ?>
        <section class="content-section" id="genreFilterSection">
            <div class="genre-filter-banner">
                <div>
                    <h2><i class="fas fa-filter"></i> <?php echo ucfirst(str_replace('-', ' ', $genre_filter)); ?> Stories</h2>
                    <p id="genreCount"><?php echo count($genre_stories) . ' ' . (count($genre_stories) === 1 ? 'story' : 'stories') . ' found'; ?></p>
                </div>
                <a href="browse.php" class="clear-filter-btn">
                    <i class="fas fa-times"></i> Clear Filter
                </a>
            </div>

            <?php if (empty($genre_stories)): ?>
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <h3>No <?php echo ucfirst(str_replace('-', ' ', $genre_filter)); ?> Stories Yet</h3>
                <p>Check back soon for more content!</p>
                <a href="browse.php" class="btn-back">
                    <i class="fas fa-arrow-left"></i> Back to All Stories
                </a>
            </div>
            <?php else: ?>
            <div class="story-grid">
                <?php foreach ($genre_stories as $story): ?>
                <div class="story-card <?php echo (isset($story['status']) && (strtolower(trim($story['status'])) == 'coming soon' || strtolower(trim($story['status'])) == 'comingsoon')) ? 'coming-soon-card' : ''; ?>" 
                     data-story-id="<?php echo $story['id']; ?>"
                     data-title="<?php echo strtolower(htmlspecialchars($story['title'])); ?>"
                     data-genre="<?php echo strtolower($story['genre']); ?>"
                     data-author="<?php echo isset($story['author']) ? strtolower(htmlspecialchars($story['author'])) : ''; ?>"
                     data-coming-soon="<?php echo (isset($story['status']) && (strtolower(trim($story['status'])) == 'coming soon' || strtolower(trim($story['status'])) == 'comingsoon')) ? 'true' : 'false'; ?>">
                    <div class="story-image">
                        <img src="<?php echo $story['cover']; ?>" alt="<?php echo htmlspecialchars($story['title']); ?>" onerror="this.src='assets/images/placeholder.jpg'">
                        <?php if (isset($story['status']) && (strtolower(trim($story['status'])) == 'coming soon' || strtolower(trim($story['status'])) == 'comingsoon')): ?>
                        <div class="coming-soon-overlay">
                            <div class="coming-soon-banner">
                                <i class="fas fa-clock"></i>
                                <span>COMING SOON</span>
                            </div>
                        </div>
                        <?php else: ?>
                        <div class="story-overlay">
                            <button class="btn-read"><i class="fas fa-book-reader"></i> Read Now</button>
                            
                        </div>
                        <?php endif; ?>
                        <span class="badge genre-badge"><?php echo $story['genre']; ?></span>
                        <span class="badge rating-badge"><i class="fas fa-star"></i> <?php echo $story['rating']; ?></span>
                            <?php if (isset($story['status']) && (strtolower(trim($story['status'])) != 'coming soon' && strtolower(trim($story['status'])) != 'comingsoon')): ?>
                            <span class="badge status-badge status-<?php echo str_replace(' ', '-', strtolower($story['status'])); ?>"><?php echo strtoupper($story['status']); ?></span>
                            <?php endif; ?>
                        <?php if (isset($story['status']) && (strtolower(trim($story['status'])) != 'coming soon' && strtolower(trim($story['status'])) != 'comingsoon')): ?>
                        <span class="badge status-badge status-<?php echo str_replace(' ', '-', strtolower($story['status'])); ?>"><?php echo strtoupper($story['status']); ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="story-info">
                        <h3><?php echo htmlspecialchars($story['title']); ?></h3>
                        <p class="story-meta">
                            <i class="fas fa-book"></i> <?php echo $story['chapters']; ?> Chapters
                        </p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </section>
        <?php endif; ?>

        <?php if (!$genre_filter): ?>
        <section class="content-section" id="featuredSection">
            <div class="section-header">
                <h2><i class="fas fa-star"></i> Featured Today</h2>
            </div>
            <?php if (empty($featured_stories)): ?>
            <div class="empty-state">
                <i class="fas fa-star"></i>
                <h3>No Featured Stories Yet</h3>
                <p>Check back soon for featured content!</p>
            </div>
            <?php else: ?>
            <div class="story-scroll-container">
                <div class="story-grid featured-grid" id="featured-grid">
                    <?php foreach ($featured_stories as $story): ?>
                    <div class="story-card featured-card <?php echo (isset($story['status']) && (strtolower(trim($story['status'])) == 'coming soon' || strtolower(trim($story['status'])) == 'comingsoon')) ? 'coming-soon-card' : ''; ?>" 
                         data-story-id="<?php echo $story['id']; ?>"
                         data-title="<?php echo strtolower(htmlspecialchars($story['title'])); ?>"
                         data-genre="<?php echo strtolower($story['genre']); ?>"
                         data-author="<?php echo isset($story['author']) ? strtolower(htmlspecialchars($story['author'])) : ''; ?>"
                         data-coming-soon="<?php echo (isset($story['status']) && (strtolower(trim($story['status'])) == 'coming soon' || strtolower(trim($story['status'])) == 'comingsoon')) ? 'true' : 'false'; ?>">
                        <div class="story-image">
                            <img src="<?php echo $story['cover']; ?>" alt="<?php echo htmlspecialchars($story['title']); ?>" onerror="this.src='assets/images/placeholder.jpg'">
                            <?php if (isset($story['status']) && (strtolower(trim($story['status'])) == 'coming soon' || strtolower(trim($story['status'])) == 'comingsoon')): ?>
                            <div class="coming-soon-overlay">
                                <div class="coming-soon-banner">
                                    <i class="fas fa-clock"></i>
                                    <span>COMING SOON</span>
                                </div>
                            </div>
                            <?php else: ?>
                            <div class="story-overlay">
                                <button class="btn-read"><i class="fas fa-book-reader"></i> Read Now</button>
                                
                            </div>
                            <?php endif; ?>
                            <span class="badge genre-badge"><?php echo $story['genre']; ?></span>
                            <span class="badge rating-badge"><i class="fas fa-star"></i> <?php echo $story['rating']; ?></span>
                            <?php if (isset($story['status']) && (strtolower(trim($story['status'])) != 'coming soon' && strtolower(trim($story['status'])) != 'comingsoon')): ?>
                            <span class="badge status-badge status-<?php echo str_replace(' ', '-', strtolower($story['status'])); ?>"><?php echo strtoupper($story['status']); ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="story-info">
                            <h3><?php echo htmlspecialchars($story['title']); ?></h3>
                            <p class="story-meta">
                                <i class="fas fa-book"></i> <?php echo $story['chapters']; ?> Chapters
                            </p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="scroll-nav-buttons">
                    <button class="scroll-nav-btn" onclick="scrollSection('featured', 'left')"><i class="fas fa-chevron-left"></i></button>
                    <button class="scroll-nav-btn" onclick="scrollSection('featured', 'right')"><i class="fas fa-chevron-right"></i></button>
                </div>
            </div>
            <?php endif; ?>
        </section>

        <section class="content-section" id="trendingSection">
            <div class="section-header">
                <h2><i class="fas fa-fire"></i> Trending Now</h2>
            </div>
            <?php if (empty($trending_stories)): ?>
            <div class="empty-state">
                <i class="fas fa-fire"></i>
                <h3>No Trending Stories Yet</h3>
                <p>Popular stories will appear here!</p>
            </div>
            <?php else: ?>
            <div class="story-scroll-container">
                <div class="story-grid" id="trending-grid">
                    <?php foreach ($trending_stories as $story): ?>
                    <div class="story-card <?php echo (isset($story['status']) && (strtolower(trim($story['status'])) == 'coming soon' || strtolower(trim($story['status'])) == 'comingsoon')) ? 'coming-soon-card' : ''; ?>" 
                         data-story-id="<?php echo $story['id']; ?>"
                         data-title="<?php echo strtolower(htmlspecialchars($story['title'])); ?>"
                         data-genre="<?php echo strtolower($story['genre']); ?>"
                         data-author="<?php echo isset($story['author']) ? strtolower(htmlspecialchars($story['author'])) : ''; ?>"
                         data-coming-soon="<?php echo (isset($story['status']) && (strtolower(trim($story['status'])) == 'coming soon' || strtolower(trim($story['status'])) == 'comingsoon')) ? 'true' : 'false'; ?>">
                        <div class="story-image">
                            <img src="<?php echo $story['cover']; ?>" alt="<?php echo htmlspecialchars($story['title']); ?>" onerror="this.src='assets/images/placeholder.jpg'">
                            <?php if (isset($story['status']) && (strtolower(trim($story['status'])) == 'coming soon' || strtolower(trim($story['status'])) == 'comingsoon')): ?>
                            <div class="coming-soon-overlay">
                                <div class="coming-soon-banner">
                                    <i class="fas fa-clock"></i>
                                    <span>COMING SOON</span>
                                </div>
                            </div>
                            <?php else: ?>
                            <div class="story-overlay">
                                <button class="btn-read"><i class="fas fa-book-reader"></i> Read Now</button>
                                
                            </div>
                            <?php endif; ?>
                            <span class="badge genre-badge"><?php echo $story['genre']; ?></span>
                            <span class="badge rating-badge"><i class="fas fa-star"></i> <?php echo $story['rating']; ?></span>
                            <?php if (isset($story['status']) && (strtolower(trim($story['status'])) != 'coming soon' && strtolower(trim($story['status'])) != 'comingsoon')): ?>
                            <span class="badge status-badge status-<?php echo str_replace(' ', '-', strtolower($story['status'])); ?>"><?php echo strtoupper($story['status']); ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="story-info">
                            <h3><?php echo htmlspecialchars($story['title']); ?></h3>
                            <p class="story-meta">
                                <i class="fas fa-book"></i> <?php echo $story['chapters']; ?> Chapters
                            </p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="scroll-nav-buttons">
                    <button class="scroll-nav-btn" onclick="scrollSection('trending', 'left')"><i class="fas fa-chevron-left"></i></button>
                    <button class="scroll-nav-btn" onclick="scrollSection('trending', 'right')"><i class="fas fa-chevron-right"></i></button>
                </div>
            </div>
            <?php endif; ?>
        </section>

        <section class="content-section" id="newSection">
            <div class="section-header">
                <h2><i class="fas fa-clock"></i> New Releases</h2>
            </div>
            <?php if (empty($new_releases)): ?>
            <div class="empty-state">
                <i class="fas fa-clock"></i>
                <h3>No New Releases Yet</h3>
                <p>Fresh content coming soon!</p>
            </div>
            <?php else: ?>
            <div class="story-scroll-container">
                <div class="story-grid" id="new-grid">
                    <?php foreach ($new_releases as $story): ?>
                    <div class="story-card <?php echo (isset($story['status']) && (strtolower(trim($story['status'])) == 'coming soon' || strtolower(trim($story['status'])) == 'comingsoon')) ? 'coming-soon-card' : ''; ?>" 
                         data-story-id="<?php echo $story['id']; ?>"
                         data-title="<?php echo strtolower(htmlspecialchars($story['title'])); ?>"
                         data-genre="<?php echo strtolower($story['genre']); ?>"
                         data-author="<?php echo isset($story['author']) ? strtolower(htmlspecialchars($story['author'])) : ''; ?>"
                         data-coming-soon="<?php echo (isset($story['status']) && (strtolower(trim($story['status'])) == 'coming soon' || strtolower(trim($story['status'])) == 'comingsoon')) ? 'true' : 'false'; ?>">
                        <div class="story-image">
                            <img src="<?php echo $story['cover']; ?>" alt="<?php echo htmlspecialchars($story['title']); ?>" onerror="this.src='assets/images/placeholder.jpg'">
                            <?php if (isset($story['status']) && (strtolower(trim($story['status'])) == 'coming soon' || strtolower(trim($story['status'])) == 'comingsoon')): ?>
                            <div class="coming-soon-overlay">
                                <div class="coming-soon-banner">
                                    <i class="fas fa-clock"></i>
                                    <span>COMING SOON</span>
                                </div>
                            </div>
                            <?php else: ?>
                            <div class="story-overlay">
                                <button class="btn-read"><i class="fas fa-book-reader"></i> Read Now</button>
                                
                            </div>
                            <span class="badge new-badge"><i class="fas fa-certificate"></i> NEW</span>
                            <?php endif; ?>
                            <span class="badge genre-badge"><?php echo $story['genre']; ?></span>
                            <span class="badge rating-badge"><i class="fas fa-star"></i> <?php echo $story['rating']; ?></span>
                            <?php if (isset($story['status']) && (strtolower(trim($story['status'])) != 'coming soon' && strtolower(trim($story['status'])) != 'comingsoon')): ?>
                            <span class="badge status-badge status-<?php echo str_replace(' ', '-', strtolower($story['status'])); ?>"><?php echo strtoupper($story['status']); ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="story-info">
                            <h3><?php echo htmlspecialchars($story['title']); ?></h3>
                            <p class="story-meta">
                                <i class="fas fa-book"></i> <?php echo $story['chapters']; ?> Chapters
                            </p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="scroll-nav-buttons">
                    <button class="scroll-nav-btn" onclick="scrollSection('new', 'left')"><i class="fas fa-chevron-left"></i></button>
                    <button class="scroll-nav-btn" onclick="scrollSection('new', 'right')"><i class="fas fa-chevron-right"></i></button>
                </div>
            </div>
            <?php endif; ?>
        </section>

        <div id="noResultsMessage" style="display: none;">
            <div class="empty-state">
                <i class="fas fa-search"></i>
                <h2>No Stories Found</h2>
                <p>We couldn't find any stories matching your search.</p>
                <button onclick="clearSearch()" class="btn-primary">
                    <i class="fas fa-redo"></i> Show All Stories
                </button>
            </div>
        </div>
        <?php endif; ?>

    </main>

    <footer>
        <div class="footer-content">
            <div class="footer-section">
                <h3>About ReadQuest</h3>
                <p>Your premier destination for amazing stories and novels online.</p>
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

    <script src="assets/js/browse.js"></script>
</body>
</html>