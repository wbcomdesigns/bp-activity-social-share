<?php
/**
 * Define the internationalization functionality
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       http://wbcomdesigns.com
 * @since      1.0.0
 *
 * @package    Buddypress_Share
 * @subpackage Buddypress_Share/includes
 */

/**
 * Define the internationalization functionality.
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Buddypress_Share
 * @subpackage Buddypress_Share/includes
 * @author     Wbcom Designs <admin@wbcomdesigns.com>
 */
// phpcs:ignore PEAR.NamingConventions.ValidClassName.Invalid, Squiz.Commenting.ClassComment.Missing -- Legacy class name; docblock is above.
class Buddypress_Share_i18n {

	/**
	 * Load the plugin text domain for translation.
	 *
	 * Registered on `init` (see Buddypress_Share::set_locale). That timing is
	 * deliberate and must not be moved earlier: WordPress 6.7 warns via
	 * _doing_it_wrong() when a text domain is loaded before `init`, so hooking
	 * this to plugins_loaded would trade a working setup for a notice.
	 *
	 * Up to 3.5.6 this class carried its own copy of core's
	 * load_plugin_textdomain() - the same WP_LANG_DIR-then-plugin-dir lookup,
	 * minus the $locale argument core has passed to load_textdomain() since
	 * 6.1 for cache keying. Core's version is a strict superset, so the copy
	 * is gone and this calls core directly.
	 *
	 * @since    1.0.0
	 */
	public function bp_share_load_plugin_textdomain() {

		load_plugin_textdomain(
			'buddypress-share',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages'
		);
	}
}
