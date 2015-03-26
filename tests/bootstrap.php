<?php

// Try to get data off a local .env file
try { Dotenv::load(__DIR__ . '/../');} catch (Exception $e) {}

require __DIR__ . '/../vendor/autoload.php';

Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
Mage::getModel('core/config')->saveConfig('kl_rulemailer_settings/general/key', getenv('RULEMAILER_APIKEY'));
Mage::getConfig()->reinit();
Mage::app()->reinitStores();