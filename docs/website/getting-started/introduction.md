# Introduction

BuddyPress Activity Social Share is a free WordPress plugin by Wbcom Designs that adds a "Share" button to each entry in the BuddyPress activity stream. When a member opens the button, a row of social network icons appears, letting them post the activity to their own social profiles or send it by email.

The plugin also adds Open Graph and Twitter Card meta tags to single activity pages, so a shared link shows a title, description, and image on the destination network.

## What it does

- Adds a "Share" button to the activity stream (BuddyPress `bp_activity_entry_meta`).
- Opens a set of social network share links for the activity permalink.
- Adds Open Graph and Twitter Card meta tags to single activity pages.
- Lets the site owner choose which networks are enabled, reorder them, and set each icon color from the admin.

## Supported networks

The following networks have working share links in the plugin:

- Facebook
- X (Twitter)
- Pinterest
- Reddit
- WordPress (Press This)
- Pocket
- LinkedIn
- WhatsApp
- E-mail

Every network in this list is enabled or disabled individually from the plugin settings.

## Requirements

- WordPress 4.0 or later (tested up to 6.7.0).
- BuddyPress installed and active. The plugin only loads on the `bp_loaded` hook when the `BuddyPress` class exists, and it deactivates itself with an admin notice if BuddyPress is missing.
- The plugin must be activated on the same site (and same network configuration) as BuddyPress.
- The free version cannot run at the same time as BuddyPress Activity Social Share Pro. If the Pro version is active, the free version deactivates itself.

## Who it is for

Any BuddyPress community that wants members to be able to push activity updates out to social networks and to have those shared links display rich previews.
