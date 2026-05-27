<?php
/**
 * Taxonomien – Churum Meru
 *
 * Alle Taxonomien zentral hier registriert.
 * Keine Taxonomy-Registrierungen in custom-post-types.php – vermeidet Duplikate.
 *
 * @package ChuramMeru
 */

if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'init', 'churum_meru_register_taxonomies' );

function churum_meru_register_taxonomies(): void {

    // =========================================================================
    // Künstler·innen – Herkunftsregion (hierarchisch)
    // =========================================================================

    register_taxonomy( 'artist_region', 'artist', [
        'labels' => [
            'name'              => __( 'Regionen', 'churum-meru' ),
            'singular_name'     => __( 'Region', 'churum-meru' ),
            'search_items'      => __( 'Regionen durchsuchen', 'churum-meru' ),
            'all_items'         => __( 'Alle Regionen', 'churum-meru' ),
            'parent_item'       => __( 'Übergeordnete Region', 'churum-meru' ),
            'parent_item_colon' => __( 'Übergeordnete Region:', 'churum-meru' ),
            'edit_item'         => __( 'Region bearbeiten', 'churum-meru' ),
            'update_item'       => __( 'Region aktualisieren', 'churum-meru' ),
            'add_new_item'      => __( 'Neue Region hinzufügen', 'churum-meru' ),
            'new_item_name'     => __( 'Neuer Regionsname', 'churum-meru' ),
            'menu_name'         => __( 'Regionen', 'churum-meru' ),
        ],
        'hierarchical'      => true,  // Österreich / Lateinamerika > Länder
        'public'            => true,
        'show_ui'           => true,
        'show_in_rest'      => true,
        'show_admin_column' => true,
        'rewrite'           => [ 'slug' => 'region' ],
    ] );

    // =========================================================================
    // Künstler·innen – Kunstform (flach)
    // =========================================================================

    register_taxonomy( 'artist_artform', 'artist', [
        'labels' => [
            'name'          => __( 'Kunstformen', 'churum-meru' ),
            'singular_name' => __( 'Kunstform', 'churum-meru' ),
            'search_items'  => __( 'Kunstformen durchsuchen', 'churum-meru' ),
            'all_items'     => __( 'Alle Kunstformen', 'churum-meru' ),
            'edit_item'     => __( 'Kunstform bearbeiten', 'churum-meru' ),
            'update_item'   => __( 'Kunstform aktualisieren', 'churum-meru' ),
            'add_new_item'  => __( 'Neue Kunstform hinzufügen', 'churum-meru' ),
            'new_item_name' => __( 'Neuer Kunstform-Name', 'churum-meru' ),
            'menu_name'     => __( 'Kunstformen', 'churum-meru' ),
        ],
        'hierarchical'      => false,  // flach: Fotografie, Malerei, Volkstanz, …
        'public'            => true,
        'show_ui'           => true,
        'show_in_rest'      => true,
        'show_admin_column' => true,
        'rewrite'           => [ 'slug' => 'kunstform' ],
    ] );

    // =========================================================================
    // Künstler·innen – Zugehörigkeit / Kontext (flach)
    // Archiv, Weihnachten, Ausstellungen, …
    // Erweiterbar: falls Ausstellungen eigene Seiten brauchen → CPT migration
    // =========================================================================

    register_taxonomy( 'artist_context', 'artist', [
        'labels' => [
            'name'          => __( 'Zugehörigkeit', 'churum-meru' ),
            'singular_name' => __( 'Zugehörigkeit', 'churum-meru' ),
            'search_items'  => __( 'Zugehörigkeit durchsuchen', 'churum-meru' ),
            'all_items'     => __( 'Alle Zugehörigkeiten', 'churum-meru' ),
            'edit_item'     => __( 'Zugehörigkeit bearbeiten', 'churum-meru' ),
            'update_item'   => __( 'Zugehörigkeit aktualisieren', 'churum-meru' ),
            'add_new_item'  => __( 'Neue Zugehörigkeit hinzufügen', 'churum-meru' ),
            'new_item_name' => __( 'Neuer Name', 'churum-meru' ),
            'menu_name'     => __( 'Zugehörigkeit', 'churum-meru' ),
        ],
        'hierarchical'      => false,
        'public'            => true,
        'show_ui'           => true,
        'show_in_rest'      => true,
        'show_admin_column' => true,
        'rewrite'           => [ 'slug' => 'zugehoerigkeit' ],
    ] );

    // =========================================================================
    // Mitglieder-Kategorie (z.B. Leitung, Trainer, Vorstand, Ehrenamtlich, …)
    // =========================================================================

    register_taxonomy( 'team_category', 'team', [
        'labels' => [
            'name'              => __( 'Mitglieder-Kategorien', 'churum-meru' ),
            'singular_name'     => __( 'Mitglieder-Kategorie', 'churum-meru' ),
            'search_items'      => __( 'Kategorien durchsuchen', 'churum-meru' ),
            'all_items'         => __( 'Alle Kategorien', 'churum-meru' ),
            'parent_item'       => __( 'Übergeordnete Kategorie', 'churum-meru' ),
            'parent_item_colon' => __( 'Übergeordnete Kategorie:', 'churum-meru' ),
            'edit_item'         => __( 'Kategorie bearbeiten', 'churum-meru' ),
            'update_item'       => __( 'Kategorie aktualisieren', 'churum-meru' ),
            'add_new_item'      => __( 'Neue Kategorie hinzufügen', 'churum-meru' ),
            'new_item_name'     => __( 'Neuer Kategorie-Name', 'churum-meru' ),
            'menu_name'         => __( 'Mitglieder-Kategorien', 'churum-meru' ),
        ],
        'hierarchical'      => true,
        'public'            => true,
        'show_ui'           => true,
        'show_in_rest'      => true,
        'show_admin_column' => true,
        'rewrite'           => [ 'slug' => 'team-kategorie' ],
    ] );

    // =========================================================================
    // FAQ Kategorie
    // =========================================================================

    register_taxonomy( 'faq_category', 'faq', [
        'labels' => [
            'name'              => __( 'FAQ Kategorien', 'churum-meru' ),
            'singular_name'     => __( 'FAQ Kategorie', 'churum-meru' ),
            'search_items'      => __( 'FAQ Kategorien durchsuchen', 'churum-meru' ),
            'all_items'         => __( 'Alle FAQ Kategorien', 'churum-meru' ),
            'parent_item'       => __( 'Übergeordnete Kategorie', 'churum-meru' ),
            'parent_item_colon' => __( 'Übergeordnete Kategorie:', 'churum-meru' ),
            'edit_item'         => __( 'Kategorie bearbeiten', 'churum-meru' ),
            'update_item'       => __( 'Kategorie aktualisieren', 'churum-meru' ),
            'add_new_item'      => __( 'Neue Kategorie hinzufügen', 'churum-meru' ),
            'new_item_name'     => __( 'Neuer Kategorie-Name', 'churum-meru' ),
            'menu_name'         => __( 'FAQ Kategorien', 'churum-meru' ),
        ],
        'hierarchical'       => true,
        'public'             => false,
        'show_ui'            => true,
        'show_in_rest'       => true,
        'show_admin_column'  => true,
        'show_in_nav_menus'  => false,
        'rewrite'            => false,
    ] );

    // =========================================================================
    // Karussell Kategorie
    // =========================================================================

    register_taxonomy( 'carousel_category', 'carousel', [
        'labels' => [
            'name'          => __( 'Karussell Kategorien', 'churum-meru' ),
            'singular_name' => __( 'Karussell Kategorie', 'churum-meru' ),
            'all_items'     => __( 'Alle Kategorien', 'churum-meru' ),
            'edit_item'     => __( 'Kategorie bearbeiten', 'churum-meru' ),
            'update_item'   => __( 'Kategorie aktualisieren', 'churum-meru' ),
            'add_new_item'  => __( 'Neue Kategorie hinzufügen', 'churum-meru' ),
            'menu_name'     => __( 'Kategorien', 'churum-meru' ),
        ],
        'hierarchical'      => true,
        'public'            => false,
        'show_ui'           => true,
        'show_in_rest'      => true,
        'show_admin_column' => true,
        'show_in_nav_menus' => false,
        'rewrite'           => false,
    ] );

    // =========================================================================
    // Sponsor Kategorie (z.B. Gold, Silber, Bronze – oder Hauptsponsor, Partner)
    // =========================================================================

    register_taxonomy( 'sponsor_category', 'sponsor', [
        'labels' => [
            'name'          => __( 'Sponsor-Kategorien', 'churum-meru' ),
            'singular_name' => __( 'Sponsor-Kategorie', 'churum-meru' ),
            'all_items'     => __( 'Alle Kategorien', 'churum-meru' ),
            'edit_item'     => __( 'Kategorie bearbeiten', 'churum-meru' ),
            'update_item'   => __( 'Kategorie aktualisieren', 'churum-meru' ),
            'add_new_item'  => __( 'Neue Kategorie hinzufügen', 'churum-meru' ),
            'menu_name'     => __( 'Kategorien', 'churum-meru' ),
        ],
        'hierarchical'      => true,
        'public'            => false,
        'show_ui'           => true,
        'show_in_rest'      => true,
        'show_admin_column' => true,
        'show_in_nav_menus' => false,
        'rewrite'           => false,
    ] );

    // =========================================================================
    // Unterstützer Kategorie (z.B. Privatperson, Organisation, Institution)
    // =========================================================================

    register_taxonomy( 'supporter_category', 'supporter', [
        'labels' => [
            'name'              => __( 'Unterstützer-Kategorien', 'churum-meru' ),
            'singular_name'     => __( 'Unterstützer-Kategorie', 'churum-meru' ),
            'search_items'      => __( 'Kategorien durchsuchen', 'churum-meru' ),
            'all_items'         => __( 'Alle Kategorien', 'churum-meru' ),
            'parent_item'       => __( 'Übergeordnete Kategorie', 'churum-meru' ),
            'parent_item_colon' => __( 'Übergeordnete Kategorie:', 'churum-meru' ),
            'edit_item'         => __( 'Kategorie bearbeiten', 'churum-meru' ),
            'update_item'       => __( 'Kategorie aktualisieren', 'churum-meru' ),
            'add_new_item'      => __( 'Neue Kategorie hinzufügen', 'churum-meru' ),
            'new_item_name'     => __( 'Neuer Kategorie-Name', 'churum-meru' ),
            'menu_name'         => __( 'Kategorien', 'churum-meru' ),
        ],
        'hierarchical'      => true,
        'public'            => true,
        'show_ui'           => true,
        'show_in_rest'      => true,
        'show_admin_column' => true,
        'rewrite'           => [ 'slug' => 'unterstuetzer-kategorie' ],
    ] );

    // =========================================================================
    // Veranstaltungs-Kategorie (Kurs, Retreat, Workshop, Ausbildung, …)
    // =========================================================================

    register_taxonomy( 'event_category', 'event', [
        'labels' => [
            'name'              => __( 'Veranstaltungsarten', 'churum-meru' ),
            'singular_name'     => __( 'Veranstaltungsart', 'churum-meru' ),
            'search_items'      => __( 'Veranstaltungsarten durchsuchen', 'churum-meru' ),
            'all_items'         => __( 'Alle Veranstaltungsarten', 'churum-meru' ),
            'parent_item'       => __( 'Übergeordnete Art', 'churum-meru' ),
            'parent_item_colon' => __( 'Übergeordnete Art:', 'churum-meru' ),
            'edit_item'         => __( 'Art bearbeiten', 'churum-meru' ),
            'update_item'       => __( 'Art aktualisieren', 'churum-meru' ),
            'add_new_item'      => __( 'Neue Art hinzufügen', 'churum-meru' ),
            'new_item_name'     => __( 'Neuer Art-Name', 'churum-meru' ),
            'menu_name'         => __( 'Veranstaltungsarten', 'churum-meru' ),
        ],
        'hierarchical'      => true,
        'public'            => true,
        'show_ui'           => true,
        'show_in_rest'      => true,
        'show_admin_column' => true,
        'rewrite'           => [ 'slug' => 'veranstaltungsart' ],
    ] );

    // =========================================================================
    // Veranstaltungs-Ort (Wien, Online, Retreat-Zentrum, …)
    // =========================================================================

    register_taxonomy( 'event_location', 'event', [
        'labels' => [
            'name'          => __( 'Veranstaltungsorte', 'churum-meru' ),
            'singular_name' => __( 'Veranstaltungsort', 'churum-meru' ),
            'search_items'  => __( 'Orte durchsuchen', 'churum-meru' ),
            'all_items'     => __( 'Alle Orte', 'churum-meru' ),
            'edit_item'     => __( 'Ort bearbeiten', 'churum-meru' ),
            'update_item'   => __( 'Ort aktualisieren', 'churum-meru' ),
            'add_new_item'  => __( 'Neuen Ort hinzufügen', 'churum-meru' ),
            'new_item_name' => __( 'Neuer Ort-Name', 'churum-meru' ),
            'menu_name'     => __( 'Veranstaltungsorte', 'churum-meru' ),
        ],
        'hierarchical'      => false, // Tag-artig: flexibler für Ortsangaben
        'public'            => true,
        'show_ui'           => true,
        'show_in_rest'      => true,
        'show_admin_column' => true,
        'rewrite'           => [ 'slug' => 'veranstaltungsort' ],
    ] );
}
