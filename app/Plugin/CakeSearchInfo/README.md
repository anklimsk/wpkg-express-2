# CakePHP 2.x Search for information plugin
[![Build Status](https://travis-ci.com/anklimsk/cakephp-search-info.svg?branch=master)](https://travis-ci.com/anklimsk/cakephp-search-info)
[![Coverage Status](https://codecov.io/gh/anklimsk/cakephp-search-info/branch/master/graph/badge.svg)](https://codecov.io/gh/anklimsk/cakephp-search-info)
[![Latest Stable Version](https://poser.pugx.org/anklimsk/cakephp-search-info/version)](https://packagist.org/packages/anklimsk/cakephp-search-info)
[![License](https://poser.pugx.org/anklimsk/cakephp-search-info/license)](https://packagist.org/packages/anklimsk/cakephp-search-info)

Search for information in the project database

## This plugin provides next features:

- Search for information in the project database;
- Adding a user role prefix to links to a search result;
- Support auto-completion in the search bar;
- Support for keyboard layout corrections for the Russian language;
- Supports two levels of search:
   * For all fields of the `Model`;
   * For each field of the `Model` separately.

## Installation

1. Install the Plugin using composer: `composer require anklimsk/cakephp-search-info`
2. Add the next line to the end of the file `app/Config/bootstrap.php`:

   ```php
   CakePlugin::load('CakeSearchInfo', ['bootstrap' => true, 'routes' => true]);
   ```

## Using

[Using this plugin](docs/USING.md)
