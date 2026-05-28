<?php
/**
 * Shortcode: Mitglieder-Grid
 * Datei: inc/shortcode-mitglieder.php
 *
 * Einbinden in der Plugin-Hauptdatei oder inc/shortcodes.php:
 *   require_once plugin_dir_path( __FILE__ ) . 'inc/shortcode-mitglieder.php';
 *
 * Verwendung:
 *   [mitglieder]
 *   [mitglieder kategorie="Vorstand"]
 *   [mitglieder kategorie="Vorstand" sprache="de" spalten="4" limit="8"]
 *
 * Parameter:
 *   kategorie   – Name der team_category (leer = alle)
 *   sprache     – Polylang-Sprachcode z.B. "de" | "es" (leer = alle Sprachen)
 *   spalten     – Anzahl Spalten auf Desktop: 2 | 3 | 4  (Standard: 3)
 *   limit       – Max. Anzahl Einträge, -1 = alle          (Standard: -1)
 *   reihenfolge – ASC | DESC                              (Standard: ASC)
 *   sortierung  – menu_order | title | date               (Standard: menu_order)
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// =============================================================================
// ACF-Feldnamen – hier anpassen falls abweichend
// =============================================================================
define( 'CM_MEMBER_FIELD_ROLE',      'rolle' );
define( 'CM_MEMBER_FIELD_BIO',       'kurzbeschreibung' );
define( 'CM_MEMBER_FIELD_EMAIL',     'email' );
define( 'CM_MEMBER_FIELD_PHONE',     'telefon' );
define( 'CM_MEMBER_FIELD_INSTAGRAM', 'instagram' );
define( 'CM_MEMBER_FIELD_FACEBOOK',  'facebook' );
define( 'CM_MEMBER_FIELD_LINKEDIN',  'linkedin' );
define( 'CM_MEMBER_FIELD_WEBSITE',   'website' );


// =============================================================================
// Shortcode-Registrierung
// =============================================================================

add_shortcode( 'mitglieder', 'cm_mitglieder_grid_shortcode' );

function cm_mitglieder_grid_shortcode( $atts ) : string {

    $atts = shortcode_atts( [
        'kategorie'   => '',
        'sprache'     => '',
        'spalten'     => '3',
        'limit'       => '-1',
        'reihenfolge' => 'ASC',
        'sortierung'  => 'menu_order',
    ], $atts, 'mitglieder' );

    // Spalten absichern
    $cols = in_array( (int) $atts['spalten'], [ 1, 2, 3, 4 ], true )
        ? (int) $atts['spalten']
        : 3;

    // Query aufbauen
    $args = [
        'post_type'      => 'team',
        'post_status'    => 'publish',
        'posts_per_page' => (int) $atts['limit'],
        'orderby'        => sanitize_key( $atts['sortierung'] ),
        'order'          => strtoupper( $atts['reihenfolge'] ) === 'DESC' ? 'DESC' : 'ASC',
    ];

    // Polylang: nur filtern wenn sprache= explizit angegeben
    if ( ! empty( $atts['sprache'] ) ) {
        $args['lang'] = sanitize_key( $atts['sprache'] );
    }

    // Kategorie-Filter (nach Name, nicht Slug)
    if ( ! empty( $atts['kategorie'] ) ) {
        $args['tax_query'] = [ [
            'taxonomy' => 'team_category',
            'field'    => 'name',
            'terms'    => sanitize_text_field( $atts['kategorie'] ),
        ] ];
    }

    $query = new WP_Query( $args );

    if ( ! $query->have_posts() ) {
        return '<p class="mitglieder-grid__empty">'
            . esc_html__( 'Keine Mitglieder gefunden.', 'churum-meru' )
            . '</p>';
    }

    // Output-Puffer
    ob_start();
    ?>
    <div class="mitglieder-grid mitglieder-grid--cols-<?php echo esc_attr( $cols ); ?>">
    <?php
    while ( $query->have_posts() ) :
        $query->the_post();
        $id = get_the_ID();

        // ACF-Felder
        $rolle = function_exists('get_field') ? get_field( CM_MEMBER_FIELD_ROLE,  $id ) : '';
        $bio   = function_exists('get_field') ? get_field( CM_MEMBER_FIELD_BIO,   $id ) : '';
        $email = function_exists('get_field') ? get_field( CM_MEMBER_FIELD_EMAIL, $id ) : '';
        $phone = function_exists('get_field') ? get_field( CM_MEMBER_FIELD_PHONE, $id ) : '';

        // Fallback Bio → vollständiger Post-Inhalt (kein gekürztes Excerpt)
        if ( empty( $bio ) ) {
            $bio = wp_strip_all_tags( get_the_content() );
        }

        // Social Links
        $socials = [];
        $social_fields = [
            'instagram' => [ 'field' => CM_MEMBER_FIELD_INSTAGRAM, 'label' => 'Instagram', 'icon' => cm_social_icon('instagram') ],
            'facebook'  => [ 'field' => CM_MEMBER_FIELD_FACEBOOK,  'label' => 'Facebook',  'icon' => cm_social_icon('facebook')  ],
            'linkedin'  => [ 'field' => CM_MEMBER_FIELD_LINKEDIN,  'label' => 'LinkedIn',  'icon' => cm_social_icon('linkedin')  ],
            'website'   => [ 'field' => CM_MEMBER_FIELD_WEBSITE,   'label' => 'Website',   'icon' => cm_social_icon('website')   ],
        ];

        if ( function_exists('get_field') ) {
            foreach ( $social_fields as $key => $cfg ) {
                $url = get_field( $cfg['field'], $id );
                if ( ! empty( $url ) ) {
                    $socials[ $key ] = array_merge( $cfg, [ 'url' => $url ] );
                }
            }
        }
        ?>
        <article class="mitglieder-grid__card" id="mitglied-<?php echo esc_attr( $id ); ?>">

            <?php /* ── Foto ──────────────────────────────────── */ ?>
            <div class="mitglieder-grid__image-wrap">
                <?php if ( has_post_thumbnail() ) : ?>
                    <?php the_post_thumbnail( 'medium', [
                        'class'   => 'mitglieder-grid__image',
                        'alt'     => esc_attr( get_the_title() ),
                        'loading' => 'lazy',
                    ] ); ?>
                <?php else : ?>
                    <div class="mitglieder-grid__image mitglieder-grid__image--placeholder" aria-hidden="true">
                        <?php echo cm_person_placeholder_svg(); ?>
                    </div>
                <?php endif; ?>
            </div>

            <?php /* ── Inhalt ────────────────────────────────── */ ?>
            <div class="mitglieder-grid__body">

                <h3 class="mitglieder-grid__name"><?php the_title(); ?></h3>

                <?php if ( ! empty( $rolle ) ) : ?>
                    <p class="mitglieder-grid__role"><?php echo esc_html( $rolle ); ?></p>
                <?php endif; ?>

            </div>

            <?php /* ── Kontakt: E-Mail + Telefon + Social ──── */ ?>
            <?php /* Immer gerendert (auch leer) – Subgrid-Track 3 muss existieren */ ?>
            <div class="mitglieder-grid__contact">

                <?php if ( ! empty( $email ) ) : ?>
                    <a class="mitglieder-grid__email"
                       href="<?php echo esc_url( 'mailto:' . antispambot( $email ) ); ?>"
                       aria-label="<?php echo esc_attr( sprintf( __( 'E-Mail an %s', 'churum-meru' ), get_the_title() ) ); ?>">
                        <?php echo cm_social_icon('email'); ?>
                        <span><?php echo esc_html( antispambot( $email ) ); ?></span>
                    </a>
                <?php endif; ?>

                <?php if ( ! empty( $phone ) ) : ?>
                    <a class="mitglieder-grid__phone"
                       href="<?php echo esc_url( 'tel:' . preg_replace( '/[^+\d]/', '', $phone ) ); ?>"
                       aria-label="<?php echo esc_attr( sprintf( __( 'Anrufen: %s', 'churum-meru' ), get_the_title() ) ); ?>">
                        <?php echo cm_social_icon('phone'); ?>
                        <span><?php echo esc_html( $phone ); ?></span>
                    </a>
                <?php endif; ?>

                <?php if ( ! empty( $socials ) ) : ?>
                    <nav class="mitglieder-grid__socials" aria-label="<?php esc_attr_e( 'Social Media', 'churum-meru' ); ?>">
                        <?php foreach ( $socials as $social ) : ?>
                            <a href="<?php echo esc_url( $social['url'] ); ?>"
                               class="mitglieder-grid__social-link mitglieder-grid__social-link--<?php echo esc_attr( $social['label'] ); ?>"
                               target="_blank"
                               rel="noopener noreferrer"
                               aria-label="<?php echo esc_attr( $social['label'] . ' – ' . get_the_title() ); ?>">
                                <?php echo $social['icon']; ?>
                            </a>
                        <?php endforeach; ?>
                    </nav>
                <?php endif; ?>

            </div>

            <?php /* ── Footer: Bio ───────────────────────────── */ ?>
            <?php if ( ! empty( $bio ) ) : ?>
            <footer class="mitglieder-grid__footer">
                <p class="mitglieder-grid__bio"><?php echo esc_html( $bio ); ?></p>
            </footer>
            <?php endif; ?>
        </article>
    <?php
    endwhile;
    wp_reset_postdata();
    ?>
    </div>
    <?php

    return ob_get_clean();
}


// =============================================================================
// SVG-Icons (inline, keine externe Abhängigkeit)
// =============================================================================

function cm_social_icon( string $platform ) : string {
    $icons = [
        'instagram' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/></svg>',
        'facebook'  => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" fill="currentColor" aria-hidden="true"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>',
        'linkedin'  => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"/><rect x="2" y="9" width="4" height="12"/><circle cx="4" cy="4" r="2"/></svg>',
        'website'   => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>',
        'email'     => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>',
        'phone'     => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.07 12a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3 1.17h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.09 8.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>',
    ];

    return $icons[ $platform ] ?? '';
}


// =============================================================================
// Placeholder SVG (wenn kein Beitragsbild vorhanden)
// =============================================================================

function cm_person_placeholder_svg() : string {
    return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200" aria-hidden="true">
        <rect width="200" height="200" fill="var(--color-surface, #f0f0f0)"/>
        <circle cx="100" cy="80" r="35" fill="var(--color-text-muted, #bbb)"/>
        <path d="M40 180 Q40 130 100 130 Q160 130 160 180Z" fill="var(--color-text-muted, #bbb)"/>
    </svg>';
}