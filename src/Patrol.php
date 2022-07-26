<?php
namespace verbb\patrol;

use verbb\patrol\base\PluginTrait;
use verbb\patrol\models\Settings;
use verbb\patrol\variables\PatrolVariable;

use Craft;
use craft\base\Plugin;
use craft\events\RegisterUserPermissionsEvent;
use craft\events\RegisterUrlRulesEvent;
use craft\helpers\UrlHelper;
use craft\services\Plugins;
use craft\services\UserPermissions;
use craft\web\UrlManager;
use craft\web\twig\variables\CraftVariable;

use yii\base\Event;

class Patrol extends Plugin
{
    // Properties
    // =========================================================================

    public string $schemaVersion = '3.0.0';
    public bool $hasCpSettings = true;


    // Traits
    // =========================================================================

    use PluginTrait;


    // Public Methods
    // =========================================================================

    public function init(): void
    {
        parent::init();

        self::$plugin = $this;

        $this->_setPluginComponents();
        $this->_setLogging();
        $this->_registerCpRoutes();
        $this->_registerVariables();
        $this->_registerCraftEventListeners();
        $this->_registerPermissions();
    }

    public function getPluginName(): string
    {
        return Craft::t('patrol', 'Patrol');
    }

    public function getSettingsResponse(): mixed
    {
        return Craft::$app->getResponse()->redirect(UrlHelper::cpUrl('patrol/settings'));
    }


    // Protected Methods
    // =========================================================================

    protected function createSettingsModel(): Settings
    {
        return new Settings();
    }


    // Private Methods
    // =========================================================================

    private function _registerCpRoutes(): void
    {
        Event::on(UrlManager::class, UrlManager::EVENT_REGISTER_CP_URL_RULES, function(RegisterUrlRulesEvent $event) {
            $event->rules = array_merge($event->rules, [
                'patrol/settings' => 'patrol/base/settings',
            ]);
        });
    }

    private function _registerVariables(): void
    {
        Event::on(CraftVariable::class, CraftVariable::EVENT_INIT, function(Event $event) {
            $event->sender->set('patrol', PatrolVariable::class);
        });
    }

    private function _registerCraftEventListeners(): void
    {
        Event::on(Plugins::class, Plugins::EVENT_AFTER_LOAD_PLUGINS, function(Event $event) {
            $request = Craft::$app->getRequest();

            if ($request->getIsConsoleRequest() || ($request->getIsLivePreview() || $request->getIsPreview())) {
                return;
            }

            Patrol::$plugin->getService()->allow();
            Patrol::$plugin->getService()->watch();
        });
    }

    private function _registerPermissions(): void
    {
        Event::on(UserPermissions::class, UserPermissions::EVENT_REGISTER_PERMISSIONS, function(RegisterUserPermissionsEvent $event) {
            $event->permissions[Craft::t('patrol', 'Patrol')] = [
                'patrolMaintenanceModeBypass' => ['label' => Craft::t('patrol', 'Access the site when maintenance mode is enabled')],
            ];
        });
    }
}
