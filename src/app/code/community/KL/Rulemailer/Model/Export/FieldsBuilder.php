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
        $fieldSet =  [
            ['key' => 'Order.Id', 'value' => (string)$order->getIncrementId()],
            ['key' => 'Order.Date', 'value' => (string)$order->getCreatedAt()],
            ['key' => 'Order.TaxAmount', 'value' => (float)$this->getTaxAmount($order)],
            ['key' => 'Order.Sku', 'value' => (array)$this->getOrderSkus($order)],
            ['key' => 'Order.StoreId', 'value' => (integer)$order->getStoreId()],
            ['key' => 'Order.Categories', 'value' => (array)$this->getCategoryNames($order)],
            ['key' => 'Order.Weight', 'value' => (float)$order->getWeight()],
            ['key' => 'Order.GrandTotal', 'value' => (float)$order->getGrandTotal()],
            ['key' => 'Order.StoreCurrencyCode', 'value' => (string)$order->getStoreCurrencyCode()],
            ['key' => 'Order.ShippingMethod', 'value' => (string)$order->getShippingMethod()],
            ['key' => 'Order.Coupon', 'value' => (string)$order->getCouponCode()],
            ['key' => 'Order.DiscountAmount', 'value' => (float)$order->getDiscountAmount()],
            ['key' => 'Order.DiscountPercent', 'value' => (float)$this->getDiscountPercent($order)],
            ['key' => 'Order.ShippingAmount', 'value' => (float)$order->getShippingAmount()],
            ['key' => 'Order.Brands', 'value' => (array)$this->getBrands($order)]
        ];

        return $this->addArbitraryFields($fieldSet, $order);
    }

    /**
     *  Returns all categories from all products in the order
     *
     * @param $order
     * @return array
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

        return $categories;
    }

    /**
     * @param Mage_Sales_Model_Order $order
     * @return string
     */
    private function getOrderSkus(Mage_Sales_Model_Order $order)
    {
        $skus = array();
        foreach ($order->getAllItems() as $item) {
            if ($this->hasSimpleTypeAndIsUnique($item, $skus)) {
                $skus[] = $item->getSku();
            }
        }
        return $skus;
    }

    /**
     * @param Mage_Sales_Model_Order $order
     * @return array
     */
    private function getBrands(Mage_Sales_Model_Order $order)
    {
        $brands = array();
        foreach ($order->getAllItems() as $item) {
            $product = $this->load($item);
            $brands[] = $product->getAttributeText('manufacturer');
        }
        return $brands;
    }

    /**
     * @param Mage_Sales_Model_Order $order
     * @return float
     */
    private function getDiscountPercent(Mage_Sales_Model_Order $order)
    {
        return (float)$order->getDiscountAmount() / (float)$order->getGrandTotal();
    }

    /**
     * @param $item
     * @param $skus
     * @return bool
     */
    private function hasSimpleTypeAndIsUnique($item, $skus)
    {
        return !in_array($item->getSku(), $skus) && $this->isSimple($item);
    }

    private function addArbitraryFields(array $fieldSet, Mage_Sales_Model_Order $order)
    {
        $attributes = [];
        foreach (KL_Rulemailer_Model_Export_Attributes::defaultSet() as $attribute) {
            foreach ($order->getAllItems() as $item) {
                $attributes[$attribute][] = $this->getAttributeValue($attribute, $item);
            }
        }

        return array_merge($this->buildFields($attributes), $fieldSet);
    }


    /**
     * @param Mage_Sales_Model_Order_Item $item
     * @return bool
     */
    private function isSimple(Mage_Sales_Model_Order_Item $item)
    {
        return $item->getProduct()->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_SIMPLE;
    }

    /**
     * @param Mage_Sales_Model_Order $order
     * @return mixed
     */
    private function getTaxAmount(Mage_Sales_Model_Order $order)
    {
        return $order->getTaxMount();
    }

    private function buildFields(array $attributes)
    {
        $fields = [];
        foreach ($attributes as $attributeKey => $attributeValue) {
            $keyValue = $this->buildKeyValue($attributeKey, $attributeValue);
            if ($keyValue) {
                $fields[] = $keyValue;
            }
        }
        return $fields;
    }

    private function buildKeyValue($attributeKey, $attributeValue)
    {
        $value = $this->getValue($attributeValue);
        if (!count($value) || !$value) {
            return false;
        }
        return ['key' => 'Order.'.ucfirst($attributeKey), 'value' => $value];
    }

    private function getValue($attributeValue)
    {
        if (is_array($attributeValue)) {
            if (count($attributeValue) <= 1) {
                return reset($attributeValue);
            }
        }
        return $attributeValue;
    }

    private function getAttributeValue($attribute, Mage_Sales_Model_Order_Item $item)
    {
        $method = "get".ucfirst($attribute);
        $product = $this->load($item);
        return $product->$method();
    }

    /**
     * @param Mage_Sales_Model_Order_Item $item
     * @return Mage_Core_Model_Abstract
     */
    private function load(Mage_Sales_Model_Order_Item $item)
    {
        return $item->getProduct()->load($item->getProduct()->getId());
    }

}
