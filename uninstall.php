<?php
/**
 * Fired when the plugin is uninstalled.
 * When populating this file, consider the following flow
 * of control:
 *
 * - This method should be static
 * - Check if the $_REQUEST content actually is the plugin name
 * - Run an admin referrer check to make sure it goes through authentication
 * - Verify the output of $_GET makes sense
 * - Repeat with other user roles. Best directly by using the links/query string parameters.
 * - Repeat things for multisite. Once for a single site in the network, once sitewide.
 * This file may be updated more in future version of the Boilerplate; however, this is the
 * general skeleton and outline for how the file should work.
 * For more information, see the following discussion:
 * https://github.com/tommcfarlin/WordPress-Plugin-Boilerplate/pull/123#issuecomment-28541913
 *
 * @link       http://wbcomdesigns.com
 * @since      1.0.0
 *
 * @package    Buddypress_Share
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

/**
 * Remove every option the plugin writes, from both stores.
 *
 * The plugin persists settings in the network store (update_site_option:
 * bp_share_services, bp_share_services_extra, bp_share_flag,
 * bp_share_all_services_disable, bp_share_db_version) AND in the per-site
 * store (update_option: bpas_icon_color_settings, seeded in both since
 * 3.5.6). Legacy releases also set bp_social_share_activation_date /
 * bp_social_share_no_bug. Delete all keys from both stores, and on
 * multisite from every site, so uninstall leaves nothing behind.
 */
$bp_share_option_keys = array(
	'bp_share_services',
	'bp_share_services_extra',
	'bp_share_flag',
	'bp_share_all_services_disable',
	'bp_share_db_version',
	'bpas_icon_color_settings',
	'bp_social_share_activation_date',
	'bp_social_share_no_bug',
);

// Network-level store (get_site_option falls back to options on single site).
foreach ( $bp_share_option_keys as $bp_share_option_key ) {
	delete_site_option( $bp_share_option_key );
}

// Per-site store.
if ( is_multisite() ) {
	$bp_share_site_ids = get_sites(
		array(
			'fields' => 'ids',
			'number' => 0,
		)
	);
	foreach ( $bp_share_site_ids as $bp_share_site_id ) {
		switch_to_blog( $bp_share_site_id );
		foreach ( $bp_share_option_keys as $bp_share_option_key ) {
			delete_option( $bp_share_option_key );
		}
		restore_current_blog();
	}
} else {
	foreach ( $bp_share_option_keys as $bp_share_option_key ) {
		delete_option( $bp_share_option_key );
	}
}
