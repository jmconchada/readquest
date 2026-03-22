// ========== DOM READY ==========
document.addEventListener('DOMContentLoaded', function() {
    initializeIndexPage();
});

// ========== INITIALIZE INDEX PAGE ==========
function initializeIndexPage() {
    setupStickyHeader();
    setupThemeToggle();
    setupScrollEffects();
}

// ========== MANGADEX-STYLE STICKY HEADER ON SCROLL ==========
function setupStickyHeader() {
    const nav = document.getElementById('mainNav');
    if (!nav) return;

    window.addEventListener('scroll', function() {
        if (window.scrollY > 50) {
            nav.classList.add('scrolled');
        } else {
            nav.classList.remove('scrolled');
        }
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
            
            // Show notification
            showNotification(`Switched to ${theme} theme`, 'success');
        });
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
    
    // Observe feature cards
    document.querySelectorAll('.feature-card').forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        card.style.transition = `opacity 0.5s ease ${index * 0.1}s, transform 0.5s ease ${index * 0.1}s`;
        observer.observe(card);
    });
    
    // Observe genre cards
    document.querySelectorAll('.genre-preview-card').forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        card.style.transition = `opacity 0.4s ease ${index * 0.05}s, transform 0.4s ease ${index * 0.05}s`;
        observer.observe(card);
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
    // Press 'Escape' to close dropdown menu
    if (e.key === 'Escape') {
        const dropdown = document.querySelector('.mangadex-dropdown');
        if (dropdown && dropdown.style.opacity === '1') {
            // Close dropdown by removing hover
            document.activeElement.blur();
        }
    }
});

// ========== CONSOLE MESSAGE ==========
console.log('%c📚 ReadQuest ', 'background: #3498db; color: white; font-size: 20px; padding: 10px;');
console.log('%cWelcome to ReadQuest!', 'color: #2c3e50; font-size: 14px;');
console.log('%cYour premier destination for manga and comics.', 'color: #7f8c8d; font-size: 12px;');