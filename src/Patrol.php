<?php
namespace selvinortiz\patrol;

use yii\base\Event;

use Craft;
use craft\base\Plugin;
use craft\helpers\Template;

use craft\services\UserPermissions;
use craft\events\RegisterUserPermissionsEvent;
use craft\web\Application;
use selvinortiz\patrol\models\SettingsModel;
use selvinortiz\patrol\services\PatrolService;
use selvinortiz\patrol\assetbundles\plugin\PatrolPluginAssetBundle;

/**
 * Class Plugin
 *
 * @package selvinortiz\patrol
 * @author  Selvin Ortiz <selvin@selvin.co>
 * @since   3.0
 *
 * @property PatrolService $defaultService The default service instance
 */
class Patrol extends Plugin
{
    const MAINTENANCE_MODE_BYPASS_PERMISSION = 'patrolMaintenanceModeBypass';

    public $hasCpSection = true;

    public $controllerNamespace = 'selvinortiz\\patrol\\controllers';

    /**
     * @throws \yii\base\ErrorException
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\web\HttpException
     */
    public function init()
    {
        parent::init();

        Craft::$app->on(Application::EVENT_INIT, function()
        {
            if (!Craft::$app->request->isConsoleRequest && !Craft::$app->request->isLivePreview)
            {
                $this->defaultService->allow();
                $this->defaultService->watch();
            }
        });

        Event::on(
            UserPermissions::class,
            UserPermissions::EVENT_REGISTER_PERMISSIONS,
            function(RegisterUserPermissionsEvent $event)
            {
                $section = \Craft::t('patrol', 'Patrol');

                $event->permissions[$section] = $this->getPermissionsToRegister();
            }
        );
    }

    /**
     * Returns settings model with custom properties
     *
     * @return SettingsModel
     */
    public function createSettingsModel()
    {
        return new SettingsModel();
    }

    /**
     * Returns rendered settings UI as a twig markup object
     *
     * @return \Twig_Markup
     * @throws \Twig_Error_Loader
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function settingsHtml()
    {
        Craft::$app->view->registerAssetBundle(PatrolPluginAssetBundle::class);

        /**
         * @var SettingsModel $settings
         */
        $settings  = $this->getSettings();
        $variables = [
            'plugin'       => $this,
            'settings'     => $settings,
            'settingsJson' => $settings->getJsonObject(),
        ];

        $html = Craft::$app->view->renderTemplate('patrol/_settings', $variables);

        return Template::raw($html);
    }

    /**
     * @return array
     */
    protected function getPermissionsToRegister()
    {
        return [
            static::MAINTENANCE_MODE_BYPASS_PERMISSION => [
                'label' => Craft::t('patrol', 'Access the site when maintenance mode is enabled'),
            ],
        ];
    }
}
