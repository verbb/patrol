<?php
namespace verbb\patrol\console\controllers;

use verbb\patrol\Patrol;

use craft\console\Controller;
use craft\helpers\Console;

use yii\console\ExitCode;

/**
 * Manages IP addresses authorized through maintenance mode access tokens.
 */
class AuthorizedIpsController extends Controller
{
    // Properties
    // =========================================================================

    public $defaultAction = 'list';


    // Public Methods
    // =========================================================================

    /**
     * Lists the IP addresses authorized through an access token.
     */
    public function actionList(): int
    {
        $ips = Patrol::$plugin->getService()->getTokenAuthorizedIps();

        if (!$ips) {
            $this->line('No IP addresses have been authorized through an access token.');

            return ExitCode::OK;
        }

        foreach ($ips as $ip) {
            $this->line($ip);
        }

        return ExitCode::OK;
    }

    /**
     * Removes an IP address that was authorized through an access token.
     */
    public function actionRemove(string $ip): int
    {
        if (!Patrol::$plugin->getService()->removeTokenAuthorizedIp(trim($ip))) {
            $this->line("“{$ip}” isn’t in the list of IP addresses authorized through an access token.", Console::FG_YELLOW);

            return ExitCode::UNSPECIFIED_ERROR;
        }

        $this->line("Removed “{$ip}”.", Console::FG_GREEN);

        return ExitCode::OK;
    }

    /**
     * Removes every IP address that was authorized through an access token.
     */
    public function actionClear(): int
    {
        if ($this->interactive && !$this->confirm('Remove every IP address authorized through an access token?')) {
            return ExitCode::OK;
        }

        $count = Patrol::$plugin->getService()->clearTokenAuthorizedIps();

        $this->line("Removed {$count} IP " . ($count === 1 ? 'address' : 'addresses') . '.', Console::FG_GREEN);

        return ExitCode::OK;
    }


    // Private Methods
    // =========================================================================

    private function line($text = '', $color = null): void
    {
        $this->stdout($text . PHP_EOL, $color);
    }
}
