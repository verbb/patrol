# Patrol plugin for Craft CMS
Easy Maintenance Mode and Smart HTTPS Routing.

## Features

### HTTPS Routing 👮‍
- Force HTTPS (server agnostic)
- Force a Primary Domain (naked domain vs www prefixed)
- Define where HTTPS is enforced (if not globally)
- Control the best redirect status code for your use case

### Maintenance Mode 🚧
- Put your site on maintenance mode
- Define who can access the site while offline
- Reroute guests to an offline page (or custom response)

## Installation
You can install Patrol via the plugin store, or through Composer.

### Craft Plugin Store
To install **Patrol**, navigate to the _Plugin Store_ section of your Craft control panel, search for `Patrol`, and click the _Try_ button.

### Composer
You can also add the package to your project using Composer.

1. Open your terminal and go to your Craft project:

        cd /path/to/project

2. Then tell Composer to load the plugin:
    
        composer require verbb/patrol

3. In the Control Panel, go to Settings → Plugins and click the “Install” button for Patrol.

## Configuring Patrol
The plugin is configured in the `config/` directory in a file you create called `patrol.php`. What follows is an example of what it might contain.

```php
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
    ]
];
```

## Configuration Options
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
If you define the following access tokens:

```php
$maintenanceModeAccessTokens =  [
    'ceo-access-token',
    'd0nn3bd8a2iza1ikjxxdo28iicabh7ts',
];
```

You will be able to send someone a link with the access token. When the visit that link, their IP will be added to the allowed list.

- https://domain.com/?access=ceo-access-token
- https://domain.com/?access=d0nn3bd8a2iza1ikjxxdo28iicabh7ts

You can use any string as an access token but avoid using spaces.

> If you are planning on using access tokens, do not include `$maintenanceModeAuthorizedIps` as a file config setting.

## Credits
Originally created by [Selvin Ortiz](https://github.com/selvindev).

## Show your Support
Patrol is licensed under the MIT license, meaning it will always be free and open source – we love free stuff! If you'd like to show your support to the plugin regardless, [Sponsor](https://github.com/sponsors/verbb) development.

<h2></h2>

<a href="https://verbb.io" target="_blank">
    <img width="100" src="https://verbb.io/assets/img/verbb-pill.svg">
</a>





