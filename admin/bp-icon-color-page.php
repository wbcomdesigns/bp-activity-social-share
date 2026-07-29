<?php
/**
 * Icon color settings tab body — rendered inside the card-panel shell
 * (includes/admin/views/shell.php) by BPAS_Admin::render_tab_body().
 *
 * Uses the bpas-card component vocabulary from assets/css/admin.css so
 * this tab matches the rest of the chrome (see admin/bp-welcome-page.php,
 * the reference implementation of this pattern).
 *
 * @package BuddyPress_Activity_Social_Share
 */

defined( 'ABSPATH' ) || exit;

wp_enqueue_script( 'wp-color-picker' );
wp_enqueue_style( 'wp-color-picker' );
$bpas_icon_color_settings = get_option( 'bpas_icon_color_settings' );
?>
<div class="bpas-card bpas-card--overflow">
	<div class="bpas-card__head">
		<h2 class="bpas-card__title"><?php esc_html_e( 'Icon Color Settings', 'buddypress-share' ); ?></h2>
		<p class="bpas-card__desc"><?php esc_html_e( 'Pick a background color for each sharing icon. Click a swatch to open the color picker.', 'buddypress-share' ); ?></p>
	</div>
	<div class="bpas-card__body">
		<form method="post" action="options.php">
			<?php
			settings_fields( 'bpas_icon_color_settings' );
			do_settings_sections( 'bpas_icon_color_settings' );
			?>
			<ul class="wb-social-icon-color-option">
				<li>
					<label for="bpas_facebook_bg_color"><?php esc_html_e( 'Facebook Icon Color', 'buddypress-share' ); ?></label>
					<input type="text" id="bpas_facebook_bg_color" class="bp_share_facebook" name="bpas_icon_color_settings[bpas_facebook_bg_color]" value="<?php echo isset( $bpas_icon_color_settings['bpas_facebook_bg_color'] ) ? esc_attr( $bpas_icon_color_settings['bpas_facebook_bg_color'] ) : ''; ?>">
				</li>
				<li>
					<label for="bpas_twitter_bg_color"><?php esc_html_e( 'Twitter Icon Color', 'buddypress-share' ); ?></label>
					<input type="text" id="bpas_twitter_bg_color" class="bp_share_twitter" name="bpas_icon_color_settings[bpas_twitter_bg_color]" value="<?php echo isset( $bpas_icon_color_settings['bpas_twitter_bg_color'] ) ? esc_attr( $bpas_icon_color_settings['bpas_twitter_bg_color'] ) : ''; ?>">
				</li>
				<li>
					<label for="bpas_pinterest_bg_color"><?php esc_html_e( 'Pinterest Icon Color', 'buddypress-share' ); ?></label>
					<input type="text" id="bpas_pinterest_bg_color" class="bp_share_pinterest" name="bpas_icon_color_settings[bpas_pinterest_bg_color]" value="<?php echo isset( $bpas_icon_color_settings['bpas_pinterest_bg_color'] ) ? esc_attr( $bpas_icon_color_settings['bpas_pinterest_bg_color'] ) : ''; ?>">
				</li>
				<li>
					<label for="bpas_linkedin_bg_color"><?php esc_html_e( 'Linkedin Icon Color', 'buddypress-share' ); ?></label>
					<input type="text" id="bpas_linkedin_bg_color" class="bp_share_linkedin" name="bpas_icon_color_settings[bpas_linkedin_bg_color]" value="<?php echo isset( $bpas_icon_color_settings['bpas_linkedin_bg_color'] ) ? esc_attr( $bpas_icon_color_settings['bpas_linkedin_bg_color'] ) : ''; ?>">
				</li>
				<li>
					<label for="bpas_reddit_bg_color"><?php esc_html_e( 'Reddit Icon Color', 'buddypress-share' ); ?></label>
					<input type="text" id="bpas_reddit_bg_color" class="bp_share_raddit" name="bpas_icon_color_settings[bpas_reddit_bg_color]" value="<?php echo isset( $bpas_icon_color_settings['bpas_reddit_bg_color'] ) ? esc_attr( $bpas_icon_color_settings['bpas_reddit_bg_color'] ) : ''; ?>">
				</li>
				<li>
					<label for="bpas_wordpress_bg_color"><?php esc_html_e( 'WordPress Icon Color', 'buddypress-share' ); ?></label>
					<input type="text" id="bpas_wordpress_bg_color" class="bp_share_wordpress" name="bpas_icon_color_settings[bpas_wordpress_bg_color]" value="<?php echo isset( $bpas_icon_color_settings['bpas_wordpress_bg_color'] ) ? esc_attr( $bpas_icon_color_settings['bpas_wordpress_bg_color'] ) : ''; ?>">
				</li>
				<li>
					<label for="bpas_telegram_bg_color"><?php esc_html_e( 'Telegram Icon Color', 'buddypress-share' ); ?></label>
					<input type="text" id="bpas_telegram_bg_color" class="bp_share_telegram" name="bpas_icon_color_settings[bpas_telegram_bg_color]" value="<?php echo isset( $bpas_icon_color_settings['bpas_telegram_bg_color'] ) ? esc_attr( $bpas_icon_color_settings['bpas_telegram_bg_color'] ) : ''; ?>">
				</li>
				<li>
					<label for="bpas_bluesky_bg_color"><?php esc_html_e( 'Bluesky Icon Color', 'buddypress-share' ); ?></label>
					<input type="text" id="bpas_bluesky_bg_color" class="bp_share_bluesky" name="bpas_icon_color_settings[bpas_bluesky_bg_color]" value="<?php echo isset( $bpas_icon_color_settings['bpas_bluesky_bg_color'] ) ? esc_attr( $bpas_icon_color_settings['bpas_bluesky_bg_color'] ) : ''; ?>">
				</li>
				<li>
					<label for="bpas_email_bg_color"><?php esc_html_e( 'E-mail Icon Color', 'buddypress-share' ); ?></label>
					<input type="text" id="bpas_email_bg_color" class="bp_share_email" name="bpas_icon_color_settings[bpas_email_bg_color]" value="<?php echo isset( $bpas_icon_color_settings['bpas_email_bg_color'] ) ? esc_attr( $bpas_icon_color_settings['bpas_email_bg_color'] ) : ''; ?>">
				</li>
				<li>
					<label for="bpas_whatsapp_bg_color"><?php esc_html_e( 'Whatsapp Icon Color', 'buddypress-share' ); ?></label>
					<input type="text" id="bpas_whatsapp_bg_color" class="bp_share_wordpress" name="bpas_icon_color_settings[bpas_whatsapp_bg_color]" value="<?php echo isset( $bpas_icon_color_settings['bpas_whatsapp_bg_color'] ) ? esc_attr( $bpas_icon_color_settings['bpas_whatsapp_bg_color'] ) : ''; ?>">
				</li>
			</ul>
			<div class="bpas-save-bar">
				<?php submit_button(); ?>
			</div>
		</form>
	</div>
</div>
<?php
// Note: the wp-color-picker initialisation lives in assets/js/admin.js
// (gate F2 — no inline <script> in PHP). It binds to
// .wb-social-icon-color-option input[type="text"], which covers every
// color field rendered above.
