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
        $this->addAttributeOption('manufacturer', 'Union Carbide');
        $this->addAttributeToAttributeSet(81, 4, 'Default');

        $product = Factory::make('catalog/product', [
            'sku' => 'foo',
            'weight' => 1.0,
            'special_price' => null,
            'price' => 495.0,
            'color' => 'Brun',
            'manufacturer' => 3
        ]);

        $quote = Factory::with($product)->make('sales/quote');
        $order = Factory::with($quote)->make('sales/order');

        $fieldsBuilder = new KL_Rulemailer_Model_Export_FieldsBuilder;
        $fields = $fieldsBuilder->extractFields($order);

        $expected = [
            ['key' => 'Order.Color', 'value' => 'Brun'],
            ['key' => 'Order.Id', 'value' => $order->getIncrementId()],
            ['key' => 'Order.Date', 'value' => $order->getCreatedAt()],
            ['key' => 'Order.TaxAmount', 'value' => 0],
            ['key' => 'Order.Sku', 'value' => ['foo']],
            ['key' => 'Order.StoreId', 'value' => 1],
            ['key' => 'Order.Categories', 'value' => []],
            ['key' => 'Order.Weight', 'value' => 1.0],
            ['key' => 'Order.GrandTotal', 'value' => 500.0],
            ['key' => 'Order.StoreCurrencyCode', 'value' => 'EUR'],
            ['key' => 'Order.ShippingMethod', 'value' => 'flatrate_flatrate'],
            ['key' => 'Order.Coupon', 'value' => ''],
            ['key' => 'Order.DiscountAmount', 'value' => 0],
            ['key' => 'Order.DiscountPercent', 'value' => 0],
            ['key' => 'Order.ShippingAmount', 'value' => 5.0000],
            ['key' => 'Order.Brands', 'value' => ['Union Carbide']]
        ];

        $this->assertEquals($expected, $fields);
    }
}
 