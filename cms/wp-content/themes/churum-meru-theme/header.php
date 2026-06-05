<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
    <style id="ml-hero-padding-fix">
        /* ── CSS-Variable: Fallback = tatsächliche Desktop-Header-Höhe ─────────── */
        :root {
            --header-height: 125px; /* wird von JS nach window.load präzise gesetzt */
        }

        /* ── Hero: padding-top entfernen, overflow freigeben ─────────────────── */
        .site-main:has(> .hero-image:first-child) {
            padding-top: 0 !important;
            overflow: visible !important;
        }

        /* ── Hero: margin-top mit Variable ───────────────────────────────────── */
        .hero-image {
            margin-top: calc(-1 * var(--header-height)) !important;
        }

        /* ── Hero Desktop: max-height auf 500px (war 400px) ──────────────────── */
        @media (min-width: 992px) {
            .hero-image {
                max-height: 500px !important;
            }
        }

        /* ── Site-Header: 85% Opazität ───────────────────────────────────────── */
        /* Das Theme setzt background: var(--color-surface) als Shorthand →     */
        /* background-color allein reicht nicht, Shorthand muss überschrieben   */
        @media (min-width: 992px) {
            .site-header {
                background:              rgba(255, 255, 255, 0.85) !important;
                -webkit-backdrop-filter: blur(4px);
                backdrop-filter:         blur(4px);
            }
            .site-header,
            .site-navigation {
                min-height: 125px !important;
            }
            .site-logo__img--desktop {
                width: auto !important;
                height: 80px !important;
                max-width: 320px;
                object-fit: contain;
            }
        }

        /* ── Hero-Titel: vertikal zentriert zwischen Header-UK und Hero-UK ───── */
        /* Override vpos-bottom: Content nimmt volle Höhe ein */
        .hero-image--vpos-bottom {
            align-items: stretch !important;
        }
        /* Padding-top = Header-Höhe → verbleibender Raum wird zentriert */
        .hero-image--vpos-bottom .hero-image__content {
            display:        flex !important;
            flex-direction: column !important;
            justify-content: center !important;
            padding-top:    var(--header-height) !important;
            box-sizing:     border-box !important;
        }
    </style>
    <script>
        // --header-height dynamisch setzen – NACH vollständigem Load
        // (Fonts + Logo müssen geladen sein für korrekte Header-Höhe)
        (function() {
            function setHeaderHeight() {
                var h = document.querySelector('.site-header');
                if (h) {
                    document.documentElement.style.setProperty(
                        '--header-height', h.offsetHeight + 'px'
                    );
                }
            }
            // window.load → Fonts, Bilder und Admin-Bar sind bereit
            window.addEventListener('load', setHeaderHeight);
            // Resize (z.B. Orientierungswechsel, Admin-Bar)
            window.addEventListener('resize', setHeaderHeight, { passive: true });
        })();
    </script>
    <script>
        // Dark Mode deaktiviert – Website verwendet ausschließlich Light Mode
        (function() {
            document.documentElement.setAttribute('data-theme', 'light');
            localStorage.removeItem('theme-preference');
        })();
    </script>
    <style id="ml-cf7-select-fix">
        /* CF7 Select: macOS WebKit rendert mit appearance:none keinen Text.
           Workaround: color transparent + text-shadow erzeugt sichtbaren Text
           unabhängig vom Browser-Rendering-Bug.
           Eigener Theme-Pfeil wird per background-image wiederhergestellt. */
        .wpcf7-form-control.wpcf7-select,
        .wpcf7 select {
            -webkit-appearance:      none !important;
            appearance:              none !important;
            color:                   rgba(0,0,0,0) !important;
            -webkit-text-fill-color: rgba(0,0,0,0) !important;
            text-shadow:             0 0 0 #374151 !important;
            background-image:        url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath fill='%23999' d='M6 8L0 0h12z'/%3E%3C/svg%3E") !important;
        }

        /* Datumsfeld: Format-Platzhalter (tt.mm.jjjj) wieder dunkelgrau */
        .wpcf7 input[type="date"]::-webkit-datetime-edit {
            color:                   #6b7280 !important;
            -webkit-text-fill-color: #6b7280 !important;
        }
    </style>
</head>
<body <?php body_class(); ?>>

<?php wp_body_open(); ?>

<a class="skip-link" href="#main-content">
    <?php esc_html_e( 'Zum Inhalt springen', 'custom-theme' ); ?>
</a>

<?php
// ── Scroll Progress Bar ───────────────────────────────────────────────────
if ( function_exists('get_field') && get_field('scroll_progress_enabled', 'option') ) :
    $scope = get_field('scroll_progress_scope', 'option') ?: 'single';
    $show_progress = match($scope) {
        'singular' => is_singular(),
        'all'      => true,
        default    => is_single(),
    };
    if ( $show_progress ) : ?>
