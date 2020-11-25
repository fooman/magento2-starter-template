<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/** @var $objectManager \Magento\Framework\ObjectManagerInterface */
$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

/** @var \Magento\Store\Model\StoreManagerInterface $storeManager */
$storeManager = $objectManager->get(\Magento\Store\Model\StoreManagerInterface::class);

/** @var \Magento\Store\Model\Store $store */
$store = $objectManager->create(\Magento\Store\Model\Store::class);
$storeCode = 'secondary';

if (!$store->load($storeCode)->getId()) {
    $store->setCode($storeCode)
        ->setWebsiteId($storeManager->getWebsite()->getId())
        ->setGroupId($storeManager->getWebsite()->getDefaultGroupId())
        ->setName('Secondary Store View')
        ->setSortOrder(10)
        ->setIsActive(1);
    $store->save();

    /** @var \Magento\Framework\App\MutableScopeConfig $scopeConfig */
    $scopeConfig = $objectManager->get(\Magento\Framework\App\MutableScopeConfig::class);
    $scopeConfig->setValue(
        'general/locale/code',
        'de_DE',
        \Magento\Store\Model\ScopeInterface::SCOPE_STORES,
        $store->getId()
    );
}

/** @var $productFirst \Magento\Catalog\Model\Product */
$productFirst = $objectManager->create(\Magento\Catalog\Model\Product::class);
$productFirst->setTypeId('simple')
    ->setAttributeSetId(4)
    ->setWebsiteIds([1])
    ->setName('Simple Product Apple')
    ->setSku('fulltext-1')
    ->setPrice(10)
    ->setMetaTitle('first meta title')
    ->setMetaKeyword('first meta keyword')
    ->setMetaDescription('first meta description')
    ->setVisibility(\Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
    ->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
    ->setStockData(['use_config_manage_stock' => 0])
    ->save();

/** @var $productSecond \Magento\Catalog\Model\Product */
$productSecond = $objectManager->create(\Magento\Catalog\Model\Product::class);
$productSecond->setTypeId('simple')
    ->setAttributeSetId(4)
    ->setWebsiteIds([1])
    ->setName('Simple Product Banana')
    ->setSku('fulltext-2')
    ->setPrice(20)
    ->setMetaTitle('second meta title')
    ->setMetaKeyword('second meta keyword')
    ->setMetaDescription('second meta description')
    ->setVisibility(\Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
    ->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
    ->setStockData(['use_config_manage_stock' => 0])
    ->save();

/** @var $productThird \Magento\Catalog\Model\Product */
$productThird = $objectManager->create(\Magento\Catalog\Model\Product::class);
$productThird->setTypeId('simple')
    ->setAttributeSetId(4)
    ->setWebsiteIds([1])
    ->setName('Simple Product Orange')
    ->setSku('fulltext-3')
    ->setPrice(20)
    ->setMetaTitle('third meta title')
    ->setMetaKeyword('third meta keyword')
    ->setMetaDescription('third meta description')
    ->setVisibility(\Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
    ->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
    ->setStockData(['use_config_manage_stock' => 0])
    ->save();

/** @var $productFourth \Magento\Catalog\Model\Product */
$productFourth = $objectManager->create(\Magento\Catalog\Model\Product::class);
$productFourth->setTypeId('simple')
    ->setAttributeSetId(4)
    ->setWebsiteIds([1])
    ->setName('Simple Product Papaya')
    ->setSku('fulltext-4')
    ->setPrice(20)
    ->setMetaTitle('fourth meta title')
    ->setMetaKeyword('fourth meta keyword')
    ->setMetaDescription('fourth meta description')
    ->setVisibility(\Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
    ->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
    ->setStockData(['use_config_manage_stock' => 0])
    ->save();

/** @var $productFifth \Magento\Catalog\Model\Product */
$productFifth = $objectManager->create(\Magento\Catalog\Model\Product::class);
$productFifth->setTypeId('simple')
    ->setAttributeSetId(4)
    ->setWebsiteIds([1])
    ->setName('Simple Product Cherry')
    ->setSku('fulltext-5')
    ->setPrice(20)
    ->setMetaTitle('fifth meta title')
    ->setMetaKeyword('fifth meta keyword')
    ->setMetaDescription('fifth meta description')
    ->setVisibility(\Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
    ->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
    ->setStockData(['use_config_manage_stock' => 0])
    ->save();
