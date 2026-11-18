<?php
/**
 * 3.5.x / Pro 2.x settings -> bpas_settings, then remove every legacy key.
 *
 * @package BPAS
 */

namespace BPAS\Migrations;

use BPAS\Settings;

defined( 'ABSPATH' ) || exit;

/**
 * Plan section 12.4 (Free-owned and shared keys).
 */
final class Migration_3_6_0 {

	/**
	 * Keys written by Free 3.5.x or shared with Pro 2.x.
	 */
	const LEGACY_KEYS = array(
		'bp_share_services',
		'bp_share_services_extra',
		'bp_share_services_enable',
		'bp_share_services_logout_enable',
		'bp_share_services_serialized',
		'bp_share_services_old',
		'bp_share_services_old_format',
		'bp_share_pro_services',
		'bp_share_flag',
		'bp_share_db_version',
		'bp_share_all_services_disable',
		'bpas_icon_color_settings',
		'bp_share_icon_custom_color',
		'bp_social_share_activation_date',
		'bp_social_share_no_bug',
		'bpas_onboarding_complete',
	);

	/**
	 * Keys only Pro 2.x wrote. Removed here when Pro is not installed; Pro 3.6.0 removes them otherwise.
	 */
	const PRO_ONLY_KEYS = array(
		'bp_reshare_settings',
		'bp_share_general_settings',
		'bp_share_plugin_settings',
		'bp_share_post_type_settings',
		'bp_share_plugin_version',
		'bp_share_install_date',
		'bp_share_last_activation',
		'bp_share_needs_upgrade_tasks',
		'bp_share_license_key',
		'bp_share_license_status',
		'bp_share_license_data',
		'bp_activity_share_plugin_license_key',
		'bp_activity_share_plugin_license_status',
		'bp_activity_share_plugin_license_data',
	);

	/**
	 * Legacy service names (any casing / label) -> 3.6.0 slug. Retired networks map to ''.
	 */
	const SLUG_MAP = array(
		'facebook'  => 'facebook',
		'twitter'   => 'x',
		'x'         => 'x',
		'x-twitter' => 'x',
		'linkedin'  => 'linkedin',
		'whatsapp'  => 'whatsapp',
		'telegram'  => 'telegram',
		'bluesky'   => 'bluesky',
		'reddit'    => 'reddit',
		'pinterest' => 'pinterest',
		'e-mail'    => 'email',
		'email'     => 'email',
		'copy-link' => 'copy',
		'copy link' => 'copy',
		'copylink'  => 'copy',
		'wordpress' => '',
		'pocket'    => '',
	);

	/**
	 * Run. Returns false to keep legacy data and retry later.
	 */
	public static function run(): bool {
		$services = self::read( 'bp_share_services' );

		if ( is_array( $services ) && ! empty( $services ) ) {
			$guest   = self::read( 'bp_share_services_logout_enable' );
			$changes = array(
				'networks'    => self::enabled_slugs( $services ),
				// 3.5.x showed Share to logged-in members only and never wrote this option, so a
				// missing value keeps it that way. Fresh installs get the new default from Settings.
				'guest_share' => null !== $guest && (bool) $guest,
			);
			$saved   = Settings::update( $changes );

			// Read back before deleting anything.
			$check = get_option( Settings::OPTION );
			if ( ! is_array( $check ) || $check['networks'] !== $saved['networks'] ) {
				return false;
			}
		}
		// No legacy services: fresh install, or an earlier request already migrated. Never overwrite.

		self::delete_keys( self::LEGACY_KEYS );
		if ( ! file_exists( WP_PLUGIN_DIR . '/buddypress-activity-share-pro/buddypress-share.php' ) ) {
			self::delete_keys( self::PRO_ONLY_KEYS );
		}
		wp_clear_scheduled_hook( 'bp_share_weekly_cleanup' );
		wp_clear_scheduled_hook( 'bp_share_daily_cache_cleanup' );
		self::delete_legacy_transients();

		return true;
	}

	/**
	 * Enabled slugs, in the stored order, from any legacy shape.
	 *
	 * Shapes seen in the wild: Name => Label, Name => array( 'chb_Name' => 0|1 ), lowercase duplicates.
	 * 3.5.x showed every listed service and switched one off by deleting its key; the chb_* flag was
	 * never read, so a listed service counts as on whatever its flag says (keeps what members saw).
	 *
	 * @param array $services Legacy value.
	 * @return string[]
	 */
	public static function enabled_slugs( array $services ): array {
		$slugs = array();
		foreach ( $services as $key => $value ) {
			$enabled = is_array( $value ) ? ! empty( $value ) : ( '' !== trim( (string) $value ) && '0' !== (string) $value );
			$slug    = self::SLUG_MAP[ strtolower( trim( (string) $key ) ) ] ?? '';
			if ( $enabled && '' !== $slug && ! in_array( $slug, $slugs, true ) ) {
				$slugs[] = $slug;
			}
		}
		return empty( $slugs ) ? Settings::defaults()['networks'] : $slugs;
	}

	/**
	 * Legacy value from the network store first, then the site store.
	 *
	 * @param string $key Option name.
	 * @return mixed|null Null when absent in both.
	 */
	private static function read( string $key ) {
		$value = get_site_option( $key, null );
		return null === $value ? get_option( $key, null ) : $value;
	}

	/**
	 * Delete keys from both stores.
	 *
	 * @param string[] $keys Option names.
	 */
	public static function delete_keys( array $keys ): void {
		foreach ( $keys as $key ) {
			delete_option( $key );
			delete_site_option( $key );
		}
	}

	/**
	 * One-time sweep of the legacy plugins' transients (both stores).
	 */
	public static function delete_legacy_transients(): void {
		global $wpdb;
		$prefixes = array( 'bp_share_', 'bpas_analytics', 'bpas_overview_stats', '_bpshare_is_new_install' );
		foreach ( $prefixes as $prefix ) {
			foreach ( array( '_transient_', '_transient_timeout_', '_site_transient_', '_site_transient_timeout_' ) as $type ) {
				$like = $wpdb->esc_like( $type . $prefix ) . '%';
				$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s", $like ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery -- one-time legacy cleanup.
				if ( is_multisite() ) {
					$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->sitemeta} WHERE meta_key LIKE %s", $like ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery -- one-time legacy cleanup.
				}
			}
		}
		wp_cache_delete( 'alloptions', 'options' );
		wp_cache_delete( 'notoptions', 'options' );
	}
}
