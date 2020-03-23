<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Checkout\Test\TestStep;

use Magento\Checkout\Test\Page\CheckoutOnepage;
use Magento\Mtf\TestStep\TestStepInterface;

/**
 * Class FillShippingMethodStep
 * Fill shipping information
 */
class FillShippingMethodStep implements TestStepInterface
{
    /**
     * Onepage checkout page
     *
     * @var CheckoutOnepage
     */
    protected $checkoutOnepage;

    /**
     * Shipping carrier and method
     *
     * @var array
     */
    protected $shipping;

    /**
     * @constructor
     * @param CheckoutOnepage $checkoutOnepage
     * @param array $shipping
     */
    public function __construct(CheckoutOnepage $checkoutOnepage, array $shipping = [])
    {
        $this->checkoutOnepage = $checkoutOnepage;
        $this->shipping = $shipping;
    }

    /**
     * Select shipping method
     *
     * @return void
     */
    public function run()
    {
        if (!empty($this->shipping)) {
            $this->checkoutOnepage->getShippingMethodBlock()->selectShippingMethod($this->shipping);
            $this->checkoutOnepage->getShippingMethodBlock()->clickContinue();
        }
    }
}
