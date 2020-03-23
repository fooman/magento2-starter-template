<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Store\Test\Constraint;

use Magento\Backend\Test\Page\Adminhtml\NewGroupIndex;
use Magento\Mtf\Constraint\AbstractConstraint;

/**
 * Assert that 'Delete' button on StoreGroup view edit page is absent.
 */
class AssertStoreGroupNoDeleteButton extends AbstractConstraint
{
    /**
     * Assert that 'Delete' button on StoreGroup view edit page is absent.
     *
     * @param NewGroupIndex $newGroupIndex
     * @return void
     */
    public function processAssert(NewGroupIndex $newGroupIndex)
    {
        \PHPUnit\Framework\Assert::assertFalse(
            $newGroupIndex->getFormPageActions()->checkDeleteButton(),
            '\'Delete\' button on StoreGroup view edit page is present when it should not.'
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return '\'Delete\' button on StoreGroup view edit page is absent.';
    }
}
