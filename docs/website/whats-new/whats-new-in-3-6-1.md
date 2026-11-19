# What's new in 3.6.1

3.6.1 fixes the share menu near sticky headers, the Share button in profile and group headers, and the Share button order in the activity bar. It follows 3.6.0, released a day earlier: if you have not read [What's new in 3.6.0](whats-new-in-3-6-0.md), read it first. Activity Share Pro 3.6.1 is released with it; update both plugins together.

## Update notes

- **WordPress 6.7, PHP 8.1 and BuddyPress 12.0 or later, or BuddyBoss Platform,** are required, as in 3.6.0.
- **No settings change** in this release and nothing needs to be moved.
- **Settings labels were reworded.** The Networks tab now reads **Share member profiles** ("Adds a Share button next to the profile header buttons.") and **Share public groups**. The settings behind them are the same.

## What changed for site owners

- **Position on activity now covers every button.** With **After the other buttons**, Share prints after all other activity buttons, including buttons other plugins add. With **Before the other buttons**, it prints first.
- **The share menu no longer hides behind a sticky header.** When the menu opens upward under a header that has not pinned yet (BuddyX at the top of the page), its first rows are no longer covered. The menu also re-measures while the page scrolls, so a header that becomes sticky on scroll (BuddyX, Reign) no longer covers it. The menu closes once its button scrolls out of view.
- **The Share button in member and group headers stays visible** over a dark cover photo in Reign, because it now has its own background.
- **A notice explains why activity sharing is off.** When the BuddyPress **Activity** component is off, the Activity Share settings page now says so and links to **BuddyPress > Settings > Components**. Sharing from groups and member profiles keeps working.
- **No stray text in embeds and feeds.** The Share button no longer shows up as loose text in activity embeds and RSS feeds.

## Retest after updating

1. Open the activity stream, click **Share** on a post and check the menu opens fully, then scroll the page and check it still shows every row.
2. Open a member profile and a public group and check the **Share** icon is visible in the header, including over a dark cover photo.
3. On **WB Plugins > Activity Share > Appearance**, choose **After the other buttons** and check Share is the last button on an activity post, after any button added by another plugin.
4. If you turn the BuddyPress **Activity** component off for a moment, check the notice on **WB Plugins > Activity Share** and turn it back on.
5. Open an activity embed or an activity RSS feed and check no Share button text appears.

## For developers

- Activity and group sharing now check that the BuddyPress component is switched on before they run. `bpas_share_context( 'activity', $id )` and `bpas_share_context( 'group', $id )` return `null` while Activity or User Groups is off.
- The activity position now follows the setting through the priority of the `bp_activity_entry_meta` action (5 for before, 999 for after). Code that relied on a fixed position should print on that hook with its own priority.
