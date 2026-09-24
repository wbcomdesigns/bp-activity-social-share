---
title: Developer guide
---

# Developer guide

## Add a share network

```php
add_action( 'bpas_register_networks', function () {
	bpas_register_network( 'mastodon', array(
		'label'     => 'Mastodon',
		'share_url' => 'https://mastodonshare.com/?url={url}&text={title}', // {url} {title} {text} {image}
		'icon'      => '<svg viewBox="0 0 24 24">...</svg>',              // or 'icon_url'
		'color'     => '#6364FF',
		'mobile'    => true,
	) );
} );
```

It appears on the Networks screen (off until ticked) and in every share menu. Share URLs must use https; placeholders are encoded for you; SVG is sanitised. Remove a built-in with `bpas_unregister_network( 'pinterest' )`.

## Add a row to the share menu

```php
add_action( 'bpas_share_menu_items', function ( $ctx, $external ) {
	echo '<ul class="bpas-share__list"><li class="bpas-share__item"><button type="button" class="bpas-share__link">'
		. bpas_icon( 'link' ) . '<span class="bpas-share__name">Save</span></button></li></ul>';
}, 10, 2 );
```

## Public functions

`bpas_version()`, `bpas_get_setting()`, `bpas_networks()`, `bpas_register_network()`, `bpas_unregister_network()`, `bpas_share_context( $type, $id )`, `bpas_activity_share_context( $activity )`, `bpas_can_share_externally( $ctx )`, `bpas_render_share_menu( $ctx, $args )`, `bpas_icon( $name )`, `bpas_enqueue_assets()`, `bpas_rest_namespace()`.

## Filters and actions

| Hook | Type | Use |
|---|---|---|
| `bpas_networks` | filter | Change the network list |
| `bpas_default_settings` | filter | Defaults |
| `bpas_share_context` | filter | Change url / title / text / image |
| `bpas_share_url` | filter | Change the shared URL (e.g. add UTM or referral) |
| `bpas_can_share_externally` | filter | Override the privacy rule |
| `bpas_share_menu_args` | filter | Label, position, classes |
| `bpas_share_menu_items` | action | Add rows |
| `bpas_share_toggle_after` | action | Add markup inside the Share button |
| `bpas_admin_tabs` | filter | Add admin tabs |
| `bpas_open_graph_enabled` | filter | Turn link-preview tags on / off |
| `bpas_enqueue_assets` | filter | Load assets on a non-BuddyPress page |
| `bpas_settings_updated` | action | React to settings changes |

**Pro:** `bpas_pro_before_reshare` (filter, return WP_Error to stop), `bpas_pro_after_reshare`, `bpas_pro_after_send`, `bpas_pro_after_undo_reshare`, `bpas_pro_share_visit`, `bpas_pro_share_signup`, `bpas_pro_user_can_repost`, `bpas_pro_retention_days`, `bpas_pro_client_ip` (trusted proxies), `bpas_pro_card_colors`, `bpas_pro_short_link_prefix`, `bpas_pro_feature_{key}_enabled`.

## Template override

Copy `templates/share-menu.php` to `{your-theme}/bp-activity-social-share/share-menu.php`.

## REST API (`/wp-json/bpas/v1/`)

| Route | Method | Auth |
|---|---|---|
| `settings` | GET, PATCH | manage_options |
| `reshare` | POST | logged in (Pro) |
| `reshare/{id}` | DELETE | author (Pro) |
| `send` | POST | logged in (Pro) |
| `me/groups`, `me/friends` | GET | logged in (Pro) |
| `activity/{id}/shares`, `activity/{id}/reposters` | GET | public, respects privacy (Pro) |
| `pro-settings` | GET, PATCH | manage_options (Pro) |
| `analytics` | GET | manage_options (Pro) |
| `track` | POST | public, only when analytics is on (Pro) |

Lists return `{ items, total, pages, has_more }`; errors are `WP_Error` with 401 / 403 / 404 / 409.
