<?php
// =============================================================================
// Footer – Mehrsprachigkeit (Polylang)
// Datei: inc/footer-multilang.php
//
// Einbinden in functions.php (ans Ende):
//   require_once get_template_directory() . '/inc/footer-multilang.php';
//
// Voraussetzung: Polylang Plugin installiert und aktiviert.
// Polylang: https://wordpress.org/plugins/polylang/
//
// Was Polylang AUTOMATISCH erledigt (kein Code nötig):
//   ✓ Nav-Menüs       → je Sprache eigenes Menü unter Design → Menüs
//   ✓ Letzte Einträge → WP_Query filtert automatisch nach aktiver Sprache
//   ✓ Seiten/Posts    → URL-Umschaltung, Sprachfilter
//
// Was DIESER FILE erledigt:
//   ✓ Widget-Area "Footer: Kontakt" → je Sprache eigene Area
//   ✓ Copyright-Text                → je Sprache übersetzbar
//   ✓ Sprachnavigation im Footer    → optionale Sprachumschalter-Funktion
// =============================================================================


// ── Widget-Areas: eine pro Sprache ───────────────────────────────────────────

add_action( 'widgets_init', function() {

    // Sprachliste aus Polylang holen (Fallback: nur 'de')
    $languages = function_exists( 'pll_languages_list' )
        ? pll_languages_list()
        : [ 'de' ];

    foreach ( $languages as $lang ) {
        register_sidebar([
            'name'          => sprintf(
                /* translators: %s: Sprachcode, z.B. "DE" */
                __( 'Footer: Kontakt (%s)', 'churum-meru-theme' ),
                strtoupper( $lang )
            ),
            'id'            => 'footer-contact-' . sanitize_key( $lang ),
            'description'   => sprintf(
                __( 'Kontaktdaten im Footer – Sprache: %s', 'churum-meru-theme' ),
                strtoupper( $lang )
            ),
            'before_widget' => '<div class="footer-contact-block %2$s">',
            'after_widget'  => '</div>',
            'before_title'  => '<h4 class="footer-contact-block__title">',
            'after_title'   => '</h4>',
        ]);
    }

} );


// ── Hilfsfunktion: richtige Widget-Area für aktive Sprache laden ──────────────

if ( ! function_exists( 'churummeru_footer_contact_sidebar' ) ) {
    /**
     * Gibt die Widget-Area "Footer: Kontakt" für die aktive Sprache aus.
     * Fällt auf die deutsche Area zurück wenn die Sprachversion leer ist.
     */
    function churummeru_footer_contact_sidebar() : void {
        // Aktive Sprache ermitteln
        $lang = 'de'; // Fallback
        if ( function_exists( 'pll_current_language' ) ) {
            $lang = pll_current_language() ?: 'de';
        }

        $sidebar_lang    = 'footer-contact-' . sanitize_key( $lang );
        $sidebar_default = 'footer-contact-de';  // Primärsprache als Fallback

        if ( is_active_sidebar( $sidebar_lang ) ) {
            dynamic_sidebar( $sidebar_lang );
        } elseif ( $lang !== 'de' && is_active_sidebar( $sidebar_default ) ) {
            // Fallback auf Deutsch wenn Übersetzung noch fehlt
            dynamic_sidebar( $sidebar_default );
        }
    }
}


// ── Hilfsfunktion: Copyright-Text mehrsprachig ────────────────────────────────

if ( ! function_exists( 'churummeru_footer_copyright' ) ) {
    /**
     * Gibt den Copyright-Text aus.
     * Priorität:
     *   1. ACF Options-Feld "footer_copyright" (falls befüllt)
     *   2. pll__() Polylang-String (registriert und übersetzt via Polylang → Übersetzungen)
     *   3. Automatischer Fallback
     */
    function churummeru_footer_copyright() : void {
        // 1. ACF Options
        $text = '';
        if ( function_exists( 'get_field' ) ) {
            $text = get_field( 'footer_copyright', 'option' );
        }

        // 2. Polylang-String (wenn kein ACF-Feld)
        if ( empty( $text ) && function_exists( 'pll__' ) ) {
            // String einmalig in Polylang registrieren
            pll_register_string(
                'footer_copyright',
                '© Verein Churúm-Merú. Alle Bilder sind urheberrechtlich geschützt und dürfen keinesfalls weiterverbreitet oder heruntergeladen werden.',
                'Churum Meru Theme'
            );
            $text = pll__( '© Verein Churúm-Merú. Alle Bilder sind urheberrechtlich geschützt und dürfen keinesfalls weiterverbreitet oder heruntergeladen werden.' );
        }

        // 3. Fallback
        if ( empty( $text ) ) {
            $text = sprintf(
                '&copy; %s %s.',
                date('Y'),
                get_bloginfo('name')
            );
        }

        echo wp_kses_post( $text );
    }
}


// ── Polylang: Sprachumschalter im Footer (optional) ───────────────────────────

if ( ! function_exists( 'churummeru_language_switcher' ) ) {
    /**
     * Gibt einen einfachen Sprachumschalter aus.
     * Verwendung in footer.php: <?php churummeru_language_switcher(); ?>
     */
    function churummeru_language_switcher() : void {
        if ( ! function_exists( 'pll_the_languages' ) ) return;
        ?>
        <nav class="footer-lang-switcher" aria-label="<?php esc_attr_e( 'Sprache wählen', 'churum-meru-theme' ); ?>">
            <?php
            pll_the_languages([
                'show_flags'       => 0,
                'show_names'       => 1,
                'display_names_as' => 'slug',   // 'de', 'es' – kompakt
                'hide_current'     => 0,
                'hide_if_no_translation' => 0,
            ]);
            ?>
        </nav>
        <?php
    }
}
