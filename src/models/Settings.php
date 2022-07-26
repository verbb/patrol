<?php
namespace verbb\patrol\models;

use verbb\patrol\validators\AuthorizedIp;
use verbb\patrol\validators\RestrictedUrl;

use craft\base\Model;

class Settings extends Model
{
    // Properties
    // =========================================================================

    public string $primaryDomain = '';
    public int $redirectStatusCode = 302;
    public bool $sslRoutingEnabled = false;
    public string $sslRoutingBaseUrl = '';
    public array $sslRoutingRestrictedUrls = ['/'];
    public bool $maintenanceModeEnabled = false;
    public string $maintenanceModePageUrl = '/offline';
    public int $maintenanceModeResponseStatusCode = 403;
    public array $maintenanceModeAuthorizedIps = ['::1', '127.0.0.1'];
    public array $maintenanceModeAccessTokens = [];
    public array $limitCpAccessTo = [];


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
