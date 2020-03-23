<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Swagger\Test\Constraint;

use Magento\Swagger\Test\Page\SwaggerUiPage;
use Magento\Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertServiceContentDisplay
 */
class AssertServiceContentDisplay extends AbstractConstraint
{
    /**
     * Assert service content on swagger page
     *
     * @param SwaggerUiPage $swaggerPage
     * @param $serviceName
     * @param array $endpoints
     * @return void
     */
    public function processAssert(SwaggerUiPage $swaggerPage, $serviceName, array $endpoints)
    {
        foreach ($endpoints as $endpoint) {
            /**
             * Selector for operation
             */
            $operationSelector = '#operations-%s-%s%s';
            $operationSelector = sprintf($operationSelector, $serviceName, $serviceName, $endpoint);
            \PHPUnit\Framework\Assert::assertTrue(
                $swaggerPage->isElementVisible($operationSelector),
                'REST API service endpoints on swagger page.'
            );
        }
    }

    /**
     * Returns a string representation of successful assertion.
     *
     * @return string
     */
    public function toString()
    {
        return 'REST API service endpoints on swagger page.';
    }
}
