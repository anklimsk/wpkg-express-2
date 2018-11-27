<?php
/**
 * This file is the authentication component file of the application uses LDAP.
 *
 * CakeLdap: Authentication of users by member group of Active Directory.
 * @copyright Copyright 2016-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.Controller.Component.Auth
 */

App::uses('BaseAuthenticate', 'Controller/Component/Auth');
App::uses('ClassRegistry', 'Utility');
App::uses('Hash', 'Utility');

/**
 * LDAP Authentication.
 *
 * @package plugin.Controller.Component.Auth
 */
class LdapAuthenticate extends BaseAuthenticate {

/**
 * Constructor
 *
 * Actions:
 *  - prepare setings.
 *
 * @param ComponentCollection $collection The Component collection used on this request.
 * @param array $settings Array of settings to use.
 */
	public function __construct(ComponentCollection $collection, $settings) {
		$this->settings = array_merge(
			$this->settings,
			[
				'userModel' => 'CakeLdap.User',
				'externalAuth' => false,
				'groups' => [],
				'prefixes' => [],
				'includeFields' => [],
				'bindFields' => [],
			]
		);

		parent::__construct($collection, $settings);
	}

/**
 * Return state of setting `externalAuth`
 *
 * @return bool Return True, if used external auth.
 *  False otherwise.
 */
	protected function _useExternalAuth() {
		return (bool)Hash::get($this->settings, 'externalAuth');
	}

/**
 * Authenticate a user based on the request information.
 *
 * @param CakeRequest $request Request to get authentication information from.
 * @param CakeResponse $response A response object that can have headers added.
 * @return mixed Either false on failure, or an array of user data on success.
 */
	public function authenticate(CakeRequest $request, CakeResponse $response) {
		$externalAuth = $this->_useExternalAuth();
		$userModel = $this->settings['userModel'];
		$userCredentials = $this->_getUserCredentials($request);
		extract($userCredentials);

		$auth = false;
		if ($externalAuth) {
			if (!empty($usr)) {
				$auth = true;
			}
		} else {
			$objectModel = ClassRegistry::init($userModel, true);
			if ($objectModel === false) {
				return false;
			}
			$dataSource = $objectModel->getDataSource();
			if ($dataSource === false) {
				return false;
			}
			/*
			if (Hash::get($dataSource->config, 'datasource') !== 'LdapExtSource')
				return false;
			*/
			$hosts = Hash::get($dataSource->config, 'host');
			if (empty($hosts)) {
				return false;
			}

			if (!is_array($hosts)) {
				$hosts = [$hosts];
			}

			$port = Hash::get($dataSource->config, 'port');
			if (empty($port)) {
				$port = '389';
			}

			foreach ($hosts as $host) {
				$connection = ldap_connect($host, $port);
				ldap_set_option($connection, LDAP_OPT_REFERRALS, 0);
				ldap_set_option($connection, LDAP_OPT_PROTOCOL_VERSION, 3);
				//@codingStandardsIgnoreStart
				if (@ldap_bind($connection, $usr, $pwd) !== false) {
				//@codingStandardsIgnoreEnd
					ldap_unbind($connection);
					$auth = true;
					break;
				}
			}
		}

		if (!$auth) {
			return false;
		}

		return $this->_findUser($usr);
	}

/**
 * Return user credentials
 *
 * @param CakeRequest $request Request to get authentication information from.
 * @return array  Return user credentials in format:
 *  - key: `usr`, value: username;
 *  - key: `pwd`, value: password.
 */
	protected function _getUserCredentials(CakeRequest $request) {
		$externalAuth = $this->_useExternalAuth();
		$usr = null;
		$pwd = null;

		if ($externalAuth) {
			$user = env('REMOTE_USER');
			if (empty($user)) {
				$user = env('REDIRECT_REMOTE_USER');
			}
			if (empty($user) || !is_string($user)) {
				return compact('usr', 'pwd');
			}

			$usr = $user;
		} else {
			$userModel = $this->settings['userModel'];
			$objectModel = ClassRegistry::init($userModel, true);
			if ($objectModel === false) {
				return compact('usr', 'pwd');
			}

			$fields = $this->settings['fields'];
			if (!$this->_checkFields($request, $objectModel->alias, $fields)) {
				return compact('usr', 'pwd');
			}

			$user = $request->data[$objectModel->alias][$fields['username']];
			if (empty($user) || !is_string($user)) {
				return compact('usr', 'pwd');
			}

			$pswd = $request->data[$objectModel->alias][$fields['password']];
			if (empty($pswd) || !is_string($pswd)) {
				return compact('usr', 'pwd');
			}

			$usr = $user;
			$pwd = $pswd;
		}

		return compact('usr', 'pwd');
	}

/**
 * function ldapEscape
 *
 * @param string $subject The subject string
 * @param bool $dn Treat subject as a DN if TRUE
 * @param string|array $ignore Set of characters to leave untouched
 * @return string The escaped string
 * @author Chris Wright
 * @version 2.0
 */
	public function ldapEscape($subject, $dn = false, $ignore = null) {
		// The base array of characters to escape
		// Flip to keys for easy use of unset()
		$search = array_flip($dn ? ['\\', ',', '=', '+', '<', '>', ';', '"', '#'] : ['\\', '*', '(', ')', "\x00"]);

		// Process characters to ignore
		if (is_array($ignore)) {
			$ignore = array_values($ignore);
		}
		for ($char = 0; isset($ignore[$char]); $char++) {
			unset($search[$ignore[$char]]);
		}

		// Flip $search back to values and build $replace array
		$search = array_keys($search);
		$replace = [];
		foreach ($search as $char) {
			$replace[] = sprintf('\\%02x', ord($char));
		}

		// Do the main replacement
		$result = str_replace($search, $replace, $subject);

		// Encode leading/trailing spaces in DN values
		if ($dn) {
			if ($result[0] == ' ') {
				$result = '\\20' . substr($result, 1);
			}
			if ($result[strlen($result) - 1] == ' ') {
				$result = substr($result, 0, -1) . '\\20';
			}
		}

		return $result;
	}

/**
 * Checks the fields to ensure they are supplied.
 *
 * @param CakeRequest $request The request that contains login information.
 * @param string $model The model used for login verification.
 * @param array $fields The fields to be checked.
 * @return bool False if the fields have not been supplied. True if they exist.
 */
	protected function _checkFields(CakeRequest $request, $model, $fields) {
		if (empty($request->data[$model])) {
			return false;
		}
		if (empty($request->data[$model][$fields['username']])) {
			return false;
		}

		return true;
	}

/**
 * Find a user record using the standard options.
 *
 * The $username parameter can be a (string)username or an array containing
 * conditions for Model::find('first'). If the $password param is not provided
 * the password field will be present in returned array.
 *
 * Input passwords will be hashed even when a user doesn't exist. This
 * helps mitigate timing attacks that are attempting to find valid usernames.
 *
 * @param string|array $username The username/identifier, or an array of find conditions.
 * @param string $password The password, only used if $username param is string.
 * @return bool|array Either false on failure, or an array of user data.
 */
	protected function _findUser($username, $password = null) {
		$userModel = $this->settings['userModel'];
		$groups = $this->settings['groups'];
		$prefixes = $this->settings['prefixes'];
		$includeFields = $this->settings['includeFields'];
		$bindFields = $this->settings['bindFields'];
		$objectModel = ClassRegistry::init($userModel, true);
		if ($objectModel === false) {
			return false;
		}

		if (!is_array($includeFields)) {
			$includeFields = [$includeFields];
		}
		if (!is_array($bindFields)) {
			$bindFields = [];
		}
		if (!is_array($groups)) {
			$groups = [$groups];
		}

		if (is_array($username)) {
			$conditions = $username;
		} else {
			$conditions = [CAKE_LDAP_LDAP_ATTRIBUTE_USER_PRINCIPAL_NAME => mb_strtolower($this->ldapEscape($username, false))];
		}

		$user = $username;
		$groupsDn = [];
		$includedFields = [];
		$fields = array_merge([CAKE_LDAP_LDAP_ATTRIBUTE_DISPLAY_NAME, CAKE_LDAP_LDAP_ATTRIBUTE_MEMBER_OF, 'name'], $includeFields, array_keys($bindFields));
		$fields = array_unique($fields);
		$userInfo = $objectModel->find('first', compact('conditions', 'fields'));
		if (empty($userInfo)) {
			$ds = $objectModel->getDataSource();
			if (!isset($ds->config['basedn'])) {
				return false;
			}
			if ($objectModel->useTable === $ds->config['basedn']) {
				return false;
			}
			$objectModel->useTable = $ds->config['basedn'];
			$userInfo = $objectModel->find('first', compact('conditions', 'fields'));
			if (empty($userInfo)) {
				return false;
			}
		}

		$displayname = Hash::get($userInfo, $objectModel->alias . '.' . CAKE_LDAP_LDAP_ATTRIBUTE_DISPLAY_NAME);
		$name = Hash::get($userInfo, $objectModel->alias . '.' . CAKE_LDAP_LDAP_ATTRIBUTE_NAME);
		if (!empty($displayname)) {
			$user = $displayname;
		} elseif (!empty($name)) {
			$user = $name;
		}
		$memberOf = Hash::extract($userInfo, $objectModel->alias . '.' . CAKE_LDAP_LDAP_ATTRIBUTE_MEMBER_OF);
		if (!empty($memberOf)) {
			if (!is_array($memberOf)) {
				$memberOf = [$memberOf];
			}
			$groupsDn = $memberOf;
		}

		foreach ($includeFields as $includeField) {
			$includedFields[$includeField] = Hash::get($userInfo, $objectModel->alias . '.' . $includeField);
		}

		$id = null;
		$modelCache = [];
		foreach ($bindFields as $LDAPbindField => $DBbindField) {
			$data = Hash::get($userInfo, $objectModel->alias . '.' . $LDAPbindField);
			if ($data === null) {
				continue;
			}

			if (strpos($DBbindField, '.') === false) {
				continue;
			}

			list($bindModel, $bindField) = pluginSplit($DBbindField);
			if (isset($modelCache[$bindModel])) {
				$objBindModel = $modelCache[$bindModel];
			} else {
				$objBindModel = ClassRegistry::init($bindModel, true);
				$modelCache[$bindModel] = $objBindModel;
			}
			if ($objBindModel === false) {
				continue;
			}

			$conditions = [$objBindModel->alias . '.' . $bindField => $data];
			$id = $objBindModel->field('id', $conditions);
		}

		$role = 0;
		$prefix = null;
		ksort($groups);
		foreach ($groups as $roleBit => $groupDn) {
			if (empty($groupDn)) {
				continue;
			}

			if (mb_stripos($groupDn, 'default') === 0) {
				$role |= (int)$roleBit;
			} elseif (!empty($groupsDn) && in_array($groupDn, $groupsDn)) {
				$role |= (int)$roleBit;
				if (!empty($prefixes) && is_array($prefixes) && isset($prefixes[$roleBit])) {
					$prefix = $prefixes[$roleBit];
				}
			}
		}

		$result = compact('user', 'role', 'prefix', 'id');
		if (!empty($includedFields)) {
			$result += compact('includedFields');
		}

		return $result;
	}
}
