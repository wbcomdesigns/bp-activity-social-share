<?php
/**
 * Link-preview tags for shared BuddyPress pages.
 *
 * @package BPAS
 */

namespace BPAS;

defined( 'ABSPATH' ) || exit;

/**
 * Prints Open Graph + Twitter card tags on single activity, profiles, public groups and single posts.
 */
final class Open_Graph {

	/**
	 * Hook registration.
	 */
	public static function init(): void {
		add_action( 'wp_head', array( __CLASS__, 'print_tags' ), 5 );
	}

	/**
	 * Print tags for the current BuddyPress screen.
	 */
	public static function print_tags(): void {
		if ( ! (bool) apply_filters( 'bpas_open_graph_enabled', ! self::seo_plugin_active() ) ) {
			return;
		}

		$ctx = self::current_context();
		if ( ! $ctx || ! Visibility::can_share_externally( $ctx ) ) {
			return;
		}
		// Nothing about an activity the viewer may not read (BuddyBoss serves such permalinks to guests).
		if ( 'activity' === $ctx->type && ! Visibility::viewer_can_read( $ctx->object ) ) {
			return;
		}

		$image = $ctx->preview_image();
		$size  = '' !== $image ? Share_Context::image_meta( $image ) : null;
		$tags  = array(
			'og:type'        => 'member' === $ctx->type ? 'profile' : 'article',
			'og:title'       => $ctx->title,
			'og:description' => $ctx->text,
			'og:url'         => $ctx->url,
			'og:image'       => $image,
			// Sizes let Facebook render the image on the very first share instead of after a fetch.
			'og:image:width'  => $size ? (string) $size['width'] : '',
			'og:image:height' => $size ? (string) $size['height'] : '',
			'og:image:type'   => $size ? $size['mime'] : '',
			'og:image:alt'    => '' !== $image ? $ctx->title : '',
			'og:site_name'   => get_bloginfo( 'name' ),
			'twitter:card'   => '' !== $image ? 'summary_large_image' : 'summary',
		);

		foreach ( array_filter( $tags ) as $property => $value ) {
			$attr = str_starts_with( $property, 'twitter:' ) ? 'name' : 'property';
			$safe = in_array( $property, array( 'og:url', 'og:image' ), true ) ? esc_url( $value ) : esc_attr( $value );
			printf( '<meta %1$s="%2$s" content="%3$s" />' . "\n", esc_attr( $attr ), esc_attr( $property ), $safe ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above.
		}
	}

	/**
	 * Context for the screen being viewed, if it is a shareable BuddyPress page.
	 */
	private static function current_context(): ?Share_Context {
		if ( function_exists( 'bp_is_single_activity' ) && bp_is_single_activity() ) {
			// /members/{user}/activity/{id}/ or /activity/p/{id}/.
			$id = is_numeric( bp_current_action() ) ? (int) bp_current_action() : (int) bp_action_variable( 0 );
			return Share_Context::for_object( 'activity', $id );
		}
		if ( function_exists( 'bp_is_user' ) && bp_is_user() && ! bp_is_single_activity() ) {
			return Share_Context::for_object( 'member', (int) bp_displayed_user_id() );
		}
		if ( function_exists( 'bp_is_group' ) && bp_is_group() ) {
			$group = groups_get_current_group();
			return empty( $group->id ) ? null : Share_Context::for_object( 'group', (int) $group->id );
		}
		// Blog-post activities share the post URL, so the post needs the same preview.
		if ( is_singular() && ! is_front_page() && ! ( function_exists( 'is_buddypress' ) && is_buddypress() ) ) {
			return Share_Context::for_object( 'post', (int) get_queried_object_id() );
		}
		return null;
	}

	/**
	 * True when an SEO plugin already prints Open Graph tags.
	 */
	private static function seo_plugin_active(): bool {
		return defined( 'WPSEO_VERSION' ) || class_exists( 'RankMath' ) || defined( 'AIOSEO_VERSION' ) || defined( 'SEOPRESS_VERSION' );
	}
}
