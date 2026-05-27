<?php
/**
 * Theme Fixes – Churum Meru Theme
 * Datei: inc/theme-fixes.php
 *
 * Einbinden in functions.php:
 *   require_once get_template_directory() . '/inc/theme-fixes.php';
 */


// =============================================================================
// 1. DARK MODE TOGGLE ENTFERNEN
// =============================================================================

add_action( 'wp_enqueue_scripts', function() {
    $handles = [
        'ml-dark-mode', 'media-lab-dark-mode', 'ml-darkmode',
        'agency-core-dark-mode', 'ml-color-scheme', 'ml-theme-toggle',
    ];
    foreach ( $handles as $h ) {
        wp_dequeue_script( $h );  wp_deregister_script( $h );
        wp_dequeue_style( $h );   wp_deregister_style( $h );
    }
}, 100 );

add_action( 'wp_head', function() { ?>
<style id="ml-dark-mode-toggle-fix">
    /* Alle bekannten Selektoren des Agency Core Dark Mode Toggles */
    .ml-dark-mode-toggle,
    .dark-mode-toggle,
    #dark-mode-toggle,
    [class*="dark-mode-toggle"],
    [id*="dark-mode-toggle"],
    .ml-theme-switcher,
    [data-dark-mode-toggle],
    .ml-color-scheme-toggle,
    #ml-color-scheme-toggle,
    /* Häufige generische Toggle-Klassen aus Agency Core */
    .toggle[data-toggle],
    button.toggle[id*="color"],
    button.toggle[id*="dark"],
    button.toggle[id*="theme"],
    button.toggle[id*="scheme"],
    /* Floating / fixed positionierte Toggles (kein Scroll-to-Top) */
    .toggle--fixed,
    .ml-floating-toggle,
    [class*="color-scheme-toggle"],
    [class*="theme-toggle"]:not([class*="mobile-menu"]) {
        display:    none !important;
        visibility: hidden !important;
        pointer-events: none !important;
    }
</style>
<?php }, 100 );


// =============================================================================
// 1b. BACK-TO-TOP BUTTON SICHERSTELLEN
//
// Agency Core rendert den Button via wp_footer-Hook.
// Falls er nicht erscheint (z.B. durch zu breite CSS-Selektoren versteckt),
// wird er hier explizit sichtbar gesetzt UND als Fallback neu gerendert.
// =============================================================================

/**
 * CSS: Back-to-Top explizit sichtbar (verhindert versehentliches Ausblenden)
 */
add_action( 'wp_head', function() { ?>
<style id="ml-back-to-top-fix">
    /* Back-to-Top Button IMMER sichtbar – überschreibt evtl. zu breite Selektoren */
    .ml-back-to-top,
    .back-to-top,
    #back-to-top,
    .scroll-to-top,
    #scroll-to-top,
    [class*="back-to-top"],
    [class*="scroll-to-top"],
    [id*="back-to-top"],
    [id*="scroll-to-top"] {
        display: flex !important;
        visibility: visible !important;
        pointer-events: auto !important;
    }
</style>
<?php }, 200 );

/**
 * Back-to-Top Button: direkt als HTML ausgeben.
 * Kein Check auf Agency Core – Button wird immer gerendert.
 * CSS versteckt/zeigt ihn per JS-Klasse.
 */
add_action( 'wp_footer', function() { ?>

<button id="ml-scroll-to-top"
        class="ml-scroll-to-top"
        aria-label="<?php esc_attr_e('Nach oben scrollen', 'churum-meru-theme'); ?>"
        type="button">
    &#9650;
</button>

<style>
    #ml-scroll-to-top {
        position:         fixed;
        bottom:           2rem;
        right:            2rem;
        width:            48px;
        height:           48px;
        background-color: #ff681a;
        color:            #ffffff;
        border:           none;
        border-radius:    4px;
        font-size:        18px;
        cursor:           pointer;
        z-index:          9000;
        display:          flex;
        align-items:      center;
        justify-content:  center;
        box-shadow:       0 4px 12px rgba(0,0,0,0.25);
        opacity:          0;
        visibility:       hidden;
        transform:        translateY(12px);
        transition:       opacity 0.3s ease, visibility 0.3s ease, transform 0.3s ease;
    }
    #ml-scroll-to-top.is-visible {
        opacity:    1;
        visibility: visible;
        transform:  translateY(0);
    }
    #ml-scroll-to-top:hover {
        background-color: #e64d00;
    }
