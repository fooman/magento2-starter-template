<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Catalog\Test\Constraint;

use Magento\Catalog\Test\Fixture\CatalogProductAttribute;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductEdit;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductIndex;
use Magento\Mtf\Constraint\AbstractConstraint;
use Magento\Mtf\Fixture\InjectableFixture;

/**
 * Check whether the attribute is mandatory.
 */
class AssertProductAttributeIsRequired extends AbstractConstraint
{
    /**
     * Expected message.
     */
    const REQUIRE_MESSAGE = 'This is a required field.';

    /**
     * Check whether the attribute is mandatory.
     *
     * @param CatalogProductIndex $catalogProductIndex
     * @param CatalogProductEdit $catalogProductEdit
     * @param CatalogProductAttribute $attribute
     * @param InjectableFixture $product
     * @param string $sectionName
     * @return void
     */
    public function processAssert(
        CatalogProductIndex $catalogProductIndex,
        CatalogProductEdit $catalogProductEdit,
        CatalogProductAttribute $attribute,
        InjectableFixture $product,
        $sectionName
    ) {
        $catalogProductIndex->open()->getProductGrid()->searchAndOpen(['sku' => $product->getSku()]);
        $productForm = $catalogProductEdit->getProductForm();
        if (!$productForm->checkAttributeLabel($attribute)) {
            $productForm->openSection($sectionName);
        }
        $productForm->getAttributeElement($attribute)->setValue('');
        $catalogProductEdit->getFormPageActions()->save();
        $validationErrors = $productForm->getSection($sectionName)->getValidationErrors();
        $actualMessage = isset($validationErrors[$attribute->getFrontendLabel()])
            ? $validationErrors[$attribute->getFrontendLabel()]
            : '';

        \PHPUnit\Framework\Assert::assertEquals(
            self::REQUIRE_MESSAGE,
            $actualMessage,
            'JS error notice on product edit page is not equal to expected.'
        );
    }

    /**
     * Return string representation of object.
     *
     * @return string
     */
    public function toString()
    {
        return '"This is a required field" notice is visible on product edit page after trying to save product with '
        . 'blank required field.';
    }
}
