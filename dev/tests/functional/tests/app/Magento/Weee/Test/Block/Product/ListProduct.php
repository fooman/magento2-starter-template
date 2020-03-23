<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Weee\Test\Block\Product;

use Magento\Weee\Test\Block\Product\ProductList\ProductItem;
use Magento\Mtf\Client\Locator;
use Magento\Mtf\Fixture\FixtureInterface;

/**
 * Product list block.
 */
class ListProduct extends \Magento\Catalog\Test\Block\Product\ListProduct
{
    /**
     * Return product item block.
     *
     * @param FixtureInterface $product
     * @return ProductItem
     */
    public function getProductItem(FixtureInterface $product)
    {
        $locator = sprintf($this->productItem, $product->getName());
        return $this->blockFactory->create(
            \Magento\Weee\Test\Block\Product\ProductList\ProductItem::class,
            ['element' => $this->_rootElement->find($locator, Locator::SELECTOR_XPATH)]
        );
    }
}
