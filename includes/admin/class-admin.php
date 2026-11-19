<?php
/**
 * Admin page: WB Plugins > Activity Share.
 *
 * @package BPAS
 */

namespace BPAS\Admin;

use BPAS\Networks;
use BPAS\Settings;
use BPAS\Share_Context;
use BPAS\Share_Menu;

defined( 'ABSPATH' ) || exit;

/**
 * One page, sidebar tabs (Free: Networks, Appearance; Pro adds its own via bpas_admin_tabs).
 */
final class Admin {

	const SLUG = 'buddypress-share'; // Kept from 3.5.x so existing bookmarks still work.
	const HUB  = 'wbcomplugins';

	/**
	 * Hook registration.
	 */
	public static function init(): void {
		add_action( 'admin_menu', array( __CLASS__, 'menu' ) );
		add_action( 'admin_menu', array( __CLASS__, 'takeover_hub_landing' ), 999 );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue' ) );
		add_action( 'in_admin_header', array( __CLASS__, 'suppress_foreign_notices' ), 1 );
		add_filter( 'plugin_action_links_' . plugin_basename( BPAS_FILE ), array( __CLASS__, 'action_links' ) );
	}

	/**
	 * Open the settings page after a single activation of Free or Pro (as 3.5.x did); Pro lands on its
	 * own Features tab. Bulk, network and WP-CLI activations stay put.
	 *
	 * @param string $plugin Plugin basename that was activated.
	 */
	public static function activation_redirect( string $plugin ): void {
		$targets = array(
			plugin_basename( BPAS_FILE ) => '',
			\BPAS\Plugin::PRO_FILE     => '&tab=features',
		);
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- core verified the activation nonce; this only reads which screen ran it.
		if ( ! isset( $targets[ $plugin ] ) || 'activate' !== sanitize_key( wp_unslash( $_REQUEST['action'] ?? '' ) ) || is_network_admin() || ! current_user_can( 'manage_options' ) || \BPAS\Plugin::blocker() ) {
			return;
		}
		wp_safe_redirect( admin_url( 'admin.php?page=' . self::SLUG . $targets[ $plugin ] ) );
		exit;
	}

	/**
	 * Shared hub (created by the first Wbcom plugin) + our submenu.
	 */
	public static function menu(): void {
		if ( empty( $GLOBALS['admin_page_hooks'][ self::HUB ] ) ) {
			add_menu_page( __( 'WB Plugins', 'bp-activity-social-share' ), __( 'WB Plugins', 'bp-activity-social-share' ), 'manage_options', self::HUB, array( __CLASS__, 'render_hub' ), 'dashicons-lightbulb', 59 );
		}
		add_submenu_page( self::HUB, __( 'Activity Share', 'bp-activity-social-share' ), __( 'Activity Share', 'bp-activity-social-share' ), 'manage_options', self::SLUG, array( __CLASS__, 'render_page' ) );
	}

	/**
	 * Portfolio standard: modern plugins render the hub landing (peer list).
	 */
	public static function takeover_hub_landing(): void {
		if ( empty( $GLOBALS['admin_page_hooks'][ self::HUB ] ) ) {
			return;
		}
		remove_all_actions( 'toplevel_page_' . self::HUB );
		add_action( 'toplevel_page_' . self::HUB, array( __CLASS__, 'render_hub' ) );
	}

	/**
	 * Hide third-party notices on our screen and the hub only.
	 */
	public static function suppress_foreign_notices(): void {
		if ( self::is_our_screen() ) {
			remove_all_actions( 'admin_notices' );
			remove_all_actions( 'all_admin_notices' );
		}
	}

	/**
	 * Tabs: Free's two, Pro appends. `icon` is a dashicon class; `group` is the sidebar
	 * section label (defaults to "Settings"), and a new group starts a new section.
	 *
	 * @return array<string,array{label:string,render:callable,icon?:string,group?:string}>
	 */
	public static function tabs(): array {
		$tabs = array(
			'networks'   => array(
				'label'  => __( 'Networks', 'bp-activity-social-share' ),
				'render' => array( __CLASS__, 'render_networks' ),
				'icon'   => 'dashicons-share',
			),
			'appearance' => array(
				'label'  => __( 'Appearance', 'bp-activity-social-share' ),
				'render' => array( __CLASS__, 'render_appearance' ),
				'icon'   => 'dashicons-admin-appearance',
			),
		);
		return (array) apply_filters( 'bpas_admin_tabs', $tabs );
	}

	/**
	 * Current tab key.
	 */
	public static function current_tab(): string {
		$tab = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : 'networks'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- navigation only.
		return array_key_exists( $tab, self::tabs() ) ? $tab : 'networks';
	}

