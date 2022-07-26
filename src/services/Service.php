<?php
namespace verbb\patrol\services;

use verbb\patrol\Patrol;

use Craft;
use craft\base\Component;
use craft\helpers\UrlHelper;

use yii\base\ErrorException;
use yii\base\Exception;
use yii\base\InvalidConfigException;
use yii\web\HttpException;

use Closure;
use Throwable;

class Service extends Component
{
    // Properties
    // =========================================================================

    /**
     * Key/value pairs used when parsing restricted areas like {cpTrigger}
     *
     * @var array
     */
    protected $dynamicParams;


    // Public Methods
    // =========================================================================

    public function allow(): void
    {
        $settings = Patrol::$plugin->getSettings();
        $request = Craft::$app->getRequest();
        $requestToken = $request->getQueryParam('access');
        $requestingIp = $request->getUserIp();

        if (!empty($requestToken) && in_array($requestToken, $settings->maintenanceModeAccessTokens)) {
            if (!in_array($requestingIp, $settings->maintenanceModeAuthorizedIps)) {
                $settings->maintenanceModeAuthorizedIps[] = $requestingIp;

                Craft::$app->getPlugins()->savePluginSettings(Patrol::$plugin, $settings->getAttributes());
            }
        }
    }

    /**
     * @throws ErrorException
     * @throws HttpException
     * @throws Exception
     * @throws InvalidConfigException
     * @throws Throwable
     */
    public function watch(): void
    {
        $this->handleRouting();
        $this->handleSslRouting();
        $this->handleMaintenanceMode();
    }

    /**
     * @throws ErrorException
     * @throws Exception
     * @throws InvalidConfigException
     */
    public function handleRouting(): void
    {
        $settings = Patrol::$plugin->getSettings();
        $request = Craft::$app->getRequest();

        if (!is_string($settings->primaryDomain) || empty($settings->primaryDomain)) {
            return;
        }

        $primaryDomain = mb_strtolower(trim($settings->primaryDomain));
        $requestedDomain = mb_strtolower(trim(Craft::$app->getRequest()->getHostName()));

        if (empty($primaryDomain) || $primaryDomain === '*') {
            return;
        }

        if ($primaryDomain === $requestedDomain) {
            return;
        }

        $http = $request->getIsSecureConnection() ? 'https://' : 'http://';

        $request->setHostInfo($http . $primaryDomain);
        Craft::$app->getResponse()->redirect($request->getUrl(), $settings->redirectStatusCode);
    }

