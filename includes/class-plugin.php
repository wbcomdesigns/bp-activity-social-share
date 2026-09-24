<?php
/**
 * Bootstrap: guards, upgrade, module wiring.
 *
 * @package BPAS
 */

namespace BPAS;

defined( 'ABSPATH' ) || exit;

/**
 * Boots the plugin once BuddyPress is available.
 */
final class Plugin {

	const PRO_FILE = 'buddypress-activity-share-pro/buddypress-share.php';

	/**
	 * Runs on plugins_loaded.
	 */
	public static function boot(): void {
		if ( ! function_exists( 'buddypress' ) ) {
			return; // `Requires Plugins: buddypress` normally prevents this.
		}

		if ( class_exists( 'Youzify' ) ) {
			self::notice( static fn() => __( 'Activity Share for BuddyPress does not support Youzify and is inactive while Youzify is active.', 'bp-activity-social-share' ) );
			return;
		}

		if ( self::legacy_pro_active() ) {
			// Pro 2.x ships its own copy of everything. Stay out of its way and keep
			// the legacy settings it reads until Pro is updated.
			self::notice( static fn() => __( 'Activity Share Pro needs to be updated to version 3.6.0 or newer. Until then, sharing is provided by your current Pro version.', 'bp-activity-social-share' ) );
			return;
		}

		Upgrade::maybe_run();

		add_action( 'init', array( Networks::class, 'init' ), 5 );
		add_action( 'bp_init', array( __CLASS__, 'load_modules' ) );
		add_action( 'rest_api_init', array( Api\Settings_Controller::class, 'register' ) );

		if ( is_admin() ) {
			Admin\Admin::init();
		}
	}

	/**
	 * Wire front-end modules once BuddyPress components are known.
	 */
	public static function load_modules(): void {
		Assets::init();
		Open_Graph::init();

		if ( bp_is_active( 'activity' ) ) {
			Integrations\Activity::init();
		}
		Integrations\Member_Header::init();
		if ( bp_is_active( 'groups' ) ) {
			Integrations\Group_Header::init();
		}
	}

	/**
	 * True when Activity Share Pro older than 3.6.0 is active.
	 */
	public static function legacy_pro_active(): bool {
		static $legacy = null;
		if ( null !== $legacy ) {
			return $legacy;
		}

		$active = (array) get_option( 'active_plugins', array() );
		if ( is_multisite() ) {
			$active = array_merge( $active, array_keys( (array) get_site_option( 'active_sitewide_plugins', array() ) ) );
		}
		if ( ! in_array( self::PRO_FILE, $active, true ) ) {
			$legacy = false;
			return $legacy;
		}

		$file = WP_PLUGIN_DIR . '/' . self::PRO_FILE;
		$data = is_readable( $file ) ? get_file_data( $file, array( 'Version' => 'Version' ) ) : array( 'Version' => '0' );

		$legacy = version_compare( (string) $data['Version'], '3.6.0', '<' );
		return $legacy;
	}

	/**
	 * Show a one-line notice to administrators. The message is built inside the
	 * callback so translations are not loaded before init.
	 *
	 * @param callable $message Returns the plain-text message.
	 */
	private static function notice( callable $message ): void {
		add_action(
			'admin_notices',
			static function () use ( $message ) {
				if ( current_user_can( 'activate_plugins' ) ) {
					printf( '<div class="notice notice-warning"><p>%s</p></div>', esc_html( (string) $message() ) );
				}
			}
		);
	}
}
