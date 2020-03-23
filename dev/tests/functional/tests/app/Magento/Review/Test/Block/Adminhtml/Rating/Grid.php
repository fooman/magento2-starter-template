<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Review\Test\Block\Adminhtml\Rating;

use Magento\Backend\Test\Block\Widget\Grid as AbstractGrid;

/**
 * Class RatingGrid
 * Backend product rating grid
 */
class Grid extends AbstractGrid
{
    /**
     * Locator value for rating code column
     *
     * @var string
     */
    protected $editLink = 'td[data-column="rating_code"]';

    /**
     * Filters array mapping
     *
     * @var array
     */
    protected $filters = [
        'rating_code' => [
            'selector' => '[name="rating_code"]',
        ],
        'is_active' => [
            'selector' => '[name="is_active"]',
            'input' => 'select',
        ],
    ];
}
