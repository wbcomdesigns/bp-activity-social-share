# Enable Social Networks

All network selection happens on the General Settings tab at WB Plugins, then BuddyPress Share, then General Settings.

## The Enable and Disable lists

The tab shows two lists under the heading "Enable Sharing Sites":

- Disable: networks that are currently turned off.
- Enable: networks that are currently turned on and will appear on the front end.

The networks you can move between the lists are:

- Facebook
- X (Twitter)
- Pinterest
- LinkedIn
- Reddit
- WordPress
- Pocket
- E-mail
- WhatsApp

## Turn a network on or off

Drag a network from the Disable list into the Enable list to turn it on. Drag it back to the Disable list to turn it off. Each drag is saved immediately through an admin AJAX request, so there is no separate save step for enabling or disabling a network.

## Reorder the enabled networks

Within the Enable list, drag networks up or down to change their order. The order is saved and is the order the icons appear behind the "Share" button on the front end.

## Save changes

The General Settings tab also has a "Save Changes" button for the settings form. The enable, disable, and reorder actions are saved as you drag; the Save button commits the form's other options.

## Open as popup window

Below the network lists is an Extra Options section with a single checkbox, "Open as popup window":

- Checked: clicking a network opens a centered popup window.
- Unchecked: clicking a network opens a new browser tab instead.

The default behavior is popup. WhatsApp and E-mail always open directly and are never placed in a popup.

## Default enabled networks

On first activation the plugin enables a starter set of networks automatically so the button works out of the box. You can change this selection at any time using the lists above.
