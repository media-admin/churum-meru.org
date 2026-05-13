<?php
defined( 'ABSPATH' ) || exit;

/**
 * MLBKP_Database_Backup
 *
 * Erstellt einen SQL-Dump der WordPress-Datenbank.
 * Verwendet mysqldump wenn verfügbar, fällt sonst auf reines PHP zurück.
 */
class MLBKP_Database_Backup {

    private string $temp_dir;

    public function __construct( string $temp_dir ) {
        $this->temp_dir = $temp_dir;
    }

    /**
     * Erstellt einen SQL-Dump und gibt den Pfad zur .sql.gz Datei zurück.
     *
     * @return array{path: string, size: int, method: string}
     * @throws RuntimeException
     */
    public function create(): array {
        $filename  = 'db-backup-' . gmdate( 'Y-m-d_H-i-s' ) . '.sql';
        $filepath  = $this->temp_dir . $filename;
        $gzip_path = $filepath . '.gz';

        // mysqldump versuchen
        if ( $this->is_mysqldump_available() ) {
            $this->dump_via_mysqldump( $filepath );
            $method = 'mysqldump';
        } else {
            $this->dump_via_php( $filepath );
            $method = 'php';
        }

        // Komprimieren
        $this->gzip_file( $filepath, $gzip_path );
        @unlink( $filepath );

        if ( ! file_exists( $gzip_path ) ) {
            throw new RuntimeException( 'DB-Dump-Datei konnte nicht erstellt werden.' );
        }

        return [
            'path'     => $gzip_path,
            'filename' => basename( $gzip_path ),
            'size'     => filesize( $gzip_path ),
            'method'   => $method,
        ];
    }

    // ── mysqldump ─────────────────────────────────────────────────────────────

    private function is_mysqldump_available(): bool {
        if ( ! function_exists( 'exec' ) && ! function_exists( 'shell_exec' ) ) {
            return false;
        }

        $output = [];
        @exec( 'mysqldump --version 2>&1', $output, $code );
        return $code === 0;
    }

    /**
     * @throws RuntimeException
     */
    private function dump_via_mysqldump( string $filepath ): void {
        global $wpdb;

        $host     = DB_HOST;
        $dbname   = DB_NAME;
        $username = DB_USER;
        $password = DB_PASSWORD;

        // Host und Port trennen
        $port = '3306';
        if ( str_contains( $host, ':' ) ) {
            [ $host, $port ] = explode( ':', $host, 2 );
        }

        $cmd = sprintf(
            'mysqldump --host=%s --port=%s --user=%s --password=%s --single-transaction --routines --triggers --events --set-gtid-purged=OFF %s > %s 2>&1',
            escapeshellarg( $host ),
            escapeshellarg( $port ),
            escapeshellarg( $username ),
            escapeshellarg( $password ),
            escapeshellarg( $dbname ),
            escapeshellarg( $filepath )
        );

        exec( $cmd, $output, $return_code );

        if ( $return_code !== 0 ) {
            throw new RuntimeException(
                'mysqldump fehlgeschlagen (Exit-Code ' . $return_code . '): ' . implode( ' ', $output )
            );
        }

        if ( ! file_exists( $filepath ) || filesize( $filepath ) === 0 ) {
            throw new RuntimeException( 'mysqldump hat eine leere Datei erzeugt.' );
        }
    }

    // ── PHP-Fallback ──────────────────────────────────────────────────────────

    /**
     * Reiner PHP-Datenbankdump ohne externe Abhängigkeiten.
     *
     * @throws RuntimeException
     */
    private function dump_via_php( string $filepath ): void {
        global $wpdb;

        $handle = @fopen( $filepath, 'w' );
        if ( ! $handle ) {
            throw new RuntimeException( "Konnte Dump-Datei nicht öffnen: {$filepath}" );
        }

        $this->write_header( $handle );

        $tables = $wpdb->get_col( 'SHOW TABLES' );

        foreach ( $tables as $table ) {
            $this->dump_table( $handle, $table );
        }

        $this->write_footer( $handle );
        fclose( $handle );
    }

    private function write_header( $handle ): void {
        $header = "-- ============================================================\n";
        $header .= "-- Media Lab Backup — WordPress Database Dump\n";
        $header .= "-- Erstellt: " . gmdate( 'Y-m-d H:i:s' ) . " UTC\n";
        $header .= "-- Datenbank: " . DB_NAME . "\n";
        $header .= "-- WordPress: " . get_bloginfo( 'version' ) . "\n";
        $header .= "-- ============================================================\n\n";
        $header .= "SET FOREIGN_KEY_CHECKS=0;\n";
        $header .= "SET SQL_MODE='NO_AUTO_VALUE_ON_ZERO';\n";
        $header .= "SET NAMES utf8mb4;\n\n";
        fwrite( $handle, $header );
    }

    private function write_footer( $handle ): void {
        fwrite( $handle, "\nSET FOREIGN_KEY_CHECKS=1;\n" );
        fwrite( $handle, "-- Dump abgeschlossen: " . gmdate( 'Y-m-d H:i:s' ) . " UTC\n" );
    }

    private function dump_table( $handle, string $table ): void {
        global $wpdb;

        fwrite( $handle, "\n-- ─────────────────────────────────────────────\n" );
        fwrite( $handle, "-- Tabelle: `{$table}`\n" );
        fwrite( $handle, "-- ─────────────────────────────────────────────\n\n" );
        fwrite( $handle, "DROP TABLE IF EXISTS `{$table}`;\n" );

        // CREATE TABLE
        $create = $wpdb->get_row( "SHOW CREATE TABLE `{$table}`", ARRAY_N );
        if ( $create ) {
            fwrite( $handle, $create[1] . ";\n\n" );
        }

        // Daten in Chunks (Speicherschonend)
        $chunk_size = 500;
        $offset     = 0;

        do {
            $rows = $wpdb->get_results(
                $wpdb->prepare( "SELECT * FROM `{$table}` LIMIT %d OFFSET %d", $chunk_size, $offset ),
                ARRAY_N
            );

            if ( empty( $rows ) ) break;

            $columns_raw = $wpdb->get_col( "SHOW COLUMNS FROM `{$table}`" );
            $columns     = '`' . implode( '`, `', $columns_raw ) . '`';

            $values_list = [];
            foreach ( $rows as $row ) {
                $values = array_map( static function ( $value ) {
                    if ( $value === null ) return 'NULL';
                    return "'" . addslashes( $value ) . "'";
                }, $row );
                $values_list[] = '(' . implode( ', ', $values ) . ')';
            }

            fwrite( $handle,
                "INSERT INTO `{$table}` ({$columns}) VALUES\n" .
                implode( ",\n", $values_list ) . ";\n"
            );

            $offset += $chunk_size;
        } while ( count( $rows ) === $chunk_size );

        fwrite( $handle, "\n" );
    }

    // ── GZIP ─────────────────────────────────────────────────────────────────

    private function gzip_file( string $source, string $destination ): void {
        if ( ! file_exists( $source ) ) {
            throw new RuntimeException( "Quelldatei für GZIP nicht gefunden: {$source}" );
        }

        $in  = fopen( $source, 'rb' );
        $out = gzopen( $destination, 'wb9' ); // Level 9 = beste Kompression

        if ( ! $in || ! $out ) {
            throw new RuntimeException( 'Konnte GZIP-Datei nicht erstellen.' );
        }

        while ( ! feof( $in ) ) {
            gzwrite( $out, fread( $in, 524288 ) ); // 512KB Chunks
        }

        fclose( $in );
        gzclose( $out );
    }
}