    /**
     * Forces SSL based on restricted URLs
     * The environment settings take priority over those defined in the control panel
     *
     * @return bool
     *
     * @throws ErrorException
     * @throws Exception
     * @throws InvalidConfigException
     * @throws Throwable
     */
    public function handleSslRouting(): bool
    {
        $settings = Patrol::$plugin->getSettings();
        $request = Craft::$app->getRequest();
        $view = Craft::$app->getView();

        if ($settings->sslRoutingEnabled && !$request->getIsConsoleRequest()) {
            $requestedUrl = $request->getUrl();
            $restrictedUrls = $settings->sslRoutingRestrictedUrls;

            if (!$request->isSecureConnection) {
                foreach ($restrictedUrls as $restrictedUrl) {
                    // Parse dynamic variables like /{cpTrigger}
                    if (stripos($restrictedUrl, '{') !== false) {
                        $restrictedUrl = $view->renderObjectTemplate($restrictedUrl, $this->getDynamicParams());
                    }

                    $restrictedUrl = '/' . ltrim($restrictedUrl, '/');

                    if (stripos($requestedUrl, $restrictedUrl) === 0) {
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
     * @throws Exception
     */
    protected function getDynamicParams(): array
    {
        if (is_null($this->dynamicParams)) {
            $this->dynamicParams = [
                'siteUrl' => UrlHelper::siteUrl(),
                'cpTrigger' => Craft::$app->getConfig()->getGeneral()->cpTrigger,
                'actionTrigger' => Craft::$app->getConfig()->getGeneral()->actionTrigger,
            ];
        }

        return $this->dynamicParams;
    }

    /**
     * Redirects to the HTTPS version of the requested URL
     *
     * @throws ErrorException
     * @throws Exception
     * @throws InvalidConfigException
     * @throws Throwable
     */
    protected function forceSsl(): void
    {
        $settings = Patrol::$plugin->getSettings();
        $request = Craft::$app->getRequest();
        $view = Craft::$app->getView();

        // Define and trim base URL
        $baseUrl = trim($settings->sslRoutingBaseUrl);

        // Parse dynamic params in SSL routing URL
        if (mb_stripos($baseUrl, '{') !== false) {
            $baseUrl = $view->renderObjectTemplate($settings->sslRoutingBaseUrl, $this->getDynamicParams());
        }

        // Protect against invalid base URL
        if (empty($baseUrl) || $baseUrl == '/') {
            $baseUrl = trim($request->hostInfo);
        }

        // Define and trim URI to append to the base URL
        $requestUri = trim($request->getUrl());

        // Base URL should now be set to 'http://domain.ext' or 'http://domain.ext/'
        // Request URI can could be empty or '/page?key=val'

        // Define the final URL formed by the host portion and the URI portion
        $url = sprintf('%s%s', rtrim($baseUrl, '/'), $requestUri);

        // Ensure https is used
        $url = str_replace('http:', 'https:', $url);

        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new ErrorException(
                Craft::t('patrol', '{url} is not a valid URL', ['url' => $url])
            );
        }

        Craft::$app->getResponse()->redirect($url, $settings->redirectStatusCode);
    }

    /**
     * Restricts accessed based on authorizedIps
     *
     * @return bool
     *
     * @throws HttpException
     * @throws InvalidConfigException
     */
    public function handleMaintenanceMode(): ?bool
    {
        $settings = Patrol::$plugin->getSettings();

        // Authorize logged in admins on the fly
        if ($this->doesCurrentUserHaveAccess()) {
            return true;
        }

        if (Craft::$app->getRequest()->isSiteRequest && $settings->maintenanceModeEnabled) {
            $requestingIp = $this->getRequestingIp();
            $authorizedIps = $settings->maintenanceModeAuthorizedIps;
            $maintenanceUrl = $settings->maintenanceModePageUrl;

            if ($maintenanceUrl == Craft::$app->getRequest()->getUrl()) {
                return true;
            }

            if (empty($authorizedIps)) {
                $this->forceRedirect($maintenanceUrl);
            }

            if (is_array($authorizedIps) && count($authorizedIps)) {
                if (in_array($requestingIp, $authorizedIps)) {
                    return true;
                }

                foreach ($authorizedIps as $authorizedIp) {
                    $authorizedIp = str_replace('*', '', $authorizedIp);

                    if (stripos($requestingIp, $authorizedIp) === 0) {
                        return true;
                    }
                }

                $this->forceRedirect($maintenanceUrl);
            }
        }

        return false;
    }

    /**
     * Returns whether the current user has access during maintenance mode
     */
    protected function doesCurrentUserHaveAccess(): bool
    {
        // Admins have access by default
        if (Craft::$app->getUser()->getIsAdmin()) {
            return true;
        }

        // User has the right permission
        if (Craft::$app->getUser()->checkPermission('patrolMaintenanceModeBypass')) {
            return true;
        }

        return false;
    }

    /**
     * Ensures that we get the right IP address even if behind CloudFlare or most proxies
     *
     * @return string
     */
    public function getRequestingIp()
    {
        if (isset($_SERVER['HTTP_CF_CONNECTING_IP'])) {
            return $_SERVER['HTTP_CF_CONNECTING_IP'];
        }

        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return isset($_SERVER['HTTP_X_FORWARDED_FOR']);
        }

        if (isset($_SERVER['HTTP_X_REAL_IP'])) {
            return isset($_SERVER['HTTP_X_REAL_IP']);
        }

        return $_SERVER['REMOTE_ADDR'];
    }

    /**
     * @param string $redirectTo
     *
     * @throws HttpException
     */
    protected function forceRedirect(string $redirectTo = ''): void
    {
        $settings = Patrol::$plugin->getSettings();

        if (empty($redirectTo)) {
            $this->runDefaultBehavior();
        }

        Craft::$app->getResponse()->redirect($redirectTo, $settings->redirectStatusCode);
    }

    /**
     * @throws HttpException
     */
    protected function runDefaultBehavior(): void
    {
        $settings = Patrol::$plugin->getSettings();

        throw new HttpException($settings->maintenanceModeResponseStatusCode);
    }

    /**
     * Parses authorizedIps to ensure they are valid even when created from a string
     *
     * @param array|string $ips
     *
     * @return array
     */
    public function parseAuthorizedIps($ips): array
    {
        $ips = trim($ips);

        if (is_string($ips) && !empty($ips)) {
            $ips = explode(PHP_EOL, $ips);
        }

        return $this->filterOutArrayValues($ips, function($val) {
            return preg_match('/^[0-9\.\*]{5,15}$/i', $val);
        });
    }

    /**
     * Filters out array values by using a custom filter
     *
     * @param array|string|null $values
     * @param Closure|null $filter
     * @param bool $preserveKeys
     *
     * @return array
     */
    protected function filterOutArrayValues($values = null, Closure $filter = null, bool $preserveKeys = false): array
    {
        $data = [];

        if (is_array($values) && count($values)) {
            foreach ($values as $key => $value) {
                $value = trim($value);

                if (!empty($value) && is_callable($filter) && $filter($value)) {
                    $data[$key] = $value;
                }
            }

            if (!$preserveKeys) {
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
    public function parseRestrictedAreas($areas): array
    {
        if (is_string($areas) && !empty($areas)) {
            $areas = trim($areas);
            $areas = explode(PHP_EOL, $areas);
        }

        return $this->filterOutArrayValues($areas, function($val) {
            $valid = preg_match('/^[\/\{\}a-z\_\-\?\=]{1,255}$/i', $val);

            if (!$valid) {
                return false;
            }

            return true;
        });
    }
}
