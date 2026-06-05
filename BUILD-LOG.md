# BUILD-LOG — Admin wrapper migration (OLD → card-panel)

**Plugin:** buddypress-activity-social-share (Wbcom Designs - BuddyPress Activity Social Share)
**Branch:** 3.5.5  →  shipped as version **3.5.6**
**Type:** UX-only chrome migration (reskin, not rebuild). Zero option-key / nonce / AJAX-action / hook renames.
**Pattern source:** buddypress-contact-me (wbcom-pro), playbook `references/wbcom-wrapper-migration.md`.

---

## Approach

This was a **reskin-the-chrome** migration (playbook Part 0.3). The plugin's two
real settings tabs are not generic Settings-API forms:

- **General Settings** — a custom jQuery-UI drag/drop enable/disable list wired to
  `admin/js/buddypress-share-admin.js`, styled by `admin/css/buddypress-share-admin.css`,
  saved through **AJAX** (`bp_share_chb_services_ajax`, `bp_share_sort_social_links_ajax`,
  `wss_social_icons`, `wss_social_remove_icons`) plus a small Settings-API form
  (`bp_share_services_extra`). The "Save Changes" button fires BOTH paths.
- **Icon Colors** — a Settings-API form (`register_setting('bpas_icon_color_settings')`)
  with `wp-color-picker`.

So the content renderers + their JS/CSS + every save path were kept byte-for-byte.
The new `BPAS_Admin` chrome only owns menu + page shell + sidebar nav + enqueue +
notice suppression, and delegates each tab body back to the retained
`Buddypress_Share_Admin` section renderers.

Mixed storage preserved exactly per the per-plugin note:
- `bp_share_services` — `site_option`, custom AJAX save (unchanged).
- `bp_share_services_extra` — `site_option`, Settings-API group `bp_share_services_extra` (unchanged).
- `bpas_icon_color_settings` — `option` (get/update_option), Settings-API group `bpas_icon_color_settings` (unchanged).

**Sentinel-guarded merge (Playbook 7.1) was evaluated and is NOT needed here:** each
option key is owned by exactly one tab (no single option key is split across tabs),
so the multi-tab data-loss class of bug does not apply. The existing sanitizers were
left untouched.

---

## Files DELETED (4 files + dir)

- `admin/wbcom/wbcom-admin-settings.php` (class `Wbcom_Admin_Settings`, `[wbcom_admin_setting_header]` shortcode, `wbcom_addons_cards` AJAX)
- `admin/wbcom/templates/wbcom-license-page.php`
- `admin/wbcom/templates/wbcom-plugins-page.php`
- `admin/wbcom/templates/wbcom-support-page.php`
- `admin/wbcom/assets/css/wbcom-admin-setting.css`
- `admin/wbcom/assets/js/wbcom-admin-setting.js`
- (whole `admin/wbcom/` directory removed)

Dead code removed from `admin/class-buddypress-share-admin.php` (Part 3.1 — every
removed method was proven 0-cross-file-refs after the rewire):
- `enqueue_styles()`, `enqueue_scripts()` (chrome enqueue — now in BPAS_Admin)
- `bp_share_plugin_menu()` (menu — now in BPAS_Admin)
- `bp_share_plugin_options()` (old nav-tab page render, used `[wbcom_admin_setting_header]`)
- `bpas_plugin_settings_tabs()` (old nav-tab nav)
- `bpas_include_admin_setting_tabs()` (old tab dispatcher — replaced by `BPAS_Admin::render_tab_body`)
- `wbcom_hide_all_admin_notices_from_setting_page()` (now BPAS_Admin::suppress_foreign_notices)

Inline `<script>` (wpColorPicker init) removed from `admin/bp-icon-color-page.php`
(gate F2) → moved into `assets/js/admin.js`.

## Files CREATED (5)

- `includes/admin/class-bpas-admin.php` — card-panel controller (`get_tabs`, `register`,
  `add_menu`, `takeover_hub_landing`, `enqueue_assets`, `suppress_foreign_notices`,
  `is_our_screen`, `render_hub`, `render_page`, `render_tab_body`).
- `includes/admin/views/shell.php` — page header + sidebar nav + body slot.
- `includes/admin/views/hub.php` — WB Plugins hub landing (with legacy wrapper-slug filter).
- `assets/css/admin.css` — `--bpas-admin-*` token system (ported from contact-me, prefix-swapped).
- `assets/js/admin.js` — `bpasToast` + `bpasConfirm` + wpColorPicker init + destructive-link interceptor.

## Files MODIFIED (4)

- `includes/class-buddypress-share.php` — dropped `require admin/wbcom/...`; added
  `require includes/admin/class-bpas-admin.php`; rewired `define_admin_hooks()` to keep
  settings registration + 4 AJAX handlers and instantiate `BPAS_Admin($plugin_admin)->register()`.
- `admin/class-buddypress-share-admin.php` — removed dead chrome/dispatcher methods;
  rewrote class docblock to describe post-migration scope; kept settings registration,
  sanitizers, the 3 tab section renderers, the 4 AJAX handlers, and the Settings-API
  field/section callbacks.
