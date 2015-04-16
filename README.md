# Karlsson & Lord RuleMailer Magento Extension

[![Build Status](https://travis-ci.org/karlssonlord/rulemailer.svg)](https://travis-ci.org/karlssonlord/rulemailer)

Magento module for integrating RuleMailer into Magento CE/EE.

## Requirements

- PHP 5.4.* or PHP 5.5.*
- Magento 1.9.* - earlier versions might work, but remains untested and those version do not officially support PHP 5.4 either...

## Install

Use [Composer](http://getcomposer.org) to install this module's dependencies.

    composer install --no-dev

Use [Modman](https://github.com/colinmollenhour/modman) or [Composer](https://github.com/Cotya/magento-composer-installer) to deploy them this module into your Magento app. Flush the cache and logout admin user before visiting the configuration section in admin backend.

## Module assumes
#### MAGENTO_ROOT placement
Currently this module makes the assumption that the MAGENTO_ROOT is place in a subdirectory from your project root.

Either like this

	src/{MAGENTO_ROOT}

...or like this

	src/magento/{MAGENTO_ROOT}

Directory names won't matter.

#### Cron setup
It also assumes that the *Magento cron* is running on your server. Ohterwise there will be no customer data exported to Rule, other than the subscriber email.

Example crontab for Unix type servers:

	*/5 * * * *  /bin/sh /absolute/path/to/magento/cron.sh


## Usage

1. Login to admin
1. System menu &rarr; Configuration &rarr; RuleMailer (to the left)
1. Enter your API key and save settings
1. Save settings

### TODO

- Develop installation instructions
- Have Travis test multiple Magento versions
- Record customer state
