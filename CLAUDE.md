# Plugin: Wbcom Designs - BuddyPress Activity Social Share

> **READ FIRST:** [`audit/manifest.json`](audit/manifest.json) is the canonical inventory — 5 AJAX handlers, 6 admin menu entries, 3 user-facing settings + 5 internal options, 1 shortcode, 5 extension hooks. 0 REST / 0 blocks / 0 CPT / 0 taxonomies / 0 cron / 0 custom tables. Use this before grepping. See also [`audit/FEATURE_AUDIT.md`](audit/FEATURE_AUDIT.md), [`audit/CODE_FLOWS.md`](audit/CODE_FLOWS.md), [`audit/ROLE_MATRIX.md`](audit/ROLE_MATRIX.md), and the wppqa baseline at [`audit/wppqa-baseline-2026-06-05/SUMMARY.md`](audit/wppqa-baseline-2026-06-05/SUMMARY.md). Refresh via `/wp-plugin-onboard --refresh` after non-trivial changes.

## Development skill (READ for all code work)
All implementation work on this plugin MUST follow the **`/wp-plugin-development`** skill — the 16 critical admin rules, Part 6 (Admin UI patterns), the 3-layer design tokens, escaping rules, and dev hygiene (no em-dash in i18n, Lucide icons, no inline SVG in new code, WPCS). This is a legacy WordPress-Plugin-Boilerplate plugin; new UI work should move toward the modern card-based admin pattern, not extend the OLD tab wrapper.

## Quick reference
- **Main file:** `buddypress-share.php`
- **Version:** `3.5.5`  |  **Branch:** `3.5.5`
- **Namespace:** none (global functions + `Buddypress_Share_*` classes)
- **Text domain:** `buddypress-share`
- **Extends:** null. **Pro counterpart:** `buddypress-activity-share-pro` (free self-deactivates when Pro is active).
- **Requires:** BuddyPress (boots on `bp_loaded`).

## Key entry points
- Bootstrap: `buddypress-share.php`
- Core orchestrator: `includes/class-buddypress-share.php` (`define_admin_hooks` / `define_public_hooks`)
- Hook registry: `includes/class-buddypress-share-loader.php` (deferred add_action/add_filter — AJAX is registered HERE, not via raw add_action)
- Admin: `admin/class-buddypress-share-admin.php`
- Public: `public/class-buddypress-share-public.php`

## Admin-UI wrapper classification: **OLD**
- **Wrapper:** OLD — `admin/wbcom/wbcom-admin-settings.php` (class `Wbcom_Admin_Settings`, tab-based shared Wbcom chrome).
- NOT present: INTERMEDIATE (`includes/shared-admin/class-wbcom-shared-dashboard.php`), NEW (`includes/admin/views/shell.php`).
- **Plugin settings menu:** parent `wbcomplugins` (top-level `add_menu_page`, registered on `bp_core_admin_hook()` in `Buddypress_Share_Admin::bp_share_plugin_menu`, admin.php:350); plugin settings page slug **`buddypress-share`** (submenu, admin.php:353), tabs `bpas_welcome` / `bpas_general_settings` / `bpas_icon_color_settings`.
- **Shared wrapper menus** (admin_menu@999, wbcom-admin-settings.php): `wbcom-plugins-page`, `wbcom-themes-page` (BROKEN — template missing), `wbcom-support-page`.

## Important patterns
- **Hooks go through the loader.** Add new actions/filters in `define_admin_hooks()`/`define_public_hooks()`, not scattered add_action calls.
- **Options are site_options** (`get_site_option`/`update_site_option`) — EXCEPT `bpas_icon_color_settings` which uses `get_option`/`update_option` (Settings API). Be deliberate about which one.
- **AJAX auth:** all 4 plugin AJAX handlers pair `bp_share_nonce` + `current_user_can('manage_options')`. The shared-wrapper `wbcom_addons_cards` handler is nonce-only (see Known Issues).
- **Free/Pro guard:** several functions bail/deactivate if `buddypress-activity-share-pro` is active. Preserve this when editing bootstrap.
- **Release build:** `gruntfile.js` present (grunt).

## CSS selectors (for testing/dev)
- Frontend: `.bp-share-btn`, `.bp-share-button`, `.service-buttons` (display:none until JS toggle), `.bp-share`, `.has-popup`, per-network ids `#bp_facebook_share`, `#bp_twitter_share`, etc.
- Admin: `#drag_social_icon` / `#drag_icon_ul` (disable/enable lists), `.socialicon`, `.bp_share_option_save`.

## Known issues (from onboarding audit 2026-06-05)
1. **Broken "Our Themes" admin page** — `wbcom-admin-settings.php:303` `include`s `templates/wbcom-themes-page.php` which does not exist. PHP warning + empty page.
2. **nonce-no-cap** — `wbcom-admin-settings.php:43` (`wbcom_addons_cards` AJAX) verifies nonce but no `current_user_can()`. Low impact (only echoes a sanitized POST string) but should add a cap check.
3. **No-JS frontend gap** — `.service-buttons` are `display:none` with no PHP/no-JS fallback; share targets are unreachable without JavaScript.
4. **Write-only option** — `bp_share_all_services_disable` is written (admin:301) but never read in the free build.
5. `uninstall.php` performs no option cleanup (leaves `bp_share_*` behind).

## Recent changes
| Date | Type | Description | Files |
|---|---|---|---|
| 2026-07-03 | bug-fix | Welcome tab rebuilt on bpas-card pattern (was unstyled wbcom-* markup inside 3.5.6 shell) | admin/bp-welcome-page.php, assets/css/admin.css |
| 2026-07-03 | bug-fix | Dark-mode share icons: brand-color defaults + one-time #000000 remap migration (bp_share_db_version), stale .fa-* selectors replaced with dashicons | includes/class-buddypress-share-activator.php, buddypress-share.php, public/css/buddypress-share-public.css, public/class-buddypress-share-public.php |
| 2026-06-05 | onboard | Generated audit/ inventory, wppqa baseline, READ-FIRST CLAUDE.md | audit/*, CLAUDE.md |
