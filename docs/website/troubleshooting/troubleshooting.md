# Troubleshooting

## The Share menu is cut off or hidden by the theme

Activity Share automatically detects when the menu would open past the edge of the screen or inside a container that clips overflow, and adjusts its position (or temporarily lifts the clipping) while the menu is open. If a menu is still cut off:

1. Confirm you are on the latest version of both the plugin and your theme.
2. Check for a very unusual container (for example `overflow: hidden` combined with a fixed height) around the activity stream - this is the one case the automatic fix cannot reach.
3. Report the theme and page so the containing selector can be added to the fix.

## The button label or "Share" text is not translated

1. Confirm your site's language has an available translation, or add your own with a plugin like Loco Translate, using the text domain `bp-activity-social-share` (or `buddypress-activity-share-pro` for Pro strings).
2. If you only want to change the English word "Share", set your own **Button label** on the [Appearance](../settings/02-appearance.md) tab instead of relying on translation.

## The menu does not follow dark mode

Activity Share follows your theme's own colour tokens (BuddyX, BuddyX Pro, Reign and most themes using standard WordPress colour tokens). If your theme uses a non-standard dark-mode method, the menu may keep its light colours. This is a theme compatibility gap, not a broken setting - report the theme name so support can add a token mapping for it.

## Link previews look wrong or duplicated when sharing

- **Wrong image or text:** the preview is built from the post's own image and text. If it looks wrong, check the post has a featured image or a usable first image.
- **Duplicated tags:** if you also run an SEO plugin (Yoast SEO, Rank Math, All in One SEO, SEOPress), Activity Share detects it and steps aside automatically. If you still see duplicate tags, confirm only one of the two is configured to output Open Graph tags for that page type.

## Visitors do not see the Share button

Check **Visitors can share** is turned on under [Networks settings](../settings/01-networks.md). This setting only affects **public** posts - visitors never see sharing options on private or hidden group content, by design.

## Activity Share Pro will not activate, or a notice keeps appearing

- **"Activity Share Pro requires the free plugin":** install and activate **Activity Share for BuddyPress** first, then Pro.
- **"Update Activity Share Pro":** your Free plugin is newer than Pro. Update Pro to the same version.
- **"Sharing is provided by your current Pro version":** you have an old Activity Share Pro (older than 3.6.0) active with a newer Free plugin. Free stands aside so your site keeps working; update Pro to 3.6.0 or later to finish the upgrade.

Free and Pro must always run the **same version number** - update them together.

## Share analytics shows zero

Share analytics is **off by default**. Turn it on under **WB Plugins > Activity Share > Features**, then give it a little time - clicks and visits only start counting from the moment it is switched on. Reposts and sends to friends are counted even with analytics off, so if those also show zero, confirm the **Repost** and **Send to a friend** features are turned on too.

## A short share link (`/s/...`) returns a 404

This usually means WordPress permalinks need refreshing after activation or an update. Go to **Settings > Permalinks** and click **Save Changes** without changing anything - this rebuilds the rewrite rules the short links depend on.

## Still stuck?

Check the [FAQ](../faq/faq.md), or contact Wbcom Designs support with your WordPress version, PHP version, the plugin versions (Free and Pro), and the exact steps to reproduce the issue.
