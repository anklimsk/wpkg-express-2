<?php
/**
 * Plugin model for CakePHP.
 *
 * CakeLdap: Authentication of users by member group of Active Directory.
 * @copyright Copyright 2016-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.Model
 */

App::uses('AppModel', 'Model');

/**
 * Plugin model for CakePHP.
 *
 * @package plugin.Model
 */
class CakeLdapAppModel extends AppModel {

/**
 * Name of the validation string domain to use when translating validation errors.
 *
 * @var array
 */
	public $validationDomain = 'cake_ldap_validation_errors';

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
 * Actions:
 * - Set global limit if needed.
 *
 * @param array $query Data used to execute this query, i.e. conditions, order, etc.
 * @return mixed true if the operation should continue, false if it should abort; or, modified
 *  $query to continue with new $query
 * @link http://book.cakephp.org/2.0/en/models/callback-methods.html#beforefind
 */
	public function beforeFind($query) {
		if (isset($query['limit'])) {
			return parent::beforeFind($query);
		}

		$modelConfigSync = ClassRegistry::init('CakeLdap.ConfigSync');
		$query['limit'] = $modelConfigSync->getSyncLimit();
		$parentQuery = parent::beforeFind($query);
		if (!$parentQuery) {
			return false;
		} elseif ($parentQuery !== true) {
			$query = $parentQuery;
		}

		return $query;
	}
}
