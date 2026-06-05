# Wrapper Audit — buddypress-activity-social-share (branch 3.5.5 → 3.5.6)

**Audit date:** 2026-06-05
**Wrapper type:** NEW card-panel (`includes/admin/views/shell.php` present)
**Overall verdict:** SHIP-CLEAN (minor polish items only; all blockers cleared)

---

## 1. Controller class correctness

**Controller:** `includes/admin/class-bpas-admin.php` — class `BPAS_Admin`

| Method | Present? | Notes |
|---|---|---|
| `get_tabs()` | YES | Static; returns 3 tabs; filterable via `bpas_admin_tabs` |
| `register()` | YES | Hooks `bp_core_admin_hook()` + `admin_menu @999` + `admin_enqueue_scripts` + `in_admin_header @1` |
| `add_menu()` | YES | `wbcomplugins` top-level guard + `buddypress-share` submenu; cap `manage_options` |
| `takeover_hub_landing()` | YES | `admin_menu @999` reclaims hub render from legacy peers |
| `register_settings()` | N/A | Delegated to retained `Buddypress_Share_Admin::bp_share_settings_init` + `bpas_icon_color_register_setting` (both wired on `admin_init` in the orchestrator) |
| `sanitize_settings()` | N/A | Delegated (see above) |
| `enqueue_assets()` | YES | Screen-scoped; loads both chrome layer and content layer |
| `is_our_screen()` | YES | Private; matches `_page_buddypress-share$` + `toplevel_page_wbcomplugins` |
| `suppress_foreign_notices()` | YES | `in_admin_header @1`; gates on `is_our_screen()` |
| `render_hub()` | YES | Renders `includes/admin/views/hub.php` |
| `render_page()` | YES | Cap check, tab routing, shell include |
| `render_tab_body()` | YES | Static; switch delegates to retained content renderers |
| `admin_notices` hook | N/A (intentional) | Notices suppressed globally; `settings_errors()` called in shell |

**No methods missing from the contract.**

### Path-constant check: PASS

`BP_ACTIVITY_SHARE_PLUGIN_PATH` and `BP_ACTIVITY_SHARE_PLUGIN_URL` are both defined in `buddypress-share.php` (the plugin root file) using `plugin_dir_path(__FILE__)` / `plugin_dir_url(__FILE__)`. All uses in `BPAS_Admin::enqueue_assets()` and `render_hub()`/`render_page()` are rooted from this constant.

Verified all referenced asset paths exist on disk:
- `assets/css/admin.css` — EXISTS
- `assets/js/admin.js` — EXISTS
- `admin/css/buddypress-share-admin.css` — EXISTS
- `admin/js/buddypress-share-admin.js` — EXISTS
- `assets/css/font-awesome.min.css` — EXISTS
- `includes/admin/views/hub.php` — EXISTS
- `includes/admin/views/shell.php` — EXISTS

The `load_dependencies()` method in `includes/class-buddypress-share.php` uses `plugin_dir_path(__DIR__)`. `__DIR__` there is the `includes/` subdirectory, so `plugin_dir_path(__DIR__)` correctly resolves to the plugin root. All six `require_once` paths resolve correctly.

No path resolves to `includes/` when it should resolve to the plugin root. The ads-plugin path bug is not present here.

---

## 2. Wrapper structure / gaps

| Item | Status |
|---|---|
| `views/shell.php` | EXISTS — header card, version pill, sidebar nav from `get_tabs()`, `BPAS_Admin::render_tab_body()` in body slot |
| `views/hub.php` | EXISTS — WB Plugins hub landing, legacy slug filter list, empty-state, about card |
| Tab→view map | Complete (see table below) |
| Dead nav links | NONE |
| Orphan views | NONE |
| Legacy `admin/wbcom/` dir | DELETED — confirmed absent |
| `wbcom-admin-settings.php` | DELETED |
| `Wbcom_Admin_Settings` class | DELETED |
| `[wbcom_admin_setting_header]` shortcode | DELETED |
| `wbcom_addons_cards` AJAX handler | DELETED |
| `wbcom-themes-page` submenu | DELETED (only reference is in hub.php exclusion filter — correct) |
| `wbcom-plugins-page` submenu | DELETED |
| `wbcom-support-page` submenu | DELETED |
| `require admin/wbcom/...` in orchestrator | DELETED |

