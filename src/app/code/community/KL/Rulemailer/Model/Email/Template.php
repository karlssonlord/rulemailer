<?php

/**
 * Email template model
 *
 * @category KL
 * @package  KL_Rulemailer
 */
class KL_Rulemailer_Model_Email_Template
    extends Mage_Core_Model_Email_Template
{
    protected $_mail;

    public function encode($data)
    {
        return base64_encode($data);
    }

    /**
     * Send one or multiple emails
     *
     * Returns true if sent successfully, otherwise it returns false.
     *
     * @param array|string $email     Receiver email(s)
     * @param array|string $name      Receiver name(s)
     * @param array        $variables Template variables
     *
     * @return bool
     *
     * @access public
     */
    public function send($email, $name = null, array $variables = array())
    {
        if (!Mage::getSingleton('rulemailer/config')->get('transactional')) {
            Mage::log('Transactionals not enabled.', null, 'api.log', true);
            return parent::send($email, $name, $variables);
        }

        if (!$this->isValidForSend()) {
            Mage::logException(new Exception('This letter cannot be sent.'));
            return false;
        }

        $emails = array_values((array)$email);
        $names  = is_array($name) ? $name : (array)$name;
        $names  = array_values($names);

        foreach ($emails as $key => $email) {
            if (!isset($names[$key])) {
                $names[$key] = substr($email, 0, strpos($email, '@'));
            }
        }

        $this->setUseAbsoluteLinks(true);
        $text = $this->getProcessedTemplate($variables);

        foreach ($emails as $key => $email) {
            try {
                    $result = Mage::getModel('rulemailer/api_transactional', null)->sendEmail(
                        $this->getProcessedTemplateSubject($variables),
                        array('name' => $this->getSenderName(), 'email' => $this->getSenderEmail()),
                        array('name' => $names[$key], "email" => $email),
                        array('html' => base64_encode($text), 'plain' => base64_encode($text)),
                        '',
                        false
                    );
            } catch(Exception $e) {
                Mage::log($e->getMessage(), null, 'result.log', true);
            }
        }

        return true;
    }

    /**
     * Retrieve mail object instance
     *
     * @return KL_Rulemailer_Model_Email|Zend_Mail
     *
     * @access public
     */
    public function getMail()
    {
        if (is_null($this->_mail)) {
            if (Mage::getSingleton('rulemailer/config')->get('transactional')) {
                $this->_mail = Mage::getModel('rulemailer/email');
            } else {
                $this->_mail = parent::getMail();
            }
        }

        return $this->_mail;
    }

}
