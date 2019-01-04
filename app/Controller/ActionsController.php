<?php
/**
 * This file is the controller file of the application. Used for
 *  management information about the package actions.
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

App::uses('AppController', 'Controller');

/**
 * The controller is used for management information about the package actions.
 *
 * This controller allows to perform the following operations:
 *  - to view, edit, delete and changing position of package action;
 *  - verifying and recovery state list of package actions;
 *  - autocomplete command line switches.
 *
 * @package app.Controller
 */
class ActionsController extends AppController {

/**
 * The name of this controller. Controller names are plural, named after the model they manipulate.
 *
 * @var string
 * @link http://book.cakephp.org/2.0/en/controllers.html#controller-attributes
 */
	public $name = 'Actions';

/**
 * Array containing the names of components this controller uses. Component names
 * should not contain the "Component" portion of the class name.
 *
 * @var array
 * @link http://book.cakephp.org/2.0/en/controllers/components.html
 */
	public $components = [
		'ChangeState' => ['TargetModel' => 'PackageAction'],
		'VerifyData' => ['TargetModel' => 'PackageAction'],
		'CakeTheme.Move' => ['model' => 'PackageAction']
	];

/**
 * An array containing the names of helpers this controller uses. The array elements should
 * not contain the "Helper" part of the class name.
 *
 * @var mixed
 * @link http://book.cakephp.org/2.0/en/controllers.html#components-helpers-and-uses
 */
	public $helpers = [
		'Tools.Tree',
		'Check',
	];

/**
 * An array containing the class names of models this controller uses.
 *
 * @var mixed
 * @link http://book.cakephp.org/2.0/en/controllers.html#components-helpers-and-uses
 */
	public $uses = [
		'PackageAction',
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
			'admin_drop',
			'admin_move',
			'admin_autocomplete',
		];

		parent::beforeFilter();
	}

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
 * Base of action `view`. Used to view information about package actions.
 *
 * @param int|string $refId Record ID of the package.
 * @throws NotFoundException if record for parameter $refId was not found
 * @return void
 */
	protected function _view($refId = null) {
		$this->view = 'view';
		$fullName = $this->PackageAction->getFullName(null, null, null, $refId);
		if (empty($fullName)) {
			return $this->ViewExtension->setExceptionMessage(new NotFoundException(__('Invalid ID for package action')));
		}
		$breadCrumbs = $this->PackageAction->getBreadcrumbInfo(null, null, null, $refId);
		$breadCrumbs[] = __('Viewing');
		$packageActions = $this->PackageAction->getPackageActions($refId);
		$pageHeader = __('Information of package actions');
		$headerMenuActions = [
			[
				'fas fa-plus',
				__('Add action'),
				['controller' => 'actions', 'action' => 'add', $refId],
				[
					'title' => __('Add action'),
					'action-type' => 'modal',
				]
			],
			[
				'fas fa-clipboard-check',
				__('Verify state of list actions'),
				['controller' => 'actions', 'action' => 'verify', $refId],
				[
					'title' => __('Verify state of list package actions'),
					'action-type' => 'modal'
				]
			],
			'divider',
			[
				'fas fa-clipboard-list',
				__('Edit action types'),
				['controller' => 'action_types', 'action' => 'index'],
				[
					'title' => __('Edit package action types'),
					'action-type' => 'modal',
				]
			],
		];
		$this->ViewExtension->setRedirectUrl(true, 'package');

		$this->set(compact('packageActions', 'breadCrumbs', 'fullName',
			'pageHeader', 'headerMenuActions'));
	}

/**
 * Action `view`. Used to view information about package actions.
 * User role - administrator.
 *
 * @param int|string $refId Record ID of the package.
 * @return void
 */
	public function admin_view($refId = null) {
		$this->_view($refId);
	}

