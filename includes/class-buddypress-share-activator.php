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
			if ( get_site_option( 'bp_share_services' ) !== false ) {
				$services = get_site_option( 'bp_share_services' );
				if ( empty( $services ) ) {
					$bp_share_social_icon_default = array(
							'Facebook' => 'Facebook',
							'Twitter'  => 'Twitter',
							'LinkedIn'  => 'LinkedIn',
							'WhatsApp'  => 'WhatsApp',
						);
					update_site_option( 'bp_share_services', $bp_share_social_icon_default );
				} else {
						$services['Facebook'] = 'Facebook';

						$services['Twitter'] = 'Twitter';

						$services['LinkedIn'] = 'LinkedIn';

						$services['WhatsApp'] = 'WhatsApp';

					update_site_option( 'bp_share_services', $services );

				}
			}else {
				$bp_share_social_icon_default = array(
							'Facebook' => 'Facebook',
							'Twitter'  => 'Twitter',
							'LinkedIn'  => 'LinkedIn',
							'WhatsApp'  => 'WhatsApp',
						);

				// The option hasn't been added yet. We'll add it with $autoload set to 'no'.

				$deprecated = null;

				$autoload = 'no';

				update_site_option( 'bp_share_services', $bp_share_social_icon_default, $deprecated, $autoload );

			}

			if ( get_site_option( 'bp_share_services' ) !== false ) {

				$extra_option_new = array(

					'bp_share_services_open' => 1,

				);

				update_site_option( 'bp_share_services_extra', $extra_option_new );

			} else {

				$extra_option_new = array(

					'bp_share_services_open' => 1,

				);

				$deprecated = null;

				$autoload = 'no';

				update_site_option( 'bp_share_services_extra', $extra_option_new, $deprecated, $autoload );

			}

			if ( get_site_option( 'bp_share_all_services_disable' ) === false ) {
				update_site_option( 'bp_share_all_services_disable', 'enable' );
			}
		}

	}

}
