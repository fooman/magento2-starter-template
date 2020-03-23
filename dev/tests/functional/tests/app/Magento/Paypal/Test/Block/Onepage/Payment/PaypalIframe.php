<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Paypal\Test\Block\Onepage\Payment;

use Magento\Checkout\Test\Block\Onepage\Payment\Method;
use Magento\Mtf\Client\ElementInterface;
use Magento\Mtf\Fixture\FixtureInterface;

/**
 * Paypal Iframe block.
 */
class PaypalIframe extends Method
{
    /**
     * 'Pay Now' button selector.
     *
     * @var string
     */
    private $payNowButton = '#btn_pay_cc';

    /**
     * PayPal iframe selector.
     *
     * @var string
     */
    private $paypalIframe = '.paypal.iframe';

    /**
     * Credit card form selector.
     *
     * @var string
     */
    private $creditCardForm = '#formCreditCard';

    /**
     * Error message selector.
     *
     * @var string
     */
    private $errorMessage = '#messageBox';

    /**
     * Block for filling credit card data for payment method.
     *
     * @var string
     */
    protected $formBlockCc;

    /**
     * CSS selector for loader block in iframe.
     *
     * @var string
     */
    protected $loader = '#lightBoxDiv';

    /**
     * Fill credit card data in PayPal iframe form.
     *
     * @param FixtureInterface $creditCard
     * @return void
     */
    public function fillPaymentData(FixtureInterface $creditCard)
    {
        $iframeRootElement = $this->switchToPaypalFrame();
        $formBlock = $this->blockFactory->create(
            $this->formBlockCc,
            ['element' => $this->_rootElement->find($this->creditCardForm)]
        );
        $formBlock->fill($creditCard, $iframeRootElement);
        $iframeRootElement->find($this->payNowButton)->click();

        $this->waitSubmitForm($iframeRootElement);

        $this->browser->switchToFrame();
    }

    /**
     * Wait form submit for that payment.
     *
     * @param ElementInterface $iframeRootElement
     * @return void
     */
    protected function waitSubmitForm(ElementInterface $iframeRootElement)
    {
        $loaderElement = $iframeRootElement->find($this->loader);

        $loaderElement->waitUntil(function () use ($loaderElement) {
            return $loaderElement->isVisible() ? true : null;
        });

        $loaderElement->waitUntil(function () use ($loaderElement) {
            return !$loaderElement->isVisible() ? true : null;
        });
    }

    /**
     * Check if error message is appeared.
     *
     * @return bool
     */
    public function isErrorMessageVisible()
    {
        $isErrorMessageVisible = false;
        if ($this->_rootElement->find($this->paypalIframe)->isPresent()) {
            $iframeRootElement = $this->switchToPaypalFrame();
            $isErrorMessageVisible = $iframeRootElement->find($this->errorMessage)->isVisible();
            $this->browser->switchToFrame();
        }
        return $isErrorMessageVisible;
    }

    /**
     * Change the focus to a PayPal frame.
     *
     * @return ElementInterface
     */
    private function switchToPaypalFrame()
    {
        $iframeLocator = $this->browser->find($this->paypalIframe)->getLocator();
        $this->browser->switchToFrame($iframeLocator);
        return $this->browser->find('body');
    }
}
