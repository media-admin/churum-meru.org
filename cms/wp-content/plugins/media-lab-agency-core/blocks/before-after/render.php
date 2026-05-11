<?php
/**
 * Block-Render: Vorher / Nachher
 *
 * ACF-Felder:
 *   ba_image_before      Image   Vorher-Bild (Pflicht)
 *   ba_image_after       Image   Nachher-Bild (Pflicht)
 *   ba_label_before      Text    Label links  (Standard: „Vorher")
 *   ba_label_after       Text    Label rechts (Standard: „Nachher")
 *   ba_start_position    Number  Startposition in % (Standard: 50)
 *   ba_aspect_ratio      Select  Seitenverhältnis (auto/16:9/4:3/1:1/3:4)
 *
 * JS: Reiner Drag-Slider ohne externe Library.
 *     Mouse + Touch-Events. prefers-reduced-motion: Slider deaktiviert.
 *
 * @package MediaLabAgencyCore
 * @since   1.11.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$img_before  = get_field( 'ba_image_before' );
$img_after   = get_field( 'ba_image_after'  );

if ( ( empty( $img_before ) || empty( $img_after ) ) && ! $is_preview ) return;

// Bild-URLs
$src_before = is_array( $img_before ) ? ( $img_before['url'] ?? '' ) : wp_get_attachment_url( (int) $img_before );
$src_after  = is_array( $img_after  ) ? ( $img_after['url']  ?? '' ) : wp_get_attachment_url( (int) $img_after  );
$alt_before = is_array( $img_before ) ? ( $img_before['alt'] ?? '' ) : '';
$alt_after  = is_array( $img_after  ) ? ( $img_after['alt']  ?? '' ) : '';
$w_before   = is_array( $img_before ) ? ( (int) ( $img_before['width']  ?? 0 ) ) : 0;
$h_before   = is_array( $img_before ) ? ( (int) ( $img_before['height'] ?? 0 ) ) : 0;

$label_before  = get_field( 'ba_label_before'   ) ?: __( 'Vorher',  'media-lab-core' );
$label_after   = get_field( 'ba_label_after'    ) ?: __( 'Nachher', 'media-lab-core' );
$start_pos     = max( 0, min( 100, (int) ( get_field( 'ba_start_position' ) ?: 50 ) ) );
$aspect_ratio  = get_field( 'ba_aspect_ratio' ) ?: 'auto';

$ratio_map = [ '16:9' => '56.25%', '4:3' => '75%', '1:1' => '100%', '3:4' => '133.33%' ];
$padding_top = $ratio_map[ $aspect_ratio ] ?? null;

$classes = array_filter( [
    'ml-block-before-after',
    ! empty( $block['className'] ) ? $block['className'] : '',
    ! empty( $block['align'] )     ? 'align' . $block['align'] : '',
] );
$block_id = ! empty( $block['anchor'] ) ? ' id="' . esc_attr( $block['anchor'] ) . '"' : '';

if ( $is_preview && ( ! $src_before || ! $src_after ) ) {
    echo '<div class="' . esc_attr( implode( ' ', $classes ) ) . '"'
       . ' style="padding:2rem;background:#f0f0f0;text-align:center;">';
    echo '<p style="color:#aaa;font-size:.875rem;">'
       . esc_html__( 'Vorher/Nachher – bitte beide Bilder im Inspector wählen.', 'media-lab-core' )
       . '</p>';
    echo '</div>';
    return;
}
?>
<div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>"<?php echo $block_id; ?>>

    <div class="ml-ba__container"
         data-start="<?php echo (int) $start_pos; ?>"
         role="group"
         aria-label="<?php echo esc_attr( sprintf(
             __( 'Bildvergleich: %1$s und %2$s', 'media-lab-core' ),
             $label_before, $label_after
         ) ); ?>">

        <?php if ( $padding_top ) : ?>
        <div class="ml-ba__ratio" style="padding-top: <?php echo esc_attr( $padding_top ); ?>;"></div>
        <?php endif; ?>

        <!-- Nachher-Bild (Basis, vollständig sichtbar) -->
        <div class="ml-ba__after">
            <?php if ( $src_after ) : ?>
            <img src="<?php echo esc_url( $src_after ); ?>"
                 alt="<?php echo esc_attr( $alt_after ); ?>"
                 class="ml-ba__img"
                 loading="lazy"
                 draggable="false">
            <?php endif; ?>
            <span class="ml-ba__label ml-ba__label--after"
                  aria-hidden="true"><?php echo esc_html( $label_after ); ?></span>
        </div>

        <!-- Vorher-Bild (geclipt, Breite = Slider-Position) -->
        <div class="ml-ba__before"
             style="width: <?php echo (int) $start_pos; ?>%;">
            <?php if ( $src_before ) : ?>
            <img src="<?php echo esc_url( $src_before ); ?>"
                 alt="<?php echo esc_attr( $alt_before ); ?>"
                 class="ml-ba__img"
                 <?php if ( $w_before && $h_before ) : ?>
                 width="<?php echo $w_before; ?>" height="<?php echo $h_before; ?>"
                 <?php endif; ?>
                 loading="eager"
                 draggable="false">
            <?php endif; ?>
            <span class="ml-ba__label ml-ba__label--before"
                  aria-hidden="true"><?php echo esc_html( $label_before ); ?></span>
        </div>

        <!-- Drag-Handle -->
        <div class="ml-ba__handle"
             style="left: <?php echo (int) $start_pos; ?>%;"
             role="slider"
             aria-label="<?php esc_attr_e( 'Vergleichs-Regler', 'media-lab-core' ); ?>"
             aria-valuemin="0"
             aria-valuemax="100"
             aria-valuenow="<?php echo (int) $start_pos; ?>"
             tabindex="0">
            <div class="ml-ba__handle-line"></div>
            <div class="ml-ba__handle-icon" aria-hidden="true">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M8 5l-5 7 5 7M16 5l5 7-5 7" stroke="currentColor" stroke-width="2"
                          fill="none" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
        </div>

    </div>

</div>
