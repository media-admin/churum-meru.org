<?php
/**
 * Shortcode: [veranstaltungen_abfrage]
 *
 * Zeigt Veranstaltungen (CPT: event) an.
 * Einträge sind NICHT klickbar – reine Darstellung.
 * Alle Taxonomie-Filter sind optional und kombinierbar.
 *
 * Parameter:
 *   category    – Kommagetrennte Slugs: event_category
 *                 Beispiel: category="kurs,retreat"
 *   location    – Kommagetrennte Slugs: event_location
 *                 Beispiel: location="wien,online"
 *   relation    – Verknüpfung mehrerer Taxonomien: AND oder OR (Standard: AND)
 *   number      – Anzahl der Einträge (Standard: -1 = alle)
 *   columns     – Spalten im Grid: 1, 2, 3 oder 4 (Standard: 1)
 *   show_image  – Thumbnail anzeigen: true/false (Standard: true)
 *   show_date   – Datum anzeigen: true/false (Standard: true)
 *   orderby     – menu_order, date, title, rand (Standard: menu_order)
 *   order       – ASC oder DESC (Standard: ASC)
 *
 * Verwendung:
 *   Alle Veranstaltungen:
 *     [veranstaltungen_abfrage]
 *
 *   Nur Kurse in Wien:
 *     [veranstaltungen_abfrage category="kurs" location="wien"]
 *
 *   Kurse oder Retreats (OR):
 *     [veranstaltungen_abfrage category="kurs,retreat" relation="OR"]
 *
 *   Online-Angebote, 2-spaltig:
 *     [veranstaltungen_abfrage location="online" columns="2"]
 *
 * @package ChuramMeru
 */

if ( ! defined( 'ABSPATH' ) ) exit;

add_shortcode( 'veranstaltungen_abfrage', 'churum_meru_veranstaltungen_abfrage_shortcode' );

function churum_meru_veranstaltungen_abfrage_shortcode( array $atts ): string {

    $atts = shortcode_atts( [
        'category'         => '',
        'location'         => '',
        'exclude_category' => '',  // Kategorien ausschließen: exclude_category="archiv"
        'exclude_location' => '',  // Orte ausschließen:      exclude_location="online"
        'relation'         => 'AND',
        'number'           => -1,
        'columns'          => 1,
        'show_image'       => 'true',
        'show_date'        => 'true',
        'orderby'          => 'menu_order',
        'order'            => 'ASC',
    ], $atts, 'veranstaltungen_abfrage' );

    // ── Tax Query aufbauen ─────────────────────────────────────────────────────

    $tax_query = [];

    $filters = [
        'event_category' => $atts['category'],
        'event_location' => $atts['location'],
    ];

    foreach ( $filters as $taxonomy => $value ) {
        if ( empty( trim( $value ) ) ) continue;

        $slugs = array_filter( array_map( 'trim', explode( ',', $value ) ) );
        if ( empty( $slugs ) ) continue;

        $tax_query[] = [
            'taxonomy' => $taxonomy,
            'field'    => 'slug',
            'terms'    => $slugs,
            'operator' => 'IN',
        ];
    }

    // Ausschließen via post__not_in (zuverlässiger als NOT IN auf derselben
    // Taxonomie – WordPress löst kombiniertes IN + NOT IN auf identischer
    // Taxonomie in tax_query nicht korrekt auf)
    $excludes = [
        'event_category' => $atts['exclude_category'],
        'event_location' => $atts['exclude_location'],
    ];

    $excluded_ids = [];

    foreach ( $excludes as $taxonomy => $value ) {
        if ( empty( trim( $value ) ) ) continue;

        $slugs = array_filter( array_map( 'trim', explode( ',', $value ) ) );
        if ( empty( $slugs ) ) continue;

        $id_query = new WP_Query( [
            'post_type'      => 'event',
            'post_status'    => 'publish',
            'fields'         => 'ids',
            'posts_per_page' => -1,
            'no_found_rows'  => true,
            'lang'           => '',   // Polylang: alle Sprachen berücksichtigen
            'tax_query'      => [ [
                'taxonomy' => $taxonomy,
                'field'    => 'slug',
                'terms'    => $slugs,
                'operator' => 'IN',
            ] ],
        ] );

        $ids = $id_query->posts; // WP_Query mit fields=ids liefert zuverlässig Integer-Array

        $excluded_ids = array_merge( $excluded_ids, $ids );
    }

    if ( count( $tax_query ) > 1 ) {
        $tax_query['relation'] = strtoupper( $atts['relation'] ) === 'OR' ? 'OR' : 'AND';
    }

    // ── WP_Query ───────────────────────────────────────────────────────────────

    $args = [
        'post_type'      => 'event',
        'post_status'    => 'publish',
        'posts_per_page' => intval( $atts['number'] ),
        'orderby'        => [ 'menu_order' => 'ASC', 'date' => 'DESC' ],
        'no_found_rows'  => true,
    ];

    if ( ! empty( $tax_query ) ) {
        $args['tax_query'] = $tax_query;
    }

    if ( ! empty( $excluded_ids ) ) {
        $args['post__not_in'] = array_unique( $excluded_ids );
    }

    $query = new WP_Query( $args );

    if ( ! $query->have_posts() ) {
        return '<p class="cm-posts-grid__empty">' . esc_html__( 'Keine Veranstaltungen gefunden.', 'churum-meru' ) . '</p>';
    }

    // ── Ausgabe ────────────────────────────────────────────────────────────────

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