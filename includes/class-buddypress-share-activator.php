<?php
/**
 * Fired during plugin activation.
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 *
 * @package    Buddypress_Share
 * @subpackage Buddypress_Share/includes
 * @author     Wbcom Designs <admin@wbcomdesigns.com>
 */

/**
 * Fired during plugin activation.
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 *
 * @package    Buddypress_Share
 * @subpackage Buddypress_Share/includes
 * @author     Wbcom Designs <admin@wbcomdesigns.com>
 */
class Buddypress_Share_Activator {

	/**
	 * Short Description. (use period)
	 *  Long Description.
	 *
	 *  @since    1.0.0
	 */
	public static function activate() {
		if ( class_exists( 'BuddyPress' ) ) {
			if ( get_site_option( 'bp_share_services' ) == false ) {
				$bp_share_social_icon_default = array(
							'Facebook' => 'Facebook',
							'Twitter'  => 'Twitter',
							'LinkedIn'  => 'LinkedIn',
							'WhatsApp'  => 'WhatsApp',
						);
					update_site_option( 'bp_share_services', $bp_share_social_icon_default );
			}
		}

	}

}
