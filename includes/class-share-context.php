<?php
/**
 * What is being shared.
 *
 * @package BPAS
 */

namespace BPAS;

defined( 'ABSPATH' ) || exit;

/**
 * Value object: url, title, text, image of one shareable object.
 */
final class Share_Context {

	/**
	 * Smallest side, in pixels, Facebook accepts for a link-preview image.
	 */
	private const MIN_PREVIEW = 200;

	/**
	 * Build a context.
	 *
	 * @param string $type   'activity' | 'post' | 'member' | 'group'.
	 * @param int    $id     Object ID.
	 * @param object $object Loaded object (activity, post, user, group).
	 * @param string $url    Canonical URL.
	 * @param string $title  Title (stored as plain text: tags stripped, entities decoded; escape on output).
	 * @param string $text   Short plain-text excerpt (same rule).
	 * @param string $image  Image URL ('' when none).
	 */
	public function __construct(
		public readonly string $type,
		public readonly int $id,
		public readonly object $object,
		public string $url,
		public string $title,
		public string $text = '',
		public string $image = ''
	) {
		$this->title = self::plain( $title );
		$this->text  = self::plain( $text );
	}

	/**
	 * Plain text for a share field. Every consumer escapes for its own context (attribute, URL,
	 * email), so an entity left encoded here would show up literally, e.g. "Tom &amp; Jerry".
	 *
	 * @param string $value Text that may hold tags or HTML entities.
	 */
	private static function plain( string $value ): string {
		return trim( html_entity_decode( wp_strip_all_tags( $value ), ENT_QUOTES | ENT_HTML5, 'UTF-8' ) );
	}

