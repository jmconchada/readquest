// ========== DOM READY ==========
document.addEventListener('DOMContentLoaded', function() {
    initializeStoryPage();
});

// ========== INITIALIZE STORY PAGE ==========
function initializeStoryPage() {
    setupStickyHeader();
    setupTabs();
    setupThemeToggle();
    markReadChapters();
    trackChapterClicks();
}

// ========== MARK READ CHAPTERS WITH BOOKMARK ==========
function markReadChapters() {
    // Get story ID from URL or data attribute
    const storyId = getStoryIdFromURL();
    if (!storyId) return;
    
    // Get read chapters from localStorage
    const readChaptersKey = `readquest_read_chapters_${storyId}`;
    const readChapters = JSON.parse(localStorage.getItem(readChaptersKey) || '[]');
    
    // Mark each read chapter
    readChapters.forEach(chapterId => {
        const chapterElement = document.querySelector(`[onclick*="chapter=${chapterId}"]`);
        if (chapterElement && chapterElement.classList.contains('chapter-item')) {
            chapterElement.classList.add('chapter-read');
        }
    });
}

// ========== TRACK CHAPTER CLICKS ==========
function trackChapterClicks() {
    const chapterItems = document.querySelectorAll('.chapter-item');
    
    chapterItems.forEach(item => {
        item.addEventListener('click', function() {
            const onclickAttr = this.getAttribute('onclick');
            if (!onclickAttr) return;
            
            // Extract story and chapter IDs from onclick attribute
            const storyMatch = onclickAttr.match(/story=(\d+)/);
            const chapterMatch = onclickAttr.match(/chapter=(\d+)/);
            
            if (storyMatch && chapterMatch) {
                const storyId = storyMatch[1];
                const chapterId = chapterMatch[1];
                
                // Save to localStorage
                const readChaptersKey = `readquest_read_chapters_${storyId}`;
                let readChapters = JSON.parse(localStorage.getItem(readChaptersKey) || '[]');
                
                if (!readChapters.includes(chapterId)) {
                    readChapters.push(chapterId);
                    localStorage.setItem(readChaptersKey, JSON.stringify(readChapters));
                }
            }
        });
    });
}

// ========== GET STORY ID FROM URL ==========
function getStoryIdFromURL() {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get('id');
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

// ========== TAB FUNCTIONALITY ==========
function setupTabs() {
    const tabs = document.querySelectorAll('.tab');
    const tabContents = document.querySelectorAll('.tab-content');
    
    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const tabName = this.dataset.tab;
            
            // Remove active from all tabs
            tabs.forEach(t => t.classList.remove('active'));
            tabContents.forEach(tc => tc.classList.remove('active'));
            
            // Add active to clicked tab
            this.classList.add('active');
            document.getElementById(`${tabName}-tab`).classList.add('active');
        });
    });
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
            
            showNotification(`Switched to ${theme} theme`, 'success');
        });
    });
}

// ========== NOTIFICATION SYSTEM ==========
function showNotification(message, type = 'info') {
    const existing = document.querySelector('.notification');
    if (existing) {
        existing.remove();
    }
    
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'info-circle'}"></i>
        <span>${message}</span>
    `;
    
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

// ========== CONSOLE MESSAGE ==========
console.log('%c📚 ReadQuest - Story Details ', 'background: #3498db; color: white; font-size: 20px; padding: 10px;');
console.log('%cViewing story details...', 'color: #2c3e50; font-size: 14px;');