// ========== ADMIN DASHBOARD JAVASCRIPT ==========

// ========== DOM READY ==========
document.addEventListener('DOMContentLoaded', function() {
    initializeDashboard();
});

// ========== INITIALIZE DASHBOARD ==========
function initializeDashboard() {
    setupMobileMenu();
    setupLogoutConfirmation();
    setupTableActions();
    animateStats();
    setupRefreshButton();
}

// ========== MOBILE MENU ==========
function setupMobileMenu() {
    // Create mobile menu toggle button if not exists
    if (window.innerWidth <= 768) {
        const mainContent = document.querySelector('.main-content');
        const sidebar = document.querySelector('.sidebar');
        
        // Check if toggle button already exists
        if (!document.querySelector('.mobile-menu-toggle')) {
            const menuToggle = document.createElement('button');
            menuToggle.className = 'mobile-menu-toggle';
            menuToggle.innerHTML = '<i class="fas fa-bars"></i>';
            menuToggle.style.cssText = `
                position: fixed;
                top: 20px;
                left: 20px;
                z-index: 1001;
                background: linear-gradient(135deg, #667eea, #764ba2);
                color: white;
                border: none;
                width: 50px;
                height: 50px;
                border-radius: 12px;
                font-size: 1.2rem;
                cursor: pointer;
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
                display: none;
            `;
            
            if (window.innerWidth <= 768) {
                menuToggle.style.display = 'block';
            }
            
            menuToggle.addEventListener('click', function() {
                sidebar.classList.toggle('active');
            });
            
            document.body.appendChild(menuToggle);
            
            // Close sidebar when clicking outside
            document.addEventListener('click', function(e) {
                if (!sidebar.contains(e.target) && !menuToggle.contains(e.target)) {
                    sidebar.classList.remove('active');
                }
            });
        }
    }
}

// Handle window resize
window.addEventListener('resize', function() {
    const menuToggle = document.querySelector('.mobile-menu-toggle');
    if (menuToggle) {
        if (window.innerWidth <= 768) {
            menuToggle.style.display = 'block';
        } else {
            menuToggle.style.display = 'none';
        }
    }
});

// ========== LOGOUT CONFIRMATION ==========
function setupLogoutConfirmation() {
    const logoutLinks = document.querySelectorAll('a[href*="logout"]');
    
    logoutLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            if (confirm('Are you sure you want to logout?')) {
                window.location.href = this.href;
            }
        });
    });
}

// ========== TABLE ACTIONS ==========
function setupTableActions() {
    // Delete confirmation for any delete buttons
    const deleteButtons = document.querySelectorAll('.btn-icon.btn-danger, [data-action="delete"]');
    
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const confirmMessage = this.dataset.confirm || 'Are you sure you want to delete this item?';
            
            if (confirm(confirmMessage)) {
                // If it's a link, navigate to it
                if (this.href) {
                    window.location.href = this.href;
                }
                // If it has a data-action, trigger it
                else if (this.dataset.action) {
                    performDelete(this.dataset.id, this.dataset.type);
                }
            }
        });
    });
}

function performDelete(id, type) {
    // This function would handle AJAX deletion
    console.log(`Deleting ${type} with ID: ${id}`);
    
    // Example AJAX call (uncomment when backend is ready):
    /*
    fetch(`delete_${type}.php?id=${id}`, {
        method: 'POST'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Item deleted successfully', 'success');
            // Remove row from table
            document.querySelector(`tr[data-id="${id}"]`).remove();
        } else {
            showNotification('Failed to delete item', 'error');
        }
    })
    .catch(error => {
        showNotification('An error occurred', 'error');
    });
    */
}

// ========== ANIMATE STATS ==========
function animateStats() {
    const statNumbers = document.querySelectorAll('.stat-info h3');
    
    statNumbers.forEach(stat => {
        const finalValue = parseInt(stat.textContent.replace(/,/g, ''));
        if (isNaN(finalValue)) return;
        
        let currentValue = 0;
        const increment = Math.ceil(finalValue / 50);
        const duration = 1000; // 1 second
        const stepTime = duration / 50;
        
        const counter = setInterval(() => {
            currentValue += increment;
            if (currentValue >= finalValue) {
                currentValue = finalValue;
                clearInterval(counter);
            }
            stat.textContent = currentValue.toLocaleString();
        }, stepTime);
    });
}

