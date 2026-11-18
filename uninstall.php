<?php
/**
 * Uninstall: remove every option Activity Share for BuddyPress created, plus all legacy 3.5.x keys.
 *
 * @package BPAS
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

require_once __DIR__ . '/includes/class-autoloader.php';
BPAS\Autoloader::register( 'BPAS', __DIR__ . '/includes' );

$bpas_cleanup = static function (): void {
	delete_option( 'bpas_settings' );
	delete_option( 'bpas_db_version' );
	BPAS\Migrations\Migration_3_6_0::delete_keys( BPAS\Migrations\Migration_3_6_0::LEGACY_KEYS );
	if ( ! file_exists( WP_PLUGIN_DIR . '/buddypress-activity-share-pro/buddypress-share.php' ) ) {
		BPAS\Migrations\Migration_3_6_0::delete_keys( BPAS\Migrations\Migration_3_6_0::PRO_ONLY_KEYS );
	}
	BPAS\Migrations\Migration_3_6_0::delete_legacy_transients();
	wp_clear_scheduled_hook( 'bp_share_weekly_cleanup' );
	wp_clear_scheduled_hook( 'bp_share_daily_cache_cleanup' );
};

if ( is_multisite() ) {
	foreach ( get_sites(
		array(
			'fields' => 'ids',
			'number' => 0,
		)
	) as $bpas_site_id ) {
		switch_to_blog( (int) $bpas_site_id );
		$bpas_cleanup();
		restore_current_blog();
	}
} else {
	$bpas_cleanup();
}
