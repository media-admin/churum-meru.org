<?php
/**
 * Block-Render: Inhaltsverzeichnis
 *
 * Felder:
 *   toc_title        – Titel (string)
 *   toc_mode         – inline | sticky
 *   toc_depth        – 2 | 3 | 4
 *   toc_min_headings – Mindestanzahl Überschriften (int)
 *
 * @package MediaLabAgencyCore
 * @since   1.10.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;
if ( ! function_exists( 'medialab_toc_render' ) ) return;

$args = array(
    'title'        => get_field( 'toc_title' ) ?? __( 'Inhaltsverzeichnis', 'media-lab-core' ),
    'mode'         => get_field( 'toc_mode' )  ?: 'inline',
    'depth'        => (int) ( get_field( 'toc_depth' ) ?: 3 ),
    'min_headings' => (int) ( get_field( 'toc_min_headings' ) ?: 2 ),
);

$toc_html = medialab_toc_render( $args );

if ( empty( $toc_html ) ) {
    // Im Editor: Platzhalter anzeigen
    if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
        echo '<div class="toc toc--placeholder"><p style="opacity:.5;font-size:.85rem;text-align:center;padding:1.5rem 0;">'
           . esc_html__( 'Inhaltsverzeichnis – wird automatisch aus den Überschriften des Beitrags generiert.', 'media-lab-core' )
           . '</p></div>';
    }
    return;
}

$wrapper_attributes = get_block_wrapper_attributes( array(
    'class' => 'medialab-toc-block',
) );

echo '<div ' . $wrapper_attributes . '>';
echo $toc_html; // phpcs:ignore WordPress.Security.EscapeOutput
echo '</div>';
