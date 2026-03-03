<?php
/**
 * KingfisherDirect_Posthog
 */

namespace KingfisherDirect\Posthog\Block;

use KingfisherDirect\Posthog\Helper\Data as PosthogHelper;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class Script extends Template
{
    /**
     * @var PosthogHelper
     */
    protected $posthogHelper;

    /**
     * @var CustomerSession
     */
    protected $customerSession;

    /**
     * @param Context $context
     * @param PosthogHelper $posthogHelper
     * @param CustomerSession $customerSession
     * @param array $data
     */
    public function __construct(
        Context $context,
        PosthogHelper $posthogHelper,
        CustomerSession $customerSession,
        array $data = []
    ) {
        $this->posthogHelper = $posthogHelper;
        $this->customerSession = $customerSession;
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

    /**
     * Get logged-in customer data for identify call, or empty array if guest
     *
     * @return array
     */
    public function getCustomerData(): array
    {
        if (!$this->customerSession->isLoggedIn()) {
            return [];
        }

        $customer = $this->customerSession->getCustomer();

        return [
            'id'    => (string) $customer->getId(),
            'email' => $customer->getEmail(),
            'name'  => trim($customer->getFirstname() . ' ' . $customer->getLastname()),
        ];
    }
}
