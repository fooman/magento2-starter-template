<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CatalogRuleConfigurable\Test\Constraint;

use Magento\Cms\Test\Page\CmsIndex;
use Magento\Customer\Test\Fixture\Customer;
use Magento\Mtf\Constraint\AbstractConstraint;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\Catalog\Test\Page\Category\CatalogCategoryView;

/**
 * Assert that Catalog Price Rule is applied for configurable product(s) in Catalog.
 */
class AssertCatalogPriceRuleAppliedCatalogPage extends AbstractConstraint
{
    /**
     * Assert that Catalog Price Rule is applied for configurable product(s) in Catalog
     * according to Priority(Priority/Stop Further Rules Processing).
     *
     * @param CmsIndex $cmsIndexPage
     * @param CatalogCategoryView $catalogCategoryViewPage
     * @param array $products
     * @param array $productPrice
     * @param Customer $customer
     * @return void
     */
    public function processAssert(
        CmsIndex $cmsIndexPage,
        CatalogCategoryView $catalogCategoryViewPage,
        array $products,
        array $productPrice,
        Customer $customer = null
    ) {
        if ($customer !== null) {
            $this->objectManager->create(
                \Magento\Customer\Test\TestStep\LoginCustomerOnFrontendStep::class,
                ['customer' => $customer]
            )->run();
        } else {
            $this->objectManager->create(\Magento\Customer\Test\TestStep\LogoutCustomerOnFrontendStep::class)->run();
        }

        $cmsIndexPage->open();
        foreach ($products as $key => $product) {
            $categoryName = $product->getCategoryIds()[0];
            $cmsIndexPage->getTopmenu()->selectCategoryByName($categoryName);
            $priceBlock = $catalogCategoryViewPage->getListProductBlock()->getProductItem($product)->getPriceBlock();
            \PHPUnit\Framework\Assert::assertTrue(
                $priceBlock->isVisible(),
                'Price block is not displayed for product ' . $product->getName()
            );
            // Product price with applied rule displayed as usual price for Configurable products (MAGETWO-64882)
            $actualPrice['special'] = (float)$priceBlock->getPrice();
            $diff = $this->verifyData($actualPrice, $productPrice[$key]);
            \PHPUnit\Framework\Assert::assertTrue(
                empty($diff),
                implode(' ', $diff)
            );
        }
    }

    /**
     * Check if arrays have equal values.
     *
     * @param array $formData
     * @param array $fixtureData
     * @return array
     */
    protected function verifyData(array $formData, array $fixtureData)
    {
        $errorMessage = [];
        foreach ($formData as $key => $value) {
            if ($value != $fixtureData[$key]) {
                $errorMessage[] = "Value " . $key . " is not equal."
                    . "\nExpected: " . $fixtureData[$key]
                    . "\nActual: " . $value . "\n";
            }
        }
        return $errorMessage;
    }

    /**
     * Text of catalog price rule visibility on catalog page (frontend).
     *
     * @return string
     */
    public function toString()
    {
        return 'Displayed catalog price rule data on catalog page(frontend) equals to passed from fixture.';
    }
}
