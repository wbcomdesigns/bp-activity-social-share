# Shortcode and block

> Both require **Activity Share Pro** - they place the share menu on ordinary posts and pages, not just BuddyPress activity.

## Shortcode

```
[bpas_share]
[bpas_share id="123"]
```

Without an `id`, `[bpas_share]` shares the current post. With `id`, it shares that specific post regardless of where the shortcode is placed.

## Block

Add the **Share buttons** block (`bpas/share`) from the block inserter on any post or page. It is server-rendered, so it always reflects the current settings and never needs re-saving after a settings change.

## Most shared block

The **Most shared** block (`bpas/trending`) lists the site's most-shared public content. It has two attributes, editable in the block sidebar:

| Attribute | Values | Default |
|---|---|---|
| `days` | 7 or 30 | 7 |
| `limit` | 1-20 | 5 |

A matching legacy widget ("Most shared") is also available under **Appearance > Widgets** for classic sidebars.

## Where buttons appear automatically

Content sharing on a post type is also controlled per type on the [Content types](../pro/02-content-types.md) tab - above, below, or as a floating button - without needing the shortcode or block at all.
