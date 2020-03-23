<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\CatalogSearch\Test\Constraint;

use Magento\CatalogSearch\Test\Fixture\CatalogSearchQuery;
use Magento\Cms\Test\Page\CmsIndex;
use Magento\Mtf\Constraint\AbstractConstraint;
use Magento\CatalogSearch\Test\Page\AdvancedResult;

/**
 * Class AssertSuggestSearchingResult
 */
class AssertSuggestSearchingResult extends AbstractConstraint
{
    /**
     * Check that after input some text(e.g. product name) into search field, drop-down window is appeared.
     * Window contains requested entity and number of quantity.
     * Click on search suggestion and verify that search is performed.
     *
     * @param CmsIndex $cmsIndex
     * @param CatalogSearchQuery $searchTerm
     * @param AssertCatalogSearchResult $assertCatalogSearchResult
     * @param AdvancedResult $resultPage
     * @return void
     */
    public function processAssert(
        CatalogSearchQuery $searchTerm,
        CmsIndex $cmsIndex,
        AssertCatalogSearchResult $assertCatalogSearchResult,
        AdvancedResult $resultPage
    ) {
        $cmsIndex->open();
        $searchBlock = $cmsIndex->getSearchBlock();

        $queryText = $searchTerm->getQueryText();
        $searchBlock->fillSearch($queryText);

        if ($searchTerm->hasData('num_results')) {
            $isVisible = $searchBlock->isSuggestSearchVisible($queryText, $searchTerm->getNumResults());
        } else {
            $isVisible = $searchBlock->isSuggestSearchVisible($queryText);
        }

        \PHPUnit\Framework\Assert::assertTrue(
            $isVisible,
            'Block "Suggest Search" when searching was not found'
        );
        $searchBlock->clickSuggestedText($queryText);
        $assertCatalogSearchResult->processAssert($searchTerm, $resultPage);
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Asserts window contains requested entity and quantity. Searched product has been successfully found.';
    }
}
