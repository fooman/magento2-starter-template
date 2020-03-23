<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Reports\Test\Constraint;

use Magento\Reports\Test\Page\Adminhtml\Statistics;
use Magento\Mtf\Constraint\AbstractConstraint;

/**
 * Assert that correct success message is displayed after refreshing recent reports statistics.
 */
class AssertRecentStatisticsUpdatedMessage extends AbstractConstraint
{
    /**
     * Recent statistics updated message.
     */
    const RECENT_STATISTICS_UPDATED_MESSAGE = 'Recent statistics have been updated.';

    /**
     * Assert that correct success message is displayed after refreshing recent reports statistics.
     *
     * @param Statistics $reportStatistics
     * @return void
     */
    public function processAssert(Statistics $reportStatistics)
    {
        $successMessage = $reportStatistics->getMessagesBlock()->getSuccessMessage();
        \PHPUnit\Framework\Assert::assertEquals(
            self::RECENT_STATISTICS_UPDATED_MESSAGE,
            $successMessage,
            'Wrong success message is displayed.'
            . "\nExpected: " . self::RECENT_STATISTICS_UPDATED_MESSAGE
            . "\nActual: " . $successMessage
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return "Correct message is displayed after refreshing recent statistics.";
    }
}
