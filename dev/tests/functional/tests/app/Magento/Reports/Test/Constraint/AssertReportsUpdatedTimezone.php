<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Reports\Test\Constraint;

use Magento\Reports\Test\Page\Adminhtml\Statistics;
use Magento\Mtf\Constraint\AbstractConstraint;

/**
 * Assert that reports 'Updated' values are displayed in date/time in Default Config timezone.
 */
class AssertReportsUpdatedTimezone extends AbstractConstraint
{
    /**
     * Assert that reports 'Updated' values are displayed in date/time in Default Config timezone.
     *
     * @param Statistics $reportStatistics
     * @return void
     */
    public function processAssert(Statistics $reportStatistics)
    {
        $reportStatistics->open();
        $dates = $reportStatistics->getGridBlock()->getRowsData(['updated_at']);
        $currentDate  = new \DateTime();
        $currentDate->setTimezone(new \DateTimeZone($_ENV['magento_timezone']));
        foreach ($dates as $date) {
            \PHPUnit\Framework\Assert::assertContains(
                $currentDate->format('M j, Y, g'),
                date('M j, Y, g', strtotime($date['updated_at'])),
                "Reports 'Updated' column values are displayed in an incorrect timezone."
            );
        }
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return "Reports 'Updated' column values are displayed in the correct timezone.";
    }
}
