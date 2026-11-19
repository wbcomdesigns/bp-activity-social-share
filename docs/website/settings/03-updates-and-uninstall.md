# Updates, uninstall and your data

## Where the settings are stored

Activity Share for BuddyPress keeps all of its settings in one WordPress option, `bpas_settings`. It adds no database tables and does not store anything about the people who share.

| Setting | Default |
|---|---|
| Networks | Facebook, X, LinkedIn, WhatsApp, Email, Copy link |
| Visitors can share | On |
| Share member profiles | On |
| Share public groups | On |
| Button label | Empty (shows "Share") |
| Position on activity | After the other buttons |
| Icon colours | Each network's own colour |

## Updating

Updates run automatically the first time the site loads after you update the plugin. See [Installation](../getting-started/02-installation.md#upgrading-from-an-earlier-version) for what moves from versions before 3.6.0. Update Activity Share for BuddyPress and Activity Share Pro together.

## Deleting the plugin

Delete the plugin from **Plugins** to run its clean-up. It removes:

- the `bpas_settings` option and its stored version number,
- every setting left over from versions before 3.6.0,
- the old cached data and scheduled tasks of those versions.

On a multisite network the clean-up runs for every site. Deactivating the plugin removes nothing.

If Activity Share Pro is still installed, the old Pro settings from before 3.6.0 are left for Pro to remove. Activity Share Pro has its own clean-up when you delete it.
