# 10. Bookings Plugin

**Plugin:** `media-lab-bookings`
**Version:** 1.0.0
**Abhängigkeiten:** ACF Pro, jQuery (WordPress Core)
**Optionale Integration:** Contact Form 7 (für hybride Workflows)

---

## Übersicht

Das Bookings Plugin ermöglicht standortbasierte Terminbuchungen direkt auf der Website. Buchungen werden in der WordPress-Datenbank gespeichert und per E-Mail bestätigt. Der Mailversand nutzt die SMTP-Konfiguration aus `media-lab-agency-core`.

### Features

| Feature | Beschreibung |
|---|---|
| Standortverwaltung | Beliebig viele Filialen/Standorte als CPT |
| Öffnungszeiten | Pro Wochentag konfigurierbar (on/off + von/bis) |
| Zeitslots | Slot-Dauer in Minuten, letzter Slot X Min. vor Schließung |
| Kapazitätslimit | Max. Buchungen pro Zeitslot konfigurierbar |
| Buchungs-CPT | Backend-Übersicht mit Statusverwaltung + Filter |
| E-Mail Kunden | Standortspezifisches HTML-Template mit Platzhaltern |
| E-Mail Filiale | Automatische Kopie an Filial-E-Mail-Adresse |
| SMTP | Nutzt `wp_mail()` → Core Plugin SMTP-Konfiguration |
| Shortcode | `[mlb_booking_form]` mit optionalem Standort-Preset |
| Datepicker | Flatpickr (DE) – geschlossene Wochentage automatisch deaktiviert |
| Formularfelder | Name, E-Mail, Telefon, Dienstleistung, Personenanzahl, Anmerkungen |

---

## Pluginstruktur

```
media-lab-bookings/
├── media-lab-bookings.php      Hauptdatei, Plugin-Header, Includes
├── inc/
│   ├── cpt.php                 CPTs mlb_location + mlb_booking, Custom Statuses
│   ├── acf-fields.php          ACF-Feldgruppen (programmatisch registriert)
│   ├── slots.php               Slot-Generierung + Kapazitätsprüfung
│   ├── ajax.php                AJAX-Handler (Standortdaten, Slots, Submit)
│   ├── mail.php                E-Mail-Versand, Platzhalter, HTML-Wrapper
│   ├── shortcode.php           [mlb_booking_form] + Asset-Enqueue
│   └── admin.php               Menü, Buchungs-Tabelle, Filter, Dashboard
├── templates/
│   └── booking-form.php        Formular-HTML-Template 
└── assets/
    ├── js/booking-form.js      Frontend-JS (Flatpickr, AJAX, Submit)
    └── css/booking-form.css    Formular-Styles mit CSS Custom Properties
```

### Klassen-Übersicht

| Klasse | Datei | Funktion |
|---|---|---|
| `MLB_CPT` | `cpt.php` | Registriert CPTs + Post Statuses |
| `MLB_Slots` | `slots.php` | Slot-Logik, Öffnungszeiten, Kapazität |
| `MLB_Ajax` | `ajax.php` | 3 AJAX-Endpunkte |
| `MLB_Mail` | `mail.php` | Kunden- + Admin-Mail |
| `MLB_Shortcode` | `shortcode.php` | Shortcode-Registrierung + Assets |
| `MLB_Admin` | `admin.php` | Backend-UI |

---

## Installation

1. ZIP in WordPress hochladen: **Plugins → Installieren → Plugin hochladen**
2. Plugin aktivieren
3. SMTP unter **Agency Core → E-Mail / SMTP** konfigurieren (falls noch nicht geschehen)
4. Ersten Standort anlegen: **Bookings → Standorte → Hinzufügen**

**Voraussetzungen:**
- ACF Pro aktiv
- `media-lab-agency-core` aktiv (SMTP)
- WordPress 6.0+, PHP 7.4+

---

## Custom Post Types

### `mlb_location` – Standorte

Wird im Backend unter **Bookings → Standorte** verwaltet. Jeder Standort definiert seine eigenen Öffnungszeiten, Slotregeln und E-Mail-Templates.

- Sichtbarkeit: `public => false` (keine Frontend-Archiv-Seite)
- Unterstützt: `title` (= Standortname)

### `mlb_booking` – Buchungen

Wird automatisch beim Formular-Submit erstellt. Manuelles Anlegen im Backend ebenfalls möglich.

- Sichtbarkeit: `public => false`
- Standard-Status bei neuem Eintrag: `mlb-pending`

### Custom Post Statuses

