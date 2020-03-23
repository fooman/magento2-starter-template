<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Customer\Test\TestCase;

use Magento\Customer\Test\Fixture\Customer;
use Magento\Customer\Test\Page\CustomerAccountIndex;
use Magento\Mtf\TestCase\Injectable;

/**
 * Preconditions:
 * 1. Create customer.
 * 2. Add default address (NY).
 * 3. Add one more address (CA).
 *
 * Steps:
 * 1. Open frontend.
 * 2. Login as customer.
 * 3. Go to 'Address Book' tab > Additional Address Entries.
 * 4. Delete second address - click 'Delete Address' button.
 * 5. Perform all assertions.
 *
 * @group Customers
 * @ZephyrId MAGETWO-28066
 */
class DeleteCustomerAddressTest extends Injectable
{
    /* tags */
    const MVP = 'yes';
    const STABLE = 'no';
    /* end tags */

    /**
     * Customer index page.
     *
     * @var CustomerAccountIndex
     */
    protected $customerAccountIndex;

    /**
     * Prepare pages for test.
     *
     * @param CustomerAccountIndex $customerAccountIndex
     * @return void
     */
    public function __inject(
        CustomerAccountIndex $customerAccountIndex
    ) {
        $this->customerAccountIndex = $customerAccountIndex;
    }

    /**
     * Runs Delete Customer Address test.
     *
     * @param Customer $customer
     * @return array
     */
    public function test(Customer $customer)
    {
        // Precondition:
        $customer->persist();
        $addressToDelete = $customer->getDataFieldConfig('address')['source']->getAddresses()[1];

        // Steps:
        $this->objectManager->create(
            \Magento\Customer\Test\TestStep\LoginCustomerOnFrontendStep::class,
            ['customer' => $customer]
        )->run();
        $this->customerAccountIndex->getAccountMenuBlock()->openMenuItem('Address Book');
        $this->customerAccountIndex->getAdditionalAddressBlock()->deleteAdditionalAddress($addressToDelete);

        return ['deletedAddress' => $addressToDelete];
    }
}
