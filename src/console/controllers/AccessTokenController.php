<?php
namespace verbb\patrol\console\controllers;

use craft\console\Controller;
use craft\helpers\Console;
use craft\helpers\StringHelper;

use yii\console\ExitCode;

/**
 * Manages Patrol.
 */
class AccessTokenController extends Controller
{
    // Properties
    // =========================================================================

    public $defaultAction = 'generate';


    // Public Methods
    // =========================================================================

    /**
     * Generate a new access token for dynamic IP authorization.
     */
    public function actionGenerate(): int
    {
        $access = mb_strtolower(StringHelper::randomString(32));

        $this->line($access, Console::FG_GREEN);

        return ExitCode::OK;
    }


    // Private Methods
    // =========================================================================

    private function line($text = '', $color = null): void
    {
        $this->stdout($text . PHP_EOL, $color);
    }
}
