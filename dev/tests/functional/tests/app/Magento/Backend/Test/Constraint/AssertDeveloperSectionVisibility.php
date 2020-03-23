<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Backend\Test\Constraint;

use Magento\Mtf\Constraint\AbstractConstraint;
use Magento\Backend\Test\Page\Adminhtml\SystemConfigEdit;

/**
 * Assert that Developer section is not present in production mode.
 */
class AssertDeveloperSectionVisibility extends AbstractConstraint
{
    /**
     * Assert Developer section is not present in production mode.
     *
     * @param SystemConfigEdit $configEdit
     * @return void
     */
    public function processAssert(SystemConfigEdit $configEdit)
    {
        if ($_ENV['mage_mode'] === 'production') {
            \PHPUnit\Framework\Assert::assertFalse(
                in_array('Developer', $configEdit->getTabs()->getSubTabsNames('Advanced')),
                'Developer section should be hidden in production mode.'
            );
        } else {
            \PHPUnit\Framework\Assert::assertTrue(
                in_array('Developer', $configEdit->getTabs()->getSubTabsNames('Advanced')),
                'Developer section should be not hidden in developer or default mode.'
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
        return 'Developer section has correct visibility.';
    }
}
