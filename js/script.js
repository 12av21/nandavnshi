// Back to Top Button
const backToTopButton = document.querySelector('.back-to-top');
// Guard: if the element does not exist, skip attaching handlers
if (backToTopButton) {

    window.addEventListener('scroll', () => {
        if (window.pageYOffset > 300) {
            backToTopButton.style.display = 'block';
        } else {
            backToTopButton.style.display = 'none';
        }
    });

    backToTopButton.addEventListener('click', (e) => {
        e.preventDefault();
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });

}

// Smooth scrolling for anchor links
// Smooth scrolling for anchor links that reference actual IDs (ignore bare '#')
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    const href = anchor.getAttribute('href');
    if (!href || href === '#') return; // ignore placeholders

    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        try {
            const target = document.querySelector(href);
            if (target) {
                target.scrollIntoView({ behavior: 'smooth' });
            }
        } catch (err) {
            // Invalid selector or other issue - silently ignore to avoid uncaught exceptions
            console.debug('Smooth scroll skipped for invalid selector:', href, err);
        }
    });
});

// Initialize tooltips
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
});

// Mobile menu close on click
const navLinks = document.querySelectorAll('.nav-link');
const menuToggle = document.getElementById('mainNav');
const bsCollapse = new bootstrap.Collapse(menuToggle, {toggle: false});

navLinks.forEach(l => {
    l.addEventListener('click', () => {
        if (window.innerWidth < 992) {
            bsCollapse.toggle();
        }
    });
});

// Add animation on scroll
const animateOnScroll = () => {
    const elements = document.querySelectorAll('.card, .section-title, .event-item');
    
    elements.forEach(element => {
        const elementPosition = element.getBoundingClientRect().top;
        const screenPosition = window.innerHeight / 1.3;
        
        if (elementPosition < screenPosition) {
            element.style.opacity = '1';
            element.style.transform = 'translateY(0)';
        }
    });
};

// Set initial styles for animation
document.addEventListener('DOMContentLoaded', () => {
    const animatedElements = document.querySelectorAll('.card, .section-title, .event-item');
    animatedElements.forEach(element => {
        element.style.opacity = '0';
        element.style.transform = 'translateY(20px)';
        element.style.transition = 'opacity 0.6s ease-out, transform 0.6s ease-out';
    });
    
    // Trigger initial animation
    setTimeout(animateOnScroll, 300);
});

window.addEventListener('scroll', animateOnScroll);

// Form validation for search
const searchForm = document.querySelector('.search-box form');
if (searchForm) {
    searchForm.addEventListener('submit', (e) => {
        const searchInput = searchForm.querySelector('input[type="text"]');
        if (searchInput.value.trim() === '') {
            e.preventDefault();
            searchInput.focus();
        }
    });
}

// Add active class to current nav item
const currentLocation = location.href;
const menuItems = document.querySelectorAll('.navbar-nav .nav-link');
const menuLength = menuItems.length;

for (let i = 0; i < menuLength; i++) {
    if (menuItems[i].href === currentLocation) {
        menuItems[i].classList.add('active');
        // Also add active class to parent if it's a dropdown
        const parentItem = menuItems[i].closest('.dropdown');
        if (parentItem) {
            const parentLink = parentItem.querySelector('.nav-link');
            if (parentLink) {
                parentLink.classList.add('active');
            }
        }
    }
}

// Handle dropdown menu on hover (for desktop)
if (window.innerWidth > 991) {
    const dropdowns = document.querySelectorAll('.dropdown');
    
    dropdowns.forEach(dropdown => {
        dropdown.addEventListener('mouseenter', () => {
            const dropdownMenu = dropdown.querySelector('.dropdown-menu');
            dropdownMenu.classList.add('show');
        });
        
        dropdown.addEventListener('mouseleave', () => {
            const dropdownMenu = dropdown.querySelector('.dropdown-menu');
            dropdownMenu.classList.remove('show');
        });
    });
}

// Make table responsive
const tables = document.querySelectorAll('table');
if (tables.length > 0) {
    tables.forEach(table => {
        const wrapper = document.createElement('div');
        wrapper.className = 'table-responsive';
        table.parentNode.insertBefore(wrapper, table);
        wrapper.appendChild(table);
    });
}

// Add loading animation for all links and forms
document.addEventListener('click', (e) => {
    const target = e.target.closest('a, button[type="submit"], input[type="submit"]');
    
    if (target && !target.hasAttribute('data-no-loader')) {
        // Add loading state to buttons
        if (target.tagName === 'BUTTON' || target.type === 'submit') {
            target.setAttribute('data-original-text', target.innerHTML);
            target.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Loading...';
            target.disabled = true;
        }
    }
});

// Initialize alert banner scrolling (dynamic duration based on text width)
document.addEventListener('DOMContentLoaded', () => {
    const banner = document.querySelector('.alert-banner');
    if (!banner) return;
    const text = banner.querySelector('.alert-text');
    if (!text) return;

    // ensure text is inline-block for width measurement
    text.style.display = 'inline-block';
    const viewportWidth = banner.offsetWidth || window.innerWidth;
    const textWidth = text.scrollWidth;
    const distance = viewportWidth + textWidth;
    // base speed: 100 pixels per second
    const duration = Math.max(8, Math.round(distance / 100)); // at least 8s

    text.style.animation = `alert-scroll ${duration}s linear infinite`;
    text.style.paddingLeft = '0';

    // Pause animation on hover
    banner.addEventListener('mouseenter', () => {
        text.style.animationPlayState = 'paused';
    });

    // Resume animation on mouse leave
    banner.addEventListener('mouseleave', () => {
        text.style.animationPlayState = 'running';
    });
});
