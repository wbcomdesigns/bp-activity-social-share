<?php
wp_enqueue_script( 'wp-color-picker' );
wp_enqueue_style( 'wp-color-picker' );
$bpas_icon_color_settings = get_option( 'bpas_icon_color_settings' );
?>
<div class="wbcom-tab-content">
				<div class="wbcom-admin-title-section">
					<h3><?php esc_html_e( 'Icon Color Settings', 'buddypress-share' ); ?></h3>
				</div>
				<div class="wbcom-admin-option-wrap wbcom-admin-option-wrap-view">
					<div class="save-option-message"></div>
					<div class="option-not-save-message"></div>
					<form method="post" action="options.php">
						<?php
							settings_fields( 'bpas_icon_color_settings' );
							do_settings_sections( 'bpas_icon_color_settings' );
						?>
						 <div class="wbcom-settings-section-wrap">
							<div class="wbcom-settings-section-options-heading">
								<label for="wbcom-social-share">
									<?php esc_html_e( 'Sharing Icon Color', 'wbcom-social-share' ); ?>
								</label>
							</div>
															
								<div class="wbcom-settings-section-options">
								<ul class="Button-Color-Option">
									<li>						
										<input type="text"  class="bp_share_facebook" aria-expanded="false" name="bpas_icon_color_settings[bpas_facebook_bg_color]"  value="<?php echo isset( $bpas_icon_color_settings['bpas_facebook_bg_color'] ) ? $bpas_icon_color_settings['bpas_facebook_bg_color'] : '';?>" style="background-color: rgb(46, 188, 174);">
										<label>
											<small><?php esc_html_e( 'Facebbok Icon Color', 'bp-resume-manager' ); ?></small>
										</label>
									</li>									
									<li>						
										<input type="text"  class="bp_share_twitter" name="bpas_icon_color_settings[bpas_twitter_bg_color]"  value="<?php echo isset( $bpas_icon_color_settings['bpas_twitter_bg_color'] ) ? $bpas_icon_color_settings['bpas_twitter_bg_color'] : '';?>">
										<label>
											<small><?php esc_html_e( 'Twitter Icon Color', 'bp-resume-manager' ); ?></small>
										</label>
									</li>
									<li>						
										<input type="text"  class="bp_share_pinterest" name="bpas_icon_color_settings[bpas_pinterest_bg_color]"  value="<?php echo isset( $bpas_icon_color_settings['bpas_pinterest_bg_color'] ) ? $bpas_icon_color_settings['bpas_pinterest_bg_color'] : '';?>">
										<label>
											<small><?php esc_html_e( 'Pinterest Icon Color', 'bp-resume-manager' ); ?></small>
										</label>
									</li>
									<li>						
										<input type="text"  class="bp_share_linkedin" name="bpas_icon_color_settings[bpas_linkedin_bg_color]"  value="<?php echo isset( $bpas_icon_color_settings['bpas_linkedin_bg_color'] ) ? $bpas_icon_color_settings['bpas_linkedin_bg_color'] : '';?>">
										<label>
											<small><?php esc_html_e( 'Linkedin Icon Color', 'bp-resume-manager' ); ?></small>
										</label>
									</li>
									<li>						
										<input type="text"  class="bp_share_raddit" name="bpas_icon_color_settings[bpas_reddit_bg_color]"  value="<?php echo isset( $bpas_icon_color_settings['bpas_reddit_bg_color'] ) ? $bpas_icon_color_settings['bpas_reddit_bg_color'] : '';?>">
										<label>
											<small><?php esc_html_e( 'Raddit Icon Color', 'bp-resume-manager' ); ?></small>
										</label>
									</li>
									<li>						
										<input type="text"  class="bp_share_wordpress" name="bpas_icon_color_settings[bpas_wordpress_bg_color]"  value="<?php echo isset( $bpas_icon_color_settings['bpas_wordpress_bg_color'] ) ? $bpas_icon_color_settings['bpas_wordpress_bg_color'] : '';?>">
										<label>
											<small><?php esc_html_e( 'Wordpress Icon Color', 'bp-resume-manager' ); ?></small>
										</label>
									</li>
									<li>						
										<input type="text"  class="bp_share_pocket" name="bpas_icon_color_settings[bpas_pocket_bg_color]"  value="<?php echo isset( $bpas_icon_color_settings['bpas_pocket_bg_color'] ) ? $bpas_icon_color_settings['bpas_pocket_bg_color'] : '';?>">
										<label>
											<small><?php esc_html_e( 'Pocket Icon Color', 'bp-resume-manager' ); ?></small>
										</label>
									</li>
									<li>						
										<input type="text"  class="bp_share_email" name="bpas_icon_color_settings[bpas_email_bg_color]"  value="<?php echo isset( $bpas_icon_color_settings['bpas_email_bg_color'] ) ? $bpas_icon_color_settings['bpas_email_bg_color'] : '';?>">
										<label>
											<small><?php esc_html_e( 'E-mail Icon Color', 'bp-resume-manager' ); ?></small>
										</label>
									</li>
									<li>						
										<input type="text"  class="bp_share_wordpress" name="bpas_icon_color_settings[bpas_whatsapp_bg_color]"  value="<?php echo isset( $bpas_icon_color_settings['bpas_whatsapp_bg_color'] ) ? $bpas_icon_color_settings['bpas_whatsapp_bg_color'] : '';?>">
										<label>
											<small><?php esc_html_e( 'Whatsapp Icon Color', 'bp-resume-manager' ); ?></small>
										</label>						
								</ul>
							</div>	
							<?php submit_button(); ?>			
						</form>
					</div>
				</div>


<script>
jQuery(document).ready(function( ) {
	jQuery(".bp_share_facebook").wpColorPicker();
	jQuery(".bp_share_twitter").wpColorPicker();
	jQuery(".bp_share_pinterest").wpColorPicker();
	jQuery(".bp_share_linkedin").wpColorPicker();
	jQuery(".bp_share_raddit").wpColorPicker();
	jQuery(".bp_share_wordpress").wpColorPicker();
	jQuery(".bp_share_pocket").wpColorPicker();
	jQuery(".bp_share_email").wpColorPicker();
	jQuery(".bp_share_wordpress").wpColorPicker();
});
</script>
