<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Newsletter\Test\Block\Adminhtml\Template;

use Magento\Mtf\Client\Locator;

/**
 * Class Grid
 * Newsletter templates grid block
 */
class Grid extends \Magento\Backend\Test\Block\Widget\Grid
{
    /**
     * Filters array mapping
     *
     * @var array
     */
    protected $filters = [
        'code' => [
            'selector' => 'input[name="code"]',
        ],
    ];

    /**
     * Locator value for link in action column
     *
     * @var string
     */
    protected $editLink = 'td.col-template';

    /**
     * Locator for "Action"
     *
     * @var string
     */
    protected $action = '.col-actions [class*="control-select"]';

    /**
     * Action for newsletter template
     *
     * @param string $action
     * @return void
     */
    public function performAction($action)
    {
        $this->_rootElement->find($this->action, Locator::SELECTOR_CSS, 'select')->setValue($action);
    }
}
