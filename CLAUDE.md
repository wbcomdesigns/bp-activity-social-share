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

## Admin-UI wrapper classification: **card-panel (migrated 2026-07-03)**
- The OLD shared wrapper (`admin/wbcom/wbcom-admin-settings.php`, class `Wbcom_Admin_Settings`) and its `wbcom-plugins-page` / `wbcom-themes-page` / `wbcom-support-page` menus were **deleted** in the card-panel migration (commit `fff4b3d`). `admin/wbcom/` no longer exists.
- **Plugin settings menu:** parent `wbcomplugins` (registered on `bp_core_admin_hook()` in `Buddypress_Share_Admin::bp_share_plugin_menu`); plugin settings page slug **`buddypress-share`**, tabs `bpas_welcome` / `bpas_general_settings` / `bpas_icon_color_settings`.
- New admin assets live in `assets/css/admin.css` + `assets/js/admin.js` (bpas-card pattern, design tokens).

## Important patterns
- **Hooks go through the loader.** Add new actions/filters in `define_admin_hooks()`/`define_public_hooks()`, not scattered add_action calls.
- **Options are site_options** (`get_site_option`/`update_site_option`) — EXCEPT `bpas_icon_color_settings`, which is read/saved per-site via `get_option`/`update_option` (Settings API) and, since 3.5.6, seeded in BOTH stores at activation (with a `maybe_upgrade()` backfill from the network store). Be deliberate about which one.
- **AJAX auth:** all 4 plugin AJAX handlers pair `bp_share_nonce` + `current_user_can('manage_options')`. (The nonce-only shared-wrapper `wbcom_addons_cards` handler was removed with the OLD wrapper.)
- **Free/Pro guard:** several functions bail/deactivate if `buddypress-activity-share-pro` is active. Preserve this when editing bootstrap.
- **Release build:** `gruntfile.js` present (grunt).

## CSS selectors (for testing/dev)
- Frontend: `.bp-share-btn`, `.bp-share-button`, `.service-buttons` (display:none until JS toggle), `.bp-share`, `.has-popup`, per-network ids `#bp_facebook_share`, `#bp_twitter_share`, etc.
- Admin: `#drag_social_icon` / `#drag_icon_ul` (disable/enable lists), `.socialicon`, `.bp_share_option_save`.

## Known issues
1. **No-JS frontend gap** — `.service-buttons` are `display:none` with no PHP/no-JS fallback; share targets are unreachable without JavaScript.
2. **Write-only option** — `bp_share_all_services_disable` is written (admin:301) but never read in the free build.
3. **Dead confirm scaffolding** — `assets/js/admin.js` wires a `[data-bpas-confirm]` promise-based confirm modal, but no PHP template emits that attribute; the icon-remove control has no client-side confirm. Wire it or remove it.

Resolved (do not re-report): broken "Our Themes" page and the nonce-no-cap `wbcom_addons_cards` AJAX were removed with the OLD wrapper (commit `fff4b3d`, 2026-07-03); `uninstall.php` now cleans all `bp_share_*` / `bpas_*` options from both stores (2026-07-03).

## Recent changes
| Date | Type | Description | Files |
|---|---|---|---|
| 2026-07-03 | bug-fix | Audit-verdict majors: enqueue version tied to BP_SHARE_VERSION (cache-bust), Twitter static fallback #1DA1F2, icon colors seeded in both option stores + maybe_upgrade backfill (multisite gap), labeled color inputs + :focus-visible, uninstall option cleanup | includes/class-buddypress-share.php, includes/class-buddypress-share-activator.php, public/css/buddypress-share-public.css, admin/bp-icon-color-page.php, admin/css/buddypress-share-admin.css, uninstall.php |
| 2026-07-03 | bug-fix | Welcome tab rebuilt on bpas-card pattern (was unstyled wbcom-* markup inside 3.5.6 shell) | admin/bp-welcome-page.php, assets/css/admin.css |
| 2026-07-03 | bug-fix | Dark-mode share icons: brand-color defaults + one-time #000000 remap migration (bp_share_db_version), stale .fa-* selectors replaced with dashicons | includes/class-buddypress-share-activator.php, buddypress-share.php, public/css/buddypress-share-public.css, public/class-buddypress-share-public.php |
| 2026-06-05 | onboard | Generated audit/ inventory, wppqa baseline, READ-FIRST CLAUDE.md | audit/*, CLAUDE.md |
