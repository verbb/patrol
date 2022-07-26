<?php
namespace verbb\patrol\controllers;

use craft\helpers\StringHelper;

use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\Console;

class AccessTokenController extends controller
{
    // Properties
    // =========================================================================

    public $defaultAction = 'generate';


    // Public Methods
    // =========================================================================

    /**
     * Generate a new access token for dynamic IP authorization
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