### Tab → view map

| Tab slug | get_tabs() entry | View/renderer | Reachable? |
|---|---|---|---|
| `bpas_welcome` | YES | `bpas_welcome_section()` → `admin/bp-welcome-page.php` | YES |
| `bpas_general_settings` | YES | `bpas_general_setting_section()` — inline renderer | YES |
| `bpas_icon_color_settings` | YES | `bpas_icon_color_setting_section()` → `admin/bp-icon-color-page.php` | YES |

---

## 3. Option wiring (end-to-end)

### bp_share_services — site_option, custom AJAX save

| Step | Status | Evidence |
|---|---|---|
| Registered | YES (custom AJAX path, not Settings API) | 4 AJAX handlers registered on `wp_ajax_*` via the loader; all guard `nonce bp_share_nonce` + `manage_options` |
| Rendered | YES | `bpas_general_setting_section()` — dual drag/drop `<ul>` lists read `get_site_option('bp_share_services')` and reflect current enable/disable state |
| Saved + sanitized | YES | `bp_share_chb_services_ajax`: `map_deep(wp_unslash($_POST[...]), 'sanitize_text_field')`; `bp_share_sort_social_links_ajax`: same pattern; `wss_social_icons` / `wss_social_remove_icons`: `sanitize_text_field()` |
| Read back | YES | Public: `public/class-buddypress-share-public.php:113,127`; Admin drag lists re-read on render |
| Save triggered by | JS intercepting the "Save Changes" button (`return false` prevents form submit to `options.php`); both `bp_share_sort_social_links_ajax` + `bp_share_chb_services_ajax` fire via `jQuery.post` |

**Verdict: PASS**

### bp_share_services_extra — site_option, Settings API group + AJAX hybrid

| Step | Status | Evidence |
|---|---|---|
| Registered | YES | `register_setting('bp_share_services_extra', 'bp_share_services_extra', ...)` in `bp_share_settings_init()` on `admin_init` |
| Rendered | YES | `bp_share_checkbox_open_services_render()` reads `get_site_option('bp_share_services_extra')` and emits a checkbox; rendered via `do_settings_sections('bp_share_services_extra')` |
| Saved + sanitized | YES | `bp_share_chb_services_ajax` saves via `update_site_option('bp_share_services_extra', $extra_option_new)` where value is built from a sanitized POST array. Note: the `options.php` route through `settings_fields()` is intentionally absent — the form "Save Changes" button is intercepted by JS and triggers AJAX exclusively. The `do_settings_sections()` call renders the field UI only; `options.php` is never submitted for this tab. |
| Read back | YES | `public/class-buddypress-share-public.php:128,310` reads `get_site_option('bp_share_services_extra')` for the popup/new-tab behaviour |

**Verdict: PASS** (hybrid AJAX save is intentional per BUILD-LOG; `settings_fields()` absence is not a bug)

### bpas_icon_color_settings — get_option/update_option, Settings API

| Step | Status | Evidence |
|---|---|---|
| Registered | YES | `register_setting('bpas_icon_color_settings', 'bpas_icon_color_settings', ['sanitize_callback' => 'bpas_sanitize_icon_color_settings'])` on `admin_init` |
| Rendered | YES | `admin/bp-icon-color-page.php` reads `get_option('bpas_icon_color_settings')` and emits 11 color `<input type="text">` fields with correct `name="bpas_icon_color_settings[bpas_*_bg_color]"` attributes; `settings_fields('bpas_icon_color_settings')` present |
| Saved + sanitized | YES | Settings API save path; `bpas_sanitize_icon_color_settings()` iterates keys with `sanitize_text_field()` and values with `sanitize_hex_color()` (falls back to `sanitize_text_field()`) |
| Read back | YES | `public/class-buddypress-share-public.php:346-357` reads via `get_option('bpas_icon_color_settings')` and emits per-icon CSS colours in `wp_head` |

**Verdict: PASS**

Note: `bp-icon-color-page.php` calls `wp_enqueue_script('wp-color-picker')` and `wp_enqueue_style('wp-color-picker')` at lines 10-11 redundantly — `BPAS_Admin::enqueue_assets()` already enqueues both before the template runs. WordPress deduplicates by handle so no double-load occurs, but the calls are noise.

