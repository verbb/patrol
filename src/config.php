<?php

// Move to config/patrol.php if you'd like to customize settings
// Note that once moved to config/patrol.php those settings will take precedence over those set via the UI
return [
    'primaryDomain'   => '*',
    'sslRoutingEnabled' => false,
    'sslRoutingRestrictedUrls' => [
        '/{cpTrigger}',
    ],
    'maintenanceModeEnabled' => false,
    'maintenanceModePageUrl'  => '/offline',
    'maintenanceModeAuthorizedIps'   => [
        '::1',
        '127.0.0.1',
    ],
    'limitCpAccessTo' => [],
];
