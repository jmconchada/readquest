// ========== DOM READY ==========
document.addEventListener('DOMContentLoaded', function() {
    initializeBrowsePage();
});

// ========== INITIALIZE BROWSE PAGE ==========
function initializeBrowsePage() {
    setupStickyHeader();
    setupThemeToggle();
    setupProfileDropdown();
    setupLogout();
    setupStoryCards();
    setupBookmarks();
    setupScrollEffects();
    setupLazyLoading();
}

// ========== STICKY HEADER ON SCROLL ==========
function setupStickyHeader() {
    const header = document.querySelector('header');
    if (!header) return;

    window.addEventListener('scroll', function() {
        if (window.scrollY > 50) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    });
}

// ========== PROFILE DROPDOWN (HOVER + CLICK) ==========
function setupProfileDropdown() {
    const userMenuWrapper = document.querySelector('.user-menu-wrapper');
    const profilePic = document.querySelector('.profile-pic');
    
    if (!userMenuWrapper || !profilePic) return;
    
    // Click to toggle dropdown (stays open)
    profilePic.addEventListener('click', function(e) {
        e.stopPropagation();
        userMenuWrapper.classList.toggle('active');
    });
    
    // Click outside to close
    document.addEventListener('click', function(e) {
        if (!userMenuWrapper.contains(e.target)) {
            userMenuWrapper.classList.remove('active');
        }
    });
    
    // Prevent dropdown from closing when clicking inside it
    const dropdown = document.querySelector('.mangadex-dropdown');
    if (dropdown) {
        dropdown.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }
}

// ========== THEME TOGGLE ==========
function setupThemeToggle() {
    const root = document.documentElement;
    const themeButtons = document.querySelectorAll('.theme-btn');
    
    if (themeButtons.length === 0) return;
    
    // Load saved theme or default to dark
    const savedTheme = localStorage.getItem('theme') || 'dark';
    root.setAttribute('data-theme', savedTheme);
    
    // Update active button
    themeButtons.forEach(btn => {
        if (btn.dataset.theme === savedTheme) {
            btn.classList.add('active');
        } else {
            btn.classList.remove('active');
        }
    });
    
    // Handle theme switch
    themeButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const theme = this.dataset.theme;
            root.setAttribute('data-theme', theme);
            localStorage.setItem('theme', theme);
            
            // Update active state
            themeButtons.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            // Show notification
            showNotification(`Switched to ${theme} theme`, 'success');
        });
    });
}

// ========== LOGOUT FUNCTIONALITY ==========
function setupLogout() {
    const logoutBtn = document.getElementById('logoutBtn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Show confirmation dialog
            if (confirm('Are you sure you want to logout?')) {
                // Add loading state
                logoutBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Logging out...';
                
                // Redirect after short delay for visual feedback
                setTimeout(() => {
                    window.location.href = 'logout.php';
                }, 500);
            }
        });
    }
}

// ========== STORY CARD INTERACTIONS ==========
function setupStoryCards() {
    const storyCards = document.querySelectorAll('.story-card');
    
    storyCards.forEach(card => {
        // Click on card to view story details
        card.addEventListener('click', function(e) {
            // Don't navigate if clicking on buttons
            if (e.target.closest('.btn-read') || e.target.closest('.btn-bookmark')) {
                return;
            }
            
            const isComingSoon = this.dataset.comingSoon === 'true';
            const storyId = this.dataset.storyId;
            
            if (isComingSoon) {
                // Show coming soon modal instead
                showComingSoonModal();
            } else if (storyId) {
                window.location.href = `story.php?id=${storyId}`;
            }
        });

        // Read Now button
        const readBtn = card.querySelector('.btn-read');
        if (readBtn) {
            readBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                const storyId = card.dataset.storyId;
                const isComingSoon = card.dataset.comingSoon === 'true';
                
                if (isComingSoon) {
                    showComingSoonModal();
                } else if (storyId) {
                    // Add loading animation
                    this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Opening...';
                    
                    setTimeout(() => {
                        window.location.href = `read.php?id=${storyId}`;
                    }, 500);
                }
            });
        }

        // Add hover effect for card title
        const cardTitle = card.querySelector('.story-info h3');
        if (cardTitle) {
            const fullTitle = cardTitle.textContent;
            cardTitle.setAttribute('title', fullTitle);
        }
    });
}

// ========== COMING SOON MODAL ==========
function showComingSoonModal() {
    const modal = document.getElementById('comingSoonModal');
    if (modal) {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
}

function closeComingSoonModal() {
    const modal = document.getElementById('comingSoonModal');
    if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = '';
    }
}

