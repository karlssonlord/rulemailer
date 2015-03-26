<?php

class CustomizationsTest extends PHPUnit_Framework_TestCase
{
    /** @test */
    public function it_calls_the_right_method_on_the_client_collaborator()
    {
        $responseMock = Mockery::mock('GuzzleHttp\Message\ResponseInterface');
        $responseMock->shouldReceive('json')->once();
        $responseMock->shouldReceive('getStatusCode')->once()->andReturn(200);

        $clientMock = Mockery::mock('KL_Rulemailer_Model_Api_Rest_Client');
        $clientMock->shouldReceive('get')->with('customizations', array('page' => 1))->once()->andReturn($responseMock);

        $rest = new KL_Rulemailer_Model_Api_Customizations($clientMock);
        $response = $rest->findAll(1);

        $this->assertInstanceOf('KL_Rulemailer_Model_Api_Rest_Response', $response);
        $this->assertTrue($response->isSuccess());
    }

}
 