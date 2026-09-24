#!/usr/bin/env bash
# Local CI for Activity Share for BuddyPress. Exit 1 on any failure.
set -uo pipefail
cd "$(dirname "$0")/.."
fail=0
step() { printf '\n== %s\n' "$1"; }
bad() { echo "FAIL: $1"; fail=1; }

step "PHP lint"
while IFS= read -r f; do php -l "$f" >/dev/null || bad "lint $f"; done < <(find . -name '*.php' -not -path './node_modules/*')

step "WPCS (errors)"
phpcs -q -n || bad "WPCS errors"

step "Version triangulation"
hv=$(grep -m1 -E '^\s*\*\s*Version:' buddypress-share.php | awk '{print $NF}')
cv=$(grep -m1 "define( 'BPAS_VERSION'" buddypress-share.php | grep -oE "[0-9]+\.[0-9]+\.[0-9]+")
rv=$(grep -m1 -i 'Stable tag' readme.txt | tr -d '\r' | awk '{print $NF}')
pv=$(node -p "require('./package.json').version")
echo "header=$hv const=$cv readme=$rv package=$pv"
[ "$hv" = "$cv" ] && [ "$cv" = "$rv" ] && [ "$rv" = "$pv" ] || bad "version mismatch"

step "Version pair with Pro (Free and Pro always ship the same version)"
pro=../buddypress-activity-share-pro/buddypress-share.php
if [ -f "$pro" ]; then
	prov=$(grep -m1 -E '^\s*\*\s*Version:' "$pro" | awk '{print $NF}')
	echo "free=$hv pro=$prov"
	[ "$prov" = "$hv" ] || bad "Free $hv != Pro $prov"
else
	echo "Pro checkout not found - skipped"
fi

step "BuddyBoss-safe URLs (bp_members_get_user_url / bp_get_group_url are BuddyPress 12+ only)"
grep -rn 'bp_members_get_user_url\|bp_get_group_url' --include='*.php' includes templates | grep -v 'includes/functions.php' && bad "use bpas_member_url() / bpas_group_url()"

step "No inline <script>/<style> in PHP"
grep -rln '<script\|<style' --include='*.php' includes templates && bad "inline script/style"

step "No raw hex outside :root in share CSS"
awk '/^:root|^\[data-theme|^html|^body/{inroot=1} inroot&&/}/{inroot=0;next} !inroot&&/#[0-9a-fA-F]{3,6}\b/{print FILENAME": "$0; found=1} END{exit found}' assets/css/bpas-share.css || bad "hex in bpas-share.css"

step "i18n: POT builds without warnings"
out=$(wp i18n make-pot . /tmp/bpas-ci.pot --domain=bp-activity-social-share --exclude=node_modules,bin,docs 2>&1)
echo "$out" | grep -qi warning && { echo "$out"; bad "make-pot warnings"; }

step "Plan copies in sync"
[ -f ../buddypress-activity-share-pro/docs/plans/3.6.0-PLAN.md ] && { diff -q docs/plans/3.6.0-PLAN.md ../buddypress-activity-share-pro/docs/plans/3.6.0-PLAN.md >/dev/null || bad "plan copies differ"; }

step "Assets built"
for f in assets/css/bpas-share.min.css assets/css/bpas-share-rtl.min.css assets/js/bpas-share.min.js; do [ -f "$f" ] || bad "missing $f (npm run build)"; done

[ $fail -eq 0 ] && echo -e "\nALL CHECKS PASSED" || echo -e "\nCHECKS FAILED"
exit $fail
