<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Catalog\Test\Block\Adminhtml\Product\Attribute\Edit\Tab;

use Magento\Backend\Test\Block\Widget\Tab;
use Magento\Mtf\Client\Element\SimpleElement;
use Magento\Mtf\Client\Element;

/**
 * Options form.
 */
class Options extends Tab
{
    /**
     * 'Add Option' button
     *
     * @var string
     */
    protected $addOption = '#add_new_option_button';

    /**
     * Fill 'Options' tab
     *
     * @param array $fields
     * @param SimpleElement|null $element
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setFieldsData(array $fields, SimpleElement $element = null)
    {
        foreach ($fields['options']['value'] as $field) {
            $this->_rootElement->find($this->addOption)->click();
            $this->blockFactory->create(
                \Magento\Catalog\Test\Block\Adminhtml\Product\Attribute\Edit\Tab\Options\Option::class,
                ['element' => $this->_rootElement->find('.ui-sortable tr:last-child')]
            )->fillOptions($field);
        }
        return $this;
    }
}
