<?php
/**
 * This file is the model file of the application. Used to
 *  manage information about computers from LDAP.
 *
 * This file is part of wpkgExpress II.
 *
 * wpkgExpress II is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * wpkgExpress II is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with wpkgExpress II. If not, see <https://www.gnu.org/licenses/>.
 *
 * wpkgExpress II: A web-based frontend to WPKG.
 *  Based on wpkgExpress by Brian White.
 * @copyright Copyright 2009, Brian White.
 * @copyright Copyright 2018, Andrey Klimov.
 * @package app.Model
 */

App::uses('AppModel', 'Model');
App::uses('ClassRegistry', 'Utility');
App::uses('Hash', 'Utility');

/**
 * The model is used to manage information about computers
 *  from LDAP.
 *
 * @package app.Model
 */
class LdapComputer extends AppModel {

/**
 * The name of the DataSource connection that this Model uses
 *
 * @var string
 * @link https://book.cakephp.org/2.0/en/models/model-attributes.html#usedbconfig
 */
	public $useDbConfig = 'ldap';

/**
 * The name of the primary key field for this model.
 *
 * @var string
 * @link https://book.cakephp.org/2.0/en/models/model-attributes.html#primarykey
 */
	public $primaryKey = CAKE_LDAP_LDAP_DISTINGUISHED_NAME;

/**
 * Custom database table name, or null/false if no table association is desired.
 *
 * @var string
 * @link https://book.cakephp.org/2.0/en/models/model-attributes.html#usetable
 */
	public $useTable = '';

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

		$modelSetting = ClassRegistry::init('Setting');
		$searchBase = $modelSetting->getConfig('SearchBaseComp');
		if (empty($searchBase)) {
			$ds = $this->getDataSource();
			if (isset($ds->config['basedn'])) {
				$searchBase = $ds->config['basedn'];
			}
		}
		$this->useTable = $searchBase;
	}

/**
 * Called before each save operation, after validation. Return a non-true result
 * to halt the save.
 *
 * Actions:
 *  - Disabling save information.
 *
 * @param array $options Options passed from Model::save().
 * @return bool True if the operation should continue, false if it should abort
 * @link https://book.cakephp.org/2.0/en/models/callback-methods.html#beforesave
 * @see Model::save()
 */
	public function beforeSave($options = []) {
		return false;
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
 * Return list information of all computers
 *
 * @param string|null $name Name for select computers
 * @param int|string $limit Limit for result
 * @return array Return array of informationa about a computers.
 */
	protected function _getListComputers($name = '', $limit = CAKE_LDAP_SYNC_AD_LIMIT) {
		$dataStr = serialize($limit);
		$cachePath = 'ListInfo.LdapComputer.' . md5($dataStr);
		$name = trim($name);
		$duration = '+5 minutes';
		if (empty($name)) {
			Cache::set('duration', $duration, CACHE_KEY_LISTS_INFO_LDAP_COMPUTER);
			$cached = Cache::read($cachePath, CACHE_KEY_LISTS_INFO_LDAP_COMPUTER);
			if (!empty($cached)) {
				return $cached;
			}
		}

		$result = [];
		$conditions = '(&(!(useraccountcontrol:1.2.840.113556.1.4.803:=2))(objectClass=computer)(name=' . $name . '*))';
		$order = [CAKE_LDAP_LDAP_ATTRIBUTE_NAME];
		$fields = [
			CAKE_LDAP_LDAP_ATTRIBUTE_NAME,
			CAKE_LDAP_LDAP_ATTRIBUTE_DISTINGUISHED_NAME
		];
		$data = $this->find('all', compact('conditions', 'fields', 'order', 'limit'));
		if (empty($data)) {
			return $result;
		}
		$result = Hash::combine(
			$data,
			'{n}.' . $this->alias . '.' . CAKE_LDAP_LDAP_ATTRIBUTE_DISTINGUISHED_NAME,
			'{n}.' . $this->alias . '.' . CAKE_LDAP_LDAP_ATTRIBUTE_NAME
		);
		if (empty($name)) {
			Cache::set('duration', $duration, CACHE_KEY_LISTS_INFO_LDAP_COMPUTER);
			Cache::write($cachePath, $result, CACHE_KEY_LISTS_INFO_LDAP_COMPUTER);
		}

		return $result;
	}

/**
 * Return list information of all computers from cache
 *
 * @param string|null $name Name for select computers
 * @param int|string $limit Limit for result
 * @return array Return array of informationa about a computers.
 */
	public function getListComputersFromCache($name = '', $limit = null) {
		$result = [];
		$name = trim($name);
		$listComputers = $this->_getListComputers();
		if (empty($listComputers)) {
			return $result;
		}
		$result = $listComputers;
		if (!empty($name)) {
			$result = array_filter(
				$listComputers,
				function ($val) use ($name) {
					return (mb_stripos($val, $name) !== false);
				}
			);
		}
		if (!empty($result) && !empty($limit)) {
			$result = array_slice($result, 0, $limit);
		}

		return $result;
	}

/**
 * Return list information of all computers
 *
 * @param string|null $name Name for select computers
 * @param int|string $limit Limit for result
 * @return array Return array of informationa about a computers.
 */
	public function getListComputers($name = '', $limit = CAKE_LDAP_SYNC_AD_LIMIT) {
		return $this->_getListComputers($name, $limit);
	}
}
