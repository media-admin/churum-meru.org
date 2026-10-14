<?php
/**
 * Plugin Name: ML Hero-Feld-Migration (einmalig, danach löschen)
 */
add_action( 'admin_init', function () {
	if ( ! isset( $_GET['ml_hero_migrate'] ) || ! current_user_can( 'manage_options' ) ) {
		return;
	}
	global $wpdb;
	$run = ( 'run' === $_GET['ml_hero_migrate'] );
	if ( $run ) {
		check_admin_referer( 'ml_hero_migrate' );
	}
	$out = [];

	// 1) page/post/event: image_desktop|mobile -> hero_image_desktop|mobile
	foreach ( [ 'image_desktop' => 'hero_image_desktop', 'image_mobile' => 'hero_image_mobile' ] as $old => $new ) {
		$ids = $wpdb->get_col( $wpdb->prepare(
			"SELECT pm.post_id FROM {$wpdb->postmeta} pm
			 JOIN {$wpdb->posts} p ON p.ID = pm.post_id
			 WHERE pm.meta_key = %s AND pm.meta_value = %s
			   AND p.post_type IN ('page','post','event')",
			'_' . $old, 'field_hero_' . $old
		) );
		$todo = 0;
		$skip = 0;
		foreach ( $ids as $pid ) {
			$has_new = (int) $wpdb->get_var( $wpdb->prepare(
				"SELECT COUNT(*) FROM {$wpdb->postmeta} WHERE post_id = %d AND meta_key IN (%s,%s)",
				$pid, $new, '_' . $new
			) );
			if ( $has_new ) { $skip++; continue; }
			$todo++;
			if ( $run ) {
				$wpdb->update( $wpdb->postmeta, [ 'meta_key' => $new ],        [ 'post_id' => $pid, 'meta_key' => $old ] );
				$wpdb->update( $wpdb->postmeta, [ 'meta_key' => '_' . $new ],  [ 'post_id' => $pid, 'meta_key' => '_' . $old ] );
			}
		}
		$out[] = sprintf( '%s -> %s: %d %s, %d übersprungen (Ziel existiert)', $old, $new, $todo, $run ? 'umbenannt' : 'zu migrieren', $skip );
	}

	// 2) hero_slide: Referenzen auf die neuen Feldschlüssel
	$refs = [
		'field_hero_image_desktop'   => 'field_slide_image_desktop',
		'field_hero_image_mobile'    => 'field_slide_image_mobile',
		'field_hero_overlay_opacity' => 'field_slide_overlay_opacity',
	];
	$rows = $wpdb->get_results(
		"SELECT pm.meta_id, pm.meta_value FROM {$wpdb->postmeta} pm
		 JOIN {$wpdb->posts} p ON p.ID = pm.post_id
		 WHERE p.post_type = 'hero_slide'
		   AND pm.meta_key IN ('_image_desktop','_image_mobile','_overlay_opacity')"
	);
	$n = 0;
	foreach ( $rows as $r ) {
		if ( isset( $refs[ $r->meta_value ] ) ) {
			$n++;
			if ( $run ) {
				$wpdb->update( $wpdb->postmeta, [ 'meta_value' => $refs[ $r->meta_value ] ], [ 'meta_id' => $r->meta_id ] );
			}
		}
	}
	$out[] = sprintf( 'hero_slide-Referenzen: %d %s', $n, $run ? 'umgestellt' : 'zu migrieren' );

	if ( $run ) {
		wp_cache_flush();
	}
	$link = wp_nonce_url( admin_url( 'index.php?ml_hero_migrate=run' ), 'ml_hero_migrate' );
	wp_die(
		'<pre>' . esc_html( implode( "\n", $out ) ) . '</pre>' .
		( $run ? '<p><b>Fertig.</b> Datei jetzt löschen.</p>' : '<p><a href="' . esc_url( $link ) . '">Jetzt ausführen</a></p>' ),
		'Hero-Migration',
		[ 'response' => 200 ]
	);
} );