| Status-Key | Label | Bedeutung |
|---|---|---|
| `mlb-pending` | Ausstehend | Neu eingegangen, noch nicht bearbeitet |
| `mlb-confirmed` | Bestätigt | Buchung bestätigt |
| `mlb-cancelled` | Storniert | Buchung storniert |

---

## ACF-Felder

### Feldgruppe: Standort-Einstellungen (`group_mlb_location`)

**Tab: Öffnungszeiten** – Pro Wochentag (Montag–Sonntag):

| Feldname | Typ | Beschreibung |
|---|---|---|
| `mlb_{day}_active` | true/false | Standort an diesem Tag geöffnet? |
| `mlb_{day}_open` | text | Öffnungszeit (Format `HH:MM`) |
| `mlb_{day}_close` | text | Schließzeit (Format `HH:MM`) |

`{day}` = `mon`, `tue`, `wed`, `thu`, `fri`, `sat`, `sun`. Öffnung/Schließung werden per `conditional_logic` nur angezeigt wenn der Tag aktiv ist. Standard: Mo–Fr aktiv (`09:00`–`18:00`).

**Tab: Zeitslots**

| Feldname | Typ | Standard | Beschreibung |
|---|---|---|---|
| `mlb_slot_duration` | number | `60` | Slot-Dauer in Minuten |
| `mlb_last_slot_offset` | number | `60` | Letzter Slot startet X Min. vor Schließung |
| `mlb_max_capacity` | number | `1` | Max. Buchungen pro Slot |

**Tab: Kontakt**

| Feldname | Typ | Beschreibung |
|---|---|---|
| `mlb_location_email` | email | Filial-E-Mail (Kopie-Empfänger) |
| `mlb_location_phone` | text | Telefonnummer |
| `mlb_location_address` | textarea | Adresse (für E-Mail-Template) |

**Tab: Bestätigungsmail**

| Feldname | Typ | Beschreibung |
|---|---|---|
| `mlb_confirmation_subject` | text | Betreff der Kunden-E-Mail |
| `mlb_confirmation_template` | wysiwyg | HTML-Mailtext mit Platzhaltern |

**Tab: Dienstleistungen** – Repeater `mlb_services`:

| Sub-Feld | Typ | Beschreibung |
|---|---|---|
| `service_name` | text | Bezeichnung der Dienstleistung |
| `service_duration` | number | Optionale Dauer in Minuten |

---

### Feldgruppe: Buchungsdetails (`group_mlb_booking`)

| Feldname | Typ | Beschreibung |
|---|---|---|
| `mlb_booking_status` | select | `mlb-pending` / `mlb-confirmed` / `mlb-cancelled` |
| `mlb_booking_location` | post_object | Verknüpfter Standort (ID) |
| `mlb_booking_date` | date_picker | Buchungsdatum (`Y-m-d`) |
| `mlb_booking_time` | time_picker | Uhrzeit (`H:i`) |
| `mlb_booking_service` | text | Gewählte Dienstleistung |
| `mlb_booking_persons` | number | Personenanzahl |
| `mlb_booking_name` | text | Name des Kunden |
| `mlb_booking_email` | email | E-Mail des Kunden |
| `mlb_booking_phone` | text | Telefonnummer |
| `mlb_booking_notes` | textarea | Anmerkungen |

---

## Shortcode

```
[mlb_booking_form]
[mlb_booking_form location="123"]
[mlb_booking_form location="wien-mitte" title="Jetzt buchen"]
[mlb_booking_form location="42" class="mein-custom-wrapper"]
```

| Attribut | Typ | Beschreibung |
|---|---|---|
| `location` | ID oder Slug | Standort vorauswählen; Dropdown wird ausgeblendet |
| `title` | string | Überschrift über dem Formular |
| `class` | string | Zusätzliche CSS-Klassen auf dem Wrapper |

**Assets:** Flatpickr + Plugin-CSS/JS werden nur geladen wenn der Shortcode auf der aktuellen Seite vorhanden ist (`wp_register_*` + `wp_enqueue_*` on demand).

---

## E-Mail-Platzhalter

In `mlb_confirmation_subject` und `mlb_confirmation_template` sind folgende Platzhalter verfügbar:

| Platzhalter | Inhalt |
|---|---|
| `{name}` | Vor- und Nachname des Kunden |
| `{email}` | E-Mail-Adresse des Kunden |
| `{phone}` | Telefonnummer |
| `{date}` | Buchungsdatum (WordPress-Datumsformat) |
| `{time}` | Uhrzeit + „Uhr" |
| `{service}` | Gewählte Dienstleistung |
| `{persons}` | Personenanzahl |
| `{notes}` | Anmerkungen |
| `{location_name}` | Name des Standorts |
| `{location_address}` | Adresse des Standorts |
| `{location_email}` | Filial-E-Mail |
| `{location_phone}` | Filial-Telefon |
| `{booking_id}` | WordPress-Post-ID der Buchung (`#42`) |

