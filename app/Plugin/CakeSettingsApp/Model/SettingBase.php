<?php
/**
 * This file is the model file of the plugin.
 * Methods for management settings of application.
 *
 * CakeSettingsApp: Manage settings of application.
 * @copyright Copyright 2016-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.Model
 */

App::uses('CakeSettingsAppAppModel', 'CakeSettingsApp.Model');
App::uses('ClassRegistry', 'Utility');
App::uses('Security', 'Utility');
App::uses('File', 'Utility');
App::uses('CakeTime', 'Utility');
App::uses('Language', 'CakeBasicFunctions.Utility');
App::uses('CakeSession', 'Model/Datasource');

/**
 * SettingBase for CakeSettingsApp.
 *
 * @package plugin.Model
 */
class SettingBase extends CakeSettingsAppAppModel {

/**
 * Name of the model.
 *
 * @var string
 * @link http://book.cakephp.org/2.0/en/models/model-attributes.html#name
 */
	public $name = 'Setting';

/**
 * Custom database table name, or null/false if no table association is desired.
 *
 * @var string
 * @link http://book.cakephp.org/2.0/en/models/model-attributes.html#usetable
 */
	public $useTable = false;

/**
 * List of behaviors to load when the model object is initialized. Settings can be
 * passed to behaviors by using the behavior name as index.
 *
 * @var array
 * @link https://book.cakephp.org/2.0/en/models/behaviors.html#using-behaviors
 */
	public $actsAs = ['CakeTheme.BreadCrumb'];

/**
 * List of validation rules. It must be an array with the field name as key and using
 * as value one of the following possibilities
 *
 * @var array
 * @link http://book.cakephp.org/2.0/en/models/model-attributes.html#validate
 * @link http://book.cakephp.org/2.0/en/models/data-validation.html
 */
	public $validate = [
		'ExternalAuth' => [
			'rule' => 'boolean',
			'message' => 'Incorrect value for checkbox',
			'required' => true,
			'allowEmpty' => true,
		],
		'EmailContact' => [
			'rule' => 'validEmail',
			'message' => 'This field must contain a valid e-mail',
			'required' => false,
			'allowEmpty' => true,
		],
		'EmailSubject' => [
			'rule' => 'notBlank',
			'message' => 'This field must contain a valid subject of e-mail',
			'required' => true,
			'allowEmpty' => false,
		],
		'EmailSmtphost' => [
			'rule' => 'validHost',
			'required' => true,
			'message' => 'Invalid SMTP Server',
			'allowEmpty' => true,
		],
		'EmailSmtpport' => [
			'rule' => 'validHost',
			'required' => true,
			'message' => 'Invalid port number',
			'allowEmpty' => true,
		],
		'EmailSmtpuser' => [
			'rule' => 'notBlank',
			'required' => false,
			'message' => 'Invalid SMTP Username',
			'allowEmpty' => true,
		],
		'EmailSmtppassword' => [
			'smtppassword' => [
				'rule' => 'notBlank',
				'required' => false,
				'allowEmpty' => true,
				'last' => true,
				'message' => 'Invalid SMTP password',
			],
			'smtppasswordequal' => [
				'rule' => 'validPasswords',
				'required' => false,
				'last' => true,
				'message' => 'Passwords dont match'
			]
		],
		'EmailNotifyUser' => [
			'rule' => 'boolean',
			'message' => 'Incorrect value for checkbox',
			'required' => true,
			'allowEmpty' => true,
		],
		'AutocompleteLimit' => [
			'rule' => ['range', 4, 101],
			'message' => 'This field must contain a valid limit of autocomplete between %d and %d',
			'required' => true,
			'allowEmpty' => false,
		],
		'Company' => [
			'rule' => 'notBlank',
			'message' => 'This field must contain a valid company name',
			'required' => true,
			'allowEmpty' => false,
		],
		'SearchBase' => [
			'validText' => [
				'rule' => '/^([a-z][a-z0-9-]*)=(?![ #])(((?![\\="+,;<>]).)|(\\[ \\#="+,;<>])|(\\[a-f0-9][a-f0-9]))*(,([a-z][a-z0-9-]*)=(?![ #])(((?![\\="+,;<>]).)|(\\[ \\#="+,;<>])|(\\[a-f0-9][a-f0-9]))*)*$/i',
				'message' => 'This field must contain a valid distinguished name of the search base object',
				'required' => true,
				'allowEmpty' => true,
				'last' => true,
			],
			'validDn' => [
				'rule' => 'validSearchBase',
				'message' => 'This field must contain a valid distinguished name of the search base object',
				'required' => true,
				'allowEmpty' => true,
			]
		],
		'Language' => [
			'rule' => ['inList', ['eng', 'rus']],
			'message' => 'This field must contain a valid language name',
			'required' => true,
			'allowEmpty' => false,
		],
	];

/**
 * Path to application configuration file.
 *
 * @var string
 */
	public $pathAppCfg = CONFIG;

/**
 * Path to marker file.
 *
 * @var string
 */
	public $markerFile = CAKE_SETTINGS_APP_SETTINGS_MARKER_FILE;

/**
 * Field-by-field table metadata.
 *
 * @var array
 */
	protected $_schemaData = [
		'EmailContact' => ['type' => 'string', 'default' => ''],
		'EmailSubject' => ['type' => 'string', 'default' => ''],
		'Company' => ['type' => 'string', 'default' => ''],
		'SearchBase' => ['type' => 'string', 'default' => ''],
		'AutocompleteLimit' => ['type' => 'integer', 'default' => false],
		'ExternalAuth' => ['type' => 'boolean', 'default' => ''],
		'EmailSmtphost' => ['type' => 'string', 'default' => ''],
		'EmailSmtpport' => ['type' => 'integer', 'default' => ''],
		'EmailSmtpuser' => ['type' => 'string', 'default' => ''],
		'EmailSmtppassword' => ['type' => 'string', 'default' => ''],
		'EmailNotifyUser' => ['type' => 'boolean', 'default' => ''],
		'Language' => ['type' => 'string', 'default' => 'eng'],
	];

/**
 * Object of model `ConfigSettingsApp`
 *
 * @var object
 */
	protected $_modelConfigSettingsApp = null;

/**
 * Constructor. Binds the model's database table to the object.
 *
 * @param bool|int|string|array $id Set this ID for this model on startup,
 * can also be an array of options, see above.
 * @param string $table Name of database table to use.
 * @param string $ds DataSource connection name.
 */
	public function __construct($id = false, $table = null, $ds = null) {
		$this->_modelConfigSettingsApp = ClassRegistry::init('CakeSettingsApp.ConfigSettingsApp');

		$merge = ['validate'];
		$parentClass = get_parent_class($this);
		if ($parentClass === 'SettingBase') {
			$this->_mergeVars($merge, $parentClass);
		}

		parent::__construct($id, $table, $ds);
	}

/**
 * Return GUID of current login user.
 *
 * @return string Return GUID of current login user.
 */
	public function getUserGuid() {
		return CakeSession::read('Auth.User.includedFields.' . CAKE_SETTINGS_APP_LDAP_ATTRIBUTE_OBJECT_GUID);
	}

/**
 * Check exists security group in Active Directory.
 *
 * @param string $data Security group name.
 * @return bool Success
 */
	public function validSearchBase($data) {
		$data = array_shift($data);
		if (empty($data)) {
			return false;
		}

		$userGuid = $this->getUserGuid();
		$modelLdap = ClassRegistry::init('CakeSettingsApp.Ldap');

		return $modelLdap->checkSearchBase($userGuid, $data);
	}

/**
 * Check exists security group in Active Directory.
 *
 * @param string $data Security group name.
 * @param string $userGuid GUID of user for check member of this group.
 * @return bool Success
 */
	public function validGroup($data = null, $userGuid = null) {
		$data = array_shift($data);
		if (empty($data)) {
			return false;
		}

		$modelLdap = ClassRegistry::init('CakeSettingsApp.Ldap');

		return $modelLdap->groupExists($data, $userGuid);
	}

/**
 * Validation email addresses
 *
 * @param string $data A list of email addresses separated by a comma.
 * @return bool Success
 */
	public function validEmail($data) {
		$data = array_shift($data);
		if (empty($data)) {
			return false;
		}

		if (is_array($data)) {
			return false;
		}

		$result = true;
		$emails = explode(CAKE_SETTINGS_APP_SMTP_EMAIL_DELIM, (string)$data);
		foreach ($emails as $email) {
			$email = trim($email);
			if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
				$result = false;
			}
		}

