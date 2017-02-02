<?php
namespace selvinortiz\patrol\models;

use craft\base\Model;

/**
 * Class SettingsModel
 *
 * @package selvinortiz\patrol
 */
class SettingsModel extends Model {

    /**
     * @var string
     */
    public $primaryDomain = '*';

    /**
     * Whether or not SSL routing should be enabled
     *
     * @var bool
     */
    public $sslRoutingEnabled = false;

    /**
     * The base URL to use when redirecting to and from restricted URLs
     *
     * @var string
     */
    public $sslRoutingBaseUrl = '{siteUrl}';

    /**
     * The URLs to enforce SSL on
     *
     * @var array
     */
    public $sslRoutingRestrictedUrls = ['/'];

    /**
     * Whether or not maintenance mode is enabled
     *
     * @var bool
     */
    public $maintenanceModeEnabled = false;

    /**
     * The URL to redirect to when an unauthorized request is made during maintenance mode
     *
     * @var array
     */
    public $maintenanceModePageUrl = '/offline';

    /**
     * The IP addresses that should be allowed in during maintenance mode
     *
     * @var array
     */
    public $maintenanceModeAuthorizedIps = ['::1', '127.0.0.1'];

    /**
     * @var array
     */
    public $limitCpAccessTo = [];

    /**
     * @return array
     */
    public function rules() {
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
    public function getJsonObject() {
        // array_filter() ensures that empty values are filtered out
        // array_values() ensures encoding to array rather than object
        $this->sslRoutingRestrictedUrls     = array_values(array_filter($this->sslRoutingRestrictedUrls));
        $this->maintenanceModeAuthorizedIps = array_values(array_filter($this->maintenanceModeAuthorizedIps));

        return json_encode(get_object_vars($this));
    }
}
