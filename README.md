# Karlsson & Lord RuleMailer Magento Extension

[![Build Status](https://travis-ci.org/karlssonlord/rulemailer.svg)](https://travis-ci.org/karlssonlord/rulemailer)

Magento module for integrating RuleMailer into Magento CE/EE.

## Requirements

- PHP 5.4.* or PHP 5.5.*
- Magento 1.9.* - earlier versions might work, but remains untested and those version do not officially support PHP 5.4 either...

## Install

This module can be installed which ever way you like, either through modman or composer, or if you want to you can manually place the files into the correct places.

#### Cron setup
This module assumes that the *Magento cron* is running on your server. Otherwise there will be no customer data exported to Rule, other than the subscriber email.

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
