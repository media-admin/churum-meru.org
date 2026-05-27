<?php
/**
 * Page Template – Churum Meru Theme
 * Datei: page.php
 *
 * Hero wird gerendert sobald hero_image_show = true,
 * unabhängig davon ob ein Bild gesetzt ist.
 * Ohne Bild: Primary-Color-Hintergrund + Titel.
 */

get_header();

// ── Hero Image ────────────────────────────────────────────────────────────────

if ( function_exists('get_field') && get_field('hero_image_show') ) :

    // Seiteneigenes Bild
    $img_desktop = get_field('image_desktop');

    // Fallback: globales Fallback-Bild aus Agency Core → Hero Image Einstellungen
    if ( ! $img_desktop ) {
        $img_desktop = get_field( 'hero_fallback_desktop', 'option' );
    }

    // Fallback 2: Featured Image der Seite
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

    $img_mobile   = get_field('image_mobile')
                 ?: get_field('hero_fallback_mobile', 'option');

    $hero_title   = get_field('hero_image_title')   ?: get_the_title();
    $hero_sub     = get_field('hero_image_subtitle') ?: '';
    $hero_btn     = get_field('hero_btn1_text')      ?: '';

    // Seiteneigene Einstellungen, Fallback auf globale Agency Core Defaults
    $hero_align   = get_field('hero_image_align')
                 ?: get_field('hero_default_align',  'option')
                 ?: 'center';
    $hero_height  = get_field('hero_image_height')
                 ?: get_field('hero_default_height', 'option')
                 ?: 'md';
    $hero_vpos    = get_field('hero_image_vpos')     ?: 'bottom';

    // Overlay: Seitenwert → globaler Wert (in %, daher /100) → 0
    $page_opacity   = get_field('hero_image_opacity');
    $global_opacity = get_field('overlay_opacity', 'option');
    $hero_opacity   = $page_opacity ?? ( $global_opacity !== null ? $global_opacity / 100 : 0 );

    // Klassen
    $is_numeric_height = is_numeric( $hero_height );
    $height_class      = $is_numeric_height ? '' : 'hero-image--' . sanitize_html_class( $hero_height );
    $height_style      = $is_numeric_height ? ' style="height:' . intval($hero_height) . 'px"' : '';
    $no_image_class    = ( ! $img_desktop ) ? ' hero-image--no-bg' : '';

    $hero_classes = trim( implode( ' ', array_filter([
        'hero-image',
        $height_class,
        'hero-image--align-' . sanitize_html_class( $hero_align ),
        'hero-image--vpos-'  . sanitize_html_class( $hero_vpos ),
    ]) ) ) . $no_image_class;

    // Bild-Daten Desktop
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

    // Bild-Daten Mobile
    $m_url = '';
    if ( $img_mobile ) {
        $m_url = is_array($img_mobile)
            ? ( $img_mobile['url'] ?? '' )
            : wp_get_attachment_url( intval($img_mobile) );
    }
    ?>

    <section class="<?php echo esc_attr( $hero_classes ); ?>"<?php echo $height_style; ?>
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
                 style="--hero-opacity: <?php echo esc_attr( floatval($hero_opacity) ); ?>"
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

<?php endif; // hero_image_show ?>

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