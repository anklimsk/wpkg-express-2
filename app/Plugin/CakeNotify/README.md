# CakePHP 2.x Notification plugin
[![Build Status](https://travis-ci.com/anklimsk/cakephp-notify.svg?branch=master)](https://travis-ci.com/anklimsk/cakephp-notify)
[![Coverage Status](https://codecov.io/gh/anklimsk/cakephp-notify/branch/master/graph/badge.svg)](https://codecov.io/gh/anklimsk/cakephp-notify)
[![Latest Stable Version](https://poser.pugx.org/anklimsk/cakephp-notify/version)](https://packagist.org/packages/anklimsk/cakephp-notify)
[![License](https://poser.pugx.org/anklimsk/cakephp-notify/license)](https://packagist.org/packages/anklimsk/cakephp-notify)

Sending E-mail from CakePHP using task queues and sending a notification to the user using WEB Notifications API

## This plugin provides next features:

- Sending E-mail from CakePHP using task queues;
- Using Inline CSS style with `Twitter Bootstrap`;
- Notification to the user by ID or role using WEB Notifications API.

## Installation

1. Install the Plugin using composer: `composer require anklimsk/cakephp-notify`
2. Add the next line to the end of the file `app/Config/bootstrap.php`:

   ```php
   CakePlugin::load('CakeNotify', ['bootstrap' => true, 'routes' => true]);
   ```

3. Copy configuration file from `app/Plugin/CakeNotify/Config/email.php.defaultё to ёapp/Config/email.php`
4. Add `JavaScript` files in your layout:

   ```php
   echo $this->Html->script('CakeNotify.WebNotifications.min.js');

   // Add jQuery plugin Server-Sent Events
   echo $this->Html->script('CakeNotify.jquery.sse.min.js');

   // If need use store configuration of plugin in storages, include file:
   echo $this->Html->script('CakeNotify.js.storage.min.js');
   ```

5. Get the name of the user that is running the web server, run the command:
`ps aux | grep -E '[a]pache|[h]ttpd|[_]www|[w]ww-data|[n]ginx' | grep -v root | head -1 | cut -d\  -f1`.
6. Configure scripts to run schedule, run the command `crontab -u www-data -e` where
`www-data` - user name for web server.
7. Add the following line to the list of cron jobs:

   ```
   #
   # In this example, run the cleanup script expired notifications 
   #  will be made every day on 6:00 AM 
   0 6 * * * cd /var/www/paht_to_app/app && Console/cake CakeNotify.cron clear -q
   ```

## Using

[Using this plugin](docs/USING.md)
