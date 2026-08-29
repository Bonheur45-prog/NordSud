import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;

/**
 * Sticky header shadow — adds a class once the page has scrolled past a
 * small threshold, rather than showing a harsh shadow right at the top.
 */
Alpine.data('navbar', () => ({
    scrolled: false,
    openGroup: null,

    init() {
        this.onScroll();
        window.addEventListener('scroll', () => this.onScroll(), { passive: true });
    },

    onScroll() {
        this.scrolled = window.scrollY > 20;
    },

    toggleGroup(key) {
        this.openGroup = this.openGroup === key ? null : key;
    },

    closeGroups() {
        this.openGroup = null;
    },
}));

/**
 * Mobile slide-in drawer (right side). Locks body scroll while open so
 * the page behind it doesn't scroll along with the drawer's own content.
 */
Alpine.data('mobileMenu', () => ({
    open: false,

    show() {
        this.open = true;
        document.body.style.overflow = 'hidden';
    },

    hide() {
        this.open = false;
        document.body.style.overflow = '';
    },
}));

Alpine.start();
