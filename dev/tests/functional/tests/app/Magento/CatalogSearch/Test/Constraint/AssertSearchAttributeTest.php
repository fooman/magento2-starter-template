<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\CatalogSearch\Test\Constraint;

use Magento\CatalogSearch\Test\Page\AdvancedSearch;
use Magento\Mtf\Constraint\AbstractConstraint;

/**
 * Assert advanced attribute is present(or absent) in Advanced Search Page.
 */
class AssertSearchAttributeTest extends AbstractConstraint
{
    /**
     * Assert advanced attribute is present(or absent) in Advanced Search Page.
     *
     * @param AdvancedSearch $advancedSearch
     * @param array $attributeForSearch
     * @return void
     */
    public function processAssert(
        AdvancedSearch $advancedSearch,
        array $attributeForSearch
    ) {
        $advancedSearch->open();
        $availableAttributes = $advancedSearch->getForm()->getFormLabels();
        if (isset($attributeForSearch['isVisible'])) {
            \PHPUnit\Framework\Assert::assertTrue(
                (false !== array_search($attributeForSearch['name'], $availableAttributes)),
                'Attribute ' . $attributeForSearch['name'] . 'was not found in Advanced Search Page.'
            );
        } else {
            \PHPUnit\Framework\Assert::assertTrue(
                (false == array_search($attributeForSearch['name'], $availableAttributes)),
                'Attribute ' . $attributeForSearch['name'] . ' was found in Advanced Search Page.'
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
        return 'Attribute was found in Advanced Search Page.';
    }
}
