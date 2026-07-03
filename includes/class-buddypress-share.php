<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://wbcomdesigns.com
 * @since      1.0.0
 *
 * @package    Buddypress_Share
 * @subpackage Buddypress_Share/includes
 */

defined( 'ABSPATH' ) || exit;

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Buddypress_Share
 * @subpackage Buddypress_Share/includes
 * @author     Wbcom Designs <admin@wbcomdesigns.com>
 */
class Buddypress_Share {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Buddypress_Share_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;


	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @access public
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->plugin_name = 'buddypress-share';
		$this->version     = defined( 'BP_SHARE_VERSION' ) ? BP_SHARE_VERSION : '3.5.6';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Buddypress_Share_Loader. Orchestrates the hooks of the plugin.
	 * - Buddypress_Share_i18n. Defines internationalization functionality.
	 * - Buddypress_Share_Admin. Defines all hooks for the admin area.
	 * - Buddypress_Share_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( __DIR__ ) . 'includes/class-buddypress-share-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( __DIR__ ) . 'includes/class-buddypress-share-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 * Retained post-migration: it still owns the General + Icon Colors tab
		 * content renderers, the settings registration, and the AJAX save paths.
		 * Its menu + enqueue + notice-suppression methods are no longer hooked —
		 * BPAS_Admin (the card-panel chrome) owns those as of 3.5.6.
		 */
		require_once plugin_dir_path( __DIR__ ) . 'admin/class-buddypress-share-admin.php';

		/**
		 * The card-panel admin chrome (menu, page shell, sidebar nav, enqueue).
		 * Replaces the legacy admin/wbcom/ wrapper. See
		 * includes/admin/class-bpas-admin.php.
		 */
		require_once plugin_dir_path( __DIR__ ) . 'includes/admin/class-bpas-admin.php';

		/**
		 * The class responsible for display admin notice for revirew after 7 days.
		 */
		require_once plugin_dir_path( __DIR__ ) . 'admin/class-buddypress-share-feedback.php';

		/**
		 * The class responsible for defining custom settings
		 * of the plugin.
		 */
		// Previously: require_once for class-buddypress-share-settings.php.
		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( __DIR__ ) . 'public/class-buddypress-share-public.php';

		$this->loader = new Buddypress_Share_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Buddypress_Share_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Buddypress_Share_i18n();

		$this->loader->add_action( 'init', $plugin_i18n, 'bp_share_load_plugin_textdomain' );
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {
		$plugin_admin = new Buddypress_Share_Admin( $this->get_plugin_name(), $this->get_version() );

		// Settings registration (Settings API) — still owned by the legacy
		// service. Preserves the bp_share_services_extra + bpas_icon_color_settings
		// option groups byte-for-byte.
		$this->loader->add_action( 'admin_init', $plugin_admin, 'bp_share_settings_init' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'bpas_icon_color_register_setting' );

		// AJAX save paths for the General tab (services enable/disable + sort).
		// Each verifies bp_share_nonce + current_user_can( 'manage_options' ).
		$this->loader->add_action( 'wp_ajax_bp_share_chb_services_ajax', $plugin_admin, 'bp_share_chb_services_ajax' );
		$this->loader->add_action( 'wp_ajax_bp_share_sort_social_links_ajax', $plugin_admin, 'bp_share_sort_social_links_ajax' );
		$this->loader->add_action( 'wp_ajax_wss_social_icons', $plugin_admin, 'wss_social_icons' );
		$this->loader->add_action( 'wp_ajax_wss_social_remove_icons', $plugin_admin, 'wss_social_remove_icons' );

		// Card-panel admin chrome: menu + page shell + enqueue + notice
		// suppression. Replaces the legacy enqueue_styles / enqueue_scripts /
		// bp_share_plugin_menu / wbcom_hide_all_admin_notices_from_setting_page
		// wiring (those methods on the legacy service are no longer hooked).
		$panel = new BPAS_Admin( $plugin_admin );
		$panel->register();
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {
		$plugin_public = new Buddypress_Share_Public( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_filter( 'language_attributes', $plugin_public, 'bp_share_doctype_opengraph' );
		$this->loader->add_action( 'wp_head', $plugin_public, 'bp_share_opengraph', 999 );
		$this->loader->add_action( 'bp_init', $plugin_public, 'bp_activity_share_button_dis' );
		$this->loader->add_action( 'wp_head', $plugin_public, 'bp_share_icon_custom_color' );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @access public
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @access public
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @access public
	 * @return    Buddypress_Share_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @access public
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}
}
