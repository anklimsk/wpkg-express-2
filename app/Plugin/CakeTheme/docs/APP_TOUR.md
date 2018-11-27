# Creating tour of the application

1. Copy configuration file from `app/Plugin/CakeTheme/Config/caketheme.php` to `app/Config`
2. Edit configuration file and configure plugin [See `Example of configuration file`](#example-of-configuration-file)
3. Copy translation files from `app/Plugin/CakeTheme/Locale/rus/LC_MESSAGES/tour_app.*' to 'app/Locale/rus/LC_MESSAGES/'
4. Add JavaScript files in your layout file:

   ```php
   echo $this->Html->script('CakeTheme.TourApp.min.js');

   // If need use store configuration of plugin in storages, include file:
   echo $this->Html->script('CakeTheme.js.storage.min.js');
   ```

5. Add item to main menu, e.g.:

   ```php
   echo $this->ViewExtension->menuItemLink('fa-question', __d('tour_app', 'Start the tour of the application'),
       '#', ['data-toggle' => 'start-app-tour']);
   ```

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
    'TourApp' => [
        //  Steps of tour.
        //  See 'Step Options' http://bootstraptour.com/api/
        'Steps' => [
/*
            [
                'path' => '/',
                'element' => 'ul.nav',
                'title' => 'Main menu',
                'content' => 'Main menu of application.',
                'onNext' => 'alert("Next step is called");'
            ],
*/
        ]
    ]
];
```
