<?php
/**
 * Custom Post Types – Churum Meru
 *
 * Registriert alle projektspezifischen CPTs.
 * Taxonomien werden ausschließlich in taxonomies.php registriert.
 *
 * CPTs:
 *   - team           Teammitglieder
 *   - testimonial    Erfahrungsberichte
 *   - faq            Häufige Fragen
 *   - gmap           Google Maps Standorte
 *   - hero_slide     Hero Slider Slides
 *   - carousel       Karussell-Elemente
 *   - sponsor        Sponsoren (ersetzt generisches Logo-CPT)
 *   - supporter      Unterstützer
 *   - event          Veranstaltungen
 *
 * @package ChuramMeru
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// =============================================================================
// Künstler·innen
// =============================================================================

add_action( 'init', 'churum_meru_register_artist_cpt' );

function churum_meru_register_artist_cpt(): void {
    register_post_type( 'artist', [
        'labels' => [
            'name'               => __( 'Künstler·innen', 'churum-meru' ),
            'singular_name'      => __( 'Künstler·in', 'churum-meru' ),
            'menu_name'          => __( 'Künstler·innen', 'churum-meru' ),
            'add_new'            => __( 'Neu hinzufügen', 'churum-meru' ),
            'add_new_item'       => __( 'Neue·r Künstler·in', 'churum-meru' ),
            'edit_item'          => __( 'Künstler·in bearbeiten', 'churum-meru' ),
            'new_item'           => __( 'Neue·r Künstler·in', 'churum-meru' ),
            'view_item'          => __( 'Künstler·in ansehen', 'churum-meru' ),
            'view_items'         => __( 'Künstler·innen ansehen', 'churum-meru' ),
            'search_items'       => __( 'Künstler·innen durchsuchen', 'churum-meru' ),
            'not_found'          => __( 'Keine Künstler·innen gefunden', 'churum-meru' ),
            'not_found_in_trash' => __( 'Keine Künstler·innen im Papierkorb', 'churum-meru' ),
            'all_items'          => __( 'Alle Künstler·innen', 'churum-meru' ),
        ],
        'public'          => true,
        'has_archive'     => true,
        'show_in_rest'    => true,
        'supports'        => [ 'title', 'editor', 'thumbnail', 'excerpt', 'revisions', 'page-attributes' ],
        'menu_icon'       => 'dashicons-art',
        'menu_position'   => 19,
        'rewrite'         => [ 'slug' => 'kuenstlerinnen' ],
        'capability_type' => 'post',
        'taxonomies'      => [ 'artist_region', 'artist_artform', 'artist_context' ],
    ] );
}

// =============================================================================
// Team
// =============================================================================

add_action( 'init', 'churum_meru_register_team_cpt' );

function churum_meru_register_team_cpt(): void {
    register_post_type( 'team', [
        'labels' => [
            'name'               => __( 'Mitglieder', 'churum-meru' ),
            'singular_name'      => __( 'Mitglied', 'churum-meru' ),
            'menu_name'          => __( 'Mitglieder', 'churum-meru' ),
            'add_new'            => __( 'Neu hinzufügen', 'churum-meru' ),
            'add_new_item'       => __( 'Neues Mitglied', 'churum-meru' ),
            'edit_item'          => __( 'Mitglied bearbeiten', 'churum-meru' ),
            'new_item'           => __( 'Neues Mitglied', 'churum-meru' ),
            'view_item'          => __( 'Mitglied ansehen', 'churum-meru' ),
            'search_items'       => __( 'Mitglieder durchsuchen', 'churum-meru' ),
            'not_found'          => __( 'Keine Mitglieder gefunden', 'churum-meru' ),
            'not_found_in_trash' => __( 'Keine Mitglieder im Papierkorb', 'churum-meru' ),
        ],
        'public'          => true,
        'has_archive'     => true,
        'show_in_rest'    => true,
        'supports'        => [ 'title', 'editor', 'thumbnail', 'excerpt', 'page-attributes' ],
        'menu_icon'       => 'dashicons-groups',
        'menu_position'   => 20,
        'rewrite'         => [ 'slug' => 'team' ],
        'capability_type' => 'post',
        'taxonomies'      => [ 'team_category' ],
    ] );
}

// =============================================================================
// Testimonials
// =============================================================================

add_action( 'init', 'churum_meru_register_testimonial_cpt' );

function churum_meru_register_testimonial_cpt(): void {
    register_post_type( 'testimonial', [
        'labels' => [
            'name'               => __( 'Erfahrungsberichte', 'churum-meru' ),
            'singular_name'      => __( 'Erfahrungsbericht', 'churum-meru' ),
            'menu_name'          => __( 'Erfahrungsberichte', 'churum-meru' ),
            'add_new'            => __( 'Neu hinzufügen', 'churum-meru' ),
            'add_new_item'       => __( 'Neuer Erfahrungsbericht', 'churum-meru' ),
            'edit_item'          => __( 'Erfahrungsbericht bearbeiten', 'churum-meru' ),
            'new_item'           => __( 'Neuer Erfahrungsbericht', 'churum-meru' ),
            'view_item'          => __( 'Erfahrungsbericht ansehen', 'churum-meru' ),
            'search_items'       => __( 'Erfahrungsberichte durchsuchen', 'churum-meru' ),
            'not_found'          => __( 'Keine Erfahrungsberichte gefunden', 'churum-meru' ),
            'not_found_in_trash' => __( 'Keine Erfahrungsberichte im Papierkorb', 'churum-meru' ),
        ],
        'public'          => true,
        'has_archive'     => false,
        'show_in_rest'    => true,
        'supports'        => [ 'title', 'editor', 'thumbnail', 'page-attributes' ],
        'menu_icon'       => 'dashicons-format-quote',
        'menu_position'   => 21,
        'rewrite'         => [ 'slug' => 'erfahrungsberichte' ],
        'capability_type' => 'post',
    ] );
}

// =============================================================================
// FAQ
// =============================================================================

add_action( 'init', 'churum_meru_register_faq_cpt' );

function churum_meru_register_faq_cpt(): void {
    register_post_type( 'faq', [
        'labels' => [
            'name'               => __( 'FAQ', 'churum-meru' ),
            'singular_name'      => __( 'Frage', 'churum-meru' ),
            'menu_name'          => __( 'FAQ', 'churum-meru' ),
            'add_new'            => __( 'Neu hinzufügen', 'churum-meru' ),
            'add_new_item'       => __( 'Neue Frage', 'churum-meru' ),
            'edit_item'          => __( 'Frage bearbeiten', 'churum-meru' ),
            'new_item'           => __( 'Neue Frage', 'churum-meru' ),
            'search_items'       => __( 'Fragen durchsuchen', 'churum-meru' ),
            'not_found'          => __( 'Keine Fragen gefunden', 'churum-meru' ),
            'not_found_in_trash' => __( 'Keine Fragen im Papierkorb', 'churum-meru' ),
        ],
        'public'              => false,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'show_in_rest'        => true,
        'show_in_admin_bar'   => true,
        'show_in_nav_menus'   => false,
        'has_archive'         => false,
        'exclude_from_search' => true,
        'publicly_queryable'  => false,
        'supports'            => [ 'title', 'editor', 'page-attributes' ],
        'menu_icon'           => 'dashicons-editor-help',
        'menu_position'       => 22,
        'rewrite'             => [ 'slug' => 'faq' ],
        'capability_type'     => 'post',
    ] );
}

// =============================================================================
// Google Maps
// =============================================================================

add_action( 'init', 'churum_meru_register_gmap_cpt' );

function churum_meru_register_gmap_cpt(): void {
    register_post_type( 'gmap', [
        'labels' => [
            'name'          => __( 'Google Maps', 'churum-meru' ),
            'singular_name' => __( 'Karte', 'churum-meru' ),
            'menu_name'     => __( 'Google Maps', 'churum-meru' ),
            'add_new'       => __( 'Neu hinzufügen', 'churum-meru' ),
            'add_new_item'  => __( 'Neue Karte', 'churum-meru' ),
            'edit_item'     => __( 'Karte bearbeiten', 'churum-meru' ),
            'all_items'     => __( 'Alle Karten', 'churum-meru' ),
            'not_found'     => __( 'Keine Karten gefunden', 'churum-meru' ),
        ],
        'public'              => false,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'show_in_rest'        => true,
        'show_in_admin_bar'   => true,
        'show_in_nav_menus'   => false,
        'has_archive'         => false,
        'exclude_from_search' => true,
        'publicly_queryable'  => false,
        'supports'            => [ 'title' ],
        'menu_icon'           => 'dashicons-location-alt',
        'menu_position'       => 23,
        'capability_type'     => 'post',
    ] );
}

// =============================================================================
// Hero Slides
// =============================================================================

add_action( 'init', 'churum_meru_register_hero_slide_cpt' );

function churum_meru_register_hero_slide_cpt(): void {
    register_post_type( 'hero_slide', [
        'labels' => [
            'name'          => __( 'Hero Slides', 'churum-meru' ),
            'singular_name' => __( 'Hero Slide', 'churum-meru' ),
            'menu_name'     => __( 'Hero Slides', 'churum-meru' ),
            'add_new'       => __( 'Neu hinzufügen', 'churum-meru' ),
            'add_new_item'  => __( 'Neue Hero Slide', 'churum-meru' ),
            'edit_item'     => __( 'Hero Slide bearbeiten', 'churum-meru' ),
            'all_items'     => __( 'Alle Hero Slides', 'churum-meru' ),
            'not_found'     => __( 'Keine Hero Slides gefunden', 'churum-meru' ),
        ],
        'public'              => false,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'show_in_rest'        => true,
        'show_in_admin_bar'   => true,
        'show_in_nav_menus'   => false,
        'has_archive'         => false,
        'exclude_from_search' => true,
        'publicly_queryable'  => false,
        'supports'            => [ 'title', 'editor', 'thumbnail' ],
        'menu_icon'           => 'dashicons-slides',
        'menu_position'       => 24,
        'capability_type'     => 'post',
    ] );
}

// =============================================================================
// Karussell
// =============================================================================

add_action( 'init', 'churum_meru_register_carousel_cpt' );

function churum_meru_register_carousel_cpt(): void {
    register_post_type( 'carousel', [
        'labels' => [
            'name'          => __( 'Karussell', 'churum-meru' ),
            'singular_name' => __( 'Karussell-Element', 'churum-meru' ),
            'menu_name'     => __( 'Karussells', 'churum-meru' ),
            'add_new'       => __( 'Neu hinzufügen', 'churum-meru' ),
            'add_new_item'  => __( 'Neues Element', 'churum-meru' ),
            'edit_item'     => __( 'Element bearbeiten', 'churum-meru' ),
            'all_items'     => __( 'Alle Elemente', 'churum-meru' ),
            'not_found'     => __( 'Keine Elemente gefunden', 'churum-meru' ),
        ],
        'public'              => false,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'show_in_rest'        => true,
        'show_in_nav_menus'   => false,
        'has_archive'         => false,
        'exclude_from_search' => true,
        'publicly_queryable'  => false,
        'supports'            => [ 'title', 'editor', 'thumbnail', 'page-attributes' ],
        'menu_icon'           => 'dashicons-images-alt2',
        'menu_position'       => 25,
        'capability_type'     => 'post',
        'taxonomies'          => [ 'carousel_category' ],
    ] );
}

// =============================================================================
// Sponsoren (ersetzt generisches Logo-CPT aus dem Framework)
// =============================================================================

add_action( 'init', 'churum_meru_register_sponsor_cpt' );

function churum_meru_register_sponsor_cpt(): void {
    register_post_type( 'sponsor', [
        'labels' => [
            'name'               => __( 'Sponsoren', 'churum-meru' ),
            'singular_name'      => __( 'Sponsor', 'churum-meru' ),
            'menu_name'          => __( 'Sponsoren', 'churum-meru' ),
            'add_new'            => __( 'Neu hinzufügen', 'churum-meru' ),
            'add_new_item'       => __( 'Neuer Sponsor', 'churum-meru' ),
            'edit_item'          => __( 'Sponsor bearbeiten', 'churum-meru' ),
            'new_item'           => __( 'Neuer Sponsor', 'churum-meru' ),
            'view_item'          => __( 'Sponsor ansehen', 'churum-meru' ),
            'search_items'       => __( 'Sponsoren durchsuchen', 'churum-meru' ),
            'not_found'          => __( 'Keine Sponsoren gefunden', 'churum-meru' ),
            'not_found_in_trash' => __( 'Keine Sponsoren im Papierkorb', 'churum-meru' ),
        ],
        'public'              => false,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'show_in_rest'        => true,
        'show_in_admin_bar'   => true,
        'show_in_nav_menus'   => false,
        'has_archive'         => false,
        'exclude_from_search' => true,
        'publicly_queryable'  => false,
        'supports'            => [ 'title', 'thumbnail', 'page-attributes' ],
        'menu_icon'           => 'dashicons-star-filled',
        'menu_position'       => 26,
        'rewrite'             => [ 'slug' => 'sponsoren' ],
        'capability_type'     => 'post',
        'taxonomies'          => [ 'sponsor_category' ],
    ] );
}

// =============================================================================
// Unterstützer
// =============================================================================

add_action( 'init', 'churum_meru_register_supporter_cpt' );

function churum_meru_register_supporter_cpt(): void {
    register_post_type( 'supporter', [
        'labels' => [
            'name'               => __( 'Unterstützer', 'churum-meru' ),
            'singular_name'      => __( 'Unterstützer', 'churum-meru' ),
            'menu_name'          => __( 'Unterstützer', 'churum-meru' ),
            'add_new'            => __( 'Neu hinzufügen', 'churum-meru' ),
            'add_new_item'       => __( 'Neuer Unterstützer', 'churum-meru' ),
            'edit_item'          => __( 'Unterstützer bearbeiten', 'churum-meru' ),
            'new_item'           => __( 'Neuer Unterstützer', 'churum-meru' ),
            'view_item'          => __( 'Unterstützer ansehen', 'churum-meru' ),
            'search_items'       => __( 'Unterstützer durchsuchen', 'churum-meru' ),
            'not_found'          => __( 'Keine Unterstützer gefunden', 'churum-meru' ),
            'not_found_in_trash' => __( 'Keine Unterstützer im Papierkorb', 'churum-meru' ),
        ],
        'public'          => true,
        'has_archive'     => true,
        'show_in_rest'    => true,
        'supports'        => [ 'title', 'editor', 'thumbnail', 'excerpt', 'page-attributes' ],
        'menu_icon'       => 'dashicons-heart',
        'menu_position'   => 27,
        'rewrite'         => [ 'slug' => 'unterstuetzer' ],
        'capability_type' => 'post',
        'taxonomies'      => [ 'supporter_category' ],
    ] );
}

// =============================================================================
// Veranstaltungen
// =============================================================================

add_action( 'init', 'churum_meru_register_event_cpt' );

function churum_meru_register_event_cpt(): void {
    register_post_type( 'event', [
        'labels' => [
            'name'               => __( 'Veranstaltungen', 'churum-meru' ),
            'singular_name'      => __( 'Veranstaltung', 'churum-meru' ),
            'menu_name'          => __( 'Veranstaltungen', 'churum-meru' ),
            'add_new'            => __( 'Neu hinzufügen', 'churum-meru' ),
            'add_new_item'       => __( 'Neue Veranstaltung', 'churum-meru' ),
            'edit_item'          => __( 'Veranstaltung bearbeiten', 'churum-meru' ),
            'new_item'           => __( 'Neue Veranstaltung', 'churum-meru' ),
            'view_item'          => __( 'Veranstaltung ansehen', 'churum-meru' ),
            'view_items'         => __( 'Veranstaltungen ansehen', 'churum-meru' ),
            'search_items'       => __( 'Veranstaltungen durchsuchen', 'churum-meru' ),
            'not_found'          => __( 'Keine Veranstaltungen gefunden', 'churum-meru' ),
            'not_found_in_trash' => __( 'Keine Veranstaltungen im Papierkorb', 'churum-meru' ),
            'all_items'          => __( 'Alle Veranstaltungen', 'churum-meru' ),
        ],
        'public'          => true,
        'has_archive'     => true,
        'show_in_rest'    => true,
        'supports'        => [ 'title', 'editor', 'thumbnail', 'excerpt', 'revisions', 'page-attributes' ],
        'menu_icon'       => 'dashicons-calendar-alt',
        'menu_position'   => 28,
        'rewrite'         => [ 'slug' => 'veranstaltungen' ],
        'capability_type' => 'post',
        'taxonomies'      => [ 'event_category', 'event_location' ],
    ] );
}

// ── Admin-Spalten: Mitglieder-Kategorie ──────────────────────────────────────

add_filter( 'manage_team_posts_columns', 'churum_meru_team_admin_columns' );

function churum_meru_team_admin_columns( array $columns ): array {
    // Nach "title" einfügen
    $new = [];
    foreach ( $columns as $key => $label ) {
        $new[ $key ] = $label;
        if ( $key === 'title' ) {
            $new['team_category'] = __( 'Kategorie', 'churum-meru' );
        }
    }
    return $new;
}

add_action( 'manage_team_posts_custom_column', 'churum_meru_team_column_content', 10, 2 );

function churum_meru_team_column_content( string $column, int $post_id ): void {
    if ( $column !== 'team_category' ) return;

    $terms = get_the_terms( $post_id, 'team_category' );
    if ( $terms && ! is_wp_error( $terms ) ) {
        echo esc_html( implode( ', ', wp_list_pluck( $terms, 'name' ) ) );
    } else {
        echo '—';
    }
}