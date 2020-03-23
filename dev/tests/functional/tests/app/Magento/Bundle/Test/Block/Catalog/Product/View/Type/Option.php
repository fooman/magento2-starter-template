<?php
/**
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Bundle\Test\Block\Catalog\Product\View\Type;

use Magento\Mtf\Block\Form;

/**
 * Class Option
 * Bundle option
 */
class Option extends Form
{
    /**
     * Set data in bundle option
     *
     * @param array $data
     * @return void
     */
    public function fillOption(array $data)
    {
        $mapping = $this->dataMapping($data);
        $this->_fill($mapping);
    }
}
