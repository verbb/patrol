<?php
namespace verbb\patrol\models;

use verbb\patrol\validators\AuthorizedIp;
use verbb\patrol\validators\RestrictedUrl;

use craft\base\Model;

class Settings extends Model
{
    // Properties
    // =========================================================================

    /**
     * Primary domain to enforce
     *
     * If your site is accessible via multiple domains,
     * you may want to ensure that it can only be accessed by the primary domain.
     *
     * @example
     * - domain.com (Primary)
     * - www.domain.com
     *
     * Anytime www.domain.com/page is requested.
     * a redirect to domain.com/page will be performed
     *
     * @var string
     */
    public $primaryDomain = '';

    /**
     * Redirect status code to use when...
     * 1. redirecting to and from SSL restricted URLs
     * 2. redirecting to primary domain, if one is set.
     *
     * @var int
     */
    public $redirectStatusCode = 302;

    /**
     * Whether SSL routing should be enabled
     *
     * @var bool
     */
    public $sslRoutingEnabled = false;

    /**
     * Base URL to use when...
     * 1. redirecting to and from SSL restricted areas
     *
     * @var string
     */
    public $sslRoutingBaseUrl = '';

    /**
     * URLs to force SSL on
     *
     * @example
     * - /           enables SSL everywhere
     * - {cpTrigger} enables SSL on the CP
     * - /members    enables SSL on everything after /members
     *
     * @var array
     */
    public array $sslRoutingRestrictedUrls = ['/'];

    /**
     * Whether maintenance mode is enabled
     *
     * If maintenance mode is enabled...
     * - unauthorized users will be routed to your offline page
     *   > or be sent a response with a specific status code
     * - authorized users can access the site as usual
     *
     * Users can be authorized in several methods
     * - by logging into the CP and...
     *   > be assigned to the admin group
     *   > be given the Patrol permission
     * - by adding their IP to the list of authorized users
     *
     * @var bool
     */
    public $maintenanceModeEnabled = false;

    /**
     * URL to redirect to when...
     * maintenance mode is enabled and the request is not authorized
     *
     * @var array
     */
    public $maintenanceModePageUrl = '/offline';

    /**
     * Response status code to use when...
     * 1. there is no offline page set
     *
     * @var int
     */
    public $maintenanceModeResponseStatusCode = 403;

    /**
     * IP addresses that should be allowed during maintenance
     * ...even if they're not logged in.
     *
     * @var array
     */
    public array $maintenanceModeAuthorizedIps = [
        '::1',
        '127.0.0.1',
    ];

    /**
     * @var array
     */
    public $maintenanceModeAccessTokens = [];

    /**
     * @var array
     */
    public $limitCpAccessTo = [];


    // Public Methods
    // =========================================================================

    /**
     * @return array
     */
    public function defineRules(): array
    {
        $rules = parent::defineRules();

        $rules[] = ['sslRoutingRestrictedUrls', RestrictedUrl::class];
        $rules[] = ['maintenanceModeAuthorizedIps', AuthorizedIp::class];

        return $rules;
    }

    public function getMaintenanceModeAuthorizedIps(): string
    {
        return $this->_getMultilineFromArray($this->maintenanceModeAuthorizedIps);
    }

    public function getSslRoutingRestrictedUrls(): string
    {
        return $this->_getMultilineFromArray($this->sslRoutingRestrictedUrls);
    }

    public function setAttributes($values, $safeOnly = true): void
    {
        // Normalize array data set using a textarea
        $maintenanceModeAuthorizedIps = $values['maintenanceModeAuthorizedIps'] ?? '';
        $sslRoutingRestrictedUrls = $values['sslRoutingRestrictedUrls'] ?? '';

        $values['maintenanceModeAuthorizedIps'] = $this->_getArrayFromMultiline($maintenanceModeAuthorizedIps);
        $values['sslRoutingRestrictedUrls'] = $this->_getArrayFromMultiline($sslRoutingRestrictedUrls);

        parent::setAttributes($values, $safeOnly);
    }


    // Private Methods
    // =========================================================================

    private function _getMultilineFromArray($value): string
    {
        return implode(PHP_EOL, $value);
    }

    private function _getArrayFromMultiline($value): array
    {
        if (is_array($value)) {
            return $value;
        }

        $array = [];

        if ($value) {
            $array = array_map('trim', explode(PHP_EOL, $value));
        }

        return $array;
    }
}
