<?php

class KL_Rulemailer_Model_Export_FieldsBuilder
{
    /**
     * @var Mage_Catalog_Model_Category
     */
    private $category;
    /**
     * @var Mage_Catalog_Model_Product
     */
    private $product;

    public function __construct(
        Mage_Catalog_Model_Category $category = null,
        Mage_Catalog_Model_Product $product = null
    ) {
        $this->category = $category ? : Mage::getModel('catalog/category');
        $this->product = $product ? : Mage::getModel('catalog/product');
    }

    /**
     *  Returns an array structure of useful fields
     *
     * @param Mage_Sales_Model_Order $order
     * @return array
     */
    public function extractFields(Mage_Sales_Model_Order $order)
    {
        return [
            ['key' => 'Order.Id', 'value' => (integer)$order->getIncrementId()],
            ['key' => 'Order.Sku', 'value' => (string)$this->getOrderSkus($order)],
            ['key' => 'Order.StoreId', 'value' => (integer)$order->getStoreId()],
            ['key' => 'Order.Categories', 'value' => (string)$this->getCategoryNames($order)],
            ['key' => 'Order.Weight', 'value' => (float)$order->getWeight()],
            ['key' => 'Order.GrandTotal', 'value' => (float)$order->getGrandTotal()],
            ['key' => 'Order.StoreCurrencyCode', 'value' => (string)$order->getStoreCurrencyCode()],
            ['key' => 'Order.ShippingMethod', 'value' => (string)$order->getShippingMethod()],
            ['key' => 'Order.Coupon', 'value' => (string)$order->getCouponCode()],
            ['key' => 'Order.DiscountAmount', 'value' => (float)$order->getDiscountAmount()],
            ['key' => 'Order.ShippingAmount', 'value' => (float)$order->getShippingAmount()],
            ['key' => 'Order.Brands', 'value' => (string)$this->getBrands($order)]
        ];
    }

    /**
     *  Returns all categories from all products in the order
     *
     * @param $order
     * @return string
     */
    private function getCategoryNames($order)
    {
        $categoryIds = array();
        foreach ($order->getAllVisibleItems() as $item) {
            $product = $this->product->load($item->getId());
            foreach (explode('/', $product->getCategoryIds()) as $categoryId) {
                if (!in_array($categoryId, $categoryIds)) {
                    $categoryIds[] = $categoryId;
                }
            }
        }

        // Sort the category ids in ascending order
        ksort($categoryIds);

        $categories = array();
        foreach ($categoryIds as $categoryId) {
            $category = $this->category->load($categoryId);
            $categories[] = $category->getName();
        }

        return join(', ', $categories);
    }

    /**
     * @param Mage_Sales_Model_Order $order
     * @return string
     */
    private function getOrderSkus(Mage_Sales_Model_Order $order)
    {
        $skus = array();
        foreach ($order->getAllVisibleItems() as $item) {
            if (!in_array($item->getSku(), $skus)) {
                $skus[] = $item->getSku();
            }
        }
        return join(', ', $skus);
    }

    /**
     * @param Mage_Sales_Model_Order $order
     * @return array
     */
    private function getBrands(Mage_Sales_Model_Order $order)
    {
        $brands = array();
        foreach ($order->getAllVisibleItems() as $item) {
            $product = $this->product->load($item->getId());
            $brands[] = $product->getAttributeText('manufacturer');
        }
        return join(', ', $brands);
    }

}
