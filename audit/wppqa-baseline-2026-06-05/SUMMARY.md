# wppqa Baseline — buddypress-activity-social-share

- **Plugin:** Wbcom Designs - BuddyPress Activity Social Share
- **Version:** 3.5.5  |  **Branch:** 3.5.5
- **Date:** 2026-06-05
- **Path:** wp-content/plugins/buddypress-activity-social-share

## Per-check results

| Check | Passed | Failed | Skipped | Notes |
|---|---|---|---|---|
| `wppqa_check_plugin_dev_rules` | 8 | 1 | 0 | 1 high: nonce-no-cap |
| `wppqa_check_rest_js_contract` | 0 | 0 | 1 | Skipped — plugin exposes no REST routes |
| `wppqa_check_wiring_completeness` | 0 | 0 | 1 | Skipped — no `templates/` dir for the scanner; settings reads live in `public/` + admin classes |

`wppqa_audit_plugin` failed-count > 0 ⇒ **NOT release-ready** until the 1 finding is triaged/resolved.

## Findings

### 1. [high] Nonce check without capability check — TRIAGED: real but low impact
- **File:** `admin/wbcom/wbcom-admin-settings.php:43`
- **Handler:** `Wbcom_Admin_Settings::wbcom_addons_cards_links()` (AJAX `wbcom_addons_cards`)
- **Detail:** Verifies `wbcom_admin_setting_nonce` but never calls `current_user_can()`. The handler only `echo esc_html( $display_extension )` of a sanitized POST string and `die`s — it performs no privileged write. Nonce is created only inside an admin-only enqueue, so practical exposure is limited, but per `security.md` a nonce authenticates intent, not authority.
- **Recommendation:** add `current_user_can( 'manage_options' )` to the guard for defense-in-depth. NOT a functional bug.

## False-positive pre-triage (per environment note)
- `wppqa_check_plugin_dev_rules` does not model `nopriv` AJAX. This plugin has **no** `wp_ajax_nopriv_*` actions (all 5 AJAX actions are logged-in/admin only), so no 'nonce-no-cap' false positives arose from public endpoints on this run. The single finding above is on an admin action and is genuine (low impact).

## Manifest-relevant context
- Permission model: every admin AJAX pairs `bp_share_nonce` + `current_user_can('manage_options')` EXCEPT `wbcom_addons_cards` (the finding above).
- No REST, no blocks, no CPT/taxonomy, no cron, no custom tables.
