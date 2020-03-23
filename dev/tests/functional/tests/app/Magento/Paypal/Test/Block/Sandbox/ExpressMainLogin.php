<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Paypal\Test\Block\Sandbox;

use Magento\Mtf\Block\Form;

/**
 * Login to PayPal side within new or old login form.
 */
class ExpressMainLogin extends Form
{
    /**
     * Express Login Block selector.
     *
     * @var string
     */
    protected $expressLogin = '[name=login]';

    /**
     * Old Express Login Block selector.
     *
     * @var string
     */
    protected $expressOldLogin = '#loginBox';

    /**
     * PayPal load spinner.
     *
     * @var string
     */
    protected $preloaderSpinner = '#preloaderSpinner';

    /**
     * Wait for PayPal page is loaded.
     *
     * @return void
     */
    public function waitForFormLoaded()
    {
        $this->waitForElementNotVisible($this->preloaderSpinner);
    }

    /**
     * Determines whether new login form or old is shown.
     *
     * @return \Magento\Paypal\Test\Block\Sandbox\ExpressLogin|\Magento\Paypal\Test\Block\Sandbox\ExpressOldLogin
     */
    public function getLoginBlock()
    {
        if ($this->_rootElement->find($this->expressLogin)->isVisible()) {
            return $this->blockFactory->create(
                \Magento\Paypal\Test\Block\Sandbox\ExpressLogin::class,
                ['element' => $this->_rootElement->find($this->expressLogin)]
            );
        }
        return $this->blockFactory->create(
            \Magento\Paypal\Test\Block\Sandbox\ExpressOldLogin::class,
            ['element' => $this->_rootElement->find($this->expressOldLogin)]
        );
    }
}
