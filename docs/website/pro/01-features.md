# Activity Share Pro features

> **Pro feature** - requires Activity Share Pro (same version as the free plugin).

**WB Plugins > Activity Share > Features** - one switch per feature.

| Feature | What members / owners get | Default |
|---|---|---|
| Repost | **Repost** (instant, with Undo) and **Repost with comment** to your profile or a group. @mentions work in the comment. | On |
| Send to a friend | Sends the post to a friend as a private message. Needs the BuddyPress Friend Connections and Private Messaging components. | On |
| Share replies | Replies get their own share menu and can be reposted. Needs Repost to be on as well. | On |
| Counts and who reposted | Share count next to Share, and a list of who reposted. | On |
| Share image for text posts | Text-only posts get a branded image when shared to Facebook, LinkedIn or WhatsApp. | On |
| Short share links | Links like `yoursite.com/s/abc123` that credit the member who shared. | On |
| Share analytics | Counts clicks on share buttons and visits from shared links (no IP addresses stored). | Off |

## Rules that always apply

These are fixed behaviour, not settings:

- Posts in private and hidden groups can only be reposted inside their group.
- Reposting a repost reposts the original post.
- Members can repost their own posts only into a group, not back onto their own profile.
- Members can turn off "Allow other members to repost my posts" in **Settings > General**.
- Group admins of public groups can untick "Members can share posts from this group to other sites (Facebook, X, WhatsApp...)" and "Members can repost posts from this group to their profile or other groups" in the **Sharing** box under **Manage > Settings**. Both are on by default.

## Share rewards

Points for shares that bring a visitor or a new member turn on by themselves when **WB Gamification**, **GamiPress** or **myCRED** is active. Set the point values in that plugin. They are earned through short share links, so keep **Short share links** on. **WB Plugins > Activity Share > Features** shows a read-only Share rewards line naming the active points plugin, or telling you to activate one.

## Notifications

Authors get one grouped notification ("Anna and 5 others reposted your update") and at most one email per post per day. Members can switch the email off in **Settings > Email**.

See also: [Content types](02-content-types.md) - [Analytics](03-analytics.md) - [License](04-license.md)
