# CakePHP 2.x Application Settings UI
[![Build Status](https://travis-ci.com/anklimsk/cakephp-settings-app.svg?branch=master)](https://travis-ci.com/anklimsk/cakephp-settings-app)
[![Coverage Status](https://codecov.io/gh/anklimsk/cakephp-settings-app/branch/master/graph/badge.svg)](https://codecov.io/gh/anklimsk/cakephp-settings-app)
[![Latest Stable Version](https://poser.pugx.org/anklimsk/cakephp-settings-app/version)](https://packagist.org/packages/anklimsk/cakephp-settings-app)
[![License](https://poser.pugx.org/anklimsk/cakephp-settings-app/license)](https://packagist.org/packages/anklimsk/cakephp-settings-app)


UI for CakePHP application settings

## This plugin provides next features:

- Base settings for the `CakePHP` application;
- Creating сustom Settings:
   * Defining the settings scheme;
   * Defining rules for validate settings;
   * Creating UI for settings.
- Setting users with role and prefix, that are members of a of
  security group on LDAP server
- Setting for sending an E-mail including the encrypted user password
- Getting list of E-mail for users, that are members
  of a of security group on LDAP server

## Installation

1. Install the Plugin using composer: `composer require anklimsk/cakephp-settings-app`
2. Add the next line to the end of the file `app/Config/bootstrap.php`:

   ```php
   CakePlugin::load('CakeSettingsApp', ['bootstrap' => true, 'routes' => true]);
   ```

3. Get the name of the user that is running the web server, run the command:
`ps aux | grep -E '[a]pache|[h]ttpd|[_]www|[w]ww-data|[n]ginx' | grep -v root | head -1 | cut -d\  -f1`.
4. Set owner of file `app/Config/config.php` run the command `chown www-data app/Config/config.php` where
`www-data` - user name for web server.
5. Add to file `app/Config/core.php`:

   ```php
   /**
    * A random numeric string (digits only) used to encrypt/decrypt strings.
    */
       Configure::write('Security.key', '9b8964f94127f5b843c67e8c89479e4f2cfac2b182c72dc0691cc384c438f9ca');
   ```

See https://book.cakephp.org/2.0/en/core-utility-libraries/security.html#Security::encrypt

## Using

[Using this plugin](docs/USING.md)