**Fallback-Template:** Wenn kein Template im ACF-Feld hinterlegt ist, wird automatisch ein Standard-Template mit allen oben genannten Feldern verwendet.

---

## E-Mail-Versand

### Kunden-Bestätigung
- **An:** E-Mail-Adresse aus dem Formular
- **Betreff:** ACF-Feld `mlb_confirmation_subject` (mit Platzhalter-Ersetzung)
- **Body:** ACF-Feld `mlb_confirmation_template` (HTML, mit Platzhalter-Ersetzung)
- **Absender:** Website-Name + Admin-E-Mail (konfigurierbar über Core SMTP)

### Filial-Kopie
- **An:** `mlb_location_email` des gewählten Standorts
- **Betreff:** `[Neue Buchung] {Buchungstitel}`
- **Body:** Tabellarische Übersicht aller Buchungsdetails + Link direkt ins Backend

### SMTP-Konfiguration
Der Versand läuft über `wp_mail()`. Die SMTP-Einstellungen werden aus dem `media-lab-agency-core` Plugin übernommen:

```
WP-Admin → Agency Core → E-Mail / SMTP
```

Kein separates SMTP-Setup im Bookings Plugin erforderlich.

---

## Slot-Logik

### Generierung
`MLB_Slots::generate( $location_id, $date )` erzeugt alle Slots für einen Tag:

```
Öffnungszeit     → Schließzeit − last_slot_offset
Slot-Abstände    = slot_duration Minuten
Beispiel: 09:00–18:00, Dauer 60 Min., Offset 60 Min.
→ Slots: 09:00, 10:00, 11:00, 12:00, 13:00, 14:00, 15:00, 16:00, 17:00
```

### Kapazitätsprüfung
Pro Slot wird `MLB_Slots::count_bookings()` ausgeführt. Gezählt werden alle Buchungen mit Status `publish`, `mlb-pending` oder `mlb-confirmed` für denselben Standort, dasselbe Datum und dieselbe Uhrzeit. Stornierte Buchungen (`mlb-cancelled`) werden nicht gezählt.

### AJAX-Endpunkte

| Action | Funktion | Parameter |
|---|---|---|
| `mlb_get_location_data` | Wochentage + Services laden | `location_id`, `nonce` |
| `mlb_get_slots` | Zeitslots für Datum laden | `location_id`, `date`, `nonce` |
| `mlb_submit_booking` | Buchung speichern + Mails senden | Alle Formularfelder, `nonce` |

Alle Endpunkte sind per `check_ajax_referer( 'mlb_nonce', 'nonce' )` abgesichert und für eingeloggte wie nicht eingeloggte Nutzer verfügbar (`wp_ajax_nopriv_*`).

---

## Backend-Übersicht

Das Plugin fügt ein eigenes Toplevel-Menü **Bookings** hinzu:

```
Bookings
├── Übersicht       Dashboard mit Status-Statistiken
├── Buchungen       Liste aller mlb_booking Einträge
└── Standorte       Liste aller mlb_location Einträge
```

### Buchungs-Listenansicht

Spalten: Status (Badge), Standort, Datum, Uhrzeit, Kunde (Name + E-Mail + Telefon), Dienstleistung, Personenanzahl, Eingangsdatum.

Filter: **Standort** + **Status** (Dropdown-Filter über der Tabelle).

Sortierung: Datum und Status sind als sortierbare Spalten registriert.

### Status-Badges

| Badge | Farbe |
|---|---|
| Ausstehend | Gelb |
| Bestätigt | Grün |
| Storniert | Rot |

---

## Styling

Das Formular nutzt CSS Custom Properties für einfache Theme-Integration. Überschreiben durch einmalige Definition im Theme-CSS:

```css
.mlb-booking-form {
    --mlb-color-primary:    #d40000;   /* Primärfarbe (Buttons, Links, Akzente) */
    --mlb-color-primary-dk: #b30000;   /* Hover-Zustand */
    --mlb-color-border:     #d0d5dd;   /* Input-Rahmen */
    --mlb-color-radius:     6px;       /* Border-Radius */
    --mlb-font:             inherit;   /* Schriftart aus Theme übernehmen */
}
```

