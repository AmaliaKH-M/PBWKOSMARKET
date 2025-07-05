// Mobile menu toggle
document.addEventListener('DOMContentLoaded', function() {
    const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
    const mobileMenu = document.querySelector('.mobile-menu');
    
    if (mobileMenuBtn && mobileMenu) {
        mobileMenuBtn.addEventListener('click', function() {
            mobileMenu.style.display = mobileMenu.style.display === 'block' ? 'none' : 'block';
        });
    }

    // Smooth scrolling for navigation links
    function smoothScroll(target) {
        const element = document.querySelector(target);
        if (element) {
            element.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    }

    // Add click handlers for navigation links
    document.querySelectorAll('.nav-link-section').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const target = this.getAttribute('href');
            smoothScroll(target);
        });
    });

    // Wishlist functionality
    document.querySelectorAll('.wishlist-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const productId = this.dataset.productId;
            const heart = this.querySelector('.heart');
            const isActive = this.classList.contains('active');
            
            // Toggle wishlist
            fetch('ajax/wishlist.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=${isActive ? 'remove' : 'add'}&product_id=${productId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.classList.toggle('active');
                    heart.textContent = this.classList.contains('active') ? '❤️' : '♡';
                    
                    // Show notification
                    showNotification(
                        this.classList.contains('active') ? 
                        'Ditambahkan ke wishlist!' : 
                        'Dihapus dari wishlist!',
                        'success'
                    );
                } else {
                    showNotification(data.message || 'Terjadi kesalahan!', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Terjadi kesalahan!', 'error');
            });
        });
    });

    // View product images
    document.querySelectorAll('.card-img, .gallery-image').forEach(image => {
        image.addEventListener('click', function() {
            showImageModal(this.src, this.alt);
        });
    });

    // Image gallery functionality
    document.querySelectorAll('.gallery-image').forEach(image => {
        image.addEventListener('click', function() {
            // Remove active class from all images
            document.querySelectorAll('.gallery-image').forEach(img => {
                img.classList.remove('active');
            });
            
            // Add active class to clicked image
            this.classList.add('active');
            
            // Update main image if exists
            const mainImage = document.querySelector('.main-product-image');
            if (mainImage) {
                mainImage.src = this.src;
                mainImage.alt = this.alt;
            }
        });
    });

    // Search functionality
    const searchInput = document.getElementById('search-input');
    const searchSuggestions = document.getElementById('search-suggestions');
    
    if (searchInput && searchSuggestions) {
        let searchTimeout;
        
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const query = this.value.trim();
            
            if (query.length < 2) {
                searchSuggestions.style.display = 'none';
                return;
            }
            
            searchTimeout = setTimeout(() => {
                fetch('ajax/search_suggestions.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `query=${encodeURIComponent(query)}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.suggestions && data.suggestions.length > 0) {
                        searchSuggestions.innerHTML = data.suggestions
                            .map(item => `<div class="suggestion-item" data-value="${item}">${item}</div>`)
                            .join('');
                        searchSuggestions.style.display = 'block';
                        
                        // Add click handlers to suggestions
                        document.querySelectorAll('.suggestion-item').forEach(item => {
                            item.addEventListener('click', function() {
                                searchInput.value = this.dataset.value;
                                searchSuggestions.style.display = 'none';
                                searchInput.form.submit();
                            });
                        });
                    } else {
                        searchSuggestions.style.display = 'none';
                    }
                })
                .catch(error => {
                    console.error('Search error:', error);
                    searchSuggestions.style.display = 'none';
                });
            }, 300);
        });
        
        // Hide suggestions when clicking outside
        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !searchSuggestions.contains(e.target)) {
                searchSuggestions.style.display = 'none';
            }
        });
    }

    // Load wishlist status for current user
    loadWishlistStatus();
});

// Show notification function
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${type === 'success' ? 'var(--success-green)' : type === 'error' ? 'var(--danger-red)' : 'var(--primary-red)'};
        color: white;
        padding: 1rem 1.5rem;
        border-radius: var(--border-radius);
        z-index: 1001;
        font-family: 'Poppins', sans-serif;
        font-weight: 500;
        box-shadow: var(--box-shadow);
        animation: slideIn 0.3s ease-out;
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease-in';
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }, 3000);
}

// Show image modal
function showImageModal(src, alt) {
    const modal = document.createElement('div');
    modal.className = 'modal';
    modal.innerHTML = `
        <div class="modal-content" style="max-width: 80%; text-align: center;">
            <span class="close">&times;</span>
            <img src="${src}" alt="${alt}" style="max-width: 100%; height: auto; border-radius: var(--border-radius);">
            <p style="margin-top: 1rem; font-family: 'Poppins', sans-serif;">${alt}</p>
        </div>
    `;
    
    document.body.appendChild(modal);
    modal.style.display = 'block';
    
    // Close modal handlers
    const closeBtn = modal.querySelector('.close');
    closeBtn.addEventListener('click', () => {
        modal.style.display = 'none';
        document.body.removeChild(modal);
    });
    
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.style.display = 'none';
            document.body.removeChild(modal);
        }
    });
}

// Load wishlist status
function loadWishlistStatus() {
    const wishlistButtons = document.querySelectorAll('.wishlist-btn');
    if (wishlistButtons.length === 0) return;
    
    const productIds = Array.from(wishlistButtons).map(btn => btn.dataset.productId);
    
    fetch('ajax/wishlist_status.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `product_ids=${productIds.join(',')}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.wishlist) {
            wishlistButtons.forEach(button => {
                const productId = button.dataset.productId;
                const heart = button.querySelector('.heart');
                
                if (data.wishlist.includes(productId)) {
                    button.classList.add('active');
                    heart.textContent = '❤️';
                } else {
                    button.classList.remove('active');
                    heart.textContent = '♡';
                }
            });
        }
    })
    .catch(error => {
        console.error('Error loading wishlist status:', error);
    });
}

// CSS for animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    
    @keyframes slideOut {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
    
    .search-suggestions {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border: 1px solid var(--border-color);
        border-top: none;
        border-radius: 0 0 var(--border-radius) var(--border-radius);
        box-shadow: var(--box-shadow);
        max-height: 300px;
        overflow-y: auto;
        z-index: 1000;
        display: none;
    }
    
    .suggestion-item {
        padding: 0.75rem 1rem;
        cursor: pointer;
        border-bottom: 1px solid var(--border-color);
        font-family: 'Poppins', sans-serif;
        transition: background-color 0.2s ease;
    }
    
    .suggestion-item:hover {
        background-color: var(--light-gray);
    }
    
    .suggestion-item:last-child {
        border-bottom: none;
    }
`;
document.head.appendChild(style);