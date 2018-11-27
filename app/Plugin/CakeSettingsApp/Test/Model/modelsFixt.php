<?php
/**
 * Mock models file
 */

App::uses('SettingBase', 'CakeSettingsApp.Model');

if (!class_exists('CakeSettingsAppAppModel')) {

/**
 * Plugin model for CakePHP.
 *
 * @package plugin.Model
 */
	class CakeSettingsAppAppModel extends CakeTestModel {

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
			if ($this->primaryKey === CAKE_SETTINGS_APP_LDAP_DISTINGUISHED_NAME) {
				$this->useTable = 'ldap';
				$this->primaryKey = 'id';
				$this->_isLdapModel = true;
			}
		}

	/**
	 * List of behaviors to load when the model object is initialized. Settings can be
	 * passed to behaviors by using the behavior name as index. Eg:
	 *
	 * public $actsAs = array('Translate', 'MyBehavior' => array('setting1' => 'value1'))
	 *
	 * @var array
	 * @link http://book.cakephp.org/2.0/en/models/behaviors.html#using-behaviors
	 */
		public $actsAs = [
			'CakeConfigPlugin.InitConfig' => [
				'pluginName' => 'CakeSettingsApp'
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
				$query['fields'][] = CAKE_SETTINGS_APP_LDAP_DISTINGUISHED_NAME;
			}

			return $query;
		}

	}
}