// Close modal when clicking outside
document.addEventListener('click', function(e) {
    const modal = document.getElementById('comingSoonModal');
    if (modal && e.target === modal) {
        closeComingSoonModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeComingSoonModal();
    }
});

// ========== BOOKMARK FUNCTIONALITY ==========
function setupBookmarks() {
    const bookmarkBtns = document.querySelectorAll('.btn-bookmark');
    
    bookmarkBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            const card = this.closest('.story-card');
            const storyId = card.dataset.storyId;
            
            toggleBookmark(storyId, this);
        });
    });

    // Load bookmark states from localStorage or server
    loadBookmarkStates();
}

function toggleBookmark(storyId, button) {
    const icon = button.querySelector('i');
    const isBookmarked = icon.classList.contains('fas');
    
    if (isBookmarked) {
        // Remove bookmark
        icon.classList.remove('fas');
        icon.classList.add('far');
        button.setAttribute('title', 'Add to bookmarks');
        removeBookmark(storyId);
        showNotification('Removed from bookmarks', 'info');
    } else {
        // Add bookmark
        icon.classList.remove('far');
        icon.classList.add('fas');
        button.setAttribute('title', 'Remove from bookmarks');
        addBookmark(storyId);
        showNotification('Added to bookmarks!', 'success');
    }
    
    // Add animation
    button.style.transform = 'scale(1.2)';
    setTimeout(() => {
        button.style.transform = '';
    }, 200);
}

function addBookmark(storyId) {
    // Save to database
    fetch('api/bookmarks.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ story_id: storyId, action: 'add' })
    })
    .then(res => res.json())
    .then(data => {
        if (!data.success) {
            console.error('Failed to save bookmark:', data.message);
        }
    })
    .catch(err => console.error('Bookmark error:', err));
    
    // Also save to localStorage as backup
    let bookmarks = JSON.parse(localStorage.getItem('bookmarks') || '[]');
    if (!bookmarks.includes(storyId)) {
        bookmarks.push(storyId);
        localStorage.setItem('bookmarks', JSON.stringify(bookmarks));
    }
}

function removeBookmark(storyId) {
    // Remove from database
    fetch('api/bookmarks.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ story_id: storyId, action: 'remove' })
    })
    .then(res => res.json())
    .then(data => {
        if (!data.success) {
            console.error('Failed to remove bookmark:', data.message);
        }
    })
    .catch(err => console.error('Bookmark error:', err));
    
    // Also remove from localStorage
    let bookmarks = JSON.parse(localStorage.getItem('bookmarks') || '[]');
    bookmarks = bookmarks.filter(id => id !== storyId);
    localStorage.setItem('bookmarks', JSON.stringify(bookmarks));
}

function loadBookmarkStates() {
    const bookmarks = JSON.parse(localStorage.getItem('bookmarks') || '[]');
    
    bookmarks.forEach(storyId => {
        const card = document.querySelector(`[data-story-id="${storyId}"]`);
        if (card) {
            const bookmarkBtn = card.querySelector('.btn-bookmark i');
            if (bookmarkBtn) {
                bookmarkBtn.classList.remove('far');
                bookmarkBtn.classList.add('fas');
            }
        }
    });
}

// ========== SCROLL EFFECTS ==========
function setupScrollEffects() {
    // Animate elements on scroll into view
    observeElements();
    
    // Add scroll to top button
    setupScrollToTop();
}

function observeElements() {
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);
    
    // Observe story cards
    document.querySelectorAll('.story-card').forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        card.style.transition = `opacity 0.5s ease ${index * 0.05}s, transform 0.5s ease ${index * 0.05}s`;
        observer.observe(card);
    });
    
    // Observe sections
    document.querySelectorAll('.content-section').forEach(section => {
        observer.observe(section);
    });
}

