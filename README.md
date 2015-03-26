Karlsson & Lord Rulemailer module
=================================
[![Build Status](https://travis-ci.org/karlssonlord/rulemailer.svg)](https://travis-ci.org/karlssonlord/rulemailer)

Magento module for integrating Rulemailer into Magento CE/EE.

Requirements
------------
PHP 5.4 or above
Magento 1.9.* - earlier versions might work, but remains untested

Install
-------

Move files to the right place or use modman/composer to deploy them. Flush the cache and logout admin user before visiting the configuration section in admin backend.

### Composer

    composer install --no-dev

### Modman

    modman deploy KL_Rulemailer

Usage
-----

1. Login to admin
1. System menu &rarr; Configuration &rarr; RuleMailer (to the left)
1. Enter your API key and save settings
1. Save settings
