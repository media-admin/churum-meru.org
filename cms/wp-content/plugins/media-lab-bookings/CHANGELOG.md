# Changelog

Alle wesentlichen Änderungen werden in dieser Datei dokumentiert.
Format basiert auf [Keep a Changelog](https://keepachangelog.com/de/1.0.0/),
Versionierung nach [Semantic Versioning](https://semver.org/lang/de/).

---

## [1.3.1] - 2026-03-27

### media-lab-bookings 1.3.1

#### Changed
- **`templates/booking-form.php`** – Name-Feld in eigene Zeile (volle Breite) verschoben; E-Mail + Telefon in 2-spaltigem `.mlb-form__row` darunter. Vorher waren alle drei Felder in einem 3-spaltigen Grid.
- **`assets/css/booking-form.css`** – `.mlb-form__row--3` entfernt (nicht mehr verwendet).

---

## [1.3.0] - 2026-03-27

### media-lab-bookings 1.3.0

#### Added
- **Template-Override** (`shortcode.php`) – Das Plugin lädt das Formular-Template via `locate_template()`. Eine Datei unter `{active-theme}/media-lab-bookings/booking-form.php` hat automatisch Vorrang vor dem Plugin-Template. Damit sind HTML-Anpassungen update-sicher im Theme ablegbar.
- **Filter `mlb_before_save_booking`** (`ajax.php`) – Alle Buchungsdaten vor dem Speichern filtern oder `false` zurückgeben um die Buchung abzubrechen.
- **Action `mlb_after_save_booking`** (`ajax.php`) – Wird nach erfolgreichem Speichern ausgelöst. Parameter: `$booking_id`, `$booking_data`.
- **Filter `mlb_confirmation_body`** (`mail.php`) – HTML-Body der Kunden-Bestätigungsmail filtern. Parameter: `$body`, `$booking_id`, `$location_id`.
- **Filter `mlb_confirmation_subject`** (`mail.php`) – Betreff der Kunden-Bestätigungsmail filtern. Parameter: `$subject`, `$booking_id`, `$location_id`.

---

## [1.2.2] - 2026-03-27

### media-lab-bookings 1.2.2

#### Fixed
- **`assets/js/booking-form.js`** – `$('.mlb-booking-form').each()` Initialisierung ans Ende der IIFE verschoben. `new MLBForm()` wurde zuvor aufgerufen bevor alle `MLBForm.prototype.*`-Methoden definiert waren → `Uncaught TypeError: self.loadLocationData is not a function`.

---

## [1.2.1] - 2026-03-25

### media-lab-bookings 1.2.1

#### Fixed
- **`templates/booking-form.php`** – `mlb_label()` aus dem Template entfernt; Funktion ist in `inc/shortcode.php` definiert. Verhindert Fatal Error „Cannot redeclare mlb_label()" wenn der Shortcode mehrfach auf einer Seite verwendet wird.

---

## [1.2.0] - 2026-03-25

### media-lab-bookings 1.2.0

#### Changed
- **`shortcode.php`** – Wenn nur 1 aktiver Standort (`mlb_location`, Status `publish`) vorhanden ist, wird dieser automatisch vorausgewählt und das Standort-Dropdown ausgeblendet. Ab 2 Standorten erscheint das Dropdown wie gewohnt. Manuell per Shortcode-Attribut gesetzte Standorte (`location="..."`) bleiben davon unberührt.

---

## [1.1.0] - 2026-03-25

### media-lab-bookings 1.1.0

#### Added
- **DSGVO-Zustimmungs-Checkbox** – Pflichtfeld vor dem Absenden; serverseitige Prüfung in `ajax.php`; clientseitige Validierung mit Inline-Fehlermeldung in `booking-form.js`; „Datenschutzerklärung" im Zustimmungstext wird automatisch mit der WP-Privacy-Policy-URL verlinkt
- **ACF-Tab „Formular-Labels"** pro Standort – alle 10 Feldbeschriftungen (Standort, Datum, Uhrzeit, Dienstleistung, Personenanzahl, Name, E-Mail, Telefon, Anmerkungen, Button) im Backend änderbar; DSGVO-Zustimmungstext und optionaler Hinweistext unter dem Button ebenfalls per ACF konfigurierbar; Fallback auf Standardtexte wenn Felder leer

#### Changed
- **`templates/booking-form.php`** – Labels werden aus ACF-Feldern des gewählten Standorts geladen via Hilfsfunktion `mlb_label()`; alter statischer Datenschutz-Hinweistext ersetzt durch konfigurierbare DSGVO-Checkbox; `privacy_note` optional
- **`assets/css/booking-form.css`** – Checkbox-Styling (`.mlb-form__field--checkbox`, `.mlb-form__checkbox-label`, `.mlb-form__field-error`) ergänzt; `accent-color` nutzt `--mlb-color-primary`

---

## [1.0.0] - 2026-03-25

### media-lab-bookings 1.0.0

#### Added

- **Custom Post Type `mlb_location`** – Standorte/Filialen mit ACF-Feldern für Öffnungszeiten, Zeitslots, Kontakt, E-Mail-Template und Dienstleistungen
- **Custom Post Type `mlb_booking`** – Buchungseinträge mit Backend-Übersicht, Custom Post Statuses (`mlb-pending`, `mlb-confirmed`, `mlb-cancelled`)
- **ACF-Feldgruppen** – programmatisch registriert via `acf/include_fields`; Öffnungszeiten für alle 7 Wochentage (je `active`, `open`, `close` mit `conditional_logic`); Zeitslot-Konfiguration; Kontaktfelder; WYSIWYG-Bestätigungsmail-Template; Dienstleistungen-Repeater
- **Slot-Logik** (`MLB_Slots`) – dynamische Slot-Generierung aus Öffnungszeiten, konfigurierbarer Slot-Dauer und `last_slot_offset`; Kapazitätsprüfung gegen bestehende Buchungen per `WP_Query`
- **AJAX-Endpunkte** (`MLB_Ajax`) – `mlb_get_location_data` (Wochentage + Services), `mlb_get_slots` (verfügbare Zeitslots), `mlb_submit_booking` (Validierung, Speichern, Mail); alle Endpunkte per Nonce abgesichert
- **E-Mail-Versand** (`MLB_Mail`) – Kunden-Bestätigung mit standortspezifischem HTML-Template und 13 Platzhaltern (`{name}`, `{date}`, `{time}`, `{location_name}` etc.); Kopie an Filial-E-Mail; Fallback-Template wenn kein ACF-Template hinterlegt; Filial-Benachrichtigung mit Backend-Link; HTML-Wrapper mit Inline-CSS
- **SMTP-Integration** – nutzt `wp_mail()` → SMTP-Konfiguration aus `media-lab-agency-core`; kein separates SMTP-Setup erforderlich
- **Shortcode `[mlb_booking_form]`** – Attribute `location` (ID oder Slug), `title`, `class`; Assets werden nur bei Bedarf geladen (`wp_register_*` + on-demand `wp_enqueue_*`)
- **Flatpickr-Integration** – Deutsch-Locale; geschlossene Wochentage automatisch deaktiviert; `altInput` mit lesbarem Datumsformat
- **Admin-Dashboard** (`MLB_Admin`) – Toplevel-Menü „Bookings"; Übersichtsseite mit Status-Statistiken; Buchungs-Listenansicht mit Spalten (Status-Badge, Standort, Datum, Uhrzeit, Kunde, Dienstleistung, Personen); Filter nach Standort + Status; sortierbare Spalten (Datum, Status); Standort-Listenansicht mit Slot-Zusammenfassung + Buchungszähler
- **Formular-Template** (`templates/booking-form.php`) – sauber vom PHP-Logic getrennt; mehrsprachige Fehlermeldungen über `mlbConfig.i18n`; Datenschutz-Hinweis mit Link auf WP-Privacy-Policy
- **Frontend-CSS** – CSS Custom Properties für Theme-Integration; responsives Grid; Flatpickr-Override; Spinner-Animation; Erfolgs- und Fehlermeldungs-States
- **Dokumentation** – `docs/14_BOOKINGS.md` im Format der bestehenden Docs-Dateien

---
