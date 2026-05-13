<?php
defined( 'ABSPATH' ) || exit;

$s = mlbkp_get_settings();
$next_run = MLBKP_Scheduler::get_next_run();
?>

<form id="mlb-settings-form" class="mlb-settings-form">

    <?php /* ─── Status-Banner ─────────────────────────────────────────────── */ ?>
    <div class="mlb-status-bar">
        <div class="mlb-status-item">
            <span class="mlb-status-label">Nächstes Backup</span>
            <strong><?php echo esc_html( $next_run ?? 'Kein Zeitplan aktiv' ); ?></strong>
        </div>
        <?php
        $last = MLBKP_Logger::get_last_successful();
        if ( $last ):
        ?>
        <div class="mlb-status-item">
            <span class="mlb-status-label">Letztes erfolgreiches Backup</span>
            <strong><?php echo esc_html( wp_date( 'd.m.Y H:i', strtotime( $last['finished_at'] ) ) ); ?></strong>
        </div>
        <?php endif; ?>
        <div class="mlb-status-item">
            <span class="mlb-status-label">phpseclib</span>
            <strong class="<?php echo class_exists( 'phpseclib3\Net\SFTP' ) ? 'mlb-ok' : 'mlb-error'; ?>">
                <?php echo class_exists( 'phpseclib3\Net\SFTP' ) ? '✅ Installiert' : '❌ Nicht installiert – composer install ausführen'; ?>
            </strong>
        </div>
    </div>

    <?php /* ─── SFTP-Verbindung ─────────────────────────────────────────── */ ?>
    <div class="mlb-card">
        <h2 class="mlb-card-title">🔌 SFTP-Verbindung (Hetzner Storage Box)</h2>
        <div class="mlb-grid-2">

            <div class="mlb-field">
                <label for="sftp_host">Hostname</label>
                <input type="text" id="sftp_host" name="sftp_host"
                       value="<?php echo esc_attr( $s['sftp_host'] ); ?>"
                       placeholder="u123456.your-storagebox.de" />
                <p class="description">Dein Hetzner Storage Box Hostname.</p>
            </div>

            <div class="mlb-field">
                <label for="sftp_port">Port</label>
                <input type="number" id="sftp_port" name="sftp_port"
                       value="<?php echo esc_attr( $s['sftp_port'] ); ?>"
                       min="1" max="65535" style="width: 120px;" />
                <p class="description">Standard: 22 (SFTP)</p>
            </div>

            <div class="mlb-field">
                <label for="sftp_username">Benutzername</label>
                <input type="text" id="sftp_username" name="sftp_username"
                       value="<?php echo esc_attr( $s['sftp_username'] ); ?>"
                       autocomplete="off" />
            </div>

            <div class="mlb-field">
                <label for="sftp_password">Passwort</label>
                <input type="password" id="sftp_password" name="sftp_password"
                       value="" placeholder="<?php echo ! empty( $s['sftp_password'] ) ? '(gespeichert – leer lassen zum Beibehalten)' : ''; ?>"
                       autocomplete="new-password" />
            </div>

            <div class="mlb-field mlb-span-2">
                <label for="sftp_path">Remote-Pfad</label>
                <input type="text" id="sftp_path" name="sftp_path"
                       value="<?php echo esc_attr( $s['sftp_path'] ); ?>"
                       placeholder="/backups" />
                <p class="description">Basisverzeichnis auf der Storage Box. Pro Website wird automatisch ein Unterordner angelegt.</p>
            </div>

        </div>

        <button type="button" id="mlb-test-connection" class="button button-secondary">
            🔍 Verbindung testen
        </button>
        <span id="mlb-connection-status" class="mlb-inline-status"></span>
    </div>

    <?php /* ─── Backup-Scope ─────────────────────────────────────────────── */ ?>
    <div class="mlb-card">
        <h2 class="mlb-card-title">📦 Was soll gesichert werden?</h2>
        <div class="mlb-scope-options">

            <label class="mlb-toggle-card <?php echo ! empty( $s['backup_database'] ) ? 'active' : ''; ?>">
                <input type="checkbox" name="backup_database" value="1"
                       <?php checked( ! empty( $s['backup_database'] ) ); ?> />
                <div class="mlb-toggle-icon">🗄</div>
                <div>
                    <strong>Datenbank</strong>
                    <span>SQL-Dump aller Tabellen (gzip-komprimiert)</span>
                </div>
            </label>

            <label class="mlb-toggle-card <?php echo ! empty( $s['backup_wpcontent'] ) ? 'active' : ''; ?>">
                <input type="checkbox" name="backup_wpcontent" value="1"
                       <?php checked( ! empty( $s['backup_wpcontent'] ) ); ?> />
                <div class="mlb-toggle-icon">📁</div>
                <div>
                    <strong>wp-content/</strong>
                    <span>Uploads, Plugins, Themes (ZIP)</span>
                    <?php
                    $wpcontent_size = MLBKP_File_Backup::estimate_size( WP_CONTENT_DIR );
                    if ( $wpcontent_size > 0 ):
                    ?>
                    <em>~<?php echo esc_html( MLBKP_Logger::format_bytes( $wpcontent_size ) ); ?></em>
                    <?php endif; ?>
                </div>
            </label>

            <label class="mlb-toggle-card <?php echo ! empty( $s['backup_wpcore'] ) ? 'active' : ''; ?>">
                <input type="checkbox" name="backup_wpcore" value="1"
                       <?php checked( ! empty( $s['backup_wpcore'] ) ); ?> />
                <div class="mlb-toggle-icon">⚙️</div>
                <div>
                    <strong>Vollständiges WordPress-Verzeichnis</strong>
                    <span>inkl. WordPress-Core-Dateien (große ZIP-Datei)</span>
                </div>
            </label>

        </div>

        <div class="mlb-field" style="margin-top: 20px;">
            <label for="exclude_paths">Ausschlüsse (optional)</label>
            <textarea id="exclude_paths" name="exclude_paths" rows="5"
                      placeholder="wp-content/themes/old-theme&#10;wp-content/plugins/heavy-plugin"><?php echo esc_textarea( $s['exclude_paths'] ); ?></textarea>
            <p class="description">Ein Pfad pro Zeile, relativ zum jeweiligen Backup-Stammverzeichnis.</p>
        </div>
    </div>

    <?php /* ─── Zeitplan ───────────────────────────────────────────────────── */ ?>
    <div class="mlb-card">
        <h2 class="mlb-card-title">🕐 Automatischer Zeitplan (WP-Cron)</h2>
        <div class="mlb-grid-2">

            <div class="mlb-field">
                <label for="schedule">Intervall</label>
                <select id="schedule" name="schedule">
                    <option value="none"   <?php selected( $s['schedule'], 'none'   ); ?>>Kein automatisches Backup</option>
                    <option value="daily"  <?php selected( $s['schedule'], 'daily'  ); ?>>Täglich</option>
                    <option value="weekly" <?php selected( $s['schedule'], 'weekly' ); ?>>Wöchentlich</option>
                </select>
            </div>

            <div class="mlb-field">
                <label for="schedule_time">Uhrzeit</label>
                <input type="time" id="schedule_time" name="schedule_time"
                       value="<?php echo esc_attr( $s['schedule_time'] ); ?>" />
                <p class="description">Serverzeit (UTC)</p>
            </div>

            <div class="mlb-field" id="mlb-field-day" style="<?php echo $s['schedule'] !== 'weekly' ? 'display:none;' : ''; ?>">
                <label for="schedule_day">Wochentag</label>
                <select id="schedule_day" name="schedule_day">
                    <?php
                    $days = [
                        'monday' => 'Montag', 'tuesday' => 'Dienstag', 'wednesday' => 'Mittwoch',
                        'thursday' => 'Donnerstag', 'friday' => 'Freitag',
                        'saturday' => 'Samstag', 'sunday' => 'Sonntag',
                    ];
                    foreach ( $days as $val => $label ):
                    ?>
                    <option value="<?php echo esc_attr( $val ); ?>" <?php selected( $s['schedule_day'], $val ); ?>>
                        <?php echo esc_html( $label ); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mlb-field">
                <label for="retention_count">Aufbewahrung</label>
                <input type="number" id="retention_count" name="retention_count"
                       value="<?php echo esc_attr( $s['retention_count'] ); ?>"
                       min="1" max="365" style="width: 100px;" />
                <p class="description">Anzahl der Backups, die behalten werden sollen.</p>
            </div>

        </div>
    </div>

    <?php /* ─── E-Mail-Benachrichtigung ──────────────────────────────────── */ ?>
    <div class="mlb-card">
        <h2 class="mlb-card-title">📧 E-Mail-Benachrichtigung</h2>
        <div class="mlb-grid-2">

            <div class="mlb-field">
                <label for="notify_email">E-Mail-Adresse</label>
                <input type="email" id="notify_email" name="notify_email"
                       value="<?php echo esc_attr( $s['notify_email'] ); ?>" />
            </div>

            <div class="mlb-field">
                <label for="notify_on">Benachrichtigen bei</label>
                <select id="notify_on" name="notify_on">
                    <option value="always" <?php selected( $s['notify_on'], 'always' ); ?>>Immer (Erfolg + Fehler)</option>
                    <option value="error"  <?php selected( $s['notify_on'], 'error'  ); ?>>Nur bei Fehler</option>
                    <option value="never"  <?php selected( $s['notify_on'], 'never'  ); ?>>Nie</option>
                </select>
            </div>

        </div>
    </div>

    <?php /* ─── Speichern ──────────────────────────────────────────────────── */ ?>
    <div class="mlb-submit-row">
        <button type="submit" id="mlb-save-settings" class="button button-primary button-large">
            💾 Einstellungen speichern
        </button>
        <span id="mlb-save-status" class="mlb-inline-status"></span>
    </div>

</form>
