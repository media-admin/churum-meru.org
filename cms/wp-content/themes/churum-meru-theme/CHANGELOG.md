# Changelog

Alle nennenswerten Änderungen an diesem Theme werden hier dokumentiert.

> Frühere Einträge (vor 1.14.1) tragen keine Versionsnummer – aus der
> Commit-Historie rekonstruiert, da vor diesem Zeitpunkt keine
> Versionierung geführt wurde.

## 1.14.1 – 2026-09-24

- Fix: Hero-/Startseiten-Slider – Untertitel (`.ml-slider__slide-text`) war unformatiert und auf hellen Hintergrundbildern schlecht lesbar. Jetzt weiß mit Text-Shadow, analog zum bestehenden `.ml-slider__slide-heading`-Badge.
- Fix: Cookie-Notice angepasst (`assets/src/js/components/cookie-notice.js`)
- feat: DSGVO-konforme Social Embeds (Facebook-Video-, Social-Embed- und YouTube-nocookie-Consent-Komponenten) ergänzt
- chore: Build-Artefakte + Vite-Enqueue-Anpassungen für neue Blöcke
- chore: `media-lab-agency-core` ist keine Repo-Abhängigkeit mehr innerhalb dieses Client-Repos – wird künftig ausschließlich im Starter-Kit-Repo (`media-admin/media-lab-starter-kit`) versioniert und über normale Plugin-Updates eingespielt

## Vor 1.14.1 (unversioniert)

- 2026-08-11 – Skeleton-Loading-States für bessere UX bei AJAX-Requests ergänzt
- 2026-08-03 – Hintergrundmusik-Player-Komponente ergänzt
- 2026-07-21 – Fix (Performance): Default-Wert für `$attr` in `customtheme_webp_picture_element` gesetzt
- 2026-06-24 – Header-Höhe und Mobile-Styles aktualisiert
- 2026-06-05 – Startseiten-Styles verbessert, Hero-Image-Bedingungsprüfung optimiert
- 2026-06-05 – CF7-Select-Styling gefixt, Swiper-Copy-Plugin zur Vite-Config ergänzt
- 2026-06-05 – Codestruktur refactored für bessere Lesbarkeit/Wartbarkeit
- 2026-06-03 – `vite.config.js`: Chunk-Naming und manuelles Chunking für Swiper verbessert
- 2026-06-03 – SCSS für Startseiten-Slider refactored, Header-Styles aktualisiert
- 2026-06-03 – SCSS für Startseite refactored (Slider-Layout, Abstände)
- 2026-06-02 – Hero-Image-Logik verbessert, Inline-Styles für bessere Darstellung ergänzt
- 2026-06-01 – Typografie und Layout für Hero- und Kategorie-Beiträge verbessert
- 2026-06-01 – Footer-Logo-Anpassungsoption ergänzt, deutsche Übersetzungen für Twenty Twenty-Four ergänzt
- 2026-05-28 – Mitglieder-Grid-Styles ergänzt, neue XML-Exportdatei
- 2026-05-27 – Codestruktur refactored, Performance in mehreren Modulen optimiert
- 2026-05-26 – WooCommerce-E-Mail-Templates und Styles ergänzt