# What's new in 3.6.0

3.6.0 is a rebuild of the share menu: faster, usable with a keyboard and without JavaScript, open to visitors, and styled by the plugin so it looks the same on any theme. Read the update notes and run the short retest below before you tell your members the update is done.

## Update notes

- **WordPress 6.7 and PHP 8.1 are required.** A site on older PHP cannot activate 3.6.0. Check your hosting PHP version before updating.
- **BuddyPress 12.0 or later, or BuddyBoss Platform,** is required.
- **Your settings move automatically** the first time the site loads after the update. Network choices and the visitors setting are carried over and the old entries are removed. See [Upgrading from an earlier version](../getting-started/02-installation.md#upgrading-from-an-earlier-version).
- **Visitors can share is on for new installs.** A site that had 3.5.x network settings keeps its old behaviour; if visitors could not share before, turn the switch on under **WB Plugins > Activity Share > Networks**.
- **Twitter is now X.** Pocket and WordPress sharing were removed.
- **Update Activity Share Pro to 3.6.0 or later.** While an older Pro is active, this plugin stands aside and shows a notice, and your existing settings are kept.
- **Translations from translate.wordpress.org now load.**
- **A custom button or menu style may need a second look.** The share button and menu are now styled by the plugin itself, so CSS written for the old menu no longer applies.

## What changed for site owners

- **Visitors can share public posts.** Switch it off under **Who and where**. See [Privacy and guest sharing](../features/03-privacy-and-guest-sharing.md).
- **Share icon on member profiles and public groups,** next to the profile's own buttons. See [Sharing profiles and groups](../features/02-profiles-and-groups.md).
- **Copy link** with a "Link copied" message.
- **More options** opens the phone's own share menu, so members can share to Instagram, Messages and other installed apps.
- **New networks:** Telegram, Bluesky and Reddit, with X replacing Twitter. See [Social sharing](../features/01-social-sharing.md).
- **Appearance settings** for the button label, its position and the icon colours (each network's colour or your theme's), with a live preview. See [Appearance](../settings/02-appearance.md).
- **Network order** can be changed with up and down buttons that work with a keyboard.
- **The menu is a bottom sheet on phones** with large touch targets, follows your theme's colours and dark mode, and works with a keyboard, screen readers, right-to-left languages and without JavaScript.
- **Updates with nothing to share,** such as "joined the group", no longer show a Share button.
- **Link previews** use the post's own image and step aside when Yoast SEO, Rank Math, All in One SEO or SEOPress already prints preview tags.
- **A new settings screen** under **WB Plugins > Activity Share** with grouped settings and a Resources list.

## Fixes you may have noticed

- Networks you turn off no longer appear on activity.
- Saving settings with no changes no longer shows an error.
- Posts in private and hidden groups no longer offer sharing to other sites.
- The share menu is no longer cut off inside profile headers and reply threads, or behind sticky theme headers such as Reign's.
- The Share button appears in member and group headers on BuddyBoss Platform.
- Upgrading to Activity Share Pro no longer causes an activation error.
- Link previews and shared text use the activity as the viewer sees it, so details that other plugins hide, such as a check-in's street address, are not leaked. Posts the viewer cannot see get no preview text.

## Retest after updating

1. Open **WB Plugins > Activity Share > Networks** and check the networks you use are ticked, in the order you want.
2. Check **Visitors can share** is set the way you want it.
3. Open the activity stream in a private browser window, click **Share** on a public post and check the menu, **Copy link** and a network.
4. Open a post in a private group and check no network links appear.
5. Check the **Share** icon on a member profile and on a public group.
6. Open the site on a phone and check **More options** and the bottom sheet.
7. If you use Activity Share Pro, check it is on 3.6.0 or later and the notice about an old Pro is gone.

## For developers

- Settings now live in one option, `bpas_settings`. The old `bp_share_*` options are migrated and removed, so code that read them must use `bpas_get_setting()` instead. See [Public functions, REST API and template override](../developer-guide/02-integration.md).
- The share menu template can be overridden in your theme at `bp-activity-social-share/share-menu.php`.
- Networks are now added with `bpas_register_network()`. See [Hooks](../developer-guide/01-hooks.md).
