# CakePHP 2.x Extended test plugin
[![Build Status](https://travis-ci.com/anklimsk/cakephp-extended-test.svg?branch=master)](https://travis-ci.com/anklimsk/cakephp-extended-test)
[![Coverage Status](https://codecov.io/gh/anklimsk/cakephp-extended-test/branch/master/graph/badge.svg)](https://codecov.io/gh/anklimsk/cakephp-extended-test)
[![Latest Stable Version](https://poser.pugx.org/anklimsk/cakephp-extended-test/version)](https://packagist.org/packages/anklimsk/cakephp-extended-test)
[![License](https://poser.pugx.org/anklimsk/cakephp-extended-test/license)](https://packagist.org/packages/anklimsk/cakephp-extended-test)

Extended test tools for CakePHP

## This plugin provides next features:

- Baking test case use cake console;
- Testing a non-public method or property using a proxy object;
- Testing the View with a CSS Selector;
- Applying testing configuration of application from file;
- Applying testing information of logged-on user from array;
- Advanced testing methods: test Flash messages, upload file, testing method arguments, 
   assertions and messages from array.

## Installation

1. Install the Plugin using composer: `composer require anklimsk/cakephp-extended-test`
2. Add the next line to the end of the file `app/Config/bootstrap.php`:

   ```php
   CakePlugin::load('CakeExtendTest');
   ```

## Using

[Using this plugin](docs/USING.md)
