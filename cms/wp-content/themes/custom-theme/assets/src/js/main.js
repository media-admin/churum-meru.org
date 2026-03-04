/**
 * Main Entry Point
 * Media Lab Starter Kit – Custom Theme
 *
 * Strategie:
 *  - Kern-Komponenten: synchron geladen (immer benötigt)
 *  - Schwere Komponenten: Dynamic Import (nur wenn DOM-Element vorhanden)
 */

// CSS
import '../scss/style.scss';

// Sentry (nur in Production, wird via Vite-Env gesteuert)
if (import.meta.env.PROD) {
  import('./utils/sentry').then(({ initSentry }) => initSentry());
}

// ─── Kern-Komponenten (immer geladen) ───────────────────────────────────────
import Navigation     from './components/navigation';
import DarkMode       from './components/theme-switcher';
import CookieNotice   from './components/cookie-notice';
import BackToTop      from './components/back-to-top';
import Notifications  from './components/notifications';
import initTopHeader  from './components/top-header';

// ─── Helfer ─────────────────────────────────────────────────────────────────
const safeInit = (name, initFn) => {
  try { initFn(); }
  catch (err) {
    if (import.meta.env.DEV) console.error(`[${name}]`, err);
  }
};

// Prüft ob ein CSS-Selektor im DOM existiert
const has = (selector) => !!document.querySelector(selector);

// ─── Initialisierung ────────────────────────────────────────────────────────
const initApp = async () => {

  // Kern (immer)
  safeInit('Navigation',   () => new Navigation());
  safeInit('DarkMode',     () => new DarkMode());
  safeInit('CookieNotice', () => new CookieNotice());
  safeInit('BackToTop',    () => new BackToTop());
  safeInit('Notifications',() => new Notifications());
  safeInit('TopHeader',    () => initTopHeader());

  // ── Lazy: nur wenn DOM-Element vorhanden ──────────────────────────────────

  if (has('.accordion, [data-accordion]')) {
    const { default: Accordion } = await import('./components/accordion');
    safeInit('Accordion', () => new Accordion());
  }

  if (has('.hero-slider, .swiper')) {
    const { default: HeroSlider } = await import('./components/hero-slider');
    safeInit('HeroSlider', () => new HeroSlider());
  }

  if (has('.testimonials-slider')) {
    const { default: TestimonialsSlider } = await import('./components/testimonials-slider');
    safeInit('TestimonialsSlider', () => new TestimonialsSlider());
  }

  if (has('.logo-carousel')) {
    const { default: LogoCarousel } = await import('./components/logo-carousel');
    safeInit('LogoCarousel', () => new LogoCarousel());
  }

  if (has('.carousel')) {
    await import('./components/carousel');
  }

  if (has('.lightbox, [data-lightbox]')) {
    const { default: Lightbox } = await import('./components/lightbox');
    safeInit('Lightbox', () => new Lightbox());
  }

  if (has('.modal, [data-modal]')) {
    const { default: Modal } = await import('./components/modal');
    safeInit('Modal', () => new Modal());
  }

  if (has('.image-comparison')) {
    const { default: ImageComparison } = await import('./components/image-comparison');
    safeInit('ImageComparison', () => new ImageComparison());
  }

  if (has('.stats, .stats-counter, [data-counter]')) {
    const { default: StatsCounter } = await import('./components/stats-counter');
    safeInit('StatsCounter', () => new StatsCounter());
  }

  if (has('.tabs, [data-tabs]')) {
    const { default: Tabs } = await import('./components/tabs');
    safeInit('Tabs', () => new Tabs());
  }

  if (has('.spoiler, [data-spoiler]')) {
    const { default: Spoiler } = await import('./components/spoiler');
    safeInit('Spoiler', () => new Spoiler());
  }

  if (has('.faq-accordion')) {
    await import('./components/faq-accordion');
  }

  if (has('.video-player, [data-video]')) {
    const { default: VideoPlayer } = await import('./components/video-player');
    safeInit('VideoPlayer', () => new VideoPlayer());
  }

  if (has('[data-scroll-animation], .animate-on-scroll')) {
    const { default: ScrollAnimations } = await import('./components/scroll-animations');
    safeInit('ScrollAnimations', () => new ScrollAnimations());
  }

  // ── Schwere Features: separater Chunk ─────────────────────────────────────

  if (has('.ajax-search, [data-ajax-search]')) {
    await import('./components/ajax-search');
  }

  if (has('.load-more, [data-load-more]')) {
    await import('./components/load-more');
  }

  if (has('.ajax-filters, [data-filters]')) {
    const { default: AjaxFilters } = await import('./components/ajax-filters');
    safeInit('AjaxFilters', () => new AjaxFilters());
  }

  if (has('.google-map, [data-map]')) {
    await import('./components/google-maps');
  }
};

// DOM Ready
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initApp);
} else {
  initApp();
}
