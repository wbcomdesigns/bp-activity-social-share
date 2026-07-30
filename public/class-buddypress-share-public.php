<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://wbcomdesigns.com
 * @since      1.0.0
 *
 * @package    Buddypress_Share
 * @subpackage Buddypress_Share/public
 */

defined( 'ABSPATH' ) || exit;

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Buddypress_Share
 * @subpackage Buddypress_Share/public
 * @author     Wbcom Designs <admin@wbcomdesigns.com>
 */
class Buddypress_Share_Public {


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
	 * @param      string $plugin_name       The name of the plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @access public
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_register_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/buddypress-share-public.css', array(), $this->version, 'all' );

		if ( $this->should_enqueue_assets() ) {
			wp_enqueue_style( $this->plugin_name );
		}

		// Per-site custom icon colors are injected as :root token overrides
		// attached to the enqueued stylesheet handle (cacheable, CSP-safe)
		// instead of a render-blocking inline <style> printed in wp_head.
		$custom_css = $this->bp_share_icon_custom_color_css();
		if ( ! empty( $custom_css ) ) {
			wp_add_inline_style( $this->plugin_name, $custom_css );
		}
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @access public
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_register_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/buddypress-share-public.js', array( 'jquery' ), $this->version, false );

		if ( $this->should_enqueue_assets() ) {
			wp_enqueue_script( $this->plugin_name );
		}
	}

	/**
	 * Whether the public share assets are needed up-front for this request.
	 *
	 * The share button only ever renders inside a BuddyPress activity loop, so
	 * loading the CSS/JS on every page of the site is waste. This covers the
	 * common case - a BuddyPress screen - so the stylesheet lands in the head
	 * with no flash of unstyled buttons.
	 *
	 * An activity loop can also appear outside a BP page through a widget,
	 * block or shortcode. Those cases are handled lazily instead: the render
	 * method enqueues both handles when it actually outputs a share button, so
	 * nothing is dropped, it simply prints in the footer. Both assets are
	 * registered unconditionally so that late enqueue always resolves.
	 *
	 * @since 3.5.6
	 *
	 * @return bool True when the share assets should load up-front.
	 */
	protected function should_enqueue_assets() {
		$should = function_exists( 'bp_is_active' )
			&& bp_is_active( 'activity' )
			&& function_exists( 'is_buddypress' )
			&& is_buddypress();

		/**
		 * Filter whether the public share CSS/JS is enqueued up-front.
		 *
		 * @since 3.5.6
		 *
		 * @param bool $should Whether to enqueue the public assets in the head.
		 */
		return (bool) apply_filters( 'bpas_enqueue_public_assets', $should );
	}

	/**
	 * Display share button in front page.
	 *
	 * @access public
	 * @since    1.0.0
	 */
	public function bp_activity_share_button_dis() {
		$all_services = get_site_option( 'bp_share_services' );
		if ( is_user_logged_in() && ! empty( $all_services ) ) {
			add_action( 'bp_activity_entry_meta', array( $this, 'bp_share_activity_filter' ) );
		}
	}

	/**
	 * BP Share activity filter
	 *
	 * @access public
	 * @since    1.0.0
	 */
	public function bp_share_activity_filter() {
		global $activities_template;

		/*
		 * Covers activity loops rendered outside a BuddyPress page (widget,
		 * block, shortcode), where should_enqueue_assets() correctly declined
		 * to load up-front. Both handles are registered in enqueue_styles() /
		 * enqueue_scripts(), so this resolves and simply prints in the footer.
		 * No-ops when the assets are already enqueued.
		 */
		wp_enqueue_style( $this->plugin_name );
		wp_enqueue_script( $this->plugin_name );

		$social_service = get_site_option( 'bp_share_services' );
		$extra_options  = get_site_option( 'bp_share_services_extra' );
		$activity_type  = bp_get_activity_type();
		$activity_link  = bp_get_activity_thread_permalink();
		$activity_title = bp_get_activity_feed_item_title(); // use for description : bp_get_activity_feed_item_description().
		$plugin_path    = plugins_url();
		$mail_subject   = wp_strip_all_tags( $activities_template->activity->action );

		/**
		 * Filter the label on the Share button.
		 *
		 * The default is translated, so a site that only wants the button in
		 * its own language needs no filter at all - this is for sites that
		 * want different wording ("Post to", "Send", ...).
		 *
		 * @since 1.0.0
		 *
		 * @param string $share_button_text Button label.
		 */
		$share_button_text = apply_filters( 'bpas_share_button_text_override', __( 'Share', 'buddypress-share' ) );
		?>
		<div class="bp-share-btn generic-button">
			<a class="button item-button bp-secondary-action bp-share-button" rel="nofollow"><span><?php echo esc_html( $share_button_text ); ?></span></a>
		</div>
		
		<div class="service-buttons <?php echo esc_html( $activity_type ); ?>" style="display: none;">
			<?php
			if ( ! empty( $social_service ) ) {
				if ( isset( $social_service ) && ! empty( $social_service['Facebook'] ) ) {
					echo '<a target="_blank" rel="noopener" href="https://www.facebook.com/sharer.php?u=' . esc_url( $activity_link ) . '" class="bp-share" id="bp_facebook_share"><span class="dashicons dashicons-facebook-alt"></span></a>';
				}
				if ( isset( $social_service ) && ! empty( $social_service['Twitter'] ) ) {
					$twitter_title = rawurlencode( html_entity_decode( get_the_title(), ENT_COMPAT, 'UTF-8' ) );
					echo '<a target="_blank" rel="noopener" class="bp-share" id="bp_twitter_share" href="https://twitter.com/share?url=' . esc_url( $activity_link ) . '&text=' . rawurlencode( $activity_title ) . '"><span class="dashicons dashicons-twitter"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M8 2H1L9.26086 13.0145L1.44995 21.9999H4.09998L10.4883 14.651L16 22H23L14.3917 10.5223L21.8001 2H19.1501L13.1643 8.88578L8 2ZM17 20L5 4H7L19 20H17Z"></path></svg></span></a>';
				}
				if ( isset( $social_service ) && ! empty( $social_service['Pinterest'] ) ) {
					echo '<a target="_blank" rel="noopener" class="bp-share" id="bp_pinterest_share" href="https://pinterest.com/pin/create/bookmarklet/?url=' . esc_url( $activity_link ) . '&description=' . rawurlencode( $activity_title ) . '"><span class="dashicons dashicons-pinterest"></span></a>';
				}
				if ( isset( $social_service ) && ! empty( $social_service['Reddit'] ) ) {
					echo '<a target="_blank" rel="noopener" class="bp-share" id="bp_reddit_share" href="http://reddit.com/submit?url=' . esc_url( $activity_link ) . '&title=' . rawurlencode( $activity_title ) . '"><span class="dashicons dashicons-reddit"></span></a>';
				}
				if ( isset( $social_service ) && ! empty( $social_service['WordPress'] ) ) {
					echo '<a target="_blank" rel="noopener" class="bp-share" id="bp_wordpress_share" href="https://wordpress.com/wp-admin/press-this.php?u=' . esc_url( $activity_link ) . '&t=' . rawurlencode( $activity_title ) . '"><span class="dashicons dashicons-wordpress"></span></a>';
				}
				if ( isset( $social_service ) && ! empty( $social_service['Telegram'] ) ) {
					echo '<a target="_blank" rel="noopener" class="bp-share" id="bp_share_telegram" href="https://t.me/share/url?url=' . esc_url( $activity_link ) . '&text=' . rawurlencode( $activity_title ) . '"><span class="dashicons dashicons-telegram"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M2.14753 11.8099C7.3949 9.52374 10.894 8.01654 12.6447 7.28833C17.6435 5.20916 18.6822 4.84799 19.3592 4.83606C19.5081 4.83344 19.8411 4.87034 20.0567 5.04534C20.2388 5.1931 20.2889 5.39271 20.3129 5.5328C20.3369 5.6729 20.3667 5.99204 20.343 6.2414C20.0721 9.08763 18.9 15.9947 18.3037 19.1825C18.0514 20.5314 17.5546 20.9836 17.0736 21.0279C16.0283 21.1241 15.2345 20.3371 14.2221 19.6735C12.6379 18.635 11.7429 17.9885 10.2051 16.9751C8.42795 15.804 9.58001 15.1603 10.5928 14.1084C10.8579 13.8331 15.4635 9.64397 15.5526 9.26395C15.5637 9.21642 15.5741 9.03926 15.4688 8.94571C15.3636 8.85216 15.2083 8.88415 15.0962 8.9096C14.9373 8.94566 12.4064 10.6184 7.50365 13.928C6.78528 14.4212 6.13461 14.6616 5.55163 14.649C4.90893 14.6351 3.67265 14.2856 2.7536 13.9869C1.62635 13.6204 0.730432 13.4267 0.808447 12.8044C0.849081 12.4803 1.29544 12.1488 2.14753 11.8099Z"></path></svg></span></a>';
				}
				if ( isset( $social_service ) && ! empty( $social_service['Bluesky'] ) ) {
					echo '<a target="_blank" rel="noopener" class="bp-share" id="bp_share_bluesky" href="https://bsky.app/intent/compose?text=' . rawurlencode( 'Check this out! ' . $activity_title . ' ' . $activity_link ) . '"><span class="dashicons dashicons-bluesky"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M12 11.3884C11.0942 9.62673 8.62833 6.34423 6.335 4.7259C4.13833 3.17506 3.30083 3.4434 2.75167 3.69256C2.11583 3.9784 2 4.95506 2 5.52839C2 6.10339 2.315 10.2367 2.52 10.9276C3.19917 13.2076 5.61417 13.9776 7.83917 13.7309C4.57917 14.2142 1.68333 15.4017 5.48083 19.6292C9.65833 23.9542 11.2058 18.7017 12 16.0392C12.7942 18.7017 13.7083 23.7651 18.4442 19.6292C22 16.0392 19.4208 14.2142 16.1608 13.7309C18.3858 13.9784 20.8008 13.2076 21.48 10.9276C21.685 10.2376 22 6.10256 22 5.52923C22 4.95423 21.8842 3.97839 21.2483 3.6909C20.6992 3.44256 19.8617 3.17423 17.665 4.72423C15.3717 6.34506 12.9058 9.62756 12 11.3884Z"></path></svg></span></a>';
				}
				if ( isset( $social_service ) && ! empty( $social_service['Linkedin'] ) ) {
					echo '<a target="_blank" rel="noopener" class="bp-share" id="bp_linkedin_share" href="http://www.linkedin.com/shareArticle?mini=true&url=' . esc_url( $activity_link ) . '&text=' . rawurlencode( $activity_title ) . '"><span class="dashicons dashicons-linkedin"></span></a>';
				}
				if ( isset( $social_service ) && ! empty( $social_service['Whatsapp'] ) ) {
					$whatsapp_share_link = 'https://wa.me/?text=' . rawurlencode( $activity_link );
					echo '<a target="_blank" rel="noopener" class="bp-share" id="bp_whatsapp_share" href="' . esc_url( $whatsapp_share_link ) . '"><span class="dashicons dashicons-whatsapp"></span></a>';
				}
				if ( isset( $social_service ) && ! empty( $social_service['E-mail'] ) ) {
					// Get the site title and URL dynamically.
					$site_title = get_bloginfo( 'name' ); // Fetch the site title.
					$site_url   = home_url();            // Fetch the site URL.

					/*
					 * Plain text destined for rawurlencode() below — do NOT esc_html()
					 * here. Escaping first turns & into &amp;, which rawurlencode then
					 * encodes literally, so the recipient sees "&amp;" in the subject.
					 *
					 * The body is one string rather than one per line: a translator
					 * needs the whole message to get the tone and the line breaks
					 * right, and line-by-line fragments cannot be reordered for
					 * languages that need it.
					 */
					$email_subject = sprintf(
						/* translators: 1: site title, 2: activity title. */
						__( 'New activity on %1$s: %2$s', 'buddypress-share' ),
						$site_title,
						$mail_subject
					);
					$email_body = sprintf(
						/* translators: 1: site title, 2: link to the activity, 3: site home page URL. Keep the blank lines, this is the body of a plain-text email. */
						__( "Hi,\n\nI wanted to share this activity with you from %1\$s:\n\n%2\$s\n\nYou can explore more activities here: %3\$s\n\nBest regards,\nThe %1\$s Team", 'buddypress-share' ),
						$site_title,
						$activity_link,
						$site_url
					);

					// Create the mailto link.
					$email = 'mailto:?subject=' . rawurlencode( $email_subject ) . '&body=' . rawurlencode( $email_body );

					// Output the email share button.
					echo '<a class="bp-share" id="bp_email_share" href="' . esc_url( $email ) . '"><span class="dashicons dashicons-email"></span></a>';
				}
			} else {
				esc_html_e( 'Please enable share services!', 'buddypress-share' );
			}
			/**
			 * Fires after the built-in share targets, so an add-on can append
			 * its own.
			 *
			 * Passes the enabled services. Up to 3.5.6 this argument was
			 * written inline as `$social_service = array()`, which handed every
			 * consumer an empty array (and clobbered the local variable), so
			 * nothing could tell which networks were switched on.
			 *
			 * @since 1.0.0
			 *
			 * @param array  $social_service Enabled share services.
			 * @param string $activity_link  Permalink of the activity.
			 * @param string $activity_title Title of the activity.
			 */
			do_action( 'bp_share_user_services', $social_service, $activity_link, $activity_title );
			?>
		</div>

		<script>
			jQuery(document).ready(function() {
				var pop_active = <?php echo wp_json_encode( isset( $extra_options['bp_share_services_open'] ) ? (string) $extra_options['bp_share_services_open'] : '' ); ?>;
				if (pop_active == 1) {
					// Add popup class to all share links except WhatsApp and Email.
					jQuery('.bp-share').not('#bp_whatsapp_share, #bp_email_share').addClass('has-popup');
				}
			});
		</script>
		<?php
	}

	/**
	 * Displays the language attributes for the ‘html’ tag.
	 *
	 * @param  string $output The type of HTML document. Accepts 'xhtml' or 'html'.
	 */
	public function bp_share_doctype_opengraph( $output ) {
		return $output . '
    xmlns:og="http://opengraphprotocol.org/schema/"
    xmlns:fb="http://www.facebook.com/2008/fbml"';
	}

	/**
	 * Share activity with og meta values
	 */
	public function bp_share_opengraph() {
		global $bp, $post;
		if ( ( bp_is_active( 'activity' ) && bp_is_current_component( 'activity' ) && ! empty( $bp->current_action ) && is_numeric( $bp->current_action ) && bp_is_single_activity() ) ) {
			$activity_img       = null;
			$activity_assets    = array();
			$activity_content   = null;
			$first_img_src      = null;
			$title              = null;
			$og_image           = null;
			$activity_permalink = null;
			$activity_obj       = new BP_Activity_Activity( $bp->current_action );
			$activity_link      = bp_activity_get_permalink( $bp->current_action );
			preg_match_all( '/(src|width|height)=("[^"]*")/', $activity_obj->content, $result );

			if ( isset( $result[2] ) && ! empty( $result[2] ) ) {
				$result_new = array_map(
					function ( $i ) {
						return trim( $i, '"' );
					},
					$result[2]
				);
				foreach ( $result[1] as $key => $result_key ) {
					$activity_assets[ $result_key ] = $result_new[ $key ];
				}
			}
			if ( ! empty( $activity_obj->action ) ) {
				$content = $activity_obj->action;
			} else {
				$content = $activity_obj->content;
			}

			$content = explode( '<span', $content );
			$title   = wp_strip_all_tags( ent2ncr( trim( convert_chars( $content[0] ) ) ) );

			if ( ':' === substr( $title, -1 ) ) {
				$title = substr( $title, 0, -1 );
			}

			$activity_content = preg_replace( '#<ul class="rtmedia-list(.*?)</ul>#', ' ', $activity_obj->content );

			if ( ! empty( $activity_assets['src'] ) ) {
				$activity_content = explode( '<span>', $activity_content );
				$activity_content = wp_strip_all_tags( ent2ncr( trim( convert_chars( $activity_content[1] ) ) ) );
			} else {
				$activity_content = $activity_obj->content;
			}

			preg_match_all( '/<img.*?src\s*=.*?>/', $activity_obj->content, $matches );
			if ( isset( $matches[0][0] ) ) {
				preg_match_all( '/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $matches[0][0], $matches2 );
				if ( isset( $matches2[1][0] ) ) {
					$first_img_src = $matches2[1][0];
				}
			}

			$og_image = '';

			if ( class_exists( 'BP_Media' ) ) {
				$media_ids = bp_activity_get_meta( $activity_obj->id, 'bp_media_ids', true );
				$media_ids = explode( ',', $media_ids );

				if ( ! empty( $media_ids[0] ) ) {
					$media_data = new BP_Media( $media_ids[0] );
					$og_image   = esc_attr( wp_get_attachment_image_url( $media_data->attachment_id, 'full' ) );
				}
			}

			// Youzer media support.
			if ( class_exists( 'Youzer' ) || class_exists( 'Youzify' ) ) {
				$media_ids = ! empty( bp_activity_get_meta( $activity_obj->id, 'yz_attachments', true ) ) ? bp_activity_get_meta( $activity_obj->id, 'yz_attachments', true ) : bp_activity_get_meta( $activity_obj->id, 'youzify_attachments', true );
				if ( ! empty( $media_ids ) ) {
					$media_id = array_key_first( $media_ids );
					$og_image = esc_attr( wp_get_attachment_image_url( $media_id, 'full' ) );
				}
			}

			$activity_content   = wp_strip_all_tags( $activity_content );
			$activity_content   = stripslashes( $activity_content );
			$extra_options      = get_site_option( 'bp_share_services_extra' );
			$enable_user_avatar = false;

			if ( ! empty( $first_img_src ) ) {
				$og_image = $first_img_src;
			}
			?>
				<!-- Existing Open Graph Meta Tags for Facebook -->
				<meta property="og:type" content="article" />
				<meta property="og:url" content="<?php echo esc_url( $activity_link ); ?>" />
				<meta property="og:title" content="<?php echo esc_html( $title ); ?>" />
				<meta property="og:description" content="<?php echo esc_html( $activity_content ); ?>" />
				<meta property="og:image" content="<?php echo esc_url( $og_image ); ?>" />
				<meta property="og:image:secure_url" content="<?php echo esc_url( $og_image ); ?>" />
				<meta property="og:image:width" content="400" />
				<meta property="og:image:height" content="300" />

				<!-- Additional Meta Tags for Twitter Card -->
				<meta name="twitter:card" content="summary_large_image" />
				<meta name="twitter:url" content="<?php echo esc_url( $activity_link ); ?>" />
				<meta name="twitter:title" content="<?php echo esc_html( $title ); ?>" />
				<meta name="twitter:description" content="<?php echo esc_html( $activity_content ); ?>" />
				<meta name="twitter:image" content="<?php echo esc_url( $og_image ); ?>" />

			<?php
		} else {
			return;
		}
	}

	/**
	 * Build :root token overrides for custom icon colors.
	 *
	 * Site owners can override the default brand colors from the Icon
	 * Colors settings screen. Those values are emitted as CSS custom
	 * property overrides and attached to the public stylesheet handle
	 * via wp_add_inline_style() (see enqueue_styles). This replaces the
	 * legacy render-blocking inline <style> block printed in wp_head,
	 * which duplicated stylesheet values, bypassed caching, and was
	 * invisible to CSP style-src rules.
	 *
	 * @since 1.0.0
	 * @return string CSS custom-property override block, or '' when nothing is customized.
	 */
	public function bp_share_icon_custom_color_css() {
		$settings = get_option( 'bpas_icon_color_settings' );
		if ( empty( $settings ) || ! is_array( $settings ) ) {
			return '';
		}

		$map = array(
			'bpas_facebook_bg_color'  => '--bpas-brand-facebook',
			'bpas_twitter_bg_color'   => '--bpas-brand-twitter',
			'bpas_pinterest_bg_color' => '--bpas-brand-pinterest',
			'bpas_linkedin_bg_color'  => '--bpas-brand-linkedin',
			'bpas_reddit_bg_color'    => '--bpas-brand-reddit',
			'bpas_wordpress_bg_color' => '--bpas-brand-wordpress',
			'bpas_telegram_bg_color'  => '--bpas-brand-telegram',
			'bpas_bluesky_bg_color'   => '--bpas-brand-bluesky',
			'bpas_email_bg_color'     => '--bpas-brand-email',
			'bpas_whatsapp_bg_color'  => '--bpas-brand-whatsapp',
		);

		$vars = '';
		foreach ( $map as $option_key => $css_var ) {
			if ( empty( $settings[ $option_key ] ) ) {
				continue;
			}
			$color = sanitize_hex_color( $settings[ $option_key ] );
			if ( empty( $color ) ) {
				continue;
			}
			$vars .= sprintf( '%s:%s;', $css_var, $color );
		}

		return '' !== $vars ? ':root{' . $vars . '}' : '';
	}
}
