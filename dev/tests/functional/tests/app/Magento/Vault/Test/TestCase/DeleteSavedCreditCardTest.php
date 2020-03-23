<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Vault\Test\TestCase;

use Magento\Checkout\Test\Page\CheckoutOnepage;
use Magento\Customer\Test\Fixture\Customer;
use Magento\Mtf\ObjectManager;
use Magento\Mtf\TestCase\Injectable;
use Magento\Vault\Test\Constraint\AssertCreditCardNotPresentOnCheckout;

/**
 * Preconditions:
 * 1. Credit card is saved during checkout
 *
 * Steps:
 * 1. Log in Storefront.
 * 2. Click 'My Account' link.
 * 3. Click 'My Credit Cards' tab.
 * 4. Click the 'Delete' button next to stored credit card.
 * 5. Click 'Delete' button.
 * 6. Go to One page Checkout
 * 7. Perform assertions.
 *
 * @group Vault
 * @ZephyrId MAGETWO-54059, MAGETWO-54072, MAGETWO-54068, MAGETWO-54015, MAGETWO-54011
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class DeleteSavedCreditCardTest extends Injectable
{
    /* tags */
    const MVP = 'yes';
    const TEST_TYPE = '3rd_party_test';
    const SEVERITY = 'S1';
    /* end tags */

    /**
     * Page for one page checkout.
     *
     * @var CheckoutOnepage
     */
    private $checkoutOnepage;

    /**
     * Injection data.
     *
     * @param CheckoutOnepage $checkoutOnepage
     * @return void
     */
    public function __inject(CheckoutOnepage $checkoutOnepage)
    {
        $this->checkoutOnepage = $checkoutOnepage;
    }

    /**
     * Runs delete saved credit card test.
     *
     * @param AssertCreditCardNotPresentOnCheckout $assertCreditCardNotPresentOnCheckout
     * @param $products
     * @param $configData
     * @param $customer
     * @param $checkoutMethod
     * @param $shippingAddress
     * @param $shipping
     * @param array $payments
     * @param $creditCardSave
     * @return void
     */
    public function test(
        AssertCreditCardNotPresentOnCheckout $assertCreditCardNotPresentOnCheckout,
        $products,
        $configData,
        $customer,
        $checkoutMethod,
        $shippingAddress,
        $shipping,
        array $payments,
        $creditCardSave
    ) {
        // Preconditions
        $products = $this->prepareProducts($products);
        $this->setupConfiguration($configData);
        $customer = $this->createCustomer($customer);

        // Steps
        foreach ($payments as $key => $payment) {
            $this->addToCart($products);
            $this->proceedToCheckout();
            if ($key < 1) { // if this is the first order to be placed
                $this->selectCheckoutMethod($checkoutMethod, $customer);
                $this->fillShippingAddress($shippingAddress);
            }
            $this->fillShippingMethod($shipping);
            if ($key >= 2) { // if this order will be placed via stored credit card
                $this->useSavedCreditCard($payment['vault']);
            } else {
                $arguments = isset($payment['arguments']) ? $payment['arguments'] : [];
                $this->selectPaymentMethod($payment, $payment['creditCard'], $arguments);
                $this->saveCreditCard($payment, $creditCardSave);
            }
            $this->placeOrder();
        }
        // Delete credit cards from Stored Payment Methods and verify they are not available on checkout
        $paymentsCount = count($payments);
        for ($i = 2; $i < $paymentsCount; $i++) {
            $deletedCard = $this->deleteCreditCardFromMyAccount(
                $customer,
                $payments[$i]['creditCard']
            );
            $this->addToCart($products);
            $this->proceedToCheckout();
            $this->fillShippingMethod($shipping);
            $assertCreditCardNotPresentOnCheckout->processAssert(
                $this->checkoutOnepage,
                $deletedCard['deletedCreditCard']
            );
        }
    }

    /**
     * Setup configuration step.
     *
     * @param $configData
     * @return void
     */
    private function setupConfiguration($configData)
    {
        $setupConfigurationStep = ObjectManager::getInstance()->create(
            \Magento\Config\Test\TestStep\SetupConfigurationStep::class,
            ['configData' => $configData]
        );

        $setupConfigurationStep->run();
    }

    /**
     * Create products step.
     *
     * @param string $productList
     * @return array
     */
    protected function prepareProducts($productList)
    {
        $addToCartStep = ObjectManager::getInstance()->create(
            \Magento\Catalog\Test\TestStep\CreateProductsStep::class,
            ['products' => $productList]
        );

        $result = $addToCartStep->run();
        return $result['products'];
    }

    /**
     * Add to cart step.
     *
     * @param array $products
     * @return void
     */
    protected function addToCart(array $products)
    {
        $addToCartStep = ObjectManager::getInstance()->create(
            \Magento\Checkout\Test\TestStep\AddProductsToTheCartStep::class,
            ['products' => $products]
        );
        $addToCartStep->run();
    }

    /**
     * Proceed to checkout step.
     *
     * @return void
     */
    protected function proceedToCheckout()
    {
        $clickProceedToCheckoutStep = ObjectManager::getInstance()->create(
            \Magento\Checkout\Test\TestStep\ProceedToCheckoutStep::class
        );
        $clickProceedToCheckoutStep->run();
    }

    /**
     * Create customer step.
     *
     * @param array $customer
     * @return Customer
     */
    protected function createCustomer(array $customer)
    {
        $createCustomerStep = ObjectManager::getInstance()->create(
            \Magento\Customer\Test\TestStep\CreateCustomerStep::class,
            ['customer' => $customer]
        );
        $result = $createCustomerStep->run();
        return $result['customer'];
    }

    /**
     * Select Checkout method step.
     *
     * @param $checkoutMethod
     * @param $customer
     * @return void
     */
    protected function selectCheckoutMethod($checkoutMethod, $customer)
    {
        $selectCheckoutMethodStep = ObjectManager::getInstance()->create(
            \Magento\Checkout\Test\TestStep\SelectCheckoutMethodStep::class,
            [
                'checkoutMethod' => $checkoutMethod,
                'customer' => $customer,
            ]
        );
        $selectCheckoutMethodStep->run();
    }

    /**
     * Fill shipping address step.
     *
     * @param array $shippingAddress
     * @return void
     */
    protected function fillShippingAddress(array $shippingAddress)
    {
        $fillShippingAddressStep = ObjectManager::getInstance()->create(
            \Magento\Checkout\Test\TestStep\FillShippingAddressStep::class,
            ['shippingAddress' => $shippingAddress]
        );
        $fillShippingAddressStep->run();
    }

    /**
     * Add products to cart.
     *
     * @param array $shipping
     * @return void
     */
    protected function fillShippingMethod(array $shipping)
    {
        $fillShippingMethodStep = ObjectManager::getInstance()->create(
            \Magento\Checkout\Test\TestStep\FillShippingMethodStep::class,
            ['shipping' => $shipping]
        );
        $fillShippingMethodStep->run();
    }

    /**
     * Select payment method step.
     *
     * @param array $payment
     * @param array $creditCard
     * @param array $arguments
     * @return void
     */
    protected function selectPaymentMethod(array $payment, array $creditCard, array $arguments)
    {
        $selectPaymentMethodStep = ObjectManager::getInstance()->create(
            \Magento\Checkout\Test\TestStep\SelectPaymentMethodStep::class,
            array_merge(
                [
                    'payment' => $payment,
                    'creditCard' => $creditCard,
                ],
                $arguments
            )
        );
        $selectPaymentMethodStep->run();
    }

    /**
     * Add products to cart
     *
     * @param $payment
     * @param $creditCardSave
     * @return void
     */
    protected function saveCreditCard($payment, $creditCardSave)
    {
        $saveCreditCardStep = ObjectManager::getInstance()->create(
            \Magento\Vault\Test\TestStep\SaveCreditCardStep::class,
            [
                'creditCardSave' => $creditCardSave,
                'payment' => $payment
            ]
        );
        $saveCreditCardStep->run();
    }

    /**
     * Fill billing information step.
     *
     * @return void
     */
    protected function fillBillingInformation()
    {
        $fillBillingInformationStep = ObjectManager::getInstance()->create(
            \Magento\Checkout\Test\TestStep\FillBillingInformationStep::class
        );
        $fillBillingInformationStep->run();
    }

    /**
     * Place order step.
     *
     * @return void
     */
    protected function placeOrder()
    {
        $placeOrderStep = ObjectManager::getInstance()->create(
            \Magento\Checkout\Test\TestStep\PlaceOrderStep::class
        );
        $placeOrderStep->run();
    }

    /**
     * Use saved credit card step.
     *
     * @param $payment
     * @return void
     */
    protected function useSavedCreditCard($payment)
    {
        $useSavedCreditCardStep = ObjectManager::getInstance()->create(
            \Magento\Vault\Test\TestStep\UseSavedPaymentMethodStep::class,
            ['vault' => $payment]
        );
        $useSavedCreditCardStep->run();
    }

    /**
     * Delete credit card from My Account step.
     *
     * @param $customer
     * @param $creditCard
     * @return array
     */
    protected function deleteCreditCardFromMyAccount($customer, $creditCard)
    {
        $deleteCreditCardFromMyAccountStep = ObjectManager::getInstance()->create(
            \Magento\Vault\Test\TestStep\DeleteCreditCardFromMyAccountStep::class,
            [
                'customer' => $customer,
                'creditCard' => $creditCard
            ]
        );
        $deletedCard = $deleteCreditCardFromMyAccountStep->run();
        return $deletedCard;
    }
}
