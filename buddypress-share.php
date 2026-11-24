<?php
/**
 * Plugin Name:       Activity Share for BuddyPress
 * Plugin URI:        https://wbcomdesigns.com/downloads/buddypress-activity-social-share/
 * Description:       Let members and visitors share BuddyPress activity, profiles and groups to social networks, copy the link, or use the phone's share sheet.
 * Version:           3.6.1
 * Requires at least: 6.7
 * Requires PHP:      8.1
 * Author:            Wbcom Designs
 * Author URI:        https://wbcomdesigns.com/
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       bp-activity-social-share
 * Domain Path:       /languages
 *
 * @package BPAS
 */

defined( 'ABSPATH' ) || exit;

// One copy only: stop if this plugin is already loaded from another folder.
if ( defined( 'BP_ACTIVITY_SOCIAL_SHARE_LOADED' ) ) {
	return;
}
define( 'BP_ACTIVITY_SOCIAL_SHARE_LOADED', __FILE__ ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedConstantFound -- One-copy guard is named after the WordPress.org slug.

define( 'BPAS_VERSION', '3.6.1' );
// Public API contract version (major.minor). Pro compares against this.
define( 'BPAS_API_VERSION', '3.6' );
// Schema / migration milestone. Bump with every new migration.
define( 'BPAS_DB_VERSION', '3.6.0' );
define( 'BPAS_FILE', __FILE__ );
define( 'BPAS_DIR', plugin_dir_path( __FILE__ ) );
define( 'BPAS_URL', plugin_dir_url( __FILE__ ) );

require_once BPAS_DIR . 'includes/class-autoloader.php';
require_once BPAS_DIR . 'includes/functions.php';

BPAS\Autoloader::register( 'BPAS', BPAS_DIR . 'includes' );

add_action( 'plugins_loaded', array( 'BPAS\Plugin', 'boot' ) );
// Registered here, not in Admin::init(): the activation request ends before plugins_loaded boots the admin.
add_action( 'activated_plugin', array( 'BPAS\Admin\Admin', 'activation_redirect' ) );
