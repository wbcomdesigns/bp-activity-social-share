# Troubleshooting

## The plugin will not activate, or shows a "requires BuddyPress" notice

The plugin requires BuddyPress. Install and activate BuddyPress, then activate this plugin. If you see a notice that BuddyPress is required, that is the cause.

## A notice says the Pro version is already active

The free and Pro versions cannot run together. If BuddyPress Activity Social Share Pro is active, the free version deactivates itself. Keep only one of the two active.

## On multisite, a notice says it must share the same network configuration

The plugin stores its settings as network options and must be activated on the same site and with the same network configuration as BuddyPress. Activate it where BuddyPress is active.

## The Share button does not appear

Check each of these:

- You are logged in. The button is shown to logged-in members only.
- At least one network is enabled on the General Settings tab. If none is enabled, the button area shows "Please enable share services!" instead.
- BuddyPress is active and the activity stream is displaying entries.

## The button area shows "Please enable share services!"

No sharing network is enabled. Go to the General Settings tab and drag at least one network into the Enable list.

## A network icon does not show even though I enabled it

The front-end button row and the admin lists match network names case for network name. If a network was added or edited in an unexpected way, remove it from the Enable list and drag it back in so it is stored under the expected name. Then reload the activity stream.

## Links open in the wrong way (popup vs new tab)

Adjust the "Open as popup window" checkbox on the General Settings tab. WhatsApp and E-mail always open directly and cannot be forced into a popup.

## Icon colors do not change

- Confirm you saved the Icon Color Settings tab.
- An empty color field is not applied, so the icon keeps the theme's default color. Set an explicit color if you want to override the theme.
- Theme styles can override the plugin's inline color. If a color still does not apply, check the theme's CSS for a more specific rule on the icon.

## Shared links do not show a preview image

The Open Graph image is taken, in order, from the first image in the activity content, then from attached BuddyBoss / BuddyPress media, then from Youzer / Youzify media. If an activity has no image in any of these, the shared link has no preview image. Some networks also cache previews; use the network's own debugger to refresh the cache.

## There is no copy to clipboard button on the front end

The plugin's front-end script includes a copy to clipboard handler, but the free version's activity template does not render a copy button, so nothing is displayed for it. This is expected in the free version.
