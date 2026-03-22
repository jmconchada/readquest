// ========== DOM READY ==========
document.addEventListener('DOMContentLoaded', function() {
    initializeReaderPage();
});

// ========== INITIALIZE READER PAGE ==========
function initializeReaderPage() {
    setupThemeToggle();
    setupPageNavigation();
    setupAutoHideNav();
    setupKeyboardShortcuts();
}

// ========== PAGE NAVIGATION (LEFT/RIGHT) ==========
let currentPageIndex = 0;
let totalPages = 0;
let pages = [];

function setupPageNavigation() {
    pages = document.querySelectorAll('.page-item');
    totalPages = pages.length;
    
    if (totalPages === 0) return;
    
    // Show first page
    showPage(0);
    
    // Create navigation buttons
    createNavigationButtons();
    
    // Create page counter
    createPageCounter();
    
    // Create click areas
    createClickAreas();
    
    // Update page indicator in nav
    updatePageIndicator();
}

function createNavigationButtons() {
    const navContainer = document.createElement('div');
    navContainer.className = 'page-nav-buttons';
    navContainer.innerHTML = `
        <button class="page-nav-btn prev-page" id="prevPageBtn">
            <i class="fas fa-chevron-left"></i>
        </button>
        <button class="page-nav-btn next-page" id="nextPageBtn">
            <i class="fas fa-chevron-right"></i>
        </button>
    `;
    document.body.appendChild(navContainer);
    
    // Add event listeners
    document.getElementById('prevPageBtn').addEventListener('click', () => navigatePage('prev'));
    document.getElementById('nextPageBtn').addEventListener('click', () => navigatePage('next'));
    
    updateNavigationButtons();
}

function createPageCounter() {
    const counter = document.createElement('div');
    counter.className = 'page-counter';
    counter.id = 'pageCounter';
    counter.innerHTML = `<span class="current">${currentPageIndex + 1}</span> / ${totalPages}`;
    document.body.appendChild(counter);
}

function createClickAreas() {
    // Left click area for previous page
    const leftArea = document.createElement('div');
    leftArea.className = 'click-area-left';
    leftArea.addEventListener('click', () => navigatePage('prev'));
    document.body.appendChild(leftArea);
    
    // Right click area for next page
    const rightArea = document.createElement('div');
    rightArea.className = 'click-area-right';
    rightArea.addEventListener('click', () => navigatePage('next'));
    document.body.appendChild(rightArea);
}

function navigatePage(direction) {
    if (direction === 'next' && currentPageIndex < totalPages - 1) {
        currentPageIndex++;
        showPage(currentPageIndex);
    } else if (direction === 'prev' && currentPageIndex > 0) {
        currentPageIndex--;
        showPage(currentPageIndex);
    }
}

function showPage(index) {
    // Hide all pages
    pages.forEach(page => {
        page.classList.remove('active');
    });
    
    // Show only the current page
    if (pages[index]) {
        pages[index].classList.add('active');
    }
    
    currentPageIndex = index;
    updateNavigationButtons();
    updatePageIndicator();
    updatePageCounter();
    
    // Scroll to top smoothly
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function updateNavigationButtons() {
    const prevBtn = document.getElementById('prevPageBtn');
    const nextBtn = document.getElementById('nextPageBtn');
    
    if (prevBtn && nextBtn) {
        prevBtn.disabled = currentPageIndex === 0;
        nextBtn.disabled = currentPageIndex === totalPages - 1;
    }
}

function updatePageIndicator() {
    const currentPageSpan = document.getElementById('currentPage');
    const totalPagesSpan = document.getElementById('totalPages');
    
    if (currentPageSpan) {
        currentPageSpan.textContent = currentPageIndex + 1;
    }
    
    if (totalPagesSpan) {
        totalPagesSpan.textContent = totalPages;
    }
}

function updatePageCounter() {
    const counter = document.getElementById('pageCounter');
    if (counter) {
        counter.innerHTML = `<span class="current">${currentPageIndex + 1}</span> / ${totalPages}`;
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
        });
    });
}

// ========== AUTO HIDE NAVIGATION ==========
function setupAutoHideNav() {
    const readerNav = document.getElementById('readerNav');
    let lastScrollY = window.scrollY;
    let ticking = false;
    
    window.addEventListener('scroll', () => {
        if (!ticking) {
            window.requestAnimationFrame(() => {
                const currentScrollY = window.scrollY;
                
                // Show nav when scrolling up, hide when scrolling down
                if (currentScrollY < lastScrollY || currentScrollY < 100) {
                    readerNav.classList.remove('hidden');
                } else if (currentScrollY > lastScrollY && currentScrollY > 100) {
                    readerNav.classList.add('hidden');
                }
                
                lastScrollY = currentScrollY;
                ticking = false;
            });
            
            ticking = true;
        }
    });
    
    // Show nav on mouse move near top
    document.addEventListener('mousemove', (e) => {
        if (e.clientY < 100) {
            readerNav.classList.remove('hidden');
        }
    });
}

// ========== KEYBOARD SHORTCUTS ==========
function setupKeyboardShortcuts() {
    const prevChapterBtn = document.getElementById('prevChapter');
    const nextChapterBtn = document.getElementById('nextChapter');
    
    document.addEventListener('keydown', (e) => {
        // Arrow Left - Previous Page
        if (e.key === 'ArrowLeft') {
            e.preventDefault();
            navigatePage('prev');
        }
        
        // Arrow Right - Next Page
        if (e.key === 'ArrowRight') {
            e.preventDefault();
            navigatePage('next');
        }
        
        // Shift + Arrow Left - Previous Chapter
        if (e.shiftKey && e.key === 'ArrowLeft' && prevChapterBtn && !prevChapterBtn.disabled) {
            prevChapterBtn.click();
        }
        
        // Shift + Arrow Right - Next Chapter
        if (e.shiftKey && e.key === 'ArrowRight' && nextChapterBtn && !nextChapterBtn.disabled) {
            nextChapterBtn.click();
        }
        
        // Escape - Back to story
        if (e.key === 'Escape') {
            const storyId = new URLSearchParams(window.location.search).get('story') || 
                           new URLSearchParams(window.location.search).get('id');
            if (storyId) {
                window.location.href = `story.php?id=${storyId}`;
            }
        }
    });
}

// ========== CONSOLE MESSAGE ==========
console.log('%c📖 ReadQuest - Reader ', 'background: #8b7fe8; color: white; font-size: 20px; padding: 10px;');
console.log('%c← → Navigate Pages | Shift+← → Navigate Chapters | Esc Back to Story', 'color: #6b687e; font-size: 12px;');