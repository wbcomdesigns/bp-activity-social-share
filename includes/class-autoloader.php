<?php
/**
 * Namespace autoloader shared by Free and Pro.
 *
 * `Prefix\Sub_Dir\Class_Name` resolves to `{dir}/sub-dir/class-class-name.php`.
 * Pro registers its own prefix through bpas_register_autoload(), so there is
 * exactly one autoloader for the product family.
 *
 * @package BPAS
 */

namespace BPAS;

defined( 'ABSPATH' ) || exit;

/**
 * Maps namespace prefixes to directories.
 */
final class Autoloader {

	/**
	 * Registered prefix => directory map.
	 *
	 * @var array<string,string>
	 */
	private static array $map = array();

	/**
	 * Register a namespace prefix.
	 *
	 * @param string $prefix Top-level namespace, e.g. 'BPAS' or 'BPAS_Pro'.
	 * @param string $dir    Absolute directory holding the classes.
	 */
	public static function register( string $prefix, string $dir ): void {
		if ( empty( self::$map ) ) {
			spl_autoload_register( array( __CLASS__, 'load' ) );
		}
		self::$map[ trim( $prefix, '\\' ) . '\\' ] = trailingslashit( $dir );
	}

	/**
	 * Load a class if it belongs to a registered prefix.
	 *
	 * @param string $class Fully qualified class name.
	 */
	public static function load( string $class ): void {
		foreach ( self::$map as $prefix => $dir ) {
			if ( ! str_starts_with( $class, $prefix ) ) {
				continue;
			}
			$parts = explode( '\\', substr( $class, strlen( $prefix ) ) );
			$name  = array_pop( $parts );
			$path  = $dir;
			foreach ( $parts as $part ) {
				$path .= strtolower( str_replace( '_', '-', $part ) ) . '/';
			}
			$file = $path . 'class-' . strtolower( str_replace( '_', '-', $name ) ) . '.php';
			if ( is_readable( $file ) ) {
				require_once $file;
			}
			return;
		}
	}
}
