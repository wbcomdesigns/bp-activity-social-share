#!/usr/bin/env bash
# Build dist/bp-activity-social-share-{version}.zip (runs local CI first).
set -euo pipefail
cd "$(dirname "$0")/.."
slug=bp-activity-social-share
main=buddypress-share.php
npm run build --silent
bin/local-ci.sh
version=$(grep -m1 -E '^\s*\*\s*Version:' "$main" | awk '{print $NF}')
rm -rf dist && mkdir -p "dist/$slug"
rsync -a --exclude-from=<(sed 's#^/##' .distignore) --exclude=dist ./ "dist/$slug/"
zip="$PWD/dist/$slug-$version.zip"
# Deflate every entry: a PHP file stored uncompressed trips the store's upload firewall.
( cd dist && python3 -I -c 'import os,sys,zipfile
out,top=sys.argv[1],sys.argv[2]
with zipfile.ZipFile(out,"w",zipfile.ZIP_DEFLATED,compresslevel=9) as z:
    for r,d,f in os.walk(top):
        d.sort()
        for n in sorted(f): z.write(os.path.join(r,n))' "$zip" "$slug" )

# --- WordPress.org folder lock (Wbcom standard - do not edit) ----------------------
entries=$(unzip -Z1 "$zip")   # read the whole list once: no early-exit pipe, safe under pipefail
top=${entries%%/*}
[ "$top" = "$slug" ] || { echo "Zip folder '$top' is not the WordPress.org slug '$slug'"; exit 1; }
grep -qx "$slug/$main" <<<"$entries" || { echo "Main file $slug/$main missing from zip"; exit 1; }
[ "$(LC_ALL=C grep -a -c '<?php' "$zip" || true)" = 0 ] || { echo "Zip has plain-text PHP (upload firewall)"; exit 1; }
# -----------------------------------------------------------------------------------

leaks=$(unzip -l "$zip" | grep -iE '/docs/|/audit/|/bin/|/tests/|node_modules|CLAUDE|\.md$' || true)
[ -z "$leaks" ] || { echo "Zip contains dev files:"; echo "$leaks"; exit 1; }
echo "Built dist/$slug-$version.zip"
