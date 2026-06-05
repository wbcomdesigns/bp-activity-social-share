# Feature Audit — BuddyPress Activity Social Share v3.5.5

Source of truth: [`manifest.json`](manifest.json). This is the human view.

## 1. Frontend share button (core feature)
- **Trigger:** `bp_activity_entry_meta` action, attached in `Buddypress_Share_Public::bp_activity_share_button_dis()` (only when `is_user_logged_in()` AND `bp_share_services` is non-empty).
- **Renderer:** `bp_share_activity_filter()` — emits `.bp-share-btn` toggle + hidden `.service-buttons` block, one `<a class="bp-share">` per enabled network.
- **Networks:** Facebook, Twitter/X, Pinterest, Reddit, WordPress, Pocket, Telegram, Bluesky, LinkedIn, WhatsApp, E-mail.
- **Popup vs new-tab:** controlled by `bp_share_services_extra['bp_share_services_open']`; inline `<script>` adds `.has-popup` to all but WhatsApp/Email.
- **Extension point:** `do_action( 'bp_share_user_services', $social_service, $activity_link, $activity_title )`.
- **Label override:** `apply_filters( 'bpas_share_button_text_override', 'Share' )`.

## 2. OpenGraph / Twitter Card meta (single activity)
- `Buddypress_Share_Public::bp_share_opengraph()` on `wp_head@999` — emits og:* and twitter:* tags for single-activity views. Extracts first image, supports BP_Media / Youzer / Youzify media. `bp_share_doctype_opengraph()` adds OG xmlns on `language_attributes`.

## 3. Per-icon color (frontend)
- `bp_share_icon_custom_color()` on `wp_head` prints an inline `<style>` block coloring each icon from `bpas_icon_color_settings` (an `option`, not site_option).

## 4. Admin: General Settings tab
- `admin.php?page=buddypress-share&tab=bpas_general_settings`.
- Drag-and-drop enable/disable + sort of social services (jQuery UI sortable/draggable/droppable).
- Saves via 4 AJAX handlers (see below) and via Settings API form (`bp_share_services_extra` group).

## 5. Admin: Icon Color Settings tab
- `&tab=bpas_icon_color_settings` → `admin/bp-icon-color-page.php`. wp-color-picker per network; Settings API group `bpas_icon_color_settings`.

## 6. Admin: Welcome tab
- `&tab=bpas_welcome` → `admin/bp-welcome-page.php`.

## AJAX handlers
| Action | File:line | Nonce | Capability | Purpose |
|---|---|---|---|---|
| `bp_share_chb_services_ajax` | admin/class-buddypress-share-admin.php:263 | bp_share_nonce | manage_options | Save enabled services + popup flag |
| `bp_share_sort_social_links_ajax` | :361 | bp_share_nonce | manage_options | Persist drag-sort order |
| `wss_social_icons` | :642 | bp_share_nonce | manage_options | Add icon to enabled set |
| `wss_social_remove_icons` | :679 | bp_share_nonce | manage_options | Remove icon from enabled set |
| `wbcom_addons_cards` | admin/wbcom/wbcom-admin-settings.php:40 | wbcom_admin_setting_nonce | **NONE** | Echo back addons-card markup |

## REST endpoints
_None._

## Admin pages / menus
| Title | Slug | Parent | Cap | Source |
|---|---|---|---|---|
| WB Plugins | wbcomplugins | (top) | manage_options | admin.php:350 |
| General | wbcomplugins | wbcomplugins | manage_options | :351 |
| BuddyPress Share | buddypress-share | wbcomplugins | manage_options | :353 |
| Our Plugins | wbcom-plugins-page | wbcomplugins | manage_options | wbcom:260 |
| Our Themes | wbcom-themes-page | wbcomplugins | manage_options | wbcom:268 (**broken — template missing**) |
| Support | wbcom-support-page | wbcomplugins | manage_options | wbcom:276 |

## Settings / options
| Key | Storage | Controls |
|---|---|---|
| bp_share_services | site_option | Enabled networks + order |
| bp_share_services_extra | site_option | Popup-vs-tab |
| bpas_icon_color_settings | option | Per-icon hex colors |
| bp_share_flag | site_option | One-time default-merge guard |
| bp_share_all_services_disable | site_option | **WRITE-ONLY in free** |
| bp_social_share_activation_date / _no_bug | site_option | Review nag |
| _bpshare_is_new_install | transient | Activation redirect |

## Shortcodes
| Tag | Handler | Scope |
|---|---|---|
| wbcom_admin_setting_header | Wbcom_Admin_Settings::wbcom_admin_setting_header_html | Admin header only |

## Content types / taxonomies / tables / cron / blocks / WP-CLI
_None._

## Hooks fired (extension points)
| Hook | Type | Args | Where |
|---|---|---|---|
| bp_share_user_services | action | 3 | public:202 |
| bp_share_add_services_options | action | 2 | admin:622 |
| wbcom_add_header_menu | action | 0 | wbcom:370 |
| wbcom_add_plugin_license_code | action | 0 | wbcom-license-page.php |
| bpas_share_button_text_override | filter | 1 | public:135 |

## Integrations
- **BuddyPress** (required — boots on `bp_loaded`, checks `class_exists('BuddyPress')` + blog/network config).
- **BP_Media / Youzer / Youzify** — OG image source detection.
- **Pro:** `buddypress-activity-share-pro` — free deactivates itself if Pro is active (`buddypress-share.php:46,193,259`).

## Lifecycle
- **Activation:** `Buddypress_Share_Activator::activate()` seeds `bp_share_services` + `bpas_icon_color_settings` (only if BuddyPress active).
- **Uninstall:** `uninstall.php` guards `WP_UNINSTALL_PLUGIN` but performs **no option cleanup**.
