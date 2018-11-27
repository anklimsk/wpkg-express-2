<?php
/**
 * This file is the util file of the plugin.
 * UserInfo Utility.
 * Methods for working with information of the authenticated user
 *
 * CakeLdap: Authentication of users by member group of Active Directory.
 * @copyright Copyright 2016-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.Lib.Utility
 */

App::uses('CakeSession', 'Model/Datasource');
App::uses('Hash', 'Utility');

/**
 * User information helper library.
 * Methods for working with information of the authenticated user
 *
 * @package plugin.Lib.Utility
 */
class UserInfo {

/**
 * Stores information of the authenticated user
 *
 * @var array
 */
	protected $_userInfo = null;

/**
 * Constructor.
 *
 * Return void
 */
	public function __construct() {
		$this->_userInfo = CakeSession::read('Auth.User');
	}

/**
 * Return value of field from user auth information
 *
 * @param string $field Field to retrieve
 * @param array $userInfo Array of information about authenticated user
 * @return mixed|null User info record. Or null if no user is logged in.
 */
	public function getUserField($field = null, $userInfo = null) {
		if (empty($field)) {
			return null;
		}

		if (!empty($userInfo) && is_array($userInfo)) {
			$targetUserInfo = $userInfo;
		} else {
			$targetUserInfo = $this->_userInfo;
		}

		if (empty($targetUserInfo) || !is_array($targetUserInfo)) {
			return null;
		}

		$result = Hash::get($targetUserInfo, $field);

		return $result;
	}

/**
 * Checking user for compliance with roles
 *
 * @param int $roles Bit mask of user role for checking or array
 *  of bit masks.
 * @param bool $logicalOr If True, used logical OR for checking several bit masks.
 *  Used logical AND otherwise.
 * @param array $userInfo Array of information about authenticated user
 * @return bool True if success.
 */
	public function checkUserRole($roles = null, $logicalOr = true, $userInfo = null) {
		if (empty($roles) || (empty($this->_userInfo) &&
			(empty($userInfo) || !is_array($userInfo)))) {
			return false;
		}

		if (!empty($userInfo) && is_array($userInfo)) {
			$targetUserInfo = $userInfo;
		} else {
			$targetUserInfo = $this->_userInfo;
		}
		$userRole = (int)Hash::get($targetUserInfo, 'role');
		if ($userRole === 0) {
			return false;
		}

		if (!is_array($roles)) {
			$roles = [$roles];
		}

		if ($logicalOr) {
			$result = false;
		} else {
			$result = true;
		}

		foreach ($roles as $role) {
			$role = (int)$role;
			$resultItem = ((($userRole & $role) === $role) ? true : false);
			if ($logicalOr) {
				$result |= $resultItem;
			} else {
				$result &= $resultItem;
			}
		}

		return (bool)$result;
	}
}
