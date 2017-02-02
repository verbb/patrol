<?php

// Move to config/patrol.php if you'd like to customize settings
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
