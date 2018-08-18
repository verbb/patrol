<?php
namespace selvinortiz\patrol\services;

use yii\web\HttpException;
use yii\base\ErrorException;

use Craft;
use craft\base\Component;
use craft\helpers\UrlHelper;

use selvinortiz\patrol\Patrol;
use selvinortiz\patrol\models\SettingsModel;

/**
 * Class PatrolService
 *
 * @package selvinortiz\patrol\services
 */
class PatrolService extends Component
{
    /**
     * @var SettingsModel
     */
    protected $settings;

    /**
     * Key/value pairs used when parsing restricted areas like {cpTrigger}
     *
     * @var array
     */
    protected $dynamicParams;

    /**
     * @throws ErrorException
     * @throws HttpException
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function watch()
    {
        $this->settings = Patrol::getInstance()->getSettings();

        $this->handleRouting();
        $this->handleSslRouting();
        $this->handleMaintenanceMode();
    }

    /**
     * @throws ErrorException
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function handleRouting()
    {
        if (!is_string($this->settings->primaryDomain) || empty($this->settings->primaryDomain))
        {
            return;
        }

        $primaryDomain   = mb_strtolower(trim($this->settings->primaryDomain));
        $requestedDomain = mb_strtolower(trim(Craft::$app->request->getHostName()));

        if ($primaryDomain === '*')
        {
            return;
        }

        if ($primaryDomain === $requestedDomain)
        {
            return;
        }

        $this->settings->sslRoutingBaseUrl = $primaryDomain;

        $this->handleSslRouting();

        $http = Craft::$app->request->getIsSecureConnection() ? 'https://' : 'http://';

        $url = $http.$primaryDomain.Craft::$app->request->getUrl();

        Craft::$app->response->redirect($url, $this->settings->sslRoutingRedirectStatusCode);
    }

    /**
     * Forces SSL based on restricted URLs
     * The environment settings take priority over those defined in the control panel
     *
     * @return bool
     *
     * @throws ErrorException
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function handleSslRouting()
    {
        if ($this->settings->sslRoutingEnabled && !Craft::$app->getRequest()->getIsConsoleRequest())
        {
            $requestedUrl   = Craft::$app->request->getUrl();
            $restrictedUrls = $this->settings->sslRoutingRestrictedUrls;

            if (!Craft::$app->request->isSecureConnection)
            {
                foreach ($restrictedUrls as $restrictedUrl)
                {
                    // Parse dynamic variables like /{cpTrigger}
                    if (stripos($restrictedUrl, '{') !== false)
                    {
                        $restrictedUrl = Craft::$app->view->renderObjectTemplate(
                            $restrictedUrl,
                            $this->getDynamicParams()
                        );
                    }

                    $restrictedUrl = '/'.ltrim($restrictedUrl, '/');

                    if (stripos($requestedUrl, $restrictedUrl) === 0)
                    {
                        $this->forceSsl();
                    }
                }

                return true;
            }
        }

        return false;
    }

    /**
     * Returns a list of dynamic parameters and their values that can be used in restricted area settings
     *
     * @return array
     *
     * @throws \yii\base\Exception
     */
    protected function getDynamicParams()
    {
        if (is_null($this->dynamicParams))
        {
            $this->dynamicParams = [
                'siteUrl'       => UrlHelper::siteUrl(),
                'cpTrigger'     => Craft::$app->config->general->cpTrigger,
                'actionTrigger' => Craft::$app->config->general->actionTrigger,
            ];
        }

        return $this->dynamicParams;
    }

    /**
     * Redirects to the HTTPS version of the requested URL
     *
     * @throws ErrorException
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     */
    protected function forceSsl()
    {
        $baseUrl = Craft::$app->view->renderObjectTemplate(
            $this->settings->sslRoutingBaseUrl,
            $this->getDynamicParams()
        );

        $baseUrl = trim($baseUrl);

        if (empty($baseUrl) || $baseUrl == '/')
        {
            $baseUrl = Craft::$app->request->serverName;
        }

        $url = sprintf('%s%s', $baseUrl, ltrim(Craft::$app->request->getUrl(), '/')); // http://domain.com/page?query=something
        $url = str_replace('http:', 'https:', $url);                                  // https://domain.com/page?query=something

        if (!filter_var($url, FILTER_VALIDATE_URL))
        {
            throw new ErrorException(
                Patrol::t('{url} is not a valid URL', ['url' => $url])
            );
        }

        Craft::$app->response->redirect(
            $url,
            $this->settings->sslRoutingRedirectStatusCode
        );
    }