<div
    class="scroll-progress"
    role="progressbar"
    aria-valuemin="0"
    aria-valuemax="100"
    aria-valuenow="0"
    aria-label="<?php esc_attr_e('Lesefortschritt', 'custom-theme'); ?>"
></div>
<?php endif; endif; ?>

<?php
/**
 * Top Header (über dem Main Header)
 */
if ( function_exists( 'get_field' ) && get_field( 'top_header_enable', 'option' ) ) :
    $th_address = get_field( 'top_header_address', 'option' );
    $th_hours   = get_field( 'top_header_hours',   'option' );
    $th_phone   = get_field( 'top_header_phone',   'option' );
    $th_email   = get_field( 'top_header_email',   'option' );
    $th_social  = get_field( 'top_header_social',  'option' );
    $th_style   = get_field( 'top_header_style',   'option' );

    $bg_class     = 'top-header--' . esc_attr( $th_style['background'] ?? 'primary' );
    $mobile_class = 'top-header--mobile-' . esc_attr( $th_style['mobile'] ?? 'toggle' );

    $th_renderers = [

        'address' => function () use ( $th_address ) {
            if ( empty( $th_address['enable'] ) || ( empty( $th_address['street'] ) && empty( $th_address['city'] ) ) ) return;
            ?>
            <span class="top-header__item top-header__address">
                <svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                <?php if ( ! empty( $th_address['maps_link'] ) ) : ?>
                    <a href="<?php echo esc_url( $th_address['maps_link'] ); ?>" target="_blank" rel="noopener">
                <?php endif; ?>
                <?php echo esc_html( trim( ( $th_address['street'] ?? '' ) . ', ' . ( $th_address['city'] ?? '' ), ', ' ) ); ?>
                <?php if ( ! empty( $th_address['country'] ) ) : ?>, <?php echo esc_html( $th_address['country'] ); ?><?php endif; ?>
                <?php if ( ! empty( $th_address['maps_link'] ) ) : ?></a><?php endif; ?>
            </span>
            <?php
        },

        'hours' => function () use ( $th_hours ) {
            if ( empty( $th_hours['enable'] ) || empty( $th_hours['text'] ) ) return;
            ?>
            <span class="top-header__item top-header__hours">
                <svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                <?php echo esc_html( $th_hours['text'] ); ?>
            </span>
            <?php
        },

        'phone' => function () use ( $th_phone ) {
            if ( empty( $th_phone['enable'] ) || empty( $th_phone['number'] ) ) return;
            ?>
            <span class="top-header__item top-header__phone">
                <svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.61 3.53 2 2 0 0 1 3.6 1.37h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L7.91 9a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                <a href="tel:<?php echo esc_attr( preg_replace( '/\s+/', '', $th_phone['number'] ) ); ?>">
                    <?php echo esc_html( ! empty( $th_phone['display'] ) ? $th_phone['display'] : $th_phone['number'] ); ?>
                </a>
            </span>
            <?php
        },

        'email' => function () use ( $th_email ) {
            if ( empty( $th_email['enable'] ) || empty( $th_email['address'] ) ) return;
            ?>
            <span class="top-header__item top-header__email">
                <svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                <a href="mailto:<?php echo esc_attr( $th_email['address'] ); ?>">
                    <?php echo esc_html( $th_email['address'] ); ?>
                </a>
            </span>
            <?php
        },

    ];

    $social_links = [
        'facebook'  => [ 'label' => 'Facebook',   'icon' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>' ],
        'instagram' => [ 'label' => 'Instagram',  'icon' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>' ],
        'linkedin'  => [ 'label' => 'LinkedIn',   'icon' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"/><rect x="2" y="9" width="4" height="12"/><circle cx="4" cy="4" r="2"/></svg>' ],
        'twitter'   => [ 'label' => 'X / Twitter','icon' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>' ],
        'youtube'   => [ 'label' => 'YouTube',    'icon' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M22.54 6.42a2.78 2.78 0 0 0-1.95-1.96C18.88 4 12 4 12 4s-6.88 0-8.59.46a2.78 2.78 0 0 0-1.95 1.96A29 29 0 0 0 1 12a29 29 0 0 0 .46 5.58A2.78 2.78 0 0 0 3.41 19.54C5.12 20 12 20 12 20s6.88 0 8.59-.46a2.78 2.78 0 0 0 1.95-1.96A29 29 0 0 0 23 12a29 29 0 0 0-.46-5.58z"/><polygon points="9.75,15.02 15.5,12 9.75,8.98 9.75,15.02" fill="#fff"/></svg>' ],
        'xing'      => [ 'label' => 'Xing',       'icon' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M18.188 0c-.517 0-.741.325-.927.66 0 0-7.455 13.224-7.702 13.657.015.024 4.919 9.023 4.919 9.023.17.308.436.66.967.66h3.454c.211 0 .375-.078.463-.22.089-.151.089-.346-.009-.536l-4.879-8.916c-.004-.006-.004-.016 0-.022L22.139.756c.095-.191.097-.387.006-.535C22.056.078 21.894 0 21.686 0h-3.498zM3.648 4.74c-.211 0-.385.074-.473.216-.09.149-.078.339.02.531l2.34 4.05c.004.01.004.016 0 .021L1.86 16.051c-.099.188-.093.381 0 .529.085.142.239.234.45.234h3.461c.518 0 .766-.348.945-.667l3.734-6.609-2.378-4.155c-.172-.315-.434-.659-.962-.659H3.648v.016z"/></svg>' ],
    ];

    $th_order     = function_exists( 'medialab_get_top_header_order' )
        ? medialab_get_top_header_order()
        : array_keys( $th_renderers );

    $social_order = function_exists( 'medialab_get_top_header_social_order' )
        ? medialab_get_top_header_social_order()
        : array_keys( $social_links );
?>
<div class="top-header <?php echo $bg_class . ' ' . $mobile_class; ?>" role="complementary" aria-label="Kontaktinformationen">
    <div class="top-header__inner container">

        <div class="top-header__contact">
            <?php foreach ( $th_order as $key ) {
                if ( isset( $th_renderers[ $key ] ) ) $th_renderers[ $key ]();
            } ?>
        </div>

        <?php if ( ! empty( $th_social['enable'] ) ) : ?>
        <div class="top-header__social">
            <?php foreach ( $social_order as $key ) :
                if ( empty( $social_links[ $key ] ) || empty( $th_social[ $key ] ) ) continue;
                $data = $social_links[ $key ]; ?>
            <a href="<?php echo esc_url( $th_social[ $key ] ); ?>"
               class="top-header__social-link top-header__social-<?php echo esc_attr( $key ); ?>"
               target="_blank" rel="noopener noreferrer"
               aria-label="<?php echo esc_attr( $data['label'] ); ?>">
                <?php echo $data['icon']; ?>
            </a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

    </div>
</div>
<?php endif; // top_header_enable ?>



<header class="site-header" role="banner">
    <nav class="site-navigation" role="navigation" aria-label="Primary Navigation">
        <!-- Logo -->
        <a href="<?php echo esc_url(home_url('/')); ?>" class="site-logo" aria-label="<?php bloginfo('name'); ?>">
            <?php
            $logo_desktop       = function_exists('get_field') ? get_field('logo_desktop', 'option')       : null;
            $logo_mobile        = function_exists('get_field') ? get_field('logo_mobile', 'option')        : null;
            $logo_desktop_width = function_exists('get_field') ? get_field('logo_desktop_width', 'option') : 180;
            $logo_mobile_width  = function_exists('get_field') ? get_field('logo_mobile_width', 'option')  : 120;

            if ($logo_desktop) :
                echo '<img src="' . esc_url($logo_desktop['url']) . '"'
                   . ' alt="' . esc_attr($logo_desktop['alt'] ?: get_bloginfo('name')) . '"'
                   . ' width="' . esc_attr($logo_desktop_width) . '"'
                   . ' class="site-logo__img site-logo__img--desktop"'
                   . ' loading="eager">';

                if ($logo_mobile) :
                    echo '<img src="' . esc_url($logo_mobile['url']) . '"'
                       . ' alt="' . esc_attr($logo_mobile['alt'] ?: get_bloginfo('name')) . '"'
                       . ' width="' . esc_attr($logo_mobile_width) . '"'
                       . ' class="site-logo__img site-logo__img--mobile"'
                       . ' loading="eager">';
                endif;

            else :
                echo '<span class="site-logo__text">' . esc_html(get_bloginfo('name')) . '</span>';
            endif;
            ?>
        </a>

        <!-- Desktop Menu -->
        <div class="primary-menu">
            <?php
            wp_nav_menu(array(
                'theme_location' => 'primary',
                'container'      => false,
                'menu_class'     => '',
                'fallback_cb'    => false,
                'depth'          => 4,
            ));
            ?>
        </div>

        <!-- Mobile Toggle -->
        <button class="mobile-menu-toggle" aria-label="Toggle Menu" aria-expanded="false">
            <span></span>
        </button>
    </nav>
</header>

<!-- Mobile Menu -->
<div class="mobile-menu" role="navigation" aria-label="Mobile Navigation">
    <?php
    wp_nav_menu(array(
        'theme_location' => 'primary',
        'container'      => false,
        'menu_class'     => '',
        'fallback_cb'    => false,
        'depth'          => 4,
    ));
    ?>
</div>

<!-- Mobile Overlay -->
<div class="mobile-menu-overlay"></div>

<main id="main-content" class="site-main" tabindex="-1">