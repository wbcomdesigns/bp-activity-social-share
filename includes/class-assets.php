<?php
/**
 * Front-end assets.
 *
 * @package BPAS
 */

namespace BPAS;

defined( 'ABSPATH' ) || exit;

/**
 * Registers tokens + share CSS/JS; enqueues on BuddyPress pages or when a menu renders.
 */
final class Assets {

	/**
	 * Hook registration.
	 */
	public static function init(): void {
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'register' ), 5 );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'maybe_enqueue' ) );
	}

	/**
	 * Register handles (Pro depends on `bpas-tokens` and `bpas-share`).
	 */
	public static function register(): void {
		$min = self::suffix();

		wp_register_style( 'bpas-tokens', BPAS_URL . "assets/css/bpas-tokens{$min}.css", array(), BPAS_VERSION );
		wp_style_add_data( 'bpas-tokens', 'rtl', 'replace' );
		wp_style_add_data( 'bpas-tokens', 'suffix', $min );

		wp_register_style( 'bpas-share', BPAS_URL . "assets/css/bpas-share{$min}.css", array( 'bpas-tokens' ), BPAS_VERSION );
		wp_style_add_data( 'bpas-share', 'rtl', 'replace' );
		wp_style_add_data( 'bpas-share', 'suffix', $min );
		wp_add_inline_style( 'bpas-share', self::brand_css() );

		wp_register_script(
			'bpas-share',
			BPAS_URL . "assets/js/bpas-share{$min}.js",
			array(),
			BPAS_VERSION,
			array(
				'strategy'  => 'defer',
				'in_footer' => true,
			)
		);
		wp_add_inline_script(
			'bpas-share',
			'window.bpasShare = ' . wp_json_encode(
				array(
					'i18n' => array(
						'copied'     => __( 'Link copied', 'bp-activity-social-share' ),
						'copyFailed' => __( 'Could not copy the link. Press and hold to copy it instead.', 'bp-activity-social-share' ),
					),
				)
			) . ';',
			'before'
		);
	}

	/**
	 * Enqueue on BuddyPress screens (activity loads by AJAX there).
	 */
	public static function maybe_enqueue(): void {
		$should = function_exists( 'is_buddypress' ) && is_buddypress();
		if ( (bool) apply_filters( 'bpas_enqueue_assets', $should ) ) {
			self::enqueue();
		}
	}

	/**
	 * Enqueue now (safe to call during render; styles then print in the footer).
	 */
	public static function enqueue(): void {
		if ( ! wp_style_is( 'bpas-share', 'registered' ) ) {
			self::register();
		}
		wp_enqueue_style( 'bpas-share' );
		wp_enqueue_script( 'bpas-share' );
	}

	/**
	 * Per-network brand colours as CSS variables.
	 */
	private static function brand_css(): string {
		$vars = '';
		foreach ( Networks::all() as $slug => $network ) {
			$color = sanitize_hex_color( (string) ( $network['color'] ?? '' ) );
			if ( $color ) {
				$vars .= sprintf( '--bpas-brand-%s:%s;', sanitize_html_class( $slug ), $color );
			}
		}
		return '' === $vars ? '' : ':root{' . $vars . '}';
	}

	/**
	 * '.min' unless SCRIPT_DEBUG.
	 */
	private static function suffix(): string {
		return ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
	}
}
