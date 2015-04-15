<?php

class KL_Rulemailer_Model_Config extends KL_Rulemailer_Model_Abstract
{
    /**
     * Configuration section.
     *
     * @var string
     */
    const SECTION = 'kl_rulemailer_settings';

    /**
     * Get a module specific configuration option.
     *
     * @param string $key The option name.
     * @param string $group The option group.
     *                      
     * @return string
     */
    public function get($key, $group = 'general')
    {
        $configKey = join(array(self::SECTION, $group, $key), '/');

        return Mage::getStoreConfig($configKey);
    }
}
