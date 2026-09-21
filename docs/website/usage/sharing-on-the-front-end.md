# Sharing on the Front End

This page describes what members see and do once the plugin is set up.

## Where the button appears

A "Share" button is added to each activity entry in the BuddyPress activity stream, in the entry's action area (the `bp_activity_entry_meta` area alongside the other activity buttons).

## Who sees it

The button is shown only to logged-in members. It also appears only when at least one sharing network is enabled in the settings. If no network is enabled, the button area shows a "Please enable share services!" message instead.

## How members share

1. The member clicks the "Share" button on an activity.
2. A row of network icons slides open below the button.
3. The member clicks a network icon.
4. The network's share dialog opens, pre-filled with that activity's permalink (and, depending on the network, its title or a description).

Depending on the "Open as popup window" setting, the network opens either in a centered popup window or in a new browser tab. WhatsApp and E-mail always open directly.

## What gets shared

- Facebook, WhatsApp, and WordPress share the activity permalink.
- X (Twitter), Reddit, Pocket, Pinterest, and LinkedIn share the permalink together with the activity's title or a description.
- E-mail opens the member's mail client with a subject and message body that include your site title and the activity link.

## Link previews

When someone opens a shared single-activity link, the plugin's Open Graph and Twitter Card meta tags let the destination network show a title, description, and image for the activity. The image is taken from the activity content or from attached BuddyBoss, BuddyPress, Youzer, or Youzify media where available.
