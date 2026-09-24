<?php
/**
 * Share menu in member profile headers.
 *
 * @package BPAS
 */

namespace BPAS\Integrations;

use BPAS\Settings;
use BPAS\Share_Context;
use BPAS\Share_Menu;

defined( 'ABSPATH' ) || exit;

/**
 * "Share this profile".
 */
final class Member_Header {

	/**
	 * Hook registration.
	 */
	public static function init(): void {
		add_action( 'bp_member_header_actions', array( __CLASS__, 'render' ), 99 );
	}

	/**
	 * Print the menu for the displayed member.
	 */
	public static function render(): void {
		if ( ! Settings::get( 'show_on_profiles' ) ) {
			return;
		}
		$ctx = Share_Context::for_object( 'member', (int) bp_displayed_user_id() );
		if ( $ctx ) {
			echo Share_Menu::render( $ctx, array( 'class' => 'bpas-share--header' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- template escapes.
		}
	}
}