		return $result;
	}

/**
 * Checking connection with the host through the specified port.
 *
 * @return bool Success
 */
	public function validHost() {
		$hostalias = 'EmailSmtphost';
		$portalias = 'EmailSmtpport';
		$port = CAKE_SETTINGS_APP_SMTP_DEFAULT_PORT;
		$timeLimit = CAKE_SETTINGS_APP_SMTP_TIME_LIMIT;
		if (!isset($this->data[$this->alias][$hostalias]) ||
			empty($this->data[$this->alias][$hostalias])) {
			return true;
		}

		$result = false;
		if (isset($this->data[$this->alias][$portalias]) &&
			ctype_digit($this->data[$this->alias][$portalias])) {
			$port = $this->data[$this->alias][$portalias];
		}

		//@codingStandardsIgnoreStart
		$fp = @fsockopen($this->data[$this->alias][$hostalias], $port, $errno, $errstr, $timeLimit);
		//@codingStandardsIgnoreEnd
		if ($fp) {
			$result = true;
			fclose($fp);
		}

		return $result;
	}

/**
 * Matching passwords
 *
 * @param string $data The password to check.
 * @return bool Success
 */
	public function validPasswords($data) {
		$field = key($data);
		$data = reset($data);
		if (empty($data) || empty($field)) {
			return false;
		}

		$controlField = $field . '_confirm';
		if (!isset($this->data[$this->alias][$controlField])) {
			return false;
		}

		if ($data !== $this->data[$this->alias][$controlField]) {
			return false;
		}

		return true;
	}

