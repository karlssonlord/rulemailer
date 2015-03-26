<?php

/**
 * Class KL_Rulemailer_Model_Api_Transactional
 * @package KL_Rulemailer
 * @author Andreas Karlsson <andreas@karlssonlord.com>
 * @author David Wickström <david@karlssonlord.com>
 */
class KL_Rulemailer_Model_Api_Transactional extends KL_Rulemailer_Model_Api_Rest
{
    /**
     *  Endpoint
     */
    const TRANSACTIONALS_ENDPOINT = 'transactionals';
    /**
     *  Email
     */
    const EMAIL = 'email';
    /**
     *  Text message
     */
    const TEXT_MESSAGE = 'text_message';

    /**
     * Post a transactional email
     *
     * @param string  $subject
     * @param array   $from
     * @param array   $to
     * @param array   $content
     * @param string  $transactionName
     * @param boolean $async
     *
     * @throws KL_Rulemailer_Model_Api_Rest_Exception
     * @return \KL_Rulemailer_Model_Api_Rest_Response
     */
    public function sendEmail($subject, array $from, array $to, array $content, $transactionName = '', $async = false)
    {
        $this->validateEmailParams($from, $to, $content);

        $data = array();
        $data['subject'] = $subject;
        $data['from']    = $from;
        $data['to']      = $to;
        $data['content'] = $content;
        $data['transaction_type'] = self::EMAIL;

        if ($transactionName !== '') {
            $data['transaction_name'] = $transactionName;
        }

        $response = $this->client->post(static::TRANSACTIONALS_ENDPOINT, $data);
        return new KL_Rulemailer_Model_Api_Rest_Response($response);
    }

    /**
     * Post a transactional text message
     *
     * @param array   $to
     * @param array   $content
     * @param string  $transactionName
     * @param boolean $async
     *
     * @throws KL_Rulemailer_Model_Api_Rest_Exception
     * @return \KL_Rulemailer_Model_Api_Rest_Response
     */
    public function sendTextMessage(array $to, array $content, $transactionName = '', $async = false)
    {
        $this->validateTextMessageParams($to, $content);

        $data = array();
        $data['to']      = $to;
        $data['content'] = $content;
        $data['transaction_type'] = self::TEXT_MESSAGE;

        if ($transactionName !== '') {
            $data['transaction_name'] = $transactionName;
        }

        $response = $this->client->post(self::TRANSACTIONALS_ENDPOINT, $data);
        return new KL_Rulemailer_Model_Api_Rest_Response($response);
    }

    /**
     * @param array $from
     * @param array $to
     * @param array $content
     * @throws KL_Rulemailer_Model_Api_Rest_Exception
     */
    private function validateEmailParams(array $from, array $to, array $content)
    {
        if (!array_key_exists('name', $from)) {
            throw new KL_Rulemailer_Model_Api_Rest_Exception(
                'Required value "name" is missing for the sender'
            );
        }

        if (!array_key_exists('email', $from)) {
            throw new KL_Rulemailer_Model_Api_Rest_Exception(
                'Required value "email" is missing for the sender'
            );
        }

        if (!array_key_exists('email', $to)) {
            throw new KL_Rulemailer_Model_Api_Rest_Exception(
                'Required value "email" is missing for the recipient'
            );
        }

        if (!array_key_exists('html', $content)) {
            throw new KL_Rulemailer_Model_Api_Rest_Exception(
                'Required content in HTML format is missing'
            );
        }

        if (!array_key_exists('plain', $content)) {
            throw new KL_Rulemailer_Model_Api_Rest_Exception(
                'Required content in plain text format is missing'
            );
        }
    }

    /**
     * @param array $to
     * @param array $content
     * @throws KL_Rulemailer_Model_Api_Rest_Exception
     */
    private function validateTextMessageParams(array $to, array $content)
    {
        if (!array_key_exists('phone_number', $to)) {
            throw new KL_Rulemailer_Model_Api_Rest_Exception(
                'Required "phone_number" is missing for the recipient'
            );
        }

        if (!array_key_exists('text_message', $content)) {
            throw new KL_Rulemailer_Model_Api_Rest_Exception(
                'Required "text_message" is missing'
            );
        }
    }

}
