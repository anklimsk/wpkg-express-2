<?php
/**
 * This file is the controller file of the application. Used to
 *  manage exit code information for package actions.
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
 * @copyright Copyright 2018-2020, Andrey Klimov.
 * @package app.Controller
 */

App::uses('AppController', 'Controller');

/**
 * The controller is used to manage exit code information for
 *  package actions.
 *
 * This controller allows to perform the following operations:
 *  - to view, edit, and delete exit codes for package actions.
 *
 * @package app.Controller
 */
class ExitCodesController extends AppController {

/**
 * The name of this controller. Controller names are plural, named after the model they manipulate.
 *
 * @var string
 * @link http://book.cakephp.org/2.0/en/controllers.html#controller-attributes
 */
	public $name = 'ExitCodes';

/**
 * Array containing the names of components this controller uses. Component names
 * should not contain the "Component" portion of the class name.
 *
 * @var array
 * @link http://book.cakephp.org/2.0/en/controllers/components.html
 */
	public $components = [
		'ChangeState' => ['TargetModel' => 'ExitCode'],
	];

/**
 * An array containing the class names of models this controller uses.
 *
 * @var mixed
 * @link http://book.cakephp.org/2.0/en/controllers.html#components-helpers-and-uses
 */
	public $uses = [
		'ExitCode'
	];

/**
 * Base of action `index`. Used for redirect to home page.
 *
 * @return void
 */
	protected function _index() {
		$this->redirect('/');
	}

/**
 * Action `index`. Used for redirect to home page.
 *  User role - administrator.
 *
 * @return void
 */
	public function admin_index() {
		$this->_index();
	}

/**
 * Base of action `view`. Used to view information about exit codes.
 *
 * @param int|string $refId Record ID of the package action.
 * @throws NotFoundException if record for parameter $refId was not found
 * @return void
 */
	protected function _view($refId = null) {
		$this->view = 'view';
		$fullName = $this->ExitCode->getFullName(null, null, null, $refId);
		if (empty($fullName)) {
			return $this->ViewExtension->setExceptionMessage(new NotFoundException(__('Invalid ID for exit code')));
		}
		$breadCrumbs = $this->ExitCode->getBreadcrumbInfo(null, null, null, $refId);
		$breadCrumbs[] = __('Viewing');
		$exitCodes = $this->ExitCode->getExitCodes($refId);
		$this->ViewExtension->setRedirectUrl(true, 'package');
		$pageHeader = __('Information of exit codes');
		$headerMenuActions = [
			[
				'fas fa-plus',
				__('Add exit code'),
				['controller' => 'exit_codes', 'action' => 'add', $refId],
				[
					'title' => __('Add exit code'),
					'action-type' => 'modal',
				]
			],
		];

		$this->set(compact('exitCodes', 'breadCrumbs', 'fullName',
			'pageHeader', 'headerMenuActions'));
	}

/**
 * Action `view`. Used to view information about exit codes.
 * User role - administrator.
 *
 * @param int|string $refId Record ID of the package action.
 * @return void
 */
	public function admin_view($refId = null) {
		$this->_view($refId);
	}

/**
 * Base of action `add`. Used to add exit code.
 *
 * POST Data:
 *  - ExitCode: array data of exit code
 *
 * @param int|string $refId Record ID of the package action.
 * @return void
 */
	protected function _add($refId = null) {
		$this->view = 'add';
		$fullName = $this->ExitCode->getFullName(null, null, null, $refId);
		if (empty($fullName)) {
			return $this->ViewExtension->setExceptionMessage(new NotFoundException(__('Invalid referrer ID or type for exit code')));
		}
		$breadCrumbs = $this->ExitCode->getBreadcrumbInfo(null, null, null, $refId);
		$breadCrumbs[] = __('Adding');
		if ($this->request->is('post')) {
			$this->ExitCode->create();
			if ($this->ExitCode->saveAndUpdateDate($this->request->data, $breadCrumbs)) {
				$this->Flash->success(__('Exit code has been saved.'));

				return $this->ViewExtension->redirectByUrl(null, 'package');
			} else {
				$this->Flash->error(__('Exit code could not be saved. Please, try again.'));
			}
		} else {
			$this->request->data = $this->ExitCode->getDefaultValues($refId);
			$this->ViewExtension->setRedirectUrl(null, 'package');
		}
		$pageHeader = __('Adding exit code');
		$listRebootType = $this->ExitCode->ExitcodeRebootType->getListRebootTypes();

		$this->set(compact('breadCrumbs', 'pageHeader', 'fullName', 'listRebootType'));
	}

/**
 * Action `add`. Used to add exit code.
 *  User role - administrator.
 *
 * @param int|string $refId Record ID of the package action.
 * @return void
 */
	public function admin_add($refId = null) {
		$this->_add($refId);
	}

/**
 * Base of action `edit`. Used to edit information about exit code.
 *
 * POST Data:
 *  - ExitCode: array data of exit code
 *
 * @param int|string $id ID of record for editing
 * @throws NotFoundException if record for parameter $id was not found
 * @return void
 */
	protected function _edit($id = null) {
		$this->view = 'edit';
		$exitCode = $this->ExitCode->get($id);
		if (empty($exitCode)) {
			return $this->ViewExtension->setExceptionMessage(new NotFoundException(__('Invalid ID for exit code')));
		}

		$refId = $exitCode['ExitCode']['package_action_id'];
		$breadCrumbs = $this->ExitCode->getBreadcrumbInfo($id, null, null, $refId);
		$breadCrumbs[] = __('Editing');
		$fullName = $this->ExitCode->getFullName($id, null, null, $refId);
		if ($this->request->is(['post', 'put'])) {
			if ($this->ExitCode->saveAndUpdateDate($this->request->data, $breadCrumbs)) {
				$this->Flash->success(__('Exit code has been saved.'));

				return $this->ViewExtension->redirectByUrl(null, 'package');
			} else {
				$this->Flash->error(__('Exit code could not be saved. Please, try again.'));
			}
		} else {
			$this->request->data = $exitCode;
			$this->ViewExtension->setRedirectUrl(null, 'package');
		}
		$pageHeader = __('Editing exit code');
		$listRebootType = $this->ExitCode->ExitcodeRebootType->getListRebootTypes();

		$this->set(compact('breadCrumbs', 'pageHeader', 'fullName', 'listRebootType'));
	}

/**
 * Action `edit`. Used to edit information about exit code.
 *  User role - administrator.
 *
 * @param int|string $id ID of record for editing
 * @return void
 */
	public function admin_edit($id = null) {
		$this->_edit($id);
	}

/**
 * Base of action `delete`. Used to delete exit code.
 *
 * @param int|string $id ID of record for deleting
 * @throws NotFoundException if record for parameter $id was not found
 * @throws MethodNotAllowedException if request is not `POST` or `DELETE`
 * @return void
 */
	protected function _delete($id = null) {
		$this->ChangeState->delete($id);
	}

/**
 * Action `delete`. Used to delete exit code.
 *  User role - administrator.
 *
 * @param int|string $id ID of record for deleting
 * @return void
 */
	public function admin_delete($id = null) {
		$this->_delete($id);
	}

}
