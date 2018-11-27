<?php
/**
 * This file is the model file of the plugin.
 * The model is used for validation login information.
 *
 * CakeLdap: Authentication of users by member group of Active Directory.
 * @copyright Copyright 2016-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.Model
 */

App::uses('CakeLdapAppModel', 'CakeLdap.Model');

/**
 * The model is used for validation login information.
 *
 * @package plugin.Model
 */
class User extends CakeLdapAppModel {

/**
 * Name of the model.
 *
 * @var string
 */
	public $name = 'User';

/**
 * The name of the DataSource connection that this Model uses
 *
 * @var string
 */
	public $useDbConfig = 'ldap';

/**
 * The name of the primary key field for this model.
 *
 * @var string
 */
	public $primaryKey = CAKE_LDAP_LDAP_DISTINGUISHED_NAME;

/**
 * Custom database table name, or null/false if no table association is desired.
 *
 * @var string
 */
	public $useTable = '';

/**
 * Fields name for find action.
 *
 * @var string|array|null
 */
	protected $_findFields = null;

/**
 * List of validation rules.
 *
 * @var array
 */
	public $validate = [
		'username' => [
			'rule' => 'notBlank',
			'required' => true,
			'allowEmpty' => false,
			'last' => true,
			'message' => 'You must enter username.'
		],
		'password' => [
			'rule' => 'notBlank',
			'required' => true,
			'allowEmpty' => false,
			'last' => true,
			'message' => 'You must enter password.'
		]
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
 * - Store fields list.
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

		return parent::beforeFind($query);
	}

/**
 * Called after each find operation. Can be used to modify any results returned by find().
 * Return value should be the (modified) results.
 *
 * Actions:
 * - Changing the value of the result depending on the data source
 *
 * @param mixed $results The results of the find operation
 * @param bool $primary Whether this model is being queried directly (vs. being queried as an association)
 * @return mixed Result of the find operation
 * @link http://book.cakephp.org/2.0/en/models/callback-methods.html#afterfind
 */
	public function afterFind($results, $primary = false) {
		if (empty($results)) {
			return $results;
		}

		if (isset($results[0][0])) {
			unset($results[0][0]);
		}

		$fields = $this->_getFindFields();
		$emptyFields = [];
		if (!empty($fields)) {
			$emptyFields = array_fill_keys($fields, '');
		}

		foreach ($results as &$result) {
			if (!isset($result[$this->alias])) {
				continue;
			}

			if (isset($result[$this->alias][CAKE_LDAP_LDAP_ATTRIBUTE_OBJECT_GUID]) &&
				isBinary($result[$this->alias][CAKE_LDAP_LDAP_ATTRIBUTE_OBJECT_GUID])) {
				$result[$this->alias][CAKE_LDAP_LDAP_ATTRIBUTE_OBJECT_GUID] = GuidToString($result[$this->alias][CAKE_LDAP_LDAP_ATTRIBUTE_OBJECT_GUID]);
			}

			if (!empty($emptyFields)) {
				$result[$this->alias] += $emptyFields;
			}
		}

		return $results;
	}

/**
 * Set fields for Model::afterFind callback
 *
 * @param string $fields Fields name for find action
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
}
