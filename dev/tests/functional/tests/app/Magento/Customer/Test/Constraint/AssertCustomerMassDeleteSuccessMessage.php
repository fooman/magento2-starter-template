<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Customer\Test\Constraint;

use Magento\Customer\Test\Page\Adminhtml\CustomerIndex;
use Magento\Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertCustomerMassDeleteSuccessMessage
 * Check that message "A total of "x" record(s) were deleted." is present
 */
class AssertCustomerMassDeleteSuccessMessage extends AbstractConstraint
{
    /**
     * Message that appears after deletion via mass actions
     */
    const SUCCESS_DELETE_MESSAGE = 'A total of %d record(s) were deleted.';

    /**
     * Assert that message "A total of "x" record(s) were deleted."
     *
     * @param $customersQtyToDelete
     * @param CustomerIndex $customerIndexPage
     * @return void
     */
    public function processAssert($customersQtyToDelete, CustomerIndex $customerIndexPage)
    {
        \PHPUnit\Framework\Assert::assertEquals(
            sprintf(self::SUCCESS_DELETE_MESSAGE, $customersQtyToDelete),
            $customerIndexPage->getMessagesBlock()->getSuccessMessage(),
            'Wrong delete message is displayed.'
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Mass delete customer message is displayed.';
    }
}
