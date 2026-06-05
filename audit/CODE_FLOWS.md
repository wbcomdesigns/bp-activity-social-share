# Code Flows — BuddyPress Activity Social Share v3.5.5

## Boot sequence
```
buddypress-share.php
  register_activation_hook -> activate_buddypress_share() -> Buddypress_Share_Activator::activate()
  add_action('bp_loaded', 'bpshare_plugin_init')
    -> if class_exists('BuddyPress') && bp_activity_share_check_config()
       -> run_buddypress_share() -> new Buddypress_Share()->run()
  admin_init: bpshare_requires_buddypress, bpshare_do_activation_redirect, bpshare_set_default_option
```
`Buddypress_Share::__construct()` wires everything through `Buddypress_Share_Loader` (a deferred add_action/add_filter registry executed in `run()`).

## Flow A — Frontend share button
```
bp_init -> Buddypress_Share_Public::bp_activity_share_button_dis()
  (logged-in + services set) -> add_action('bp_activity_entry_meta', bp_share_activity_filter)
bp_activity_entry_meta (per activity) -> bp_share_activity_filter()
  reads bp_share_services + bp_share_services_extra
  -> renders .bp-share-btn + hidden .service-buttons (one <a> per network)
  -> do_action('bp_share_user_services', ...)   [Pro hook]
  inline <script> applies .has-popup based on bp_share_services_open
public/js/buddypress-share-public.js -> click .bp-share-button toggles .service-buttons; .has-popup opens window.open popup
```
Key files: `public/class-buddypress-share-public.php`, `public/js/buddypress-share-public.js`, `public/css/buddypress-share-public.css`.

## Flow B — OpenGraph on single activity
```
wp_head@999 -> bp_share_opengraph()
  guard: bp_is_active('activity') && single activity
  builds title/desc/image (regex over activity content; BP_Media/Youzify support)
  echoes og:* + twitter:* <meta>
language_attributes -> bp_share_doctype_opengraph() adds OG xmlns
```

## Flow C — Admin: enable/disable + sort services (AJAX)
```
admin.php?page=buddypress-share&tab=bpas_general_settings
  enqueue_scripts() (hook 'wb-plugins_page_buddypress-share') -> buddypress-share-admin.js + my_ajax_object{nonce:bp_share_nonce}
  drag enable  -> POST wss_social_icons          -> wss_social_icons()        -> update_site_option('bp_share_services')
  drag disable -> POST wss_social_remove_icons    -> wss_social_remove_icons() -> unset + update_site_option
  reorder      -> POST bp_share_sort_social_links_ajax -> reindex 'bp_share_services'
  save form    -> POST bp_share_chb_services_ajax -> writes services + bp_share_services_extra + bp_share_all_services_disable
  all guarded by wp_verify_nonce(bp_share_nonce) + current_user_can('manage_options')
```
Key files: `admin/class-buddypress-share-admin.php`, `admin/js/buddypress-share-admin.js`.

## Flow D — Admin: icon colors
```
&tab=bpas_icon_color_settings -> admin/bp-icon-color-page.php
  Settings API group 'bpas_icon_color_settings' (register_setting @ admin:91)
  wp-color-picker inputs -> options.php save -> get_option('bpas_icon_color_settings')
  frontend: wp_head -> bp_share_icon_custom_color() emits inline <style>
```

## Flow E — Shared Wbcom admin chrome (OLD wrapper)
```
admin/wbcom/wbcom-admin-settings.php (Wbcom_Admin_Settings, auto-instantiated)
  admin_menu@999 -> add_submenu_page x3 (Our Plugins / Our Themes / Support) under 'wbcomplugins'
  shortcode [wbcom_admin_setting_header] -> top nav rendered inside bp_share_plugin_options()
  Our Themes callback include 'templates/wbcom-themes-page.php'  <-- FILE MISSING (broken page)
```

## Key files
| File | Role |
|---|---|
| buddypress-share.php | Bootstrap, activation, BP/Pro guards, default options |
| includes/class-buddypress-share.php | Core orchestrator; define_admin_hooks/define_public_hooks |
| includes/class-buddypress-share-loader.php | Deferred hook registry |
| admin/class-buddypress-share-admin.php | Admin UI, settings tabs, 4 AJAX handlers |
| admin/wbcom/wbcom-admin-settings.php | OLD shared Wbcom admin wrapper + 5th AJAX |
| admin/bp-icon-color-page.php / bp-welcome-page.php | Tab templates |
| admin/class-buddypress-share-feedback.php | Review-nag notice |
| public/class-buddypress-share-public.php | Share button, OG meta, icon CSS |

## Roles
All admin surfaces require `manage_options`. Frontend share button requires `is_user_logged_in()`. No custom capabilities.
