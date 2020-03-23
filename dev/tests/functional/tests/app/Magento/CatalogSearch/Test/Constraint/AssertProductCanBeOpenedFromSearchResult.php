<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\CatalogSearch\Test\Constraint;

use Magento\Mtf\Constraint\AbstractConstraint;
use Magento\CatalogSearch\Test\Page\AdvancedResult;
use Magento\Catalog\Test\Page\Product\CatalogProductView;
use Magento\CatalogSearch\Test\Fixture\CatalogSearchQuery;

/**
 * Assert product can be opened from search results page.
 */
class AssertProductCanBeOpenedFromSearchResult extends AbstractConstraint
{
    /**
     * Assert product can be opened from search results page.
     *
     * @param CatalogSearchQuery $catalogSearch
     * @param AdvancedResult $resultPage
     * @param CatalogProductView $catalogProductViewPage
     * @return void
     */
    public function processAssert(
        CatalogSearchQuery $catalogSearch,
        AdvancedResult $resultPage,
        CatalogProductView $catalogProductViewPage
    ) {
        $product = $catalogSearch->getDataFieldConfig('query_text')['source']->getFirstProduct();

        do {
            $isProductVisible = $resultPage->getListProductBlock()->getProductItem($product)->isVisible();
        } while (!$isProductVisible && $resultPage->getBottomToolbar()->nextPage());

        $productName = $product->getName();
        \PHPUnit\Framework\Assert::assertTrue($isProductVisible, "A product with name $productName was not found.");

        $resultPage->getListProductBlock()->getProductItem($product)->open();
        \PHPUnit\Framework\Assert::assertEquals(
            $productName,
            $catalogProductViewPage->getViewBlock()->getProductName(),
            'Wrong product page has been opened.'
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Product can be opened from search results page.';
    }
}
