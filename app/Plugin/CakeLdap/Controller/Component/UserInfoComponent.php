<?php
/**
 * This file is the componet file of the plugin.
 * Methods for working with information of the authenticated user.
 *
 * CakeLdap: Authentication of users by member group of Active Directory.
 * @copyright Copyright 2016-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.Controller.Component
 */

App::uses('Component', 'Controller');
App::uses('Router', 'Routing');
App::uses('UserInfo', 'CakeLdap.Utility');

/**
 * UserInfo Component.
 *
 * Methods for working with information of the authenticated user
 * @package plugin.Controller.Component
 */
class UserInfoComponent extends Component {

/**
 * Object of utility `UserInfo`
 *
 * @var object
 */
	protected $_objUserInfo = null;

/**
 * List of user roles prefixes
 *
 * @var array
 */
	public $prefixes = null;

/**
 * Constructor
 *
 * @param ComponentCollection $collection A ComponentCollection this component can use to lazy load its components
 * @param array $settings Array of configuration settings.
 */
	public function __construct(ComponentCollection $collection, $settings = []) {
		$this->_objUserInfo = new UserInfo();
		if (isset($settings['prefixes']) && !empty($settings['prefixes'])) {
			$this->prefixes = (array)$settings['prefixes'];
		} else {
			$this->prefixes = (array)Router::prefixes();
		}
		parent::__construct($collection, $settings);
	}

/**
 * Called before the Controller::beforeFilter().
 *
 * Actions:
 *  - save controller object
 *
 * @param Controller $controller Controller with components to initialize
 * @return void
 * @link http://book.cakephp.org/2.0/en/controllers/components.html#Component::initialize
 */
	public function initialize(Controller $controller) {
		$this->_controller = $controller;
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

/**
 * Checking the requested controller action on the user's access by prefix role
 *
 * @param array $user Array of information about authenticated user
 * @return bool True if action is accessible. False otherwise.
 */
	public function isAuthorized($user = null) {
		$result = false;
		if (empty($this->prefixes) || !is_array($this->prefixes)) {
			return $result;
		}
		$rolePrefix = (string)$this->_objUserInfo->getUserField('prefix', $user);
		if (!empty($rolePrefix)) {
			if (!in_array($rolePrefix, $this->prefixes)) {
				return $result;
			}
			$prefixes = [$rolePrefix];
		} else {
			$prefixes = $this->prefixes;
		}

		$action = $this->_controller->request->param('action');
		foreach ($prefixes as $prefix) {
			if (mb_stripos($action, $prefix . '_') === 0) {
				$result |= true;
			} else {
				$result |= false;
			}
		}
		if (empty($rolePrefix)) {
			$result = !$result;
		}

		return (bool)$result;
	}

/**
 * Checking the request is use external authentication (e.g. Kerberos)
 *
 * @return bool True if external authentication is used. False otherwise.
 */
	public function isExternalAuth() {
		$remoteUser = env('REMOTE_USER');
		if (empty($remoteUser)) {
			$remoteUser = env('REDIRECT_REMOTE_USER');
		}

		$result = !empty($remoteUser);

		return $result;
	}
}