	/**
	 * Activity title as the viewer sees it on the page: the stored action run through the same
	 * filters BuddyPress applies when it renders it (minus the timestamp), so plugins that hide or
	 * rename the author (anonymous posting, moderation) apply to the share too. Never falls back to
	 * the stored author's name.
	 *
	 * @param object $activity Activity.
	 */
	private static function activity_title( object $activity ): string {
		$action = (string) $activity->action;
		if ( '' !== $action ) {
			$action = self::in_loop(
				$activity,
				static function () use ( $action, $activity ) {
					$args   = array( 'no_timestamp' => true );
					$action = (string) apply_filters_ref_array( 'bp_get_activity_action_pre_meta', array( $action, &$activity, $args ) ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- BuddyPress core hook.
					return (string) apply_filters_ref_array( 'bp_get_activity_action', array( $action, &$activity, $args ) ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- BuddyPress core hook.
				}
			);
		}
		$title = self::plain( $action );
		return '' !== $title ? $title : __( 'Shared update', 'bp-activity-social-share' );
	}

	/**
	 * Activity text as the current viewer sees it on the page: the stored content run through the
	 * filters BuddyPress applies when it renders the body, so plugins that hide or coarsen part of
	 * it for some viewers (a check-in's street address, moderation) apply to the share and the
	 * link preview too. Never the raw stored content. A link-preview crawler is a guest.
	 *
	 * @param object $activity Activity.
	 */
	private static function activity_text( object $activity ): string {
		$content = (string) $activity->content;
		if ( '' === $content ) {
			return '';
		}
		$html = self::in_loop(
			$activity,
			static function () use ( $content, $activity ) {
				// The full body, not the stream's "[Read more]" cut (BuddyPress drops it the same way for REST).
				$truncate = remove_filter( 'bp_get_activity_content_body', 'bp_activity_truncate_entry', 5 );
				$html     = (string) apply_filters_ref_array( 'bp_get_activity_content_body', array( $content, &$activity ) ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- BuddyPress core hook.
				if ( $truncate ) {
					add_filter( 'bp_get_activity_content_body', 'bp_activity_truncate_entry', 5 );
				}
				return $html;
			}
		);
		return self::excerpt( $html );
	}

	/**
	 * Run BuddyPress render filters for one activity. Their callbacks (secondary avatars, theme and
	 * add-on hooks) read the loop global, which does not exist in <head> or REST: point it at this
	 * activity while they run, then restore it.
	 *
	 * @param object   $activity Activity.
	 * @param callable $render   Returns the filtered string.
	 */
	private static function in_loop( object $activity, callable $render ): string {
		global $activities_template;
		$saved   = $activities_template ?? null;
		$swapped = (int) ( $saved->activity->id ?? 0 ) !== (int) $activity->id;
		if ( $swapped ) {
			$activities_template = (object) array( 'activity' => $activity ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited, WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- BuddyPress loop global, restored below.
		}
		$out = (string) $render();
		if ( $swapped ) {
			$activities_template = $saved; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited, WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- restoring the BuddyPress loop global.
		}
		return $out;
	}

	/**
	 * Context for a supported object, or null when it does not exist.
	 *
	 * @param string $type Object type.
	 * @param int    $id   Object ID.
	 */
	public static function for_object( string $type, int $id ): ?self {
		$ctx = match ( $type ) {
			'activity' => self::activity( $id ),
			'post'     => self::post( $id ),
			'member'   => self::member( $id ),
			'group'    => self::group( $id ),
			default    => null,
		};
		return $ctx ? apply_filters( 'bpas_share_context', $ctx ) : null;
	}

	/**
	 * Context from an activity object already loaded by the loop (no extra query).
	 *
	 * @param object $activity Activity row from $activities_template or BP_Activity_Activity.
	 */
	public static function from_activity( object $activity ): ?self {
		if ( empty( $activity->id ) || ! function_exists( 'bp_activity_get_permalink' ) ) {
			return null;
		}
		// Content-less system lines ("joined the group", "became friends") have nothing worth sharing;
		// members and groups are shared from their own headers.
		$shareable = '' !== trim( wp_strip_all_tags( (string) $activity->content ) )
			|| in_array( $activity->type, array( 'activity_share', 'post_share', 'new_blog_post' ), true );
		/**
		 * Whether an activity item gets the share menu.
		 *
		 * @param bool   $shareable Default: it has content, or is a repost or blog post.
		 * @param object $activity  Activity.
		 */
		if ( ! apply_filters( 'bpas_is_shareable_activity', $shareable, $activity ) ) {
			return null;
		}
		return apply_filters( 'bpas_share_context', self::build_activity( $activity ) );
	}

	/**
	 * Activity context by ID.
	 *
	 * @param int $id Activity ID.
	 */
	private static function activity( int $id ): ?self {
		if ( ! function_exists( 'bp_activity_get_permalink' ) || $id <= 0 ) {
			return null;
		}
		$activity = new \BP_Activity_Activity( $id );
		return empty( $activity->id ) ? null : self::build_activity( $activity );
	}

	/**
	 * Build the activity context.
	 *
	 * @param object $activity Activity.
	 */
	private static function build_activity( object $activity ): self {
		// A blog-post activity shares the post itself: its live permalink (the stored primary_link
		// goes stale when the slug changes), title, excerpt and featured image.
		$post = self::activity_post( $activity );
		if ( $post ) {
			return new self(
				'activity',
				(int) $activity->id,
				$activity,
				(string) get_permalink( $post ),
				wp_strip_all_tags( get_the_title( $post ) ),
				self::post_text( $post ),
				self::post_image( $post )
			);
		}

		return new self(
			'activity',
			(int) $activity->id,
			$activity,
			(string) bp_activity_get_permalink( (int) $activity->id, $activity ),
			self::activity_title( $activity ),
			self::activity_text( $activity ),
			self::activity_image( $activity )
		);
	}

	/**
	 * The public post behind a blog-post activity, or null.
	 *
	 * BuddyPress stores the post ID in secondary_item_id (item_id is the blog); plugins that record
	 * blog posts under their own component (BuddyPress Member Blog) store it in item_id.
	 *
	 * @param object $activity Activity.
	 */
	private static function activity_post( object $activity ): ?\WP_Post {
		$types = array( 'new_blog_post' );
		if ( function_exists( 'bp_activity_get_post_types_tracking_args' ) ) {
			$types = array_merge( $types, array_keys( (array) bp_activity_get_post_types_tracking_args() ) );
		}
		if ( ! in_array( $activity->type ?? '', $types, true ) ) {
			return null;
		}
		$post_id = in_array( $activity->component ?? '', array( 'blogs', 'activity' ), true )
			? (int) $activity->secondary_item_id
			: (int) $activity->item_id;
		/**
		 * Post ID behind a blog-post activity.
		 *
		 * @param int    $post_id  Detected post ID (0 for none).
		 * @param object $activity Activity.
		 */
		$post = get_post( (int) apply_filters( 'bpas_activity_post_id', $post_id, $activity ) );
		return ( $post && is_post_publicly_viewable( $post ) ) ? $post : null;
	}

	/**
	 * The item's pictures for a rich preview (a private message): its own image first, then the
	 * images in its content, without repeats of one picture at different sizes.
	 *
	 * @param int $limit Maximum number of URLs.
	 * @return string[] Image URLs.
	 */
	public function images( int $limit = 4 ): array {
		$html = '';
		if ( 'post' === $this->type ) {
			$post = $this->object;
		} elseif ( 'activity' === $this->type ) {
			$post = self::activity_post( $this->object );
			$html = $post ? '' : (string) $this->object->content;
		}
		if ( ! empty( $post ) && '' === $post->post_password ) {
			$html = (string) $post->post_content;
		}
		preg_match_all( '/<img[^>]+src=["\']([^"\']+)["\']/i', $html, $found );

		$urls = array();
		foreach ( array_merge( array( $this->image ), $found[1] ) as $url ) {
			// "photo-1024x768.jpg" and "photo.jpg" are the same picture.
			$key = preg_replace( '/-\d+x\d+(?=\.\w+$)/', '', strtok( (string) $url, '?' ) );
			if ( '' !== $url && ! isset( $urls[ $key ] ) ) {
				$urls[ $key ] = esc_url_raw( $url );
			}
		}
		return array_slice( array_values( $urls ), 0, max( 1, $limit ) );
	}

	/**
	 * Image for link previews: the object's own image, else a stand-in (site icon or logo).
	 * Only previews need a picture; everywhere else an empty image means "none".
	 */
	public function preview_image(): string {
		if ( '' !== $this->image ) {
			return $this->image;
		}
		/**
		 * Stand-in preview image for an object without an image of its own.
		 *
		 * @param string        $image Site icon or custom logo URL ('' when neither is set).
		 * @param Share_Context $ctx   Context.
		 */
		return (string) apply_filters( 'bpas_preview_image', self::site_image(), $this );
	}

	/**
	 * Post (any post type) context.
	 *
	 * @param int $id Post ID.
	 */
	private static function post( int $id ): ?self {
		$post = get_post( $id );
		if ( ! $post ) {
			return null;
		}
		return new self(
			'post',
			(int) $post->ID,
			$post,
			(string) get_permalink( $post ),
			wp_strip_all_tags( get_the_title( $post ) ),
			self::post_text( $post ),
			self::post_image( $post )
		);
	}

	/**
	 * Member profile context.
	 *
	 * @param int $id User ID.
	 */
	private static function member( int $id ): ?self {
		$user = get_userdata( $id );
		if ( ! $user ) {
			return null;
		}
		return new self(
			'member',
			(int) $user->ID,
			$user,
			bpas_member_url( (int) $user->ID ),
			bp_core_get_user_displayname( $user->ID ),
			'',
			self::profile_image( 'member', (int) $user->ID )
		);
	}

	/**
	 * Group context.
	 *
	 * @param int $id Group ID.
	 */
	private static function group( int $id ): ?self {
		if ( ! function_exists( 'groups_get_group' ) ) {
			return null;
		}
		$group = groups_get_group( $id );
		if ( empty( $group->id ) ) {
			return null;
		}
		return new self(
			'group',
			(int) $group->id,
			$group,
			bpas_group_url( $group ),
			wp_strip_all_tags( (string) $group->name ),
			self::excerpt( (string) $group->description ),
			self::profile_image( 'group', (int) $group->id )
		);
	}

	/**
	 * A member's or group's own picture, large enough for a link preview (Facebook ignores
	 * images under 200x200): the uploaded avatar, else the cover image, else ''.
	 * BuddyPress full avatars are 150x150 unless a site raises the size, so the cover usually wins.
	 * Theme / BuddyPress placeholders and Gravatar never qualify: only files in uploads count.
	 *
	 * @param string $object 'member' | 'group'.
	 * @param int    $id     User or group ID.
	 */
	private static function profile_image( string $object, int $id ): string {
		$group  = 'group' === $object;
		$avatar = ( $group ? bp_get_group_has_avatar( $id ) : bp_get_user_has_avatar( $id ) )
			? (string) bp_core_fetch_avatar(
				array(
					'item_id' => $id,
					'object'  => $group ? 'group' : 'user',
					'type'    => 'full',
					'html'    => false,
					'no_grav' => true,
				)
			)
			: '';
		$size   = '' !== $avatar ? self::image_meta( $avatar ) : null;
		if ( $size && min( $size['width'], $size['height'] ) >= self::MIN_PREVIEW ) {
			return (string) set_url_scheme( $avatar ); // Previews need an absolute URL, never "//host/...".
		}

		$cover = function_exists( 'bp_attachments_get_attachment' )
			? (string) bp_attachments_get_attachment(
				'url',
				array(
					'object_dir' => $group ? 'groups' : 'members',
					'item_id'    => $id,
				)
			)
			: '';
		if ( '' === $cover ) {
			return '';
		}
		// A cover is always an upload; offloaded (non-local) files are kept since their size is unknown.
		$size = self::image_meta( $cover );
		return ! $size || min( $size['width'], $size['height'] ) >= self::MIN_PREVIEW ? (string) set_url_scheme( $cover ) : '';
	}

	/**
	 * Width, height, MIME type and path of an image stored in this site's uploads.
	 * Null for anything outside uploads (theme defaults, remote or offloaded files).
	 *
	 * @param string $url Image URL.
	 * @return array{width:int,height:int,mime:string,file:string}|null
	 */
	public static function image_meta( string $url ): ?array {
		$uploads = wp_get_upload_dir();
		$base    = set_url_scheme( (string) $uploads['baseurl'], 'https' );
		$url     = set_url_scheme( strtok( $url, '?' ), 'https' );
		if ( ! str_starts_with( $url, trailingslashit( $base ) ) ) {
			return null;
		}
		$file = (string) $uploads['basedir'] . '/' . rawurldecode( substr( $url, strlen( trailingslashit( $base ) ) ) );
		if ( str_contains( $file, '..' ) || ! is_file( $file ) ) {
			return null;
		}
		$info = wp_getimagesize( $file );
		return $info ? array(
			'width'  => (int) $info[0],
			'height' => (int) $info[1],
			'mime'   => (string) $info['mime'],
			'file'   => $file,
		) : null;
	}

	/**
	 * Featured image, else the first image in the content.
	 *
	 * @param \WP_Post $post Post.
	 */
	private static function post_image( \WP_Post $post ): string {
		$url = (string) get_the_post_thumbnail_url( $post, 'large' );
		return '' !== $url ? $url : self::first_image( (string) $post->post_content );
	}

	/**
	 * The activity's own image: BuddyBoss media, an image in the content, or a link preview.
	 *
	 * @param object $activity Activity.
	 */
	private static function activity_image( object $activity ): string {
		$id = (int) $activity->id;

		// BuddyBoss media.
		$media_ids = function_exists( 'bp_activity_get_meta' ) ? (string) bp_activity_get_meta( $id, 'bp_media_ids', true ) : '';
		if ( '' !== $media_ids && class_exists( '\BP_Media' ) ) {
			$first = new \BP_Media( (int) strtok( $media_ids, ',' ) );
			if ( ! empty( $first->attachment_id ) ) {
				$url = wp_get_attachment_image_url( (int) $first->attachment_id, 'large' );
				if ( $url ) {
					return (string) $url;
				}
			}
		}

		// Image inside the post content.
		$img = self::first_image( (string) $activity->content );
		if ( '' !== $img ) {
			return $img;
		}

		// Link preview (BuddyBoss).
		$preview = function_exists( 'bp_activity_get_meta' ) ? bp_activity_get_meta( $id, '_link_preview_data', true ) : array();
		if ( is_array( $preview ) && ! empty( $preview['image_url'] ) ) {
			return esc_url_raw( (string) $preview['image_url'] );
		}

		return '';
	}

	/**
	 * First <img> src in HTML.
	 *
	 * @param string $html HTML.
	 */
	private static function first_image( string $html ): string {
		return preg_match( '/<img[^>]+src=["\']([^"\']+)["\']/i', $html, $m ) ? esc_url_raw( $m[1] ) : '';
	}

	/**
	 * Site icon or custom logo.
	 */
	private static function site_image(): string {
		$icon = get_site_icon_url( 512 );
		if ( $icon ) {
			return $icon;
		}
		$logo = (int) get_theme_mod( 'custom_logo' );
		return $logo ? (string) wp_get_attachment_image_url( $logo, 'full' ) : '';
	}

	/**
	 * Post excerpt; nothing for password-protected posts, whose text must not leak into previews.
	 *
	 * @param \WP_Post $post Post.
	 */
	private static function post_text( \WP_Post $post ): string {
		if ( '' !== $post->post_password ) {
			return '';
		}
		return self::excerpt( has_excerpt( $post ) ? $post->post_excerpt : $post->post_content );
	}

	/**
	 * Plain-text excerpt.
	 *
	 * @param string $html Source.
	 */
	private static function excerpt( string $html ): string {
		return wp_trim_words( wp_strip_all_tags( strip_shortcodes( $html ) ), 40, '...' );
	}
}
