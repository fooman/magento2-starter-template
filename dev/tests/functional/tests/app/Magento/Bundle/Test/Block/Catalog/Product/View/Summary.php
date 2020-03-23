<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Bundle\Test\Block\Catalog\Product\View;

use Magento\Bundle\Test\Block\Catalog\Product\View\Summary\ConfiguredPrice;

/**
 * Bundle Summary block.
 */
class Summary extends \Magento\Catalog\Test\Block\Product\View
{
    /**
     * Configured Price block selector.
     *
     * @var string
     */
    private $configuredPriceBlockSelector = '.price-configured_price';

    /**
     * Summary items selector.
     *
     * @var string
     */
    private $summaryItemsSelector = '.bundle li div div';

    /**
     * Get configured price block.
     *
     * @return ConfiguredPrice
     */
    public function getConfiguredPriceBlock()
    {
        return $this->blockFactory->create(
            ConfiguredPrice::class,
            ['element' => $this->_rootElement->find($this->configuredPriceBlockSelector)]
        );
    }

    /**
     * Get Bundle Summary row items.
     *
     * @return \Magento\Mtf\Client\ElementInterface[]
     */
    public function getSummaryItems()
    {
        return $this->_rootElement->getElements($this->summaryItemsSelector);
    }
}
