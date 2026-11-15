<?php
/**
 * Share menu in public group headers.
 *
 * @package BPAS
 */

namespace BPAS\Integrations;

use BPAS\Settings;
use BPAS\Share_Context;
use BPAS\Share_Menu;

defined( 'ABSPATH' ) || exit;

/**
 * "Share this group" - public groups only.
 */
final class Group_Header {

	/**
	 * Hook registration.
	 */
	public static function init(): void {
		add_action( 'bp_group_header_actions', array( __CLASS__, 'render' ), 99 );
	}

	/**
	 * Print the menu for the current group.
	 */
	public static function render(): void {
		if ( ! Settings::get( 'show_on_groups' ) ) {
			return;
		}
		$group = groups_get_current_group();
		if ( empty( $group->id ) || 'public' !== $group->status ) {
			return;
		}
		$ctx = Share_Context::for_object( 'group', (int) $group->id );
		if ( $ctx ) {
			$args = array(
				'class' => 'bpas-share--header',
				'title' => __( 'Share this group', 'bp-activity-social-share' ),
			);
			echo Share_Menu::render( $ctx, $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- template escapes.
		}
	}
}
