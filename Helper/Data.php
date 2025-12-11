<?php
/**
 * KingfisherDirect_Posthog
 */

namespace KingfisherDirect\Posthog\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    const XML_PATH_ENABLED = 'posthog/general/enabled';
    const XML_PATH_PROJECT_API_KEY = 'posthog/general/project_api_key';
    const XML_PATH_API_HOST = 'posthog/general/api_host';
    const XML_PATH_PERSON_PROFILES = 'posthog/general/person_profiles';

    /**
     * Check if PostHog is enabled
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isEnabled($storeId = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get Project API Key
     *
     * @param int|null $storeId
     * @return string
     */
    public function getProjectApiKey($storeId = null)
    {
        return (string) $this->scopeConfig->getValue(
            self::XML_PATH_PROJECT_API_KEY,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get API Host
     *
     * @param int|null $storeId
     * @return string
     */
    public function getApiHost($storeId = null)
    {
        return (string) $this->scopeConfig->getValue(
            self::XML_PATH_API_HOST,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get Person Profiles setting
     *
     * @param int|null $storeId
     * @return string
     */
    public function getPersonProfiles($storeId = null)
    {
        return (string) $this->scopeConfig->getValue(
            self::XML_PATH_PERSON_PROFILES,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Check if PostHog is properly configured
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isConfigured($storeId = null)
    {
        return $this->isEnabled($storeId)
            && !empty($this->getProjectApiKey($storeId))
            && !empty($this->getApiHost($storeId));
    }
}
