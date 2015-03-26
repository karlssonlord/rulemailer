<?php

class GuzzleClientTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
    }

    /** @test */
    public function it_is_initializable()
    {
        $client = new KL_Rulemailer_Model_Api_Rest_GuzzleClient;

        $this->assertInstanceOf('KL_Rulemailer_Model_Api_Rest_Client', $client);
    }

    /** @test */
    public function it_calls_the_post_method_on_the_guzzle_client()
    {
        $guzzleResponse = Mockery::mock('GuzzleHttp\Message\Response');
        $guzzleResponse->shouldReceive('getStatusCode')->once()->andReturn('foo');
        $guzzleResponse->shouldReceive('getReasonPhrase')->once()->andReturn('bar');

        $guzzleMock = Mockery::mock('GuzzleHttp\ClientInterface');
        $guzzleMock->shouldReceive('post')->once()->andReturn($guzzleResponse);
        $client = new KL_Rulemailer_Model_Api_Rest_GuzzleClient($guzzleMock);
        $response = $client->post('/test', array());

        $this->assertEquals('foo', $response->getStatusCode());
        $this->assertEquals('bar', $response->getReasonPhrase());
    }

    /** @test */
    public function it_calls_the_get_method_on_the_guzzle_client()
    {
        $guzzleResponse = Mockery::mock('GuzzleHttp\Message\Response');
        $guzzleResponse->shouldReceive('getStatusCode')->once()->andReturn('foo');
        $guzzleResponse->shouldReceive('getReasonPhrase')->once()->andReturn('bar');

        $guzzleMock = Mockery::mock('GuzzleHttp\ClientInterface');
        $guzzleMock->shouldReceive('get')->once()->andReturn($guzzleResponse);
        $client = new KL_Rulemailer_Model_Api_Rest_GuzzleClient($guzzleMock);
        $response = $client->get('/test', array());

        $this->assertEquals('foo', $response->getStatusCode());
        $this->assertEquals('bar', $response->getReasonPhrase());
    }

    /** @test */
    public function it_calls_the_put_method_on_the_guzzle_client()
    {
        $guzzleResponse = Mockery::mock('GuzzleHttp\Message\Response');
        $guzzleResponse->shouldReceive('getStatusCode')->once()->andReturn('foo');
        $guzzleResponse->shouldReceive('getReasonPhrase')->once()->andReturn('bar');

        $guzzleMock = Mockery::mock('GuzzleHttp\ClientInterface');
        $guzzleMock->shouldReceive('put')->once()->andReturn($guzzleResponse);
        $client = new KL_Rulemailer_Model_Api_Rest_GuzzleClient($guzzleMock);
        $response = $client->put('/test', array());

        $this->assertEquals('foo', $response->getStatusCode());
        $this->assertEquals('bar', $response->getReasonPhrase());
    }

    /** @test */
    public function it_calls_the_delete_method_on_the_guzzle_client()
    {
        $guzzleResponse = Mockery::mock('GuzzleHttp\Message\Response');
        $guzzleResponse->shouldReceive('getStatusCode')->once()->andReturn('foo');
        $guzzleResponse->shouldReceive('getReasonPhrase')->once()->andReturn('bar');

        $guzzleMock = Mockery::mock('GuzzleHttp\ClientInterface');
        $guzzleMock->shouldReceive('delete')->once()->andReturn($guzzleResponse);
        $client = new KL_Rulemailer_Model_Api_Rest_GuzzleClient($guzzleMock);
        $response = $client->delete('/test', array());

        $this->assertEquals('foo', $response->getStatusCode());
        $this->assertEquals('bar', $response->getReasonPhrase());
    }

}

 