// ========== REFRESH BUTTON ==========
function setupRefreshButton() {
    // Add refresh button to top bar if not exists
    const topBar = document.querySelector('.top-bar');
    if (topBar && !document.querySelector('.refresh-btn')) {
        const refreshBtn = document.createElement('button');
        refreshBtn.className = 'refresh-btn';
        refreshBtn.innerHTML = '<i class="fas fa-sync-alt"></i>';
        refreshBtn.title = 'Refresh Dashboard';
        refreshBtn.style.cssText = `
            background: #3498db;
            color: white;
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1rem;
            transition: all 0.3s;
            margin-left: 15px;
        `;
        
        refreshBtn.addEventListener('click', function() {
            this.querySelector('i').style.animation = 'spin 1s linear';
            setTimeout(() => {
                location.reload();
            }, 500);
        });
        
        refreshBtn.addEventListener('mouseenter', function() {
            this.style.background = '#2980b9';
            this.style.transform = 'scale(1.1)';
        });
        
        refreshBtn.addEventListener('mouseleave', function() {
            this.style.background = '#3498db';
            this.style.transform = 'scale(1)';
        });
        
        const userInfo = topBar.querySelector('.user-info');
        userInfo.parentNode.insertBefore(refreshBtn, userInfo);
    }
}

// ========== NOTIFICATION SYSTEM ==========
function showNotification(message, type = 'info') {
    // Remove existing notifications
    const existing = document.querySelector('.notification');
    if (existing) {
        existing.remove();
    }
    
    // Create notification
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    
    const colors = {
        success: '#27ae60',
        error: '#e74c3c',
        warning: '#f39c12',
        info: '#3498db'
    };
    
    const icons = {
        success: 'check-circle',
        error: 'exclamation-circle',
        warning: 'exclamation-triangle',
        info: 'info-circle'
    };
    
    notification.innerHTML = `
        <i class="fas fa-${icons[type]}"></i>
        <span>${message}</span>
    `;
    
    Object.assign(notification.style, {
        position: 'fixed',
        top: '20px',
        right: '20px',
        background: colors[type],
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
    
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
`;
document.head.appendChild(style);

// ========== SEARCH FUNCTIONALITY ==========
function setupSearch() {
    const searchInput = document.getElementById('tableSearch');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const tableRows = document.querySelectorAll('.data-table tbody tr');
            
            tableRows.forEach(row => {
                const text = row.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }
}

// ========== UTILITY FUNCTIONS ==========

// Format numbers
function formatNumber(num) {
    if (num >= 1000000) {
        return (num / 1000000).toFixed(1) + 'M';
    }
    if (num >= 1000) {
        return (num / 1000).toFixed(1) + 'K';
    }
    return num.toString();
}

// Confirm action
function confirmAction(message) {
    return confirm(message);
}

// Smooth scroll to top
function scrollToTop() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}

// ========== KEYBOARD SHORTCUTS ==========
document.addEventListener('keydown', function(e) {
    // Ctrl/Cmd + K for quick search
    if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
        e.preventDefault();
        const searchInput = document.getElementById('tableSearch');
        if (searchInput) {
            searchInput.focus();
        }
    }
    
    // Escape to close mobile menu
    if (e.key === 'Escape') {
        const sidebar = document.querySelector('.sidebar');
        if (sidebar) {
            sidebar.classList.remove('active');
        }
    }
});

// ========== CONSOLE MESSAGE ==========
console.log('%c📊 ReadQuest Admin Dashboard ', 'background: #667eea; color: white; font-size: 16px; padding: 8px;');
console.log('%cDashboard loaded successfully!', 'color: #27ae60; font-size: 12px;');