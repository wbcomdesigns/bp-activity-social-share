<?php
/**
 * Public API.
 *
 * The ONLY functions Activity Share Pro, themes and third-party code may call.
 * Signatures here are a contract: keep them stable for at least one minor
 * version after deprecating (see docs/website/developer-guide/).
 *
 * @package BPAS
 */

defined( 'ABSPATH' ) || exit;

/**
 * Plugin version.
 */
function bpas_version(): string {
	return BPAS_VERSION;
}

/**
 * Public API contract version (major.minor).
 */
function bpas_api_version(): string {
	return BPAS_API_VERSION;
}

/**
 * Register a namespace prefix with the shared autoloader (used by Pro).
 *
 * @param string $prefix Namespace prefix, e.g. 'BPAS_Pro'.
 * @param string $dir    Directory holding the classes.
 */
function bpas_register_autoload( string $prefix, string $dir ): void {
	BPAS\Autoloader::register( $prefix, $dir );
}

/**
 * Read one Free setting (merged with defaults).
 *
 * @param string $key     Setting key.
 * @param mixed  $default Returned when the key is unknown.
 * @return mixed
 */
function bpas_get_setting( string $key, $default = null ) {
	return BPAS\Settings::get( $key, $default );
}

/**
 * Registered share networks.
 *
 * @param bool $enabled_only True = only those the owner enabled, in the owner's order.
 * @return array<string,array> Keyed by slug.
 */
function bpas_networks( bool $enabled_only = true ): array {
	return $enabled_only ? BPAS\Networks::enabled() : BPAS\Networks::all();
}

/**
 * Register a share network. Call inside the `bpas_register_networks` action.
 *
 * @param string $slug Unique slug (a-z, 0-9, dash).
 * @param array  $args label, share_url (https, placeholders {url} {title} {text} {image}), icon (inline SVG) or icon_url, color, mobile.
 * @return true|WP_Error
 */
function bpas_register_network( string $slug, array $args ) {
	return BPAS\Networks::register( $slug, $args );
}

/**
 * Remove a registered network.
 *
 * @param string $slug Network slug.
 */
function bpas_unregister_network( string $slug ): bool {
	return BPAS\Networks::unregister( $slug );
}

/**
 * Build the share context for an object.
 *
 * @param string $type 'activity' | 'post' | 'member' | 'group'.
 * @param int    $id   Object ID.
 */
function bpas_share_context( string $type, int $id ): ?BPAS\Share_Context {
	return BPAS\Share_Context::for_object( $type, $id );
}

/**
 * Share context from an activity (or reply) object already loaded - no extra query.
 *
 * @param object $activity Activity row from a loop, or BP_Activity_Activity.
 */
function bpas_activity_share_context( object $activity ): ?BPAS\Share_Context {
	return BPAS\Share_Context::from_activity( $activity );
}

/**
 * Whether the object may be shared outside the community (networks, public link).
 *
 * @param BPAS\Share_Context $ctx Context.
 */
function bpas_can_share_externally( BPAS\Share_Context $ctx ): bool {
	return BPAS\Visibility::can_share_externally( $ctx );
}

/**
 * Render the share menu for a context.
 *
 * @param BPAS\Share_Context $ctx  Context.
 * @param array              $args label, position, class.
 * @return string HTML ('' when nothing may be shown).
 */
function bpas_render_share_menu( BPAS\Share_Context $ctx, array $args = array() ): string {
	return BPAS\Share_Menu::render( $ctx, $args );
}

/**
 * Decorative inline SVG from the shared icon set (assets/icons/ui).
 *
 * @param string $name Icon name, e.g. 'repeat', 'send', 'users'.
 */
function bpas_icon( string $name ): string {
	return BPAS\Icons::get( $name, 'ui' );
}

/**
 * Enqueue the share menu assets early (e.g. on a page that will render a menu).
 */
function bpas_enqueue_assets(): void {
	BPAS\Assets::enqueue();
}

/**
 * A member's profile URL on BuddyPress 12+ and on BuddyBoss / older BuddyPress.
 *
 * @param int $user_id Member.
 */
function bpas_member_url( int $user_id ): string {
	if ( function_exists( 'bp_members_get_user_url' ) ) {
		return (string) bp_members_get_user_url( $user_id );
	}
	return function_exists( 'bp_core_get_user_domain' ) ? (string) bp_core_get_user_domain( $user_id ) : '';
}

/**
 * A group's URL on BuddyPress 12+ and on BuddyBoss / older BuddyPress.
 *
 * @param object $group Group.
 */
function bpas_group_url( object $group ): string {
	if ( function_exists( 'bp_get_group_url' ) ) {
		return (string) bp_get_group_url( $group );
	}
	return function_exists( 'bp_get_group_permalink' ) ? (string) bp_get_group_permalink( $group ) : '';
}

/**
 * REST namespace shared by Free and Pro.
 */
function bpas_rest_namespace(): string {
	return 'bpas/v1';
}
