<?php

use MageTest\Manager\Factory;

class FieldsBuilderTest extends PHPUnit_Framework_TestCase
{
    use HandleAttributes;

    public function setUp()
    {
        Factory::prepareDb();
    }

    public function tearDown()
    {
        Factory::clear();
    }

    /** @test */
    public function it_builds_up_the_fields_structures()
    {
        $this->createAttribute('size', 'Size', 'select', 'simple');
        $this->addAttributeOption('size', 'XXS');
        $this->addAttributeOption('size', 'XS');
        $this->addAttributeOption('size', 'S');
        $this->addAttributeOption('size', 'M');
        $this->addAttributeOption('size', 'L');
        $this->addAttributeOption('size', 'XL');
        $this->addAttributeOption('size', 'XXL');
        $this->addAttributeOption('size', 'XXXL');
        $this->addAttributeOption('manufacturer', 'Union Carbide');

        $this->addAttributeToAttributeSet(81, 4, 'Default');

        $product = Factory::make('catalog/product', [
            'sku' => 'foo',
            'weight' => 1.0,
                'special_price' => null,
            'price' => 495.0,
            'color' => 'Brun',
            'size' => 'XXS',
            'manufacturer' => 3
        ]);

        $quote = Factory::with($product)->make('sales/quote');
        $order = Factory::with($quote)->make('sales/order');
        $order->setCustomerFirstname('Foo');

        $fieldsBuilder = new KL_Rulemailer_Model_Export_FieldsBuilder;
        $fields = $fieldsBuilder->extractFields($order);

        $expected = [
            ['key' => 'Order.Color', 'value' => ['Brun'], 'type' => 'multiple'],
            ['key' => 'Order.Size', 'value' => ['XXS'], 'type' => 'multiple'],
            ['key' => 'Order.IncrementId', 'value' => $order->getIncrementId()],
            ['key' => 'Order.Date', 'value' => $order->getCreatedAt(), 'type' => 'date'],
            ['key' => 'Order.Firstname', 'value' => 'Foo'],
            ['key' => 'Order.Lastname', 'value' => (string)$order->getCustomerLastname()],
            ['key' => 'Order.Street', 'value' => 'Swedenborgsgatan 1'],
            ['key' => 'Order.City', 'value' => (string)$order->getShippingAddress()->getCity()],
            ['key' => 'Order.Postcode', 'value' => (string)$order->getShippingAddress()->getPostcode()],
            ['key' => 'Order.Country', 'value' => (string)$order->getShippingAddress()->getCountry()],
            ['key' => 'Order.Phone', 'value' => (string)$order->getShippingAddress()->getTelephone()],
            ['key' => 'Order.TaxAmount', 'value' => 0],
            ['key' => 'Order.Sku', 'value' => ['foo'], 'type' => 'multiple'],
            ['key' => 'Order.StoreId', 'value' => 1],
            ['key' => 'Order.Categories', 'value' => [], 'type' => 'multiple'],
            ['key' => 'Order.Weight', 'value' => 1.0],
            ['key' => 'Order.Subtotal', 'value' => (float)$order->getSubtotal()],
            ['key' => 'Order.GrandTotal', 'value' => 500.0],
            ['key' => 'Order.StoreCurrencyCode', 'value' => 'EUR'],
            ['key' => 'Order.ShippingMethod', 'value' => 'flatrate_flatrate'],
            ['key' => 'Order.Coupon', 'value' => ''],
            ['key' => 'Order.DiscountAmount', 'value' => 0],
            ['key' => 'Order.DiscountPercent', 'value' => 0],
            ['key' => 'Order.ShippingAmount', 'value' => 5.0000],
            ['key' => 'Order.Brands', 'value' => ['Union Carbide'], 'type' => 'multiple']
        ];

        $this->assertEquals($expected, $fields);
    }
}
 