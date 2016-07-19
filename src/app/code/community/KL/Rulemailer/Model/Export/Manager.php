<?php

/**
 * Class KL_Rulemailer_Model_Export_Manager
 */
class KL_Rulemailer_Model_Export_Manager
{
    /**
     * @var false|Mage_Core_Model_Abstract
     */
    private $newsletterSubscriber;
    /**
     * @var Mage_Core_Model_Abstract
     */
    private $lastOrderId;

    /**
     * @var false|Mage_Core_Model_Abstract
     */
    private $exportClerk;

    /**
     * @var false|Mage_Core_Model_Abstract
     */
    private $order;

    /**
     * @var null
     */
    private $lastExported = null;

    /**
     * @param null $newsletterSubscriber
     * @param null $lastOrderId
     * @param null $exportClerk
     * @param null $order
     */
    public function __construct($newsletterSubscriber = null, $lastOrderId = null, $exportClerk = null, $order = null)
    {
        $this->newsletterSubscriber = $newsletterSubscriber ? : Mage::getModel('newsletter/subscriber');
        $this->lastOrderId = $lastOrderId ? : Mage::getStoreConfig('kl_rulemailer/last_exported/order_id');
        $this->exportClerk = $exportClerk ? : Mage::getModel('rulemailer/export_clerk', null);
        $this->order = $order ? : Mage::getModel('sales/order');
    }

    /**
     *  For every order not previously exported
     */
    public function exportData()
    {
        $orders = $this->getOrdersCollection();
        if (count($orders) === 0) return;
        foreach ($orders as $order) {
            try {
                $this->exportClerk->conductExport($order);
            } catch (Exception $e) {
                Mage::log($e->getMessage(), null, 'KL_Rulemailer.log', true);
            }

            $this->lastExported = $order->getId();
        }
        // Remember which order was the last one to have been exported
        Mage::getModel('core/config')->saveConfig('kl_rulemailer/last_exported/order_id', $this->lastExported);
    }

    /**
     * @param $email
     * @return bool
     */
    private function isOrderedBySubscriber($email)
    {
        $emailExist = $this->newsletterSubscriber->load($email, 'subscriber_email');

        if ($emailExist->getId()) {
            return true;
        }
        return false;
    }

    /**
     * @return mixed
     */
    private function getOrdersCollection()
    {
        $lastExported = Mage::getStoreConfig('kl_rulemailer/last_exported/order_id') ? : 0;

        return $this->order
            ->getCollection()
            ->addAttributeToFilter('entity_id', array('gt' => $lastExported))
            ->addAttributeToSelect('*')
            ->setOrder('entity_id', 'ASC')
        ;
    }

}