<?php
/**
 * REST: bpas/v1/settings - the admin screen uses this.
 *
 * @package BPAS
 */

namespace BPAS\Api;

use BPAS\Networks;
use BPAS\Settings;

defined( 'ABSPATH' ) || exit;

/**
 * GET / PATCH settings (manage_options).
 */
final class Settings_Controller {

	/**
	 * Register routes.
	 */
	public static function register(): void {
		register_rest_route(
			bpas_rest_namespace(),
			'/settings',
			array(
				array(
					'methods'             => 'GET',
					'callback'            => array( __CLASS__, 'get' ),
					'permission_callback' => array( __CLASS__, 'can_manage' ),
				),
				array(
					'methods'             => 'PATCH',
					'callback'            => array( __CLASS__, 'patch' ),
					'permission_callback' => array( __CLASS__, 'can_manage' ),
					'args'                => array(
						'networks'         => array(
							'type'  => 'array',
							'items' => array( 'type' => 'string' ),
						),
						'guest_share'      => array( 'type' => 'boolean' ),
						'show_on_profiles' => array( 'type' => 'boolean' ),
						'show_on_groups'   => array( 'type' => 'boolean' ),
						'label'            => array( 'type' => 'string' ),
						'position'         => array(
							'type' => 'string',
							'enum' => array( 'before', 'after' ),
						),
						'colors'           => array(
							'type' => 'string',
							'enum' => array( 'brand', 'theme' ),
						),
					),
				),
			)
		);
	}

	/**
	 * Permission: administrators only, with actionable errors for apps.
	 *
	 * @return true|\WP_Error
	 */
	public static function can_manage() {
		if ( ! is_user_logged_in() ) {
			return new \WP_Error( 'bpas_authentication_required', __( 'Authentication required.', 'bp-activity-social-share' ), array( 'status' => 401 ) );
		}
		if ( ! current_user_can( 'manage_options' ) ) {
			return new \WP_Error( 'bpas_forbidden', __( 'You are not allowed to change share settings.', 'bp-activity-social-share' ), array( 'status' => 403 ) );
		}
		return true;
	}

	/**
	 * Current settings + available networks.
	 */
	public static function get(): \WP_REST_Response {
		return rest_ensure_response( self::payload( Settings::all() ) );
	}

	/**
	 * Partial update.
	 *
	 * @param \WP_REST_Request $request Request.
	 */
	public static function patch( \WP_REST_Request $request ): \WP_REST_Response {
		$keys    = array( 'networks', 'guest_share', 'show_on_profiles', 'show_on_groups', 'label', 'position', 'colors' );
		$changes = array_intersect_key( (array) $request->get_json_params() + $request->get_body_params(), array_flip( $keys ) );
		return rest_ensure_response( self::payload( Settings::update( $changes ) ) );
	}

	/**
	 * Response shape.
	 *
	 * @param array $settings Settings.
	 */
	private static function payload( array $settings ): array {
		$networks = array();
		foreach ( Networks::all() as $slug => $network ) {
			$networks[] = array(
				'slug'  => $slug,
				'label' => $network['label'],
			);
		}
		return array(
			'settings' => $settings,
			'networks' => $networks,
		);
	}
}
