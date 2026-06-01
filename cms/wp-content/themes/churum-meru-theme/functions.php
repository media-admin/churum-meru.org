<?php
/**
 * Media Lab Theme - Custom Theme
 * 
 * Presentation layer only. Business logic in plugins.
 * 
 * @package Custom_Theme
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

// Theme version
define('CUSTOM_THEME_VERSION', '1.4.0');

/**
 * Check Required Plugins
 */
function customtheme_check_required_plugins() {
    $required_plugins = array(
        'media-lab-agency-core' => 'Media Lab Agency Core',
    );
    
    $missing_plugins = array();
    
    foreach ($required_plugins as $plugin_slug => $plugin_name) {
        if (!is_plugin_active($plugin_slug . '/' . $plugin_slug . '.php')) {
            $missing_plugins[] = $plugin_name;
        }
    }
    
    if (!empty($missing_plugins)) {
        add_action('admin_notices', function() use ($missing_plugins) {
            echo '<div class="notice notice-warning"><p>';
            echo '<strong>Custom Theme:</strong> The following plugins are recommended: ';
            echo implode(', ', $missing_plugins);
            echo '</p></div>';
        });
    }
}
add_action('after_setup_theme', 'customtheme_check_required_plugins');

/**
 * Theme Setup
 */
function customtheme_setup() {
    // Theme support
    add_theme_support('post-thumbnails');
    add_theme_support('title-tag');
    add_theme_support('custom-logo');
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
    ));
    add_theme_support('responsive-embeds');
    add_theme_support('editor-styles');
    
    // Navigation menus
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'custom-theme'),
        'footer' => __('Footer Menu', 'custom-theme'),
        'footer-legal' => __('Footer Legal', 'custom-theme'),
    ));
    
    // Image sizes
    add_image_size('custom-thumbnail', 400, 300, true);
    add_image_size('custom-medium', 800, 600, true);
    add_image_size('custom-large', 1200, 900, true);
}
add_action('after_setup_theme', 'customtheme_setup');

/**
 * Load Theme Components
 */
require_once get_template_directory() . '/inc/enqueue.php';
require_once get_template_directory() . '/inc/performance.php';
require_once get_template_directory() . '/inc/shortcode-overrides.php';
require_once get_template_directory() . '/inc/theme-fixes.php';
require_once get_template_directory() . '/inc/customizer-footer.php';


// Optional components (only if files exist)
$optional_components = array(
    'walker-nav-menu.php',
    'helpers.php',
    'woocommerce.php',
    'woocommerce-emails.php',
    'acf-welcome.php',   // ACF-Felder: Welcome Page
    'welcome-mode.php',  // Weiterleitung: Welcome Mode (auskommentieren zum Deaktivieren)
);

foreach ($optional_components as $component) {
    $file = get_template_directory() . '/inc/' . $component;
    if (file_exists($file)) {
        require_once $file;
    }
}

/**
 * Theme Customizations
 */

// Customize excerpt length
add_filter('excerpt_length', function($length) {
    return 20;
});

// Customize excerpt more
add_filter('excerpt_more', function($more) {
    return '...';
});

/**
 * WooCommerce Support (if WooCommerce is active)
 */
if (class_exists('WooCommerce')) {
    add_theme_support('woocommerce');
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');
}

