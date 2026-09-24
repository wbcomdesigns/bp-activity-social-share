<?php
/**
 * The one settings store for Free.
 *
 * @package BPAS
 */

namespace BPAS;

defined( 'ABSPATH' ) || exit;

/**
 * Reads and writes the `bpas_settings` option.
 */
final class Settings {

	const OPTION = 'bpas_settings';

	/**
	 * Defaults (plan 12.1).
	 */
	public static function defaults(): array {
		return (array) apply_filters(
			'bpas_default_settings',
			array(
				'networks'         => array( 'facebook', 'x', 'linkedin', 'whatsapp', 'email', 'copy' ),
				'guest_share'      => true,
				'show_on_profiles' => true,
				'show_on_groups'   => true,
				'label'            => '',
				'position'         => 'after',
				'colors'           => 'brand',
			)
		);
	}

	/**
	 * All settings merged with defaults.
	 */
	public static function all(): array {
		$saved = get_option( self::OPTION, array() );
		return array_merge( self::defaults(), is_array( $saved ) ? $saved : array() );
	}

	/**
	 * One setting.
	 *
	 * @param string $key     Key.
	 * @param mixed  $default Fallback for unknown keys.
	 * @return mixed
	 */
	public static function get( string $key, $default = null ) {
		$all = self::all();
		return array_key_exists( $key, $all ) ? $all[ $key ] : $default;
	}

	/**
	 * Merge, sanitise and save a partial update.
	 *
	 * @param array $changes Partial settings.
	 * @return array Saved settings.
	 */
	public static function update( array $changes ): array {
		$old = self::all();
		$new = self::sanitize( array_merge( $old, $changes ) );
		update_option( self::OPTION, $new, false );
		do_action( 'bpas_settings_updated', $new, $old );
		return $new;
	}

	/**
	 * Sanitise a full settings array.
	 *
	 * @param array $input Raw settings.
	 */
	public static function sanitize( array $input ): array {
		$defaults = self::defaults();
		$known    = array_keys( Networks::all() );

		$networks = array();
		foreach ( (array) ( $input['networks'] ?? array() ) as $slug ) {
			$slug = sanitize_key( (string) $slug );
			// Unknown slugs are kept only when no registry is loaded yet (early migration).
			if ( ( empty( $known ) || in_array( $slug, $known, true ) ) && ! in_array( $slug, $networks, true ) ) {
				$networks[] = $slug;
			}
		}

		return array(
			'networks'         => $networks,
			'guest_share'      => ! empty( $input['guest_share'] ),
			'show_on_profiles' => ! empty( $input['show_on_profiles'] ),
			'show_on_groups'   => ! empty( $input['show_on_groups'] ),
			'label'            => sanitize_text_field( (string) ( $input['label'] ?? '' ) ),
			'position'         => in_array( $input['position'] ?? '', array( 'before', 'after' ), true ) ? $input['position'] : $defaults['position'],
			'colors'           => in_array( $input['colors'] ?? '', array( 'brand', 'theme' ), true ) ? $input['colors'] : $defaults['colors'],
		);
	}

	/**
	 * Button label shown to visitors.
	 */
	public static function label(): string {
		$label = (string) self::get( 'label', '' );
		return '' !== $label ? $label : __( 'Share', 'bp-activity-social-share' );
	}
}
