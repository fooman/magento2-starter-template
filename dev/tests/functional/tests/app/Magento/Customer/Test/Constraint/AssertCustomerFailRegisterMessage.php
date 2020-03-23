<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Customer\Test\Constraint;

use Magento\Customer\Test\Page\CustomerAccountCreate;
use Magento\Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertCustomerFailRegisterMessage
 */
class AssertCustomerFailRegisterMessage extends AbstractConstraint
{
    /**
     * Assert that error message is displayed on "Create New Customer Account" page(frontend)
     *
     * @param CustomerAccountCreate $registerPage
     * @return void
     */
    public function processAssert(CustomerAccountCreate $registerPage)
    {
        $errorMessage = $registerPage->getMessagesBlock()->getErrorMessage();
        \PHPUnit\Framework\Assert::assertNotEmpty(
            $errorMessage,
            'No error message is displayed.'
        );
    }

    /**
     * Text error message is displayed
     *
     * @return string
     */
    public function toString()
    {
        return 'Assert that error message is displayed.';
    }
}
