<?php
/**
 * This file is the model file of the plugin.
 * Model use for connection to LDAP datasource.
 *
 * CakeInstaller: Installer of CakePHP web application.
 * @copyright Copyright 2016-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.Model
 */

App::uses('CakeSettingsAppAppModel', 'CakeSettingsApp.Model');

/**
 * InstallerCheck for installer.
 *
 * @package plugin.Model
 */
class Ldap extends CakeSettingsAppAppModel {

/**
 * Name of the model.
 *
 * @var string
 * @link http://book.cakephp.org/2.0/en/models/model-attributes.html#name
 */
	public $name = 'Ldap';

/**
 * The name of the DataSource connection that this Model uses
 *
 * @var string
 * @link http://book.cakephp.org/2.0/en/models/model-attributes.html#usedbconfig
 */
	public $useDbConfig = 'ldap';

/**
 * The name of the primary key field for this model.
 *
 * @var string
 * @link http://book.cakephp.org/2.0/en/models/model-attributes.html#primarykey
 */
	public $primaryKey = CAKE_SETTINGS_APP_LDAP_DISTINGUISHED_NAME;

/**
 * Custom database table name, or null/false if no table association is desired.
 *
 * @var string
 * @link http://book.cakephp.org/2.0/en/models/model-attributes.html#usetable
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
		$ds = $this->getDataSource();
		if (isset($ds->config['basedn'])) {
			$this->useTable = $ds->config['basedn'];
		}
	}

/**
 * Return list of e-mail for users, that are members of a
 *  security group in Active Directory.
 *
 * @param string $groupDn Security group distinguished name.
 * @return array List of email addresses for the users who are
 *  members of a security group in Active Directory.
 */
	public function getListGroupEmail($groupDn = null) {
		$result = [];
		if (empty($groupDn)) {
			return $result;
		}

		$cacheKey = md5($groupDn);
		$cached = Cache::read($cacheKey, CAKE_SETTINGS_APP_CACHE_KEY_AD_GROUP_MEMBER_MAIL);
		if (!empty($cached)) {
			return $cached;
		}

		$conditions = '(&(userAccountControl:1.2.840.113556.1.4.803:=512)(!(useraccountcontrol:1.2.840.113556.1.4.803:=2))(objectClass=user)(' . CAKE_SETTINGS_APP_LDAP_ATTRIBUTE_MEMBER_OF . '=' . $groupDn . '))';
		$fields = [CAKE_SETTINGS_APP_LDAP_ATTRIBUTE_NAME, CAKE_SETTINGS_APP_LDAP_ATTRIBUTE_MAIL];
		$order = CAKE_SETTINGS_APP_LDAP_ATTRIBUTE_MAIL;

		$data = $this->find('all', compact('conditions', 'fields', 'order'));
		if (empty($data)) {
			return $result;
		}

		foreach ($data as $dataItem) {
			$mail = Hash::get($dataItem, $this->alias . '.' . CAKE_SETTINGS_APP_LDAP_ATTRIBUTE_MAIL);
			$name = Hash::get($dataItem, $this->alias . '.' . CAKE_SETTINGS_APP_LDAP_ATTRIBUTE_NAME);
			if (empty($mail)) {
				continue;
			}
			$result[$mail] = $name;
		}
		Cache::write($cacheKey, $result, CAKE_SETTINGS_APP_CACHE_KEY_AD_GROUP_MEMBER_MAIL);

		return $result;
	}

/**
 * Check search base for user by GUID.
 *
 * @param string $userGuid GUID of user.
 * @param string $searchBase Search base.
 * @return bool Success
 */
	public function checkSearchBase($userGuid = null, $searchBase = null) {
		if (empty($searchBase)) {
			return false;
		}

		if (empty($userGuid)) {
			return $this->groupExists($searchBase);
		}

		$prevUseTable = $this->useTable;
		$this->useTable = $searchBase;
		$conditions = [CAKE_SETTINGS_APP_LDAP_ATTRIBUTE_OBJECT_GUID => GuidStringToLdap($userGuid)];
		$fields = [CAKE_SETTINGS_APP_LDAP_ATTRIBUTE_DISTINGUISHED_NAME];
		$group = $this->find('first', compact('conditions', 'fields'));
		$this->useTable = $prevUseTable;
		if (empty($group)) {
			return false;
		}

		return true;
	}

/**
 * Check exists security group in Active Directory.
 *
 * @param string $groupDn Security group distinguished name.
 * @param string $userGuid GUID of user for check member of this group.
 * @return bool Success
 */
	public function groupExists($groupDn = null, $userGuid = false) {
		if (empty($groupDn)) {
			return false;
		}

		$conditions = [CAKE_SETTINGS_APP_LDAP_ATTRIBUTE_DISTINGUISHED_NAME => $groupDn];
		$fields = [CAKE_SETTINGS_APP_LDAP_ATTRIBUTE_DISTINGUISHED_NAME];
		$group = $this->find('first', compact('conditions', 'fields'));
		if (empty($group)) {
			return false;
		}
		if (empty($userGuid)) {
			return true;
		}

		$conditions = [CAKE_SETTINGS_APP_LDAP_ATTRIBUTE_OBJECT_GUID => GuidStringToLdap($userGuid)];
		$fields = [CAKE_SETTINGS_APP_LDAP_ATTRIBUTE_MEMBER_OF];
		$userInfo = $this->find('first', compact('conditions', 'fields'));
		if (empty($userInfo) ||
			!isset($userInfo[$this->alias][CAKE_SETTINGS_APP_LDAP_ATTRIBUTE_MEMBER_OF]) ||
			empty($userInfo[$this->alias][CAKE_SETTINGS_APP_LDAP_ATTRIBUTE_MEMBER_OF])) {
			return false;
		}
		$groupsDn = (array)$userInfo[$this->alias][CAKE_SETTINGS_APP_LDAP_ATTRIBUTE_MEMBER_OF];
		if (!in_array($groupDn, $groupsDn)) {
			return false;
		}

		return true;
	}

/**
 * Return list of top level containers from AD
 *
 * @return array List of top level containers.
 */
	public function getTopLevelContainerList() {
		return Cache::remember('all_ad_top_lev_cont', [$this, 'rememberTopLevelContainers'], CAKE_SETTINGS_APP_CACHE_KEY_AD_GROUPS);
	}

/**
 * Return list of top level containers from AD
 *
 * @return array List of top level containers.
 */
	public function rememberTopLevelContainers() {
		$result = [];
		$conditions = '(|(objectClass=organizationalUnit)(objectClass=container))';
		$fields = [CAKE_SETTINGS_APP_LDAP_ATTRIBUTE_DISTINGUISHED_NAME];
		$order = CAKE_SETTINGS_APP_LDAP_ATTRIBUTE_DISTINGUISHED_NAME;
		$limit = CAKE_SETTINGS_APP_TOP_LEVEL_UNITS_LIST_LIMIT;
		$scope = 'one';

		$data = $this->find('all', compact('conditions', 'fields', 'order', 'limit', 'scope'));
		if (empty($data)) {
			return $result;
		}

		$result = Hash::extract(
			$data,
			'{n}.' . $this->alias . '.' . CAKE_SETTINGS_APP_LDAP_ATTRIBUTE_DISTINGUISHED_NAME
		);

		return $result;
	}

/**
 * Return list of groups from AD in format:
 *  - `key` distinguished name;
 *  - `value` group name.
 *
 * @return array List of groups.
 */
	public function getGroupList() {
		return Cache::remember('all_ad_groups', [$this, 'rememberGroups'], CAKE_SETTINGS_APP_CACHE_KEY_AD_GROUPS);
	}

/**
 * Return list of groups from AD in format:
 *  - `key` distinguished name;
 *  - `value` group name.
 *
 * @return array List of groups.
 */
	public function rememberGroups() {
		$result = [];
		$conditions = '(&(objectCategory=group)(groupType:1.2.840.113556.1.4.803:=2))';
		$fields = [CAKE_SETTINGS_APP_LDAP_ATTRIBUTE_NAME, CAKE_SETTINGS_APP_LDAP_ATTRIBUTE_DISTINGUISHED_NAME];
		$order = CAKE_SETTINGS_APP_LDAP_ATTRIBUTE_NAME;

		$data = $this->find('all', compact('conditions', 'fields', 'order'));
		if (empty($data)) {
			return $result;
		}

		$result = Hash::combine(
			$data,
			'{n}.' . $this->alias . '.' . CAKE_SETTINGS_APP_LDAP_ATTRIBUTE_DISTINGUISHED_NAME,
			'{n}.' . $this->alias . '.' . CAKE_SETTINGS_APP_LDAP_ATTRIBUTE_NAME
		);

		return $result;
	}
}
