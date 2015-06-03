<?php

/**
 * Class KL_Rulemailer_Model_Api_Rest
 * @package KL_Rulemailer
 * @author David Wickström <david@karlssonlord.com>
 */
abstract class KL_Rulemailer_Model_Api_Rest
{
    /**
     * @var KL_Rulemailer_Model_Api_Rest_Client
     */
    protected $client;

    /**
     * @param null                                $foo
     * @param KL_Rulemailer_Model_Api_Rest_Client $client
     */
    public function __construct($foo = null, KL_Rulemailer_Model_Api_Rest_Client $client = null)
    {
        $this->client = $client ? : new KL_Rulemailer_Model_Api_Rest_GuzzleClient;
    }

}
