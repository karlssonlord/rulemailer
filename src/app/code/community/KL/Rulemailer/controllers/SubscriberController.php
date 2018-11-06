<?php

class KL_Rulemailer_SubscriberController extends Mage_Core_Controller_Front_Action
{
    /**
     * Un-subscribes a subscription by the email address
     */
    public function unsubscribeAction()
    {
        $emailAddress = Mage::app()->getRequest()->getParam('email');

        if ($emailAddress) {
            $session = Mage::getSingleton('core/session');
            try {
                $subscriber = Mage::getModel('newsletter/subscriber')->loadByEmail($emailAddress);

                if ($subscriber) {
                    $subscriber->unsubscribe();
                }
                $session->addSuccess($this->__('You have been unsubscribed.'));
            }
            catch (Mage_Core_Exception $e) {
                $session->addException($e, $e->getMessage());
            }
            catch (Exception $e) {
                $session->addException($e, $this->__('There was a problem with the un-subscription.'));
            }
        }
        $this->_redirectReferer();
    }
}
