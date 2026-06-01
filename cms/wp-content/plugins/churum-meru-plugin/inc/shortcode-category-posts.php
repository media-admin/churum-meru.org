<?php
/**
 * Shortcode: [beitraege_kategorie]
 *
 * Zeigt Beiträge einer oder mehrerer Kategorien an.
 * Beiträge sind NICHT klickbar – reine Darstellung.
 *
 * Parameter:
 *   categories  – Kommagetrennte Kategorie-Slugs (Pflicht)
 *                 Beispiel: categories="weihnachten,archiv"
 *   number      – Anzahl der Beiträge (Standard: 6, -1 = alle)
 *   columns     – Spalten im Grid: 1, 2, 3 oder 4 (Standard: 3)
 *   show_image  – Thumbnail anzeigen: true/false (Standard: true)
 *   show_date   – Datum anzeigen: true/false (Standard: true)
 *   show_excerpt– Teaser anzeigen: true/false (Standard: true)
 *   excerpt_length – Wörter im Teaser (Standard: 20)
 *   orderby     – Sortierung: date, title, rand (Standard: date)
 *   order       – ASC oder DESC (Standard: DESC)
 *
 * Verwendung:
 *   [beitraege_kategorie categories="weihnachten" number="6" columns="3"]
 *   [beitraege_kategorie categories="weihnachten,archiv" number="-1" columns="2" show_date="false"]
 *
 * @package ChuramMeru
 */

if ( ! defined( 'ABSPATH' ) ) exit;

add_shortcode( 'beitraege_kategorie', 'churum_meru_beitraege_kategorie_shortcode' );

function churum_meru_beitraege_kategorie_shortcode( array $atts ): string {

    $atts = shortcode_atts( [
        'categories'  => '',
        'number'      => 6,
        'columns'     => 3,
        'show_image'  => 'true',
        'show_date'   => 'true',
        'orderby'     => 'menu_order', // entspricht der manuellen Reihenfolge im Backend
        'order'       => 'ASC',
    ], $atts, 'beitraege_kategorie' );

    if ( empty( $atts['categories'] ) ) {
        return '<!-- [beitraege_kategorie]: Kein "categories"-Parameter angegeben -->';
    }

    // Kategorie-Slugs in Array umwandeln
    $slugs = array_map( 'trim', explode( ',', $atts['categories'] ) );

    $args = [
        'post_type'      => 'post',
        'post_status'    => 'publish',
        'posts_per_page' => intval( $atts['number'] ),
        'orderby'        => [ 'menu_order' => 'ASC', 'date' => 'DESC' ], // Backend-Reihenfolge, Datum als Fallback
        'no_found_rows'  => true,
        'tax_query'      => [
            [
                'taxonomy' => 'category',
                'field'    => 'slug',
                'terms'    => $slugs,
                'operator' => 'IN',
            ],
        ],
    ];

    $query = new WP_Query( $args );

    if ( ! $query->have_posts() ) {
        return '<p class="cm-posts-grid__empty">' . esc_html__( 'Keine Beiträge gefunden.', 'churum-meru' ) . '</p>';
    }

    $cols       = max( 1, min( 4, intval( $atts['columns'] ) ) );
    $show_image = $atts['show_image'] !== 'false';
    $show_date  = $atts['show_date']  !== 'false';

    ob_start();
    ?>
    <div class="cm-posts-grid cm-posts-grid--cols-<?php echo esc_attr( $cols ); ?>">
        <?php while ( $query->have_posts() ) : $query->the_post(); ?>
        <article class="cm-posts-grid__item" aria-label="<?php the_title_attribute(); ?>">

            <?php if ( $show_image && has_post_thumbnail() ) : ?>
            <div class="cm-posts-grid__image" aria-hidden="true">
                <?php the_post_thumbnail( 'medium_large', [ 'loading' => 'lazy' ] ); ?>
            </div>
            <?php endif; ?>

            <div class="cm-posts-grid__body">

                <?php if ( $show_date ) : ?>
                <time class="cm-posts-grid__date" datetime="<?php echo esc_attr( get_the_date( 'Y-m-d' ) ); ?>">
                    <?php echo esc_html( get_the_date() ); ?>
                </time>
                <?php endif; ?>

                <h3 class="cm-posts-grid__title"><?php the_title(); ?></h3>

                <div class="cm-posts-grid__content">
                    <?php
                    // Vollständigen, formatierten Content ausgeben (Gutenberg-Blöcke korrekt gerendert)
                    echo apply_filters( 'the_content', get_the_content() );
                    ?>
                </div>

            </div><!-- /.cm-posts-grid__body -->

        </article>

        <?php if ( $query->current_post < $query->post_count - 1 ) : ?>
        <div class="cm-posts-grid__separator" aria-hidden="true"></div>
        <?php endif; ?>

        <?php endwhile; wp_reset_postdata(); ?>
    </div><!-- /.cm-posts-grid -->
    <?php
    return ob_get_clean();
}
