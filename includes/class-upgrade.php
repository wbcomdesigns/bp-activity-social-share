<?php
/**
 * Versioned upgrades - run on every request until the stored version matches.
 *
 * @package BPAS
 */

namespace BPAS;

defined( 'ABSPATH' ) || exit;

/**
 * Never tied to the activation hook: updates from wp.org do not fire it.
 */
final class Upgrade {

	const OPTION = 'bpas_db_version';

	/**
	 * Schedule the check (init: translations and the network registry are ready).
	 */
	public static function maybe_run(): void {
		add_action( 'init', array( __CLASS__, 'run' ), 20 );
	}

	/**
	 * Run pending migrations.
	 */
	public static function run(): void {
		$stored = (string) get_option( self::OPTION, '0' );
		if ( version_compare( $stored, BPAS_DB_VERSION, '>=' ) ) {
			return;
		}

		// Old Pro still reads the legacy keys: migrate only once it is updated or gone.
		if ( Plugin::legacy_pro_active() ) {
			return;
		}

		if ( version_compare( $stored, '3.6.0', '<' ) && ! Migrations\Migration_3_6_0::run() ) {
			return; // Keep legacy data and retry on the next request.
		}

		update_option( self::OPTION, BPAS_DB_VERSION, true );
	}
}
