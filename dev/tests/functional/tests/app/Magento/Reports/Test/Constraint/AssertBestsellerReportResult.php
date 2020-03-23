<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Reports\Test\Constraint;

use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\Reports\Test\Page\Adminhtml\Bestsellers;
use Magento\Sales\Test\Fixture\OrderInjectable;
use Magento\Mtf\Constraint\AbstractConstraint;

/**
 * Assert bestseller info in report: date, product name and qty.
 */
class AssertBestsellerReportResult extends AbstractConstraint
{
    /**
     * Assert bestseller info in report: date, product name and qty.
     *
     * @param Bestsellers $bestsellers
     * @param OrderInjectable $order
     * @param string $date
     * @return void
     */
    public function processAssert(Bestsellers $bestsellers, OrderInjectable $order, $date)
    {
        /** @var CatalogProductSimple $product */
        $product = $order->getEntityId()['products'][0];

        $filter = [
            'date' => date($date),
            'product' => $product->getName(),
            'price' => $product->getPrice(),
            'orders' => $product->getCheckoutData()['qty'],
        ];

        \PHPUnit\Framework\Assert::assertTrue(
            $bestsellers->getGridBlock()->isRowVisible($filter, false),
            'Bestseller does not present in report grid.'
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Bestseller total result is equals to data from dataset.';
    }
}
