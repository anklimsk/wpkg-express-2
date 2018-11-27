# Using this plugin

## Install this plugin

1. Copy configuration file from `app/Plugin/CakeSettingsApp/Config/cakesettingsapp.php` to `app/Config`.
2. Edit config file and configure plugin [See `Example of configuration file`](#example-of-configuration-file)
3. Include component `CakeSettingsApp.Settings` in your `AppController`:

   ```php
   public $components = [
       'CakeSettingsApp.Settings'
   ];
   ```

4. Copy translation files from `app/Plugin/CakeSettingsApp/Locale/rus/LC_MESSAGES/cake_settings_app_validation_errors.*` to
`app/Locale/rus/LC_MESSAGES`;
5. To change settings of application, go to the link `/cake_settings_app/settings` or simply `/settings`
6. To view queue of task, go to the link `/cake_settings_app/queues` or simply `/queues`.
   Require plugin `Queue`. Use the composer to install: `composer require dereuromark/cakephp-queue:^2.3.0`

## Creating сustom Settings

1. Configure Settings in file `app/Config/cakesettingsapp.php`:
   - Defining the settings scheme, e.g.:

      ```php
      'schema' => [
          'ShowDefaultPhoto' => ['type' => 'boolean', 'default' => false],
      ],
      ```

      Where the `type` can be one of:
      * `string`;
      * `integer`;
      * `float`;
      * `boolean`.
   - If necessary using multiple value of field, fill parameter `serialize`, e.g.:

      ```php
      'serialize' => [
          'FieldName'
      ],
      ```

   - If necessary creating alias for value of setting, fill parameter `alias`, e.g.:

      ```php
      'alias' => [
          'EmailContact' => [
              'Config.adminEmail'
          ],
          'EmailNotifyUser' => [
              'Email.live'
          ]
      ]
      ```

2. Defining rules for validate settings:

   - Copy configuration file from `app/Plugin/CakeSettingsApp/Model/Setting.php.default` to `app/Model/Setting.php`
   - Fill the validation rules, e.g.:

      ```php
      /**
       * List of validation rules. It must be an array with the field name as key and using
       * as value one of the following possibilities
       *
       * @var array
       * @link http://book.cakephp.org/2.0/en/models/model-attributes.html#validate
       * @link http://book.cakephp.org/2.0/en/models/data-validation.html
       */
      public $validate = [
          'ShowDefaultPhoto' => [
              'rule' => 'boolean',
              'message' => 'Incorrect value for checkbox',
              'required' => true,
              'allowEmpty' => true,
          ],
      ];
      ```

   - Fill the method `Setting::getVars()` for using additional variables in `View`, e.g.:

      ```php
      /**
       * Return extended variables for form of application settings
       *
       * @return array Extended variables
       */
      public function getVars() {
          $variables = [];
          $variables['someName'] = 'some value';

          return $variables;
      }
      ```

   - Fill the method `Setting::afterFind()` to modify any results returned by getConfig(), e.g.:

      ```php
      /**
       * Called after each find operation. Can be used to modify any results returned by find().
       * Return value should be the (modified) results.
       *
       * @param mixed $results The results of the find operation
       * @param bool $primary Whether this model is being queried directly (vs. being queried as an association)
       * @param string $key The name of the parameter to retrieve the configurations.
       * @return mixed Result of the find operation
       * @link https://book.cakephp.org/2.0/en/models/callback-methods.html#afterfind
       */
      public function afterFind($results, $primary = false, $key = null) {
      }
      ```

   - Fill the methods `Setting::beforeSave()` and `Setting::afterSave()`, if necessary.

3. Creating UI for settings:
   - Copy files of `View` element from `app/Plugin/CakeSettingsApp/View/Elements/formExtendSettingsLeft.ctp.default` and 
     `app/Plugin/CakeSettingsApp/View/Elements/formExtendSettingsRight.ctp.default` to `app/View/Elements`
   - Edit these files, e.g.:

      ```php
      echo $this->Form->inputs([
          'legend' => __('Photo'),
          'Setting.ShowDefaultPhoto' => ['label' => [__('Show default photo'),
              __('Show default photo, if the photo is not specified'), ':'],
              'type' => 'checkbox'],
      ]);
      ```

## Setting users with role and prefix, that are members of a of security group on LDAP server

In your `AppController` add:

```php
/**
 * Called before the controller action. You can use this method to configure and customize components
 * or perform logic that needs to happen before each controller action.
 *
 * Actions:
 *  - Configure components
 *
 * @return void
 * @link http://book.cakephp.org/2.0/en/controllers.html#request-life-cycle-callbacks
 */
public function beforeFilter() {
    $authGroups = [
        USER_ROLE_USER => 'default'
    ];
    $authGroupsList = $this->Setting->getAuthGroupsList();
    $authPrefixes = $this->Setting->getAuthPrefixesList();
    foreach ($authGroupsList as $userRole => $fieldName) {
        $userGroup = Configure::read(PROJECT_CONFIG_NAME . '.' . $fieldName);
        if (!empty($userGroup)) {
            $authGroups[$userRole] = $userGroup;
        }
    }

    $isExternalAuth = false;
    if ((bool)Configure::read(PROJECT_CONFIG_NAME . '.ExternalAuth') == true) {
        $isExternalAuth = $this->UserInfo->isExternalAuth();
    }

    $this->Auth->authenticate = [
        'CakeLdap.Ldap' => [
            'externalAuth' => $isExternalAuth,
            'groups' => $authGroups,
            'prefixes' => $authPrefixes,
            'includeFields' => CAKE_LDAP_LDAP_ATTRIBUTE_OBJECT_GUID,
            'bindFields' => [
                CAKE_LDAP_LDAP_ATTRIBUTE_OBJECT_GUID => 'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_OBJECT_GUID
            ]
        ]
    ];
    parent::beforeFilter();
}
```

## Getting list of E-mail for users, that are members of a of security group on LDAP server

In your `Model` add:

```php
$modelLdap = ClassRegistry::init('CakeSettingsApp.Ldap');
$listGroupEmail = $modelLdap->getListGroupEmail($groupDn);
```

Where `$groupDn` - Security group distinguished name.

## Example of configuration file

```php
$config['CakeSettingsApp'] = [
    // The application settings key. Used in `Configure::read('Key')`
    // See https://book.cakephp.org/2.0/en/development/configuration.html#Configure::read
    'configKey' => 'AppConfig',
    // Use configuration UI of SMTP
    'configSMTP' => false,
    // Use configuration UI of Autocomplete limit
    'configAcLimit' => true,
    // Use configuration UI of Search base for LDAP
    'configADsearch' => true,
    // Use configuration UI of External authentication
    'configExtAuth' => true,
/*
    // Setting users with role and prefix
    'authGroups' => [
        // User role bit mask
        1 => [
            // Name of field setting
            'field' => 'AdminGroupMember',
            // Label of field setting
            'name' => __('administrator'),
            // User role prefix
            'prefix' => 'admin'
        ]
    ],
*/
    // List of languages for UI in format: key - ISO 639-1, value - ISO 639-2
    'UIlangs' => [
        'US' => 'eng',
        'RU' => 'rus',
    ],
/*
    // Custom settings scheme
    'schema' => [
        'FieldName' => ['type' => 'string', 'default' => ''],
    ],
    // List of fields with multiple value
    'serialize' => [
        'FieldName'
    ],
    // List of alias for value of setting
    'alias' => [
        'FieldName' => [
            'ConfigGroup.Key',
        ]
    ],
*/
];

```
