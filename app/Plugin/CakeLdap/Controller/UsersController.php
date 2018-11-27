<?php
/**
 * This file is the controller file of the plugin.
 * Used for for management users.
 *
 * CakeLdap: Authentication of users by member group of Active Directory.
 * @copyright Copyright 2016-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.Controller
 */

App::uses('CakeLdapAppController', 'CakeLdap.Controller');

/**
 * The controller is used for management users.
 *
 * This controller allows to perform the following operations:
 *  - login;
 *  - logout.
 * @package plugin.Controller
 */
class UsersController extends CakeLdapAppController {

/**
 * The name of this controller. Controller names are plural, named after the model they manipulate.
 *
 * @var string
 * @link http://book.cakephp.org/2.0/en/controllers.html#controller-attributes
 */
	public $name = 'Users';

/**
 * An array containing the class names of models this controller uses.
 *
 * @var mixed
 * @link http://book.cakephp.org/2.0/en/controllers.html#components-helpers-and-uses
 */
	public $uses = ['CakeLdap.User'];

/**
 * The name of the layout file to render the view inside of. The name specified
 * is the filename of the layout in /app/View/Layouts without the .ctp
 * extension.
 *
 * @var string
 */
	public $layout = 'CakeLdap.login';

/**
 * Called before the controller action. You can use this method to configure and customize components
 * or perform logic that needs to happen before each controller action.
 *
 * Actions:
 *  - Configure Auth component;
 *
 * @return void
 * @link http://book.cakephp.org/2.0/en/controllers.html#request-life-cycle-callbacks
 */
	public function beforeFilter() {
		if ($this->Auth->loggedIn()) {
			return parent::beforeFilter();
		}

		Configure::write('debug', 0);
		$this->Auth->authError = false;
		$key = 'auth';
		if (isset($this->Auth->flash['key']) && !empty($this->Auth->flash['key'])) {
			$key = $this->Auth->flash['key'];
		}
		if ($this->Session->check('Message.' . $key)) {
			$this->Session->delete('Message.' . $key);
		}

		parent::beforeFilter();
	}

/**
 * Action `login`. Used to login user.
 *
 * @return void
 */
	public function login() {
		$externalAuth = false;
		if (isset($this->Auth->authenticate['CakeLdap.Ldap']['externalAuth'])) {
			$externalAuth = $this->Auth->authenticate['CakeLdap.Ldap']['externalAuth'];
		}

		if ($this->request->is('post') || ($externalAuth === true)) {
			if ($this->Auth->login()) {
				return $this->redirect($this->Auth->redirectUrl());
			}
			$this->Flash->error(__d('cake_ldap', 'Invalid username or password, try again'));
		}

		$this->set('pageTitle', __d('cake_ldap', 'Login'));
	}

/**
 * Action `logout`. Used to logout user.
 *
 * @return void
 */
	public function logout() {
		$this->redirect($this->Auth->logout());
	}
}
