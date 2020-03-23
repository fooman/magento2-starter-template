<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Customer\Test\Constraint;

use Magento\Customer\Test\Fixture\Address;
use Magento\Customer\Test\Fixture\Customer;
use Magento\Customer\Test\Page\Adminhtml\CustomerIndex;
use Magento\Customer\Test\Page\Adminhtml\CustomerIndexEdit;
use Magento\Mtf\Constraint\AbstractConstraint;

/**
 * Assert customer data on customer backend form.
 */
class AssertCustomerForm extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'middle';
    /* end tags */

    /**
     * Skipped fields for verify data.
     *
     * @var array
     */
    private $customerSkippedFields = [
        'id',
        'password',
        'password_confirmation',
        'current_password',
        'is_subscribed',
        'address',
        'group_id'
    ];

    /**
     * Locale map.
     *
     * @var array
     */
    private $localeMap = [
        'en_GB' => 'd/m/Y'
    ];

    /**
     * Format date for current locale.
     *
     * @var string
     */
    private $localeFormat = 'm/d/Y';

    /**
     * Assert that displayed customer data on edit page(backend) equals passed from fixture.
     *
     * @param Customer $customer
     * @param CustomerIndex $pageCustomerIndex
     * @param CustomerIndexEdit $pageCustomerIndexEdit
     * @param Address $address [optional]
     * @param string $locale
     * @return void
     */
    public function processAssert(
        Customer $customer,
        CustomerIndex $pageCustomerIndex,
        CustomerIndexEdit $pageCustomerIndexEdit,
        Address $address = null,
        $locale = ''
    ) {
        $this->localeFormat = '' !== $locale && isset($this->localeMap[$locale])
            ? $this->localeMap[$locale]
            : $this->localeFormat;
        $data = [];
        $filter = [];

        $data['customer'] = $customer->getData();
        if ($address) {
            $data['addresses'][1] = $address->hasData() ? $address->getData() : [];
        } else {
            $data['addresses'] = [];
        }
        if (isset($data['customer']['dob'])) {
            $data['customer']['dob'] = date($this->localeFormat, strtotime($data['customer']['dob']));
        }
        $filter['email'] = $data['customer']['email'];

        $pageCustomerIndex->open();
        $pageCustomerIndex->getCustomerGridBlock()->searchAndOpen($filter);

        $dataForm = $pageCustomerIndexEdit->getCustomerForm()->getDataCustomer($customer, $address);
        $dataDiff = $this->verify($data, $dataForm);
        \PHPUnit\Framework\Assert::assertTrue(
            empty($dataDiff),
            'Customer data on edit page(backend) not equals to passed from fixture.'
            . "\nFailed values: " . implode(', ', $dataDiff)
        );
        $this->assertCustomerGroupName($customer, $dataForm);
    }

    /**
     * Verify data in form equals to passed from fixture.
     *
     * @param array $dataFixture
     * @param array $dataForm
     * @return array
     */
    private function verify(array $dataFixture, array $dataForm)
    {
        $result = [];

        $customerDiff = array_diff_assoc($dataFixture['customer'], $dataForm['customer']);
        foreach ($customerDiff as $name => $value) {
            if (in_array($name, $this->customerSkippedFields)) {
                continue;
            }
            if (isset($dataForm['customer'][$name])) {
                $result[] = "\ncustomer {$name}: \"{$dataForm['customer'][$name]}\" instead of \"{$value}\"";
            } else {
                $result[] = "\ncustomer {$name}: Field is absent. Expected value \"{$value}\"";
            }
        }
        foreach ($dataFixture['addresses'] as $key => $address) {
            $addressDiff = array_diff($address, $dataForm['addresses'][$key]);
            foreach ($addressDiff as $name => $value) {
                if (isset($dataForm['addresses'][$key][$name])) {
                    $result[] = "\naddress #{$key} {$name}: \"{$dataForm['addresses'][$key][$name]}"
                        . "\" instead of \"{$value}\"";
                } else {
                    $result[] = "\naddress #{$key} {$name}: Field absent. Expected value \"{$value}\"";
                }
            }
        }

        return $result;
    }

    /**
     * Check is Customer Group name correct.
     *
     * @param Customer $customer
     * @param array $formData
     * @return void
     */
    private function assertCustomerGroupName(Customer $customer, array $formData)
    {
        $customerGroupName = $customer->getGroupId();

        if ($customerGroupName) {
            \PHPUnit\Framework\Assert::assertNotEmpty(
                $formData['customer']['group_id'],
                'Customer Group value is empty.'
            );

            if (!empty($formData['customer']['group_id'])) {
                \PHPUnit\Framework\Assert::assertContains(
                    $customerGroupName,
                    $formData['customer']['group_id'],
                    'Customer Group name is incorrect.'
                );
            }
        }
    }

    /**
     * Text success verify Customer form.
     *
     * @return string
     */
    public function toString()
    {
        return 'Displayed customer data on edit page(backend) equals to passed from fixture.';
    }
}