</style>

<script>
(function() {
    var btn = document.getElementById('ml-scroll-to-top');
    if (!btn) return;

    window.addEventListener('scroll', function() {
        if (window.scrollY > 300) {
            btn.classList.add('is-visible');
        } else {
            btn.classList.remove('is-visible');
        }
    }, { passive: true });

    btn.addEventListener('click', function() {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
})();
</script>

<?php }, 99 );


// =============================================================================
// 1c. HERO IMAGE – HINTER DEN HEADER SCHIEBEN (Inline CSS, kein SCSS-Build nötig)
// =============================================================================

add_action( 'wp_head', function() { ?>
<style id="ml-hero-behind-header">
    /* Hero startet am oberen Bildschirmrand – schiebt sich hinter den sticky Header */
    .hero-image {
        margin-top: calc(-1 * var(--header-height, 64px)) !important;
    }

    /* Desktop: Header ist 88px hoch */
    @media (min-width: 992px) {
        .hero-image {
            margin-top: -88px !important;
        }
    }

    /* site-main: kein padding-top wenn Hero vorhanden */
    body.has-hero-image .site-main,
    body.has-hero-image main#main-content,
    html.has-hero-image .site-main {
        padding-top: 0 !important;
    }
</style>
<?php }, 150 );


// =============================================================================
// 2. "has-hero-image" BODY CLASS – per JavaScript
//
// Warum JS statt PHP:
//   - Kein Shortcode verwendet → has_shortcode() greift nicht
//   - ACF-Feldname unbekannt → get_field() greift nicht
//   - Hero wird via PHP-Template gerendert, NACH body_class()
//
// JS prüft ob .hero-image im DOM existiert und setzt die Klasse sofort
// (synchron, noch vor DOMContentLoaded → kein Flackern).
// =============================================================================

add_action( 'wp_head', function() { ?>
<script id="ml-has-hero-image-init">
(function() {
    // Läuft synchron im <head> – DOM noch nicht vollständig.
    // Wir setzen einen MutationObserver der auf .hero-image wartet
    // und die Klasse unmittelbar beim Erscheinen setzt.
    var applied = false;

    function applyClass() {
        if (applied) return;
        if (document.querySelector('.hero-image')) {
            document.documentElement.classList.add('has-hero-image');
            document.body && document.body.classList.add('has-hero-image');
            applied = true;
        }
    }

    // 1. Sofort prüfen (greift wenn Hero bereits im <head> bekannt ist)
    applyClass();

    // 2. MutationObserver: greift sobald .hero-image in den DOM kommt
    if (!applied && window.MutationObserver) {
        var observer = new MutationObserver(function() {
            applyClass();
            if (applied) observer.disconnect();
        });
        document.addEventListener('DOMContentLoaded', function() {
            applyClass(); // Nochmals nach vollständigem DOM
            observer.disconnect();
        });
        // Beobachte <body> sobald er existiert
        var bodyCheck = setInterval(function() {
            if (document.body) {
                clearInterval(bodyCheck);
                observer.observe(document.body, { childList: true, subtree: true });
                applyClass();
            }
        }, 0);
    }

    // 3. Sicherheitsnetz: spätestens nach DOMContentLoaded
    document.addEventListener('DOMContentLoaded', applyClass);
})();
</script>
<?php }, 5 ); // Priorität 5 → sehr früh im <head>