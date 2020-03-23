<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Customer\Test\Constraint;

use Magento\CatalogRule\Test\Page\Adminhtml\CatalogRuleIndex;
use Magento\CatalogRule\Test\Page\Adminhtml\CatalogRuleNew;
use Magento\Customer\Test\Fixture\CustomerGroup;
use Magento\Mtf\Constraint\AbstractConstraint;
use Magento\CatalogRule\Test\Block\Adminhtml\Promo\Catalog\Edit\Section\RuleInformation;

/**
 * Assert that customer group find on catalog price rule page.
 */
class AssertCustomerGroupOnCatalogPriceRuleForm extends AbstractConstraint
{
    /**
     * Assert that customer group find on catalog price rule page.
     *
     * @param CatalogRuleIndex $catalogRuleIndex
     * @param CatalogRuleNew $catalogRuleNew
     * @param CustomerGroup $customerGroup
     * @return void
     */
    public function processAssert(
        CatalogRuleIndex $catalogRuleIndex,
        CatalogRuleNew $catalogRuleNew,
        CustomerGroup $customerGroup
    ) {
        $catalogRuleIndex->open();
        $catalogRuleIndex->getGridPageActions()->addNew();
        $catalogRuleNew->getEditForm()->openSection('rule_information');

        /** @var RuleInformation $ruleInformationSection */
        $ruleInformationSection = $catalogRuleNew->getEditForm()->getSection('rule_information');
        \PHPUnit\Framework\Assert::assertTrue(
            $ruleInformationSection->isVisibleCustomerGroup($customerGroup),
            "Customer group {$customerGroup->getCustomerGroupCode()} not in catalog price rule page."
        );
    }

    /**
     * Success assert of customer group find on catalog price rule page.
     *
     * @return string
     */
    public function toString()
    {
        return 'Customer group find on catalog price rule page.';
    }
}
