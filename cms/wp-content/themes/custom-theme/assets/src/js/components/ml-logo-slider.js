/**
 * ML Logo-Slider – Theme-Komponente
 *
 * Initialisiert alle Swiper-Slider vom medialab/logo-slider Gutenberg-Block.
 * Läuft im Theme-Bundle (Vite) damit Swiper als ES-Modul verfügbar ist.
 *
 * @since 1.11.0
 */

import Swiper from 'swiper/bundle';

export default class MLLogoSlider {
    constructor() {
        document.querySelectorAll( '.ml-logo-slider__swiper' ).forEach( el => {
            if ( el.swiper ) return;

            let config = {};
            try {
                config = JSON.parse( el.dataset.swiper || '{}' );
            } catch ( e ) {
                console.warn( '[ml-logo-slider] Ungültige Swiper-Config:', e );
                return;
            }

            try {
                new Swiper( el, config );
            } catch ( err ) {
                console.error( '[ml-logo-slider] Swiper Init-Fehler:', err );
            }
        } );
    }
}
