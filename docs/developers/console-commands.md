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

## Authorized IPs

These commands manage the IP addresses that visitors authorized with an [access token](../get-started/configuration.md#access-tokens). They don't affect the addresses in `maintenanceModeAuthorizedIps`. Because the addresses are stored in the database, the commands work on environments where `allowAdminChanges` is `false`.

::: reference
### `patrol/authorized-ips/list`

Lists each IP address authorized through an access token, oldest first. This is also the default action, so `php craft patrol/authorized-ips` runs it.

```shell
php craft patrol/authorized-ips/list
```
:::

::: reference
### `patrol/authorized-ips/remove`

Removes a single IP address, so that visitor sees the maintenance page again until they use a valid access token.

```shell
php craft patrol/authorized-ips/remove 203.0.113.10
```
:::

::: reference
### `patrol/authorized-ips/clear`

Removes every IP address authorized through an access token. Run it after replacing a token that was shared too widely, or once maintenance is over. Patrol asks for confirmation first, unless you pass `--interactive=0`.

```shell
php craft patrol/authorized-ips/clear
```
:::
