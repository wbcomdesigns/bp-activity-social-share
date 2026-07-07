# Refactor plan — finish the admin migration

Small plugin, one focused pass. Do this in a **new branch off `3.5.5` after the current QA cycle** (don't churn what QA is testing). Local plan only — not committed.

## Goal
Kill the dual admin system. End state: one admin owner, one admin CSS, one admin JS, one prefix for new code.

## Steps (in order)

1. **Move the General tab body onto bpas-card.**
   - `admin/class-buddypress-share-admin.php::bpas_general_setting_section()` currently renders the legacy `wbcom-*` drag/drop markup. Rebuild it as a `bpas-card` template like `admin/bp-welcome-page.php` / `admin/bp-icon-color-page.php` (Disable/Enable lists as two cards; keep the `.socialicon` drag `<li>`s + `#drag_social_icon`/`#drag_icon_ul` ids so `admin/js/buddypress-share-admin.js` still binds).
   - Keep the AJAX save (`bp_share_chb_services_ajax`, `bp_share_sort_social_links_ajax`, `wss_*`) untouched — only the markup/CSS changes.

2. **Collapse the admin class into one owner.**
   - The tab-body renderers (`bpas_general_setting_section`, `bpas_icon_color_setting_section`, welcome) live in the OLD `admin/class-buddypress-share-admin.php` but are called by the NEW `includes/admin/class-bpas-admin.php` shell. Move those renderers under `includes/admin/` so the shell owns them directly instead of delegating to the legacy class. Retire the legacy class once empty.

3. **Retire the legacy admin assets.**
   - After steps 1-2, nothing should reference `admin/css/buddypress-share-admin.css` (483 ln) or need the legacy breakpoints. Fold any surviving `.socialicon`/drag rules into `assets/css/admin.css`, then **delete** the legacy CSS file. Same for `admin/js/buddypress-share-admin.js` — fold the drag/drop + AJAX save into `assets/js/admin.js` (or a single `assets/js/admin-general.js`), delete the legacy file. One admin CSS, one admin JS.

4. **Standardize naming (new code only).**
   - Use the `bpas_` prefix for anything new. Do NOT rename existing `bp_share_*` / `wss_*` options/hooks without back-compat shims — those are breaking and read by Pro. Just stop adding new prefixes.

5. **Refresh the manifest.**
   - `audit/manifest.json` still lists the deleted `wbcom-plugins-page` / `wbcom-themes-page` / `wbcom-support-page` menus and `wbcom_addons_cards` AJAX (removed in `fff4b3d`). Run `/wp-plugin-onboard --refresh`.

## Out of scope (separate, optional)
- Namespacing the `Buddypress_Share_*` classes (cosmetic, low value).
- **No-JS frontend fallback** — `.service-buttons` are `display:none` with no fallback; share targets unreachable without JS. Functional gap, its own card.

## Verify (per step)
Browser at 1440/390: General drag/drop still saves; Icon Colors picker still opens; Welcome intact. WPCS clean. Confirm legacy files are gone (`git rm`), not just unreferenced.