### Multi-tab data-loss guard (Playbook 7.1)

**N/A — no sentinel merge needed.** Each option key is owned by exactly one tab:
- `bp_share_services` → General Settings tab only
- `bp_share_services_extra` → General Settings tab only  
- `bpas_icon_color_settings` → Icon Colors tab only

No single option key is split across multiple tabs.

---

## 4. Design-system / hygiene checks

### alert()/confirm() ban
- `assets/js/admin.js` — NO native `alert()`/`confirm()`. Uses `bpasToast` + `bpasConfirm` (Promise-based modal).
- `admin/js/buddypress-share-admin.js` (retained content script) — NO native `alert()`/`confirm()`.
- **PASS**

### CSS token compliance
- All structural colours in `assets/css/admin.css` use `var(--bpas-admin-*)` tokens.
- Raw hex exceptions found (all in the new file, outside the token block):
  - `color: #fff` — 8 occurrences (white on coloured backgrounds; no `--bpas-admin-white` token for foreground-on-colour contexts). Minor — acceptable pattern.
  - `color: #92400e` at line 684 — amber-text body colour for `.bpas-notice--warn`. This is semantically tied to the warn surface and has no corresponding token; a `--bpas-admin-warn-text` token would close this gap. Low severity.
- **VERDICT: PASS with minor polish** (the `#92400e` raw hex at admin.css:684 should become a token)

### Tap targets
- `.bpas-btn` has `min-height: 36px` — 4px below the 40px spec.
- `.bpas-role-chip` has `min-height: 40px` — meets spec.
- `.bpas-snav-link` has `padding: 9px 10px` with 13px text → effective height ~31-32px. Below 40px.
- **VERDICT: Two tap-target violations** (bpas-btn at 36px; bpas-snav-link ~31px). Minor severity.

### Screen-scoped enqueue
`BPAS_Admin::enqueue_assets()` gates on `is_our_screen()` using `get_current_screen()`. Assets load only on `*_page_buddypress-share` and `toplevel_page_wbcomplugins`. **PASS.**

### Focus rings
`focus-visible` rules present for `.bpas-snav-link`, `.bpas-link`, `.bpas-btn`, and all card form inputs. Mouse-click suppresses outline via `:focus:not(:focus-visible)`. **PASS.**

---

## 5. Free/Pro self-deactivation guard

Two separate guards confirmed intact in `buddypress-share.php`:
1. `activate_buddypress_share()` (line 46-48) — deactivates free on activation if Pro is active.
2. `bpshare_requires_buddypress()` on `admin_init` (lines 193-208) — deactivates free at runtime if Pro is found active.

Also preserved: `bpshare_activation_redirect_settings()` (line 259) skips the redirect if Pro is active.

**PASS — guard is fully intact.**

---

## 6. Dead "Our Themes" page

The entire `admin/wbcom/` directory (including the broken callback that `include`d the non-existent `templates/wbcom-themes-page.php`) has been deleted. The `wbcom-themes-page` submenu is no longer registered anywhere. Its slug appears only in `hub.php`'s exclusion filter list, which is correct behaviour (it filters out legacy wrapper slugs from other still-old-wrapper peers that may share the `wbcomplugins` parent).

**PASS — fully resolved.**

---

## Findings (severity-ranked)

### LOW

