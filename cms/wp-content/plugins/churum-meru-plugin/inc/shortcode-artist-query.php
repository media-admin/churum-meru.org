<?php
/**
 * Shortcode: [kuenstler_abfrage]
 *
 * Zeigt Künstler·innen (CPT: artist) an.
 * Beiträge sind NICHT klickbar – reine Darstellung.
 * Alle Taxonomie-Filter sind optional und kombinierbar.
 *
 * Parameter:
 *   context     – Kommagetrennte Slugs: artist_context
 *                 Beispiel: context="weihnachten,ausstellungen"
 *   region      – Kommagetrennte Slugs: artist_region
 *                 Beispiel: region="oesterreich"
 *   artform     – Kommagetrennte Slugs: artist_artform
 *                 Beispiel: artform="fotografie,malerei"
 *   relation    – Verknüpfung mehrerer Taxonomien: AND oder OR (Standard: AND)
 *   number      – Anzahl der Einträge (Standard: -1 = alle)
 *   columns     – Spalten im Grid: 1, 2, 3 oder 4 (Standard: 1)
 *   show_image  – Thumbnail anzeigen: true/false (Standard: true)
 *   show_date   – Datum anzeigen: true/false (Standard: false)
 *   orderby     – menu_order, title, date, rand (Standard: menu_order)
 *   order       – ASC oder DESC (Standard: ASC)
 *
 * Verwendung:
 *   Alle Künstler·innen:
 *     [kuenstler_abfrage]
 *
 *   Nur Weihnachts-Kontext:
 *     [kuenstler_abfrage context="weihnachten"]
 *
 *   Österreichische Fotograf·innen:
 *     [kuenstler_abfrage region="oesterreich" artform="fotografie"]
 *
 *   Archiv ODER Ausstellungen, aus Lateinamerika:
 *     [kuenstler_abfrage context="archiv,ausstellungen" region="lateinamerika" relation="AND"]
 *
 * @package ChuramMeru
 */

if ( ! defined( 'ABSPATH' ) ) exit;

add_shortcode( 'kuenstler_abfrage', 'churum_meru_kuenstler_abfrage_shortcode' );

function churum_meru_kuenstler_abfrage_shortcode( array $atts ): string {

    $atts = shortcode_atts( [
        'context'    => '',
        'region'     => '',
        'artform'    => '',
        'relation'   => 'AND',
        'number'     => -1,
        'columns'    => 1,
        'show_image' => 'true',
        'show_date'  => 'false',
        'orderby'    => 'menu_order',
        'order'      => 'ASC',
    ], $atts, 'kuenstler_abfrage' );

    // ── Tax Query aufbauen ─────────────────────────────────────────────────────

    $tax_query = [];

    $filters = [
        'artist_context' => $atts['context'],
        'artist_region'  => $atts['region'],
        'artist_artform' => $atts['artform'],
    ];

    foreach ( $filters as $taxonomy => $value ) {
        if ( empty( trim( $value ) ) ) continue;

        $slugs = array_filter( array_map( 'trim', explode( ',', $value ) ) );
        if ( empty( $slugs ) ) continue;

        $tax_query[] = [
            'taxonomy' => $taxonomy,
            'field'    => 'slug',
            'terms'    => $slugs,
            'operator' => count( $slugs ) > 1 ? 'IN' : 'IN',
        ];
    }

    // Verknüpfung mehrerer Taxonomie-Filter
    if ( count( $tax_query ) > 1 ) {
        $tax_query['relation'] = strtoupper( $atts['relation'] ) === 'OR' ? 'OR' : 'AND';
    }

    // ── WP_Query ───────────────────────────────────────────────────────────────

    $args = [
        'post_type'      => 'artist',
        'post_status'    => 'publish',
        'posts_per_page' => intval( $atts['number'] ),
        'orderby'        => [ 'menu_order' => 'ASC', 'title' => 'ASC' ],
        'no_found_rows'  => true,
    ];

    if ( ! empty( $tax_query ) ) {
        $args['tax_query'] = $tax_query;
    }

    $query = new WP_Query( $args );

    if ( ! $query->have_posts() ) {
        return '<p class="cm-posts-grid__empty">' . esc_html__( 'Keine Künstler·innen gefunden.', 'churum-meru' ) . '</p>';
    }

    // ── Ausgabe ────────────────────────────────────────────────────────────────

    $cols       = max( 1, min( 4, intval( $atts['columns'] ) ) );
    $show_image = $atts['show_image'] !== 'false';
    $show_date  = $atts['show_date']  === 'true';

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
                    <?php echo apply_filters( 'the_content', get_the_content() ); ?>
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
