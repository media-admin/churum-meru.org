# Changelog

Alle wesentlichen Änderungen werden in dieser Datei dokumentiert.
Format basiert auf [Keep a Changelog](https://keepachangelog.com/de/1.0.0/),
Versionierung nach [Semantic Versioning](https://semver.org/lang/de/).

---

## [1.1.0] - 2026-03-04

### media-lab-agency-core 1.3.0

#### Added
- **Top Header** – Kontaktleiste über dem Hauptheader (Adresse, Öffnungszeiten, Telefon, E-Mail, Social Media, Styling-Optionen) via ACF Settings
- **Logo** – Desktop- und Mobile-Logo hochladbar mit konfigurierbarer Breite; Fallback auf Seitennamen
- **Dark/Light Mode** – System-Präferenz (`prefers-color-scheme`) wird nun korrekt berücksichtigt; FOUC durch Inline-Script im `<head>` verhindert; `localStorage` wird nur bei expliziter User-Wahl beschrieben
- **Drag & Drop Post Order** – Sortierung aller Posts, Pages und CPTs per Drag & Drop in der Admin-Listenansicht; Reihenfolge in `menu_order` gespeichert
- **Drag & Drop Term Order** – Sortierung aller Taxonomy Terms per Drag & Drop; Reihenfolge in Term Meta gespeichert
- **Duplicate Post / Term** – „Duplizieren"-Link in allen Post- und Term-Listenansichten; kopiert Titel, Inhalt, Meta (inkl. ACF), Taxonomien, Featured Image; Duplikat immer als Entwurf
- **SMTP Mailer** – SMTP-Konfiguration (Host, Port, TLS/SSL, Credentials, Absender) via ACF Settings; Test-Mail-Funktion direkt im Backend
- **E-Mail Obfuskierung** – ROT13-basierter Spam-Schutz für E-Mail-Adressen im Content; automatischer Schutz aller `mailto:`-Links oder manuell via `[obfuscate_email]` Shortcode
- **White Label** – Vollständiges Backend-Branding: Login-Screen (Logo, Hintergrund, Primärfarbe), Admin-Bar, Dashboard-Widget mit Agentur-Kontaktdaten, Footer-Text; Menü-Sichtbarkeit nach Benutzerrolle konfigurierbar

#### Changed
- **ACF Settings** umstrukturiert: Plugin Status als eigene Gruppe oben; Hero Image Felder aus separater Sub-Page in Haupteinstellungen integriert; Mehrsprachigkeit als letzte Gruppe; White Label nach Plugin Status eingefügt
- **Admin-Menü** bereinigt: Doppelter „Agency Core"-Eintrag entfernt; saubere Menüstruktur (Einstellungen → Activity Log)
- **Hook-Reihenfolge** stabilisiert: `acf_add_options_page` via nativen `add_menu_page` + `acf_add_options_sub_page` für zuverlässige Menü-Registrierung unabhängig von ACF-Hook-Timing

#### Fixed
- Doppelter Menüeintrag „Agency Core" nach `remove_submenu_page` mit Priorität 999 entfernt
- Activity Log erscheint nun korrekt nach „Einstellungen" (Hook-Priorität 999)
- Drag-Handle erscheint jetzt inline vor dem Titel-Link ohne Zeilenumbruch

---

### media-lab-seo 1.1.0

#### Added
- **Redirections** – 301/302/307 Redirects mit Wildcard-Support (`/pfad/*`); Hit-Counter pro Redirect; separater Tab im Backend
- **404-Log** – Automatisches Tracking aller 404-Aufrufe mit URL, Referrer, Anzahl und Zeitstempel; direkt aus Log einen Redirect anlegen via Modal; Log leeren
- **SEO Meta Box** – Pro Post/Page/CPT: Meta Title, Meta Description mit Live-Zeichenzähler (grün/gelb/rot), Google Snippet Vorschau (live), Fokus-Keyword, Canonical URL, OG Image (Medien-Picker), Robots (noindex/nofollow)

#### Changed
- **Open Graph** und **Twitter Cards** nutzen jetzt zentrale Helper-Funktionen (`medialab_seo_get_title()`, `medialab_seo_get_description()`, `medialab_seo_get_og_image()`) – per Post überschreibbare Werte werden automatisch verwendet

---

### custom-theme 1.1.0

#### Added
- Logo-Ausgabe in `header.php` mit Desktop/Mobile-Variante via ACF Options
- Top Header Rendering in `header.php` (Adresse, Öffnungszeiten, Telefon, E-Mail, Social Media)
- `_top-header.scss` – Styles für alle Farbvarianten und Mobile-Verhalten
- Inline Theme-Detection Script im `<head>` verhindert FOUC beim Dark/Light Mode

#### Fixed
- Theme Switcher: System-Präferenz wird korrekt respektiert; `localStorage` nur bei expliziter Nutzerwahl

---

### Projekt

#### Changed
- `.gitignore` umfassend aktualisiert: `.env.staging`/`.env.production` hinzugefügt, Backup-Files aller Varianten, inaktive Plugins, Test-Artefakte (`playwright-report/`, `test-results/`), `query-monitor/`

---

## [1.0.0] - 2026-01-27

### Added
- Initial theme setup
- Homepage template mit Hero, Features, CTA
- Card und Button Components
- Mobile-responsive Navigation
- Custom MU-Plugins
- Deployment Scripts
- Figma Design Tokens integriert (Color System, Typography Scale, Spacing System)
- Custom Theme Struktur mit ACF Integration
- Custom Post Types
- SEO Optimierung

## [0.1.0] - 2026-01-20

### Added
- Projektinitialisierung
- Git Repository Setup
- Vite Build System