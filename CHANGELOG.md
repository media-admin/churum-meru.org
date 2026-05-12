# Changelog

Alle wesentlichen Änderungen am Media Lab Starter Kit werden hier dokumentiert.
Format basiert auf [Keep a Changelog](https://keepachangelog.com/de/1.0.0/),
Versionierung nach [Semantic Versioning](https://semver.org/lang/de/).

---

## [1.19.0] - 2026-05-12

### media-lab-agency-core 1.8.6

#### Added
- **Top Header – Drag & Drop Reihenfolge** (`inc/top-header-order.php`) –
  Neue Seite auf Agency Core → Top Header: Kontakt-Elemente (Adresse,
  Öffnungszeiten, Telefon, E-Mail) und Social-Media-Kanäle (Facebook,
  Instagram, LinkedIn, X/Twitter, YouTube, Xing) sind per Drag & Drop
  sortierbar. Reihenfolge wird in `wp_options` als JSON gespeichert
  (`medialab_top_header_item_order`, `medialab_top_header_social_order`).
  AJAX-Handler mit Nonce-Schutz; `jquery-ui-sortable` aus WP Core.

#### Changed
- **Social Share Buttons** (`assets/css/social-share.css`) – Standard-Button-
  Größe von `2.5rem` auf `1.75rem` reduziert; Padding von `0 0.75rem` auf
  `0 0.5rem`; SVG-Größe von `1.125rem` auf `1rem` verkleinert.

### custom-theme

#### Added
- **Notifications – Rich Content Popup** (`assets/src/scss/components/_notifications.scss`) –
  Neue Modifier-Klassen `.notification-popup--rich` und
  `.notification-popup__body--rich` mit scoped Gutenberg-Block-Styles
  (wp:image, wp:buttons, Überschriften, Listen, Separator). Overlay-Opacity
  von `0.5` auf `0.85` erhöht.

#### Changed
- **Notifications – Gutenberg-Content** (`assets/src/js/components/notifications.js`) –
  `showPopup()` rendert jetzt `n.content` (Gutenberg-HTML) vorrangig vor
  `n.message` (ACF-Kurztext). Bei Rich Content: kein Dashicon-Icon,
  Popup-Breite auf Container ausgedehnt.
- **Notifications – Popup-Sizing** (`_notifications.scss`) –
  Popup-Breite auf `$container-width` gesetzt; Overlay-Padding dynamisch:
  `max($spacing-4, calc((100vw - $container-width) / 2))` → Popup sitzt
  pixel-genau im Container-Raster.
- **Notifications – Text-Ausrichtung** (`_notifications.scss`) –
  `text-align: center` auf `.notification-popup` greift nur noch wenn kein
  Rich Content vorhanden (`&:not(.notification-popup--rich)`); Gutenberg-
  Ausrichtungsklassen (`.has-text-align-*`) werden vollständig respektiert.
- **Top Header – Reihenfolge** (`header.php`) –
  Feste `if/endif`-Blöcke für Kontakt-Elemente durch Render-Map + Loop
  ersetzt. Reihenfolge kommt aus `medialab_get_top_header_order()` und
  `medialab_get_top_header_social_order()` (Fallback: bisherige
  Standard-Reihenfolge). Social Media bleibt rechtsbündig.

---

## [1.19.0] - 2026-05-07

### media-lab-agency-core 1.9.0

#### Added
- **Cookie Consent – Mehrsprachigkeit** (`inc/cookie-consent.php`)
  - Neue Option „Mehrsprachigkeit aktivieren" in Agency Core → Cookie Consent
  - Repeater-Feld `cc_languages`: pro Sprache eigene Texte für Banner, Buttons,
    Modal, Kategorie-Bezeichnungen und Datenschutz-URL
  - Spracherkennung in folgender Priorität: Polylang → WPML (`ICL_LANGUAGE_CODE`) →
    WP-Locale-Fallback (`get_locale()` auf 2 Zeichen gekürzt)
  - Die erste Repeater-Zeile gilt als Standard-Sprache (Fallback bei
    fehlender Übereinstimmung)
  - Wenn Mehrsprachigkeit deaktiviert ist, bleiben alle bisherigen Flat-Felder
    unverändert aktiv (vollständige Rückwärtskompatibilität)
  - Neues ACF-Feld `cc_always_active`: konfigurierbar statt hartkodiert
  - Neues ACF-Feld `cc_banner_text_usa`: Zusatztext für Drittstaaten-Hinweis,
    pro Sprache konfigurierbar
  - Kategorie-Aktivierung (Statistik/Marketing/Komfort) bleibt global;
    nur Labels und Beschreibungen sind sprachabhängig
  - Code-Snippets (GA4, Meta Pixel …) bleiben sprachunabhängig in den Flat-Feldern

- **Cookie Consent – JS** (`assets/src/js/components/cookie-notice.js`)
  - Hardkodierte deutsche Fallback-Texte durch sprachneutrale Defaults ersetzt
  - `bannerTextUSA` wird jetzt aus `window.cookieConsent.texts` gelesen (PHP-gesteuert)
  - `bannerTitle` wird im Banner-HTML gerendert wenn gesetzt

- **Share-Buttons – Globale Konfiguration** (`inc/social-share.php`)
  - Neue Admin-Seite Agency Core → Share-Buttons (`agency-core-social-share`)
  - Zentrale Konfiguration: aktivierte Kanäle, Standard-Layout, Label-Text
  - Auto-Insert: optionale automatische Einbindung nach `the_content` für
    konfigurierbare Post-Types
  - Shortcode `[medialab_share]` liest globale Defaults; einzelne Attribute
    können weiterhin pro Instanz überschrieben werden
  - Neue PHP-Template-Funktion `medialab_share( $args )` für Theme-Templates
  - Neuer Kanal `copy` – „Link kopieren" via `navigator.clipboard` mit
    2-Sekunden-Feedback-Label (kein externes Script)

- **Share-Buttons – Gutenberg Block** (`blocks/social-share/`)
  - Neuer ACF-Block `medialab/social-share` in der Kategorie „Design"
  - Block-Inspector: „Globale Einstellungen überschreiben" schaltet
    individuelle Kanal-/Layout-/Label-Auswahl pro Block-Instanz frei
  - `render.php` merged Block-Felder mit globalen Defaults
  - Vorschau-Modus (`"mode": "preview"`) im Editor

#### Changed
- `MEDIALAB_CORE_VERSION` auf `1.9.0` angehoben
- `inc/blocks.php`: `'social-share'` zur ACF-Blocks-Liste hinzugefügt

---

## [1.8.5] - 2026-04-23

### media-lab-agency-core 1.8.5

#### Fixed
- **E-Mail Obfuskierung – Gutenberg Buttons** – `protect_content_emails()` in
  `email-obfuscation.php` baute das `<a>`-Tag bisher komplett neu auf, wodurch
  alle Original-Attribute (insb. `class="wp-block-button__link wp-element-button"`)
  verloren gingen und Buttons nicht mehr korrekt dargestellt wurden. Die Funktion
  modifiziert nun das bestehende Tag chirurgisch: nur `href` wird ersetzt und
  `data-obf-email`/`data-obf-label` werden ergänzt – alle anderen Attribute
  (`class`, `id`, `target`, `rel`, …) bleiben erhalten.

---

## [1.18.0] - 2026-03-26

### custom-theme 1.14.0

#### Added
- **Footer Legal Navigation** – neue Menu-Location `footer-legal` registriert
  - Ausgabe via `wp_nav_menu()` in `footer.php` (Tiefe 1, keine Submenüs)
  - Geeignet für Impressum, Datenschutz, AGB, Cookie-Richtlinie
  - Zuweisung im WP-Admin unter Design → Menüs
- **Footer Legal Styling** – `_footer.scss`
  - `.footer-legal` – dezente horizontale Link-Leiste mit Trennpunkten (`·`)
  - `.footer-legal a` – `font-size-xs`, `color-text-muted`, Hover: `color-primary`
  - `.site-footer__bottom` – Flex-Layout: Copyright links, Legal-Links rechts
  - Responsive: unterhalb 768px gestapelt, linksbündig
- **Credit-Line** – dezenter Agentur-Hinweis ganz unten im Footer
  - Text: „Konzept und Programmierung: Media Lab Tritremmel GmbH"
  - Link auf `https://www.media-lab.at` (öffnet in neuem Tab)
  - Styling: `opacity: 0.6` im Ruhezustand, `opacity: 1` bei Hover
  - Trennlinie (`border-top`) zwischen Legal-Bereich und Credit-Line

---

## [1.17.0] - 2026-03-10

### custom-theme 1.13.0

#### Added
- **WCAG 2.1 AA Audit** – 11 Fixes implementiert
  - Skip-Link für Tastaturnavigation
  - Keyboard-Pause für animierte Elemente
  - Primärfarbe `#ff0000` → `#d40000` (WCAG Kontrastanforderung)
  - Focus-Styles für alle interaktiven Elemente
  - `aria-hidden` auf dekorativen Elementen
  - Alt-Text-Fallback für Bilder ohne Alt-Attribut
  - Heading-Level-Hierarchie korrigiert
  - Touch-Targets auf min. 44×44px vergrößert
  - `prefers-reduced-motion` Media Query eingebaut
  - Kontrast-Fixes für Text auf farbigen Hintergründen
  - Semantische Struktur (`main`, `nav`, `footer` Landmarks)

---

## [1.16.0] - 2026-02-20

### custom-theme 1.12.0 / media-lab-agency-core 1.6.0

#### Added
- **8 Custom Gutenberg Blocks** abgeschlossen (Kategorie „Design")
  - Hero, Testimonial, Team-Mitglied, Logo-Leiste, Logo-Slider (ACF-Blöcke)
  - CTA-Banner, Accordion/FAQ, Icon+Text (Native Blöcke)
- **ACF-Felder** via PHP registriert (`inc/acf-blocks.php`)
