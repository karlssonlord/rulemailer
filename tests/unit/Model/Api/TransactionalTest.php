<?php

class TransactionalTest extends PHPUnit_Framework_TestCase
{
    /** @test */
    public function it_sends_an_email()
    {
        $responseMock = Mockery::mock('GuzzleHttp\Message\ResponseInterface');
        $responseMock->shouldReceive('json')->once();
        $responseMock->shouldReceive('getStatusCode')->once()->andReturn(200);

        $clientMock = Mockery::mock('KL_Rulemailer_Model_Api_Rest_Client');
        $clientMock->shouldReceive('post')->with('transactionals', array(
                'subject' => 'Foo subject',
                'from' => array('name' => 'hans', 'email' => 'hans@hans.net'),
                'to' => array('name' => 'fritz', 'email' => 'fritz@fritz.net'),
                'content' => array('html' => '<html>foo</html>', 'plain' => 'foo'),
                'transaction_type' => 'email'
            )
        )->once()->andReturn($responseMock);

        $subject = 'Foo subject';
        $from = array(
            'name' => 'hans',
            'email' => 'hans@hans.net'
        );
        $to = array(
            'name' => 'fritz',
            'email' => 'fritz@fritz.net'
        );
        $content = array(
            'html' => '<html>foo</html>',
            'plain' => 'foo'
        );
        $subscribe = new KL_Rulemailer_Model_Api_Transactional($clientMock);
        $response = $subscribe->sendEmail($subject, $from, $to, $content);


        $this->assertTrue($response->isSuccess());
    }

    /** @test */
    public function it_sends_a_text_message()
    {
        $responseMock = Mockery::mock('GuzzleHttp\Message\ResponseInterface');
        $responseMock->shouldReceive('json')->once();
        $responseMock->shouldReceive('getStatusCode')->once()->andReturn(200);

        $clientMock = Mockery::mock('KL_Rulemailer_Model_Api_Rest_Client');
        $clientMock->shouldReceive('post')->with('transactionals', array(
                'to' => array('name' => 'fritz', 'phone_number' => '0707-080808'),
                'content' => array('text_message' => 'foo'),
                'transaction_type' => 'text_message'
            )
        )->once()->andReturn($responseMock);

        $to = array(
            'name' => 'fritz',
            'phone_number' => '0707-080808'
        );
        $content = array(
            'text_message' => 'foo'
        );
        $subscribe = new KL_Rulemailer_Model_Api_Transactional($clientMock);
        $response = $subscribe->sendTextMessage($to, $content);


        $this->assertTrue($response->isSuccess());
    }

}
 