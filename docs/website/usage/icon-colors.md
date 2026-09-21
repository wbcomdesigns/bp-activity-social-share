# Icon Colors

The Icon Color Settings tab lets you set the color of each network's share icon. Go to WB Plugins, then BuddyPress Share, then Icon Color Settings.

## Available color options

Each network has its own color picker:

- Facebook
- Twitter
- Pinterest
- LinkedIn
- Reddit
- WordPress
- Pocket
- E-mail
- WhatsApp

Each field uses the standard WordPress color picker, so you can type a hex value or pick from the palette.

## Save your colors

Click the Save button at the bottom of the tab. The colors are stored together in a single option and applied to the icons on the front end.

## Default colors

On activation the plugin sets a default color for each network, for example a Facebook blue and a WhatsApp green. If you clear a color, that icon falls back to the theme's default color rather than a plugin color.

## How colors are applied

The saved colors are printed as inline CSS in the page head and target each network's icon by its element ID. A color you leave empty is simply not output, so the icon keeps whatever color the active theme gives it.
