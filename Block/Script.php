<?php
/**
 * KingfisherDirect_Posthog
 */

namespace KingfisherDirect\Posthog\Block;

use KingfisherDirect\Posthog\Helper\Data as PosthogHelper;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class Script extends Template
{
    /**
     * @var PosthogHelper
     */
    protected $posthogHelper;

    /**
     * @param Context $context
     * @param PosthogHelper $posthogHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        PosthogHelper $posthogHelper,
        array $data = []
    ) {
        $this->posthogHelper = $posthogHelper;
        parent::__construct($context, $data);
    }

    /**
     * Check if PostHog should be rendered
     *
     * @return bool
     */
    public function shouldRender()
    {
        return $this->posthogHelper->isConfigured();
    }

    /**
     * Get Project API Key
     *
     * @return string
     */
    public function getProjectApiKey()
    {
        return $this->posthogHelper->getProjectApiKey();
    }

    /**
     * Get API Host
     *
     * @return string
     */
    public function getApiHost()
    {
        return $this->posthogHelper->getApiHost();
    }

    /**
     * Get Person Profiles
     *
     * @return string
     */
    public function getPersonProfiles()
    {
        return $this->posthogHelper->getPersonProfiles();
    }
}
