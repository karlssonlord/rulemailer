<?php

class FieldsBuilderTest extends PHPUnit_Framework_TestCase
{
    /** @test */
    public function it_builds_up_the_fields_structures()
    {
        $category = Mockery::mock('Mage_Catalog_Model_Category');
        $category->shouldReceive('getIncrementId')->once()->andReturn('foo');
        $category->shouldReceive('load')->once()->andReturn($category);
        $category->shouldReceive('getName')->andReturn('x');

        $product = Mockery::mock('Mage_Catalog_Model_Product');
        $product->shouldReceive('getSku')->andReturn('foo');
        $product->shouldReceive('getId')->andReturn(1);
        $product->shouldReceive('load')->andReturn($product);
        $product->shouldReceive('getCategoryIds')->andReturn('1');
        $product->shouldReceive('getAttributeText')->andReturn('bar');

        $fieldsBuilder = new KL_Rulemailer_Model_Export_FieldsBuilder($category, $product);

        $order = Mockery::mock('Mage_Sales_Model_Order');
        $order->shouldReceive('getAllVisibleItems')->once()->andReturn(array($product));
        $order->shouldReceive('getIncrementId')->once()->andReturn('123123');
        $order->shouldReceive('getSku')->once()->andReturn('foo');
        $order->shouldReceive('getGrandTotal')->once()->andReturn('500');
        $order->shouldReceive('getCouponCode')->once()->andReturn('TEST');
        $order->shouldReceive('getBaseShippingInvoiced')->once()->andReturn('50');
        $order->shouldReceive('getStoreId')->once()->andReturn(1);
        $order->shouldReceive('getWeight')->once()->andReturn(1);
        $order->shouldReceive('getStoreCurrencyCode')->once()->andReturn('SEK');
        $order->shouldReceive('getShippingMethod')->once()->andReturn('flatrate_flatrate');
        $order->shouldReceive('getDiscountAmount')->once()->andReturn(10.0000);
        $order->shouldReceive('getShippingAmount')->once()->andReturn(5.0000);

        $fields = $fieldsBuilder->extractFields($order);

        $expected = [
            ['key' => 'Order.Id', 'value' => '123123'],
            ['key' => 'Order.Sku', 'value' => 'foo'],
            ['key' => 'Order.StoreId', 'value' => 1],
            ['key' => 'Order.Categories', 'value' => "x"],
            ['key' => 'Order.Weight', 'value' => 1.0],
            ['key' => 'Order.GrandTotal', 'value' => 500.0],
            ['key' => 'Order.StoreCurrencyCode', 'value' => 'SEK'],
            ['key' => 'Order.ShippingMethod', 'value' => 'flatrate_flatrate'],
            ['key' => 'Order.Coupon', 'value' =>'TEST'],
            ['key' => 'Order.DiscountAmount', 'value' => 10.0000],
            ['key' => 'Order.ShippingAmount', 'value' => 5.0000],
            ['key' => 'Order.Brands', 'value' => 'bar']
        ];

        $this->assertEquals($expected, $fields);
    }

}
 