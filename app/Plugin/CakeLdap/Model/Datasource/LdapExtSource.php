<?php
/**
 * This file is the LDAP Datasource of the plugin.
 * Connect to LDAPv3 style datasource with full CRUD support
 *  Disable delete action.
 *
 * CakeLdap: Authentication of users by member group of Active Directory.
 * @copyright Copyright 2017-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.Model.Datasource
 * @link https://github.com/cakephp/datasources
 */

App::uses('LdapSource', 'Datasources.Model/Datasource');

/**
 * Ldap Datasource
 *
 * @package datasources
 * @subpackage datasources.models.datasources
 */
class LdapExtSource extends LdapSource {

/**
 * The "D" in CRUD
 *
 * @param Model $model The model class having record(s) deleted
 * @param mixed $conditions *unused*
 * @return bool Success
 */
	public function delete(Model $model, $conditions = null) {
		return false;
	}
}
