<?php
/**
 * Block-Render: Slider (Swiper)
 *
 * Jeder direkte Child-Block der InnerBlocks wird zu einer Swiper-Folie.
 * Das JS wrapp im Frontend jeden direkten Child von .swiper-wrapper in
 * ein .swiper-slide-Element.
 *
 * ACF-Felder (Inspector):
 *   slider_autoplay          true_false   Autoplay (Standard: false)
 *   slider_autoplay_delay    Number       Delay in ms (Standard: 4000)
 *   slider_loop              true_false   Endlos-Loop (Standard: true)
 *   slider_navigation        true_false   Pfeile (Standard: true)
 *   slider_pagination        Select       Dots / Leiste / Aus
 *   slider_slides_per_view   Number       Sichtbare Folien (Standard: 1)
 *   slider_space_between     Number       Abstand in px (Standard: 0)
 *   slider_effect            Select       slide / fade / coverflow
 *   slider_speed             Number       Transition in ms (Standard: 600)
 *   slider_centered          true_false   centeredSlides (Standard: false)
 *
 * InnerBlocks:
 *   Jeder direkt eingefügte Block = eine Folie.
 *   Empfehlung: Group-Blöcke pro Folie für strukturierten Inhalt.
 *
 * @package MediaLabAgencyCore
 * @since   1.11.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

static $slider_count = 0;
$slider_count++;
$slider_id = 'ml-slider-' . $slider_count;

// ── Swiper-Konfiguration ──────────────────────────────────────────────────────
$autoplay      = get_field( 'slider_autoplay' );
$autoplay_delay= max( 500, (int) ( get_field( 'slider_autoplay_delay'  ) ?: 4000 ) );
$loop          = get_field( 'slider_loop' ) !== false;
$navigation    = get_field( 'slider_navigation' ) !== false;
$pagination    = get_field( 'slider_pagination' ) ?: 'bullets';
$spv           = max( 1, (int) ( get_field( 'slider_slides_per_view'  ) ?: 1    ) );
$space_between = max( 0, (int) ( get_field( 'slider_space_between'    ) ?: 0    ) );
$effect        = get_field( 'slider_effect' ) ?: 'slide';
$speed         = max( 100, (int) ( get_field( 'slider_speed'          ) ?: 600  ) );
$centered      = (bool) get_field( 'slider_centered' );

$swiper_config = [
    'loop'           => $loop,
    'speed'          => $speed,
    'effect'         => $effect,
    'slidesPerView'  => $spv,
    'spaceBetween'   => $space_between,
    'centeredSlides' => $centered,
    'grabCursor'     => true,
    'a11y'           => [ 'enabled' => true ],
];

if ( $autoplay ) {
    $swiper_config['autoplay'] = [
        'delay'                => $autoplay_delay,
        'disableOnInteraction' => false,
        'pauseOnMouseEnter'    => true,
    ];
}

if ( $navigation ) {
    $swiper_config['navigation'] = [
        'nextEl' => '#' . $slider_id . ' .swiper-button-next',
        'prevEl' => '#' . $slider_id . ' .swiper-button-prev',
    ];
}

if ( $pagination !== 'none' ) {
    $swiper_config['pagination'] = [
        'el'        => '#' . $slider_id . ' .swiper-pagination',
        'type'      => $pagination === 'progressbar' ? 'progressbar' : 'bullets',
        'clickable' => true,
    ];
}

$classes = array_filter( [
    'ml-block-slider',
    'ml-slider--effect-' . sanitize_html_class( $effect ),
    $navigation    ? 'ml-slider--has-nav'  : '',
    $pagination !== 'none' ? 'ml-slider--has-pagination' : '',
    ! empty( $block['className'] ) ? $block['className'] : '',
    ! empty( $block['align'] )     ? 'align' . $block['align'] : '',
] );

$block_id_attr = ! empty( $block['anchor'] ) ? ' id="' . esc_attr( $block['anchor'] ) . '"' : '';
?>
<div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>"<?php echo $block_id_attr; ?>>

    <div id="<?php echo esc_attr( $slider_id ); ?>"
         class="swiper ml-slider__swiper"
         data-swiper="<?php echo esc_attr( wp_json_encode( $swiper_config ) ); ?>">

        <!-- InnerBlocks – jeder direkte Child wird per JS zu .swiper-slide -->
        <div class="swiper-wrapper ml-slider__wrapper">
            <?php echo $content; ?>
        </div>

        <?php if ( $navigation ) : ?>
        <button class="swiper-button-prev ml-slider__btn ml-slider__btn--prev"
                aria-label="<?php esc_attr_e( 'Vorherige Folie', 'media-lab-core' ); ?>"></button>
        <button class="swiper-button-next ml-slider__btn ml-slider__btn--next"
                aria-label="<?php esc_attr_e( 'Nächste Folie', 'media-lab-core' ); ?>"></button>
        <?php endif; ?>

        <?php if ( $pagination !== 'none' ) : ?>
        <div class="swiper-pagination ml-slider__pagination"></div>
        <?php endif; ?>

    </div>

</div>
