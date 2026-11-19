# Installation

## Install the free plugin

1. In wp-admin, go to **Plugins > Add New**.
2. Search for **Activity Share for BuddyPress**.
3. Click **Install Now**, then **Activate**.
4. That's it - every activity post now has a **Share** button, using the default networks.

You can also upload the plugin zip file under **Plugins > Add New > Upload Plugin** if you downloaded it directly.

## Add Activity Share Pro (optional)

1. Make sure the free plugin is installed and active first.
2. Upload and activate **Activity Share Pro** the same way.
3. Pro opens **WB Plugins > Activity Share > Features** after a single activation. The extra **Features**, **Content types**, **Analytics** and **License** tabs sit next to the free **Networks** and **Appearance** tabs.
4. Paste your license key on the **License** tab and click **Activate License** to receive updates. Every Pro feature works with or without an active license - the license only controls updates.

If the free plugin is missing or inactive, WordPress refuses to activate Pro and tells you to activate the free plugin first.

## Upgrading from an earlier version

If you are updating from Activity Share 3.5.x or Activity Share Pro 2.x, your existing network choices and "visitors can share" setting move to the new settings automatically the first time the site loads after the update. The old settings are removed once the move is confirmed.

- The new **Visitors can share** switch is on for new installs. A site that had 3.5.x network settings keeps what 3.5.x did: if visitors could not share before, the switch is off after the update, and you can turn it on under **WB Plugins > Activity Share > Networks**.
- Pocket and WordPress sharing no longer exist and are dropped from your list. Twitter is now **X**.
- If the move cannot be confirmed, the old settings are kept and the move is retried on the next page load.

If your site still has an old Activity Share Pro (older than 3.6.0) active when you update the free plugin, the free plugin stands aside and shows one admin notice asking you to update Pro. Your site keeps working with the old Pro version until you do.

Next: [your first share](03-first-share.md).
