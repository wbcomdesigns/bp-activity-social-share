<?php
/**
 * Inline SVG icons.
 *
 * @package BPAS
 */

namespace BPAS;

defined( 'ABSPATH' ) || exit;

/**
 * Reads bundled SVGs once per request and sanitises third-party ones.
 */
final class Icons {

	/**
	 * Request cache.
	 *
	 * @var array<string,string>
	 */
	private static array $cache = array();

	/**
	 * Bundled icon markup, decorative (aria-hidden).
	 *
	 * @param string $name Icon file name without extension.
	 * @param string $set  'brand' or 'ui'.
	 */
	public static function get( string $name, string $set = 'ui' ): string {
		$key = $set . '/' . $name;
		if ( ! isset( self::$cache[ $key ] ) ) {
			$file                = BPAS_DIR . 'assets/icons/' . ( 'brand' === $set ? 'brand' : 'ui' ) . '/' . sanitize_file_name( $name ) . '.svg';
			$svg                 = is_readable( $file ) ? (string) file_get_contents( $file ) : ''; // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- local bundled file.
			self::$cache[ $key ] = self::decorate( $svg );
		}
		return self::$cache[ $key ];
	}

	/**
	 * Sanitise an SVG supplied by third-party code (no scripts, no event handlers).
	 *
	 * @param string $svg Raw SVG.
	 */
	public static function sanitize_svg( string $svg ): string {
		$shape   = array(
			'd'               => true,
			'fill'            => true,
			'stroke'          => true,
			'stroke-width'    => true,
			'stroke-linecap'  => true,
			'stroke-linejoin' => true,
			'fill-rule'       => true,
			'clip-rule'       => true,
			'transform'       => true,
			'cx'              => true,
			'cy'              => true,
			'r'               => true,
			'rx'              => true,
			'ry'              => true,
			'x'               => true,
			'y'               => true,
			'x1'              => true,
			'x2'              => true,
			'y1'              => true,
			'y2'              => true,
			'width'           => true,
			'height'          => true,
			'points'          => true,
		);
		$allowed = array(
			'svg'      => array(
				'xmlns'   => true,
				'viewbox' => true,
				'width'   => true,
				'height'  => true,
				'fill'    => true,
				'stroke'  => true,
			),
			'g'        => $shape,
			'path'     => $shape,
			'circle'   => $shape,
			'ellipse'  => $shape,
			'rect'     => $shape,
			'line'     => $shape,
			'polyline' => $shape,
			'polygon'  => $shape,
		);
		return self::decorate( wp_kses( $svg, $allowed ) );
	}

	/**
	 * Mark an SVG decorative and give it the shared class.
	 *
	 * @param string $svg SVG markup.
	 */
	private static function decorate( string $svg ): string {
		if ( '' === trim( $svg ) ) {
			return '';
		}
		return (string) preg_replace( '/<svg\b/i', '<svg class="bpas-icon" aria-hidden="true" focusable="false"', trim( $svg ), 1 );
	}
}
