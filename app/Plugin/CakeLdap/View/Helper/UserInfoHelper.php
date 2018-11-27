<?php
/**
 * This file is the helper file of the plugin.
 * Information about authenticated user Helper.
 * Methods for working with information of the authenticated user
 *
 * CakeLdap: Authentication of users by member group of Active Directory.
 * @copyright Copyright 2016-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.View.Helper
 */

App::uses('CakeLdapAppHelper', 'CakeLdap.View/Helper');
App::uses('UserInfo', 'CakeLdap.Utility');

/**
 * Methods for working with information of the authenticated user.
 *
 * @package plugin.View.Helper
 */
class UserInfoHelper extends CakeLdapAppHelper {

/**
 * Object of utility `UserInfo`
 *
 * @var object
 */
	protected $_objUserInfo = null;

/**
 * Default Constructor
 *
 * @param View $View The View this helper is being attached to.
 * @param array $settings Configuration settings for the helper.
 */
	public function __construct(View $View, $settings = []) {
		parent::__construct($View, $settings);
		$this->_objUserInfo = new UserInfo();
	}

/**
 * Return value of field from user auth information
 *
 * @param string $field Field to retrieve
 * @return mixed|null User info record. Or null if no user is logged in.
 */
	public function getUserField($field = null) {
		return $this->_objUserInfo->getUserField($field);
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
		return $this->_objUserInfo->checkUserRole($roles, $logicalOr, $userInfo);
	}
}
