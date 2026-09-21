# Features Overview

## Share button on activity entries

The plugin adds a "Share" button to every activity entry through the BuddyPress `bp_activity_entry_meta` action. Clicking the button slides open a row of enabled network icons for that activity. The button is shown only to logged-in users, and only when at least one sharing service is enabled.

## Supported networks

Each of these networks has a working share link in the plugin and can be turned on or off individually:

| Network | What it shares |
|---|---|
| Facebook | The activity permalink, through the Facebook sharer. |
| X (Twitter) | The activity permalink plus the activity title. |
| Pinterest | The activity permalink plus a description. |
| Reddit | The activity permalink plus the activity title. |
| WordPress | The activity permalink through WordPress.com Press This. |
| Pocket | The activity permalink plus the activity title. |
| LinkedIn | The activity permalink plus text. |
| WhatsApp | The activity permalink through wa.me. |
| E-mail | A mailto link with a subject and body built from your site title and the activity link. |

The E-mail and WhatsApp links open directly rather than in a popup window.

## Choose and reorder networks

From the General Settings tab you drag networks between an Enable list and a Disable list, and you can reorder the enabled networks by dragging. The order you set is the order the icons appear on the front end.

## Popup or new tab

An "Open as popup window" option controls whether share links open in a centered popup or in a new browser tab. WhatsApp and E-mail always open directly regardless of this setting.

## Custom icon colors

The Icon Color Settings tab gives each network its own color picker. Colors are applied to the icons on the front end. Defaults are set on activation.

## Open Graph and Twitter Card meta

On a single activity page the plugin outputs Open Graph and Twitter Card meta tags (type, url, title, description, image). This helps shared links show a proper preview on the destination network.

For the activity image, the plugin looks for media in this order:

- The first image found in the activity content.
- BuddyBoss / BuddyPress media attached to the activity (`BP_Media`).
- Youzer or Youzify media attached to the activity.

## Change the button label

The button text ("Share") can be changed by developers through a filter. See [Hooks, Filters and Options](../developer-guide/hooks-and-filters.md).

## What is not included

- There is no share count, share analytics, or click counter. The plugin does not track how many times an activity was shared.
- The copy to clipboard front-end button is not rendered by the free version's activity template. See [Troubleshooting](../troubleshooting/troubleshooting.md) for details.
