<?php

class KL_Rulemailer_Model_Export_FieldsBuilder
{
    const ORDER_GROUP = "Order";
    const SUBSCRIBER_GROUP = "User";
    const CART_GROUP = "Cart";
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
        Mage_Catalog_Model_Product $product = null,
        Mage_Customer_Model_Customer $customer = null
    ) {
        $this->category = $category ? : Mage::getModel('catalog/category');
        $this->product = $product ? : Mage::getModel('catalog/product');
        $this->customer = $customer ? : Mage::getModel('customer/customer');
    }

    public function extractCustomerFields(Mage_Customer_Model_Customer $customer)
    {
        $fields = [
                   ['key' => self::SUBSCRIBER_GROUP . '.Firstname', 'value' => $customer->getFirstname()],
                   ['key' => self::SUBSCRIBER_GROUP . '.Lastname', 'value' => $customer->getLastname()]
        ];

        if ($customer->getDob()) {
            $fields[] = ['key' => self::SUBSCRIBER_GROUP . '.BirthDate', 'value' => $customer->getDob()];
        }

        if ($customer->getGender()) {
            $fields[] = ['key' => self::SUBSCRIBER_GROUP . '.Gender', 'value' => $customer->getGender()];
        }

        return $fields;
    }

    public function extractCartFields(Mage_Sales_Model_Quote $quote)
    {
        $fields = [
                   ['key' => self::CART_GROUP . '.GrandTotal', 'value' => $quote->getGrandTotal()],
                   ['key' => self::CART_GROUP . '.Currency', 'value' => $quote->getQuoteCurrencyCode()],
                   ['key' => self::CART_GROUP . '.ItemsCount', 'value' => $quote->getItemsCount()],
                   ['key' => self::CART_GROUP . '.TotalCost', 'value' => $quote->getGrandTotal()],
                   ['key' => self::CART_GROUP . '.Products', 'value' => $this->getProductsJson($quote), 'type' => 'json']
                   ];

        return $fields;
    }

    protected function getProductsJson($quote)
    {
        $products = [];

        foreach ($quote->getAllVisibleItems() as $item) {
            $product = $this->product->load($item->getProductId());
            $products[] = ['name' => $product->getName(), 'quantity' => $item->getQty(),
                           'url' => $product->getProductUrl(), 'price' => $product->getPrice()];
        }

        return json_encode($products);
    }

    /**
     *  Returns an array structure of useful fields
     *
     * @param Mage_Sales_Model_Order $order
     * @return array
     */
    public function extractOrderFields(Mage_Sales_Model_Order $order)
    {
        $fieldSet =  [
            ['key' => self::ORDER_GROUP . '.IncrementId', 'value' => (string)$order->getIncrementId()],
            ['key' => self::ORDER_GROUP . '.Date', 'value' => (string)$order->getCreatedAt(), 'type' => 'date'],
            ['key' => self::ORDER_GROUP . '.Firstname', 'value' => (string)$order->getCustomerFirstname()],
            ['key' => self::ORDER_GROUP . '.Lastname', 'value' => (string)$order->getCustomerLastname()],
            ['key' => self::ORDER_GROUP . '.Street', 'value' => (string)$order->getShippingAddress()->getStreet1()],
            ['key' => self::ORDER_GROUP . '.City', 'value' => (string)$order->getShippingAddress()->getCity()],
            ['key' => self::ORDER_GROUP . '.Postcode', 'value' => (string)$order->getShippingAddress()->getPostcode()],
            ['key' => self::ORDER_GROUP . '.Country', 'value' => (string)$order->getShippingAddress()->getCountry()],
            ['key' => self::ORDER_GROUP . '.Phone', 'value' => (string)$order->getShippingAddress()->getTelephone()],
            ['key' => self::ORDER_GROUP . '.TaxAmount', 'value' => (float)$this->getTaxAmount($order)],
            ['key' => self::ORDER_GROUP . '.Sku', 'value' => (array)$this->getOrderSkus($order), 'type' => 'multiple'],
            ['key' => self::ORDER_GROUP . '.StoreId', 'value' => (integer)$order->getStoreId()],
            ['key' => self::ORDER_GROUP . '.Categories', 'value' => (array)$this->getCategoryNames($order), 'type' => 'multiple'],
            ['key' => self::ORDER_GROUP . '.Weight', 'value' => (float)$order->getWeight()],
            ['key' => self::ORDER_GROUP . '.Subtotal', 'value' => (float)$order->getSubtotal()],
            ['key' => self::ORDER_GROUP . '.GrandTotal', 'value' => (float)$order->getGrandTotal()],
            ['key' => self::ORDER_GROUP . '.StoreCurrencyCode', 'value' => (string)$order->getStoreCurrencyCode()],
            ['key' => self::ORDER_GROUP . '.ShippingMethod', 'value' => (string)$order->getShippingMethod()],
            ['key' => self::ORDER_GROUP . '.Coupon', 'value' => (string)$order->getCouponCode()],
            ['key' => self::ORDER_GROUP . '.DiscountAmount', 'value' => (float)$order->getDiscountAmount()],
            ['key' => self::ORDER_GROUP . '.DiscountPercent', 'value' => (float)$this->getDiscountPercent($order)],
            ['key' => self::ORDER_GROUP . '.ShippingAmount', 'value' => (float)$order->getShippingAmount()],
            ['key' => self::ORDER_GROUP . '.Brands', 'value' => (array)$this->getBrands($order), 'type' => 'multiple'],
            ['key' => self::ORDER_GROUP . '.Products', 'value' => $this->getProductsJson($order), 'type' => 'json']
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
            $product = $this->product->load($item->getProductId());

            foreach ($product->getCategoryIds() as $categoryId) {
                if (!in_array($categoryId, $categoryIds)) {
                    $categoryIds[] = $categoryId;
                }
            }
        }

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
            $product = $this->loadProduct($item);
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
                if ($this->isSimple($item) && $attributeValue = $this->getAttributeValue($attribute, $item)) {
                    $attributes[$attribute][] = $attributeValue;
                }
            }
        }

        // I got into this mess of throwing booleans around. Haven't got time to redo this.
        // Suck...
        $additionalAttributes = $this->buildFields($attributes);
        if ($additionalAttributes) {
            return array_merge($additionalAttributes, $fieldSet);
        }

        return $fieldSet;
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
        return $order->getTaxAmount();
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
        if (count($attributeValue) == 1 && is_null(reset($attributeValue))) return false;
        return ['key' => 'Order.'.ucfirst($attributeKey), 'value' => $attributeValue, 'type' => 'multiple'];
    }

    private function getValue($attributeValue)
    {
        if (is_array($attributeValue)) {
            if (count($attributeValue) <= 1) {
                return false;
            }
        }
        return $attributeValue;
    }

    private function getAttributeValue($attributeCode, Mage_Sales_Model_Order_Item $item)
    {
        $product = $this->loadProduct($item);
        $versionInfo = Mage::getVersionInfo();
        if ($versionInfo['minor'] < 9) {
            return $product->getAttributeText($attributeCode);
        }
        $method = 'get'.ucfirst($attributeCode);
        return $product->$method() ? : '';
    }

    /**
     * @param Mage_Sales_Model_Order_Item $item
     * @return Mage_Core_Model_Abstract
     */
    private function loadProduct(Mage_Sales_Model_Order_Item $item)
    {
        return $item->getProduct()->load($item->getProduct()->getId());
    }

    
}
