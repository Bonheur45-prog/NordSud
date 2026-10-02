import './bootstrap';
import './home-show';
import Alpine from 'alpinejs';

window.Alpine = Alpine;

/**
 * Sticky header shadow — adds a class once the page has scrolled past a
 * small threshold, rather than showing a harsh shadow right at the top.
 */
Alpine.data('navbar', () => ({
    scrolled: false,
    openGroup: null,
    hoverTimeout: null,

    init() {
        this.onScroll();
        window.addEventListener('scroll', () => this.onScroll(), { passive: true });
    },

    onScroll() {
        this.scrolled = window.scrollY > 20;
    },

    openGroupOnHover(key) {
        clearTimeout(this.hoverTimeout);
        this.openGroup = key;
    },

    closeGroupsOnLeave() {
        this.hoverTimeout = setTimeout(() => {
            this.openGroup = null;
        }, 150);
    },

    closeGroups() {
        clearTimeout(this.hoverTimeout);
        this.openGroup = null;
    },
}));

/**
 * Language dropdown selector with hover behavior
 */
Alpine.data('languageDropdown', () => ({
    open: false,
    closeTimeout: null,

    openLanguageDropdown() {
        clearTimeout(this.closeTimeout);
        this.open = true;
    },

    closeLanguageDropdown() {
        this.closeTimeout = setTimeout(() => {
            this.open = false;
        }, 150);
    },
}));

/**
 * Mobile slide-in drawer (right side). Locks body scroll while open so
 * the page behind it doesn't scroll along with the drawer's own content.
 */
Alpine.data('mobileMenu', () => ({
    open: false,

    toggle() {
        this.open = !this.open;
        document.body.style.overflow = this.open ? 'hidden' : '';
    },

    show() {
        this.open = true;
        document.body.style.overflow = 'hidden';
    },

    hide() {
        this.open = false;
        document.body.style.overflow = '';
    },
}));

/**
 * Mobile menu with accordion-style collapsible groups
 */
Alpine.data('mobileMenuData', () => ({
    open: false,
    openGroups: [],

    toggle() {
        this.open = !this.open;
        document.body.style.overflow = this.open ? 'hidden' : '';
    },

    show() {
        this.open = true;
        document.body.style.overflow = 'hidden';
    },

    hide() {
        this.open = false;
        document.body.style.overflow = '';
    },

    toggleGroup(key) {
        if (this.openGroups.includes(key)) {
            this.openGroups = this.openGroups.filter(g => g !== key);
        } else {
            this.openGroups.push(key);
        }
    },
}));

Alpine.start();
