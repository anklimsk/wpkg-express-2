# CakePHP 2.x Basic functions plugin
[![Build Status](https://travis-ci.com/anklimsk/cakephp-basic-functions.svg?branch=master)](https://travis-ci.com/anklimsk/cakephp-basic-functions)
[![Coverage Status](https://codecov.io/gh/anklimsk/cakephp-basic-functions/branch/master/graph/badge.svg)](https://codecov.io/gh/anklimsk/cakephp-basic-functions)
[![Latest Stable Version](https://poser.pugx.org/anklimsk/cakephp-basic-functions/version)](https://packagist.org/packages/anklimsk/cakephp-basic-functions)
[![License](https://poser.pugx.org/anklimsk/cakephp-basic-functions/license)](https://packagist.org/packages/anklimsk/cakephp-basic-functions)

Global basic utilities for the CakePHP application

## This plugin provides next features:

- Getting array of constants value with name `constsToWords()`;
- Getting array of constants values by prefix `constsVals()`;
- Getting name of constant by prefix and value `constValToLcSingle()`;
- Translate array values by Domain name `translArray()`;
- Getting a string with the first character of string capitalized `mb_ucfirst()`;
- Checking whether the string is GUID `isGuid()`;
- Getting readable GUID from binary string LDAP `GuidToString()`;
- Getting GUID for LDAP query from readable GUID string `GuidStringToLdap()`;
- Analog of PHP function `str_pad()` for using unicode `mb_str_pad()`;
- Getting unicode char by its code `unichr()`;
- Checking whether the array is zero-indexed and sequential `isAssoc()`;
- Checking string is binary `isBinary()`;
- Getting information about the current language of the UI and converting it.

## Installation

1. Install the Plugin using composer: `composer require anklimsk/cakephp-basic-functions`
2. Add the next line to the end of the file `app/Config/bootstrap.php`:

   ```php
   CakePlugin::load('CakeBasicFunctions', ['bootstrap' => true]);
   ```

## Using

[Using this plugin](docs/USING.md)