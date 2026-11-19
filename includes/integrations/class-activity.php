<?php
/**
 * Share menu on activity entries.
 *
 * @package BPAS
 */

namespace BPAS\Integrations;

use BPAS\Settings;
use BPAS\Share_Context;
use BPAS\Share_Menu;

defined( 'ABSPATH' ) || exit;

/**
 * Renders the menu inside the activity action bar (Nouveau, Legacy, BuddyBoss).
 */
final class Activity {

	/**
	 * Hook registration. The priority follows the owner's "Button position": other plugins add
	 * their buttons at the default 10, so Share prints first (5) or last (999) in the bar.
	 */
	public static function init(): void {
		add_action( 'bp_activity_entry_meta', array( __CLASS__, 'render' ), 'before' === Settings::get( 'position' ) ? 5 : 999 );
	}

	/**
	 * Print the menu for the current loop activity (uses the loaded row: no extra query).
	 */
	public static function render(): void {
		if ( is_embed() || is_feed() ) {
			return;
		}
		global $activities_template;
		$activity = $activities_template->activity ?? null;
		if ( ! is_object( $activity ) ) {
			return;
		}
		$ctx = Share_Context::from_activity( $activity );
		if ( $ctx ) {
			echo Share_Menu::render( $ctx, array( 'class' => 'bpas-share--activity' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- template escapes.
		}
	}
}
