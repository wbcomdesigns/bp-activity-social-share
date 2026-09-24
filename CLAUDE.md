# Activity Share for BuddyPress (free)

wp.org slug `bp-activity-social-share` - main file `buddypress-share.php` (never rename: sites would lose the active plugin on update). Pro add-on: `buddypress-activity-share-pro` extends this plugin through its public API; Free and Pro always ship the **same version**.

Plans: `docs/plans/3.6.0-PLAN.md` (product plan, canonical copy shared with Pro) and `docs/plans/3.6.0-IMPLEMENTATION-FREE.md`.

## Rules
- Follow the `/wp-plugin-development` skill and the `ux-foundation` design system.
- **Public API = `includes/functions.php` + documented hooks.** Pro and themes use nothing else. Changing a signature needs a deprecation for one minor version.
- One settings store: `bpas_settings` via `BPAS\Settings`. No new options without an owner-facing reason (plan section 12: settings are for site owners; developer needs go to filters).
- Private content rule is fixed, not a setting: `BPAS\Visibility::can_share_externally()` decides; private/hidden group activity never offers outside sharing.
- One share menu renderer (`BPAS\Share_Menu` + `templates/share-menu.php`) for every surface.
- Text domain `bp-activity-social-share`. Every user-facing string translatable, incl. JS strings passed via inline data.
- No inline `<script>` / `<style>` in PHP; CSS is token-only (`assets/css/bpas-tokens.css` chains the host theme's tokens); logical properties only; two width media blocks (1024 / 640) at the bottom of each file.
- The theme-protection block in `bpas-share.css` uses `!important` on purpose: BuddyPress themes style `#buddypress .activity-meta a` with ID selectors. Keep it scoped to `.bpas-share__panel`.
- Upgrades run from `BPAS\Upgrade` on `init` (never the activation hook). Bump `BPAS_DB_VERSION` with every migration.

## Layout
| Path | Role |
|---|---|
| `includes/class-plugin.php` | Boot, guards (Youzify, old Pro < 3.6.0), module wiring on `bp_init` |
| `includes/class-settings.php` | `bpas_settings` store |
| `includes/class-networks.php` | Network registry + `bpas_register_network()` |
| `includes/class-share-context.php` | What is shared (url, title, text, image) for activity / post / member / group |
| `includes/class-visibility.php` | Who may share outside |
| `includes/class-share-menu.php`, `templates/share-menu.php` | The menu |
| `includes/class-assets.php`, `assets/` | CSS / JS (built by `npm run build`: `.min`, `-rtl`) |
| `includes/class-open-graph.php` | Link-preview tags |
| `includes/integrations/` | Activity, member header, group header |
| `includes/api/` | REST `bpas/v1/settings` (admin uses it) |
| `includes/admin/` | WB Plugins > Activity Share (hub landing + notice suppression follow the portfolio standard) |
| `includes/migrations/` | Versioned data migrations |

## Commands
- `npm run build` - assets
- `bin/local-ci.sh` - lint, WPCS, versions, Free/Pro version pair, inline script/style, hex, i18n, plan sync
- `bin/build-release.sh` - clean zip in `dist/`