/**
 * Base of action `add`. Used to add package action.
 *
 * POST Data:
 *  - PackageAction: array data of package action
 *
 * @param int|string $refId Record ID of the package.
 * @return void
 */
	protected function _add($refId = null) {
		$this->view = 'add';
		$fullName = $this->PackageAction->getFullName(null, null, null, $refId);
		if (empty($fullName)) {
			return $this->ViewExtension->setExceptionMessage(new NotFoundException(__('Invalid referrer ID or type for package action')));
		}
		if ($this->request->is('post')) {
			$this->PackageAction->create();
			if ($this->PackageAction->save($this->request->data)) {
				$this->Flash->success(__('Package action has been saved.'));

				return $this->ViewExtension->redirectByUrl(null, 'package');
			} else {
				$this->Flash->error(__('Package action could not be saved. Please, try again.'));
			}
		} else {
			$this->request->data = $this->PackageAction->getDefaultValues($refId);
			$namedParams = $this->request->param('named');
			if (empty($namedParams)) {
				$this->ViewExtension->setRedirectUrl(null, 'package');
			}
		}
		$this->_parseNamedParam();
		$this->PackageAction->createValidationRules(
			$this->request->data('PackageAction.action_type_id'),
			$this->request->data('PackageAction.command_type_id')
		);
		$breadCrumbs = $this->PackageAction->getBreadcrumbInfo(null, null, null, $refId);
		$breadCrumbs[] = __('Adding');
		$pageHeader = __('Adding package action');
		$listActionType = $this->PackageAction->PackageActionType->getListActionTypes();
		$listCommandType = $this->PackageAction->getListCommandTypes();

		$this->set(compact('breadCrumbs', 'pageHeader', 'fullName', 'refId',
			'listActionType', 'listCommandType'));
	}

/**
 * Action `add`. Used to add package action.
 *  User role - administrator.
 *
 * @param int|string $refId Record ID of the package.
 * @return void
 */
	public function admin_add($refId = null) {
		$this->_add($refId);
	}

/**
 * Base of action `edit`. Used to edit information about package action.
 *
 * POST Data:
 *  - PackageAction: array data of package action
 *
 * @param int|string $id ID of record for editing
 * @throws NotFoundException if record for parameter $id was not found
 * @return void
 */
	protected function _edit($id = null) {
		$this->view = 'edit';
		$packageAction = $this->PackageAction->get($id, false);
		if (empty($packageAction)) {
			return $this->ViewExtension->setExceptionMessage(new NotFoundException(__('Invalid ID for package action')));
		}

		$refId = $packageAction['PackageAction']['package_id'];
		$breadCrumbs = $this->PackageAction->getBreadcrumbInfo($id, null, null, $refId);
		$breadCrumbs[] = __('Editing');
		$fullName = $this->PackageAction->getFullName($id, null, null, $refId);
		if ($this->request->is(['post', 'put'])) {
			if ($this->PackageAction->save($this->request->data)) {
				$this->Flash->success(__('Package action has been saved.'));

				return $this->ViewExtension->redirectByUrl(null, 'package');
			} else {
				$this->Flash->error(__('Package action could not be saved. Please, try again.'));
			}
		} else {
			$this->request->data = $packageAction;
			$namedParams = $this->request->param('named');
			if (empty($namedParams)) {
				$this->ViewExtension->setRedirectUrl(null, 'package');
			}
		}
		$this->_parseNamedParam();
		$this->PackageAction->createValidationRules(
			$this->request->data('PackageAction.action_type_id'),
			$this->request->data('PackageAction.command_type_id')
		);
		$pageHeader = __('Editing package action');
		$listActionType = $this->PackageAction->PackageActionType->getListActionTypes();
		$listCommandType = $this->PackageAction->getListCommandTypes();

		$this->set(compact('breadCrumbs', 'pageHeader', 'fullName', 'refId',
			'listActionType', 'listCommandType'));
	}

/**
 * Action `edit`. Used to edit information about package action.
 *  User role - administrator.
 *
 * @param int|string $id ID of record for editing
 * @return void
 */
	public function admin_edit($id = null) {
		$this->_edit($id);
	}

/**
 * Base of action `delete`. Used to delete package action.
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
 * Action `delete`. Used to delete package action.
 *  User role - administrator.
 *
 * @param int|string $id ID of record for deleting
 * @return void
 */
	public function admin_delete($id = null) {
		$this->_delete($id);
	}

/**
 * Action `move`. Used to move package action to new position.
 * 
 * @param string $direct Direction for moving: `up`, `down`, `top`, `bottom`
 * @param int|string $id ID of record for moving
 * @param int|string $delta Delta for moving
 * @throws MethodNotAllowedException if request is not POST
 * @return void
 */
	protected function _move($direct = null, $id = null, $delta = 1) {
		$this->Move->moveItem($direct, $id, $delta);
	}

/**
 * Action `move`. Used to move package action to new position.
 *  User role - administrator.
 *
 * @param string $direct Direction for moving: `up`, `down`, `top`, `bottom`
 * @param int|string $id ID of record for moving
 * @param int|string $delta Delta for moving
 * @return void
 */
	public function admin_move($direct = null, $id = null, $delta = 1) {
		$this->_move($direct, $id, $delta);
	}

