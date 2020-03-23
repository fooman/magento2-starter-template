<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Catalog\Test\Block\Adminhtml\Product\Edit\Section\Websites;

use Magento\Mtf\Client\Locator;
use Magento\Mtf\Client\Element\SimpleElement;

/**
 * Typified element class for store tree element.
 */
class StoreTree extends SimpleElement
{
    /**
     * Selector for website checkbox.
     *
     * @var string
     */
    protected $website = './/label[contains(text(), "%s")]';

    /**
     * Selector for selected website checkbox.
     *
     * @var string
     */
    protected $selectedWebsite = '(.//input[contains(@name, "product[website_ids]") and not(@value="0")])[%d]/../label';

    /**
     * Set value.
     *
     * @param array|string $values
     * @return void
     * @throws \Exception
     */
    public function setValue($values)
    {
        $this->clearValue();

        $values = is_array($values) ? $values : [$values];
        foreach ($values as $value) {
            $website = $this->find(sprintf($this->website, $value), Locator::SELECTOR_XPATH);
            if (!$website->isVisible()) {
                throw new \Exception("Can't find website: \"{$value}\".");
            }
            if (!$website->isSelected()) {
                $website->click();
            }
        }
    }

    /**
     * Get value.
     *
     * @return array
     */
    public function getValue()
    {
        $values = [];

        $count = 1;
        $website = $this->find(sprintf($this->selectedWebsite, $count), Locator::SELECTOR_XPATH);
        while ($website->isVisible()) {
            $values[] = $website->getText();
            ++$count;
            $website = $this->find(sprintf($this->selectedWebsite, $count), Locator::SELECTOR_XPATH);
        }
        return $values;
    }

    /**
     * Clear selected checkboxes.
     *
     * @return void
     */
    private function clearValue()
    {
        foreach ($this->getValue() as $value) {
            $this->find(sprintf($this->website, $value), Locator::SELECTOR_XPATH)->click();
        }
    }
}
