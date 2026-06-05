<?php
/**
 * BPAS Admin: menu, enqueue, page rendering (card-panel).
 *
 * Replaces the legacy Wbcom wrapper chrome (admin/wbcom/) and the
 * nav-tab-wrapper settings page that lived in
 * Buddypress_Share_Admin::bp_share_plugin_options(). Follows the
 * wp-plugin-development skill Part 6 card-panel pattern and the
 * references/wbcom-wrapper-migration.md playbook (Parts 5, 6, 15).
 *
 * This is a "reskin the chrome" migration (playbook Part 0.3): the
 * General + Icon Colors tab CONTENT renderers + their JS/CSS + the
 * AJAX save paths are unchanged and still live on
 * Buddypress_Share_Admin. This class only owns the new chrome
 * (menu, page shell, sidebar nav, enqueue) and delegates each tab's
 * body to the retained renderer.
 *
 * @package    Buddypress_Share
 * @subpackage Buddypress_Share/includes/admin
 * @since      3.5.6
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class BPAS_Admin
 *
 * @since 3.5.6
 */
class BPAS_Admin {

	/**
	 * Menu slug — kept identical to the legacy page so existing
	 * bookmarks, the plugin-row "Settings" link, and the activation
	 * redirect (admin.php?page=buddypress-share) all keep working.
	 *
	 * @var string
	 */
	const MENU_SLUG = 'buddypress-share';

	/**
	 * Retained legacy admin service that still owns the tab CONTENT
	 * renderers + AJAX save paths. Injected so the new chrome can
	 * delegate each tab body to it.
	 *
	 * @since 3.5.6
	 * @var Buddypress_Share_Admin
	 */
	private $service;

	/**
	 * Constructor.
	 *
	 * @param Buddypress_Share_Admin $service Retained legacy admin service.
	 */
	public function __construct( $service ) {
		$this->service = $service;
	}

	/**
	 * Sidebar tabs — single source of truth for the nav. Keys are tab
	 * slugs; the legacy slugs (bpas_welcome / bpas_general_settings /
	 * bpas_icon_color_settings) are preserved so the General-tab JS,
	 * which keys off ?tab=bpas_general_settings, keeps working and old
	 * bookmarked tab URLs still resolve.
	 *
	 * @return array<string, array{label:string, icon:string, group:string}>
	 */
	public static function get_tabs() {
		$tabs = array(
			'bpas_welcome'             => array(
				'label' => __( 'Welcome', 'buddypress-share' ),
				'icon'  => 'dashicons-megaphone',
				'group' => 'main',
			),
			'bpas_general_settings'    => array(
				'label' => __( 'General Settings', 'buddypress-share' ),
				'icon'  => 'dashicons-share',
				'group' => 'settings',
			),
			'bpas_icon_color_settings' => array(
				'label' => __( 'Icon Colors', 'buddypress-share' ),
				'icon'  => 'dashicons-art',
				'group' => 'settings',
			),
		);
		return apply_filters( 'bpas_admin_tabs', $tabs );
	}

	/**
	 * Bootstrap hooks.
	 *
	 * @return void
	 */
	public function register() {
		add_action( bp_core_admin_hook(), array( $this, 'add_menu' ) );
		// Priority 999 runs after every other plugin has registered its
		// admin_menu entries — reclaim the hub landing render when a
		// legacy wbcom-wrapper peer plugin registered wbcomplugins first.
		add_action( 'admin_menu', array( $this, 'takeover_hub_landing' ), 999 );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		add_action( 'in_admin_header', array( $this, 'suppress_foreign_notices' ), 1 );
	}

	/**
	 * Attach a single submenu under the shared WB Plugins hub.
	 *
	 * @return void
	 */
	public function add_menu() {
		$cap = 'manage_options';

		// First Wbcom plugin to load creates the shared WB Plugins hub.
		if ( empty( $GLOBALS['admin_page_hooks']['wbcomplugins'] ) ) {
			add_menu_page(
				esc_html__( 'WB Plugins', 'buddypress-share' ),
				esc_html__( 'WB Plugins', 'buddypress-share' ),
				$cap,
				'wbcomplugins',
				array( $this, 'render_hub' ),
				'dashicons-lightbulb',
				59
			);
		}

		add_submenu_page(
			'wbcomplugins',
			esc_html__( 'BuddyPress Activity Social Share', 'buddypress-share' ),
			esc_html__( 'Activity Social Share', 'buddypress-share' ),
			$cap,
			self::MENU_SLUG,
			array( $this, 'render_page' )
		);
	}

	/**
	 * Force the shared hub landing to render our card-panel dashboard,
	 * even when a legacy wbcom-wrapper peer registered wbcomplugins
	 * first. See playbook Part 15.
	 *
	 * @return void
	 */
	public function takeover_hub_landing() {
		global $admin_page_hooks;
		if ( empty( $admin_page_hooks['wbcomplugins'] ) ) {
			return;
		}
		remove_all_actions( 'toplevel_page_wbcomplugins' );
		add_action( 'toplevel_page_wbcomplugins', array( $this, 'render_hub' ) );
	}

