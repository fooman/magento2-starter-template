<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Bundle\Test\Handler\BundleProduct;

use Magento\Catalog\Test\Handler\CatalogProductSimple\Webapi as SimpleProductWebapi;
use Magento\Mtf\Config\DataInterface;
use Magento\Mtf\System\Event\EventManagerInterface;
use Magento\Mtf\Util\Protocol\CurlTransport\WebapiDecorator;

/**
 * Create new bundle product via webapi.
 */
class Webapi extends SimpleProductWebapi implements BundleProductInterface
{
    /**
     * @constructor
     * @param DataInterface $configuration
     * @param EventManagerInterface $eventManager
     * @param WebapiDecorator $webapiTransport
     * @param Curl $handlerCurl
     */
    public function __construct(
        DataInterface $configuration,
        EventManagerInterface $eventManager,
        WebapiDecorator $webapiTransport,
        Curl $handlerCurl
    ) {
        parent::__construct($configuration, $eventManager, $webapiTransport, $handlerCurl);
    }

    /**
     * Prepare data for creating product request.
     *
     * @return void
     */
    protected function prepareData()
    {
        parent::prepareData();
        $this->prepareBundleItems();
    }

    /**
     * Preparation of bundle items data.
     *
     * @return void
     */
    protected function prepareBundleItems()
    {
        $bundleSelections = isset($this->fields['product']['bundle_selections'])
            ? $this->fields['product']['bundle_selections']
            : [];
        $bundleProductOptions = [];

        if (!empty($bundleSelections)) {
            foreach ($bundleSelections['bundle_options'] as $key => $bundleOption) {
                $bundleProductOptions[$key] = [
                    'sku' => $this->fixture->getSku(),
                    'title' => $bundleOption['title'],
                    'type' => $bundleOption['type'],
                    'required' => $bundleOption['required'],
                    'position' => isset($bundleOption['position']) ? (int)$bundleOption['position'] : $key,
                    'product_links' => $this->prepareLinksInfo($bundleSelections, $key)
                ];
            }
        }

        $this->fields['product']['extension_attributes']['bundle_product_options'] = $bundleProductOptions;
        unset($this->fields['bundle_options']);
        unset($this->fields['bundle_selections']);
        unset($this->fields['product']['bundle_selections']);
    }

    /**
     * Prepare links info field.
     *
     * @param array $bundleSelections
     * @param int $key
     * @return array
     */
    private function prepareLinksInfo(array $bundleSelections, $key)
    {
        $result = [];
        $productLinksInfo = $bundleSelections['bundle_options'][$key]['assigned_products'];
        $products = $bundleSelections['products'][$key];
        foreach ($productLinksInfo as $linkKey => $productLink) {
            $product = $products[$linkKey];
            $result[] = [
                'sku' => $product->getSku(),
                'qty' => $productLink['data']['selection_qty'],
                'is_default' => false,
                'price' => isset($productLink['data']['selection_price_value'])
                    ? $productLink['data']['selection_price_value']
                    : null,
                'price_type' => isset($productLink['data']['selection_price_type'])
                    ? $productLink['data']['selection_price_type']
                    : null,
                'can_change_quantity' => isset($productLink['data']['user_defined'])
                    ? $productLink['data']['user_defined']
                    : 0,
                'position' => $linkKey,
            ];
        }

        return $result;
    }

    /**
     * Parse response.
     *
     * @param array $response
     * @return array
     */
    protected function parseResponse(array $response)
    {
        return array_replace_recursive(parent::parseResponse($response), $this->parseResponseSelections($response));
    }

    /**
     * Parse bundle selections in response.
     *
     * @param array $response
     * @return array
     */
    protected function parseResponseSelections(array $response)
    {
        $bundleSelections = $this->fixture->getBundleSelections();

        foreach ($response['extension_attributes']['bundle_product_options'] as $optionKey => $option) {
            foreach ($option['product_links'] as $assignedKey => $optionValue) {
                $bundleSelections['bundle_options'][$optionKey]['assigned_products'][$assignedKey] += [
                    'selection_id' => (int)$optionValue['id'],
                    'option_id' => $option['option_id']
                ];
            }
        }

        return ['bundle_selections' => $bundleSelections];
    }
}
