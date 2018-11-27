# CakePHP 2.x Console installer plugin
[![Build Status](https://travis-ci.com/anklimsk/cakephp-console-installer.svg?branch=master)](https://travis-ci.com/anklimsk/cakephp-console-installer)
[![Coverage Status](https://codecov.io/gh/anklimsk/cakephp-console-installer/branch/master/graph/badge.svg)](https://codecov.io/gh/anklimsk/cakephp-console-installer)
[![Latest Stable Version](https://poser.pugx.org/anklimsk/cakephp-console-installer/version)](https://packagist.org/packages/anklimsk/cakephp-console-installer)
[![License](https://poser.pugx.org/anklimsk/cakephp-console-installer/license)](https://packagist.org/packages/anklimsk/cakephp-console-installer)

Console installer for CakePHP

## This plugin provides next features:

- Setting application UI language;
- Checking the PHP environment for the ready to start installation;
- Setting file system permissions on the temporary directory;
- Setting security key;
- Setting timezone;
- Setting base URL;
- Checking the connection to the database;
- Configuring connections to the database;
- Creating a database and initializing data;
- Creating symbolic links to files;
- Creating cron jobs;
- Installing CakePHP application.

## Installation

1. Install the Plugin using composer: `composer require anklimsk/cakephp-console-installer`
2. Add the next line to the end of the file `app/Config/bootstrap.php`:

  ```php
  CakePlugin::load('CakeInstaller', ['bootstrap' => true, 'routes' => true]);
  ```

## Using

[Using this plugin](docs/USING.md)
