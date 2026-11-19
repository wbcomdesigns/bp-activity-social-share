# Public functions, REST API and template override

## Public functions

These are the only functions Activity Share Pro, themes and other plugins should call. Signatures are kept stable for at least one minor version after a deprecation notice.

| Function | Returns | Use |
|---|---|---|
| `bpas_version()` | `string` | The active plugin version |
| `bpas_api_version()` | `string` | The public API contract version (major.minor) |
| `bpas_get_setting( $key, $default = null )` | `mixed` | Read one Free setting |
| `bpas_networks( $enabled_only = true )` | `array` | Registered networks, keyed by slug |
| `bpas_register_network( $slug, $args )` | `true\|WP_Error` | Register a network - see [Hooks](01-hooks.md) |
| `bpas_unregister_network( $slug )` | `bool` | Remove a network |
| `bpas_share_context( $type, $id )` | `?BPAS\Share_Context` | Build a share context for `'activity'`, `'post'`, `'member'` or `'group'` |
| `bpas_activity_share_context( $activity )` | `?BPAS\Share_Context` | Build a context from an activity object already in memory (no extra query) |
| `bpas_can_share_externally( $ctx )` | `bool` | Whether the object may be shared outside the community |
| `bpas_render_share_menu( $ctx, $args = [] )` | `string` | Render the share menu HTML for a context (`''` when nothing to show) |
| `bpas_icon( $name )` | `string` | An inline SVG icon from the bundled UI icon set |
| `bpas_enqueue_assets()` | `void` | Enqueue the share menu's CSS/JS early |
| `bpas_rest_namespace()` | `string` | The shared REST namespace, `bpas/v1` |
| `bpas_register_autoload( $prefix, $dir )` | `void` | Register a namespace prefix with Free's autoloader (used by Pro) |

## Template override

Copy `templates/share-menu.php` from the plugin to `{your-theme}/bp-activity-social-share/share-menu.php` to change the share menu's markup. The template receives only the pre-built view data - it never queries the database directly.

## REST API

Base: `/wp-json/bpas/v1/`.

| Route | Method | Auth | Plugin |
|---|---|---|---|
| `settings` | GET, PATCH | `manage_options` | Free |
| `reshare` | POST | Logged in | Pro |
| `reshare/{id}` | DELETE | Author of the repost, or a site moderator | Pro |
| `send` | POST | Logged in | Pro |
| `me/groups`, `me/friends` | GET | Logged in | Pro |
| `activity/{id}/shares` | GET | Public (privacy-checked) | Pro |
| `activity/{id}/reposters` | GET | Public (privacy-checked) | Pro |
| `pro-settings` | GET, PATCH | `manage_options` | Pro |
| `analytics` | GET | `manage_options` | Pro |
| `track` | POST | Public (only records when Share analytics is on) | Pro |

Pro registers its routes only while the BuddyPress component they need is on: reposting, counts and reposters need Activity, `send` and `me/friends` need both Friend Connections and Private Messaging, and `me/groups` needs User Groups.

List responses use the envelope `{ items_key: [...], total, pages, has_more }`. Errors are `WP_Error` objects with an HTTP status of 401 (not logged in), 403 (not allowed), 404 (not found) or 409 (conflict, e.g. already reposted).

Next: [the shortcode and block](03-shortcodes.md).
