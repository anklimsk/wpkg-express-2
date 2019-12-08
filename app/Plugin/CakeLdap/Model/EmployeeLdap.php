<?php
/**
 * This file is the model file of the application. Used for
 *  management employees from LDAP.
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
 * The model is used to obtain information about employee from LDAP.
 *
 * @package plugin.Model
 */
class EmployeeLdap extends CakeLdapAppModel {

/**
 * The name of the DataSource connection that this Model uses
 *
 * The value must be an attribute name that you defined in `app/Config/database.php`
 * or created using `ConnectionManager::create()`.
 *
 * @var string
 * @link http://book.cakephp.org/2.0/en/models/model-attributes.html#usedbconfig
 */
	public $useDbConfig = 'ldap';

/**
 * Custom database table name, or null/false if no table association is desired.
 *
 * @var string
 */
	public $useTable = '';

/**
 * The name of the primary key field for this model.
 *
 * @var string
 * @link http://book.cakephp.org/2.0/en/models/model-attributes.html#primarykey
 */
	public $primaryKey = CAKE_LDAP_LDAP_DISTINGUISHED_NAME;

/**
 * Store list of fields on find method
 *
 * @var array
 */
	protected $_findFields = null;

/**
 * List of required fields
 *
 * @var array
 */
	protected $_requiredFields = [
		CAKE_LDAP_LDAP_ATTRIBUTE_OBJECT_GUID,
		CAKE_LDAP_LDAP_ATTRIBUTE_DISTINGUISHED_NAME,
		CAKE_LDAP_LDAP_ATTRIBUTE_NAME,
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
		parent::__construct($id, $table, $ds);

		$modelConfigSync = ClassRegistry::init('CakeLdap.ConfigSync');
		$searchBase = $modelConfigSync->getSearchBase();
		if (empty($searchBase)) {
			$ds = $this->getDataSource();
			if (isset($ds->config['basedn'])) {
				$searchBase = $ds->config['basedn'];
			}
		}
		$this->useTable = $searchBase;
	}

/**
 * Called before each find operation. Return false if you want to halt the find
 * call, otherwise return the (modified) query data.
 *
 * Actions:
 *  - Store fields list;
 *  - Build LDAP query string by array condition.
 *
 * @param array $query Data used to execute this query, i.e. conditions, order, etc.
 * @return mixed true if the operation should continue, false if it should abort; or, modified
 *  $query to continue with new $query
 * @link http://book.cakephp.org/2.0/en/models/callback-methods.html#beforefind
 */
	public function beforeFind($query) {
		if (isset($query['fields'])) {
			$this->_setFindFields($query['fields']);
		}

		if (!isset($query['conditions'])) {
			return false;
		}

		if (!is_array($query['conditions'])) {
			return parent::beforeFind($query);
		}

		$query['conditions'] = $this->getLdapQuery($query['conditions']);
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
 *  - Add empty fields in result;
 *  - Convert GUID value to string.
 *
 * @param mixed $results The results of the find operation
 * @param bool $primary Whether this model is being queried directly (vs. being queried as an association)
 * @return mixed Result of the find operation
 * @link http://book.cakephp.org/2.0/en/models/callback-methods.html#afterfind
 */
	public function afterFind($results, $primary = false) {
		$resultParent = parent::afterFind($results, $primary);
		if (!$resultParent || empty($results)) {
			return $resultParent;
		}

		if ($resultParent !== true) {
			$results = $resultParent;
		}

		if (isset($results[0][0])) {
			unset($results[0][0]);
		}

		$fields = $this->_getFindFields();
		$emptyFields = [];
		if (!empty($fields) && is_array($fields)) {
			$emptyFields = array_fill_keys($fields, '');
		}

		foreach ($results as &$result) {
			if (!isset($result[$this->alias])) {
				continue;
			}

			foreach ($result[$this->alias] as $field => &$value) {
				switch ($field) {
					case CAKE_LDAP_LDAP_ATTRIBUTE_OBJECT_GUID:
						$value = GuidToString($value);
						break;
					case CAKE_LDAP_LDAP_ATTRIBUTE_OTHER_TELEPHONE_NUMBER:
					case CAKE_LDAP_LDAP_ATTRIBUTE_OTHER_MOBILE_TELEPHONE_NUMBER:
						if (empty($value) || is_array($value)) {
							continue 2;
						}
						$value = [$value];
						break;
				}
			}
			unset($value);

			if (!empty($emptyFields)) {
				$result[$this->alias] += $emptyFields;
			}
		}

		return $results;
	}

/**
 * Called before every deletion operation.
 *
 * Actions:
 * - Disabling deleting data.
 *
 * @param bool $cascade If true records that depend on this record will also be deleted
 * @return bool True if the operation should continue, false if it should abort
 * @link http://book.cakephp.org/2.0/en/models/callback-methods.html#beforedelete
 */
	public function beforeDelete($cascade = true) {
		return false;
	}

/**
 * Set fields for Model::afterFind callback
 *
 * @param string $fields Fields name for find method
 * @return void
 */
	protected function _setFindFields($fields = null) {
		$this->_findFields = $fields;
	}

/**
 * Return fields name for Model::afterFind callback
 *
 * @return mixed Fields name for Model::afterFind callback
 */
	protected function _getFindFields() {
		return $this->_findFields;
	}

/**
 * Return list of required fields
 *
 * @return array Return list of required fields.
 */
	public function getRequiredFields() {
		return (array)$this->_requiredFields;
	}

/**
 * Return LDAP query string builded by array condition
 *
 * @param array $conditions Array conditions for build string
 * @param string $objectClass name of object class for condition
 * @return string Return LDAP query string
 */
	public function getLdapQuery($conditions = null, $objectClass = null) {
		if (!empty($objectClass)) {
			$objectClass = mb_strtolower($objectClass);
		} else {
			$objectClass = 'user';
		}

		$baseQuery = '(!(useraccountcontrol:1.2.840.113556.1.4.803:=2))(objectClass=' . $objectClass . ')';
		if ($objectClass === 'user') {
			$baseQuery .= '(userAccountControl:1.2.840.113556.1.4.803:=512)';
		}

		if (empty($conditions) || !is_array($conditions)) {
			return $baseQuery;
		}

		$result = '(&' . $baseQuery;
		foreach ($conditions as $attribute => $value) {
			list(, $attributeName) = pluginSplit($attribute);
			if (empty($value)) {
				continue;
			}

			switch ($attributeName) {
				case CAKE_LDAP_LDAP_DISTINGUISHED_NAME:
					$attributeName = CAKE_LDAP_LDAP_ATTRIBUTE_DISTINGUISHED_NAME;
					break;
				case CAKE_LDAP_LDAP_ATTRIBUTE_OBJECT_GUID:
					$value = GuidStringToLdap($value);
					break;
			}
			$result .= '(' . $attributeName . '=' . (string)$value . ')';
		}
		$result .= ')';

		return $result;
	}

/**
 * Return array information of all employees
 *
 * @param string|null $company Company name for select employees
 * @param string|null $guid GUID of employee
 * @param string $order Order field
 * @param int|string $limit Limit for result
 * @return array|null Return array of informationa about a employees,
 *  or Null if no result.
 */
	public function getAllEmployees($company = null, $guid = null, $order = CAKE_LDAP_LDAP_ATTRIBUTE_NAME, $limit = CAKE_LDAP_SYNC_AD_LIMIT) {
		$modelConfigSync = ClassRegistry::init('CakeLdap.ConfigSync');
		$fields = $modelConfigSync->getListFieldsLdap();
		if (empty($fields)) {
			return false;
		}

		$conditions = [];
		if (!empty($company)) {
			$conditions[CAKE_LDAP_LDAP_ATTRIBUTE_COMPANY] = $company;
		}
		if (!empty($guid)) {
			$conditions[CAKE_LDAP_LDAP_ATTRIBUTE_OBJECT_GUID] = $guid;
		}
		$result = $this->find('all', compact('conditions', 'fields', 'order', 'limit'));
		if (empty($result)) {
			return $result;
		}

		return $result;
	}

/**
 * Return array information of all departments
 *
 * @param string|null $company Company name for select department of employees
 * @param int|string $limit Limit for result
 * @return array|null Return list of departments,
 *  or Null if no result.
 */
	public function getAllDepartmentsList($company = null, $limit = CAKE_LDAP_SYNC_AD_LIMIT) {
		$order = CAKE_LDAP_LDAP_ATTRIBUTE_DEPARTMENT;
		$fields = [CAKE_LDAP_LDAP_ATTRIBUTE_DEPARTMENT];
		$conditions = [
			CAKE_LDAP_LDAP_ATTRIBUTE_DEPARTMENT => '*',
		];
		if (!empty($company)) {
			$conditions[CAKE_LDAP_LDAP_ATTRIBUTE_COMPANY] = $company;
		}
		$data = $this->find('all', compact('conditions', 'fields', 'order', 'limit'));
		if (empty($data)) {
			return $data;
		}

		$dataList = Hash::extract($data, '{n}.' . $this->alias . '.' . CAKE_LDAP_LDAP_ATTRIBUTE_DEPARTMENT);
		$result = array_values(array_unique($dataList));

		return $result;
	}
}