/**
 * Action `drop`. Used to drag and drop package action to new position
 *
 * POST Data:
 *  - `target` The ID of the item to moving to new position;
 *  - `parent` New parent ID of item;
 *  - `parentStart` Old parent ID of item;
 *  - `tree` Array of ID subtree for item. 
 *
 * @throws BadRequestException if request is not AJAX, POST or JSON.
 * @throws InternalErrorException if tree of subordinate is disabled
 * @throws MethodNotAllowedException if request is not POST
 * @return void
 */
	protected function _drop() {
		$this->Move->dropItem();
	}

/**
 * Action `drop`. Used to drag and drop package action to new position
 *  User role - administrator.
 *
 * @return void
 */
	public function admin_drop() {
		$this->_drop();
	}

/**
 * Base of action `verify`. Used to verify state list of package actions.
 *
 * @param int|string $refId Record ID of the package.
 * @return void
 */
	protected function _verify($refId = null) {
		$this->view = 'verify';
		$actionsState = $this->PackageAction->verifyActions($refId);
		$fullName = $this->PackageAction->getFullName(null, null, null, $refId);
		$pageHeader = __('Verifying state list package actions');
		$breadCrumbs = $this->PackageAction->getBreadcrumbInfo(null, null, null, $refId);
		$breadCrumbs[] = __('Verifying');

		$this->set(compact('actionsState', 'fullName', 'breadCrumbs', 'pageHeader', 'refId'));
	}

/**
 * Action `verify`. Used to verify state list of package actions.
 *  User role - administrator.
 *
 * @param int|string $refId Record ID of the package.
 * @return void
 */
	public function admin_verify($refId = null) {
		$this->_verify($refId);
	}

/**
 * Base of action `recover`. Used to recover state list of
 *  package actions.
 *
 * @param int|string $refType Type of package action.
 * @param int|string $refId Record ID of the package.
 * @return void
 */
	protected function _recover($refType = null, $refId = null) {
		$this->VerifyData->actionRecover($refType, $refId);
	}

/**
 * Action `recover`. Used to recover state list of
 *  package actions.
 *  User role - administrator.
 *
 * @param int|string $refType Type of package action.
 * @param int|string $refId Record ID of the package.
 * @return void
 */
	public function admin_recover($refType = null, $refId = null) {
		$this->_recover($refType, $refId);
	}

/**
 * Base of action `autocomplete`. Is used to autocomplete input field
 *  of command of package action.
 *
 * POST Data:
 *  - `query`: query string for autocomple;
 *  - `type`: type of request: `switch` or `command`.
 *
 * @throws BadRequestException if request is not `AJAX`, or not `POST`
 *  or not `JSON`
 * @return void
 */
	protected function _autocomplete() {
		Configure::write('debug', 0);
		if (!$this->request->is('ajax') || !$this->request->is('post') ||
			!$this->RequestHandler->prefers('json')) {
			throw new BadRequestException();
		}

		$data = [];
		$query = $this->request->data('query');
		$type = $this->request->data('type');
		if (empty($query)) {
			$this->set(compact('data'));
			$this->set('_serialize', 'data');
			return;
		}

		$limit = $this->Setting->getConfig('AutocompleteLimit');
		$data = $this->PackageAction->getAutocomplete($query, $type, $limit);
		if (empty($data)) {
			$data = [];
		}

		$this->set(compact('data'));
		$this->set('_serialize', 'data');
	}

/**
 * Action `autocomplete`. Is used to autocomplete input field
 *  of command of package action.
 *  User role - administrator.
 *
 * @return void
 */
	public function admin_autocomplete() {
		$this->_autocomplete();
	}

/**
 * Processing named parameters from request.
 *  Used to set values for action type and command type
 *  in form fields.
 *
 * @return void
 */
	protected function _parseNamedParam() {
		$argActionType = $this->request->param('named.action');
		if (empty($argActionType)) {
			$argActionType = $this->request->data('PackageAction.action_type_id');
		}

		$argCommandType = $this->request->param('named.command');
		if (empty($argCommandType)) {
			$argCommandType = $this->request->data('PackageAction.command_type_id');
		}

		$this->request->data('PackageAction.action_type_id', $argActionType);
		$this->request->data('PackageAction.command_type_id', $argCommandType);
	}

}
