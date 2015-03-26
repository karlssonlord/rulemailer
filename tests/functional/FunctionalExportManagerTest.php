<?php

use MageTest\Manager\Attributes\Provider\YamlProvider;

class FunctionalExportManagerTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
        Mage::getModel('core/config')->saveConfig('kl_rulemailer/general/logging', 1);
    }

    /**
     *  In order to post the same request every time, we mock the order to give it the same data
     *  and so this way the request is cached by php-vcr
     *
     * @test
     * @vcr export_manager.yml
     */
    public function it_exports_fields_to_an_existing_subscriber()
    {
        $fieldsDummy = [
            ['key' => 'Order.Id', 'value' => 123456789],
            ['key' => 'Order.Sku', 'value' => 'abc, 123, cde'],
            ['key' => 'Order.StoreId', 'value' => 1],
            ['key' => 'Order.Categories', 'value' => 'jackor, byxor, hattar'],
            ['key' => 'Order.Weight', 'value' => 3.1000],
            ['key' => 'Order.GrandTotal', 'value' => 2020.9],
            ['key' => 'Order.StoreCurrencyCode', 'value' => 'SEK'],
            ['key' => 'Order.ShippingMethod', 'value' => 'flatrate_flatrate'],
            ['key' => 'Order.Coupon', 'value' => 'HOSTREA123'],
            ['key' => 'Order.DiscountAmount', 'value' => 300.0000],
            ['key' => 'Order.ShippingAmount', 'value' => 75.0000],
            ['key' => 'Order.Brands', 'value' => 'Nike, Adidas, Converse']
        ];

        $fieldsBuilderMock = Mockery::mock('KL_Rulemailer_Model_Export_FieldsBuilder');
        $fieldsBuilderMock->shouldReceive('extractFields')->with(Mockery::any())->andReturn($fieldsDummy);

        $clerk = new KL_Rulemailer_Model_Export_Clerk(null, $fieldsBuilderMock);

        $subscriberMock = Mockery::mock('Mage_Newsletter_Model_Subscriber');
        $subscriberMock->shouldReceive('load')->andReturn($subscriberMock);
        $subscriberMock->shouldReceive('getId')->andReturn(666);

        $orderMock1 = Mockery::mock('Mage_Sales_Model_Order');
        $orderMock2 = Mockery::mock('Mage_Sales_Model_Order');
        $orderMock2->shouldReceive('getCustomerEmail')->twice()->andReturn('david@karlssonlord.com');
        $orderMock2->shouldReceive('getId')->andReturn(123456789);


        $ordersCollectionMock = Mockery::mock('Mage_Sales_Order_Model_Resource_Order_Collection');
        $ordersCollectionMock->shouldReceive('addAttributeToFilter')->andReturn($ordersCollectionMock);
        $ordersCollectionMock->shouldReceive('addAttributeToSelect')->andReturn($ordersCollectionMock);
        $ordersCollectionMock->shouldReceive('setOrder')->andReturn(array($orderMock2));

        $orderMock1->shouldReceive('getCollection')->once()->andReturn($ordersCollectionMock);

        $exportManager = new KL_Rulemailer_Model_Export_Manager($subscriberMock, null, $clerk, $orderMock1);
        $exportManager->exportData();
    }

}
