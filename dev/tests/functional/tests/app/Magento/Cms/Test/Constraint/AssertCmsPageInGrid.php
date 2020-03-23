<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Cms\Test\Constraint;

use Magento\Cms\Test\Fixture\CmsPage;
use Magento\Cms\Test\Page\Adminhtml\CmsPageIndex;
use Magento\Mtf\Constraint\AbstractConstraint;

/**
 * Assert that CMS page present in grid and can be found by title.
 */
class AssertCmsPageInGrid extends AbstractConstraint
{
    /**
     * Assert that cms page is present in pages grid.
     *
     * @param CmsPageIndex $cmsIndex
     * @param CmsPage $cms
     * @param string $expectedStatus [optional]
     * @return void
     */
    public function processAssert(CmsPageIndex $cmsIndex, CmsPage $cms, $expectedStatus = '')
    {
        $filter = [
            'title' => $cms->getTitle(),
            'is_active' => $expectedStatus
        ];
        $cmsIndex->open();
        \PHPUnit\Framework\Assert::assertTrue(
            $cmsIndex->getCmsPageGridBlock()->isRowVisible($filter, true, false),
            'Cms page \'' . $cms->getTitle() . '\' is not present in pages grid.'
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Cms page is present in pages grid.';
    }
}
