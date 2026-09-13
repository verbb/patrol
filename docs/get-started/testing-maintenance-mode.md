# Testing Maintenance Mode

Patrol can direct visitors to a maintenance page while authorised people continue working on the site. Start on a test installation so you can check both views before enabling the same setup on a live site.

Create a Twig template at `templates/maintenance.twig` with a short message for visitors:

```twig
<!doctype html>
<html lang="en">
<head><title>Maintenance</title></head>
<body>
    <h1>We'll Be Back Soon</h1>
    <p>We're updating the site. Please try again later.</p>
</body>
</html>
```

Check that `/maintenance` is reachable, then create `config/patrol.php` with these overrides:

```php
<?php

return [
    'maintenanceModeEnabled' => true,
    'maintenanceModePageUrl' => '/maintenance',
];
```

Give the people who need access the **Access the site when maintenance mode is enabled** permission. Open the site with an authorised account and confirm that normal pages remain available. Then test from an unauthorised browser and IP address: the visitor should reach the maintenance page. Localhost addresses are authorised by default, so a local private window alone does not test the blocked visitor's experience.

When the work is complete, set `maintenanceModeEnabled` to `false` or remove that override and check that the public site is reachable again. Keep any settings you still need; see [Configuration](docs:get-started/configuration) for IP access, access tokens and response behaviour.

## Redirecting to HTTPS

Maintenance mode and HTTPS routing are separate settings. If you use Patrol to enforce HTTPS, first confirm that the site is already reachable over HTTPS with a valid certificate. Enable `sslRoutingEnabled`, then visit an HTTP URL and inspect the destination. Check a nested path as well as the homepage, particularly if your hosting or proxy also performs redirects.
