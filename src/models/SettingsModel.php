<?php
namespace selvinortiz\patrol\models;

use craft\base\Model;

/**
 * Class SettingsModel
 *
 * @package selvinortiz\patrol
 */
class SettingsModel extends Model
{
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
     * Anytime www.domain.com/page is requested..
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
     * Whether or not SSL routing should be enabled
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
    public $sslRoutingRestrictedUrls = ['/'];

    /**
     * Whether or not maintenance mode is enabled
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
     * Redirect status code to use when...
     * 1. redirecting to your offline page
     *
     * @var int
     */
    public $maintenanceModelRedirectStatusCode = 302;

    /**
     * Response status code to use when...
     * 1. there is no offline page set
     *
     * @var int
     */
    public $maintenanceModeResponseStatusCode = 403;

    /**
     * The IP addresses that should be allowed in during maintenance mode
     *
     * @var array
     */
    public $maintenanceModeAuthorizedIps = [
        '::1',
        '127.0.0.1'
    ];

    /**
     * @var array
     */
    public $maintenanceModeAccessTokens = [];

    /**
     * @var array
     */
    public $limitCpAccessTo = [];

    /**
     * @return array
     */
    public function rules()
    {
        $rules = [
            ['sslRoutingRestrictedUrls', 'selvinortiz\\patrol\\validators\\RestrictedUrl'],
            ['maintenanceModeAuthorizedIps', 'selvinortiz\\patrol\\validators\\AuthorizedIp'],
        ];

        return array_merge(parent::rules(), $rules);
    }

    /**
     * Returns all properties and their values in JSON format
     *
     * @return string
     */
    public function getJsonObject()
    {
        // array_filter() ensures that empty values are filtered out
        // array_values() ensures encoding to array rather than object
        $this->sslRoutingRestrictedUrls     = array_values(array_filter($this->sslRoutingRestrictedUrls));
        $this->maintenanceModeAuthorizedIps = array_values(array_filter($this->maintenanceModeAuthorizedIps));

        return json_encode(get_object_vars($this));
    }
}
