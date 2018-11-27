<?php
/**
 * Mock models file
 */

App::uses('EmployeeDb', 'CakeLdap.Model');
App::uses('DepartmentDb', 'CakeLdap.Model');

if (!class_exists('CakeLdapAppModel')) {

/**
 * Plugin model for CakePHP.
 *
 * @package plugin.Model
 */
	class CakeLdapAppModel extends CakeTestModel {

	/**
	 * Flag of LDAP model.
	 *
	 * @var bool
	 */
		protected $_isLdapModel = false;

	/**
	 * Constructor. Binds the model's database table to the object.
	 *
	 * Actions:
	 * - Set useTable to `employee` if data source is `test`, and table name
	 *  is empty string.
	 *
	 * @param bool|int|string|array $id Set this ID for this model on startup,
	 * can also be an array of options, see above.
	 * @param string $table Name of database table to use.
	 * @param string $ds DataSource connection name.
	 */
		public function __construct($id = false, $table = null, $ds = null) {
			parent::__construct($id, $table, $ds);
			if ($this->useDbConfig === 'test') {
				if ($this->name === 'User') {
					$this->useTable = 'employee_ldap_auth';
					$this->primaryKey = 'id';
					$this->_isLdapModel = true;
				} elseif ($this->primaryKey === CAKE_LDAP_LDAP_DISTINGUISHED_NAME) {
					$this->useTable = 'employee_ldap';
					$this->primaryKey = 'id';
					$this->_isLdapModel = true;
				}
			}

			$this->actsAs['CakeConfigPlugin.InitConfig'] = [
				'pluginName' => 'CakeLdap',
				'checkPath' => 'CakeLdap.LdapSync.LdapFields',
			];
		}

	/**
	 * List of behaviors to load when the model object is initialized. Settings can be
	 * passed to behaviors by using the behavior name as index. Eg:
	 *
	 * @var array
	 * @link http://book.cakephp.org/2.0/en/models/behaviors.html#using-behaviors
	 */
		public $actsAs = [
			'CakeConfigPlugin.InitConfig' => [
				'pluginName' => 'CakeLdap',
				'checkPath' => 'CakeLdap.LdapSync.LdapFields',
			]
		];

	/**
	 * Called before each find operation. Return false if you want to halt the find
	 * call, otherwise return the (modified) query data.
	 *
	 * @param array $query Data used to execute this query, i.e. conditions, order, etc.
	 * @return mixed true if the operation should continue, false if it should abort; or, modified
	 *  $query to continue with new $query
	 * @link http://book.cakephp.org/2.0/en/models/callback-methods.html#beforefind
	 */
		public function beforeFind($query) {
			if (!$this->_isLdapModel || !is_string($query['conditions'])) {
				return parent::beforeFind($query);
			}

			$pattern = '\(userAccountControl\:1\.2\.840\.113556\.1\.4\.803\:\=512\)|\(\!\(useraccountcontrol\:1\.2\.840\.113556\.1\.4\.803\:\=2\)\)|\(objectClass\=user\)';
			$query['conditions'] = mb_ereg_replace($pattern, '', $query['conditions']);

			$pattern = '^\([\&\|\!]|\)$';
			$query['conditions'] = mb_ereg_replace($pattern, '', $query['conditions']);

			$pattern = '/\(([^)]+)\)/';
			$matches = [];
			$conditions = [];
			if (preg_match_all($pattern, $query['conditions'], $matches) > 0) {
				foreach ($matches[1] as $queryItem) {
					$queryItemParams = explode('=', $queryItem, 2);
					if ((count($queryItemParams) < 2) || !$this->hasField($queryItemParams[0]) ||
						empty($queryItemParams[1]) || ($queryItemParams[1] === '*')) {
						continue;
					}
					if ($queryItemParams[0] === CAKE_LDAP_LDAP_ATTRIBUTE_OBJECT_GUID) {
						$queryItemParams[1] = hex2bin(str_replace('\\', '', $queryItemParams[1]));
					}
					$conditions[$this->alias . '.' . $queryItemParams[0]] = $queryItemParams[1];
				}
			}
			if (empty($conditions)) {
				return false;
			}

			$query['conditions'] = $conditions;
			$parentQuery = parent::beforeFind($query);
			if (!$parentQuery) {
				return false;
			} elseif ($parentQuery !== true) {
				$query = $parentQuery;
			}
			if (is_array($query['fields'])) {
				$query['fields'][] = CAKE_LDAP_LDAP_DISTINGUISHED_NAME;
			}

			return $query;
		}

	/**
	 * Called after each find operation. Can be used to modify any results returned by find().
	 * Return value should be the (modified) results.
	 *
	 * Actions:
	 * - Unserialize multiple value data for LDAP fixture
	 *
	 * @param mixed $results The results of the find operation
	 * @param bool $primary Whether this model is being queried directly (vs. being queried as an association)
	 * @return mixed Result of the find operation
	 * @link http://book.cakephp.org/2.0/en/models/callback-methods.html#afterfind
	 */
		public function afterFind($results, $primary = false) {
			if (empty($results) || !in_array($this->table, ['', 'employee_ldap'])) {
				return parent::afterFind($results, $primary);
			}

			foreach ($results as &$result) {
				if (!isset($result[$this->alias])) {
					continue;
				}

				foreach ($result[$this->alias] as $field => &$value) {
					switch ($field) {
						case CAKE_LDAP_LDAP_ATTRIBUTE_OTHER_TELEPHONE_NUMBER:
						case CAKE_LDAP_LDAP_ATTRIBUTE_OTHER_MOBILE_TELEPHONE_NUMBER:
								$value = unserialize($value);
							break;
					}
				}
			}

			return parent::afterFind($results, $primary);
		}

	}
}

if (!class_exists('SyncBehaviorModel')) {

/**
 * The model is used for management departments
 *  (model extended in application).
 *
 * @package app.Model
 */
	class SyncBehaviorModel extends DepartmentDb {

	/**
	 * List of behaviors to load when the model object is initialized. Settings can be
	 * passed to behaviors by using the behavior name as index. Eg:
	 *
	 * @var array
	 * @link http://book.cakephp.org/2.0/en/models/behaviors.html#using-behaviors
	 */
		public $actsAs = [
			'CakeLdap.Sync'
		];
	}
}

if (!class_exists('BindValidationBehaviorModel')) {

/**
 * The model is used for management departments
 *  (model extended in application).
 *
 * @package app.Model
 */
	class BindValidationBehaviorModel extends DepartmentDb {

	/**
	 * List of behaviors to load when the model object is initialized. Settings can be
	 * passed to behaviors by using the behavior name as index. Eg:
	 *
	 * @var array
	 * @link http://book.cakephp.org/2.0/en/models/behaviors.html#using-behaviors
	 */
		public $actsAs = [
			'CakeLdap.BindValidation' => [
				'ldapField' => CAKE_LDAP_LDAP_ATTRIBUTE_DEPARTMENT
			]
		];
	}
}

/**
 * UserTest class
 *
 * @package       Cake.Test.Case.Model
 */
class UserTest extends CakeTestModel {

/**
 * Name of the model.
 *
 * @var string
 */
	public $name = 'UserTest';
}
