<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Wishlist\Test\TestStep;

use Magento\Catalog\Test\Page\Product\CatalogProductView;
use Magento\Mtf\Client\BrowserInterface;
use Magento\Mtf\TestStep\TestStepInterface;

/**
 * Class AddProductsToWishlistStep
 * Adding created products to the wish list
 */
class AddProductsToWishlistStep implements TestStepInterface
{
    /**
     * Array with products
     *
     * @var array
     */
    protected $products;

    /**
     * Frontend product view page
     *
     * @var CatalogProductView
     */
    protected $catalogProductView;

    /**
     * Interface Browser
     *
     * @var BrowserInterface
     */
    protected $browser;

    /**
     * Configure flag
     *
     * @var bool
     */
    protected $configure;

    /**
     * @constructor
     * @param CatalogProductView $catalogProductView
     * @param BrowserInterface $browser
     * @param array $products
     * @param bool $configure [optional]
     */
    public function __construct(
        CatalogProductView $catalogProductView,
        BrowserInterface $browser,
        array $products,
        $configure = false
    ) {
        $this->products = $products;
        $this->catalogProductView = $catalogProductView;
        $this->browser = $browser;
        $this->configure = $configure;
    }

    /**
     * Add products to the wish list
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->products as $product) {
            $this->browser->open($_ENV['app_frontend_url'] . $product->getUrlKey() . '.html');
            if ($this->configure) {
                $this->catalogProductView->getViewBlock()->addToWishlist($product);
            } else {
                $this->catalogProductView->getViewBlock()->clickAddToWishlist();
            }
        }
    }
}
