<?php
/**
 * This file is the model file of the application. Used for
 *  management configuration of plugin.
 *
 * CakeLdap: Authentication of users by member group of Active Directory.
 * @copyright Copyright 2017-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.Model
 */

App::uses('CakeLdapAppModel', 'CakeLdap.Model');
App::uses('ClassRegistry', 'Utility');

/**
 * The model is used for management configuration of plugin.
 *
 * @package plugin.Model
 */
class ConfigSync extends CakeLdapAppModel {

/**
 * Custom database table name, or null/false if no table association is desired.
 *
 * @var string
 * @link http://book.cakephp.org/2.0/en/models/model-attributes.html#usetable
 */
	public $useTable = false;

/**
 * List fields of associated models
 *
 * @var array
 */
	protected $_bindFields = [
		CAKE_LDAP_LDAP_ATTRIBUTE_DEPARTMENT => 'department_id',
		CAKE_LDAP_LDAP_ATTRIBUTE_MANAGER => 'manager_id',
		CAKE_LDAP_LDAP_ATTRIBUTE_OTHER_TELEPHONE_NUMBER => null,
		CAKE_LDAP_LDAP_ATTRIBUTE_OTHER_MOBILE_TELEPHONE_NUMBER => null,
	];

/**
 * Get configuration for plugin.
 *
 * @param string $key The name of the parameter to retrieve the configurations.
 * @return mixed Configuration for plugin
 */
	public function getConfig($key = null) {
		$configPath = 'CakeLdap';
		if (!empty($key)) {
			$configPath .= '.' . $key;
		}

		$result = Configure::read($configPath);

		return $result;
	}

/**
 * Return list of bind fields
 *
 * @return array Return list of bind fields.
 */
	protected function _getListBindFields() {
		return $this->_bindFields;
	}

/**
 * Return value of limit by configuration key.
 *
 * @param string $key Configuration key
 * @param int $default Default value of limit
 * @return int Value of limit
 */
	protected function _getLimit($key = null, $default = null) {
		$result = (int)$default;
		if (empty($key)) {
			return $result;
		}

		$data = $this->getConfig('LdapSync.Limits.' . $key);
		if (($data === null) || !is_int($data)) {
			return $result;
		}

		$data = (int)$data;
		if ($data < 0) {
			return $result;
		}

		return $data;
	}

/**
 * Return value of global query limit.
 *
 * @return int Value of limit
 */
	public function getQueryLimit() {
		return $this->_getLimit('Query', CAKE_LDAP_GLOBAL_QUERY_LIMIT);
	}

/**
 * Return value of limit records to synchronize.
 *
 * @return int Value of limit
 */
	public function getSyncLimit() {
		return $this->_getLimit('Sync', CAKE_LDAP_SYNC_AD_LIMIT);
	}

/**
 * Return company name for synchronization with AD.
 *
 * @return string Company name
 */
	public function getCompanyName() {
		$result = (string)$this->getConfig('LdapSync.Company');

		return $result;
	}

/**
 * Return state of flag enabling subordinate tree
 *
 * @return bool State of flag
 */
	public function getFlagTreeSubordinateEnable() {
		$flagTreeEnable = (bool)$this->getConfig('LdapSync.TreeSubordinate.Enable');
		if (!$flagTreeEnable) {
			return false;
		}

		$ldapFieldsInfo = $this->getLdapFieldsInfo();
		if (!isset($ldapFieldsInfo[CAKE_LDAP_LDAP_ATTRIBUTE_MANAGER])) {
			return false;
		}

		return true;
	}

/**
 * Return state of flag enabling changing position
 *  of employee in subordinate tree
 *
 * @return bool State of flag
 */
	public function getFlagTreeSubordinateDraggable() {
		$flagTreeDraggable = (bool)$this->getConfig('LdapSync.TreeSubordinate.Draggable');
		if (!$flagTreeDraggable) {
			return false;
		}

		if (!$this->getFlagTreeSubordinateEnable()) {
			return false;
		}

		return true;
	}

/**
 * Return state of flag enabling deleting information
 *  when synchronizing with AD for model Departments
 *
 * @return bool State of flag
 */
	public function getFlagDeleteDepartments() {
		$result = (bool)$this->getConfig('LdapSync.Delete.Departments');

		return $result;
	}

/**
 * Return state of flag enabling deleting information
 *  when synchronizing with AD for model Employees
 *
 * @return bool State of flag
 */
	public function getFlagDeleteEmployees() {
		$result = (bool)$this->getConfig('LdapSync.Delete.Employees');

		return $result;
	}

/**
 * Return state of flag enabling using LDAP multiple value
 *  fields in query
 *
 * @return bool State of flag
 */
	public function getFlagQueryUseFindByLdapMultipleFields() {
		return (bool)$this->getConfig('LdapSync.Query.UseFindByLdapMultipleFields');
	}

/**
 * Return distinguished name of the search base object
 *
 * @return string Distinguished name of the search base object
 */
	public function getSearchBase() {
		return (string)$this->getConfig('LdapSync.SearchBase');
	}

/**
 * Return informations about local fields of table `employees`
 *
 * @return array Informations about local fields
 */
	public function getLocalFieldsInfo() {
		$language = (string)Configure::read('Config.language');
		$cachePath = 'local_fields_info_' . $language;
		$cached = Cache::read($cachePath, CAKE_LDAP_CACHE_KEY_CONFIG);
		if ($cached !== false) {
			return $cached;
		}

		$localFieldsConfig = [
			'id' => [
				'label' => null,
				'altLabel' => null,
				'priority' => 0,
				'rules' => [
					'naturalNumber' => [
						'rule' => ['naturalNumber'],
						'message' => 'Incorrect primary key',
						'allowEmpty' => false,
						'required' => true,
						'last' => true,
						'on' => 'update'
					],
				],
				'default' => null
			],
			'department_id' => [
				'label' => null,
				'altLabel' => null,
				'priority' => 0,
				'rules' => [
					'naturalNumber' => [
						'rule' => ['naturalNumber'],
						'message' => 'Incorrect foreign key',
						'allowEmpty' => true,
						'required' => true,
						'last' => true,
					],
				],
				'default' => null
			],
			'manager_id' => [
				'label' => null,
				'altLabel' => null,
				'priority' => 0,
				'rules' => [
					'naturalNumber' => [
						'rule' => ['naturalNumber'],
						'message' => 'Incorrect foreign key',
						'allowEmpty' => true,
						'required' => false,
						'last' => true,
					],
				],
				'default' => null
			],
			'block' => [
				'label' => __d('cake_ldap_field_name', 'Block'),
				'altLabel' => __d('cake_ldap_field_name', 'Block'),
				'priority' => 100,
				'rules' => [
					'boolean' => [
						'rule' => ['boolean'],
						'message' => 'Incorrect state of blocking',
						'allowEmpty' => true,
						'required' => false,
						'last' => false,
					],
				],
				'default' => false
			],
		];

		$localFieldsList = [
			'id',
		];
		$ldapFieldsList = $this->_getListBindFields();
		$ldapFieldsConfig = $this->getLdapFieldsInfo();
		foreach ($ldapFieldsList as $ldapFieldName => $localFieldName) {
			if (isset($ldapFieldsConfig[$ldapFieldName]) && !empty($localFieldName)) {
				$localFieldsList[] = $localFieldName;
			}
		}
		if (!$this->getFlagDeleteEmployees()) {
			$localFieldsList[] = 'block';
		}

		$result = array_intersect_key($localFieldsConfig, array_flip($localFieldsList));
		Cache::write($cachePath, $result, CAKE_LDAP_CACHE_KEY_CONFIG);

		return $result;
	}

/**
 * Returns information about the local fields of table `employees`
 *  based on the `LdapFields` configuration from the plugin configuration file
 *
 * @return array Informations about local fields
 */
	public function getLdapFieldsInfo() {
		$language = (string)Configure::read('Config.language');
		$cachePath = 'ldap_fields_info_' . $language;
		$cached = Cache::read($cachePath, CAKE_LDAP_CACHE_KEY_CONFIG);
		if ($cached !== false) {
			return $cached;
		}

		$result = [];
		$data = (array)$this->getConfig('LdapSync.LdapFields');
		if (empty($data)) {
			return $result;
		}

		$modelEmployeeLdap = ClassRegistry::init('CakeLdap.EmployeeLdap');
		$schema = $modelEmployeeLdap->schema();
		if (empty($schema)) {
			$schema = [];
		}
		$requiredFields = $modelEmployeeLdap->getRequiredFields();
		$data += array_flip($requiredFields);
		$result = array_intersect_key($data, $schema);
		Cache::write($cachePath, $result, CAKE_LDAP_CACHE_KEY_CONFIG);

		return $result;
	}

/**
 * Returns list of local fields of table `employees`
 *
 * @return array List of local fields
 */
	public function getListFieldsDb() {
		$result = [];
		$fieldsInfo = $this->getLocalFieldsInfo();
		$fieldsInfo += $this->getLdapFieldsInfo();
		if (empty($fieldsInfo)) {
			return $result;
		}

		$excludeFields = $this->_getListBindFields();
		$fields = array_keys($fieldsInfo);
		$result = array_values(array_diff($fields, array_keys($excludeFields)));

		return $result;
	}

/**
 * Returns list of LDAP fields
 *
 * @return array List of LDAP fields
 */
	public function getListFieldsLdap() {
		$result = [];
		$fields = $this->getLdapFieldsInfo();
		$result = array_keys($fields);

		return $result;
	}
}
