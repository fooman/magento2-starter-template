<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Reports\Test\Constraint;

use Magento\Reports\Test\Page\Adminhtml\ProductReportView;
use Magento\Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertProductViewsReportTotalResult
 * Assert product info in report: product name, price and views
 */
class AssertProductViewsReportTotalResult extends AbstractConstraint
{
    /**
     * Assert product info in report: product name, price and views
     *
     * @param ProductReportView $productReportView
     * @param string $total
     * @param array $productsList
     * @return void
     */
    public function processAssert(ProductReportView $productReportView, $total, array $productsList)
    {
        $total = explode(', ', $total);
        $totalForm = $productReportView->getGridBlock()->getViewsResults($productsList);
        \PHPUnit\Framework\Assert::assertEquals($totalForm, $total);
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Products view total result is equals to data from dataset.';
    }
}
