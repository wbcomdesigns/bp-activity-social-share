# Frequently Asked Questions

## Do I need any other plugin for this to work?

Yes. This plugin requires BuddyPress to be installed and active. Without BuddyPress the plugin deactivates itself and shows a notice.

## Which social networks are supported?

Facebook, X (Twitter), Pinterest, Reddit, WordPress (Press This), Pocket, LinkedIn, WhatsApp, and E-mail. Each can be enabled or disabled from the General Settings tab.

## Who can see the Share button?

Logged-in members only. The button appears on activity entries when at least one sharing network is enabled.

## How do I add a social network to the front end?

On the General Settings tab, drag the network from the Disable list into the Enable list. The change saves as you drag.

## How do I remove a social network from the front end?

On the General Settings tab, drag the network from the Enable list back into the Disable list.

## Can I change the order the icons appear in?

Yes. In the Enable list on the General Settings tab, drag the networks into the order you want. That order is used on the front end.

## Do share links open in a popup or a new tab?

That is controlled by the "Open as popup window" checkbox on the General Settings tab. Checked opens a popup; unchecked opens a new browser tab. WhatsApp and E-mail always open directly.

## Can I change the icon colors?

Yes. Use the Icon Color Settings tab to set a color for each network.

## Can I change the "Share" button text?

Yes, with code. Use the `bpas_share_button_text_override` filter described in the Developer Guide.

## Is it compatible with the BuddyBoss Platform?

Yes. The plugin uses BuddyBoss / BuddyPress media (`BP_Media`) when building the Open Graph image for a shared activity.

## Is it compatible with rtMedia and Youzer / Youzify?

The plugin reads the first image in the activity content for the Open Graph image, and it also reads Youzer and Youzify media attachments where present.

## Does it show how many times an activity was shared?

No. The plugin does not track or display a share count.

## Can I run this together with the Pro version?

No. If BuddyPress Activity Social Share Pro is active, the free version deactivates itself. Use one or the other.

## Where do I get support?

Visit wbcomdesigns.com for any questions about the plugin.
