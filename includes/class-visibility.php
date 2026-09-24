<?php
/**
 * Who may share what outside the community.
 *
 * @package BPAS
 */

namespace BPAS;

defined( 'ABSPATH' ) || exit;

/**
 * Fixed privacy rules (plan 12.3) - never settings.
 */
final class Visibility {

	/**
	 * True when the object is public, so network share links and public copy make sense.
	 *
	 * @param Share_Context $ctx Context.
	 */
	public static function can_share_externally( Share_Context $ctx ): bool {
		$public = match ( $ctx->type ) {
			'activity' => self::activity_is_public( $ctx->object ),
			'post'     => is_post_publicly_viewable( $ctx->object ),
			'group'    => 'public' === ( $ctx->object->status ?? '' ),
			'member'   => true,
			default    => false,
		};
		return (bool) apply_filters( 'bpas_can_share_externally', $public, $ctx );
	}

	/**
	 * Group ID of a private or hidden group the activity belongs to, else 0.
	 *
	 * @param object $activity BP_Activity_Activity.
	 */
	public static function private_group_id( object $activity ): int {
		if ( 'groups' !== ( $activity->component ?? '' ) || ! function_exists( 'groups_get_group' ) ) {
			return 0;
		}
		$group = groups_get_group( (int) $activity->item_id );
		return ( ! empty( $group->id ) && 'public' !== $group->status ) ? (int) $group->id : 0;
	}

	/**
	 * Activity rules: not hidden, not spam, not in a private / hidden group.
	 *
	 * @param object $activity BP_Activity_Activity.
	 */
	private static function activity_is_public( object $activity ): bool {
		if ( ! empty( $activity->hide_sitewide ) || ! empty( $activity->is_spam ) ) {
			return false;
		}
		// Comments are stored as component 'activity'; their privacy is the root post's.
		if ( 'activity_comment' === ( $activity->type ?? '' ) && ! empty( $activity->item_id ) ) {
			$root = new \BP_Activity_Activity( (int) $activity->item_id );
			return ! empty( $root->id ) && self::activity_is_public( $root );
		}
		return 0 === self::private_group_id( $activity );
	}
}
