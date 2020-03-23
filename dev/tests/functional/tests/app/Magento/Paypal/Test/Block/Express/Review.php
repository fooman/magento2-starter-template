<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Paypal\Test\Block\Express;

use Magento\Checkout\Test\Block\Onepage\AbstractReview;
use Magento\Mtf\Client\Locator;

/**
 * Review order on Magento side after redirecting from PayPal.
 */
class Review extends AbstractReview
{
    /**
     * Shipping methods dropdown.
     *
     * @var string
     */
    protected $shippingMethod = '#shipping-method';

    /**
     * Select shipping method.
     *
     * @param array $shippingMethod
     */
    public function selectShippingMethod(array $shippingMethod)
    {
        $this->waitForElementVisible($this->shippingMethod);
        $shippingElement = $this->_rootElement->find(
            $this->shippingMethod,
            Locator::SELECTOR_CSS,
            \Magento\Paypal\Test\Block\Express\Review\ShippingoptgroupElement::class
        );
        $shippingElement->setValue($shippingMethod['shipping_service'] . '/' . $shippingMethod['shipping_method']);
        $this->waitForElementNotVisible('#review-please-wait');
    }

    /**
     * Click "Place Order" button.
     */
    public function placeOrder()
    {
        $this->_rootElement->find('#review-button')->click();
    }
}
