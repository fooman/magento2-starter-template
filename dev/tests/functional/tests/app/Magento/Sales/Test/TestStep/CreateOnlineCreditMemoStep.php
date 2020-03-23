<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Sales\Test\TestStep;

use Magento\Checkout\Test\Fixture\Cart;
use Magento\Mtf\TestStep\TestStepInterface;
use Magento\Sales\Test\Fixture\OrderInjectable;
use Magento\Sales\Test\Page\Adminhtml\OrderCreditMemoNew;
use Magento\Sales\Test\Page\Adminhtml\OrderIndex;
use Magento\Sales\Test\Page\Adminhtml\OrderInvoiceView;
use Magento\Sales\Test\Page\Adminhtml\SalesOrderView;
use Magento\Sales\Test\TestStep\Utils\CompareQtyTrait;

/**
 * Create credit memo for order placed using online payment methods.
 */
class CreateOnlineCreditMemoStep implements TestStepInterface
{
    use CompareQtyTrait;

    /**
     * Orders Page.
     *
     * @var OrderIndex
     */
    private $orderIndex;

    /**
     * Order View Page.
     *
     * @var SalesOrderView
     */
    private $salesOrderView;

    /**
     * OrderCreditMemoNew Page.
     *
     * @var OrderCreditMemoNew
     */
    private $orderCreditMemoNew;

    /**
     * OrderInjectable fixture.
     *
     * @var OrderInjectable
     */
    private $order;

    /**
     * Order invoice view page.
     *
     * @var OrderInvoiceView
     */
    private $orderInvoiceView;

    /**
     * Checkout Cart fixture.
     *
     * @var Cart
     */
    private $cart;

    /**
     * @param Cart $cart
     * @param OrderIndex $orderIndex
     * @param SalesOrderView $salesOrderView
     * @param OrderInjectable $order
     * @param OrderInvoiceView $orderInvoiceView
     * @param OrderCreditMemoNew $orderCreditMemoNew
     */
    public function __construct(
        Cart $cart,
        OrderIndex $orderIndex,
        SalesOrderView $salesOrderView,
        OrderInjectable $order,
        OrderInvoiceView $orderInvoiceView,
        OrderCreditMemoNew $orderCreditMemoNew
    ) {
        $this->cart = $cart;
        $this->orderIndex = $orderIndex;
        $this->salesOrderView = $salesOrderView;
        $this->order = $order;
        $this->orderInvoiceView = $orderInvoiceView;
        $this->orderCreditMemoNew = $orderCreditMemoNew;
    }

    /**
     * Create credit memo.
     *
     * @return array
     */
    public function run()
    {
        $this->orderIndex->open();
        $this->orderIndex->getSalesOrderGrid()->searchAndOpen(['id' => $this->order->getId()]);
        $refundsData = $this->order->getRefund();
        foreach ($refundsData as $refundData) {
            /** @var \Magento\Sales\Test\Block\Adminhtml\Order\View\Tab\Invoices\Grid $invoicesGrid */
            $invoicesGrid = $this->salesOrderView->getOrderForm()->getTab('invoices')->getGridBlock();
            $this->salesOrderView->getOrderForm()->openTab('invoices');
            $invoicesGrid->viewInvoice();
            $this->salesOrderView->getPageActions()->orderInvoiceCreditMemo();

            $items = $this->cart->getItems();
            $this->orderCreditMemoNew->getFormBlock()->fillProductData($refundData, $items);
            if ($this->compare($items, $refundData)) {
                $this->orderCreditMemoNew->getFormBlock()->updateQty();
            }

            $this->orderCreditMemoNew->getFormBlock()->submit();
        }

        return ['ids' => ['creditMemoIds' => $this->getCreditMemoIds()]];
    }

    /**
     * Get credit memo ids.
     *
     * @return array
     */
    private function getCreditMemoIds()
    {
        $this->salesOrderView->getOrderForm()->openTab('creditmemos');
        return $this->salesOrderView->getOrderForm()->getTab('creditmemos')->getGridBlock()->getIds();
    }
}