/**
 * Create validation rules by table fields
 *
 * @return bool Return success.
 */
	public function createValidationRules() {
		$configAcLimit = $this->_modelConfigSettingsApp->getFlagConfigAcLimit();
		$configADsearch = $this->_modelConfigSettingsApp->getFlagConfigADsearch();
		$configSMTP = $this->_modelConfigSettingsApp->getFlagConfigSmtp();
		$configExtAuth = $this->_modelConfigSettingsApp->getFlagConfigExtAuth();
		$authGroups = $this->_modelConfigSettingsApp->getAuthGroups();
		$validator = $this->validator();
		if (!$configAcLimit) {
			unset($validator['AutocompleteLimit']);
		}
		if (!$configADsearch) {
			unset($validator['Company']);
			unset($validator['SearchBase']);
		}
		if (!$configExtAuth) {
			unset($validator['ExternalAuth']);
		}
		if (!$configSMTP) {
			unset($validator['EmailSmtphost']);
			unset($validator['EmailSmtpport']);
			unset($validator['EmailSmtpuser']);
			unset($validator['EmailSmtppassword']);
			unset($validator['EmailNotifyUser']);
		}
		if (!empty($authGroups)) {
			foreach ($authGroups as $userRole => $authInfo) {
				$userGuid = null;
				if ($authInfo['prefix'] === 'admin') {
					$userGuid = $this->getUserGuid();
				}
				$fieldName = $authInfo['field'];
				$validator[$fieldName] = [
					'valid_group_' . $fieldName => [
						'rule' => ['validGroup', $userGuid],
						'message' => __d('cake_settings_app_validation_errors', 'This field must contain a valid group name.'),
						'required' => true,
						'allowEmpty' => false,
					]
				];
			}
		}

		return true;
	}

/**
 * Called during validation operations, before validation. Please note that custom
 * validation rules can be defined in $validate.
 *
 * @param array $options Options passed from Model::save().
 * @return bool True if validate operation should continue, false to abort
 * @link http://book.cakephp.org/2.0/en/models/callback-methods.html#beforevalidate
 * @see Model::save()
 */
	public function beforeValidate($options = []) {
		$this->createValidationRules();

		return true;
	}

/**
 * Called before each save operation, after validation. Return a non-true result
 * to halt the save.
 *
 * @param array $options Options passed from Model::save().
 * @return bool True if the operation should continue, false if it should abort
 * @link https://book.cakephp.org/2.0/en/models/callback-methods.html#beforesave
 * @see Model::save()
 */
	public function beforeSave($options = []) {
		return true;
	}

/**
 * Called after each successful save operation.
 *
 * @param bool $created True if this save created a new record
 * @param array $options Options passed from Model::save().
 * @return void
 * @link https://book.cakephp.org/2.0/en/models/callback-methods.html#aftersave
 * @see Model::save()
 */
	public function afterSave($created, $options = []) {
	}

