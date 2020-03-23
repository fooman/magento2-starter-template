<?php
/**
 * \Magento\Customer\Model\ResourceModel\Group\Grid\ServiceCollection
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Customer\Model\ResourceModel\Group\Grid;

class ServiceCollectionTest extends \PHPUnit\Framework\TestCase
{
    /** @var ServiceCollection */
    protected $collection;

    public function setUp()
    {
        $this->collection = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            \Magento\Customer\Model\ResourceModel\Group\Grid\ServiceCollection::class
        );
    }

    public function testSetOrder()
    {
        $this->collection->setOrder('code', \Magento\Framework\Data\Collection::SORT_ORDER_ASC);
        $this->collection->loadData();
        $items = $this->collection->getItems();
        $this->assertEquals(4, count($items));

        $this->assertEquals('General', $items[0]->getCode());
        $this->assertEquals('1', $items[0]->getId());
        $this->assertEquals('3', $items[0]->getTaxClassId());

        $this->assertEquals('NOT LOGGED IN', $items[1]->getCode());
        $this->assertEquals('0', $items[1]->getId());
        $this->assertEquals('3', $items[1]->getTaxClassId());

        $this->assertEquals('Retailer', $items[2]->getCode());
        $this->assertEquals('3', $items[2]->getId());
        $this->assertEquals('3', $items[2]->getTaxClassId());

        $this->assertEquals('Wholesale', $items[3]->getCode());
        $this->assertEquals('2', $items[3]->getId());
        $this->assertEquals('3', $items[3]->getTaxClassId());
    }

    public function testArrayFilter()
    {
        $this->collection->addFieldToFilter(['code'], [['NOT LOGGED IN']]);
        $this->collection->loadData();
        $items = $this->collection->getItems();
        $this->assertEquals(1, count($items));

        $this->assertEquals('NOT LOGGED IN', $items[0]->getCode());
        $this->assertEquals('0', $items[0]->getId());
        $this->assertEquals('3', $items[0]->getTaxClassId());
    }

    public function testOrArrayFilter()
    {
        $this->collection->addFieldToFilter(['code', 'code'], ['General', 'Retailer']);
        $this->collection->loadData();
        $items = $this->collection->getItems();
        $this->assertEquals(2, count($items));

        $this->assertEquals('General', $items[0]->getCode());
        $this->assertEquals('1', $items[0]->getId());
        $this->assertEquals('3', $items[0]->getTaxClassId());

        $this->assertEquals('Retailer', $items[1]->getCode());
        $this->assertEquals('3', $items[1]->getId());
        $this->assertEquals('3', $items[1]->getTaxClassId());
    }

    public function testSingleFilter()
    {
        $this->collection->addFieldToFilter('code', 'NOT LOGGED IN');
        $this->collection->loadData();
        $items = $this->collection->getItems();
        $this->assertEquals(1, count($items));

        $this->assertEquals('NOT LOGGED IN', $items[0]->getCode());
        $this->assertEquals('0', $items[0]->getId());
        $this->assertEquals('3', $items[0]->getTaxClassId());
    }

    public function testSingleLikeFilter()
    {
        $this->collection->addFieldToFilter('code', ['like' => 'NOT%']);
        $this->collection->loadData();
        $items = $this->collection->getItems();
        $this->assertEquals(1, count($items));

        $this->assertEquals('NOT LOGGED IN', $items[0]->getCode());
        $this->assertEquals('0', $items[0]->getId());
        $this->assertEquals('3', $items[0]->getTaxClassId());
    }

    /**
     * @expectedException \Magento\Framework\Exception\LocalizedException
     * @expectedExceptionMessage The array of fields failed to pass. The array must include at one field.
     */
    public function testAddToFilterException()
    {
        $this->collection->addFieldToFilter([], 'not_array');
    }

    /**
     * @expectedException \Magento\Framework\Exception\LocalizedException
     * @expectedExceptionMessage The field array failed to pass. The array must have a matching condition array.
     */
    public function testAddToFilterExceptionArrayNotSymmetric()
    {
        $this->collection->addFieldToFilter(['field2', 'field2'], ['condition1']);
    }
}
