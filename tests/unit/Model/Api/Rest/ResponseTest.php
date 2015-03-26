<?php

class ResponseTest extends PHPUnit_Framework_TestCase
{
    /** @test */
    public function it_determines_a_successful_request_through_the_guzzle_response_interface()
    {
        $responseMock = Mockery::mock('GuzzleHttp\Message\ResponseInterface');
        $responseMock->shouldReceive('getStatusCode')->once()->andReturn(200);
        $responseMock->shouldReceive('json')->once();
        $response = new KL_Rulemailer_Model_Api_Rest_Response($responseMock);

        $this->assertTrue($response->isSuccess());
        $this->assertFalse($response->isError());
    }

    /** @test */
    public function it_determines_a_failed_request_through_the_guzzle_response_interface()
    {
        $responseMock = Mockery::mock('GuzzleHttp\Message\ResponseInterface');
        $responseMock->shouldReceive('getStatusCode')->once()->andReturn(400);
        $responseMock->shouldReceive('json')->once();
        $response = new KL_Rulemailer_Model_Api_Rest_Response($responseMock);

        $this->assertTrue($response->isError());
        $this->assertFalse($response->isSuccess());
    }

}
 