/**
 * Return path to application configuration file.
 *
 * @return string Return path to application
 *  configuration file.
 */
	public function getPathAppCfg() {
		return (string)$this->pathAppCfg;
	}

/**
 * Return path to marker file.
 *
 * @return string Return path to marker file.
 */
	public function getPathMarkerFile() {
		return (string)$this->markerFile;
	}

/**
 * Returns an array of default settings fields metadata
 *
 * @return array Default settings fields metadata.
 */
	protected function _getDefaultSchemaData() {
		$configAcLimit = $this->_modelConfigSettingsApp->getFlagConfigAcLimit();
		$configADsearch = $this->_modelConfigSettingsApp->getFlagConfigADsearch();
		$configSMTP = $this->_modelConfigSettingsApp->getFlagConfigSmtp();
		$configExtAuth = $this->_modelConfigSettingsApp->getFlagConfigExtAuth();
		$defaultSchema = $this->_schemaData;
		$excludeFields = [];

		if (!$configAcLimit) {
			$excludeFields[] = 'AutocompleteLimit';
		}
		if (!$configADsearch) {
			$excludeFields[] = 'Company';
			$excludeFields[] = 'SearchBase';
		}
		if (!$configSMTP) {
			$excludeFields[] = 'EmailSmtphost';
			$excludeFields[] = 'EmailSmtpport';
			$excludeFields[] = 'EmailSmtpuser';
			$excludeFields[] = 'EmailSmtppassword';
			$excludeFields[] = 'EmailNotifyUser';
		}

		if (!$configExtAuth) {
			$excludeFields[] = 'ExternalAuth';
		}

		$result = array_diff_key($defaultSchema, array_flip($excludeFields));

		return $result;
	}

/**
 * Returns an array of settings fields metadata
 *
 * @return array Settings fields metadata.
 */
	public function getFullSchema() {
		$authGroups = $this->getAuthGroupsList();
		$authSchema = [];
		$defaultSchema = $this->_getDefaultSchemaData();
		$extendSchema = $this->_modelConfigSettingsApp->getExtendSchemaData();
		if (!empty($authGroups)) {
			foreach ($authGroups as $authGroupField) {
				$authSchema[$authGroupField] = ['type' => 'string', 'default' => ''];
			}
		}

		$result = $defaultSchema + $authSchema + $extendSchema;

		return $result;
	}

