<?php
/**
 * Share menu renderer - one template for every surface.
 *
 * @package BPAS
 */

namespace BPAS;

defined( 'ABSPATH' ) || exit;

/**
 * Builds view data and loads templates/share-menu.php.
 */
final class Share_Menu {

	/**
	 * Render the menu.
	 *
	 * @param Share_Context $ctx  Context.
	 * @param array         $args Optional: label, position ('before'|'after'), class, title (accessible name and tooltip of the toggle).
	 */
	public static function render( Share_Context $ctx, array $args = array() ): string {
		if ( ! is_user_logged_in() && ! Settings::get( 'guest_share' ) ) {
			return '';
		}

		$args = (array) apply_filters(
			'bpas_share_menu_args',
			array_merge(
				array(
					'label'    => Settings::label(),
					'position' => Settings::get( 'position' ),
					'class'    => '',
					'title'    => '',
				),
				$args
			),
			$ctx
		);

		$external = Visibility::can_share_externally( $ctx );
		$items    = array();
		foreach ( Networks::enabled() as $slug => $network ) {
			// Private content: only the members-only copy link is offered.
			if ( ! $external && 'copy' !== $network['type'] ) {
				continue;
			}
			$items[] = array(
				'slug'   => $slug,
				'label'  => $network['label'],
				'type'   => $network['type'],
				'href'   => 'copy' === $network['type'] ? '' : Networks::share_href( $network, $ctx ),
				'icon'   => $network['icon'],
				'mobile' => $network['mobile'],
			);
		}

		// Extension rows (Pro: repost, send to a friend).
		ob_start();
		do_action( 'bpas_share_menu_items', $ctx, $external );
		$extra = trim( (string) ob_get_clean() );

		// Extras inside the toggle (Pro: count).
		ob_start();
		do_action( 'bpas_share_toggle_after', $ctx );
		$toggle_extra = trim( (string) ob_get_clean() );

		if ( empty( $items ) && '' === $extra ) {
			return '';
		}

		Assets::enqueue();

		// Copy link and the device share sheet share what every network shares. Private content keeps
		// its plain permalink: it is for members only, and the short link refuses private items.
		$view = array(
			'ctx'          => $ctx,
			'url'          => $external ? Networks::share_url( Networks::all()['copy'] ?? array( 'type' => 'copy' ), $ctx ) : $ctx->url,
			'label'        => (string) $args['label'],
			'position'     => 'before' === $args['position'] ? 'before' : 'after',
			'colors'       => Settings::get( 'colors' ),
			'class'        => (string) $args['class'],
			'title'        => (string) $args['title'],
			'items'        => $items,
			'extra'        => $extra,
			'toggle_extra' => $toggle_extra,
			'native'       => $external,
		);

		ob_start();
		self::load_template( $view );
		return (string) ob_get_clean();
	}

	/**
	 * Load the (theme-overridable) template.
	 *
	 * @param array $view View data (no DB access in the template).
	 */
	private static function load_template( array $view ): void { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found -- used by the included template.
		$template = locate_template( 'bp-activity-social-share/share-menu.php' );
		if ( ! $template ) {
			$template = BPAS_DIR . 'templates/share-menu.php';
		}
		include $template;
	}
}
