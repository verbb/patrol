<?php

// Move to config/patrol.php if you'd like to customize settings
// Note that once moved to config/patrol.php those settings will take precedence over those set via the UI
return [
    'primaryDomain' => '',
    'redirectStatusCode' => 302,
    'sslRoutingEnabled' => false,
    'sslRoutingRestrictedUrls' => [
        '/{cpTrigger}',
    ],
    'maintenanceModeEnabled' => false,
    'maintenanceModePageUrl' => '/offline',
    'maintenanceModeResponseStatusCode' => 403,
    'maintenanceModeAuthorizedIps' => [
        '::1',
        '127.0.0.1',
    ],
    'maintenanceModeAccessTokens' => [
        'gecpqdfbfhvtnwjmfnazdejtvtraguvu',
    ],
    'limitCpAccessTo' => [],
];
