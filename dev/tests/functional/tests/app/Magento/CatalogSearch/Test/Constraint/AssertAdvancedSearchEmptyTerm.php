<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\CatalogSearch\Test\Constraint;

use Magento\CatalogSearch\Test\Page\AdvancedSearch;
use Magento\Mtf\Constraint\AbstractConstraint;

/**
 * Assert that error message is displayed after searching without entering any search terms.
 */
class AssertAdvancedSearchEmptyTerm extends AbstractConstraint
{
    /**
     * Specify search term error message.
     */
    const ERROR_MESSAGE = 'Enter a search term and try again.';

    /**
     * Assert that error message is displayed after searching without entering any search terms.
     *
     * @param AdvancedSearch $advancedSearch
     * @return void
     */
    public function processAssert(AdvancedSearch $advancedSearch)
    {
        $actualMessage = $advancedSearch->getMessagesBlock()->getErrorMessage();
        \PHPUnit\Framework\Assert::assertEquals(
            self::ERROR_MESSAGE,
            $actualMessage,
            'Wrong error message is displayed.'
            . "\nExpected: " . self::ERROR_MESSAGE
            . "\nActual: " . $actualMessage
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Correct specify search term error message is displayed.';
    }
}
