# CakePHP 2.x Full UI theme plugin
[![Build Status](https://travis-ci.com/anklimsk/cakephp-theme.svg?branch=master)](https://travis-ci.com/anklimsk/cakephp-theme)
[![Coverage Status](https://codecov.io/gh/anklimsk/cakephp-theme/branch/master/graph/badge.svg)](https://codecov.io/gh/anklimsk/cakephp-theme)
[![Latest Stable Version](https://poser.pugx.org/anklimsk/cakephp2-theme/v/stable)](https://packagist.org/packages/anklimsk/cakephp2-theme)
[![License](https://poser.pugx.org/anklimsk/cakephp2-theme/license)](https://packagist.org/packages/anklimsk/cakephp2-theme)

Full UI theme for CakePHP

## This plugin provides next features:

- Using additional `JS` and `CSS` files in `main` layout;
- Retrieving list of specific CSS or JS files for action of controller;
- Rendering CakePHP Flash message using `Noty` or` Bootstrap`;
- Rendering styled information on error or exception;
- Creating tour of the application;
- Filter for table data with a group action, data export and printing;
- Pagination controls elements:
   * Change the limit of entries on the page;
   * Go to the page;
   * Load more button (display as list).
- Creation the links:
   * with confirmation;
   * with the request `AJAX` or `PJAX`;
   * with the `AJAX`request without render result (only Flash message);
   * with display result in a modal or popup window;
   * with progress bar from queue of tasks;
   * with disabled state;
   * with `Lightbox` gallery.
- Creating forms with tabs, with an progress bar of filling the input;
- Creating forms for AJAX upload files;
- Creating forms inputs:
   * select with search and `AJAX` loading list;
   * date and time picker;
   * country flag picker;
   * spinner input;
   * drop down input;
   * checkbox and radio button;
   * password input with checking Caps Lock;
   * text input with autocomplete, input mask or focus;
   * textarea input with autocomplete.
- Creating page header with small menu with typical actions;
- Creating tooltips;
- Creating time ago block;
- Creating a collapsible tree and table with support for moving and drag and drop;
- Create a button for printing page;
- Showing button `Scroll to top`;
- AJAX render the block at regular intervals;
- Creating main menu with search input;
- Checking UI is ready to be displayed.

## Installation

1. Install the Plugin using composer: `composer require anklimsk/cakephp2-theme`
2. Add the next line to the end of the file `app/Config/bootstrap.php`:

   ```php
   CakePlugin::load('CakeTheme', ['bootstrap' => true, 'routes' => true]);
   ```

3. Copy configuration file from `app/Plugin/CakeTheme/Config/asset_compress.local.ini` to `app/Config/asset_compress.ini`
4. Create symlink `ln -sr app/Plugin/CakeTheme/webroot/ app/webroot/cake_theme`
5. Generate compiled assets run CakePHP console command `Console/cake asset_compress build_ini`

## Using

[Using this plugin](docs/USING.md)
