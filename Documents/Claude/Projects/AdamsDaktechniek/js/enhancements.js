/* Adams Daktechniek — UX enhancements (v1)
 * - markeert lazy-loaded images als geladen (CSS fade-in)
 * - voegt <main id="main"> aria-target voor skip-link toe als die ontbreekt
 * - voegt is-scrolled class toe aan <body> bij scroll (sticky header polish)
 * - smooth-scroll fallback voor in-page anchors
 */
(function () {
    'use strict';

    // ---- 1. JS-detect (verwijdert no-js class) ----
    document.documentElement.classList.remove('no-js');
    document.documentElement.classList.add('js');

    // ---- 2. Lazy-load fade-in ----
    function markLoaded(img) {
        img.classList.add('is-loaded');
    }
    document.querySelectorAll('img[loading="lazy"]').forEach(function (img) {
        if (img.complete && img.naturalWidth !== 0) {
            markLoaded(img);
        } else {
            img.addEventListener('load', function () { markLoaded(img); }, { once: true });
            img.addEventListener('error', function () { markLoaded(img); }, { once: true });
        }
    });

    // ---- 3. Body scroll-state ----
    var scrollTimer = null;
    function onScroll() {
        if (scrollTimer) return;
        scrollTimer = window.requestAnimationFrame(function () {
            var y = window.pageYOffset || document.documentElement.scrollTop;
            document.body.classList.toggle('is-scrolled', y > 40);
            scrollTimer = null;
        });
    }
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();

    // ---- 4. Smooth in-page anchor scroll (waar browser scroll-behavior niet werkt) ----
    document.addEventListener('click', function (e) {
        var a = e.target.closest('a[href^="#"]');
        if (!a) return;
        var id = a.getAttribute('href').slice(1);
        if (!id || id === 'main') return;
        var t = document.getElementById(id);
        if (!t) return;
        e.preventDefault();
        t.scrollIntoView({ behavior: 'smooth', block: 'start' });
        // update URL zonder jump
        if (history.pushState) history.pushState(null, '', '#' + id);
        // focus voor a11y
        t.setAttribute('tabindex', '-1');
        t.focus({ preventScroll: true });
    });

    // ---- 5. Externe links: rel="noopener noreferrer" defensief ----
    document.querySelectorAll('a[target="_blank"]').forEach(function (a) {
        var rel = (a.getAttribute('rel') || '').toLowerCase();
        if (!rel.includes('noopener')) {
            a.setAttribute('rel', (rel ? rel + ' ' : '') + 'noopener noreferrer');
        }
    });
})();
