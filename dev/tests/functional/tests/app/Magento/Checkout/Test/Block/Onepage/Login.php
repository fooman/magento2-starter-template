<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Checkout\Test\Block\Onepage;

use Magento\Checkout\Test\Fixture\Checkout;
use Magento\Customer\Test\Fixture\Customer;
use Magento\Mtf\Block\Form;
use Magento\Mtf\Fixture\FixtureInterface;

/**
 * One page checkout status login block.
 */
class Login extends Form
{
    /**
     * Login button
     *
     * @var string
     */
    protected $login = '[data-action=checkout-method-login]';

    /**
     * Continue checkout button
     *
     * @var string
     */
    protected $continue = '#onepage-guest-register-button';

    /**
     * Locator value for "Check Out as Guest" radio button.
     *
     * @var string
     */
    protected $guestCheckout = '[id="login:guest"]';

    /**
     * 'Register' radio button
     *
     * @var string
     */
    protected $registerCustomer = '[id="login:register"]';

    /**
     * Selector for loading mask element
     *
     * @var string
     */
    protected $loadingMask = '.loading-mask';

    /**
     * Select how to perform checkout whether guest or registered customer.
     *
     * @param FixtureInterface $fixture
     * @return void
     */
    public function checkoutMethod(FixtureInterface $fixture)
    {
        /** @var Checkout $fixture */
        if ($fixture->isRegisteredCustomer()) {
            $this->loginCustomer($fixture->getCustomer());
        } else {
            $this->guestCheckout();
            $this->clickContinue();
        }
    }

    /**
     * Perform guest checkout.
     *
     * @return void
     */
    public function guestCheckout()
    {
        $this->waitForElementVisible($this->guestCheckout);
        $this->_rootElement->find($this->guestCheckout)->click();
    }

    /**
     * Login customer during checkout.
     *
     * @param FixtureInterface $customer
     * @return void
     */
    public function loginCustomer(FixtureInterface $customer)
    {
        $this->fill($customer);
        $this->_rootElement->find($this->login)->click();
        $this->waitForElementNotVisible($this->loadingMask);
    }

    /**
     * Fill required fields for guest checkout.
     *
     * @param Customer $customer
     * @return void
     */
    public function fillGuestFields(Customer $customer)
    {
        $mapping = $this->dataMapping();
        $this->_rootElement->find($mapping['email']['selector'], $mapping['email']['strategy'])
            ->setValue($customer->getEmail());
        $this->waitForElementNotVisible($this->loadingMask);
    }

    /**
     * Click continue on checkout method block.
     *
     * @return void
     */
    public function clickContinue()
    {
        $this->_rootElement->find($this->continue)->click();
        $browser = $this->browser;
        $selector = $this->loadingMask;
        $browser->waitUntil(
            function () use ($browser, $selector) {
                $element = $browser->find($selector);
                return $element->isVisible() == false ? true : null;
            }
        );
    }
}