/**
 * Saving configurations.
 *
 * @param array $data Data to save.
 * @param bool|array $validate Either a boolean, or an array.
 *   If a boolean, indicates whether or not to validate before saving.
 *   If an array, can have following keys:
 *
 *   - atomic: If true (default), will attempt to save the record in a single transaction.
 *   - validate: Set to true/false to enable or disable validation.
 *   - fieldList: An array of fields you want to allow for saving.
 *   - callbacks: Set to false to disable callbacks. Using 'before' or 'after'
 *	 will enable only those callbacks.
 *   - `counterCache`: Boolean to control updating of counter caches (if any)
 *
 * @param array $fieldList List of fields to allow to be saved
 * @return mixed On success Model::$data if its not empty or true, false on failure
 * @triggers Model.beforeSave $this, array($options)
 * @triggers Model.afterSave $this, array($created, $options)
 */
	public function save($data = null, $validate = true, $fieldList = []) {
		$_whitelist = $this->whitelist;
		$defaults = [
			'validate' => true, 'fieldList' => [],
			'callbacks' => true, 'counterCache' => true,
			'atomic' => true
		];

		if (!is_array($validate)) {
			$options = compact('validate', 'fieldList') + $defaults;
		} else {
			$options = $validate + $defaults;
		}

		if (!empty($options['fieldList'])) {
			if (!empty($options['fieldList'][$this->alias]) && is_array($options['fieldList'][$this->alias])) {
				$this->whitelist = $options['fieldList'][$this->alias];
			} elseif (Hash::dimensions($options['fieldList']) < 2) {
				$this->whitelist = $options['fieldList'];
			}
		} elseif ($options['fieldList'] === null) {
			$this->whitelist = [];
		}

		$configPath = $this->_modelConfigSettingsApp->getNameConfigKey();
		if (empty($configPath) || !isset($data[$this->alias])) {
			$this->whitelist = $_whitelist;

			return false;
		}

		$this->set($data);
		if (empty($this->data)) {
			$this->whitelist = $_whitelist;

			return false;
		}

		$schema = $this->getFullSchema();
		$configData = [];
		foreach ($this->data[$this->alias] as $fieldName => $value) {
			if ((!empty($this->whitelist) && in_array($fieldName, $this->whitelist)) ||
				(empty($this->whitelist) && isset($schema[$fieldName])) ||
				(mb_strpos($fieldName, '_confirm') !== false)) {
				$configData[$fieldName] = $value;
			}
		}
		$this->data[$this->alias] = $configData;
		if ($options['validate'] && !$this->validates($options)) {
			$this->whitelist = $_whitelist;

			return false;
		}

		if ($options['callbacks'] === true || $options['callbacks'] === 'before') {
			$event = new CakeEvent('Model.beforeSave', $this, [$options]);
			list($event->break, $event->breakOn) = [true, [false, null]];
			$this->getEventManager()->dispatch($event);
			if (!$event->result) {
				$this->whitelist = $_whitelist;

				return false;
			}
		}

		$listSerializedFields = $this->_modelConfigSettingsApp->getListSerializeFields();
		$configSMTP = $this->_modelConfigSettingsApp->getFlagConfigSmtp();
		$dataToSave = [];
		foreach ($this->data[$this->alias] as $configName => $configValue) {
			$type = Hash::get($schema, $configName . '.type');
			if (empty($type)) {
				$type = 'string';
			}

			switch ($configName) {
				case 'EmailContact':
					$emails = explode(CAKE_SETTINGS_APP_SMTP_EMAIL_DELIM, $configValue);
					if (!empty($emails)) {
						foreach ($emails as &$email) {
							$email = trim($email);
						}
						unset($email);
						$configValue = implode(CAKE_SETTINGS_APP_SMTP_EMAIL_DELIM, $emails);
					}
					$dataToSave[$configPath . '.' . $configName] = $configValue;
					break;
				case 'EmailSmtppassword':
					if ($configSMTP) {
						$dataToSave[$configPath . '.' . $configName] = base64_encode(Security::encrypt($configValue, Configure::read('Security.key')));
					}
					break;
				case 'Language':
					$dataToSave['Config.language'] = $configValue;
					break;
				default:
					if (mb_strpos($configName, '_confirm') !== false) {
						continue 2;
					}
					if (in_array($configName, $listSerializedFields)) {
						$configValue = serialize($configValue);
						$type = 'string';
					}
					if (!settype($configValue, $type)) {
						settype($configValue, 'string');
					}
					$dataToSave[$configPath . '.' . $configName] = $configValue;
			}
		}

		$pathAppCfg = $this->getPathAppCfg();
		Configure::config('default', new PhpReader($pathAppCfg));

		$configKeysStore = [$configPath, 'Config'];
		$configKeysExtend = [];
		$configCache = [];
		$configAlias = $this->_modelConfigSettingsApp->getAliasConfig();
		if (!empty($configAlias)) {
			foreach ($configAlias as $configFieldName => $configKeys) {
				if (!isset($dataToSave[$configPath . '.' . $configFieldName])) {
					continue;
				}

				if (!is_array($configKeys)) {
					$configKeys = [$configKeys];
				}
				foreach ($configKeys as $configKey) {
					list($configGroup, $configName) = pluginSplit($configKey);
					if (!empty($configGroup) && !in_array($configGroup, $configKeysStore) &&
						!in_array($configGroup, $configKeysExtend)) {
						$configKeysExtend[] = $configGroup;
					}
					$dataToSave[$configKey] = $dataToSave[$configPath . '.' . $configFieldName];
				}
			}
		}
		if (!empty($configKeysExtend)) {
			$configKeysStore = array_merge($configKeysStore, $configKeysExtend);
			foreach ($configKeysExtend as $configKeyExtend) {
				$configValue = Configure::read($configKeyExtend);
				if ($configValue === null) {
					continue;
				}
				Configure::delete($configKeyExtend);
				$configCache[$configKeyExtend] = $configValue;
			}
		}
		$result = true;
		if (!Configure::write($dataToSave) || !Configure::dump('config.php', 'default', $configKeysStore)) {
			$result = false;
		}
		if (!empty($configCache)) {
			foreach ($configCache as $configCacheKey => $configCacheValue) {
				Configure::write($configCacheKey, $configCacheValue);
			}
		}
		if (!$result) {
			return $result;
		}

		if ($options['callbacks'] === true || $options['callbacks'] === 'after') {
			$event = new CakeEvent('Model.afterSave', $this, [false, $options]);
			$this->getEventManager()->dispatch($event);
		}

		if (!empty($this->data)) {
			$result = $this->data;
		}

		if (CakePlugin::loaded('Queue')) {
			$modelQueuedTask = ClassRegistry::init('Queue.QueuedTask');
			$modelQueuedTask->createJob('ClearCache', null, null, 'clear');
		}
		if (extension_loaded('Zend OPcache')) {
			$appCfgFile = $pathAppCfg . 'config.php';
			if (opcache_is_script_cached($appCfgFile)) {
				opcache_invalidate($appCfgFile, true);
			}
		}
		$this->_clearCache();
		$this->validationErrors = [];
		$this->whitelist = $_whitelist;
		$this->data = false;
		Configure::bootstrap(true);

		return $result;
	}

