# Media Lab Backup

WordPress-Backup-Plugin für automatische Sicherungen zur **Hetzner Storage Box** via SFTP.

Entwickelt von [Media Lab Tritremmel GmbH](https://media-lab.at).

---

## Features

- ✅ **Datenbank-Backup** — SQL-Dump via `mysqldump` (mit PHP-Fallback)
- ✅ **Datei-Backup** — `wp-content/` oder vollständiges WP-Verzeichnis als ZIP
- ✅ **SFTP-Upload** — via phpseclib (Port 22, kein SSH2-Extension nötig)
- ✅ **Retention** — automatisches Löschen alter Backups
- ✅ **WP-Cron** — täglich oder wöchentlich, konfigurierbare Uhrzeit
- ✅ **Manuelles Backup** — jederzeit aus dem Admin-Bereich
- ✅ **E-Mail-Benachrichtigung** — bei Fehler oder immer
- ✅ **Verbindungstest** — SFTP-Verbindung vor dem ersten Backup prüfen

---

## Voraussetzungen

- WordPress 6.0+
- PHP 8.0+
- Composer
- PHP-Extension: `zip`, `zlib`
- Hetzner Storage Box mit SFTP-Zugang

---

## Installation

### 1. Plugin hochladen

```
wp-content/plugins/media-lab-backup/
```

### 2. Composer-Abhängigkeiten installieren

```bash
cd wp-content/plugins/media-lab-backup
composer install --no-dev --optimize-autoloader
```

### 3. Plugin aktivieren

Im WordPress-Admin unter **Plugins → Installierte Plugins**.

### 4. Konfigurieren

**WP-Admin → ML Backup → Einstellungen**

- Hetzner Storage Box Hostname eintragen (z.B. `u123456.your-storagebox.de`)
- Port: `22`
- Benutzername & Passwort der Storage Box
- Remote-Pfad (z.B. `/backups`)
- Backup-Scope auswählen
- Zeitplan konfigurieren
- **Verbindung testen** — dann speichern

---

## Hetzner Storage Box einrichten

1. Im Hetzner Robot unter **Storage Box** → **Zugangsdaten**
2. SFTP-Zugang ist standardmäßig aktiv
3. Hostname: `uXXXXXX.your-storagebox.de`
4. Port: `22`
5. Benutzername: `uXXXXXX`

Optional: Sub-Account für Backups anlegen (empfohlen).

---

## Verzeichnisstruktur auf der Storage Box

```
/backups/
└── meinedomain-at/          ← automatisch nach Site-Domain benannt
    ├── db-backup-2025-01-15_02-00-00.sql.gz
    ├── db-backup-2025-01-16_02-00-00.sql.gz
    ├── files-wpcontent-2025-01-15_02-00-00.zip
    └── files-wpcontent-2025-01-16_02-00-00.zip
```

---

## Temp-Dateien

Backup-Dateien werden temporär in gespeichert in:

```
wp-content/uploads/media-lab-backup/temp/
```

Das Verzeichnis ist durch `.htaccess` gegen direkten Zugriff geschützt.
Temp-Dateien werden nach dem Upload automatisch gelöscht.

---

## WP-CLI

Manuelles Backup via WP-CLI (optional, für Cron via System-Cron):

```bash
wp eval "require_once WP_PLUGIN_DIR . '/media-lab-backup/media-lab-backup.php'; (new MLB_Backup_Runner())->run('full', 'cron');"
```

---

## Troubleshooting

**„phpseclib ist nicht installiert"**
→ `composer install` im Plugin-Verzeichnis ausführen.

**„SFTP-Login fehlgeschlagen"**
→ Zugangsdaten prüfen, Port 22 muss offen sein. Verbindungstest nutzen.

**Backup timeout im Browser**
→ Für sehr große Sites: Backup über WP-Cron oder WP-CLI starten.

**mysqldump nicht verfügbar**
→ Plugin verwendet automatisch den PHP-Fallback-Dump. Funktioniert auf Shared Hosting.

---

## Changelog

Siehe [CHANGELOG.md](CHANGELOG.md).

---

## Lizenz

GPL v2 or later — https://www.gnu.org/licenses/gpl-2.0.html
