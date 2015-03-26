<?php

class ExportClerkTest extends PHPUnit_Framework_TestCase
{
    /** @test */
    public function gathers_all_order_data_and_throws_it_at_the_rule_mailer_api()
    {
        $item = Mockery::mock('Mage_Sales_Model_Quote_Item');
        $item->shouldReceive('getSku')->once()->andReturn('foo');
        $item->shouldReceive('getQty')->once()->andReturn('bar');

        $order = Mockery::mock('Mage_Sales_Model_Order');
        $order->shouldReceive('getCustomerEmail')->once()->andReturn('foo@bar.com');
        $order->shouldReceive('getIncrementId')->once()->andReturn(123456789);
        $order->shouldReceive('getAllVisibleItems')->once()->andReturn(array($item));
        $order->shouldReceive('getCouponCode')->atLeast()->once()->andReturn('baz');

        $apiResponse = Mockery::mock('KL_Rulemailer_Model_Api_Rest_Response');
        $apiResponse->shouldReceive('isError')->once()->andReturn(false);

        $api = Mockery::mock('KL_Rulemailer_Model_Api_Subscriber');
        $api->shouldReceive('create')->once()->andReturn($apiResponse);

        $fieldsBuilder = Mockery::mock('KL_Rulemailer_Model_Export_FieldsBuilder');
        $fieldsBuilder->shouldReceive('extractFields')->once()->andReturn(array('frasse' => 'hasse'));

        $clerk = new KL_Rulemailer_Model_Export_Clerk($api, $fieldsBuilder);
        $clerk->conductExport($order);
    }

}
 