// ========== SCROLL TO TOP BUTTON ==========
function setupScrollToTop() {
    window.addEventListener('scroll', () => {
        let scrollBtn = document.getElementById('scrollTopBtn');
        
        if (window.pageYOffset > 500) {
            if (!scrollBtn) {
                scrollBtn = document.createElement('button');
                scrollBtn.id = 'scrollTopBtn';
                scrollBtn.innerHTML = '<i class="fas fa-arrow-up"></i>';
                scrollBtn.setAttribute('title', 'Back to top');
                scrollBtn.setAttribute('aria-label', 'Scroll to top');
                
                Object.assign(scrollBtn.style, {
                    position: 'fixed',
                    bottom: '30px',
                    right: '30px',
                    width: '50px',
                    height: '50px',
                    borderRadius: '50%',
                    backgroundColor: '#3498db',
                    color: 'white',
                    border: 'none',
                    cursor: 'pointer',
                    fontSize: '20px',
                    boxShadow: '0 4px 15px rgba(0, 0, 0, 0.3)',
                    zIndex: '9999',
                    transition: 'all 0.3s ease',
                    opacity: '0',
                    transform: 'scale(0)'
                });
                
                scrollBtn.addEventListener('click', scrollToTop);
                scrollBtn.addEventListener('mouseenter', function() {
                    this.style.backgroundColor = '#2980b9';
                    this.style.transform = 'scale(1.1)';
                });
                scrollBtn.addEventListener('mouseleave', function() {
                    this.style.backgroundColor = '#3498db';
                    this.style.transform = 'scale(1)';
                });
                
                document.body.appendChild(scrollBtn);
                
                setTimeout(() => {
                    scrollBtn.style.opacity = '1';
                    scrollBtn.style.transform = 'scale(1)';
                }, 10);
            }
        } else if (scrollBtn) {
            scrollBtn.style.opacity = '0';
            scrollBtn.style.transform = 'scale(0)';
            setTimeout(() => scrollBtn.remove(), 300);
        }
    });
}

function scrollToTop() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}

// ========== LAZY LOADING IMAGES ==========
function setupLazyLoading() {
    const images = document.querySelectorAll('.story-image img');
    
    const imageObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                
                // Add placeholder if image fails to load
                img.addEventListener('error', function() {
                    this.src = 'assets/images/placeholder.jpg';
                    this.alt = 'Image not available';
                });
                
                imageObserver.unobserve(img);
            }
        });
    });
    
    images.forEach(img => imageObserver.observe(img));
}

