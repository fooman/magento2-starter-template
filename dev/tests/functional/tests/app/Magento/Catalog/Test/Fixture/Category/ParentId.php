<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Catalog\Test\Fixture\Category;

use Magento\Catalog\Test\Fixture\Category;
use Magento\Mtf\Fixture\DataSource;
use Magento\Mtf\Fixture\FixtureFactory;

/**
 * Prepare parent category.
 */
class ParentId extends DataSource
{
    /**
     * Return category.
     *
     * @var Category
     */
    protected $parentCategory = null;

    /**
     * @constructor
     * @param FixtureFactory $fixtureFactory
     * @param array $params
     * @param array|int $data
     */
    public function __construct(FixtureFactory $fixtureFactory, array $params, $data = [])
    {
        $this->params = $params;
        if (isset($data['dataset']) && $data['dataset'] !== '-') {
            $this->parentCategory = $fixtureFactory->createByCode('category', ['dataset' => $data['dataset']]);
            if (!$this->parentCategory->hasData('id')) {
                $this->parentCategory->persist();
            }
            $this->data = $this->parentCategory->getId();
        } elseif (isset($data['source']) && $data['source'] instanceof Category) {
            $this->parentCategory = $data['source'];
            $this->data = $data['source']->getId();
        } else {
            $this->data = $data;
        }
    }

    /**
     * Return entity.
     *
     * @return Category
     */
    public function getParentCategory()
    {
        return $this->parentCategory;
    }
}
