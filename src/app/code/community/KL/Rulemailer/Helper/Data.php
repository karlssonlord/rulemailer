<?php
/**
 * RuleMailer
 */

/**
 * Data helper
 */
class KL_Rulemailer_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Check if the system is running on an old PHP version
     *
     * @return bool
     */
    public function isOldPhpVersion()
    {
        /**
         * Explode the version
         */
        $version = explode('.', phpversion());

        /**
         * If it's not PHP5
         * Not sure how it will work, but let's give it a try
         */
        if ($version[0] <= 4) {
            return true;
        }

        /**
         * If it's less than 5.2
         * Some functions has changed in PHP Version 5.3
         */
        if ($version[1] <= 2) {
            return true;
        } else {
            return false;
        }
    }

}
