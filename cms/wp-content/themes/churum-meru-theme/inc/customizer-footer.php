<?php
/**
 * Customizer – Footer-Logo
 *
 * Registriert ein eigenes Logo-Feld für den Footer,
 * unabhängig vom globalen Header-Logo.
 *
 * Einbinden in functions.php:
 *   require_once get_template_directory() . '/inc/customizer-footer.php';
 *
 * @package ChuramMeru
 */

if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'customize_register', 'churummeru_customizer_footer_logo' );

function churummeru_customizer_footer_logo( WP_Customize_Manager $wp_customize ): void {

    // ── Sektion ───────────────────────────────────────────────────────────────

    $wp_customize->add_section( 'churummeru_footer', [
        'title'    => __( 'Footer', 'churum-meru-theme' ),
        'priority' => 105, // nach "Header" in der Seitenleiste
    ] );

    // ── Setting: footer_logo ──────────────────────────────────────────────────

    $wp_customize->add_setting( 'footer_logo', [
        'default'           => '',
        'sanitize_callback' => 'absint',
        'transport'         => 'postMessage',
    ] );

    // ── Control: Bild-Upload (identisch mit dem globalen Logo-Control) ────────

    $wp_customize->add_control(
        new WP_Customize_Media_Control( $wp_customize, 'footer_logo', [
            'label'         => __( 'Footer-Logo', 'churum-meru-theme' ),
            'description'   => __( 'Wird im Footer angezeigt. Empfohlen: weiße Version auf transparentem Hintergrund.', 'churum-meru-theme' ),
            'section'       => 'churummeru_footer',
            'mime_type'     => 'image',
            'priority'      => 10,
        ] )
    );
}
