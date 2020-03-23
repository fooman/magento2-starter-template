<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Cms\Test\Constraint;

use Magento\Catalog\Test\Page\Category\CatalogCategoryView;
use Magento\Cms\Test\Fixture\CmsBlock;
use Magento\Cms\Test\Page\CmsIndex;
use Magento\Mtf\Constraint\AbstractConstraint;
use Magento\Mtf\Fixture\FixtureFactory;
use Magento\Catalog\Test\Fixture\Category;

/**
 * Assert that created CMS block non visible on frontend category page.
 */
class AssertCmsBlockNotOnCategoryPage extends AbstractConstraint
{
    /**
     * Assert that created CMS block non visible on frontend category page
     * (in order to assign block to category: go to category page> Display settings> CMS Block)
     *
     * @param CmsIndex $cmsIndex
     * @param CmsBlock $cmsBlock
     * @param CatalogCategoryView $catalogCategoryView
     * @param FixtureFactory $fixtureFactory
     * @param Category|null $category [optional]
     * @return void
     */
    public function processAssert(
        CmsIndex $cmsIndex,
        CmsBlock $cmsBlock,
        CatalogCategoryView $catalogCategoryView,
        FixtureFactory $fixtureFactory,
        Category $category = null
    ) {
        if ($category === null) {
            $category = $fixtureFactory->createByCode(
                'category',
                [
                    'dataset' => 'default_subcategory',
                    'data' => [
                        'display_mode' => 'Static block and products',
                        'landing_page' => $cmsBlock->getTitle(),
                    ]
                ]
            );
            $category->persist();
        }

        $cmsIndex->open();
        $cmsIndex->getTopmenu()->selectCategoryByName($category->getName());
        $categoryViewContent = $catalogCategoryView->getViewBlock()->getContent();

        \PHPUnit\Framework\Assert::assertNotEquals(
            $cmsBlock->getContent(),
            $categoryViewContent,
            'Wrong block content on category is displayed.'
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'CMS block description is absent on Category page (frontend).';
    }
}
