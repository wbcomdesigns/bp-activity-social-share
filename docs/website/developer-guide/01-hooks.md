# Hooks

## Add a share network

```php
add_action( 'bpas_register_networks', function () {
	bpas_register_network( 'mastodon', array(
		'label'     => __( 'Mastodon', 'my-plugin' ),
		'share_url' => 'https://mastodonshare.com/?url={url}&text={title}', // placeholders: {url} {title} {text} {image}
		'icon'      => '<svg viewBox="0 0 24 24">...</svg>', // inline SVG, sanitised; or use 'icon_url'
		'color'     => '#6364FF', // used when "brand colours" is on
		'mobile'    => true,      // show on phones
	) );
} );
```

It then behaves exactly like a built-in network: listed on the Networks tab (off until the owner ticks it), orderable, rendered in every share menu, and counted by Pro analytics. Rules Free enforces: share URLs must be `https://`, placeholders are URL-encoded automatically, SVG icons are sanitised, and slugs must be unique (`bpas_register_network()` returns a `WP_Error` on a duplicate). Remove a built-in network with `bpas_unregister_network( 'pinterest' )`.

## Add a row to the share menu

```php
add_action( 'bpas_share_menu_items', function ( $ctx, $external ) {
	echo '<ul class="bpas-share__list"><li class="bpas-share__item"><button type="button" class="bpas-share__link">'
		. bpas_icon( 'link' ) . '<span class="bpas-share__name">Save</span></button></li></ul>';
}, 10, 2 );
```

`$ctx` is the `BPAS\Share_Context` being shared; `$external` is `true` when outside sharing is allowed for it. This is how Activity Share Pro adds its Repost and Send rows.

## Filters and actions

| Hook | Type | Arguments | Use |
|---|---|---|---|
| `bpas_networks` | filter | `$networks` | Change the full network list |
| `bpas_default_settings` | filter | `$defaults` | Change default settings |
| `bpas_settings_updated` | action | `$new, $old` | React to a settings save |
| `bpas_register_networks` | action | - | Register extra networks (see above) |
| `bpas_share_context` | filter | `$ctx` | Change url / title / text / image for an object |
| `bpas_share_url` | filter | `$url, $network, $ctx` | Change the shared URL (e.g. add UTM or a referral code) |
| `bpas_is_shareable_activity` | filter | `$shareable, $activity` | Decide whether an activity item gets a Share button |
| `bpas_can_share_externally` | filter | `$public, $ctx` | Override the privacy rule |
| `bpas_share_menu_args` | filter | `$args, $ctx` | Change label, position, class before render |
| `bpas_share_menu_items` | action | `$ctx, $external` | Add rows to the menu (see above) |
| `bpas_share_toggle_after` | action | `$ctx` | Add markup inside the Share toggle button |
| `bpas_admin_tabs` | filter | `$tabs` | Add a tab to the admin screen |
| `bpas_open_graph_enabled` | filter | `$enabled` | Turn link-preview tags on or off |
| `bpas_enqueue_assets` | filter | `$should` | Load the share assets on a non-BuddyPress page |

## Pro hooks

| Hook | Type | Arguments | Use |
|---|---|---|---|
| `bpas_pro_before_reshare` | filter | `null, $user_id, $target, $group_id, $args` | Return a `WP_Error` to stop a repost |
| `bpas_pro_after_reshare` | action | `$activity_id, $user_id, $target, $group_id` | React after a repost |
| `bpas_pro_after_undo_reshare` | action | `$activity_id, $user_id, $type, $id` | React after an undo |
| `bpas_pro_after_send` | action | `$thread_id, $user_id, $friend_id, $target` | React after "send to a friend" |
| `bpas_pro_user_can_repost` | filter | `$can, $user_id, $target` | Override who can repost |
| `bpas_pro_share_visit` | action | `$sharer_id, $type, $id` | A short link brought a visit (used by rewards) |
| `bpas_pro_share_signup` | action | `$sharer_id, $new_user_id` | A short link brought a new member (used by rewards) |
| `bpas_pro_retention_days` | filter | `$days` (default 365) | Change how long analytics rows are kept |
| `bpas_pro_client_ip` | filter | `$ip` | Supply the real client IP behind a trusted proxy |
| `bpas_pro_short_link_prefix` | filter | `$prefix` (default `s`) | Change the short-link path prefix |
| `bpas_pro_default_settings` | filter | `$defaults` | Change Pro's default settings (features, content types) |
| `bpas_pro_settings_updated` | action | `$settings` | React to a Pro settings save |
| `bpas_pro_feature_{key}_enabled` | filter | `$enabled` | Override one Features-tab toggle in code |
| `bpas_pro_repost_action` | filter | `$text, $activity` | Change a repost's activity action text |
| `bpas_pro_card_colors` | filter | `$colors` | Change the generated share-image card's colours |

Continue with [public functions, the REST API and the template override](02-integration.md), or [the shortcode and block](03-shortcodes.md).
