export default class BackgroundMusic {
    constructor() {
        this.player = document.getElementById('bg-music-player');
        this.audio = document.getElementById('bg-music');
        this.toggle = document.getElementById('bg-music-toggle');
        if (!this.audio || !this.toggle || !this.player) return;

        this._moveIntoSlider();
        this._init();
    }

    _moveIntoSlider() {
        // Zielcontainer: der Media Lab Block-Slider auf der Startseite
        const slider = document.querySelector('.ml-block-slider .swiper');
        if (slider) {
            slider.appendChild(this.player);
        }
        // Falls kein Slider vorhanden (z.B. andere Seiten), bleibt Player im Header
    }

    _init() {
        this.audio.play().catch(() => {
            document.addEventListener('click', () => this.audio.play(), { once: true });
        });

        this.toggle.addEventListener('click', () => this._toggleSound());
    }

    _toggleSound() {
        this.audio.muted = !this.audio.muted;
        this.toggle.setAttribute('aria-pressed', String(!this.audio.muted));
        this.toggle.classList.toggle('is-unmuted', !this.audio.muted);

        if (!this.audio.muted && this.audio.paused) {
            this.audio.play();
        }
    }
}