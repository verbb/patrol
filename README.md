
![Patrol](resources/img/Patrol3.png)

# Patrol 3
> **Maintenance Mode** and **SSL Routing**

## Installation
1. Download via composer: `composer require selvinortiz/patrol`
2. Install from the **Control Panel**: `Settings > Plugins`

### Features
- Made with ![love] by [Selvin Ortiz][me]
- Allows you to _force SSL_ on specific areas of your site or globally
- Allows you to put your site on _maintenance mode_ and route traffic to your _offline page_
- Allows you to define who can access your website during maintenance
- Allows you to enforce a primary domain (`primaryDomain environment config`)
- Allows you to limit control panel access (`limitCpAccessTo environment config`)

> You can let users access your website during maintenance by:
- Making them **admins**
- Authorizing their **IP address**
- Giving them this permission: `Patrol > Access the site when maintenance is on`

> If you want to block all users, (including admins) during maintenance:
- Add your email or username to `limitCpAccessTo` in your _config file_ and login with that account

## @Todo
- Fix user permissions for permission based access
- Add other validators for remaining settings
- Review the way localized settings are being populated
- Review `limitCpAccessTo` functionality
- Review URL matching/parsing and redirects

### Environment Configs
> You can configure Patrol via a dedicated config file.

```php
// config/patrol.php
return [
    'settings' => [
        'primaryDomain'   => '*',
        'sslRoutingEnabled' => true,
        'sslRoutingRestrictedUrls' => [
            '/{cpTrigger}',
            '/members',
        ],
        'maintenanceModeEnabled' => false,
        'maintenanceModePageUrl'  => '/down',
        'maintenanceModeAuthorizedIps'   => [
            '127.0.0.1',
        ],
        'limitCpAccessTo' => [],
    ]
];
```

>If you want to configure it based on the environment, you can do something like this.

```
// config/patrol.php
return [
	'*' => [
		'settings' => [
			'primaryDomain'   => '*',
			'sslRoutingEnabled' => true,
			'sslRoutingRestrictedUrls' => [
				'/{cpTrigger}',
				'/members',
			],
			'maintenanceModeEnabled' => false,
			'maintenanceModePageUrl'  => '/down',
			'maintenanceModeAuthorizedIps'   => [
				'127.0.0.1',
			],
			'limitCpAccessTo' => [],
		]
    ],
    '.dev' => [
    	'settings' => [
    		'sslRoutingEnabled' => false
    	]
    ]
];
```


### Notes
> Patrol will throw an `HttpException(403)` for unauthorized users during maintenance if you do not have an _offline page_ set up.

> To force SSL everywhere (recommended practice), you can set `/` as the restricted area. If you only want to force SSL on the control panel, you could use `/admin` or `/{cpTrigger}`, the latter is recommended.

### Help & Feedback
If you have questions, comments, or suggestions, feel free to reach out to me on twitter [@selvinortiz](https://twitter.com/selvinortiz)

## License
**Patrol** for [Craft 3][craft3] is open source software

[MIT License][mit]

![osi]

[me]:https://selvinortiz.com "Selvin Ortiz"
[mit]:http://opensource.org/licenses/MIT "MIT License"
[osi]:resources/img/osilogo.png "Open Source Initiative"
[love]:resources/img/love.png "Love"
[craft3]:http://buildwithcraft.com/3 "Craft 3"
