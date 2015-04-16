<?php

/**
 * Class KL_Rulemailer_Model_Export_Clerk
 * @author David Wickström <david@karlssonlord.com>
 */
class KL_Rulemailer_Model_Export_Clerk
{
    /**
     * @var KL_Rulemailer_Model_Api_Subscriber
     */
    private $remoteSubscriber;
    /**
     * @var KL_Rulemailer_Model_Export_FieldsBuilder
     */
    private $fieldsBuilder;

    /**
     * @param KL_Rulemailer_Model_Api_Subscriber       $api
     * @param KL_Rulemailer_Model_Export_FieldsBuilder $fieldsBuilder
     */
    public function __construct(KL_Rulemailer_Model_Api_Subscriber $api = null, KL_Rulemailer_Model_Export_FieldsBuilder $fieldsBuilder = null)
    {
        $this->remoteSubscriber = $api ? : Mage::getModel('rulemailer/api_subscriber', null);
        $this->fieldsBuilder = $fieldsBuilder ? : new KL_Rulemailer_Model_Export_FieldsBuilder;
    }

    /**
     *  Accepts the order and performs the data export via the RuleMailer API
     *
     * @param Mage_Sales_Model_Order $order
     */
    public function conductExport(Mage_Sales_Model_Order $order)
    {
        $this->updateFields($this->fieldsBuilder->extractFields($order), $order->getCustomerEmail());
    }

    /**
     *  The method that communicates to update the fields via the RuleMailer API
     *
     * @param array $fields
     * @param       $customerEmail
     * @throws InvalidArgumentException
     * @throws KL_Rulemailer_Model_Api_Rest_Exception
     */
    protected function updateFields(array $fields, $customerEmail)
    {
        // Filter valid email addresses
        if (!filter_var($customerEmail, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("The supplied customer email was not valid: {$customerEmail}");
        }

        // Create JSON object & make API call
        $response = $this->remoteSubscriber->create($customerEmail, array('newsletter'), $fields);

        // Alert if all is not well
        if ($response->isError()) {
            throw new KL_Rulemailer_Model_Api_Rest_Exception($response->getError());
        }
    }

}
