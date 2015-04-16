<?php


class ExportManagerTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        Mage::init();
//        Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
    }
    /** @test */
    public function it_gathers_data_and_feeds_it_to_the_export_clerk()
    {
        $subscriberMock = Mockery::mock('Mage_Newsletter_Model_Subscriber');
        $subscriberMock->shouldReceive('load', array('foo@bar.baz', 'subscriber_email'))->once()->andReturn($subscriberMock);
        $subscriberMock->shouldReceive('getId')->once()->andReturn(123);

        $orderMock = Mockery::mock('OrderSpy');
        $orderMock->shouldReceive('getCustomerEmail')->once()->andReturn('foo@bar.baz');

        $clerkMock = Mockery::mock('KL_Rulemailer_Model_Export_Clerk');
        $clerkMock->shouldReceive('conductExport', $orderMock)->atLeast()->once();


        $collectionSpy = Mockery::mock('CollectionSpy');
        $collectionSpy->shouldReceive('addAttributeToFilter')->once()->andReturn($collectionSpy);
        $collectionSpy->shouldReceive('addAttributeToSelect')->once()->andReturn($collectionSpy);
        $collectionSpy->shouldReceive('setOrder')->once()->andReturn(array($orderMock));

        $orderMock->shouldReceive('getCollection')->once()->andReturn($collectionSpy);
        $orderMock->shouldReceive('getId');


        $manager = new KL_Rulemailer_Model_Export_Manager(
            $subscriberMock,
            1,
            $clerkMock,
            $orderMock
        );

        $manager->exportData();
    }
}

interface CollectionSpy
{
//    public function getIterator();

    public function addAttributeToFilter();
    public function addAttributeToSelect();
    public function setOrder();
}

interface OrderSpy
{
    public function getCollection();
    public function getId();
}
 