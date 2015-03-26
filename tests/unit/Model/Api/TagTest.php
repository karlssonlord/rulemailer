<?php

class TagTest extends PHPUnit_Framework_TestCase
{
    /** @test */
    public function it_can_return_all_tags_for_a_particular_customer_account()
    {
        $responseMock = Mockery::mock('GuzzleHttp\Message\ResponseInterface');
        $responseMock->shouldReceive('json')->once();
        $responseMock->shouldReceive('getStatusCode')->once()->andReturn(200);

        $clientMock = Mockery::mock('KL_Rulemailer_Model_Api_Rest_Client');
        $clientMock->shouldReceive('get')->with('tags', array('page' => 1))->once()->andReturn($responseMock);

        $subscribe = new KL_Rulemailer_Model_Api_Tag($clientMock);
        $response = $subscribe->findAll(1);

        $this->assertTrue($response->isSuccess());
    }

}
 