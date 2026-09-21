# Installation

## Before you begin

Install and activate BuddyPress first. This plugin requires BuddyPress and will not run without it. On activation, if BuddyPress is not active, the plugin deactivates itself and shows a notice explaining why.

## Install from the WordPress dashboard

1. Go to Plugins, then Add New.
2. Search for "BuddyPress Activity Social Share".
3. Click Install Now, then Activate.

## Install by upload

1. Download the plugin zip file.
2. Go to Plugins, then Add New, then Upload Plugin.
3. Choose the zip file and click Install Now.
4. Click Activate.

## Install manually

1. Extract the plugin zip file.
2. Upload the `bp-activity-social-share` directory to `/wp-content/plugins/`.
3. Go to Plugins in the dashboard and activate BuddyPress Activity Social Share.

## After activation

When the plugin activates with BuddyPress active, it does two things automatically:

- Sets a default list of enabled sharing networks so the "Share" button works right away.
- Sets default icon colors for each network.

You are then redirected to the settings screen at WB Plugins, then BuddyPress Share (admin URL `admin.php?page=buddypress-share`). A Settings link is also added next to the plugin on the Plugins list.

## Multisite note

The plugin stores its options as network options (`get_site_option` / `update_site_option`). It must be activated on the same blog and share the same network configuration as BuddyPress. If it does not, the plugin shows an admin notice and does not load.
