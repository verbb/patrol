<?php
namespace craft\plugins\patrol;

use Craft;
use craft\app\base\Plugin;
use craft\app\helpers\TemplateHelper;
use craft\plugins\patrol\models\Settings;
use craft\plugins\patrol\services\DefaultService;

/**
 * Class Plugin
 *
 * @package craft\plugins\patrol
 * @author  Selvin Ortiz <selvin@selvin.co>
 * @since   3.0
 *
 * @property DefaultService $default The default service instance
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

		Craft::$app->plugins->on('afterLoadPlugins', function()
		{
			// We can do $this since Craft requires PHP 5.4=)
			$this->default->watch();
		});
	}

	/**
	 * Returns settings model with custom properties
	 *
	 * @return Settings
	 */
	public function createSettingsModel()
	{
		return new Settings();
	}

	/**
	 * Returns rendered settings UI as a twig markup object
	 *
	 * @todo: Remove getRaw() call once the raw filter gets added by Craft
	 *
	 * @return \Twig_Markup
	 */
	public function getSettingsHtml()
	{
		/** @var Settings $settings */
		$settings  = $this->getSettings();
		$variables = [
			'plugin'       => $this,
			'settings'     => $settings,
			'settingsJson' => $settings->getJsonObject(),
		];

		Craft::$app->view->registerJsResource('patrol/js/vue.js');
		Craft::$app->view->registerJsResource('patrol/js/patrol.js');
		Craft::$app->view->registerCssResource('patrol/css/patrol.css');

		$html = Craft::$app->view->renderTemplate('patrol/_settings', $variables);

		return TemplateHelper::getRaw($html);
	}

	/**
	 * @return array
	 */
	public function registerUserPermissions()
	{
		return [
			static::MAINTENANCE_MODE_BYPASS_PERMISSION => [
				'label' => static::t('Access the site when maintenance mode is enabled')
			],
		];
	}
}
