/**
 * Adams Daktechniek — GA4 Event Tracking
 * Stuurt events naar Google Analytics 4 (G-Q11L7Z055S):
 *   • generate_lead   bij contact-form submit
 *   • whatsapp_click  bij elke wa.me link
 *   • phone_click     bij elke tel: link
 *   • cta_click       bij hero/sticky/FAB CTA-knoppen
 *
 * Vereist: gtag() functie staat al gelinkt in elke HTML <head>.
 * Faalt stil als gtag niet beschikbaar is (bv. ad-blocker).
 */

(function () {
    'use strict';

    function track(eventName, params) {
        if (typeof window.gtag !== 'function') return;
        try {
            window.gtag('event', eventName, params || {});
        } catch (e) { /* nooit page breken om analytics */ }
    }

    function pageContext() {
        return {
            page_path: location.pathname,
            page_title: document.title
        };
    }

    /* ── 1. Contact form: generate_lead ──────────────────────── */
    function hookContactForm() {
        var form = document.getElementById('contactForm');
        if (!form) return;
        form.addEventListener('submit', function () {
            // Diensten verzamelen
            var checked = form.querySelectorAll('input[name="dienst[]"]:checked');
            var diensten = Array.from(checked).map(function (c) { return c.value; }).join(', ');
            track('generate_lead', Object.assign(pageContext(), {
                form_id: 'contactForm',
                diensten: diensten || '(geen)',
                value: 1,
                currency: 'EUR'
            }));
        }, { capture: true });
    }

    /* ── 2. WhatsApp clicks ──────────────────────────────────── */
    function hookWhatsApp() {
        document.addEventListener('click', function (e) {
            var a = e.target.closest('a[href*="wa.me/"]');
            if (!a) return;
            track('whatsapp_click', Object.assign(pageContext(), {
                link_url: a.href,
                link_text: (a.textContent || '').trim().slice(0, 80)
            }));
        }, { capture: true });
    }

    /* ── 3. Bel-clicks (tel:) ────────────────────────────────── */
    function hookPhone() {
        document.addEventListener('click', function (e) {
            var a = e.target.closest('a[href^="tel:"]');
            if (!a) return;
            track('phone_click', Object.assign(pageContext(), {
                link_url: a.href,
                link_text: (a.textContent || '').trim().slice(0, 80)
            }));
        }, { capture: true });
    }

    /* ── 4. Generieke CTA-clicks (hero buttons, sticky-bar, FAB) ── */
    function hookCTAs() {
        document.addEventListener('click', function (e) {
            var a = e.target.closest('.btn-primary, .btn-accent, .mob-fab, .sticky-call-bar a, .floating-promo-btn');
            if (!a) return;
            // Skip wa.me/tel: — die zijn al hierboven afgehandeld
            if (a.matches('a[href*="wa.me/"], a[href^="tel:"]')) return;
            track('cta_click', Object.assign(pageContext(), {
                link_url: a.href || '',
                link_text: (a.textContent || '').trim().slice(0, 80),
                cta_class: a.className.split(' ')[0] || ''
            }));
        }, { capture: true });
    }

    /* ── Init ────────────────────────────────────────────────── */
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAll);
    } else {
        initAll();
    }
    function initAll() {
        hookContactForm();
        hookWhatsApp();
        hookPhone();
        hookCTAs();
    }
})();
