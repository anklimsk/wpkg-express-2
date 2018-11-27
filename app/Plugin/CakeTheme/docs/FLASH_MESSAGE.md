# Rendering CakePHP Flash message using `Noty` or` Bootstrap`

1. Copy configuration file from `app/Plugin/CakeTheme/Config/caketheme.php` to `app/Config`
2. Edit configuration file and configure plugin [See `Example of configuration file`](#example-of-configuration-file)
3. Add JavaScript files in your layout file:

   ```php
   echo $this->Html->script('CakeTheme.AjaxFlash.min.js');

   // If need use store configuration of plugin in storages, include file:
   echo $this->Html->script('CakeTheme.js.storage.min.js');
   ```

4. Set flash message in your `Controller`:

   ```php
   $this->Flash->information($message, ['params' => ['hideMsgIcon' => true]]);
   ```

   Type of messages:
   - notification;
   - information;
   - success;
   - warning;
   - error.

## List of supported design

This plugin supported the following design (in order):
- `Noty` (https://ned.im/noty);
- `Twitter bootstrap` (http://getbootstrap.com)
- `Internal style`.

## Example of configuration file

```php
<?php
/**
 * This file configures main theme
 *
 * To modify these parameters, copy this file into your own CakePHP APP/Config directory.
 * CakeTheme: Set theme for application.
 * @copyright Copyright 2016-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.Config
 */

$config['CakeTheme'] = [
    'AjaxFlash' => [
        // List of keys for flash messages
        'flashKeys' => [
            'flash',
            'auth',
        ],
        // Time out for message types: flash_information, flash_success, flash_notification
        'timeOut' => 30,
        // Delay to delete flash messages
        'delayDeleteFlash' => 5,
        // Register global ajax callback complete() for checking update part of page
        'globalAjaxComplete' => false,
        // Options for 'jQuery.noty' plugin (see http://ned.im/noty/#/about or https://github.com/needim/noty)
        'theme' => 'bootstrap-v3',
        'layout' => 'topRight',
        'open' => 'animated bounceInRight',
        'close' => 'animated bounceOutRight',
    ]
];
```