	/**
	 * Enqueue admin assets only on our screen + the shared hub landing.
	 *
	 * Loads the new card-panel chrome (admin.css / admin.js) AND the
	 * retained content layer used by the General tab — the drag/drop
	 * enable/disable lists (buddypress-share-admin.js) and their styling
	 * (buddypress-share-admin.css), plus the jQuery UI + color-picker
	 * dependencies. Keeping the content layer intact is what makes this
	 * a reskin rather than a rebuild (playbook Part 0.3).
	 *
	 * @param string $hook_suffix Current admin page hook.
	 * @return void
	 */
	public function enqueue_assets( $hook_suffix ) {
		unset( $hook_suffix );
		$screen = get_current_screen();
		if ( ! $screen || ! $this->is_our_screen( $screen ) ) {
			return;
		}

		$version = defined( 'BP_SHARE_VERSION' ) ? BP_SHARE_VERSION : '1.0.0';

		// New card-panel chrome.
		wp_enqueue_style(
			'bpas-admin',
			BP_ACTIVITY_SHARE_PLUGIN_URL . 'assets/css/admin.css',
			array(),
			$version
		);
		wp_enqueue_script(
			'bpas-admin',
			BP_ACTIVITY_SHARE_PLUGIN_URL . 'assets/js/admin.js',
			array( 'jquery', 'wp-color-picker' ),
			$version,
			true
		);
		wp_localize_script(
			'bpas-admin',
			'bpasAdmin',
			array(
				'i18n' => array(
					'confirmContinue' => __( 'Continue', 'buddypress-share' ),
					'confirmCancel'   => __( 'Cancel', 'buddypress-share' ),
				),
			)
		);

		// Retained content layer for the General tab (drag/drop lists,
		// AJAX save). Loaded only on our screen, same as before.
		if ( ! wp_style_is( 'font-awesome', 'enqueued' ) ) {
			wp_enqueue_style( 'font-awesome', BP_ACTIVITY_SHARE_PLUGIN_URL . 'assets/css/font-awesome.min.css', array(), '4.7.0', 'all' );
		}
		wp_enqueue_style(
			'buddypress-share',
			BP_ACTIVITY_SHARE_PLUGIN_URL . 'admin/css/buddypress-share-admin.css',
			array(),
			$version
		);

		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'jquery-ui-sortable' );
		wp_enqueue_script( 'jquery-ui-draggable' );
		wp_enqueue_script( 'jquery-ui-droppable' );
		wp_enqueue_script(
			'buddypress-share',
			BP_ACTIVITY_SHARE_PLUGIN_URL . 'admin/js/buddypress-share-admin.js',
			array( 'jquery', 'jquery-ui-sortable', 'jquery-ui-draggable', 'jquery-ui-droppable' ),
			$version,
			true
		);
		wp_localize_script(
			'buddypress-share',
			'my_ajax_object',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( 'bp_share_nonce' ),
			)
		);
	}

	/**
	 * Suppress 3rd-party admin notices on our screen.
	 *
	 * @return void
	 */
	public function suppress_foreign_notices() {
		$screen = get_current_screen();
		if ( ! $screen || ! $this->is_our_screen( $screen ) ) {
			return;
		}
		remove_all_actions( 'admin_notices' );
		remove_all_actions( 'all_admin_notices' );
	}

	/**
	 * True if the current screen is the plugin's admin page OR the
	 * shared hub landing (our callback owns the hub render too).
	 *
	 * @param WP_Screen $screen Current screen.
	 * @return bool
	 */
	private function is_our_screen( $screen ) {
		if ( empty( $screen->id ) ) {
			return false;
		}
		return (bool) preg_match( '/_page_' . preg_quote( self::MENU_SLUG, '/' ) . '$/', $screen->id )
			|| 'toplevel_page_wbcomplugins' === $screen->id;
	}

	/**
	 * Render the shared WB Plugins hub landing page.
	 *
	 * @return void
	 */
	public function render_hub() {
		$view = BP_ACTIVITY_SHARE_PLUGIN_PATH . 'includes/admin/views/hub.php';
		if ( file_exists( $view ) ) {
			include $view;
		}
	}

	/**
	 * Render the single admin page. Routes to the active tab.
	 *
	 * @return void
	 */
	public function render_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'buddypress-share' ) );
		}

		$bpas_tabs = self::get_tabs();
		$tab_slugs = array_keys( $bpas_tabs );
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only tab switch.
		$active = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : $tab_slugs[0];
		if ( ! isset( $bpas_tabs[ $active ] ) ) {
			$active = $tab_slugs[0];
		}

		$page_url = admin_url( 'admin.php?page=' . self::MENU_SLUG );
		$service  = $this->service;

		$shell = BP_ACTIVITY_SHARE_PLUGIN_PATH . 'includes/admin/views/shell.php';
		include $shell;
	}

	/**
	 * Render the body for the active tab by delegating to the retained
	 * legacy content renderer. Each renderer echoes the SAME markup it
	 * did under the old wrapper, so the General-tab drag/drop JS + the
	 * icon-color form + their save paths are byte-for-byte unchanged.
	 *
	 * @param string                 $active  Active tab slug.
	 * @param Buddypress_Share_Admin $service Retained legacy admin service.
	 * @return void
	 */
	public static function render_tab_body( $active, $service ) {
		switch ( $active ) {
			case 'bpas_general_settings':
				$service->bpas_general_setting_section();
				break;
			case 'bpas_icon_color_settings':
				$service->bpas_icon_color_setting_section();
				break;
			case 'bpas_welcome':
			default:
				$service->bpas_welcome_section();
				break;
		}
	}
}
