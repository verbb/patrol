# Configuration
Create a `patrol.php` file under your `/config` directory with the following options available to you. You can also use multi-environment options to change these per environment.

The below shows the defaults already used by Patrol, so you don't need to add these options unless you want to modify the values.

```php
<?php

return [
    '*' => [
        'primaryDomain' => null,
        'redirectStatusCode' => 302,

        'sslRoutingBaseUrl' => "https://mysecuredwebsite.com",
        'sslRoutingEnabled' => true,
        'sslRoutingRestrictedUrls' => ['/'],

        'maintenanceModeEnabled' => false,
        'maintenanceModePageUrl' => '/offline',
        'maintenanceModeAuthorizedIps' => ['::1', '127.0.0.1'],
        'maintenanceModeResponseStatusCode' => 410,
    ],
    'dev' => [
        'sslRoutingEnabled' => false,
    ]
    'staging' => [
        'maintenanceModePageUrl' => null,
        'maintenanceModeResponseStatusCode' => 410,
    ],
    'production' => [
        'redirectStatusCode' => 301,
        'maintenanceModeResponseStatusCode' => 503,
    ],
];
```

## Configuration options
- `primaryDomain` - Primary domain to enforce.
- `redirectStatusCode` - Redirect status code to use when redirecting.
- `sslRoutingBaseUrl` - Tells Patrol what base URL to use when redirecting to SSL.
- `sslRoutingEnabled` - Tells Patrol to force requests to be made over `https://`.
- `sslRoutingRestrictedUrls` - Tells Patrol **where** `https://` should be enforced.
- `maintenanceModeEnabled` - Tells Patrol that your site is on maintenance mode and it should start routing traffic differently. Authorized users will see your site while unauthorized users will see either your offline page or an HTTP response with a custom status code.
- `maintenanceModeAuthorizedIps` - IP addresses that should be allowed (without being logged in) during maintenance.
- `maintenanceModeResponseStatusCode` - Tells Patrol what kind of `HttpException` to throw if you do not set a `$maintenanceModePageUrl`.
- `maintenanceModeAccessTokens` - Access tokens that can be used to automatically add an IP to the allowed list.

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

## Control Panel
You can also manage configuration settings through the Control Panel by visiting Settings → Patrol.
