<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://wbcomdesigns.com
 * @since      1.0.0
 *
 * @package    Buddypress_Share
 * @subpackage Buddypress_Share/admin
 */

/**
 * Legacy admin service class.
 *
 * Post-migration (3.5.6) this class no longer renders the admin chrome
 * (menu, page, tabs, enqueue) — BPAS_Admin owns that. It is retained
 * because it still owns the settings registration (Settings API option
 * groups bp_share_services_extra + bpas_icon_color_settings and their
 * sanitizers), the General + Icon Colors tab CONTENT renderers, and the
 * four AJAX save handlers. BPAS_Admin delegates each tab body to the
 * section renderers here.
 *
 * @package    Buddypress_Share
 * @subpackage Buddypress_Share/admin
 * @author     Wbcom Designs <admin@wbcomdesigns.com>
 */
class Buddypress_Share_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of this plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Function to add admin register settings for plugin.
	 *
	 * @since    1.0.0
	 */
	public function bpas_icon_color_register_setting() {
		register_setting(
			'bpas_icon_color_settings',
			'bpas_icon_color_settings',
			array(
				'type'              => 'array',
				'sanitize_callback' => array( $this, 'bpas_sanitize_icon_color_settings' ),
			)
		);
	}

	/**
	 * Sanitize icon color settings.
	 *
	 * @param mixed $input Raw input.
	 * @return array Sanitized settings.
	 */
	public function bpas_sanitize_icon_color_settings( $input ) {
		if ( ! is_array( $input ) ) {
			return array();
		}
		$sanitized = array();
		foreach ( $input as $key => $value ) {
			$sanitized[ sanitize_text_field( $key ) ] = sanitize_hex_color( $value ) ? sanitize_hex_color( $value ) : sanitize_text_field( $value );
		}
		return $sanitized;
	}

	/**
	 * Sanitize services extra settings.
	 *
	 * @param mixed $input Raw input.
	 * @return array Sanitized settings.
	 */
	public function bpas_sanitize_services_extra( $input ) {
		if ( ! is_array( $input ) ) {
			return array();
		}
		return array_map( 'sanitize_text_field', $input );
	}

	/**
	 * Intialize setting to show share in popup or new page.
	 *
	 * @access public
	 * @author  Wbcom Designs
	 * @since    1.0.0
	 */
	public function bp_share_checkbox_open_services_render() {
		$extra_options = get_site_option( 'bp_share_services_extra' );
		?>
		<input type='checkbox' name='bp_share_services_open' 
		<?php
		if ( isset( $extra_options['bp_share_services_open'] ) && 1 === (int) $extra_options['bp_share_services_open'] ) {
			echo 'checked="checked"';
		}
		?>
																value='1'>
		<?php
	}


	/**
	 * Intialize bp_share_settings_section_callback.
	 *
	 * @access public
	 * @author  Wbcom Designs
	 * @since    1.0.0
	 */
	public function bp_share_settings_section_callback() {
		echo '<div class="bp_share_settings_section_callback_class">';
		echo '<div class="title-bp-share-view">';
		esc_html_e( 'Default is set to open window in popup. If this option is disabled then services open in new tab instead popup.', 'buddypress-share' );
		echo '</div>';
		echo '</div>';
	}

	/**
	 * BP share services ajax.
	 *
	 * @access public
	 * @author   Wbcom Designs
	 * @since    1.0.0
	 */
	public function bp_share_chb_services_ajax() {
		// Check if the request is a POST, verify the nonce, and confirm user capabilities.
		if ( ! empty( $_POST ) && check_admin_referer( 'bp_share_nonce', 'nonce' ) && current_user_can( 'manage_options' ) ) {
			// Sanitize and retrieve the extra options from POST data.
			$extras_options = isset( $_POST['active_chb_extras'] ) ? map_deep( wp_unslash( $_POST['active_chb_extras'] ), 'sanitize_text_field' ) : array();

			// Update extra options setting.
			$extra_option_new = array( 'bp_share_services_open' => in_array( 'bp_share_services_open', $extras_options, true ) ? 1 : 0 );
			update_site_option( 'bp_share_services_extra', $extra_option_new );
		}

		// Terminate the execution for AJAX requests in a WordPress-friendly way.
		wp_die();
	}


	/**
	 * Intialize plugin admin settings.
	 *
	 * @access public
	 * @author  Wbcom Designs
	 * @since    1.0.0
	 */
	public function bp_share_settings_init() {
		register_setting(
			'bp_share_services_extra',
			'bp_share_services_extra',
			array(
				'type'              => 'array',
				'sanitize_callback' => array( $this, 'bpas_sanitize_services_extra' ),
			)
		);
		add_settings_section(
			'bp_share_extra_options',
			esc_html__( 'Extra Options', 'buddypress-share' ),
			array( $this, 'bp_share_settings_section_callback' ),
			'bp_share_services_extra'
		);
		add_settings_field(
			'bp_share_services_open',
			esc_html__( 'Open as popup window', 'buddypress-share' ),
			array( $this, 'bp_share_checkbox_open_services_render' ),
			'bp_share_services_extra',
			'bp_share_extra_options'
		);
	}

	/**
	 * Welcome template
	 *
	 * @since    1.0.0
	 */
	public function bpas_welcome_section() {
		if ( file_exists( BP_ACTIVITY_SHARE_PLUGIN_PATH . 'admin/bp-welcome-page.php' ) ) {
			require_once BP_ACTIVITY_SHARE_PLUGIN_PATH . 'admin/bp-welcome-page.php';
		}
	}

	/**
	 * Social service settig template
	 *
	 * @since    1.0.0
	 */
	public function bpas_general_setting_section() {
		?>
		<form method="post" action="options.php" id="wss_form_data">
			<div class="bpas-card">
				<div class="bpas-card__head">
					<h2 class="bpas-card__title"><?php esc_html_e( 'Enable Sharing Sites', 'buddypress-share' ); ?></h2>
					<p class="bpas-card__desc"><?php esc_html_e( 'Drag a network between the two columns to turn it off or on. Changes save automatically.', 'buddypress-share' ); ?></p>
				</div>
				<div class="bpas-card__body">
					<div class="save-option-message"></div>
					<div class="option-not-save-message"></div>
					<section class="social_icon_section">
						<ul id="drag_social_icon">
							<h3><?php esc_html_e( 'Disable', 'buddypress-share' ); ?></h3>
							<?php
							$get_social_value = get_site_option( 'bp_share_services' );
							if ( empty( $get_social_value['Facebook'] ) ) {
								?>
								<li class="socialicon icon_Facebook" name="icon_facebook"><?php esc_html_e( 'Facebook', 'buddypress-share' ); ?></li>
								<?php
							}
							if ( empty( $get_social_value['Twitter'] ) ) {
								?>
								<li class="socialicon icon_Twitter" name="icon_gmail"><?php esc_html_e( 'Twitter', 'buddypress-share' ); ?></li>
								<?php
							}
							if ( empty( $get_social_value['Pinterest'] ) ) {
								?>
								<li class="socialicon icon_Pinterest" name="icon_Pinterest"><?php esc_html_e( 'Pinterest', 'buddypress-share' ); ?></li>
								<?php
							}
							if ( empty( $get_social_value['Linkedin'] ) ) {
								?>
								<li class="socialicon icon_LinkedIn" name="icon_linkedin"><?php esc_html_e( 'Linkedin', 'buddypress-share' ); ?></li>
								<?php
							}
							if ( empty( $get_social_value['Reddit'] ) ) {
								?>
								<li class="socialicon icon_Reddit" name="icon_reddit"><?php esc_html_e( 'Reddit', 'buddypress-share' ); ?></li>
								<?php
							}
							if ( empty( $get_social_value['WordPress'] ) ) {
								?>
								<li class="socialicon icon_WordPress" name="icon_wordpress"><?php esc_html_e( 'WordPress', 'buddypress-share' ); ?></li>
								<?php
							}
							if ( empty( $get_social_value['Pocket'] ) ) {
								?>
								<li class="socialicon icon_Pocket" name="icon_pocket"><?php esc_html_e( 'Pocket', 'buddypress-share' ); ?></li>
								<?php
							}
							if ( empty( $get_social_value['Telegram'] ) ) {
								?>
								<li class="socialicon icon_Telegram" name="icon_telegram"><?php esc_html_e( 'Telegram', 'buddypress-share' ); ?></li>
								<?php
							}
							if ( empty( $get_social_value['Bluesky'] ) ) {
								?>
								<li class="socialicon icon_Bluesky" name="icon_bluesky"><?php esc_html_e( 'Bluesky', 'buddypress-share' ); ?></li>
								<?php
							}
							if ( empty( $get_social_value['E-mail'] ) ) {
								?>
								<li class="socialicon icon_Gmail" name="icon_gmail"><?php esc_html_e( 'E-mail', 'buddypress-share' ); ?></li>
								<?php
							}
							if ( empty( $get_social_value['Whatsapp'] ) ) {
								?>
								<li class="socialicon icon_WhatAapp" name="icon_whatsapp"><?php esc_html_e( 'Whatsapp', 'buddypress-share' ); ?></li>
							<?php } ?>
						</ul>
						<ul id="drag_icon_ul">
							<h3><?php esc_html_e( 'Enable', 'buddypress-share' ); ?></h3>
							<?php
							if ( ! empty( $get_social_value['Facebook'] ) ) {
								?>
								<li class="socialicon icon_Facebook" name="icon_facebook"><?php esc_html_e( 'Facebook', 'buddypress-share' ); ?></li>
								<?php
							}
							if ( ! empty( $get_social_value['Twitter'] ) ) {
								?>
								<li class="socialicon icon_Twitter" name="icon_twitter"><?php esc_html_e( 'Twitter', 'buddypress-share' ); ?></li>
								<?php
							}
							if ( ! empty( $get_social_value['Pinterest'] ) ) {
								?>
								<li class="socialicon icon_Pinterest" name="icon_Pinterest"><?php esc_html_e( 'Pinterest', 'buddypress-share' ); ?></li>
								<?php
							}
							if ( ! empty( $get_social_value['Linkedin'] ) ) {
								?>
								<li class="socialicon icon_LinkedIn" name="icon_linkedin"><?php esc_html_e( 'Linkedin', 'buddypress-share' ); ?></li>
								<?php
							}
							if ( ! empty( $get_social_value['Reddit'] ) ) {
								?>
								<li class="socialicon icon_Reddit" name="icon_reddit"><?php esc_html_e( 'Reddit', 'buddypress-share' ); ?></li>
								<?php
							}
							if ( ! empty( $get_social_value['WordPress'] ) ) {
								?>
								<li class="socialicon icon_WordPress" name="icon_wordpress"><?php esc_html_e( 'WordPress', 'buddypress-share' ); ?></li>
								<?php
							}
							if ( ! empty( $get_social_value['Pocket'] ) ) {
								?>
								<li class="socialicon icon_Pocket" name="icon_pocket"><?php esc_html_e( 'Pocket', 'buddypress-share' ); ?></li>
								<?php
							}
							if ( ! empty( $get_social_value['Telegram'] ) ) {
								?>
								<li class="socialicon icon_Telegram" name="icon_telegram"><?php esc_html_e( 'Telegram', 'buddypress-share' ); ?></li>
								<?php
							}
							if ( ! empty( $get_social_value['Bluesky'] ) ) {
								?>
								<li class="socialicon icon_Bluesky" name="icon_bluesky"><?php esc_html_e( 'Bluesky', 'buddypress-share' ); ?></li>
								<?php
							}
							if ( ! empty( $get_social_value['E-mail'] ) ) {
								?>
								<li class="socialicon icon_Gmail" name="icon_gmail"><?php esc_html_e( 'E-mail', 'buddypress-share' ); ?></li>
								<?php
							}
							if ( ! empty( $get_social_value['Whatsapp'] ) ) {
								?>
								<li class="socialicon icon_WhatsApp" name="icon_whatsapp"><?php esc_html_e( 'Whatsapp', 'buddypress-share' ); ?></li>
							<?php } ?>
						</ul>
					</section>
				</div>
			</div>

			<?php
			/*
			 * Extra Options renders through the Settings API, so its heading and
			 * description arrive as siblings of the fields table rather than as
			 * one .bpas-card__head. admin/css/buddypress-share-admin.css styles
			 * that h2 + description pair as a single card head band.
			 */
			?>
			<div class="bpas-card bp-share-services-extra">
				<?php do_settings_sections( 'bp_share_services_extra' ); ?>
			</div>

			<!--save the settings-->
			<input type="hidden" name="action" value="update" />
			<?php
			$social_options = get_site_option( 'bp_share_services' );
			if ( ! empty( $social_options ) ) {
				$social_key_string = '';
				foreach ( $social_options as $service_key => $social_option ) {
					if ( count( $social_options ) !== 1 ) {
						$social_key_string .= $service_key . ',';
					} else {
						$social_key_string = $service_key;
					}
				}
				if ( count( $social_options ) !== 1 ) {
					$social_key_string = rtrim( $social_key_string, ', ' );
				}
				?>
				<input type="hidden" name="page_options" value="<?php echo esc_attr( $social_key_string ); ?>" />
				<?php
			}
			?>
			<div class="bpas-save-bar">
				<input type="submit" class="button button-primary bp_share_option_save" value="<?php esc_attr_e( 'Save Changes', 'buddypress-share' ); ?>" />
			</div>
		</form>
		<?php do_action( 'bp_share_add_services_options', '', '' ); ?>
		<?php
	}


	/**
	 * Icon color settings section template.
	 *
	 * @since 1.0.0
	 */
	public function bpas_icon_color_setting_section() {
		if ( file_exists( BP_ACTIVITY_SHARE_PLUGIN_PATH . 'admin/bp-icon-color-page.php' ) ) {
			require_once BP_ACTIVITY_SHARE_PLUGIN_PATH . 'admin/bp-icon-color-page.php';
		}
	}

	/**
	 * This function is for that save social icon values in database
	 **/
	public function wss_social_icons() {
		// Sanitize and retrieve nonce from POST data.
		$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';

		// Verify nonce and user capabilities.
		if ( ! wp_verify_nonce( $nonce, 'bp_share_nonce' ) || ! current_user_can( 'manage_options' ) ) {
			// Send a JSON error response if nonce verification fails or user lacks required capabilities.
			$error = new WP_Error( '001', 'Sorry, your nonce did not verify.', 'Some information' );
			wp_send_json_error( $error );
		}

		// Sanitize and retrieve 'term_name' from POST data.
		$term_name = isset( $_POST['term_name'] ) ? sanitize_text_field( wp_unslash( $_POST['term_name'] ) ) : '';

		// Retrieve current icon settings.
		$wss_admin_icon_setting = get_site_option( 'bp_share_services' );

		// Prepare new icon value.
		$new_icon_value = array( $term_name => $term_name );

		// Merge new icon value with existing settings or initialize if not set.
		$updated_icon_setting = is_array( $wss_admin_icon_setting )
			? array_merge( $wss_admin_icon_setting, $new_icon_value )
			: $new_icon_value;

		// Update the site option and handle the response.
		if ( update_site_option( 'bp_share_services', $updated_icon_setting ) ) {
			wp_send_json_success();
		} else {
			wp_send_json_error();
		}
	}


	/**
	 * This function is for that remove social icon when drag social icon in disable section
	 **/
	public function wss_social_remove_icons() {
		// Sanitize and retrieve nonce from POST data.
		$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';

		// Verify nonce for security and check user capabilities.
		if ( ! wp_verify_nonce( $nonce, 'bp_share_nonce' ) || ! current_user_can( 'manage_options' ) ) {
			// Send a JSON error response if nonce verification fails or user lacks required capabilities.
			$error = new WP_Error( '001', 'Sorry, your nonce did not verify.', 'Some information' );
			wp_send_json_error( $error );
		}

		// Sanitize and retrieve the icon name from POST data.
		$icon_name = isset( $_POST['icon_name'] ) ? sanitize_text_field( wp_unslash( $_POST['icon_name'] ) ) : '';

		// Retrieve current icon settings.
		$wss_admin_icon_remove = get_site_option( 'bp_share_services' );

		// Initialize update flag.
		$update_drag_value = false;

		// Check and remove the specified icon from the settings.
		if ( isset( $wss_admin_icon_remove[ $icon_name ] ) ) {
			unset( $wss_admin_icon_remove[ $icon_name ] );
			// Update the site option and set the flag to true.
			$update_drag_value = update_site_option( 'bp_share_services', $wss_admin_icon_remove );
		}

		// Send a JSON success or error response based on whether the update occurred.
		if ( $update_drag_value ) {
			wp_send_json_success();
		} else {
			wp_send_json_error();
		}
	}
}
