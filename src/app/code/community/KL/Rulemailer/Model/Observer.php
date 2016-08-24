<?php

class KL_Rulemailer_Model_Observer
{
    const CART_IN_PROGRESS_TAG = "CartInProgress";
    const NEWSLETTER_TAG = "Newsletter";
    const COMPLETE_ORDER_TAG = "Order";

    /**
     * @var null
     */
    private $apiSubscriber;

    /**
     * @param null $apiSubscriber
     */
    public function __construct($apiSubscriber = null)
    {
        $this->apiSubscriber = $apiSubscriber;
        $this->fieldsBuilder = new KL_Rulemailer_Model_Export_FieldsBuilder;
    }

    /**
     * Function for handling observer logging
     *
     * @param mixed $data
     *
     * @return void
     */
    private function logData($data)
    {
        if (Mage::getSingleton('rulemailer/config')->get('logging') == '1') {
            // Fetch backtrace
            $callers = debug_backtrace();

            // Setup caller
            $caller = $callers[1]['class'] . '::' . $callers[1]['function'];

            // Convert to string of not a string
            if (!is_string($data)) {
                $data = var_export($data, TRUE);
            }

            // Log the entry
            Mage::log('(' . $caller . ') ' . $data, NULL, 'KL_Rulemailer.log', TRUE);
        }
    }

    public function processCart(Varien_Event_Observer $observer)
    {
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $customer = Mage::getSingleton('customer/session')->getCustomer();
            $quote = $observer->getEvent()->getCart()->getQuote();

            $fields = array_merge(
                $this->fieldsBuilder->extractCustomerFields($customer),
                $this->fieldsBuilder->extractCartFields($quote)
            );

            $this->addSubscriber($customer->getEmail(), array(self::CART_IN_PROGRESS_TAG), $fields, true, true, true);
        }
    }

    /**
     * Manage subscription
     *
     * @param Varien_Event_Observer $observer
     *
     * @return Varien_Event_Observer
     */
    public function processSubscriber(Varien_Event_Observer $observer)
    {
        try {
            $event = $observer->getEvent();
            $subscriber = $event->getDataObject();
            $data = $subscriber->getData();

            /**
             * Check for customer number
             */
            if (!isset($data['customer_id']) || $data['customer_id'] == '0' || !$data['customer_id']) {
                /**
                 * Make sure a e-mail address was given
                 */
                if (isset($data['subscriber_email']) && $data['subscriber_email']) {
                    /**
                     * No customer was found, create a dummy customer object
                     */
                    $dummyCustomer = Mage::getModel('customer/customer');

                    /**
                     * Populate the object
                     */
                    $dummyCustomer->setEmail($data['subscriber_email']);

                    /**
                     * Add new subscriber
                     */
                    $this->addSubscriber($dummyCustomer, array('newsletter'), array());
                }
            } else {

                /**
                 * Load customer object
                 */
                $customer = Mage::getModel('customer/customer')->load($data['customer_id']);

                if ($subscriber->isSubscribed()) {
                    // Fetch address
                    $addressId = $customer->getDefaultBillingAddress();

                    $fields = $this->fieldsBuilder->extractCustomerFields($customer);
                    // Add or update
                    $this->addSubscriber($customer, array("newsletter"), $fields);

                } else {
                    // Remove
                    $this->logData("Removing subscriber " . $customer->getData('email'));
                    $this->removeSubscriber($customer);
                }
            }
        } catch (Exception $e) {
            $this->logData('Exception: ' . $e->getMessage());
        }

        /**
         * Nothing more to do
         */
        return $observer;
    }

    /**
     * Create RULE subscriber or update existing on customer save
     *
     * @param Varien_Event_Observer $observer
     * @return Varien_Event_Observer
     */
    public function processCustomer(Varien_Event_Observer $observer)
    {
        try {
            // Fetch observer data
            $data = $observer
                ->getEvent()
                ->getDataObject()
                ->getData();

            // Load customer object
            $customer = $observer->getCustomer();
            // Update if it's an object
            if (is_object($customer)) {

                // Check subscription status
                $newsletter = Mage::getModel('newsletter/subscriber')->loadByCustomer($customer);

                // Make sure we'd like to receive newsletters
                if ($newsletter->getData('subscriber_status') == '1') {
                    // Set the data fields of the address
                    $fields = Mage::getModel('customer/address')->load($data['entity_id'])->getData();

                    $this->logData(json_encode($fields));
                    // Add or update the subscriber
                    $this->addSubscriber($customer, array(), $fields);
                } else {
                    $this->logData("Removing subscriber " . $customer->getData('email'));
                    $this->removeSubscriber($customer);
                }
            }
        } catch (Exception $e) {
            $this->logData('Exception: ' . $e->getMessage());
        }

        return $observer;
    }

    /**
     * Get API subscriber model
     *
     * @return KL_Rulemailer_Model_Api_Subscriber
     */
    private function getApiSubscriber()
    {
        return $this->apiSubscriber ? : Mage::getModel('rulemailer/api_subscriber', null);
    }

    /**
     * Function for adding or updating subscriber
     *
     * @param Mage_Customer_Model_Customer $customer
     * @param array                        $tags
     * @param array                        $fields
     *
     * @return void
     */
    public function addSubscriber($customer, array $tags, $fields = null)
    {
        $response = $this->getApiSubscriber()->create($customer->getData('email'), $tags, $fields, true, true, true);

        if ($response->isError()) {
            $this->logData("When adding subscriber (" . $customer->getData('email') . "), get code error: " . $response->getError());
            $this->logData('Trying to add tag...');
            $response = $this->getApiSubscriber()->addTag($tags, $customer->getData('email'), 'email');
            if ($response->isError()) {
                $this->logData("When adding tag to subscriber (" . $customer->getData('email') . "), get code error: " . $response->getError());
            }
        }

        $this->logData("Added new subscriber " . $customer->getData('email'));
    }


    /**
     * Function for adding or updating subscriber
     *
     * @param Mage_Customer_Model_Customer $customer
     *
     * @return void
     */
    public function removeSubscriber($customer)
    {
        $response = $this->getApiSubscriber()->removeTag('newsletter', $customer->getData('email'));

        if ($response->isError()) {
           return  $this->logData("When removing subscriber tag (" . $customer->getData('email') . "), got code error: " . $response->getError());
        }

        $this->logData("Removed tag newsletter from subscriber " . $customer->getData('email'));
    }
}