| # | Severity | File:line | Finding |
|---|---|---|---|
| 1 | LOW | `includes/class-buddypress-share.php:75` | Hardcoded version string `'2.0.0'` in `Buddypress_Share` constructor. This value flows to `Buddypress_Share_Public` and is used as the cache-bust version for `public/css/buddypress-share-public.css` and `public/js/buddypress-share-public.js`. These assets will stay cache-pinned at `2.0.0` regardless of future releases, forcing manual URL-busting. Admin assets are not affected (BPAS_Admin uses `BP_SHARE_VERSION` constant). |
| 2 | LOW | `admin/bp-icon-color-page.php:10-11` | Redundant `wp_enqueue_script('wp-color-picker')` / `wp_enqueue_style('wp-color-picker')` inside the rendered template. BPAS_Admin already enqueues both before the template runs. Harmless (WP deduplicates) but confusing. |
| 3 | LOW | `assets/css/admin.css:684` | Raw hex `#92400e` used as warn-tone text colour in `.bpas-notice--warn`. No `--bpas-admin-warn-text` token defined; the colour cannot be overridden via token cascade. |
| 4 | LOW | `assets/css/admin.css` | `.bpas-btn` has `min-height: 36px` (4px below 40px tap-target spec). `.bpas-snav-link` effective touch area ~31-32px (padding 9px top+bottom on 13px text). |
| 5 | LOW | `admin/class-buddypress-share-admin.php:468` | `do_action( 'bp_share_add_services_options', $arg1 = '', $arg2 = '' )` — inline assignment used as a function argument. PHP allows this but it is a code smell (static analysis tools flag it). Pre-existing, not introduced by migration. |
| 6 | LOW | `admin/class-buddypress-share-admin.php:314,373` | `<h3>` elements as direct children of `<ul>` — invalid HTML5 (only `<li>` is valid inside `<ul>`). This is pre-existing legacy markup not introduced by migration but worth noting for future refactor. |
| 7 | LOW | `admin/bp-icon-color-page.php:98` | Whatsapp color input has `class="bp_share_wordpress"` (same class as the WordPress field, line 68). No CSS or JS targets this class so there is no functional impact, but it is incorrect metadata. Pre-existing. |

### (Pre-existing, deferred per BUILD-LOG — not new regressions)

| # | Severity | File:line | Finding |
|---|---|---|---|
| D1 | MEDIUM (deferred) | `public/class-buddypress-share-public.php:144` | Frontend no-JS gap: `.service-buttons` hidden with inline `style="display:none"` and no PHP/no-JS fallback. Share targets unreachable without JavaScript. Explicitly out of migration scope. |
| D2 | LOW (deferred) | `admin/class-buddypress-share-feedback.php:175` | Inline `<style>` block in the review-nag feedback renderer. Gate F1 violation, pre-existing, not part of wrapper surface. |
| D3 | LOW (deferred) | `uninstall.php` | No option cleanup — `bp_share_*` / `bpas_*` options left in DB on uninstall. Pre-existing. |

---

## Scores

| Lens | Grade | Notes |
|---|---|---|
| Controller contract | A | All required methods present and correctly wired |
| Path constants | A | All constants resolve to plugin root; all asset paths verified on disk |
| Tab↔view map | A | 3/3 complete, no dead links, no orphan views |
| Option wiring | A | All 3 live option keys: registered, rendered, sanitized, read back |
| AJAX handlers | A | All 4 handlers retained, nonce + cap on every handler |
| Legacy cleanup | A | admin/wbcom/ deleted, all dangling refs confirmed gone |
| Free/Pro guard | A | Intact at both activation and runtime |
| alert/confirm ban | A | bpasToast + bpasConfirm used; no native alert/confirm |
| Screen-scoped enqueue | A | is_our_screen() gates all asset loading |
| Focus rings | A | focus-visible correct; no bare outline:none |
| CSS tokens | B+ | One raw hex (#92400e) outside token system |
| Tap targets | B | bpas-btn 36px, snav-link ~31px — both below 40px spec |

---

## Overall verdict: SHIP-CLEAN

No blockers. No high-severity findings. All findings are low-severity polish items, and items D1–D3 are pre-existing issues explicitly deferred in the BUILD-LOG. The migration is structurally correct: path constants resolve to the plugin root, both legacy wrapper and dead "Our Themes" page are gone, all three option keys are wired end-to-end with the correct storage mechanism preserved, and the custom AJAX save (drag/drop service ordering) remains fully intact with nonce + capability on all four handlers.

**Recommended follow-up (low priority, not required for ship):**
1. Fix version string in `includes/class-buddypress-share.php:75` to use `BP_SHARE_VERSION` so public asset cache-busting works correctly across releases.
2. Add `--bpas-admin-warn-text` token for the `#92400e` value.
3. Raise `min-height` on `.bpas-btn` to 40px and increase padding on `.bpas-snav-link` to meet tap-target spec.
4. Remove redundant `wp_enqueue_*` calls from `admin/bp-icon-color-page.php:10-11`.