	/**
	 * Page shell.
	 */
	public static function render_page(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$tabs    = self::tabs();
		$current = self::current_tab();
		include BPAS_DIR . 'includes/admin/views/layout.php';
	}

	/**
	 * Notice inside our own page when the Activity component is off. Drawn here, not on admin_notices:
	 * suppress_foreign_notices() empties that hook on this screen.
	 */
	public static function render_component_notice(): void {
		if ( ! function_exists( 'bp_is_active' ) || bp_is_active( 'activity' ) ) {
			return;
		}
		$components = function_exists( 'bp_get_admin_url' )
			? bp_get_admin_url( add_query_arg( 'page', 'bp-components', 'admin.php' ) )
			: admin_url( 'admin.php?page=bp-components' );
		?>
		<div class="notice notice-warning inline">
			<p>
				<?php
				echo wp_kses(
					sprintf(
						/* translators: %s: link to BuddyPress > Settings > Components. */
						__( 'The BuddyPress Activity component is off. Sharing still works on groups and member profiles, but sharing from activity posts and reposting (Pro) are off until you turn Activity on in %s.', 'bp-activity-social-share' ),
						'<a href="' . esc_url( $components ) . '">' . esc_html__( 'BuddyPress > Settings > Components', 'bp-activity-social-share' ) . '</a>'
					),
					array(
						'a' => array( 'href' => array() ),
					)
				);
				?>
			</p>
		</div>
		<?php
	}

	/**
	 * Networks tab.
	 */
	public static function render_networks(): void {
		$settings = Settings::all();
		$all      = Networks::all();
		$ordered  = array();
		foreach ( $settings['networks'] as $slug ) {
			if ( isset( $all[ $slug ] ) ) {
				$ordered[ $slug ] = $all[ $slug ];
			}
		}
		$ordered   += $all;
		$pro_active = defined( 'BPAS_PRO_VERSION' );
		include BPAS_DIR . 'includes/admin/views/tab-networks.php';
	}

	/**
	 * Appearance tab.
	 */
	public static function render_appearance(): void {
		$settings = Settings::all();
		$sample   = new Share_Context( 'post', 0, (object) array(), home_url( '/' ), get_bloginfo( 'name' ) );
		$preview  = Share_Menu::render( $sample );
		include BPAS_DIR . 'includes/admin/views/tab-appearance.php';
	}

	/**
	 * Shared WB Plugins hub landing.
	 */
	public static function render_hub(): void {
		include BPAS_DIR . 'includes/admin/views/hub.php';
	}

	/**
	 * Admin assets on our screen + the hub.
	 */
	public static function enqueue(): void {
		if ( ! self::is_our_screen() ) {
			return;
		}
		$min = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
		wp_enqueue_style( 'bpas-admin', BPAS_URL . "assets/css/bpas-admin{$min}.css", array(), BPAS_VERSION );
		wp_style_add_data( 'bpas-admin', 'rtl', 'replace' );
		wp_style_add_data( 'bpas-admin', 'suffix', $min );

		if ( self::HUB === sanitize_key( wp_unslash( $_GET['page'] ?? '' ) ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- screen check only.
			return;
		}

		\BPAS\Assets::enqueue(); // Live preview uses the real menu.
		wp_enqueue_script( 'bpas-admin', BPAS_URL . "assets/js/bpas-admin{$min}.js", array( 'wp-api-fetch' ), BPAS_VERSION, array( 'in_footer' => true ) );
		wp_add_inline_script(
			'bpas-admin',
			'window.bpasAdmin = ' . wp_json_encode(
				array(
					'path' => '/' . bpas_rest_namespace() . '/settings',
					'i18n' => array(
						'saved'  => __( 'Settings saved.', 'bp-activity-social-share' ),
						'failed' => __( 'Settings could not be saved. Please try again.', 'bp-activity-social-share' ),
						'saving' => __( 'Saving...', 'bp-activity-social-share' ),
						/* translators: 1: network name, 2: position number. */
						'moved'  => __( '%1$s moved to position %2$d.', 'bp-activity-social-share' ),
						'share'  => _x( 'Share', 'button: share this item', 'bp-activity-social-share' ),
					),
				)
			) . ';',
			'before'
		);
	}

	/**
	 * "Settings" link on the Plugins screen.
	 *
	 * @param array $links Links.
	 */
	public static function action_links( array $links ): array {
		array_unshift( $links, sprintf( '<a href="%s">%s</a>', esc_url( admin_url( 'admin.php?page=' . self::SLUG ) ), esc_html__( 'Settings', 'bp-activity-social-share' ) ) );
		return $links;
	}

	/**
	 * Our page or the hub landing.
	 */
	private static function is_our_screen(): bool {
		$page = isset( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- screen check only.
		return is_admin() && in_array( $page, array( self::SLUG, self::HUB ), true );
	}
}
