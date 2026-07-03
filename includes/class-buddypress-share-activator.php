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
			if ( false === get_site_option( 'bp_share_services' ) ) {
				$bp_share_social_icon_default = array(
					'Facebook' => 'Facebook',
					'Twitter'  => 'Twitter',
					'Linkedin' => 'Linkedin',
					'Whatsapp' => 'Whatsapp',
				);
				update_site_option( 'bp_share_services', $bp_share_social_icon_default );
			}
			if ( false === get_site_option( 'bpas_icon_color_settings' ) ) {
				$bp_icon_color_default = array(
					'bpas_facebook_bg_color'  => '#3B5998',
					'bpas_twitter_bg_color'   => '#1DA1F2',
					'bpas_pinterest_bg_color' => '#E60023',
					'bpas_linkedin_bg_color'  => '#007BB6',
					'bpas_reddit_bg_color'    => '#FF4500',
					'bpas_wordpress_bg_color' => '#21759B',
					'bpas_pocket_bg_color'    => '#EF3E56',
					'bpas_telegram_bg_color'  => '#0088cc',
					'bpas_bluesky_bg_color'   => '#1185fe',
					'bpas_email_bg_color'     => '#AD0000',
					'bpas_whatsapp_bg_color'  => '#46bd00',
				);
					update_site_option( 'bpas_icon_color_settings', $bp_icon_color_default );
			}
		}
	}

	/**
	 * One-time upgrade routine, run on bp_loaded when the stored DB
	 * version is older than the current plugin version.
	 *
	 * 3.5.6: releases up to 3.5.5 seeded #000000 as the default icon
	 * color for Pinterest, Reddit, WordPress, and Pocket, which renders
	 * the share icons invisible on dark-mode themes (BuddyX/BuddyX Pro
	 * dark). Remap those exact legacy defaults to the network brand
	 * colors. Values the site owner customized away from #000000 are
	 * left untouched.
	 *
	 * @since 3.5.6
	 * @return void
	 */
	public static function maybe_upgrade() {
		$db_version = get_site_option( 'bp_share_db_version' );
		if ( $db_version && version_compare( (string) $db_version, '3.5.6', '>=' ) ) {
			return;
		}

		$legacy_black_defaults = array(
			'bpas_pinterest_bg_color' => '#E60023',
			'bpas_reddit_bg_color'    => '#FF4500',
			'bpas_wordpress_bg_color' => '#21759B',
			'bpas_pocket_bg_color'    => '#EF3E56',
		);

		// The Icon Colors form (options.php) saves the regular option,
		// while activation seeded the site option — migrate both so
		// single-site and multisite installs end up consistent.
		foreach ( array( 'option', 'site_option' ) as $bpas_store ) {
			$settings = ( 'site_option' === $bpas_store ) ? get_site_option( 'bpas_icon_color_settings' ) : get_option( 'bpas_icon_color_settings' );
			if ( ! is_array( $settings ) ) {
				continue;
			}
			$changed = false;
			foreach ( $legacy_black_defaults as $key => $brand_color ) {
				if ( isset( $settings[ $key ] ) && '#000000' === strtolower( trim( (string) $settings[ $key ] ) ) ) {
					$settings[ $key ] = $brand_color;
					$changed          = true;
				}
			}
			if ( $changed ) {
				if ( 'site_option' === $bpas_store ) {
					update_site_option( 'bpas_icon_color_settings', $settings );
				} else {
					update_option( 'bpas_icon_color_settings', $settings );
				}
			}
		}

		update_site_option( 'bp_share_db_version', defined( 'BP_SHARE_VERSION' ) ? BP_SHARE_VERSION : '3.5.6' );
	}
}