/**
 * Returns an array of values default settings
 *
 * @return array Values default settings.
 */
	public function getDefaultConfig() {
		$configs = [];
		$schema = $this->getFullSchema();
		foreach ($schema as $field => $metadata) {
			$configs[$this->alias][$field] = Hash::get($metadata, 'default');
		}

		return $configs;
	}

/**
 * Returns an array of authentication group information
 *
 * @param string $field Name of field to get information
 *  from configuration of authentication groups.
 * @return array Authentication group information.
 */
	protected function _getAuthFieldsList($field = null) {
		$authGroups = $this->_modelConfigSettingsApp->getAuthGroups();
		$result = [];
		if (empty($field)) {
			return $result;
		}

		foreach ($authGroups as $userRole => $authInfo) {
			if (!isset($authInfo[$field]) || empty($authInfo[$field])) {
				continue;
			}

			$result[$userRole] = $authInfo[$field];
		}

		return $result;
	}

/**
 * Get list of fields authentication group
 *
 * @return array List of fields authentication group.
 */
	public function getAuthGroupsList() {
		return $this->_getAuthFieldsList('field');
	}

/**
 * Get list of route prefixes authentication group
 *
 * @return array List of route prefixes authentication group.
 */
	public function getAuthPrefixesList() {
		return $this->_getAuthFieldsList('prefix');
	}

/**
 * Get name for user role
 *
 * @param int $role Bit mask of user role.
 * @return string Name of user role.
 */
	public function getAuthRoleName($role = null) {
		$role = (int)$role;
		$result = '';
		if ($role === 0) {
			return $result;
		}

		$roleNames = $this->_getAuthFieldsList('name');
		if (empty($roleNames)) {
			return $result;
		}

		if (!krsort($roleNames)) {
			return $result;
		}

		foreach ($roleNames as $roleMask => $roleName) {
			if (($role & $roleMask) === $roleMask) {
				$result = $roleName;
				break;
			}
		}

		return $result;
	}

/**
 * Get list of UI languages
 *
 * @return array List of UI languages in format:
 *  - `key` - country code ISO 3166-2;
 *  - `value` - language name.
 */
	public function getUiLangsList() {
		$result = [];
		$UIlangs = $this->_modelConfigSettingsApp->getUiLangs();
		if (empty($UIlangs)) {
			return $result;
		}

		$language = new Language();
		foreach ($UIlangs as $country => $UIlang) {
			$langName = $language->convertLangCode($UIlang, 'native');
			if (empty($langName)) {
				continue;
			}

			$result[$country] = mb_ucfirst($langName);
		}

		return $result;
	}

/**
 * Return information about language of UI
 *
 * @param string $key Name of key for retrieve information.
 * @param boot $reverse If true reverse array information about
 *  language of UI.
 * @return string Return information about language of UI.
 */
	protected function _getUlangInfo($key = null, $reverse = false) {
		$result = '';
		if (empty($key)) {
			return $result;
		}

		$UIlangs = $this->_modelConfigSettingsApp->getUiLangs();
		if (empty($UIlangs)) {
			return $result;
		}

		if ($reverse) {
			$UIlangs = array_flip($UIlangs);
		}
		if (isset($UIlangs[$key])) {
			$result = $UIlangs[$key];
		}

		return $result;
	}

/**
 * Return country code by language name
 *
 * @param string $lang Language name.
 * @return string Return country code.
 */
	public function getCountryCodeByLanguage($lang = null) {
		$result = $this->_getUlangInfo($lang, true);

		return $result;
	}

