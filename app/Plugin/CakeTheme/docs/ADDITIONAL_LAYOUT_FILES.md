# Using additional `JS` and `CSS` files in `main` layout

1. Copy configuration file from `app/Plugin/CakeTheme/Config/caketheme.php` to `app/Config`
2. Add build configuration in file `app/Config/asset_compress.ini`, e.g.:

   ```ini
   ; List of additional JS files for Main layout
   [additional-files.js]
   filters[] = YuiJs
   files[] = plugin:SearchInfo:js/SearchInfo.js
   ```

3. Add list of additional files in file `app/Config/caketheme.php`, e.g.:

   ```php
   $config['CakeTheme'] = [
       'AdditionalFiles' => [
           // List of additional JS files
           'js' => ['search-info'],
       ],
   ];
   ```

4. Clears all builds defined in the ini file use CakePHP console:
`Console/cake asset_compress clear`
5. Generate only build files defined in the ini file:
`Console/cake asset_compress build_ini --force`
6. Clear cache `cake_config_plugin`.

## Example of configuration file

```php
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
    'AdditionalFiles' => [
        // List of additional CSS files
        'css' => [],
        // List of additional JS files
        'js' => [],
    ]
];
```