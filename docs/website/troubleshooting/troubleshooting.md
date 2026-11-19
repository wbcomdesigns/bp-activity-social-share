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

## There is no Share button at all, and a notice shows on the Plugins or Dashboard screen

- **"Activity Share for BuddyPress needs BuddyPress or BuddyBoss Platform to be active."** Activate BuddyPress or BuddyBoss Platform.
- **"Activity Share for BuddyPress does not support Youzify and is inactive while Youzify is active."** Youzify has its own sharing. Deactivate Youzify to use this plugin.
- **"Activity Share Pro needs to be updated to version 3.6.0 or newer. Until then, sharing is provided by your current Pro version."** An old Activity Share Pro (older than 3.6.0) is active. Free stands aside so the site keeps working. Update Pro to finish the upgrade.

## There is no Share button on one activity item

Items with no text that are not reposts or blog-post updates, such as "joined the group" or "became friends with", have nothing to share and get no button. Items in private or hidden groups get a menu with only **Copy link** (when that is switched on) and no network links.

## Activity share buttons and reposting are missing

A notice at the top of **WB Plugins > Activity Share** says when the BuddyPress **Activity** component is off. Sharing still works on groups and member profiles, but sharing from activity posts and reposting (Pro) need Activity. Turn it on under **BuddyPress > Settings > Components**.

## Still stuck?

Problems with reposting, Send to a friend, short links, share analytics, the share image or the Pro license are answered in the Activity Share Pro troubleshooting guide. For anything else, check the [FAQ](../faq/faq.md), or contact Wbcom Designs support with your WordPress version, PHP version, the plugin versions (Free and Pro), and the exact steps to reproduce the issue.