/**
 * Return language name by country code
 *
 * @param string $country Country code.
 * @return string Return language name.
 */
	public function getLanguageByCountryCode($country = null) {
		$result = $this->_getUlangInfo($country, false);

		return $result;
	}

/**
 * Return array configurations.
 *
 * @param string $key The name of the parameter to retrieve the configurations.
 * @return mixed On success array configurations if its not empty or null on failure.
 */
	protected function _getConfig($key = null) {
		$language = Configure::read('Config.language');
		$key = (string)$key;

		if ($key === 'Language') {
			if (empty($language)) {
				return null;
			}

			$configs = $language;
		} else {
			$configPath = $this->_modelConfigSettingsApp->getNameConfigKey();
			if (!empty($key)) {
				$configPath .= '.' . $key;
			}

			$configs = Configure::read($configPath);
			if ($configs === null) {
				return null;
			}

			if (empty($key)) {
				$configs['Language'] = $language;
			}
		}

		if (!empty($key)) {
			$configs = [$key => $configs];
		}

		$listSerializedFields = $this->_modelConfigSettingsApp->getListSerializeFields();
		$result = [];
		foreach ($configs as $configName => $configValue) {
			switch ($configName) {
				case 'EmailSmtppassword':
					if (!empty($configValue)) {
						$result[$this->alias][$configName] = Security::decrypt(base64_decode($configValue), Configure::read('Security.key'));
					} else {
						$result[$this->alias][$configName] = $configValue;
					}
					break;
				default:
					if (in_array($configName, $listSerializedFields) && !empty($configValue)) {
						$configValue = unserialize($configValue);
					}
					$result[$this->alias][$configName] = $configValue;
			}
		}

		if (!empty($key)) {
			return Hash::get($result, $this->alias . '.' . $key);
		}

		if (empty($result)) {
			return null;
		}

		return $result;
	}

/**
 * Return array configurations.
 *
 * @param string $key The name of the parameter to retrieve the configurations.
 * @return mixed On success array configurations if its not empty or null on failure.
 * @triggers Model.afterFind $this, array($results, $primary)
 */
	public function getConfig($key = null) {
		$results = $this->_getConfig($key);
		if (empty($results)) {
			return $results;
		}

		$event = new CakeEvent('Model.afterFind', $this, [$results, true, $key]);
		$event->modParams = 0;
		$this->getEventManager()->dispatch($event);

		return $event->result;
	}

/**
 * Check application is correctly configured
 *
 * @return bool Success
 */
	public function isAuthGroupConfigured() {
		$markerFile = $this->getPathMarkerFile();
		if ($this->_checkMarkerFile($markerFile)) {
			return true;
		}

		$authGroupsList = $this->getAuthGroupsList();
		if (empty($authGroupsList)) {
			return false;
		}

		foreach ($authGroupsList as $fieldName) {
			if (!$this->getConfig($fieldName)) {
				return false;
			}
		}

		return $this->_createMarkerFile($markerFile);
	}

/**
 * Check marker file is exists
 *
 * @param string $path Path to marker file
 * @return bool Success
 */
	protected function _checkMarkerFile($path = null) {
		if (empty($path)) {
			return false;
		}

		return file_exists($path);
	}

/**
 * Create marker file
 *
 * @param string $path Path to marker file
 * @return bool Success
 */
	protected function _createMarkerFile($path = null) {
		if (empty($path)) {
			return false;
		}

		$oFile = new File($path, true);
		$now = time();
		$data = 'Configuration date: ' . CakeTime::i18nFormat($now, '%x %X');
		if (!$oFile->write($data, 'w', true)) {
			return false;
		}

		return $oFile->close();
	}

/**
 * Return plugin name.
 *
 * @return string Return plugin name for breadcrumb.
 */
	public function getPluginName() {
		$pluginName = 'cake_settings_app';

		return $pluginName;
	}

/**
 * Return controller name.
 *
 * @return string Return controller name for breadcrumb.
 */
	public function getControllerName() {
		$controllerName = 'settings';

		return $controllerName;
	}

/**
 * Return name of group data.
 *
 * @return string Return name of group data
 */
	public function getGroupName() {
		$groupName = __d('cake_settings_app', 'Application settings');

		return $groupName;
	}
}
