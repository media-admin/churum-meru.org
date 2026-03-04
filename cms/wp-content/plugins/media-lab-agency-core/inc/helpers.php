<?php
/**
 * Helper Functions
 * 
 * @package MediaLab_Core
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Get plugin version
 */
function medialab_core_version() {
    return MEDIALAB_CORE_VERSION;
}

/**
 * Check if Media Lab Core is active
 * Useful for theme/plugin compatibility checks
 */
function is_medialab_core_active() {
    return true;
}

// =============================================================================
// RATE LIMITING (F-03)
// Schützt öffentliche AJAX-Endpunkte vor Abuse / DDoS.
// Transient-basiert – kein externer Service nötig.
// =============================================================================

if ( ! function_exists( 'medialab_check_rate_limit' ) ) {
    /**
     * Rate-Limiting für öffentliche AJAX-Endpunkte.
     *
     * @param string $action  Eindeutiger Key (z.B. 'search', 'filter', 'load_more')
     * @param int    $max     Max. Anfragen pro Zeitfenster (default: 30)
     * @param int    $window  Zeitfenster in Sekunden (default: 60)
     * @return bool  true = erlaubt, false = blockiert
     */
    function medialab_check_rate_limit( string $action, int $max = 30, int $window = 60 ): bool {
        $ip  = preg_replace( '/[^0-9a-f.:]/i', '', $_SERVER['REMOTE_ADDR'] ?? '' );
        $key = 'rl_' . md5( $action . $ip );

        $hits = (int) get_transient( $key );
        if ( $hits >= $max ) {
            return false;
        }
        set_transient( $key, $hits + 1, $window );
        return true;
    }
}
