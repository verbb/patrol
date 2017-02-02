<?php
namespace selvinortiz\patrol;

use Craft;
use craft\base\Plugin;
use craft\helpers\Template;
use selvinortiz\patrol\assetbundles\plugin\PatrolPluginAssetBundle;
use selvinortiz\patrol\models\SettingsModel;
use selvinortiz\patrol\services\PatrolService;

/**
 * Class Plugin
 *
 * @package selvinortiz\patrol
 * @author  Selvin Ortiz <selvin@selvin.co>
 * @since   3.0
 *
 * @property PatrolService $default The default service instance
 */
class Patrol extends Plugin
{
    const MAINTENANCE_MODE_BYPASS_PERMISSION = 'patrolMaintenanceModeBypass';

    /**
     * @param string $message
     * @param array  $params
     *
     * @return string
     */
    public static function t($message, array $params = [])
    {
        return Craft::t('patrol', $message, $params);
    }

    /**
     * Run watch() once plugins have been loaded to avoid raise conditions
     *
     * @return void
     */
    public function init()
    {
        parent::init();

        Craft::$app->plugins->on('afterLoadPlugins', function ()
        {
            // We can do $this since Craft requires PHP 7 =)
            $this->defaultService->watch();
        });
    }

    /**
     * Returns settings model with custom properties
     *
     * @return SettingsModel
     */
public function createSettingsModel()
{
    $settings = Craft::$app->getConfig()->getConfigSettings('patrol');

    if (! is_array($settings) || empty($settings)) {
        return new SettingsModel();
    }

    return new SettingsModel($settings);
}

    /**
     * Returns rendered settings UI as a twig markup object
     *
     * @todo: Remove getRaw() call once the raw filter gets added by Craft
     *
     * @return \Twig_Markup
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
    public function registerUserPermissions()
    {
        return [
            static::MAINTENANCE_MODE_BYPASS_PERMISSION => [
                'label' => static::t('Access the site when maintenance mode is enabled'),
            ],
        ];
    }
}
