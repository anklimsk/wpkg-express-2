# Using this plugin

## Preparing this plugin

1. Copy configuration file from `app/Plugin/CakeInstaller/Config/cakeinstaller.php` to `app/Config`.
2. Edit config file and configure plugin [See "Example of configuration file"](#example-of-configuration-file)
3. Include component `CakeInstaller.Installer` in your `AppController`:

   ```php
   public $components = [
       'CakeInstaller.Installer' => [
           'ConfigKey' => 'ProjectCfg'
       ]
   ];
   ```

   Where `ProjectCfg` is the application configuration key used in call `Configure::read('ProjectCfg');`.
   Used to fast checking the application is already installed.
4. Copy translation files from `app/Plugin/CakeInstaller/Locale/rus/LC_MESSAGES/cake_installer_label.*` to
`app/Locale/rus/LC_MESSAGES`;

## Using console

To install the application, go to the OS console, navigate to the directory `app` application,
   and run the following commands:
- `sudo ./Console/cake CakeInstaller -h` - To get help;
- `sudo ./Console/cake CakeInstaller` - To start interactive shell of installer;
- `sudo ./Console/cake CakeInstaller check` - To start command of installer, e.g. `check`.

## Initialization of database tables with data

1. Add to the beginning of the schema file `app/Config/Schema/schema.php` of your application:

   ```php
   App::uses('InstallerInit', 'CakeInstaller.Model');
   App::uses('ClassRegistry', 'Utility');

   class AppSchema extends CakeSchema
   {

       public function before($event = [])
       {
           $ds = ConnectionManager::getDataSource($this->connection);
           $ds->cacheSources = false; 

           return true;
       }

       public function after($event = [])
       {
           if (!empty($event['errors']) || !isset($event['create'])) {
               return;
           }

           $installerInitModel = ClassRegistry::init('CakeInstaller.InstallerInit');
           $installerInitModel->initDbTable($event['create']);
       }
       ...
   }
   ```

2. In your Model, create the `initDbTable()` method, e.g.:

   ```php
   /**
    * Initialization of database table the initial values
    *
    * @return bool Success
    */
   public function initDbTable()
   {
       $dataToSave = [];
       $types = constsToWords('DATA_TYPE_');

       foreach ($types as $id => $name) {
           $dataToSave[][$this->alias] = compact('id', 'name');
       }

       if (empty($dataToSave)) {
           return false;
       }

       return (bool)$this->saveAll($dataToSave);
   }
   ```

## Using callback method after the installation process is complete

1. Copy model file from `app/Plugin/CakeInstaller/Model/InstallerCompleted.php.default` to `app/Model/InstallerCompleted.php`
2. Fill the method `InstallerCompleted::intsallCompleted()`:

   ```php
   /**
    * This method is called after the installation process is complete.
    *
    * @return bool Success
    */
   public function intsallCompleted() {
      return true;
   }
   ```

## Example of configuration file

```php
$config['CakeInstaller'] = [
    // Version of PHP for check
    'PHPversion' => [
        [
            // Version
            '5.4.0',
            // Operator
            '>='
        ],
    ],
    // PHP Extension for check
    'PHPextensions' => [
        [
            // Extension name
            'pdo',
            // Critical need
            true
        ],
        [
            // Extension name
            'runkit',
            // Critical need
            false
        ],
    ],
    // Commands for installer
    'installerCommands' => [
        // Set Installer UI language
        'setuilang',
        // Checking PHP environment
        'check',
        // Set file system permissions on the temporary directory
        'setdirpermiss',
        // Set security key
        'setsecurkey',
        // Set timezone
        'settimezone',
        // Set base URL
        'setbaseurl',
        // Configure database connections
        'configdb',
        // Check connect to database
        'connectdb',
        // Create database and initialize data
        'createdb',
        // Create symlinks to files
        'createsymlinks',
        // Create cron jobs
        'createcronjobs',
        // Install this application
        'install',
    ],
    // Tasks for action - install
    'installTasks' => [
        // Set Installer UI language
        'setuilang',
        // Checking PHP environment
        'check',
        // Set file system permissions on the temporary directory
        'setdirpermiss',
        // Set security key
        'setsecurkey',
        // Set timezone
        'settimezone',
        // Set base URL
        'setbaseurl',
        // Configure database
        'configdb',
        // Check connect to database
        'connectdb',
        // Create database and initialize data
        'createdb',
        // Create symlinks to files
        'createsymlinks',
        // Create cron jobs
        'createcronjobs',
    ],
    // List of database connection for configure
    'configDBconn' => [
        // Main connection for application
        'default',
        // LDAP connection for application
        'ldap',
        // Test connection for application
        'test'
    ],
    'customConnections' => [
        // Name of connection (property of class DATABASE_CONFIG)
        'connectionName' => [
            // Name of connection parameter
            'paramName' => [
                // Label of parameter. If empty, use default label or parameter name
                'label' => 'label of param',
                // Value of parameter. If exists, skip next options
                'value' => 'value of param',
                // Default value of parameter (empty console input in interactive mode)
                'defaultValue' => 'default value of param',
                // Allow empty value of parameter
                'alowEmpty' => false,
                // List of variants for console input parameter value
                // Format I:
                'options' => ['value 1', 'value 2'],
                // Format II:
                'options' => ['label of variant 1' => 'value of variant 1', 'y' => true],
                // PCRE pattern for validation console input parameter value
                'validationPattern' => '/\w{2,}\@\w{2,}\.\w{2,}/',
            ]
        ],
        'ldap' => [
            'datasource' => [
                'value' => 'CakeLdap.LdapExtSource',
            ],
            'persistent' => [],
            'host' => [
                'label' => __d('cake_installer_label', 'LDAP host'),
            ],
            'port' => [
                'defaultValue' => 389,
            ],
            'login' => [
                'defaultValue' => '',
                'label' => __d('cake_installer_label', 'User principal name (user@fabrikam.com)'),
                'validationPattern' => '/\w{2,}\@\w{2,}\.\w{2,}/',
            ],
            'password' => [
                'alowEmpty' => false,
            ],
            'database' => [
                'value' => '',
            ],
            'basedn' => [
                'label' => __d('cake_installer_label', 'The DN of the search base'),
                'validationPattern' => '/^([a-z][a-z0-9-]*)=(?![ #])(((?![\\="+,;<>]).)|(\\[ \\#="+,;<>])|(\\[a-f0-9][a-f0-9]))*(,([a-z][a-z0-9-]*)=(?![ #])(((?![\\="+,;<>]).)|(\\[ \\#="+,;<>])|(\\[a-f0-9][a-f0-9]))*)*$/i',
            ],
            'type' => [
                'defaultValue' => 'ActiveDirectory',
                'label' => __d('cake_installer_label', 'LDAP server type'),
                'options' => ['ActiveDirectory', 'OpenLDAP', 'Netscape'],
            ],
            'tls' => [
                'defaultValue' => 'n',
                'label' => __d('cake_installer_label', 'Use TLS?'),
                'options' => ['n' => false, 'y' => true],
            ],
            'version' => [
                'defaultValue' => 3,
                'label' => __d('cake_installer_label', 'Version of LDAP protocol'),
                'options' => [2, 3],
            ],
        ],
    ],
    // List of additional schemes for creation, as
    // console command `cake schema create` options
    'schemaCreationList' => [
        'sessions',
        '-p Queue',
    ],
    // List of additional schemes for checking exists in
    // database
    'schemaCheckingList' => [
        'sessions',
        '-p Queue',
    ],
    // List of symlinks for creation in format:
    // key - link; value - target.
    'symlinksCreationList' => [
        APP . 'webroot' . DS . 'cake_installer' => APP . 'Plugin' . DS . 'CakeInstaller' . DS . 'webroot'
    ],
    // List of cron job for creation in format:
    // key - command; value - start time.
    'cronJobs' => [
        // 'cd ' . APP . ' && Console/cake Queue.Queue runworker -q' => '*/10 * * * *'
    ],
    // List of languages for installer UI in format: ISO 639-2
    'UIlangList' => [
        'eng',
        'rus',
    ]
];
```
