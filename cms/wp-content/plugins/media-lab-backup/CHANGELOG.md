# Changelog — Media Lab Backup

Alle wesentlichen Änderungen werden in dieser Datei dokumentiert.
Format: [Keep a Changelog](https://keepachangelog.com/de/1.0.0/)
Versionierung: [Semantic Versioning](https://semver.org/)

---

## [1.1.0] — 2026-05-13

### Added
- **SSH-Key-Authentifizierung** als Alternative zu Passwort (via phpseclib3 `PublicKeyLoader`)
- **Konfigurierbarer Website-Unterordner** (`sftp_site_folder`) mit automatisch generiertem Vorschlag aus der Domain
- **WP-CLI-Integration** mit vier Befehlen:
  - `wp mlbkp backup [--type=<type>]` — Backup ausführen
  - `wp mlbkp status` — Konfiguration und letzten Backup-Status anzeigen
  - `wp mlbkp test` — SFTP-Verbindung testen
  - `wp mlbkp logs [--limit=<n>] [--format=<format>]` — Protokoll anzeigen

### Changed
- Standard-Remote-Basispfad von `/backups` auf `/` geändert (passend für Sub-Account-Root)
- SFTP-Einstellungen um Auth-Methode, Private Key und Key-Passphrase erweitert
- Settings-UI: Auth-Tabs (Passwort / SSH-Key) mit Panel-Toggle

### Fixed
- Doppeltes Plugin-Loading beim ZIP-Upload im WP-Admin verhindert (`defined()`-Guard)
- Klassenprefix von `MLB_` auf `MLBKP_` geändert (Kollision mit `media-lab-bookings`)

---



### Added
- Initiale Veröffentlichung
- Datenbank-Backup via `mysqldump` mit PHP-Fallback (chunk-weise INSERT-Generierung)
- GZIP-Komprimierung des SQL-Dumps
- Datei-Backup (wp-content / vollständiges WP-Verzeichnis) via PHP `ZipArchive`
- SFTP-Upload zur Hetzner Storage Box via phpseclib3 (Port 22)
- Automatische Verzeichnis-Erstellung auf dem Remote-Server (pro Site-Domain)
- Konfigurierbarer Backup-Scope: Datenbank, wp-content, WP-Core (einzeln oder kombiniert)
- WP-Cron-Integration: täglich oder wöchentlich, konfigurierbare Uhrzeit und Wochentag
- Manuelles Backup über Admin-UI mit Live-Log-Ausgabe
- SFTP-Verbindungstest direkt aus den Einstellungen
- Retention-Management: konfigurierbares Beibehalten der letzten N Backups auf dem Remote-Server
- E-Mail-Benachrichtigung: bei Fehler, immer oder nie
- Backup-Protokoll-Tabelle (`wp_mlb_logs`) mit Status, Größe, Dauer, Dateiname
- Admin-UI mit 3 Tabs: Einstellungen / Backup starten / Protokoll
- Ausschlüsse: konfigurierbare Liste auszuschließender Pfade (ein Pfad pro Zeile)
- Temp-Dateien in `wp-content/uploads/media-lab-backup/temp/` mit .htaccess-Schutz
- Sauberer Uninstall (Einstellungen, Tabelle, Temp-Verzeichnis, Cron-Jobs)
