<?php

class SubscriberTest extends PHPUnit_Framework_TestCase
{
    /** @test */
    public function it_can_create_a_new_subscriber()
    {
        $responseMock = Mockery::mock('ResponseInterface');
        $responseMock->shouldReceive('json')->once();
        $responseMock->shouldReceive('getStatusCode')->once()->andReturn(200);

        $clientMock = Mockery::mock('KL_Rulemailer_Model_Api_Rest_Client');
        $clientMock->shouldReceive('post')->with('subscribers', array(
                'email' => 'foo@bar.com',
                'tags' => array('baz'),
                'fields' => array(),
                'update_on_duplicate' => true,
                'auto_create_tags' => true,
                'auto_create_fields' => true
            ))->once()->andReturn($responseMock);

        $subscribe = new KL_Rulemailer_Model_Api_Subscriber(null, $clientMock);
        $response = $subscribe->create('foo@bar.com', array('baz'));

        $this->assertTrue($response->isSuccess());
    }

    /** @test */
    public function it_can_grab_all_current_subscribers_off_the_api()
    {
        $responseMock = Mockery::mock('ResponseInterface');
        $responseMock->shouldReceive('json')->once();
        $responseMock->shouldReceive('getStatusCode')->once()->andReturn(200);

        $clientMock = Mockery::mock('KL_Rulemailer_Model_Api_Rest_Client');
        $clientMock->shouldReceive('get')->with('subscribers', array(
                'page' => 1
            ))->once()->andReturn($responseMock);

        $subscribe = new KL_Rulemailer_Model_Api_Subscriber(null, $clientMock);
        $response = $subscribe->findAll(1);

        $this->assertTrue($response->isSuccess());
    }

    /** @test */
    public function it_can_fetch_a_single_subscriber_off_the_api()
    {
        $responseMock = Mockery::mock('ResponseInterface');
        $responseMock->shouldReceive('json')->once();
        $responseMock->shouldReceive('getStatusCode')->once()->andReturn(200);

        $clientMock = Mockery::mock('KL_Rulemailer_Model_Api_Rest_Client');
        $clientMock->shouldReceive('get')->with('subscribers/foo@bar.com', array(
                'identified_by' => 'email'
            )
        )->once()->andReturn($responseMock);

        $subscribe = new KL_Rulemailer_Model_Api_Subscriber(null, $clientMock);
        $response = $subscribe->findOne('foo@bar.com');

        $this->assertTrue($response->isSuccess());
    }

    /** @test */
    public function it_can_add_tags_to_a_single_subscriber()
    {
        $responseMock = Mockery::mock('ResponseInterface');
        $responseMock->shouldReceive('json')->once();
        $responseMock->shouldReceive('getStatusCode')->once()->andReturn(200);

        $clientMock = Mockery::mock('KL_Rulemailer_Model_Api_Rest_Client');
        $clientMock->shouldReceive('post')->with('subscribers/foo@bar.com/tags', array(
                'identifier' => 'foo@bar.com',
                'identified_by' => 'email',
                'tags' => array('foo', 'bar', 'baz')
            )
        )->once()->andReturn($responseMock);

        $subscribe = new KL_Rulemailer_Model_Api_Subscriber(null, $clientMock);
        $response = $subscribe->addTag(array('foo', 'bar', 'baz'), 'foo@bar.com');

        $this->assertTrue($response->isSuccess());
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function it_throws_an_exception_if_the_email_identifier_is_invalid()
    {
        $clientMock = Mockery::mock('KL_Rulemailer_Model_Api_Rest_Client');

        $subscribe = new KL_Rulemailer_Model_Api_Subscriber(null, $clientMock);
        $subscribe->create('foo');
    }

    /** @test */
    public function it_can_list_all_tags_off_a_given_subscriber()
    {
        $responseMock = Mockery::mock('ResponseInterface');
        $responseMock->shouldReceive('json')->once()->andReturn('boo');
        $responseMock->shouldReceive('getStatusCode')->once()->andReturn(200);

        $clientMock = Mockery::mock('KL_Rulemailer_Model_Api_Rest_Client');
        $clientMock->shouldReceive('get')->with('subscribers/foo@bar.com/tags', array(
                'identified_by' => 'email'
            )
        )->once()->andReturn($responseMock);

        $subscribe = new KL_Rulemailer_Model_Api_Subscriber(null, $clientMock);
        $response = $subscribe->listTags('foo@bar.com');

        $this->assertTrue($response->isSuccess());
        $this->assertEquals('boo', $response->toValue());
    }

    /** @test */
    public function it_can_remove_a_single_tag_off_a_subscriber()
    {
        $responseMock = Mockery::mock('ResponseInterface');
        $responseMock->shouldReceive('json')->once();
        $responseMock->shouldReceive('getStatusCode')->once()->andReturn(200);

        $clientMock = Mockery::mock('KL_Rulemailer_Model_Api_Rest_Client');
        $clientMock->shouldReceive('delete')->with('subscribers/foo@bar.com/tags/foo', array(
                'identified_by' => 'email'
            )
        )->once()->andReturn($responseMock);

        $subscribe = new KL_Rulemailer_Model_Api_Subscriber(null, $clientMock);
        $response = $subscribe->removeTag('foo', 'foo@bar.com');

        $this->assertTrue($response->isSuccess());
    }

}

interface ResponseInterface {
    public function json();
    public function getStatusCode();
}
 