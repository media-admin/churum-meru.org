<?php
/**
 * Page Template – Churum Meru Theme
 *
 * Hero-Status wird VOR get_header() geprüft.
 */

// ── Hero-Felder VOR get_header() auslesen ────────────────────────────────────

$cm_show_hero = function_exists('get_field') && get_field('hero_image_show');

if ( $cm_show_hero ) {

    // body_class-Filter → hat-page-hero Klasse für CSS-Targeting
    add_filter( 'body_class', function( $classes ) {
        $classes[] = 'has-page-hero';
        return $classes;
    });

    // wp_head-Hook (Priority 999 = nach allen Theme-Styles)
    add_action( 'wp_head', function() {
        echo '<style id="ml-page-hero-fix">'
           // site-main: kein overflow-clipping, kein padding
           . '.has-page-hero .site-main{'
           . 'padding-top:0!important;'
           . 'overflow:visible!important;'
           . '}'
           . '</style>';
    }, 999 );
}

get_header();

// ── Hero Image ────────────────────────────────────────────────────────────────

if ( $cm_show_hero ) :

    $img_desktop = get_field('hero_image_desktop');

    if ( ! $img_desktop ) {
        $img_desktop = get_field( 'hero_fallback_desktop', 'option' );
    }

    if ( ! $img_desktop && has_post_thumbnail() ) {
        $tid = get_post_thumbnail_id();
        $src = wp_get_attachment_image_src( $tid, 'full' );
        if ( $src ) {
            $img_desktop = [
                'url'    => $src[0],
                'width'  => $src[1],
                'height' => $src[2],
                'alt'    => get_post_meta( $tid, '_wp_attachment_image_alt', true ),
            ];
        }
    }

    $img_mobile = get_field('hero_image_mobile')
               ?: get_field('hero_fallback_mobile', 'option');

    $hero_title  = get_field('hero_image_title')   ?: get_the_title();
    $hero_sub    = get_field('hero_image_subtitle') ?: '';
    $hero_btn    = get_field('hero_btn1_text')      ?: '';

    $hero_align  = get_field('hero_image_align')
                ?: get_field('hero_default_align', 'option')
                ?: 'center';
    $hero_height = get_field('hero_image_height')
                ?: get_field('hero_default_height', 'option')
                ?: 'md';
    $hero_vpos   = get_field('hero_image_vpos') ?: 'bottom';

    $page_opacity   = get_field('hero_image_opacity');
    $global_opacity = get_field('hero_overlay_opacity', 'option');
    $hero_opacity   = ( $page_opacity !== null && $page_opacity !== '' )
        ? floatval( $page_opacity )
        : ( $global_opacity !== null ? floatval( $global_opacity ) / 100 : 0.3 );

    // Klassen & Höhen
    $is_numeric_height = is_numeric( $hero_height );
    $height_class      = $is_numeric_height ? '' : 'hero-image--' . sanitize_html_class( $hero_height );
    $height_style_attr = $is_numeric_height ? 'height:' . intval($hero_height) . 'px;' : '';
    $no_image_class    = $img_desktop ? '' : ' hero-image--no-bg';

    $hero_classes = trim( implode( ' ', array_filter([
        'hero-image',
        $height_class,
        'hero-image--align-' . sanitize_html_class( $hero_align ),
        'hero-image--vpos-'  . sanitize_html_class( $hero_vpos ),
    ]) ) ) . $no_image_class;

    // Bild Desktop
    $d_url = $d_alt = $d_w = $d_h = '';
    if ( $img_desktop ) {
        if ( is_array($img_desktop) ) {
            $d_url = $img_desktop['url']    ?? '';
            $d_alt = $img_desktop['alt']    ?? '';
            $d_w   = $img_desktop['width']  ?? '';
            $d_h   = $img_desktop['height'] ?? '';
        } else {
            $src   = wp_get_attachment_image_src( intval($img_desktop), 'full' );
            $d_url = $src[0] ?? '';
            $d_w   = $src[1] ?? '';
            $d_h   = $src[2] ?? '';
            $d_alt = get_post_meta( intval($img_desktop), '_wp_attachment_image_alt', true );
        }
    }

    // Bild Mobile
    $m_url = '';
    if ( $img_mobile ) {
        $m_url = is_array($img_mobile)
            ? ( $img_mobile['url'] ?? '' )
            : wp_get_attachment_url( intval($img_mobile) );
    }

    // ── Inline-Style direkt auf dem <section>-Element ─────────────────────────
    // Umgeht CSS-Spezifitätsprobleme und overflow-Clipping komplett.
    // var(--header-height, 88px) wird vom Theme gesetzt; 88px ist der Fallback.
    $section_inline = $height_style_attr
        . 'position:relative;'
        . 'z-index:0;';
    ?>

    <section class="<?php echo esc_attr( $hero_classes ); ?>"
             style="<?php echo esc_attr( $section_inline ); ?>"
             aria-label="<?php echo esc_attr( $hero_title ); ?>">

        <?php if ( $d_url ) : ?>
            <?php if ( $m_url ) : ?>
                <picture>
                    <source media="(max-width: 767px)"
                            srcset="<?php echo esc_url( $m_url ); ?>">
                    <img class="hero-image__img"
                         src="<?php echo esc_url( $d_url ); ?>"
                         alt="<?php echo esc_attr( $d_alt ); ?>"
                         <?php if ($d_w) echo 'width="'  . esc_attr($d_w) . '"'; ?>
                         <?php if ($d_h) echo 'height="' . esc_attr($d_h) . '"'; ?>
                         loading="eager" fetchpriority="high">
                </picture>
            <?php else : ?>
                <img class="hero-image__img"
                     src="<?php echo esc_url( $d_url ); ?>"
                     alt="<?php echo esc_attr( $d_alt ); ?>"
                     <?php if ($d_w) echo 'width="'  . esc_attr($d_w) . '"'; ?>
                     <?php if ($d_h) echo 'height="' . esc_attr($d_h) . '"'; ?>
                     loading="eager" fetchpriority="high">
            <?php endif; ?>

            <div class="hero-image__overlay"
                 style="--hero-opacity: <?php echo esc_attr( $hero_opacity ); ?>"
                 aria-hidden="true"></div>
        <?php endif; ?>

        <div class="hero-image__content container">
            <div class="hero-image__inner">

                <?php if ( $hero_title ) : ?>
                    <h1 class="hero-image__title">
                        <?php echo esc_html( $hero_title ); ?>
                    </h1>
                <?php endif; ?>

                <?php if ( $hero_sub ) : ?>
                    <p class="hero-image__subtitle">
                        <?php echo esc_html( $hero_sub ); ?>
                    </p>
                <?php endif; ?>

                <?php if ( $hero_btn ) : ?>
                    <div class="hero-image__buttons">
                        <a class="btn btn--primary btn--light" href="#">
                            <?php echo esc_html( $hero_btn ); ?>
                        </a>
                    </div>
                <?php endif; ?>

            </div>
        </div>

    </section>

<?php endif; // cm_show_hero ?>

<?php while ( have_posts() ) : the_post(); ?>

    <div class="container">
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

            <header class="entry-header">
                <h1 class="entry-title"><?php the_title(); ?></h1>
            </header>

            <div class="entry-content">
                <?php
                the_content();
                wp_link_pages(['before' => '<nav class="page-links">', 'after' => '</nav>']);
                ?>
            </div>

        </article>
    </div>

<?php endwhile; ?>

<?php get_footer(); ?>
