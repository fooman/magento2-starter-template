<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Integration\Test\Block\Adminhtml\Integration\IntegrationGrid;

use Magento\Mtf\Block\Form;
use Magento\Mtf\Client\Locator;

/**
 * Integration resources popup container.
 */
class ResourcesPopup extends Form
{
    /**
     * Selector for "Allow" button.
     *
     * @var string
     */
    protected $allowButtonSelector = '[data-row-dialog="tokens"][type="button"]';

    /**
     * Selector for "Reauthorize" button.
     *
     * @var string
     */
    protected $reauthorizeButtonSelector = '[data-row-dialog="tokens"][data-row-is-reauthorize="1"]';

    /**
     * API content selector.
     *
     * @var string
     */
    protected $content = '#integrations-activate-permissions-content';

    /**
     * Css selector for tree element.
     *
     * @var string
     */
    protected $tree = '[data-role="tree-resources-container"]';

    /**
     * Initialize block. Wait popup to load.
     *
     * @return void
     */
    protected function init()
    {
        parent::init();
        $this->waitPopupToLoad();
    }

    /**
     * Wait until API content is loaded.
     *
     * @return void
     */
    protected function waitPopupToLoad()
    {
        $context = $this->_rootElement;
        $selector = $this->content;
        $context->waitUntil(
            function () use ($context, $selector) {
                return $context->find($selector)->isVisible() ? true : null;
            }
        );
    }

    /**
     * Click allow button in integration resources popup window.
     *
     * @return void
     */
    public function clickAllowButton()
    {
        $this->_rootElement->find($this->allowButtonSelector)->click();
    }

    /**
     * Click reauthorize button in integration resources popup window.
     *
     * @return void
     */
    public function clickReauthorizeButton()
    {
        $this->_rootElement->find($this->reauthorizeButtonSelector)->click();
    }

    /**
     * Get tree structure for selected nodes.
     *
     * @param int|null $level
     * @return array
     */
    public function getStructure($level = null)
    {
        return $this->_rootElement->find($this->tree, Locator::SELECTOR_CSS, 'jquerytree')->getStructure($level);
    }
}
