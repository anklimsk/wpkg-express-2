# Using this plugin

## Install this plugin

1. Include components in your `AppController`:

   ```php
   public $components = [
       'CakeTheme.Theme'
       'CakeTheme.ViewExtension',
       ...
   ];
   ```

2. Include helpers in your `AppController`:

   ```php
   public $helpers = [
       'AssetCompress.AssetCompress',
       'CakeTheme.ActionScript',
       'CakeTheme.ViewExtension',
       'CakeTheme.Filter',
       'Form' => [
           'className' => 'CakeTheme.ExtBs3Form'
       ],
       ...
   ];
   ```

3. Copy translation files from `app/Plugin/CakeTheme/Locale/rus/LC_MESSAGES/` to
`app/Locale/rus/LC_MESSAGES`:
- `tour_app.*`;
- `view_extension.*`.

# Using features of this plugin

- [Using additional `JS` and `CSS` files in `main` layout](ADDITIONAL_LAYOUT_FILES.md)
- [Retrieving list of specific `CSS` or `JS` files for action of controller](ACTION_SCRIPT.md)
- [Rendering CakePHP Flash message using `Noty` or` Bootstrap`](FLASH_MESSAGE.md)
- [Creating tour of the application](APP_TOUR.md)
- [Filter for table data](FILTER.md)
- [Pagination controls elements](PAGINATION_CONTROLS.md)
- [Creation the links](LINKS.md)
- [Creating forms and inputs](FORMS.md)
- [Using `ViewExtension` helper](VIEW_EXTENSION_HELPER.md)
- [Using `ViewExtension` component](VIEW_EXTENSION_COMPONENT.md)
- [Creation main menu](MAIN_MENU.md)
- [Updating libraries](UPDATING_LIBRARIES.md)
- [Creating a collapsible tree and table with support for moving and drag and drop](TREE.md)
- [Creating a breadcrumbs navigation](BREADCRUMBS_NAVIGATION.md)

## Example of configuration file

```php
$config['CakeTheme'] = [
    'AdditionalFiles' => [
        // List of additional CSS files
        'css' => [],
        // List of additional JS files
        'js' => [],
    ],
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
    ],
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
    ],
    'ViewExtension' => [
        // Autocomplete limit for filter of table
        'AutocompleteLimit' => 10,
        // Server-Sent Events
        'SSE' => [
            // Default text for Noty message
            'text' => __d('view_extension', 'Waiting to run task'),
            // Labels for data
            'label' => [
                // Task name
                'task' => __d('view_extension', 'Task'),
                // Completed percentage
                'completed' => __d('view_extension', 'Completed'),
                // Message from task
                'message' => __d('view_extension', 'Message')
            ],
            // The number of repeated attempts to start pending tasks
            'retries' => 100,
            // Delay to delete flash messages
            'delayDeleteTask' => 5,
        ],
        // ViewExtension Helper
        'Helper' => [
            // Default FontAwesome icon prefix
            'defaultIconPrefix' => 'fas',
            // Default FontAwesome icon size
            'defaultIconSize' => '',
            // Default Bootstrap button prefix
            'defaultBtnPrefix' => 'btn',
            // Default Bootstrap button size
            'defaultBtnSize' => 'btn-xs',
        ],
/*
        // PHP Unoconv
        'Unoconv' => [
            // The timeout for the underlying process.
            'timeout' => 30,
            // The path (or an array of paths) for a custom binary.
            'binaries' => '/opt/local/unoconv/bin/unoconv'
        ]
*/
    ]
];
```
