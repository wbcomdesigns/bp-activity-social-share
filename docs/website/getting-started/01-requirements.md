# Requirements

Before you install Activity Share for BuddyPress, check your site meets these requirements.

| Requirement | Minimum |
|---|---|
| WordPress | 6.7 |
| PHP | 8.1 |
| BuddyPress | 12.0 or later, or BuddyBoss Platform |

If neither BuddyPress nor BuddyBoss Platform is active, the plugin stays idle and shows an admin notice: "Activity Share for BuddyPress needs BuddyPress or BuddyBoss Platform to be active." Activate one of them and sharing starts working.

Sharing from activity posts needs the BuddyPress **Activity** component, and sharing public groups needs the **User Groups** component. Member profiles do not need either. Turn components on under **BuddyPress > Settings > Components**.

## Activity Share Pro

If you also use **Activity Share Pro**, install and activate the free plugin first, then Pro. Free and Pro ship the same version number - update both together. If the two versions differ slightly, Pro keeps working and shows a notice naming the plugin that is behind. If Free is too old for Pro (older than 3.6), Pro pauses and shows an **Update Activity Share for BuddyPress** button while sharing keeps working from Free. If an old Pro (older than 3.6.0) is active next to a newer Free, Free steps back and keeps the site working until Pro is updated.

## Theme support

The share menu follows your theme's colours and dark mode automatically. It has been built and tested with **BuddyX**, **BuddyX Pro** and **Reign**, and works with most themes that use standard WordPress colour tokens. BuddyBoss Platform is supported on a best-effort basis. Youzify is not supported - it has its own sharing feature, so both plugins stay inactive while Youzify is active.
