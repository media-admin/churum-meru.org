/**
 * Slider Block – Swiper Init
 *
 * Wrapp jeden direkten Child von .swiper-wrapper in .swiper-slide
 * (damit InnerBlocks-Content automatisch zu Folien wird).
 * Dann Swiper mit der data-swiper-Konfiguration initialisieren.
 *
 * @since 1.11.0
 */
( function () {
    'use strict';

    document.querySelectorAll( '.ml-slider__swiper' ).forEach( function ( el ) {
        const wrapper  = el.querySelector( '.ml-slider__wrapper' );
        const configEl = el.dataset.swiper;
        if ( ! wrapper || ! configEl ) return;

        // ── InnerBlocks-Kinder zu Swiper-Slides machen ─────────────────────────
        // Direkte Children die noch kein .swiper-slide sind, werden eingewickelt.
        Array.from( wrapper.children ).forEach( function ( child ) {
            if ( child.classList.contains( 'swiper-slide' ) ) return;

            const slide = document.createElement( 'div' );
            slide.className = 'swiper-slide';
            wrapper.insertBefore( slide, child );
            slide.appendChild( child );
        } );

        // ── Swiper initialisieren ─────────────────────────────────────────────
        let config = {};
        try {
            config = JSON.parse( configEl );
        } catch ( e ) {
            console.warn( '[ml-slider] Ungültige Swiper-Config:', e );
            return;
        }

        if ( typeof Swiper === 'undefined' ) {
            console.warn( '[ml-slider] Swiper nicht geladen.' );
            return;
        }

        // Navigation-Elemente als echte DOM-Nodes übergeben
        if ( config.navigation ) {
            const parent = el.closest( '.ml-block-slider' );
            if ( parent ) {
                const prev = parent.querySelector( '.swiper-button-prev' );
                const next = parent.querySelector( '.swiper-button-next' );
                if ( prev && next ) {
                    config.navigation = { prevEl: prev, nextEl: next };
                }
            }
        }

        if ( config.pagination ) {
            const parent = el.closest( '.ml-block-slider' );
            if ( parent ) {
                const pag = parent.querySelector( '.swiper-pagination' );
                if ( pag ) config.pagination.el = pag;
            }
        }

        new Swiper( el, config );
    } );
} )();
