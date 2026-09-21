# Hooks, Filters and Options

This page lists the extension points, options, and AJAX actions that the plugin defines in code, for developers who want to customize its behavior.

## Filters

### `bpas_share_button_text_override`

Changes the label of the share button (default `Share`).

```php
add_filter( 'bpas_share_button_text_override', function ( $text ) {
    return 'Share this';
} );
```

### `language_attributes`

The plugin filters WordPress core's `language_attributes` output to add the Open Graph and Facebook XML namespaces to the `html` tag, so the Open Graph meta tags validate.

## Actions

### `bp_share_user_services`

Fires inside the front-end service button row, after the built-in network buttons are printed. Use it to output additional custom share buttons.

```php
add_action( 'bp_share_user_services', function ( $services, $activity_link, $activity_title ) {
    // Echo your own share markup here.
}, 10, 3 );
```

Arguments passed by the plugin: an (empty) services array, the activity permalink, and the activity title.

### `bp_share_add_services_options`

Fires at the end of the General Settings tab in the admin, after the settings form. Use it to render extra admin controls under the network lists.

```php
add_action( 'bp_share_add_services_options', function ( $arg1, $arg2 ) {
    // Echo additional admin settings markup here.
}, 10, 2 );
```

## Options

The plugin stores its data as network options (`get_site_option` / `update_site_option`), so it behaves the same on single site and multisite.

| Option | Purpose |
|---|---|
| `bp_share_services` | The set of networks and their enabled or disabled state, in display order. |
| `bp_share_services_extra` | Extra options. Holds `bp_share_services_open` (1 for popup, 0 for new tab). |
| `bpas_icon_color_settings` | The per-network icon colors. Registered with `register_setting`. |
| `bp_share_flag` | Internal flag marking that default services have been seeded. |
| `bp_share_all_services_disable` | Internal flag set to `enable` when services are updated through the admin. |

## AJAX actions

All of these run in the admin only. Each one verifies the `bp_share_nonce` nonce and requires the `manage_options` capability.

| Action | What it does |
|---|---|
| `wp_ajax_bp_share_chb_services_ajax` | Saves the enabled or disabled state of each network and the popup option. |
| `wp_ajax_bp_share_sort_social_links_ajax` | Saves the reordered network list. |
| `wp_ajax_wss_social_icons` | Adds a network to the enabled set when it is dragged into the Enable list. |
| `wp_ajax_wss_social_remove_icons` | Removes a network from the set when it is dragged into the Disable list. |

## Front-end hook point

The share button is attached to the BuddyPress `bp_activity_entry_meta` action, and only for logged-in users when at least one service is enabled. The Open Graph meta output runs on `wp_head`, and the icon color CSS is also printed on `wp_head`.

## Assets

- Font Awesome 4.7.0 is enqueued from a CDN.
- The share icons themselves are rendered with WordPress Dashicons (the X / Twitter icon is an inline SVG).
- The admin color picker uses WordPress core's `wp-color-picker`.
