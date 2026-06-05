# Role Matrix — BuddyPress Activity Social Share v3.5.5

Legend: C=Create R=Read U=Update D=Delete  -=No access

| Feature | Admin | Editor | Author | Subscriber | Logged-out |
|---|---|---|---|---|---|
| Settings pages (all tabs) | RU | - | - | - | - |
| Enable/disable/sort services (AJAX) | U | - | - | - | - |
| Icon colors | U | - | - | - | - |
| See share button on activity | R | R | R | R | - |
| Use share targets | R | R | R | R | - |
| OG meta on single activity | R | R | R | R | R |

All admin actions gated by `manage_options`. No custom caps. Frontend button gated by `is_user_logged_in()`.
