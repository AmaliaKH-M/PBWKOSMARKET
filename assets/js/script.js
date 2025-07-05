// Mobile menu toggle
document.addEventListener('DOMContentLoaded', function() {
    const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
    const mobileMenu = document.querySelector('.mobile-menu');
    
    if (mobileMenuBtn && mobileMenu) {
        mobileMenuBtn.addEventListener('click', function() {
            mobileMenu.style.display = mobileMenu.style.display === 'block' ? 'none' : 'block';
        });
    }

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
});

// Smooth scroll to section
function scrollToSection(sectionId) {
    const element = document.getElementById(sectionId);
    if (element) {
        const offset = 100; // Account for fixed navbar
        const elementPosition = element.offsetTop - offset;
        
        window.scrollTo({
            top: elementPosition,
            behavior: 'smooth'
        });
    }
}

// Open image modal
function openImageModal(imageSrc) {
    const modal = document.getElementById('imageModal');
    const modalImage = document.getElementById('modalImage');
    
    if (modal && modalImage) {
        modalImage.src = imageSrc;
        modal.style.display = 'block';
    }
}

// Close image modal
function closeImageModal() {
    const modal = document.getElementById('imageModal');
    if (modal) {
        modal.style.display = 'none';
    }
}

// CSS for animations
const style = document.createElement('style');
style.textContent = `
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