<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\GroupedProduct\Test\Block\Adminhtml\Product\Grouped\AssociatedProducts\ListAssociatedProducts;

use Magento\Mtf\Block\Form;

/**
 * Assigned product row to grouped option.
 */
class Product extends Form
{
    /**
     * Selector for product sku field.
     *
     * @var string
     */
    private $productSku = 'span[data-index="sku"]';

    /**
     * Fill product options.
     *
     * @param string $qtyValue
     * @return void
     */
    public function fillOption($qtyValue)
    {
        $mapping = $this->dataMapping($qtyValue);
        $this->_fill($mapping);
    }

    /**
     * Get product options.
     *
     * @param array $fields
     * @return array
     */
    public function getOption(array $fields)
    {
        $mapping = $this->dataMapping($fields);
        $newFields = $this->_getData($mapping);
        $newFields['name'] = $this->_rootElement->find('[data-index="name"]')->getText();
        return $newFields;
    }

    /**
     * Get product sku.
     *
     * @return string
     */
    public function getProductSku()
    {
        return $this->_rootElement->find($this->productSku)->getText();
    }
}
