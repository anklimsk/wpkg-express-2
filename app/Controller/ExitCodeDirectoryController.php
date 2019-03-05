<?php
/**
 * This file is the controller file of the application. Used for
 *  management exit code directory.
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
 * @copyright Copyright 2018-2019, Andrey Klimov.
 * @package app.Controller
 */

App::uses('AppController', 'Controller');

/**
 * The controller is used for management exit code directory.
 *
 * This controller allows to perform the following operations:
 *  - to view the exit code directory;
 *  - AJAX retrieve description for exit code.
 *
 * @package app.Controller
 */
class ExitCodeDirectoryController extends AppController {

/**
 * The name of this controller. Controller names are plural, named after the model they manipulate.
 *
 * @var string
 * @link http://book.cakephp.org/2.0/en/controllers.html#controller-attributes
 */
	public $name = 'ExitCodeDirectory';

/**
 * Array containing the names of components this controller uses. Component names
 * should not contain the "Component" portion of the class name.
 *
 * @var array
 * @link http://book.cakephp.org/2.0/en/controllers/components.html
 */
	public $components = [
		'Paginator',
		'CakeTheme.Filter'
	];

/**
 * Settings for component 'Paginator'
 *
 * @var array
 */
	public $paginate = [
		'page' => 1,
		'limit' => 20,
		'maxLimit' => 250,
		'fields' => [
			'ExitCodeDirectory.id',
			'ExitCodeDirectory.code',
			'ExitCodeDirectory.hexadecimal',
			'ExitCodeDirectory.constant',
			'ExitCodeDirectory.description',
		],
		'order' => [
			'ExitCodeDirectory.code' => 'asc'
		],
		'recursive' => -1
	];

/**
 * Called before the controller action.
 *
 * Actions:
 *  - Configure components.
 *
 * @return void
 * @link http://book.cakephp.org/2.0/en/controllers.html#request-life-cycle-callbacks
 */
	public function beforeFilter() {
		$this->Security->unlockedActions = [
			'admin_description',
		];

		parent::beforeFilter();
	}

/**
 * Base of action `index`. Used to view the exit code directory.
 *
 * @return void
 */
	protected function _index() {
		$this->view = 'index';
		$conditions = $this->Filter->getFilterConditions();
		$this->Paginator->settings = $this->paginate;
		$exitCodeDirectory = $this->Paginator->paginate('ExitCodeDirectory', $conditions);
		if (empty($exitCodeDirectory)) {
			$this->Flash->information(__('Exit code directory is empty'));
		}
		$breadCrumbs = $this->ExitCodeDirectory->getBreadcrumbInfo();
		$breadCrumbs[] = __('Index');
		$pageHeader = __('Exit code directory');
		$this->ViewExtension->setRedirectUrl(true, 'directory');

		$this->set(compact('exitCodeDirectory', 'breadCrumbs', 'pageHeader'));
	}

/**
 * Action `index`. Used to view the exit code directory.
 *  User role - administrator.
 *
 * @return void
 */
	public function admin_index() {
		$this->_index();
	}

/**
 * Base of action `description`. Is used to AJAX retrieve
 *  description for exit code.
 *
 * POST Data:
 *  - `code`: the exit code for retrieve description.
 *
 * @throws BadRequestException if request is not `AJAX`, or not `POST`
 *  or not `JSON`
 * @return void
 */
	protected function _description() {
		Configure::write('debug', 0);
		if (!$this->request->is('ajax') || !$this->request->is('post') ||
			!$this->RequestHandler->prefers('json')) {
			throw new BadRequestException();
		}

		$data = [
			'result' => false,
			'description' => ''
		];
		$code = (string)$this->request->data('code');
		if (empty($code) && ($code !== '0')) {
			$this->set(compact('data'));
			$this->set('_serialize', 'data');
			return;
		}

		$description = (string)$this->ExitCodeDirectory->getDescription($code);
		$result = !empty($description);
		$data = compact('result', 'description');

		$this->set(compact('data'));
		$this->set('_serialize', 'data');
	}

/**
 * Action `description`. Is used to AJAX retrieve
 *  description for exit code.
 *  User role - administrator.
 *
 * @return void
 */
	public function admin_description() {
		$this->_description();
	}

}
