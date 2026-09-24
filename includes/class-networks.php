<?php
/**
 * Share network registry.
 *
 * @package BPAS
 */

namespace BPAS;

defined( 'ABSPATH' ) || exit;

/**
 * Built-in networks + developer-registered ones.
 */
final class Networks {

	/**
	 * Registered networks keyed by slug.
	 *
	 * @var array<string,array>
	 */
	private static array $networks = array();

	/**
	 * Whether the registry has been built.
	 *
	 * @var bool
	 */
	private static bool $ready = false;

	/**
	 * Build the registry (runs on init).
	 */
	public static function init(): void {
		if ( self::$ready ) {
			return;
		}
		self::$ready = true;

		foreach ( self::builtins() as $slug => $args ) {
			self::$networks[ $slug ] = $args;
		}

		/**
		 * Register extra networks with bpas_register_network().
		 */
		do_action( 'bpas_register_networks' );
	}

	/**
	 * Every registered network, filterable.
	 *
	 * @return array<string,array>
	 */
	public static function all(): array {
		self::init();
		return (array) apply_filters( 'bpas_networks', self::$networks );
	}

	/**
	 * Enabled networks in the owner's order.
	 *
	 * @return array<string,array>
	 */
	public static function enabled(): array {
		$all     = self::all();
		$enabled = array();
		foreach ( (array) Settings::get( 'networks', array() ) as $slug ) {
			if ( isset( $all[ $slug ] ) ) {
				$enabled[ $slug ] = $all[ $slug ];
			}
		}
		return $enabled;
	}

	/**
	 * Register a network.
	 *
	 * @param string $slug Slug.
	 * @param array  $args Arguments.
	 * @return true|\WP_Error
	 */
	public static function register( string $slug, array $args ) {
		$slug = sanitize_key( $slug );
		if ( '' === $slug || isset( self::$networks[ $slug ] ) ) {
			return new \WP_Error( 'bpas_network_exists', __( 'A share network with this slug already exists.', 'bp-activity-social-share' ) );
		}

		$url = (string) ( $args['share_url'] ?? '' );
		if ( 0 !== strpos( $url, 'https://' ) ) {
			return new \WP_Error( 'bpas_network_url', __( 'Share URLs must start with https://.', 'bp-activity-social-share' ) );
		}

		$icon = '';
		if ( ! empty( $args['icon'] ) ) {
			$icon = Icons::sanitize_svg( (string) $args['icon'] );
		} elseif ( ! empty( $args['icon_url'] ) ) {
			$icon = sprintf( '<img src="%s" alt="" width="20" height="20" />', esc_url( (string) $args['icon_url'] ) );
		}

		self::$networks[ $slug ] = array(
			'label'     => sanitize_text_field( (string) ( $args['label'] ?? $slug ) ),
			'type'      => 'link',
			'share_url' => $url,
			'icon'      => $icon,
			'color'     => (string) sanitize_hex_color( (string) ( $args['color'] ?? '' ) ),
			'mobile'    => (bool) ( $args['mobile'] ?? true ),
		);
		return true;
	}

	/**
	 * Remove a network.
	 *
	 * @param string $slug Slug.
	 */
	public static function unregister( string $slug ): bool {
		self::init();
		if ( ! isset( self::$networks[ $slug ] ) ) {
			return false;
		}
		unset( self::$networks[ $slug ] );
		return true;
	}

	/**
	 * Final href for a network and context. Placeholders are always encoded here.
	 *
	 * @param array         $network Network definition.
	 * @param Share_Context $ctx     Context.
	 */
	public static function share_href( array $network, Share_Context $ctx ): string {
		$url     = (string) apply_filters( 'bpas_share_url', $ctx->url, $network, $ctx );
		$replace = array(
			'{url}'   => rawurlencode( $url ),
			'{title}' => rawurlencode( $ctx->title ),
			'{text}'  => rawurlencode( $ctx->text ),
			'{image}' => rawurlencode( $ctx->image ),
		);
		return strtr( (string) $network['share_url'], $replace );
	}

	/**
	 * Built-in networks.
	 */
	private static function builtins(): array {
		$brand = static fn( string $name ): string => Icons::get( $name, 'brand' );
		$ui    = static fn( string $name ): string => Icons::get( $name, 'ui' );

		return array(
			'facebook'  => array(
				'label'     => __( 'Facebook', 'bp-activity-social-share' ),
				'type'      => 'link',
				'share_url' => 'https://www.facebook.com/sharer/sharer.php?u={url}',
				'icon'      => $brand( 'facebook' ),
				'color'     => '#1877f2',
				'mobile'    => true,
			),
			'x'         => array(
				'label'     => __( 'X', 'bp-activity-social-share' ),
				'type'      => 'link',
				'share_url' => 'https://x.com/intent/post?url={url}&text={title}',
				'icon'      => $brand( 'x' ),
				'color'     => '',
				'mobile'    => true,
			),
			'linkedin'  => array(
				'label'     => __( 'LinkedIn', 'bp-activity-social-share' ),
				'type'      => 'link',
				'share_url' => 'https://www.linkedin.com/sharing/share-offsite/?url={url}',
				'icon'      => $brand( 'linkedin' ),
				'color'     => '#0a66c2',
				'mobile'    => true,
			),
			'whatsapp'  => array(
				'label'     => __( 'WhatsApp', 'bp-activity-social-share' ),
				'type'      => 'link',
				'share_url' => 'https://api.whatsapp.com/send?text={title}%20{url}',
				'icon'      => $brand( 'whatsapp' ),
				'color'     => '#25d366',
				'mobile'    => true,
			),
			'telegram'  => array(
				'label'     => __( 'Telegram', 'bp-activity-social-share' ),
				'type'      => 'link',
				'share_url' => 'https://t.me/share/url?url={url}&text={title}',
				'icon'      => $brand( 'telegram' ),
				'color'     => '#26a5e4',
				'mobile'    => true,
			),
			'bluesky'   => array(
				'label'     => __( 'Bluesky', 'bp-activity-social-share' ),
				'type'      => 'link',
				'share_url' => 'https://bsky.app/intent/compose?text={title}%20{url}',
				'icon'      => $brand( 'bluesky' ),
				'color'     => '#1185fe',
				'mobile'    => true,
			),
			'reddit'    => array(
				'label'     => __( 'Reddit', 'bp-activity-social-share' ),
				'type'      => 'link',
				'share_url' => 'https://www.reddit.com/submit?url={url}&title={title}',
				'icon'      => $brand( 'reddit' ),
				'color'     => '#ff4500',
				'mobile'    => true,
			),
			'pinterest' => array(
				'label'     => __( 'Pinterest', 'bp-activity-social-share' ),
				'type'      => 'link',
				'share_url' => 'https://pinterest.com/pin/create/button/?url={url}&media={image}&description={title}',
				'icon'      => $brand( 'pinterest' ),
				'color'     => '#e60023',
				'mobile'    => true,
			),
			'email'     => array(
				'label'     => __( 'Email', 'bp-activity-social-share' ),
				'type'      => 'mailto',
				'share_url' => 'mailto:?subject={title}&body={url}',
				'icon'      => $ui( 'mail' ),
				'color'     => '',
				'mobile'    => true,
			),
			'copy'      => array(
				'label'     => __( 'Copy link', 'bp-activity-social-share' ),
				'type'      => 'copy',
				'share_url' => '',
				'icon'      => $ui( 'link' ),
				'color'     => '',
				'mobile'    => true,
			),
		);
	}
}
