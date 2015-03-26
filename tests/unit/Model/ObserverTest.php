<?php

class ObserverTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        Mage::getModel('core/config')->saveConfig('kl_rulemailer_settings/general/logging', 1);
        Mage::getConfig()->reinit();
        Mage::app()->reinitStores();
    }

    /** @test */
    public function it_listens_for_customer_save_after_event_and_delegates_the_handling()
    {
        $responseMock = Mockery::mock('KL_Rulemailer_Model_Api_Rest_Response');
        $responseMock->shouldReceive('isError')->once()->andReturn(false);
        $apiMock = Mockery::mock('KL_Rulemailer_Model_Api_Subscriber');
        $apiMock->shouldReceive('create')->once()->andReturn($responseMock);
        $customerMock = Mockery::mock('Mage_Customer_Model_Customer');
        $customerMock->shouldReceive('getName')->andReturn('foo');
        $customerMock->shouldReceive('getData')->andReturn(array(1,2,3));
        $observer = new KL_Rulemailer_Model_Observer($apiMock);
        $observer->addSubscriber($customerMock, array());
    }

    /** @test */
    public function it_can_remove_a_subscriber()
    {
        $responseMock = Mockery::mock('KL_Rulemailer_Model_Api_Rest_Response');
        $responseMock->shouldReceive('isError')->once()->andReturn(false);
        $apiMock = Mockery::mock('KL_Rulemailer_Model_Api_Subscriber');
        $apiMock->shouldReceive('removeTag')->with('newsletter', 'foo@bar.com')->once()->andReturn($responseMock);
        $customerMock = Mockery::mock('Mage_Customer_Model_Customer');
        $customerMock->shouldReceive('getData')->twice()->andReturn('foo@bar.com');
        $observer = new KL_Rulemailer_Model_Observer($apiMock);
        $observer->removeSubscriber($customerMock, array());
    }
}
 