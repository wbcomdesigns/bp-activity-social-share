# Share counts and tracking

> Requires **Activity Share Pro**.

## Counts and who reposted

When the **Counts and who reposted** feature is on (Features tab), a number appears next to the Share button once a post has at least one repost. The number counts reposts only: sends to friends and clicks on network buttons are not included. When a post has reposts, the Share menu also has a **See who reposted** row that lists the members who reposted it.

## Share analytics

Turn on **Share analytics** (off by default) to also count clicks on share buttons and visits from shared links, without ever storing an IP address - visitors are counted with a one-way code that changes every day. See [Share analytics](../pro/03-analytics.md) for the full report and CSV export.

Reposts and sends to friends are always counted, even with Share analytics off.

## Short share links

Links shared from activity posts and from content types use a short form like `yoursite.com/s/abc123` instead of the full URL. The code is an opaque string of 22 characters, so no member ID is readable in it. A short link records the visit (when Share analytics is on) and credits the member who shared. It is not used for member profiles or groups, and never for private content. If your site already has a page whose address is `/s/`, short links use `/share/` instead.

## Share image for text posts

When a text-only activity post (no image) is shared to Facebook, LinkedIn or WhatsApp, Activity Share Pro generates a branded 1200 by 630 image card automatically, so the shared link still looks good in the preview. It is made for public activity posts only, and it needs the PHP GD extension with FreeType support on your server. Without it the feature does nothing and the preview falls back to your site icon or logo.

## Most shared block and widget

The **Most shared** block (and a matching legacy widget) lists the community's most-shared public posts and activity over the last 7 or 30 days. Add it from the block inserter or **Appearance > Widgets**. The list is cached for 15 minutes, so a new repost can take a while to appear in it.

## Share rewards

Points for shares that bring a visitor or a new member turn on automatically when **WB Gamification**, **GamiPress** or **myCRED** is active. Point values are set inside that points plugin, not in Activity Share Pro. Points are earned through short share links, so **Short share links** must be on, and the person who shared must have been logged in when they shared. A member is not rewarded for their own visits.
