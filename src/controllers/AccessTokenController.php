<?php
namespace selvinortiz\patrol\controllers;

use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\Console;

use Craft;
use craft\helpers\UrlHelper;
use craft\helpers\StringHelper;

class AccessTokenController extends controller
{
    public $defaultAction = 'generate';

    /**
     * Generate a new access token for dynamic IP authorization
     */
    public function actionGenerate()
    {
        $access = mb_strtolower(StringHelper::randomString(32));

        $this->line($access, Console::FG_GREEN);

        return ExitCode::OK;
    }

    private function line($text = '', $color = null)
    {
        $this->stdout($text.PHP_EOL, $color);
    }
}
