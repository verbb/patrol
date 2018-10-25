
![Patrol](resources/img/Patrol3.png)

# Patrol 3
Maintenance Mode and SSL Routing for [Craft 3][craft]

## Features 🚀

### SSL Routing 👮‍
- Force HTTPS (server agnostic)
- Force a Primary Domain (naked domain vs www prefixed)
- Define where HTTPS is enforced (if not globally)
- Control the best redirect status code for your use case

#### Maintenance Mode 🚧
- Put your site on maintenance mode
- Define who can access the site while offline
- Reroute guests to an offline page (or custom response)

---

## Install
```bash
composer require selvinortiz/patrol

./craft install/plugin patrol
```

...or you can search for Patrol in the Plugin Store.

---

## Configure
You can configure some stuff through the control panel, but doing so is not recommended. File configs are much more flexible and you can define different configs for different environments.

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

### Config Settings

#### `$primaryDomain`
> Defaults to `null`

Primary domain to enforce

If your site is accessible via multiple domains,
you may want to ensure that it can only be accessed by the primary domain.

**Example**
- domain.frb.io (App URL)
- www.domain.com (Secondary)
- domain.com (Primary)

If a user requests `www.domain.com` or `domain.frb.io`, they will be redirected to `domain.com`

#### `$redirectStatusCode`
> Defaults to `302`

Redirect status code to use when...
1. redirecting to and from SSL restricted URLs
2. redirecting to primary domain, if one is defined.

#### `$sslRoutingBaseUrl`
> Defaults to `Craft::$app->request->hostInfo`

Tells Patrol what base URL to use when redirecting to SSL

#### `$sslRoutingEnabled`
> Defaults to `false`

Tells Patrol to force requests to be made over `https://`

#### `$sslRoutingRestrictedUrls`
> Defaults to `['/']` (everything)

Tells Patrol **where** `https://` should be enforced.

#### `$maintenanceModeEnabled`
> Defaults to `false`

Tells Patrol that your site is on maintenance mode and it should start routing traffic differently.

Authorized users will see your site while unauthorized users will see either your offline page or an HTTP response with a custom status code.

#### `$maintenanceModeAuthorizedIps`
> Defaults to `['::1', '127.0.0.1']`

IP addresses that should be allowed (without being logged in) during maintenance.

#### `$maintenanceModeResponseStatusCode`
> Defaults to `410`

Tells Patrol what kind of `HttpException` to throw if you do not set a `$maintenanceModePageUrl`.

#### `$maintenanceModeAccessTokens`
> Defaults to `[]`

Access tokens that can be used to automatically add an IP to the allowed list.

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

---

### Help & Feedback
If you have questions, comments, or suggestions, feel free to reach out to me on twitter [@selvinortiz](https://twitter.com/selvinortiz)

## License
**Patrol** for [Craft CMS][craft] is open source software

[MIT License][mit]

![osi]

[me]:https://selvinortiz.com "Selvin Ortiz"
[mit]:http://opensource.org/licenses/MIT "MIT License"
[osi]:resources/img/osilogo.png "Open Source Initiative"
[love]:resources/img/love.png "Love"
[craft]:http://craftcms.com "Craft 3"
