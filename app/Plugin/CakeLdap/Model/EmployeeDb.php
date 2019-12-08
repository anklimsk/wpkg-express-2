<?php
/**
 * This file is the model file of the application. Used for
 *  management employees from DB.
 *
 * CakeLdap: Authentication of users by member group of Active Directory.
 * @copyright Copyright 2017-2019, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.Model
 */

App::uses('CakeLdapAppModel', 'CakeLdap.Model');
App::uses('ClassRegistry', 'Utility');
App::uses('Hash', 'Utility');

/**
 * The model is used to obtain information about employee from DB.
 *
 * @package plugin.Model
 */
class EmployeeDb extends CakeLdapAppModel {

/**
 * Custom display field name. Display fields are used by Scaffold, in SELECT boxes' OPTION elements.
 *
 * @var string
 * @link http://book.cakephp.org/2.0/en/models/model-attributes.html#displayfield
 */
	public $displayField = null;

/**
 * Custom database table name, or null/false if no table association is desired.
 *
 * @var string
 */
	public $useTable = 'employees';

/**
 * List of behaviors to load when the model object is initialized. Settings can be
 * passed to behaviors by using the behavior name as index.
 *
 * @var array
 * @link http://book.cakephp.org/2.0/en/models/behaviors.html#using-behaviors
 */
	public $actsAs = [
		'Containable',
		'CakeLdap.Sync',
		'CakeTheme.BreadCrumb'
	];

/**
 * List of validation rules. It must be an array with the field name as key and using
 * as value one of the following possibilities
 *
 * @var array
 * @link http://book.cakephp.org/2.0/en/models/model-attributes.html#validate
 * @link http://book.cakephp.org/2.0/en/models/data-validation.html
 */
	public $validate = [];

/**
 * Object of model `ConfigSync`
 *
 * @var object
 */
	protected $_modelConfigSync = null;

/**
 * Detailed list of associated models, grouped by binded field.
 *
 * @var array
 */
	protected $_bindModelCfg = [
		'department_id' => [
			'belongsTo' => [
				'Department' => [
					'className' => 'CakeLdap.DepartmentDb',
					'foreignKey' => 'department_id',
					'dependent' => false,
					'fields' => [
						'Department.id',
						'Department.value',
					]
				],
			],
		],
		'manager_id' => [
			'belongsTo' => [
				'Manager' => [
					'className' => 'CakeLdap.ManagerDb',
					'foreignKey' => 'manager_id',
					'dependent' => false,
					'fields' => [
						'Manager.id',
					]
				],
			],
			'hasMany' => [
				'Subordinate' => [
					'className' => 'CakeLdap.EmployeeDb',
					'foreignKey' => 'manager_id',
					'dependent' => false,
					'fields' => [
						'Subordinate.id',
					]
				],
			],
		],
		CAKE_LDAP_LDAP_ATTRIBUTE_OTHER_TELEPHONE_NUMBER => [
			'hasMany' => [
				'Othertelephone' => [
					'className' => 'CakeLdap.OthertelephoneDb',
					'foreignKey' => 'employee_id',
					'dependent' => true,
					'fields' => [
						'Othertelephone.id',
						'Othertelephone.value',
					]
				],
			],
		],
		CAKE_LDAP_LDAP_ATTRIBUTE_OTHER_MOBILE_TELEPHONE_NUMBER => [
			'hasMany' => [
				'Othermobile' => [
					'className' => 'CakeLdap.OthermobileDb',
					'foreignKey' => 'employee_id',
					'dependent' => true,
					'fields' => [
						'Othermobile.id',
						'Othermobile.value',
					]
				],
			]
		]
	];

/**
 * List fields of associated models (in format Hash path)
 *
 * @var array
 */
	protected $_bindModelFields = [
		CAKE_LDAP_LDAP_ATTRIBUTE_DEPARTMENT => 'Department.value',
		CAKE_LDAP_LDAP_ATTRIBUTE_OTHER_TELEPHONE_NUMBER => 'Othertelephone.{n}.value',
		CAKE_LDAP_LDAP_ATTRIBUTE_OTHER_MOBILE_TELEPHONE_NUMBER => 'Othermobile.{n}.value',
		CAKE_LDAP_LDAP_ATTRIBUTE_MANAGER => 'Manager.name',
	];

/**
 * List of fields for ordering data
 *
 * @var array
 */
	protected $_orderList = [
		CAKE_LDAP_LDAP_ATTRIBUTE_NAME,
		CAKE_LDAP_LDAP_ATTRIBUTE_DISPLAY_NAME,
		CAKE_LDAP_LDAP_ATTRIBUTE_SURNAME
	];

/**
 * List of fields for update query
 *
 * @var array
 */
	protected $_bindModelCfgQuery = [
		CAKE_LDAP_LDAP_ATTRIBUTE_DEPARTMENT => [
			'replace' => 'Department.value',
		],
		CAKE_LDAP_LDAP_ATTRIBUTE_OTHER_TELEPHONE_NUMBER => [
			'replace' => 'Othertelephone.value',
			'subQuery' => [
				'fields' => [
					'Othertelephone.employee_id',
				],
				'table' => 'othertelephones',
				'alias' => 'Othertelephone',
			]
		],
		CAKE_LDAP_LDAP_ATTRIBUTE_OTHER_MOBILE_TELEPHONE_NUMBER => [
			'replace' => 'Othermobile.value',
			'subQuery' => [
				'fields' => [
					'Othermobile.employee_id',
				],
				'table' => 'othermobiles',
				'alias' => 'Othermobile',
			]
		],
	];

/**
 * Constructor. Binds the model's database table to the object.
 *
 * @param bool|int|string|array $id Set this ID for this model on startup,
 * can also be an array of options, see above.
 * @param string $table Name of database table to use.
 * @param string $ds DataSource connection name.
 */
	public function __construct($id = false, $table = null, $ds = null) {
		$this->_setDisplayField();
		$this->_modelConfigSync = ClassRegistry::init('CakeLdap.ConfigSync');

		parent::__construct($id, $table, $ds);
		$this->_bindModelByCfg();
		$this->_createValidationRules();
		$this->_updateListFieldsBindModelQuery();
	}

/**
 * Set Display field of model
 *
 * @return bool Return success.
 */
	protected function _setDisplayField() {
		$orderField = $this->_getOrderField();
		if (empty($orderField)) {
			return false;
		}

		$this->displayField = $orderField;

		return true;
	}

/**
 * Bind associated model by table fields
 *
 * @return bool Return success.
 */
	protected function _bindModelByCfg() {
		$bindModelCfg = $this->_getBindModelCfg();
		if (empty($bindModelCfg)) {
			return true;
		}

		return $this->bindModel($bindModelCfg, false);
	}

/**
 * Create validation rules by table fields
 *
 * @return bool Return success.
 */
	protected function _createValidationRules() {
		$validationRules = $this->_getValidationRules();
		if (empty($validationRules)) {
			return false;
		}

		$validator = $this->validator();
		foreach ($validationRules as $validationField => $validationRule) {
			$validator[$validationField] = $validationRule;
		}

		return true;
	}

/**
 * Update list of fields for updating query
 *
 * @return bool Return success.
 */
	protected function _updateListFieldsBindModelQuery() {
		$this->_bindModelCfgQuery[CAKE_LDAP_LDAP_ATTRIBUTE_MANAGER] = [
			'replace' => 'Manager.' . $this->displayField,
		];

		return true;
	}

/**
 * Return list of associated models, grouped by binded field
 *
 * @return array Return list of associated models.
 */
	protected function _getListBindModelCfg() {
		return $this->_bindModelCfg;
	}

/**
 * Return configuration for binding associated models
 *
 * @param array|string $fields List of fields for checking on
 *  binding associated models
 * @return array Return configuration for binding models.
 */
	protected function _getBindModelCfg($fields = []) {
		$fieldsDb = $this->_modelConfigSync->getListFieldsDb();
		$fieldsLdap = $this->_modelConfigSync->getListFieldsLdap();
		$fieldsList = array_keys(array_flip($fieldsDb) + array_flip($fieldsLdap));
		if (!empty($fields)) {
			$fieldsList = array_intersect($fieldsList, (array)$fields);
		}
		$result = [];
		if (empty($fieldsList)) {
			return $result;
		}

		$bindModelCfg = $this->_getListBindModelCfg();
		foreach ($bindModelCfg as $checkField => $bindInfo) {
			if (!in_array($checkField, $fieldsList)) {
				continue;
			}

			foreach ($bindInfo as $bindType => $targetModels) {
				foreach ($targetModels as $targetModel => $targetModelInfo) {
					switch ($targetModel) {
						case 'Department':
							$modelDepartment = ClassRegistry::init('Department', true);
							if ($modelDepartment !== false) {
								$targetModelInfo['className'] = 'Department';
								$useBlockDepartment = $modelDepartment->hasField('block');
								if ($useBlockDepartment) {
									$targetModelInfo['fields'][] = $targetModel . '.block';
								}
							}
							break;
						case 'Manager':
						case 'Subordinate':
							$modelEmployee = ClassRegistry::init('Employee', true);
							if ($modelEmployee !== false) {
								$targetModelInfo['className'] = 'Employee';
							}
							$targetModelInfo['fields'][] = $targetModel . '.' . $this->displayField . ' AS name';
							if ($targetModel === 'Subordinate') {
								$targetModelInfo['order'] = [$targetModel . '.' . $this->displayField => 'asc'];
							}
							if ($this->hasField(CAKE_LDAP_LDAP_ATTRIBUTE_TITLE)) {
								$targetModelInfo['fields'][] = $targetModel . '.' . CAKE_LDAP_LDAP_ATTRIBUTE_TITLE;
							}
							break;
					}
					$result[$bindType][$targetModel] = $targetModelInfo;
				}
			}
		}

		return $result;
	}

/**
 * Return list of fields for ordering data
 *
 * @return array Return list of fields for ordering data.
 */
	protected function _getOrderList() {
		return (array)$this->_orderList;
	}

/**
 * Return list of fields for updating query conditions
 *
 * @return array Return list of fields for updating query conditions.
 */
	protected function _getListFieldsUpdateQueryConditions() {
		return (array)$this->_bindModelCfgQuery;
	}

/**
 * Return list of fields for updating query result
 *
 * @return array Return list of fields for updating query result.
 */
	protected function _getListFieldsUpdateQueryResult() {
		$cachePath = 'local_fields_update_query_res';
		$cached = Cache::read($cachePath, CAKE_LDAP_CACHE_KEY_LDAP_SYNC_DB);
		if ($cached !== false) {
			return $cached;
		}

		$result = [];
		$bindFields = $this->_getListFieldsUpdateQueryConditions();
		if (empty($bindFields)) {
			return $result;
		}

		foreach ($bindFields as $bindFieldName => $bindFieldInfo) {
			if (isset($bindFieldInfo['subQuery'])) {
				$result[] = $bindFieldName;
			}
		}

		Cache::write($cachePath, $result, CAKE_LDAP_CACHE_KEY_LDAP_SYNC_DB);

		return $result;
	}

/**
 * Return field for ordering data
 *
 * @return string|null Return field for ordering data, or Null.
 */
	protected function _getOrderField() {
		$orderList = $this->_getOrderList();
		$result = null;
		foreach ($orderList as $orderField) {
			if (!$this->hasField($orderField)) {
				continue;
			}

			$result = $orderField;
			break;
		}

		return $result;
	}

/**
 * Return list of validation rules for `local` fields
 *
 * @return array Return list of validation rules.
 */
	protected function _getLocalFieldsValidationRules() {
		$result = [];
		$localFields = $this->_modelConfigSync->getLocalFieldsInfo();
		if (empty($localFields)) {
			return $result;
		}

		foreach ($localFields as $localFieldName => $localFieldInfo) {
			if (!isset($localFieldInfo['rules']) || empty($localFieldInfo['rules'])) {
				continue;
			}

			$result[$localFieldName] = $localFieldInfo['rules'];
		}

		return $result;
	}

/**
 * Update query conditions for find by multiple value fields.
 *
 * @param array &$conditions query conditions for update.
 * @return void
 */
	protected function _prepareQueryConditions(&$conditions) {
		if (empty($conditions) || !is_array($conditions)) {
			return;
		}

		$bindFields = $this->_getListFieldsUpdateQueryConditions();
		$newConditions = [];
		foreach ($conditions as $field => &$value) {
			if (ctype_digit((string)$field)) {
				$field = $value;
			}

			if (in_array($field, ['AND', 'OR', 'NOT']) && is_array($value)) {
				$this->_prepareQueryConditions($value);
				continue;
			}

			if (!is_string($field)) {
				continue;
			}

			foreach ($bindFields as $bindField => $bindFieldInfo) {
				$fieldName = $field;
				if (strpos($fieldName, ' ') !== false) {
					$fieldName = explode(' ', $fieldName, 2);
					$fieldName = array_shift($fieldName);
				}
				if ($fieldName !== $this->alias . '.' . $bindField) {
					continue;
				}

				unset($conditions[$field]);
				$field = str_replace($this->alias . '.' . $bindField, $bindFieldInfo['replace'], $field);
				if (!isset($bindFieldInfo['subQuery'])) {
					$newConditions[$field] = $value;
					continue;
				}

				$conditionsSubQuery = [$field => $value];
				$db = $this->getDataSource();
				$subQuery = $db->buildStatement(
					[
						'limit' => null,
						'offset' => null,
						'joins' => [],
						'conditions' => $conditionsSubQuery,
						'order' => null,
						'group' => null
					] + $bindFieldInfo['subQuery'],
					$this
				);
				$subQuery = 'Employee.id IN (' . $subQuery . ') ';
				$subQueryExpression = $db->expression($subQuery);

				$newConditions[] = $subQueryExpression;
			}
		}
		$conditions = array_merge($conditions, $newConditions);
	}

/**
 * Called before each find operation. Return false if you want to halt the find
 * call, otherwise return the (modified) query data.
 *
 * Actions:
 *  - Update query conditions for find by multiple value fields.
 *
 * @param array $query Data used to execute this query, i.e. conditions, order, etc.
 * @return mixed true if the operation should continue, false if it should abort; or, modified
 *  $query to continue with new $query
 * @link http://book.cakephp.org/2.0/en/models/callback-methods.html#beforefind
 */
	public function beforeFind($query) {
		if (!$this->_modelConfigSync->getFlagQueryUseFindByLdapMultipleFields()) {
			return parent::beforeFind($query);
		}

		$this->_prepareQueryConditions($query['conditions']);
		$parentQuery = parent::beforeFind($query);
		if (!$parentQuery) {
			return false;
		} elseif ($parentQuery !== true) {
			$query = $parentQuery;
		}

		return $query;
	}

/**
 * Called after each find operation. Can be used to modify any results returned by find().
 * Return value should be the (modified) results.
 *
 * Actions:
 *  - Update result if used find by multiple value fields.
 *
 * @param mixed $results The results of the find operation
 * @param bool $primary Whether this model is being queried directly (vs. being queried as an association)
 * @return mixed Result of the find operation
 * @link http://book.cakephp.org/2.0/en/models/callback-methods.html#afterfind
 */
	public function afterFind($results, $primary = false) {
		$resultParent = parent::afterFind($results, $primary);
		if (!$resultParent || empty($results) ||
			!$this->_modelConfigSync->getFlagQueryUseFindByLdapMultipleFields()) {
			return $resultParent;
		}

		$removeFields = $this->_getListFieldsUpdateQueryResult();
		if (empty($removeFields)) {
			return $resultParent;
		}

		if ($resultParent !== true) {
			$results = $resultParent;
		}

		$removeFields = array_flip($removeFields);
		foreach ($results as &$result) {
			if (!isset($result[$this->alias])) {
				continue;
			}

			$result[$this->alias] = array_diff_key($result[$this->alias], $removeFields);
		}

		return $results;
	}

/**
 * Return list of default values for fields
 *
 * @return array Return list of default values.
 */
	public function getFieldsDefaultValue() {
		$cachePath = 'local_fields_default_val';
		$cached = Cache::read($cachePath, CAKE_LDAP_CACHE_KEY_LDAP_SYNC_DB);
		if ($cached !== false) {
			return $cached;
		}

		$result = [];
		$fieldsInfo = $this->_modelConfigSync->getLocalFieldsInfo();
		$fieldsInfo += $this->_modelConfigSync->getLdapFieldsInfo();
		if (empty($fieldsInfo)) {
			return $result;
		}

		$schema = $this->schema();
		foreach ($fieldsInfo as $fieldName => $fieldInfo) {
			if (!isset($schema[$fieldName])) {
				continue;
			}

			$defaultValue = null;
			if (isset($fieldInfo['default'])) {
				$defaultValue = $fieldInfo['default'];
			}

			$result[$fieldName] = $defaultValue;
		}

		Cache::write($cachePath, $result, CAKE_LDAP_CACHE_KEY_LDAP_SYNC_DB);

		return $result;
	}

/**
 * Return list of validation rules for `ldap` fields
 *
 * @return array Return list of validation rules.
 */
	protected function _getLdapFieldsValidationRules() {
		$result = [];
		$ldapFields = $this->_modelConfigSync->getLdapFieldsInfo();
		if (empty($ldapFields)) {
			return $result;
		}

		$localFields = $this->getListLocalFields();
		if (empty($localFields)) {
			return $result;
		}

		foreach ($ldapFields as $ldapFieldName => $ldapFieldInfo) {
			if (!isset($ldapFieldInfo['rules']) || empty($ldapFieldInfo['rules'])) {
				continue;
			}

			if (!in_array($ldapFieldName, $localFields)) {
				continue;
			}

			$result[$ldapFieldName] = $ldapFieldInfo['rules'];
		}

		return $result;
	}

/**
 * Return list of validation rules for `local` and `ldap` fields
 *
 * @return array Return list of validation rules.
 */
	protected function _getValidationRules() {
		$language = (string)Configure::read('Config.language');
		$cachePath = 'validation_rules_' . $language;
		$cached = Cache::read($cachePath, CAKE_LDAP_CACHE_KEY_LDAP_SYNC_DB);
		if ($cached !== false) {
			return $cached;
		}

		$result = $this->_getLocalFieldsValidationRules();
		$result += $this->_getLdapFieldsValidationRules();
		Cache::write($cachePath, $result, CAKE_LDAP_CACHE_KEY_LDAP_SYNC_DB);

		return $result;
	}

/**
 * Return list of models for contain behavior
 *
 * @param array|string $excludeModels List of models for excluding
 *  from result
 * @param array|string $fields List of fields for checking on
 *  contain associated models
 * @param array|string $excludeFields List of fields for exclude from
 *  checking on contain associated models
 * @return array Return list of models for contain behavior.
 */
	public function getListContain($excludeModels = null, $fields = null, $excludeFields = null) {
		$excludeModels = (array)$excludeModels;
		$fields = (array)$fields;
		$excludeFields = (array)$excludeFields;
		$bindModelCfgFull = $this->_getListBindModelCfg();
		$fieldsContainCheck = array_keys($bindModelCfgFull);
		$listFieldsDb = $this->_modelConfigSync->getListFieldsDb();
		$listFieldsLdap = $this->_modelConfigSync->getListFieldsLdap();
		if (!empty($fields)) {
			$fieldsContainCheck = array_unique(array_merge($fieldsContainCheck, $fields));
		}
		if (!empty($excludeFields)) {
			$fieldsContainCheck = array_diff($fieldsContainCheck, (array)$excludeFields);
		}
		if (!empty($fields)) {
			foreach ($fieldsContainCheck as $i => &$fieldCheck) {
				if ((in_array($fieldCheck, $listFieldsDb) && !in_array($fieldCheck, $fields)) || (
					!in_array($fieldCheck, $listFieldsDb) && !in_array($fieldCheck, $listFieldsLdap))) {
					unset($fieldsContainCheck[$i]);
				}
			}
		}

		$bindModelCfg = $this->_getBindModelCfg($fieldsContainCheck);
		$result = [];
		foreach ($bindModelCfg as $bindType => $targetModels) {
			$result = array_merge($result, array_keys($targetModels));
		}

		if (!empty($excludeModels)) {
			$result = array_values(array_diff($result, $excludeModels));
		}

		return $result;
	}

/**
 * Return list of local fields
 *
 * @param array|string $excludeFields List of fields for excluding
 *  from result
 * @return array Return list of local fields.
 */
	public function getListLocalFields($excludeFields = null) {
		$excludeFields = (array)$excludeFields;
		$schema = $this->schema();
		$result = array_keys($schema);
		if (!empty($excludeFields)) {
			$result = array_values(array_diff($result, (array)$excludeFields));
		}

		return $result;
	}

/**
 * Return list of bind model fields
 *
 * @return array Return list of bind model fields.
 */
	protected function _getListBindModelFields() {
		return $this->_bindModelFields;
	}

/**
 * Return field for ordering data as array
 *
 * @return array Return field for ordering data.
 */
	public function getOrderFiled() {
		$result = [];
		$orderField = $this->_getOrderField();
		if (empty($orderField)) {
			return $result;
		}

		$result[$this->alias . '.' . $orderField] = 'asc';

		return $result;
	}

/**
 * Return conditions for find employee
 *
 * @param string|null $id ID of record, or GUID, or Distinguished Name of employee
 * @return array Return conditions for find employee.
 */
	protected function _getConditionsForEmployee($id = null) {
		$conditions = [];
		if (empty($id)) {
			return $conditions;
		}

		$id = (string)$id;
		if (ctype_digit($id)) {
			$conditions[$this->alias . '.id'] = $id;
		} elseif (isGuid($id)) {
			$conditions[$this->alias . '.' . CAKE_LDAP_LDAP_ATTRIBUTE_OBJECT_GUID] = $id;
		} else {
			$conditions[$this->alias . '.' . CAKE_LDAP_LDAP_ATTRIBUTE_DISTINGUISHED_NAME] = $id;
		}

		return $conditions;
	}

/**
 * Check employee exists
 *
 * @param string|null $id ID of record, or GUID, or Distinguished Name of employee
 * @return bool Return Success.
 */
	public function existsEmployee($id = null) {
		if (empty($id)) {
			return false;
		}

		$conditions = $this->_getConditionsForEmployee($id);
		$callbacks = false;
		$this->recursive = -1;

		return (bool)$this->find('count', compact('conditions', 'callbacks'));
	}

/**
 * Return information about a employee
 *
 * @param string|null $id ID of record, or GUID, or Distinguished Name of employee
 *  for retieve information
 * @param array|string $excludeFields List of fields for excluding
 *  from result
 * @param bool $includeExtend Flag of including extended information in result
 *  e.g. information about tree subordinate employees.
 * @param array|string $fieldsList List of fields for retieve information
 * @param array|string $contain List of binded models
 * @return array|bool Return array of informationa about a employee,
 *  or False on failure.
 */
	public function get($id = null, $excludeFields = null, $includeExtend = false, $fieldsList = null, $contain = null) {
		if (empty($id)) {
			return false;
		}

		if (empty($contain)) {
			$contain = [];
		} elseif (!is_array($contain)) {
			$contain = [$contain];
		}
		$fields = $this->getListLocalFields($excludeFields);
		if (!empty($fieldsList)) {
			$fields = array_intersect($fields, (array)$fieldsList);
		}
		$conditions = $this->_getConditionsForEmployee($id);
		$containInt = $this->getListContain(null, $fields, $excludeFields);
		$contain = array_merge($containInt, $contain);
		$result = $this->find('first', compact('fields', 'conditions', 'contain'));
		if (!$includeExtend) {
			return $result;
		}

		if ($this->_modelConfigSync->getFlagTreeSubordinateEnable() &&
			in_array('manager_id', $fields)) {
			$result['Subordinate'] = [];
			$employeeId = Hash::get($result, $this->alias . '.id');
			if (!empty($employeeId)) {
				$modelSubordinateDb = ClassRegistry::init('CakeLdap.SubordinateDb');
				$result['Subordinate'] = $modelSubordinateDb->getArrayTreeEmployee($employeeId, false);
			}
		}

		return $result;
	}

/**
 * Return a list of employees
 *
 * @param bool $includeBlock Flag of inclusion in result the blocked
 *  employees (if True).
 * @return array Return array list of employees.
 */
	public function getListEmployees($includeBlock = true) {
		$fields = [
			$this->alias . '.id',
			$this->alias . '.' . $this->displayField,
		];
		$conditions = [];
		$blockExists = $this->hasField('block');
		if (!$includeBlock && $blockExists) {
			$conditions[$this->alias . '.block'] = false;
		}
		$order = $this->getOrderFiled();
		$this->recursive = -1;

		return $this->find('list', compact('fields', 'conditions', 'order'));
	}

/**
 * Return array information of all employees
 *
 * @param string $guid GUID of employee
 * @param int|string $limit Limit for result
 * @return array|null Return array of informationa about a employees,
 *  or Null if no result.
 */
	public function getAllEmployees($guid = null, $limit = CAKE_LDAP_SYNC_AD_LIMIT) {
		$fields = $this->getListLocalFields();
		$conditions = [];
		if (!empty($guid)) {
			$conditions[$this->alias . '.' . CAKE_LDAP_LDAP_ATTRIBUTE_OBJECT_GUID] = $guid;
		}
		$order = [$this->alias . '.' . CAKE_LDAP_LDAP_ATTRIBUTE_OBJECT_GUID => 'asc'];
		$contain = $this->getListContain(['Manager', 'Subordinate']);
		$result = $this->find('all', compact(
			'fields',
			'conditions',
			'order',
			'limit',
			'contain'
		));

		return $result;
	}

/**
 * Return list of managers for synchronization tree of
 *  subordinate employees
 *
 * @param string $guid GUID of manager
 * @param int|string $limit Limit for result
 * @return array Return list of managers.
 */
	public function getListEmployeesManager($guid = null, $limit = CAKE_LDAP_SYNC_AD_LIMIT) {
		$result = [];
		if (!$this->hasField('manager_id')) {
			return $result;
		}

		$conditions = [];
		if (!empty($guid)) {
			$conditions[$this->alias . '.' . CAKE_LDAP_LDAP_ATTRIBUTE_OBJECT_GUID] = $guid;
		}
		$fields = [
			$this->alias . '.id',
			$this->alias . '.manager_id',
			$this->alias . '.' . $this->displayField . ' AS name',
		];
		$order = [
			$this->alias . '.id' => 'asc',
		];
		$this->recursive = -1;
		$data = $this->find('all', compact('fields', 'conditions', 'order', 'limit'));
		if (empty($data)) {
			return $result;
		}

		$result = Hash::combine($data, '{n}.' . $this->alias . '.id', '{n}.' . $this->alias);

		return $result;
	}

/**
 * Return options for Paginator component
 *
 * @param array|string $excludeFields List of fields for excluding
 *  from result
 * @return array Return array options for Paginator component.
 */
	public function getPaginateOptions($excludeFields = null) {
		$page = 1;
		$limit = 20;
		$fields = $this->getListLocalFields($excludeFields);
		$order = $this->getOrderFiled();
		$contain = $this->getListContain('Subordinate');
		$result = compact(
			'page',
			'limit',
			'fields',
			'order',
			'contain'
		);

		return $result;
	}

/**
 * Return options for Filter component
 *
 * @return array Return array options for Filter component.
 */
	public function getFilterOptions() {
		$language = (string)Configure::read('Config.language');
		$cachePath = 'local_fields_filter_opt_' . $language;
		$cached = Cache::read($cachePath, CAKE_LDAP_CACHE_KEY_LDAP_SYNC_DB);
		if ($cached !== false) {
			return $cached;
		}

		$result = [];
		$excludeFields = [
			'id',
			'department_id',
			'manager_id',
			'lft',
			'rght',
			CAKE_LDAP_LDAP_ATTRIBUTE_OBJECT_GUID,
			CAKE_LDAP_LDAP_ATTRIBUTE_DISTINGUISHED_NAME,
		];
		$excludeFieldsLabel = [
			$this->alias . '.' . CAKE_LDAP_LDAP_ATTRIBUTE_OBJECT_GUID,
			$this->alias . '.' . CAKE_LDAP_LDAP_ATTRIBUTE_DISTINGUISHED_NAME,
		];

		$fieldsInfo = $this->_modelConfigSync->getLocalFieldsInfo();
		$fieldsInfo += $this->_modelConfigSync->getLdapFieldsInfo();
		$fieldsInfo = array_diff_key($fieldsInfo, array_flip($excludeFields));
		$fieldsLabels = $this->_getLabelForFields($fieldsInfo, true);
		foreach ($fieldsLabels as $fieldName => $label) {
			$fieldOptions = [];
			if (!empty($label)) {
				$fieldOptions['label'] = $label;
			}
			if ($this->_isHashPath($fieldName)) {
				$fieldOptions['disabled'] = true;
			}
			$result[$fieldName] = $fieldOptions;
		}
		Cache::write($cachePath, $result, CAKE_LDAP_CACHE_KEY_LDAP_SYNC_DB);

		return $result;
	}

/**
 * Get information about extended fields
 *
 * @return array Return array of information about extended
 *  fields in format:
 *   array(
 *      'modelName' => array(
 *              'label' => 'Full label',
 *              'altLabel' => 'Short label',
 *              'priority' => 10,
 *          )
 *  ),
 *  where: modelName - is name of model, or Hash::path
 */
	public function getExtendFieldsInfo() {
		$result = [
			'Subordinate.{n}' => [
				'label' => __d('cake_ldap_field_name', 'Subordinate'),
				'altLabel' => __d('cake_ldap_field_name', 'Subord.'),
				'priority' => 10,
			]
		];

		return $result;
	}

/**
 * Return list of labels for fields order by Priority
 *
 * @param array $fieldsInfo Informations about fields
 * @param bool $useAlternative If True, use alternative label,
 *  or normanl label otherwise.
 * @param bool $isExtendInfo If True, use use field name as is,
 *  otherwise include model name.
 * @return array Return list of labels for fields.
 */
	protected function _getLabelForFields($fieldsInfo = [], $useAlternative = false, $isExtendInfo = false) {
		$result = [];
		if (empty($fieldsInfo) || !is_array($fieldsInfo)) {
			return $result;
		}

		$labelCfgField = 'label';
		if ($useAlternative) {
			$labelCfgField = 'altLabel';
		}

		$bindModelFields = $this->_getListBindModelFields();
		$fieldsInfo = Hash::sort($fieldsInfo, '{s}.priority', 'asc');
		foreach ($fieldsInfo as $fieldName => $fieldInfo) {
			if (!isset($fieldInfo[$labelCfgField])) {
				continue;
			}

			if (empty($fieldInfo[$labelCfgField])) {
				$label = mb_convert_case($fieldName, MB_CASE_TITLE);
			} else {
				$label = $fieldInfo[$labelCfgField];
			}

			if (!$isExtendInfo) {
				if (isset($bindModelFields[$fieldName])) {
					$fieldName = $bindModelFields[$fieldName];
				} else {
					$fieldName = $this->alias . '.' . $fieldName;
				}
			}
			$result[$fieldName] = $label;
		}

		return $result;
	}

/**
 * Return list of labels for fields order by Priority
 *
 * @param array|string $excludeFields List of fields for excluding
 *  from result in format: Model.field
 * @param bool $useAlternative If True, use alternative label,
 *  or normanl label otherwise.
 * @return array Return list of labels for fields.
 */
	public function getListFieldsLabel($excludeFields = null, $useAlternative = false) {
		if (empty($excludeFields)) {
			$excludeFields = [];
		} elseif (!is_array($excludeFields)) {
			$excludeFields = [$excludeFields];
		}

		$language = (string)Configure::read('Config.language');
		$cachePath = 'local_fields_label_' .
			md5(serialize(func_get_args()) . '_' . $language);
		$cached = Cache::read($cachePath, CAKE_LDAP_CACHE_KEY_LDAP_SYNC_DB);
		if ($cached !== false) {
			return $cached;
		}

		$resultOnlyFields = [];
		$fieldsInfo = $this->_modelConfigSync->getLocalFieldsInfo();
		$fieldsInfo += $this->_modelConfigSync->getLdapFieldsInfo();
		if (empty($fieldsInfo)) {
			return $resultOnlyFields;
		}

		$resultOnlyFields = $this->_getLabelForFields($fieldsInfo, $useAlternative);
		if (!empty($excludeFields)) {
			$excludeFields = array_flip($excludeFields);
			$resultOnlyFields = array_diff_key($resultOnlyFields, $excludeFields);
		}
		$result = $resultOnlyFields;
		Cache::write($cachePath, $result, CAKE_LDAP_CACHE_KEY_LDAP_SYNC_DB);

		return $result;
	}

/**
 * Return list of labels for extended fields order by Priority
 *
 * @param bool $useAlternative If True, use alternative label,
 *  or normanl label otherwise.
 * @param array|string $excludeFields List of fields for excluding
 *  from result
 * @return array Return list of labels for fields.
 */
	public function getListFieldsLabelExtend($useAlternative = false, $excludeFields = null) {
		$language = (string)Configure::read('Config.language');
		$cachePath = 'local_fields_label_ext_' .
			md5(serialize(func_get_args()) . '_' . $language);
		$cached = Cache::read($cachePath, CAKE_LDAP_CACHE_KEY_LDAP_SYNC_DB);
		if ($cached !== false) {
			return $cached;
		}

		$fieldsInfo = $this->getExtendFieldsInfo();
		$result = $this->_getLabelForFields($fieldsInfo, $useAlternative, true);
		if (!empty($excludeFields)) {
			$result = array_diff_key($result, array_flip((array)$excludeFields));
		}

		Cache::write($cachePath, $result, CAKE_LDAP_CACHE_KEY_LDAP_SYNC_DB);

		return $result;
	}

/**
 * Check field name is Hash path
 *
 * @param string $fieldName Field name for checking.
 * @return bool Success.
 */
	protected function _isHashPath($fieldName = null) {
		if (empty($fieldName)) {
			return false;
		}

		$pos = mb_stripos($fieldName, '.{n}.');
		if ($pos !== false) {
			return true;
		}

		return false;
	}

/**
 * Return fields configuration for helper
 *
 * @return array Return array of information about extended
 *  fields in format:
 *   array(
 *      'modelName' => array(
 *              'type' => 'string',
 *              'truncate' => false,
 *          )
 *  ),
 *  where:
 *   - modelName - is name of model, or Hash::path.
 *   - type - type of data. Can be one of:
 *   integer, biginteger, float, date, time, datetime,
 *   timestamp, boolean, guid, photo, mail, string, text,
 *   binary, employee, manager, subordinate, department or
 *   element.
 *   - truncate - truncate text.
 */
	public function getExtendFieldsConfig() {
		$result = [
			'Subordinate.{n}' => [
				'type' => 'element',
				'truncate' => false,
			]
		];

		return $result;
	}

/**
 * Return fields configuration for helper
 *
 * @return array Return array fields configuration.
 */
	public function getFieldsConfig() {
		$cachePath = 'local_fields_fields_schema';
		$cached = Cache::read($cachePath, CAKE_LDAP_CACHE_KEY_LDAP_SYNC_DB);
		if ($cached !== false) {
			return $cached;
		}

		$result = [];
		$schema = $this->schema();
		$ldapFieldsInfo = $this->_modelConfigSync->getLdapFieldsInfo();
		$bindModelFields = $this->_getListBindModelFields();
		foreach ($schema as $fieldName => $fieldInfo) {
			switch ($fieldName) {
				case CAKE_LDAP_LDAP_ATTRIBUTE_OBJECT_GUID:
					$type = 'guid';
					break;
				case CAKE_LDAP_LDAP_ATTRIBUTE_BIRTHDAY:
					$type = 'date';
					break;
				case CAKE_LDAP_LDAP_ATTRIBUTE_PHOTO:
					$type = 'photo';
					break;
				case CAKE_LDAP_LDAP_ATTRIBUTE_MAIL:
					$type = 'mail';
					break;
				default:
					$type = $fieldInfo['type'];
			}
			$truncate = (bool)Hash::get($ldapFieldsInfo, $fieldName . '.truncate');
			$result[$this->alias . '.' . $fieldName] = compact('type', 'truncate');
		}

		foreach ($bindModelFields as $fieldName => $bindFieldName) {
			switch ($fieldName) {
				case CAKE_LDAP_LDAP_ATTRIBUTE_DEPARTMENT:
					//$type = 'department';
					$type = 'string';
					break;
				case CAKE_LDAP_LDAP_ATTRIBUTE_MANAGER:
					$type = 'manager';
					break;
				case CAKE_LDAP_LDAP_ATTRIBUTE_OTHER_TELEPHONE_NUMBER:
				case CAKE_LDAP_LDAP_ATTRIBUTE_OTHER_MOBILE_TELEPHONE_NUMBER:
					$type = 'string';
					break;
			}
			$truncate = (bool)Hash::get($ldapFieldsInfo, $fieldName . '.truncate');
			$result[$bindFieldName] = compact('type', 'truncate');
		}

		$extendFieldsConfig = $this->getExtendFieldsConfig();
		if (!empty($extendFieldsConfig) && is_array($extendFieldsConfig)) {
			$result += $extendFieldsConfig;
		}
		Cache::write($cachePath, $result, CAKE_LDAP_CACHE_KEY_LDAP_SYNC_DB);

		return $result;
	}

/**
 * Return plugin name.
 *
 * @return string Return plugin name for breadcrumb.
 */
	public function getPluginName() {
		$pluginName = 'cake_ldap';

		return $pluginName;
	}

/**
 * Return controller name.
 *
 * @return string Return controller name for breadcrumb.
 */
	public function getControllerName() {
		$controllerName = 'employees';

		return $controllerName;
	}

/**
 * Return name of group data.
 *
 * @return string Return name of group data
 */
	public function getGroupName() {
		$groupName = __d('cake_ldap', 'Employees');

		return $groupName;
	}
}
