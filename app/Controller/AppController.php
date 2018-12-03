<?php
/**
 * This file is the application level Controller
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
 * @package app.Controller
 */

App::uses('Controller', 'Controller');
App::uses('Hash', 'Utility');
App::uses('Inflector', 'Utility');

/**
 * Application level Controller
 *
 * @package app.Controller
 */
class AppController extends Controller {

/**
 * An array containing the class names of models this controller uses.
 *
 * @var mixed
 * @link http://book.cakephp.org/2.0/en/controllers.html#components-helpers-and-uses
 */
	public $uses = [
		'Setting',
	];

/**
 * Array containing the names of components this controller uses. Component names
 * should not contain the "Component" portion of the class name.
 *
 * @var array
 * @link http://book.cakephp.org/2.0/en/controllers/components.html
 */
	public $components = [
		'Auth',
		'Session',
		'Security',
		'Flash',
		'RequestHandler',
		'CakeTheme.ViewExtension',
		'CakeLdap.UserInfo',
		'CakeSearchInfo.SearchFilter',
		'CakeInstaller.Installer' => [
			'ConfigKey' => PROJECT_CONFIG_NAME
		],
		'CakeSettingsApp.Settings',
		'CakeTheme.Theme'
	];

/**
 * An array containing the names of helpers this controller uses. The array elements should
 * not contain the "Helper" part of the class name.
 *
 * @var mixed
 * @link http://book.cakephp.org/2.0/en/controllers.html#components-helpers-and-uses
 */
	public $helpers = [
		'CakeTheme.ActionScript',
		'CakeTheme.ViewExtension',
		'CakeTheme.Filter',
		'CakeLdap.UserInfo',
		'CakeSearchInfo.Search',
		'AssetCompress.AssetCompress',
		'Session',
		'Html',
		'Form' => [
			'className' => 'CakeTheme.ExtBs3Form'
		],
		'Number',
		'Text'
	];

/**
 * Check if the provided user is authorized.
 *  Uses to check whether or not a user is authorized.
 *
 * @param array $user The user to check the authorization of.
 * @return bool True if $user is authorized, otherwise false
 */
	public function isAuthorized($user = []) {
		$plugin = $this->request->param('plugin');
		$controller = $this->request->param('controller');
		$action = $this->request->param('action');
		switch ($plugin) {
			case 'cake_search_info':
			case 'cake_settings_app':
			case 'cake_ldap':
				if ($controller === 'users') {
					return true;
				} elseif ($this->UserInfo->checkUserRole(USER_ROLE_ADMIN, true, $user)) {
					return true;
				}
				break;
			default:
				if ($this->UserInfo->isAuthorized($user) ||
					$this->UserInfo->checkUserRole(USER_ROLE_ADMIN, true, $user)) {
					return true;
				}
		}

		if (((in_array($controller, ['packages', 'profiles', 'hosts']) &&
			($action === 'index') && $this->RequestHandler->isXml()) ||
			(($controller === 'wpi') && ($action === 'config') &&
			$this->RequestHandler->prefers('js'))) &&
			$this->UserInfo->checkUserRole([USER_ROLE_EXPORT, USER_ROLE_ADMIN], true, $user)) {
			return true;
		}

		return false;
	}

/**
 * Called before the controller action.
 *
 * Actions:
 *  - Configure components;
 *  - Set global variables for View.
 *
 * @return void
 * @link http://book.cakephp.org/2.0/en/controllers.html#request-life-cycle-callbacks
 */
	public function beforeFilter() {
		$authGroups = [
			USER_ROLE_USER => 'default'
		];
		$authGroupsList = $this->Setting->getAuthGroupsList();
		$authPrefixes = $this->Setting->getAuthPrefixesList();
		foreach ($authGroupsList as $userRole => $fieldName) {
			$userGroup = Configure::read(PROJECT_CONFIG_NAME . '.' . $fieldName);
			if (!empty($userGroup)) {
				$authGroups[$userRole] = $userGroup;
			}
		}

		$isExternalAuth = false;
		if ((bool)Configure::read(PROJECT_CONFIG_NAME . '.ExternalAuth') == true) {
			$isExternalAuth = $this->UserInfo->isExternalAuth();
		}

		$controller = $this->request->param('controller');
		$action = $this->request->param('action');
		if ((in_array($controller, ['packages', 'profiles', 'hosts']) &&
			($action === 'index') && $this->RequestHandler->isXml()) ||
			(($controller === 'wpi') && ($action === 'config') &&
			$this->RequestHandler->prefers('js'))) {
			$protectXml = $this->Setting->getConfig('ProtectXml');
			if ($protectXml &&
				!$this->UserInfo->checkUserRole([USER_ROLE_EXPORT, USER_ROLE_ADMIN], true)) {
				AuthComponent::$sessionKey = false;
				$this->Auth->authenticate = [
					'BasicInternal' => [
						'passwordHasher' => [
							'className' => 'Simple',
							'hashType' => 'sha256'
						]
					],
				];
			} else {
				$this->Auth->allow($action);
			}
		} else {
			$this->Auth->authenticate = [
				'Internal' => [
					'passwordHasher' => [
						'className' => 'Simple',
						'hashType' => 'sha256'
					]
				],
				'CakeLdap.Ldap' => [
					'externalAuth' => $isExternalAuth,
					'groups' => $authGroups,
					'prefixes' => $authPrefixes,
				]
			];
		}

		$this->Auth->authorize = ['Controller'];
		$this->Auth->flash = [
			'element' => 'warning',
			'key' => 'auth',
			'params' => []
		];
		$this->Auth->loginAction = '/users/login';

		$this->RequestHandler->viewClassMap('xml', 'View');

		if (!$this->ViewExtension->isHtml()) {
			return parent::beforeFilter();
		}

		$this->loadModel('Log');
		$countLogErrors = $this->Log->getNumberErrors();

		$emailContact = $this->Setting->getConfig('EmailContact');
		$emailSubject = $this->Setting->getConfig('EmailSubject');
		$useNavbarContainerFluid = false;
		$projectName = __d('project', PROJECT_NAME);

		$this->set(compact(
			'isExternalAuth',
			'countLogErrors',
			'emailContact',
			'emailSubject',
			'useNavbarContainerFluid',
			'projectName'
		));

		parent::beforeFilter();
	}

/**
 * Called after the controller action is run, but before the view is rendered. You can use this method
 * to perform logic or set view variables that are required on every request.
 *
 * Actions:
 *  - Set global variables $pageTitlePrefix and $pageTitlePostfix for View;
 *  - Set global variables $specificJS and $specificCSS for View;
 *  - Set global variable $activeMenuUrl for View.
 *
 * @return void
 * @link http://book.cakephp.org/2.0/en/controllers.html#request-life-cycle-callbacks
 */
	public function beforeRender() {
		if (!$this->ViewExtension->isHtml()) {
			return parent::beforeRender();
		}

		$pageTitlePrefix = __d('project', PROJECT_PAGE_TITLE) . '::';
		$pageTitlePostfix = '';
		$role = $this->Auth->user('role');
		$roleName = $this->Setting->getAuthRoleName($role);
		if (!empty($roleName)) {
			$pageTitlePostfix .= '::' . mb_ucfirst($roleName);
		}

		$specificJS = (array)Hash::get($this->viewVars, 'specificJS');
		$specificCSS = (array)Hash::get($this->viewVars, 'specificCSS');
		$specificJS = array_merge($specificJS, [
			'actions' . DS . 'add',
			'attributes' . DS . 'add',
			'checks' . DS . 'add',
			'graph' . DS . 'view',
			'packages' . DS . 'add'
		]);
		$specificCSS = array_merge($specificCSS, [
			'graph' . DS . 'view',
		]);

		$targetFieldsSelected = (array)Hash::get($this->viewVars, 'search_targetFieldsSelected');
		$targetSearchControllers = [
			'Packages',
			'Profiles',
			'Hosts'
		];
		if (in_array($this->name, $targetSearchControllers)) {
			$targetSearchModelName = Inflector::singularize($this->name);
			$targetFieldsSelectedProcessed = [];
			foreach ($targetFieldsSelected as $targetFieldPath) {
				if ((mb_stripos($targetFieldPath, $targetSearchModelName) === 0) ||
					(mb_stripos($targetFieldPath, CAKE_SEARCH_INFO_TARGET_FIELD_ANY_PART) === 0)) {
					$targetFieldsSelectedProcessed[] = $targetFieldPath;
				}
			}
			if (!empty($targetFieldsSelectedProcessed)) {
				$targetFieldsSelected = $targetFieldsSelectedProcessed;
			}
		}

		$activeMenuUrl = null;
		$activeMenuUrlControllers = [
			'Actions',
			'ActionTypes',
			'Attributes',
			'Checks',
			'ExitCodes',
			'Graph',
			'PackagePriorities',
			'Variables'
		];
		if (in_array($this->name, $activeMenuUrlControllers)) {
			$breadCrumbs = (array)Hash::get($this->viewVars, 'breadCrumbs');
			if (isset($breadCrumbs[0][1])) {
				$activeMenuUrl = $breadCrumbs[0][1];
			}
		}

		$this->set('search_targetFieldsSelected', $targetFieldsSelected);
		$this->set(compact('pageTitlePrefix', 'pageTitlePostfix', 'specificJS', 'specificCSS', 'activeMenuUrl'));
		parent::beforeRender();
	}
}
