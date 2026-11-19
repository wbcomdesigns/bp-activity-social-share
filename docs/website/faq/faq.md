# Frequently asked questions

**Can members share to Instagram, TikTok or YouTube?**
Those apps don't accept shared links from websites. On phones, members tap **More options** to open the phone's own share menu and pick any installed app.

**Why can't I share to a Facebook Group?**
Facebook's share window decides where a post can go; posting to a group depends on the member's own Facebook permissions.

**Why is there no Share button on a post in a private group?**
Only members can see those posts, so a link on another site wouldn't open for anyone else. Members can still copy the link for other members inside the group.

**Can I change the word "Share" or move the button?**
Yes - on the [Appearance](../settings/02-appearance.md) tab you can set your own label and choose whether Share appears before or after the other activity buttons.

**How do I stop the link-preview (Open Graph) tags?**
They stop automatically when Yoast SEO, Rank Math, All in One SEO or SEOPress is active. Otherwise: `add_filter( 'bpas_open_graph_enabled', '__return_false' );`

**Which BuddyPress version do I need?**
BuddyPress 12.0 or later, or BuddyBoss Platform. See [Requirements](../getting-started/01-requirements.md).

**Does Activity Share for BuddyPress work with BuddyBoss Platform or Youzify?**
BuddyBoss Platform: supported on a best-effort basis. Youzify: not supported, because it ships its own sharing feature - both plugins stay inactive while Youzify is active.

**Does Activity Share Pro work without the free plugin?**
No. Activity Share Pro is an add-on - install and activate Activity Share for BuddyPress first. WordPress refuses to activate Pro while Free is missing or off, and tells you to activate Free first. If you deactivate Free later, Pro pauses with one notice and a one-click link to activate Free again; your Pro settings are kept.

**Do I need a license key to use Activity Share Pro?**
No. Every Pro feature works with or without an active license key. The license only controls whether the plugin receives automatic updates.

**Can visitors repost or send a post to a friend?**
No. Visitors see the **Repost** and **Send to a friend** rows, but choosing one opens a **Join the conversation** box asking them to log in or register.

**Visitors cannot share on my upgraded site. Why?**
The **Visitors can share** switch is on for new installs. A site that had 3.5.x network settings keeps its earlier behaviour, where visitors could not share, until you turn the switch on under **WB Plugins > Activity Share > Networks**.

**What happens to my data if I delete the plugin?**
See [Updates, uninstall and your data](../settings/03-updates-and-uninstall.md).

**Is Activity Share for BuddyPress translation-ready?**
Yes, including right-to-left languages. Every user-facing string, including JavaScript messages, is translatable.

**Can I add a network that isn't built in?**
Yes - developers can register any network with `bpas_register_network()`. See the [developer guide](../developer-guide/01-hooks.md).
