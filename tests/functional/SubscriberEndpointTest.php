<?php

class SubscriberEndpointTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
    }

    /**
     * @test
     * @vcr subscriber_add_new.yml
     */
    public function it_can_add_a_new_subscriber()
    {
        $subscriberEndpoint = new KL_Rulemailer_Model_Api_Subscriber;
        $response = $subscriberEndpoint->create('david@karlssonlord.com');

        $this->assertEquals(200, $response->isSuccess());
    }

    /**
     * @test
     * @vcr subscriber_find_all.yml
     */
    public function it_can_fetch_all_subscribers()
    {
        $subscriberEndpoint = new KL_Rulemailer_Model_Api_Subscriber;
        $response = $subscriberEndpoint->findAll();

        $this->assertEquals(200, $response->isSuccess());
        $this->assertEquals(29, $response->toValue()['number_of_subscribers']);
    }

    /**
     * @test
     * @vcr subscriber_find_one.yml
     */
    public function it_fetch_a_single_subscriber()
    {
        $subscriberEndpoint = new KL_Rulemailer_Model_Api_Subscriber;
        $response = $subscriberEndpoint->findOne('david@karlssonlord.com');

        $this->assertEquals(200, $response->isSuccess());
        $this->assertEquals('david@karlssonlord.com', $response->toValue()['subscriber']['email']);
    }

    /**
     * @test
     * @vcr subscriber_add_tag.yml
     */
    public function it_can_add_a_tag_to_a_single_subscriber()
    {
        $subscriberEndpoint = new KL_Rulemailer_Model_Api_Subscriber;
        $response = $subscriberEndpoint->addTag(array('newsletter'), 'david@karlssonlord.com');

        $this->assertEquals('Success', $response->toValue()['message']);
    }

    /**
     * @test
     * @vcr subscriber_list_tags.yml
     */
    public function it_can_list_all_tags_given_a_subscriber()
    {
        $subscriberEndpoint = new KL_Rulemailer_Model_Api_Subscriber;
        $response = $subscriberEndpoint->listTags('david@karlssonlord.com');

        $this->assertEquals('newsletter', $response->toValue()['tags'][0]['name']);
    }

    /**
     * @test
     * @vcr subscriber_remove_tag.yml
     */
    public function it_can_remove_tags_given_a_subscriber()
    {
        $subscriberEndpoint = new KL_Rulemailer_Model_Api_Subscriber;
        $response = $subscriberEndpoint->removeTag('test','david@karlssonlord.com');

        $this->assertEquals('Success', $response->toValue()['message']);
    }

}
 