// ========== NOTIFICATION SYSTEM ==========
function showNotification(message, type = 'info') {
    // Remove existing notifications
    const existing = document.querySelector('.notification');
    if (existing) {
        existing.remove();
    }
    
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'info-circle'}"></i>
        <span>${message}</span>
    `;
    
    // Add styles
    Object.assign(notification.style, {
        position: 'fixed',
        top: '90px',
        right: '30px',
        backgroundColor: type === 'success' ? '#27ae60' : '#3498db',
        color: 'white',
        padding: '15px 25px',
        borderRadius: '8px',
        boxShadow: '0 4px 15px rgba(0, 0, 0, 0.2)',
        zIndex: '10000',
        display: 'flex',
        alignItems: 'center',
        gap: '10px',
        animation: 'slideInRight 0.3s ease-out',
        fontWeight: '500'
    });
    
    document.body.appendChild(notification);
    
    // Auto remove after 3 seconds
    setTimeout(() => {
        notification.style.animation = 'slideOutRight 0.3s ease-out';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Add notification animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(400px);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);

// ========== KEYBOARD SHORTCUTS ==========
document.addEventListener('keydown', (e) => {
    // Press '/' to focus search
    if (e.key === '/' && !e.target.matches('input, textarea')) {
        e.preventDefault();
        document.getElementById('searchInput')?.focus();
    }
    
    // Press 'Escape' to clear search or close dropdown
    if (e.key === 'Escape') {
        const searchInput = document.getElementById('searchInput');
        if (searchInput && document.activeElement === searchInput) {
            searchInput.value = '';
            searchInput.blur();
        }
        
        const dropdown = document.querySelector('.dropdown-menu');
        if (dropdown && dropdown.style.opacity === '1') {
            document.activeElement.blur();
        }
    }
});

// ========== CONSOLE MESSAGE ==========
console.log('%c📚 ReadQuest - Browse ', 'background: #3498db; color: white; font-size: 20px; padding: 10px;');
console.log('%cBrowsing stories...', 'color: #2c3e50; font-size: 14px;');
console.log('%cKeyboard shortcuts: Press "/" to search, "Esc" to clear', 'color: #7f8c8d; font-size: 12px;');

// ========== STORY CAROUSEL ==========
(function() {
    let currentSlide = 0;
    const slides = document.querySelectorAll('.carousel-slide');
    const totalSlides = slides.length;
    
    if (totalSlides === 0) return;
    
    const prevBtn = document.getElementById('carouselPrev');
    const nextBtn = document.getElementById('carouselNext');
    let autoAdvanceInterval;
    
    function showSlide(index) {
        slides.forEach(slide => slide.classList.remove('active'));
        slides[index].classList.add('active');
    }
    
    function nextSlide() {
        currentSlide = (currentSlide + 1) % totalSlides;
        showSlide(currentSlide);
    }
    
    function prevSlide() {
        currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
        showSlide(currentSlide);
    }
    
    function startAutoAdvance() {
        if (autoAdvanceInterval) {
            clearInterval(autoAdvanceInterval);
        }
        autoAdvanceInterval = setInterval(nextSlide, 3000);
    }
    
    function resetAutoAdvance() {
        startAutoAdvance();
    }
    
    if (prevBtn) {
        prevBtn.addEventListener('click', function() {
            prevSlide();
            resetAutoAdvance();
        });
    }
    
    if (nextBtn) {
        nextBtn.addEventListener('click', function() {
            nextSlide();
            resetAutoAdvance();
        });
    }
    
    document.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowLeft') {
            prevSlide();
            resetAutoAdvance();
        }
        if (e.key === 'ArrowRight') {
            nextSlide();
            resetAutoAdvance();
        }
    });
    
    const carouselSection = document.querySelector('.story-carousel-section');
    if (carouselSection) {
        carouselSection.addEventListener('mouseenter', function() {
            if (autoAdvanceInterval) {
                clearInterval(autoAdvanceInterval);
            }
        });
        
        carouselSection.addEventListener('mouseleave', function() {
            startAutoAdvance();
        });
    }
    
    startAutoAdvance();
})();

// ========== SEARCH FUNCTIONALITY ==========
function performSearch() {
    const searchInput = document.getElementById('searchInput');
    const query = searchInput.value.trim().toLowerCase();
    
    if (query === '') {
        clearSearch();
        return;
    }
    
    const allCards = document.querySelectorAll('.story-card');
    let visibleCount = 0;
    
    allCards.forEach(card => {
        const title = card.dataset.title || '';
        const genre = card.dataset.genre || '';
        const author = card.dataset.author || '';
        
        const matches = title.includes(query) || 
                      genre.includes(query) || 
                      author.includes(query);
        
        if (matches) {
            card.style.display = 'block';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });
    
    if (visibleCount === 0) {
        document.getElementById('noResultsMessage').style.display = 'block';
        document.getElementById('featuredSection').querySelector('.section-header').style.display = 'none';
        document.getElementById('trendingSection').querySelector('.section-header').style.display = 'none';
        document.getElementById('newSection').querySelector('.section-header').style.display = 'none';
    } else {
        document.getElementById('noResultsMessage').style.display = 'none';
        checkSectionVisibility('featuredSection');
        checkSectionVisibility('trendingSection');
        checkSectionVisibility('newSection');
    }
    
    document.getElementById('searchResultsInfo').style.display = 'block';
    document.getElementById('searchQuery').textContent = '"' + searchInput.value + '"';
    document.getElementById('searchCount').textContent = `Found ${visibleCount} ${visibleCount === 1 ? 'story' : 'stories'}`;
    
    window.scrollTo({ top: 400, behavior: 'smooth' });
}

function checkSectionVisibility(sectionId) {
    const section = document.getElementById(sectionId);
    const cards = section.querySelectorAll('.story-card');
    let hasVisible = false;
    
    cards.forEach(card => {
        if (card.style.display !== 'none') {
            hasVisible = true;
        }
    });
    
    section.querySelector('.section-header').style.display = hasVisible ? 'flex' : 'none';
}

function clearSearch() {
    const searchInput = document.getElementById('searchInput');
    searchInput.value = '';
    
    const allCards = document.querySelectorAll('.story-card');
    allCards.forEach(card => {
        card.style.display = 'block';
    });
    
    document.getElementById('featuredSection').querySelector('.section-header').style.display = 'flex';
    document.getElementById('trendingSection').querySelector('.section-header').style.display = 'flex';
    document.getElementById('newSection').querySelector('.section-header').style.display = 'flex';
    
    document.getElementById('searchResultsInfo').style.display = 'none';
    document.getElementById('noResultsMessage').style.display = 'none';
    
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

document.getElementById('searchInput').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        performSearch();
    }
});

// ========== HORIZONTAL SCROLL NAVIGATION ==========
function scrollSection(section, direction) {
    const grid = document.getElementById(`${section}-grid`);
    if (!grid) return;
    
    const scrollAmount = 300;
    const currentScroll = grid.scrollLeft;
    
    if (direction === 'left') {
        grid.scrollTo({
            left: currentScroll - scrollAmount,
            behavior: 'smooth'
        });
    } else {
        grid.scrollTo({
            left: currentScroll + scrollAmount,
            behavior: 'smooth'
        });
    }
}