<?php
/**
 * Block-Render: Share-Buttons
 *
 * Liest Block-spezifische ACF-Felder aus und merged sie mit den globalen
 * Defaults aus Agency Core → Share-Buttons.
 *
 * Felder:
 *   ss_block_override     – Globale Einstellungen überschreiben (bool)
 *   ss_block_services     – Aktivierte Kanäle (array, nur bei override)
 *   ss_block_layout       – Layout horizontal|vertical (string, nur bei override)
 *   ss_block_show_label   – Label anzeigen (bool, nur bei override)
 *   ss_block_label        – Label-Text (string, nur bei override)
 *
 * @package MediaLabAgencyCore
 * @since   1.9.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;
if ( ! function_exists( 'medialab_social_share_render' ) ) return;

// Globale Defaults laden
$defaults = medialab_social_share_get_defaults();

// Block-Einstellungen
$override = (bool) get_field( 'ss_block_override' );

if ( $override ) {
    $block_services = get_field( 'ss_block_services' );
    $services = ( is_array( $block_services ) && ! empty( $block_services ) )
        ? implode( ',', array_map( 'sanitize_key', $block_services ) )
        : $defaults['services'];

    $layout_raw = get_field( 'ss_block_layout' );
    $layout = in_array( $layout_raw, array( 'horizontal', 'vertical' ), true )
        ? $layout_raw
        : $defaults['layout'];

    $show_label_raw = get_field( 'ss_block_show_label' );
    $show_label = ( $show_label_raw !== null ) ? (bool) $show_label_raw : $defaults['show_label'];

    $label_raw = get_field( 'ss_block_label' );
    $label = ( $label_raw !== null && $label_raw !== '' )
        ? (string) $label_raw
        : $defaults['label'];
} else {
    $services   = $defaults['services'];
    $layout     = $defaults['layout'];
    $show_label = $defaults['show_label'];
    $label      = $defaults['label'];
}

// Block-Wrapper-Attribute (Anchor, Align, Custom CSS)
$wrapper_attributes = get_block_wrapper_attributes( array(
    'class' => 'medialab-share-block',
) );

echo '<div ' . $wrapper_attributes . '>';
echo medialab_social_share_render( compact( 'services', 'layout', 'show_label', 'label' ) );
echo '</div>';