Alle weiteren Variablen sind in `assets/css/booking-form.css` dokumentiert.

---

## Erweiterung

### Neuen Standort anlegen

1. **Bookings → Standorte → Neuen Standort hinzufügen**
2. Titel = Standortname (erscheint im Formular-Dropdown)
3. ACF-Felder ausfüllen: Öffnungszeiten, Slots, Kontakt, E-Mail-Template, Dienstleistungen
4. Veröffentlichen

### Formular mit festem Standort einbinden

```php
// In einem Template oder Page-Builder-Block:
echo do_shortcode('[mlb_booking_form location="wien-mitte" title="Termin buchen"]');
```

### E-Mail-Template anpassen

Unter **Bookings → Standorte → {Standort bearbeiten} → Tab: Bestätigungsmail** steht ein WYSIWYG-Editor zur Verfügung. Platzhalter (z.B. `{date}`, `{location_address}`) werden automatisch ersetzt.

### Bestätigungs-HTML programmatisch überschreiben

```php
add_filter( 'mlb_confirmation_template', function( $template, $booking_id, $location_id ) {
    // Eigenes Template zurückgeben
    return '<p>Hallo {name}, deine Buchung am {date} ist eingegangen.</p>';
}, 10, 3 );
```

> **Hinweis:** Der Filter `mlb_confirmation_template` ist in v1.0.0 noch nicht implementiert. Er kann bei Bedarf in `inc/mail.php` ergänzt werden.

---

## Troubleshooting

### Keine Zeitslots werden angezeigt

1. Öffnungszeiten im Standort prüfen: ACF-Tab „Zeitslots" → `mlb_slot_duration` muss ausgefüllt sein
2. Gewähltes Datum ist an einem geschlossenen Wochentag → Flatpickr sollte diese Tage bereits deaktivieren
3. Alle Slots ausgebucht → `mlb_max_capacity` erhöhen oder bestehende Buchungen prüfen
4. Browser-Konsole auf AJAX-Fehler prüfen: `mlb_get_slots` → HTTP 200?

### Formular sendet, aber keine E-Mail kommt an

1. SMTP-Konfiguration testen: **Agency Core → E-Mail / SMTP → Test-Mail senden**
2. Spam-Ordner des Empfängers prüfen
3. `mlb_location_email` im Standort hinterlegt?
4. WordPress Debug Log prüfen (`wp-content/debug.log`) auf `wp_mail_failed`

### Standort-Dropdown im Frontend leer

Prüfen ob Standorte mit Status `publish` vorhanden sind:

```bash
wp post list --post_type=mlb_location --post_status=publish
```

### ACF-Felder erscheinen nicht

ACF Pro muss aktiv sein. Feldgruppen werden über `acf/include_fields` registriert. Prüfen:

```bash
wp plugin list | grep acf
# → advanced-custom-fields-pro  active
```

### Buchungen werden nicht in der DB gespeichert

Wenn `wp_insert_post()` fehlschlägt, erscheint im Frontend die Meldung „Buchung konnte nicht gespeichert werden." Im Debug Log steht der genaue Fehler. Häufigste Ursache: fehlende Schreibrechte oder zu restriktive `WP_DEBUG`-Konfiguration.

---

## Datei-Referenz

| Datei | Klasse / Funktion | Hook |
|---|---|---|
| `cpt.php` | `MLB_CPT::register()` | `init` |
| `cpt.php` | `MLB_CPT::register_statuses()` | `init` |
| `acf-fields.php` | `mlb_register_acf_fields()` | `acf/include_fields` |
| `slots.php` | `MLB_Slots::generate()` | – (statisch) |
| `slots.php` | `MLB_Slots::count_bookings()` | – (statisch) |
| `ajax.php` | `MLB_Ajax::get_location_data()` | `wp_ajax(_nopriv)_mlb_get_location_data` |
| `ajax.php` | `MLB_Ajax::get_slots()` | `wp_ajax(_nopriv)_mlb_get_slots` |
| `ajax.php` | `MLB_Ajax::submit_booking()` | `wp_ajax(_nopriv)_mlb_submit_booking` |
| `mail.php` | `MLB_Mail::send_confirmation()` | – (statisch, aufgerufen aus Ajax) |
| `shortcode.php` | `MLB_Shortcode::render()` | `shortcode mlb_booking_form` |
| `shortcode.php` | `MLB_Shortcode::register_assets()` | `wp_enqueue_scripts` |
| `admin.php` | `MLB_Admin::register_menu()` | `admin_menu` |
| `admin.php` | `MLB_Admin::dashboard_page()` | – (Callback) |
