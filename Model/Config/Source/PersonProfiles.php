<?php
/**
 * KingfisherDirect_Posthog
 */

namespace KingfisherDirect\Posthog\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class PersonProfiles implements OptionSourceInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'always', 'label' => __('Always')],
            ['value' => 'identified_only', 'label' => __('Identified Only')],
            ['value' => 'never', 'label' => __('Never')]
        ];
    }
}
