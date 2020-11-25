<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Customer\Block\Address;

/**
 * Tests Address Edit Block
 */
class EditTest extends \PHPUnit\Framework\TestCase
{
    /** @var Edit */
    protected $_block;

    /** @var  \Magento\Customer\Model\Session */
    protected $_customerSession;

    /** @var \Magento\Backend\Block\Template\Context */
    protected $_context;

    /** @var string */
    protected $_requestId;

    protected function setUp(): void
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

        $this->_customerSession = $objectManager->get(\Magento\Customer\Model\Session::class);
        $this->_customerSession->setCustomerId(1);

        $this->_context = $objectManager->get(\Magento\Backend\Block\Template\Context::class);
        $this->_requestId = $this->_context->getRequest()->getParam('id');
        $this->_context->getRequest()->setParam('id', '1');

        $objectManager->get(\Magento\Framework\App\State::class)->setAreaCode('frontend');

        /** @var $layout \Magento\Framework\View\Layout */
        $layout = $objectManager->get(\Magento\Framework\View\LayoutInterface::class);
        $currentCustomer = $objectManager->create(
            \Magento\Customer\Helper\Session\CurrentCustomer::class,
            ['customerSession' => $this->_customerSession]
        );
        $this->_block = $layout->createBlock(
            \Magento\Customer\Block\Address\Edit::class,
            '',
            ['customerSession' => $this->_customerSession, 'currentCustomer' => $currentCustomer]
        );
    }

    protected function tearDown(): void
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->_customerSession->setCustomerId(null);
        $this->_context->getRequest()->setParam('id', $this->_requestId);
        /** @var \Magento\Customer\Model\AddressRegistry $addressRegistry */
        $addressRegistry = $objectManager->get(\Magento\Customer\Model\AddressRegistry::class);
        //Cleanup address from registry
        $addressRegistry->remove(1);
        $addressRegistry->remove(2);

        /** @var \Magento\Customer\Model\CustomerRegistry $customerRegistry */
        $customerRegistry = $objectManager->get(\Magento\Customer\Model\CustomerRegistry::class);
        //Cleanup customer from registry
        $customerRegistry->remove(1);
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testGetSaveUrl()
    {
        $this->assertEquals('http://localhost/index.php/customer/address/formPost/', $this->_block->getSaveUrl());
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Customer/_files/customer_address.php
     */
    public function testGetRegionId()
    {
        $this->assertEquals(1, $this->_block->getRegionId());
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Customer/_files/customer_address.php
     */
    public function testGetCountryId()
    {
        $this->assertEquals('US', $this->_block->getCountryId());
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Customer/_files/customer_two_addresses.php
     */
    public function testGetCustomerAddressCount()
    {
        $this->assertEquals(2, $this->_block->getCustomerAddressCount());
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testCanSetAsDefaultShipping()
    {
        $this->assertEquals(0, $this->_block->canSetAsDefaultShipping());
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testIsDefaultBilling()
    {
        $this->assertFalse($this->_block->isDefaultBilling());
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Customer/_files/customer_address.php
     */
    public function testGetStreetLine()
    {
        $this->assertEquals('Green str, 67', $this->_block->getStreetLine(1));
        $this->assertEquals('', $this->_block->getStreetLine(2));
    }
}
