<?php

class KL_Rulemailer_Model_Config
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
     * @throws InvalidArgumentException if the given option does not exists.
     * @return string
     */
    public function get($key, $group = 'general')
    {
        $configKey = join(array(self::SECTION, $group, $key), '/');

        if (!is_null(Mage::getStoreConfig($configKey))) {
            return Mage::getStoreConfig($configKey);
        } else {
            throw new InvalidArgumentException(
                'invalid config key: ' . $configKey
            );
        }

    }
}