    /**
     * Restricts accessed based on authorizedIps
     *
     * @return bool
     *
     * @throws HttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function handleMaintenanceMode()
    {
        // Authorize logged in admins on the fly
        if ($this->doesCurrentUserHaveAccess())
        {
            return true;
        }

        if (Craft::$app->request->isSiteRequest && $this->settings->maintenanceModeEnabled)
        {
            $requestingIp   = $this->getRequestingIp();
            $authorizedIps  = $this->settings->maintenanceModeAuthorizedIps;
            $maintenanceUrl = $this->settings->maintenanceModePageUrl;

            if ($maintenanceUrl == Craft::$app->request->getUrl())
            {
                return true;
            }

            if (empty($authorizedIps))
            {
                $this->forceRedirect($maintenanceUrl);
            }

            if (is_array($authorizedIps) && count($authorizedIps))
            {
                if (in_array($requestingIp, $authorizedIps))
                {
                    return true;
                }

                foreach ($authorizedIps as $authorizedIp)
                {
                    $authorizedIp = str_replace('*', '', $authorizedIp);

                    if (stripos($requestingIp, $authorizedIp) === 0)
                    {
                        return true;
                    }
                }

                $this->forceRedirect($maintenanceUrl);
            }
        }
    }

    /**
     * Returns whether or not the current user has access during maintenance mode
     */
    protected function doesCurrentUserHaveAccess()
    {
        // Admins have access by default
        if (Craft::$app->user->getIsAdmin())
        {
            return true;
        }

        // User has the right permission
        if (Craft::$app->user->checkPermission(Patrol::MAINTENANCE_MODE_BYPASS_PERMISSION))
        {
            return true;
        }

        return false;
    }

    /**
     * Ensures that we get the right IP address even if behind CloudFlare
     *
     * @return string
     */
    public function getRequestingIp()
    {
        return isset($_SERVER['HTTP_CF_CONNECTING_IP']) ? $_SERVER['HTTP_CF_CONNECTING_IP'] : $_SERVER['REMOTE_ADDR'];
    }

    /**
     * @param string $redirectTo
     *
     * @throws HttpException
     */
    protected function forceRedirect($redirectTo = '')
    {
        if (empty($redirectTo))
        {
            $this->runDefaultBehavior();
        }

        Craft::$app->response->redirect($redirectTo, $this->settings->maintenanceModelPageStatusCode);
    }

    /**
     * @throws HttpException
     */
    protected function runDefaultBehavior()
    {
        throw new HttpException($this->settings->maintenanceModeExceptionStatusCode);
    }

    /**
     * Parses authorizedIps to ensure they are valid even when created from a string
     *
     * @param array|string $ips
     *
     * @return array
     */
    public function parseAuthorizedIps($ips)
    {
        $ips = trim($ips);

        if (is_string($ips) && !empty($ips))
        {
            $ips = explode(PHP_EOL, $ips);
        }

        return $this->filterOutArrayValues(
            $ips, function($val) {
            return preg_match('/^[0-9\.\*]{5,15}$/i', $val);
        }
        );
    }

    /**
     * Filters out array values by using a custom filter
     *
     * @param array|string|null $values
     * @param callable|\Closure $filter
     * @param bool              $preserveKeys
     *
     * @return array
     */
    protected function filterOutArrayValues($values = null, \Closure $filter = null, $preserveKeys = false)
    {
        $data = [];

        if (is_array($values) && count($values))
        {
            foreach ($values as $key => $value)
            {
                $value = trim($value);

                if (!empty($value))
                {
                    if (is_callable($filter) && $filter($value))
                    {
                        $data[$key] = $value;
                    }
                }
            }

            if (!$preserveKeys)
            {
                $data = array_values($data);
            }
        }

        return $data;
    }

    /**
     * Parse restricted areas to ensure they are valid even when created from a string
     *
     * @param array|string $areas
     *
     * @return array
     */
    public function parseRestrictedAreas($areas)
    {
        if (is_string($areas) && !empty($areas))
        {
            $areas = trim($areas);
            $areas = explode(PHP_EOL, $areas);
        }

        return $this->filterOutArrayValues(
            $areas,
            function($val) {
                $valid = preg_match('/^[\/\{\}a-z\_\-\?\=]{1,255}$/i', $val);

                if (!$valid)
                {
                    return false;
                }

                return true;
            }
        );
    }
}
