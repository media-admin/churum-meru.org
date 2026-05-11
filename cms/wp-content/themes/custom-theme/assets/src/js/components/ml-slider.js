/**
 * ML Slider – Theme-Komponente
 *
 * Initialisiert alle Swiper-Slider vom medialab/slider Gutenberg-Block.
 * Läuft im Theme-Bundle (Vite) damit Swiper als ES-Modul verfügbar ist.
 *
 * Was diese Komponente tut:
 *  1. Jeden direkten Child von .swiper-wrapper in ein .swiper-slide wrappen
 *     (InnerBlocks-Output ist noch nicht in Swiper-Slides)
 *  2. data-swiper JSON-Config parsen
 *  3. Navigation- und Pagination-Elemente als DOM-Referenzen übergeben
 *  4. Swiper initialisieren
 *
 * @since 1.11.0
 */

import Swiper from 'swiper/bundle';

export default class MLSlider {
    constructor() {
        document.querySelectorAll( '.ml-slider__swiper' ).forEach( el => {
            // Bereits initialisiert?
            if ( el.swiper ) return;

            const wrapper = el.querySelector( '.ml-slider__wrapper' );
            if ( ! wrapper ) return;

            // ── InnerBlocks-Children → .swiper-slide ─────────────────────────
            Array.from( wrapper.children ).forEach( child => {
                if ( child.classList.contains( 'swiper-slide' ) ) return;
                const slide = document.createElement( 'div' );
                slide.className = 'swiper-slide';
                wrapper.insertBefore( slide, child );
                slide.appendChild( child );
            } );

            if ( ! wrapper.querySelector( '.swiper-slide' ) ) return;

            // ── Config parsen ─────────────────────────────────────────────────
            let config = {};
            try {
                config = JSON.parse( el.dataset.swiper || '{}' );
            } catch ( e ) {
                console.warn( '[ml-slider] Ungültige Swiper-Config:', e );
                return;
            }

            // ── Navigation: DOM-Referenzen statt Selektoren ───────────────────
            const parent = el.closest( '.ml-block-slider' );

            if ( config.navigation && parent ) {
                const prevBtn = parent.querySelector( '.swiper-button-prev' );
                const nextBtn = parent.querySelector( '.swiper-button-next' );
                config.navigation = prevBtn && nextBtn
                    ? { prevEl: prevBtn, nextEl: nextBtn }
                    : false;
            }

            // ── Pagination ────────────────────────────────────────────────────
            if ( config.pagination && parent ) {
                const pag = parent.querySelector( '.swiper-pagination' );
                if ( pag ) {
                    config.pagination = { ...config.pagination, el: pag };
                } else {
                    delete config.pagination;
                }
            }

            // ── Swiper init ───────────────────────────────────────────────────
            try {
                new Swiper( el, config );
            } catch ( err ) {
                console.error( '[ml-slider] Swiper Init-Fehler:', err );
            }
        } );
    }
}