// =============================================================================
// Toggle Helper
// =============================================================================
if ( ! function_exists('medialab_toggle') ) {
    /**
     * Gibt ein Toggle-Element aus.
     *
     * @param string      $id      – Eindeutige ID (für aria-labelledby etc.)
     * @param bool|string $state   – true/'on' | false/'off' | 'unavailable'
     * @param string      $label   – Optionaler Label-Text
     * @param array       $args    – Zusätzliche Argumente:
     *                               'size'    => 'sm' | '' | 'lg'
     *                               'class'   => zusätzliche CSS-Klassen
     *                               'stacked' => bool
     */
    function medialab_toggle( string $id, $state = 'off', string $label = '', array $args = [] ) : void {
        // State normalisieren
        if ( $state === true  ) $state = 'on';
        if ( $state === false ) $state = 'off';
        if ( ! in_array( $state, [ 'on', 'off', 'unavailable' ], true ) ) $state = 'off';

        $size    = isset( $args['size'] ) ? sanitize_html_class( $args['size'] ) : '';
        $extra   = isset( $args['class'] ) ? ' ' . esc_attr( $args['class'] ) : '';
        $stacked = ! empty( $args['stacked'] );

        $classes = 'toggle';
        if ( $size )    $classes .= ' toggle--' . $size;
        if ( $stacked ) $classes .= ' toggle--stacked';
        $classes .= $extra;

        $aria_pressed  = $state === 'on' ? 'true' : 'false';
        $aria_disabled = $state === 'unavailable' ? ' aria-disabled="true"' : '';
        $tabindex      = $state === 'unavailable' ? ' tabindex="-1"' : '';
        $role          = $state !== 'unavailable' ? ' role="switch" aria-pressed="' . esc_attr( $aria_pressed ) . '"' : '';
        ?>
        <button
            id="<?php echo esc_attr( $id ); ?>"
            class="<?php echo esc_attr( $classes ); ?>"
            data-toggle="<?php echo esc_attr( $state ); ?>"
            <?php echo $role; // already escaped ?>
            <?php echo $aria_disabled; // already escaped ?>
            <?php echo $tabindex; // already escaped ?>
            type="button"
        >
            <span class="toggle__track" aria-hidden="true">
                <span class="toggle__thumb"></span>
            </span>
            <?php if ( $label ) : ?>
                <span class="toggle__label"><?php echo esc_html( $label ); ?></span>
            <?php endif; ?>
        </button>
        <?php
    }
}

// Body-Klasse für Impressum-Seite (Slug-basiert)
add_filter('body_class', function(array $classes): array {
    if (is_page('impressum')) {
        $classes[] = 'impressum-page';
    }
    return $classes;
});
// Hinweis: has-hero-image Body-Class kommt aus inc/theme-fixes.php ↑

// =============================================================================
// Footer Widget-Area – ans Ende der functions.php hängen
// =============================================================================

add_action( 'widgets_init', function() {

    // Widget-Area für die Kontakt-Spalte im Footer
    register_sidebar([
        'name'          => __( 'Footer: Kontakt', 'churum-meru-theme' ),
        'id'            => 'footer-contact',
        'description'   => __( 'Kontaktdaten im Footer (E-Mail, Telefon, Partner-Logo etc.)', 'churum-meru-theme' ),
        'before_widget' => '<div class="footer-contact-block %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="footer-contact-block__title">',
        'after_title'   => '</h4>',
    ]);

} );

require_once get_template_directory() . '/inc/footer-multilang.php';
 
// =============================================================================
// Logo-Breite als CSS Custom Property ausgeben
// Ans Ende der functions.php hängen.
//
// Gibt --site-logo-width in <style> im <head> aus.
// SCSS liest: max-width: var(--site-logo-width, 220px)
//
// Reihenfolge: WP Custom Logo → ACF → Fallback 160px
// =============================================================================

add_action( 'wp_head', function() {

    $width = 0;

    // 1. WordPress Custom Logo
    $logo_id = get_theme_mod( 'custom_logo' );
    if ( $logo_id ) {
        $src = wp_get_attachment_image_src( $logo_id, 'full' );
        if ( $src && ! empty( $src[1] ) ) {
            $width = intval( $src[1] );
        }
    }

    // 2. ACF Options (Agency Core Logo-Felder)
    if ( ! $width && function_exists( 'get_field' ) ) {
        foreach ( [ 'logo', 'header_logo', 'site_logo', 'logo_image' ] as $field ) {
            $img = get_field( $field, 'option' );
            if ( ! empty( $img['width'] ) ) {
                $width = intval( $img['width'] );
                break;
            }
        }
    }

    // 3. Fallback
    if ( ! $width ) $width = 160;

    echo '<style id="site-logo-width">:root{--site-logo-width:' . $width . 'px}</style>' . "\n";

}, 20 );