- `admin/bp-icon-color-page.php` — removed inline `<script>` (init moved to admin.js).
- Version bump 3.5.5 → 3.5.6 in `buddypress-share.php` (header + `BP_SHARE_VERSION`),
  `readme.txt` (Stable tag + changelog stub), `package.json`.

---

## Option keys preserved (old → new proof)

| Option key | Storage | Save mechanism | Status |
|---|---|---|---|
| `bp_share_services` | site_option | custom AJAX (4 handlers) | unchanged, all handlers still registered |
| `bp_share_services_extra` | site_option | Settings API group `bp_share_services_extra` | `register_setting` intact (admin class) |
| `bpas_icon_color_settings` | option | Settings API group `bpas_icon_color_settings` | `register_setting` intact (admin class) |
| `bp_share_flag`, `bp_share_all_services_disable` | site_option (internal) | n/a | untouched |

AJAX actions still registered: `bp_share_chb_services_ajax`, `bp_share_sort_social_links_ajax`,
`wss_social_icons`, `wss_social_remove_icons` (all nonce `bp_share_nonce` + `current_user_can('manage_options')`).
Menu slug `buddypress-share` preserved (plugin-row Settings link + activation redirect still resolve).

---

## Safe fixes applied

1. **Broken "Our Themes" page fixed** — removed with `admin/wbcom/` (its callback `include`d the
   confirmed-missing `templates/wbcom-themes-page.php`). The whole `wbcom-themes-page` /
   `wbcom-plugins-page` / `wbcom-support-page` submenu set is gone with the wrapper.
2. **`wbcom_addons_cards` cap-check concern resolved by deletion** — the nonce-only handler
   lived on `Wbcom_Admin_Settings`, which was deleted entirely. The handler no longer exists,
   so there is no remaining nonce-without-capability surface. (No need to add a cap check to a
   deleted handler.)
3. **Inline `<script>` removed** from the Icon Colors page (gate F2).
4. **Version bump + changelog stub** (3.5.5 → 3.5.6).
5. **Free/Pro self-deactivation guard preserved** — `bpshare_requires_buddypress()` and the
   activation guard in `buddypress-share.php` are untouched; free still self-deactivates when
   `buddypress-activity-share-pro` is active.

---

## Verification

- **`php -l`** — clean on every plugin PHP file (ALL PHP OK).
- **JS** — `node --check assets/js/admin.js` OK.
- **WPCS (wpcs MCP)** — 0 errors on all created + modified files. 1 pre-existing WARNING at
  `buddypress-share.php:205` (`NonceVerification.Recommended` in `bpshare_requires_buddypress`,
  the `unset($_GET['activate'])` Pro-active guard) — untouched legacy code, not introduced here.
- **Runtime smoke (`wp eval`)** — `BPAS_Admin` instantiates; `get_tabs()` returns
  `bpas_welcome, bpas_general_settings, bpas_icon_color_settings`; `MENU_SLUG` = `buddypress-share`;
  `render_tab_body` callable; all 3 section renderers present on the legacy service; dead methods
  (`bp_share_plugin_options`, `bp_share_plugin_menu`) confirmed gone.
- **Option-key parity grep** — `register_setting` groups + names, `update_site_option` keys, and
  all 4 `wp_ajax_*` registrations match pre-migration (see table above).
- **Dangling-reference grep (Part 2.1)** — no PHP/JS references to `admin/wbcom`,
  `wbcom-admin-settings`, `Wbcom_Admin_Settings`, `wbcom_admin_setting_header`, or
  `wbcom_addons_cards` remain (only descriptive comments in the new files).
- **Inline style/script grep (gates F1/F2)** — none in the migrated admin/includes surface.

**Browser verification was NOT performed** — the plugin is currently inactive and the brief
forbids activating/deactivating it or touching the DB. All other verify-contract items are green.

---

## TODOs deferred (out of migration scope)

1. **Frontend no-JS gap (logged per brief):** `.service-buttons` are `display:none` with no
   PHP/no-JS fallback (`public/class-buddypress-share-public.php:144`) — share targets are
   unreachable without JavaScript. Not addressed; explicitly out of scope.
2. **Inline `<style>` in `admin/class-buddypress-share-feedback.php:175`** — the review-nag
   feedback class still emits an inline `<style>` block (gate F1). Pre-existing, not part of the
   wrapper surface; left untouched. Candidate for a follow-up enqueue pass.
3. **`uninstall.php` does no option cleanup** — leaves `bp_share_*` / `bpas_*` options behind
   (pre-existing; from onboarding audit). Out of scope.
4. **Write-only option `bp_share_all_services_disable`** — written in the AJAX handler, never read
   in the free build (likely Pro-consumed). Left as-is.
5. **Legacy `Buddypress_Share_Admin::$plugin_name` / `$version`** properties are now only set in
   the constructor (their former consumers, the enqueue methods, were removed). Harmless; left in
   place because the bootstrap still constructs the class with name+version. Could be trimmed in a
   later cleanup.

## Needs human eyes

- Confirm 3.5.6 is the intended release number for this UX migration (branch is named `3.5.5`
  and all version strings previously matched it with no drift; I bumped to 3.5.6 to match the
  `@since 3.5.6` tags in the new code and keep version triangulation green).
- Per brief: NOT committed/pushed, NOT activated/deactivated, DB untouched.
