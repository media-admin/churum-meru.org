# Changelog — Media Lab Backup

Alle wesentlichen Änderungen werden in dieser Datei dokumentiert.
Format: [Keep a Changelog](https://keepachangelog.com/de/1.0.0/)
Versionierung: [Semantic Versioning](https://semver.org/)

---

## [1.0.0] — 2025-05-13

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
