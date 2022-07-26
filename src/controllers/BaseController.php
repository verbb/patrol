<?php
namespace verbb\patrol\controllers;

use verbb\patrol\Patrol;

use craft\web\Controller;

use yii\web\Response;

class BaseController extends Controller
{
    // Public Methods
    // =========================================================================

    public function actionSettings(): Response
    {
        $settings = Patrol::$plugin->getSettings();

        return $this->renderTemplate('patrol/settings', [
            'settings' => $settings,
        ]);
    }

}