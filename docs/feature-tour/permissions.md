# Permissions

Patrol has one permission, for the people who need to keep using the front end of the site while maintenance mode is on, such as editors checking content or a client reviewing changes before launch. Set it in **Settings → Users → User Groups**, or on an individual user's **Permissions** tab. It's listed under the **Patrol** heading.

| Permission | What It Allows |
| --- | --- |
| Access the site when maintenance mode is enabled | Browse the front end normally while `maintenanceModeEnabled` is on, instead of being sent to the maintenance page or shown the maintenance error. |

The permission only counts while someone is logged in, so they should log in through the control panel first, which maintenance mode doesn't affect, and then visit the site in the same browser. Admins always have access. People who aren't logged in can still get through from an address in `maintenanceModeAuthorizedIps`, or by using an access token from `maintenanceModeAccessTokens` to add their address to that list, which suits a reviewer who doesn't have a Craft account. See [Configuration](docs:get-started/configuration) for both settings, and [Testing Maintenance Mode](docs:get-started/testing-maintenance-mode) for checking what each kind of visitor sees.
