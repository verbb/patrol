# Configuration

You can customise Patrol’s settings using a PHP configuration file. This is optional: each setting has a default, so you only need to include the values you want to change.

To override a setting, create `patrol.php` in your Craft project’s `/config` directory and return an array of setting names and values. For example, the following will use `/maintenance` as the maintenance page:

```php
<?php

return [
    'maintenanceModePageUrl' => '/maintenance',
];
```

All other settings keep their defaults. Add any further settings you want to change to the same array. The options below explain the available settings and their defaults.

## Configuration Options

::: reference
### `primaryDomain`

**Type:** `string` · **Default:** `''`

Primary domain to enforce.
:::


::: reference
### `redirectStatusCode`

**Type:** `int` · **Default:** `302`

Redirect status code to use when redirecting.
:::


::: reference
### `sslRoutingBaseUrl`

**Type:** `string` · **Default:** `''`

Tells Patrol what base URL to use when redirecting to SSL.
:::


::: reference
### `sslRoutingEnabled`

**Type:** `bool` · **Default:** `false`

Tells Patrol to force requests to be made over `https://`.
:::


::: reference
### `sslRoutingRestrictedUrls`

**Type:** `array` · **Default:** `['/']`

Tells Patrol **where** `https://` should be enforced.
:::


::: reference
### `maintenanceModeEnabled`

**Type:** `bool` · **Default:** `false`

Tells Patrol that your site is on maintenance mode and it should start routing traffic differently. Authorized users will see your site while unauthorized users will see either your offline page or an HTTP response with a custom status code.
:::


::: reference
### `maintenanceModeAuthorizedIps`

**Type:** `array` · **Default:** `['::1', '127.0.0.1']`

IP addresses that should be allowed (without being logged in) during maintenance.
:::


::: reference
### `maintenanceModeResponseStatusCode`

**Type:** `int` · **Default:** `403`

Tells Patrol what kind of `HttpException` to throw if you do not set a `$maintenanceModePageUrl`.
:::


::: reference
### `maintenanceModeAccessTokens`

**Type:** `array` · **Default:** `[]`

Access tokens that can be used to automatically add an IP to the allowed list.
:::


### Access Tokens
Access tokens allow you to provide specific access. For example, with the following config set for `maintenanceModeAccessTokens`:

```php
'maintenanceModeAccessTokens' => [
    'ceo-access-token',
    'd0nn3bd8a2iza1ikjxxdo28iicabh7ts',
],
```

You will be able to send someone a link with the access token. When the visit that link, their IP will be added to the allowed list.
- https://my-site.test/?access=ceo-access-token
- https://my-site.test/?access=d0nn3bd8a2iza1ikjxxdo28iicabh7ts

::: reference
### `maintenanceModePageUrl`

**Type:** `string` · **Default:** `'/offline'`

The URL of the page shown during maintenance. Set this to the maintenance page you have created for your site.
:::

## Control Panel
You can also manage configuration settings through the Control Panel by visiting Settings → Patrol.
