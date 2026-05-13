<?php
defined( 'ABSPATH' ) || exit;

use phpseclib3\Net\SFTP;

/**
 * MLBKP_SFTP
 *
 * Wrapper um phpseclib3 SFTP für Uploads zur Hetzner Storage Box.
 */
class MLBKP_SFTP {

    private SFTP $sftp;
    private string $remote_base;
    private string $site_slug;

    /**
     * @param array $settings  Plugin-Einstellungen (sftp_host, sftp_port, sftp_username, sftp_password, sftp_path)
     * @throws RuntimeException bei Verbindungs- oder Login-Fehler
     */
    public function __construct( array $settings ) {
        if ( ! class_exists( 'phpseclib3\Net\SFTP' ) ) {
            throw new RuntimeException(
                'phpseclib ist nicht installiert. Bitte "composer install" im Plugin-Verzeichnis ausführen: ' . MLBKP_PLUGIN_DIR
            );
        }

        $host     = trim( $settings['sftp_host'] ?? '' );
        $port     = (int) ( $settings['sftp_port'] ?? 22 );
        $username = trim( $settings['sftp_username'] ?? '' );
        $password = $settings['sftp_password'] ?? '';

        $this->remote_base = rtrim( $settings['sftp_path'] ?? '/backups', '/' );
        $this->site_slug   = $this->build_site_slug();

        if ( empty( $host ) || empty( $username ) ) {
            throw new RuntimeException( 'SFTP-Host und Benutzername dürfen nicht leer sein.' );
        }

        $this->sftp = new SFTP( $host, $port, 60 ); // 60s Timeout

        if ( ! $this->sftp->login( $username, $password ) ) {
            throw new RuntimeException(
                "SFTP-Login fehlgeschlagen für {$username}@{$host}:{$port}. Bitte Zugangsdaten prüfen."
            );
        }
    }

    // ── Upload ───────────────────────────────────────────────────────────────

    /**
     * Lädt eine lokale Datei auf die Storage Box hoch.
     *
     * @param string $local_path      Lokaler Dateipfad
     * @param string $remote_filename Dateiname auf dem Remote-Server
     * @return string                 Vollständiger Remote-Pfad
     * @throws RuntimeException
     */
    public function upload( string $local_path, string $remote_filename ): string {
        if ( ! file_exists( $local_path ) ) {
            throw new RuntimeException( "Lokale Datei nicht gefunden: {$local_path}" );
        }

        $remote_dir  = $this->get_remote_site_dir();
        $remote_path = $remote_dir . '/' . $remote_filename;

        $this->ensure_remote_dir( $remote_dir );

        $result = $this->sftp->put(
            $remote_path,
            $local_path,
            SFTP::SOURCE_LOCAL_FILE,
            -1,
            -1,
            static function ( $sent ) use ( $local_path ) {
                // Progress-Callback (für zukünftige Nutzung)
            }
        );

        if ( ! $result ) {
            throw new RuntimeException( "SFTP-Upload fehlgeschlagen: {$remote_path}" );
        }

        return $remote_path;
    }

    // ── Retention ────────────────────────────────────────────────────────────

    /**
     * Wendet die Aufbewahrungsregel an: Hält nur die neuesten $keep Dateien
     * die auf $prefix beginnen.
     *
     * @param string $prefix  z.B. 'db-backup-' oder 'files-backup-'
     * @param int    $keep    Anzahl zu behaltender Backups
     */
    public function apply_retention( string $prefix, int $keep ): void {
        if ( $keep <= 0 ) return;

        $files = $this->list_site_files( $prefix );
        sort( $files ); // Älteste zuerst (alphabetisch / Datum im Dateinamen)

        $to_delete = array_slice( $files, 0, max( 0, count( $files ) - $keep ) );

        foreach ( $to_delete as $filename ) {
            $this->delete_site_file( $filename );
        }
    }

    // ── Verbindungstest ──────────────────────────────────────────────────────

    /**
     * Testet die SFTP-Verbindung und gibt true oder eine Fehlermeldung zurück.
     *
     * @param array $settings
     * @return true|string
     */
    public static function test_connection( array $settings ): true|string {
        try {
            $instance = new self( $settings );
            // Versuche, das Remote-Verzeichnis zu lesen
            $dir = rtrim( $settings['sftp_path'] ?? '/backups', '/' );
            $instance->sftp->nlist( $dir );
            return true;
        } catch ( \Throwable $e ) {
            return $e->getMessage();
        }
    }

    // ── Private Hilfsmethoden ─────────────────────────────────────────────────

    private function get_remote_site_dir(): string {
        return $this->remote_base . '/' . $this->site_slug;
    }

    private function list_site_files( string $prefix = '' ): array {
        $dir   = $this->get_remote_site_dir();
        $files = $this->sftp->nlist( $dir );

        if ( ! is_array( $files ) ) return [];

        $files = array_filter( $files, static fn( $f ) => ! in_array( $f, [ '.', '..' ], true ) );

        if ( $prefix !== '' ) {
            $files = array_filter( $files, static fn( $f ) => str_starts_with( $f, $prefix ) );
        }

        return array_values( $files );
    }

    private function delete_site_file( string $filename ): void {
        $path = $this->get_remote_site_dir() . '/' . $filename;
        $this->sftp->delete( $path );
    }

    /**
     * Stellt sicher, dass ein Remote-Verzeichnis (inkl. aller Eltern) existiert.
     */
    private function ensure_remote_dir( string $path ): void {
        if ( $this->sftp->is_dir( $path ) ) return;

        $parts   = array_filter( explode( '/', $path ) );
        $current = '';

        foreach ( $parts as $part ) {
            $current .= '/' . $part;
            if ( ! $this->sftp->is_dir( $current ) ) {
                $this->sftp->mkdir( $current );
            }
        }
    }

    /**
     * Erstellt einen sicheren Site-Slug aus der Home-URL.
     * z.B. "meinekunde.at" → "meinekunde-at"
     */
    private function build_site_slug(): string {
        $host = parse_url( get_site_url(), PHP_URL_HOST ) ?? 'wordpress';
        return preg_replace( '/[^a-z0-9\-]/', '-', strtolower( $host ) );
    }
}
