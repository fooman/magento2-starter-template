<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Catalog\Test\Constraint;

use Magento\Catalog\Test\Fixture\CatalogAttributeSet;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductSetIndex;
use Magento\Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertAttributeSetNotInGrid
 * Assert that Attribute Set absence on grid
 */
class AssertAttributeSetNotInGrid extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Assert that attribute set is not displayed in Attribute Sets grid
     *
     * @param CatalogProductSetIndex $productSetPage
     * @param CatalogAttributeSet $attributeSet
     * @return void
     */
    public function processAssert(CatalogProductSetIndex $productSetPage, CatalogAttributeSet $attributeSet)
    {
        $filterAttributeSet = [
            'set_name' => $attributeSet->getAttributeSetName(),
        ];

        $productSetPage->open();
        \PHPUnit\Framework\Assert::assertFalse(
            $productSetPage->getGrid()->isRowVisible($filterAttributeSet),
            'A "' . $filterAttributeSet['set_name'] .
            '" attribute set name already exists. Create a new name and try again.'
        );
    }

    /**
     * Text absent new attribute set in grid
     *
     * @return string
     */
    public function toString()
    {
        return 'Attribute set is absent in Attribute Sets grid';
    }
}
