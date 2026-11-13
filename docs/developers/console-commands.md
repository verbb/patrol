# Console Commands

Patrol includes console commands you can run from your Craft project’s directory.

## Access Tokens

::: reference
### `patrol/access-token/generate`

Prints a new random 32-character token for maintenance mode access. Add it to [`maintenanceModeAccessTokens`](../get-started/configuration.md#maintenancemodeaccesstokens) and send the link to the person who needs access. The command only prints the token, so nothing changes until you add it to your configuration. This is also the default action, so `php craft patrol/access-token` runs it. [Access Tokens](../get-started/configuration.md#access-tokens) explains how the link works.

```shell
php craft patrol/access-token/generate
```
:::
