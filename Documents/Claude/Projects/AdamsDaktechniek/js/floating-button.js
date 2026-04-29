/**
 * Floating Button Component
 * Displays a persistent "50% OFF" floating button on the page
 * Sticky positioning to remain visible while scrolling
 */

class FloatingPromoButto {
    constructor(options = {}) {
        this.options = {
            text: options.text || '50% OFF',
            subtext: options.subtext || 'Nieuwe klanten',
            link: options.link || '/pages/onze-acties.html',
            position: options.position || { bottom: '1.25rem', right: '1.25rem' },
            showOnScroll: options.showOnScroll !== false, // Show after scrolling
            scrollThreshold: options.scrollThreshold || 300, // pixels
            ...options
        };
        
        this.element = null;
        this.isVisible = true;
        this.init();
    }

    init() {
        this.createButton();
        this.attachEventListeners();
        
        if (this.options.showOnScroll) {
            this.element.style.opacity = '0';
            this.element.style.pointerEvents = 'none';
            this.isVisible = false;
        }
    }

    createButton() {
        this.element = document.createElement('a');
        this.element.href = this.options.link;
        this.element.className = 'floating-promo-btn';
        this.element.setAttribute('aria-label', `${this.options.text} - ${this.options.subtext}`);
        
        this.element.innerHTML = `
            <span class="floating-promo-btn__badge">${this.options.text}</span>
            <span class="floating-promo-btn__text">${this.options.subtext}</span>
        `;

        // Apply inline styles
        Object.assign(this.element.style, {
            position: 'fixed',
            bottom: this.options.position.bottom || '1.25rem',
            right: this.options.position.right || '1.25rem',
            background: 'var(--color-accent, #FF6B35)',
            color: '#fff',
            padding: '0.75rem 1.2rem',
            borderRadius: '50px',
            fontWeight: '700',
            fontSize: '0.9rem',
            boxShadow: '0 8px 24px rgba(255, 107, 53, 0.4)',
            zIndex: '90',
            textDecoration: 'none',
            display: 'flex',
            alignItems: 'center',
            gap: '0.5rem',
            cursor: 'pointer',
            border: 'none',
            transition: 'all 0.3s ease',
            whiteSpace: 'nowrap'
        });

        document.body.appendChild(this.element);
    }

    attachEventListeners() {
        // Hover effect
        this.element.addEventListener('mouseenter', () => {
            if (this.isVisible) {
                this.element.style.transform = 'scale(1.1)';
                this.element.style.boxShadow = '0 12px 32px rgba(255, 107, 53, 0.5)';
            }
        });

        this.element.addEventListener('mouseleave', () => {
            this.element.style.transform = 'scale(1)';
            this.element.style.boxShadow = '0 8px 24px rgba(255, 107, 53, 0.4)';
        });

        // Show on scroll if enabled
        if (this.options.showOnScroll) {
            window.addEventListener('scroll', () => this.handleScroll(), { passive: true });
        }
    }

    handleScroll() {
        const scrolled = window.scrollY || document.documentElement.scrollTop;

        if (scrolled > this.options.scrollThreshold && !this.isVisible) {
            this.show();
        } else if (scrolled <= this.options.scrollThreshold && this.isVisible) {
            this.hide();
        }
    }

    show() {
        this.isVisible = true;
        this.element.style.opacity = '1';
        this.element.style.pointerEvents = 'auto';
    }

    hide() {
        this.isVisible = false;
        this.element.style.opacity = '0';
        this.element.style.pointerEvents = 'none';
    }

    remove() {
        if (this.element && this.element.parentNode) {
            this.element.parentNode.removeChild(this.element);
        }
    }

    updateText(text, subtext) {
        if (text) this.options.text = text;
        if (subtext) this.options.subtext = subtext;
        
        const badge = this.element.querySelector('.floating-promo-btn__badge');
        const textSpan = this.element.querySelector('.floating-promo-btn__text');
        
        if (badge) badge.textContent = this.options.text;
        if (textSpan) textSpan.textContent = this.options.subtext;
    }
}

// Auto-initialize if data attribute is present
document.addEventListener('DOMContentLoaded', () => {
    if (document.body.hasAttribute('data-floating-promo')) {
        window.floatingPromoButton = new FloatingPromoButto({
            text: document.body.getAttribute('data-promo-text') || '50% OFF',
            subtext: document.body.getAttribute('data-promo-subtext') || 'Nieuwe klanten',
            link: document.body.getAttribute('data-promo-link') || '/pages/onze-acties.html'
        });
    }
});
