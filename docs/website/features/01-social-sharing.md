# Social sharing

Every activity post gets one **Share** button that opens a menu of the networks the site owner has enabled.

## Networks

Built-in networks: Facebook, X, LinkedIn, WhatsApp, Telegram, Bluesky, Reddit, Pinterest, Email and Copy link. The site owner picks which ones show and in what order - see [Networks settings](../settings/01-networks.md).

Instagram, TikTok, YouTube and Facebook Groups do not accept a shared link from a website, so they are not in the list. On a phone, the **More options** button opens the phone's own native share sheet, which can send the link to those apps directly.

## Copy link

**Copy link** copies the post's URL to the clipboard and shows a short "Link copied" message. It works for members and, where allowed, for visitors too.

## Link previews (Open Graph)

When a post is shared, the destination network shows a preview card built from the post's own image and text, so it looks right on Facebook, LinkedIn and WhatsApp. If an SEO plugin (Yoast SEO, Rank Math, All in One SEO or SEOPress) is already printing its own preview tags, Activity Share steps aside automatically so tags are never duplicated.

## Extending the network list

Developers can add a network - for example Mastodon - with one function call. See the [developer guide](../developer-guide/01-hooks.md).
