<?php
/**
 * This file is the controller file of the application. Used for
 *  management information about the variables.
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
 * The controller is used for management information about the variables.
 *
 * This controller allows to perform the following operations:
 *  - to view, edit, delete and changing position of variable;
 *  - verifying and recovery state list of variables;
 *  - autocomplete name of variables.
 *
 * @package app.Controller
 */
class VariablesController extends AppController {

/**
 * The name of this controller. Controller names are plural, named after the model they manipulate.
 *
 * @var string
 * @link http://book.cakephp.org/2.0/en/controllers.html#controller-attributes
 */
	public $name = 'Variables';

/**
 * Array containing the names of components this controller uses. Component names
 * should not contain the "Component" portion of the class name.
 *
 * @var array
 * @link http://book.cakephp.org/2.0/en/controllers/components.html
 */
	public $components = [
		'ChangeState' => ['TargetModel' => 'Variable'],
		'VerifyData' => ['TargetModel' => 'Variable'],
		'CakeTheme.Move' => ['model' => 'Variable']
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
 * Base of action `view`. Used to view information about variables.
 *
 * @param int|string $refType ID type of object variables
 * @param int|string $refId Record ID of the node variables
 * @throws NotFoundException if record for parameter $refType and $refId
 *  was not found
 * @return void
 */
	protected function _view($refType = null, $refId = null) {
		$this->view = 'view';
		$refTypeName = $this->Variable->getNameTypeFor($refType);
		$fullName = $this->Variable->getFullName(null, $refType, null, $refId);
		if (empty($refTypeName) || empty($fullName)) {
			return $this->ViewExtension->setExceptionMessage(new NotFoundException(__('Invalid referrer ID or type for variable')));
		}
		$variables = $this->Variable->getAllVariables($refType, $refId);
		$this->ViewExtension->setRedirectUrl(true, $refTypeName);
		$breadCrumbs = $this->Variable->getBreadcrumbInfo(null, $refType, null, $refId);
		$breadCrumbs[] = __('Viewing');
		$pageHeader = __('Information of variables');
		$headerMenuActions = [
			[
				'fas fa-plus',
				__('Add variable'),
				['controller' => 'variables', 'action' => 'add', $refType, $refId],
				[
					'title' => __('Add variable'),
				]
			],
			[
				'fas fa-clipboard-check',
				__('Verify state of list variables'),
				['controller' => 'variables', 'action' => 'verify', $refType, $refId],
				[
					'title' => __('Verify state of list variables'),
				]
			],
		];

		$this->set(compact('variables', 'breadCrumbs', 'pageHeader', 'headerMenuActions',
			'fullName'));
	}

/**
 * Action `view`. Used to view information about variables.
 *  User role - administrator.
 *
 * @param int|string $refType ID type of object variables
 * @param int|string $refId Record ID of the node variables
 * @return void
 */
	public function admin_view($refType = null, $refId = null) {
		$this->_view($refType, $refId);
	}

/**
 * Action `global`. Used to view information about global variables.
 * User role - administrator.
 *
 * @return void
 */
	public function admin_global() {
		$this->_view(VARIABLE_TYPE_CONFIG, 1);
	}

/**
 * Base of action `add`. Used to add variable.
 *
 * POST Data:
 *  - Variable: array data of variable
 *
 * @param int|string $refType ID type of object variables
 * @param int|string $refId Record ID of the node variables
 * @return void
 */
	protected function _add($refType = null, $refId = null) {
		$this->view = 'add';
		$refTypeName = $this->Variable->getNameTypeFor($refType);
		$fullName = $this->Variable->getFullName(null, $refType, null, $refId);
		if (empty($refTypeName) || empty($fullName)) {
			return $this->ViewExtension->setExceptionMessage(new NotFoundException(__('Invalid referrer ID or type for variable')));
		}
		$breadCrumbs = $this->Variable->getBreadcrumbInfo(null, $refType, null, $refId);
		$breadCrumbs[] = __('Adding');
		if ($this->request->is('post')) {
			$this->Variable->create();
			if ($this->Variable->saveAndUpdateDate($this->request->data, $breadCrumbs)) {
				$this->Flash->success(__('Variable has been saved.'));

				return $this->ViewExtension->redirectByUrl(null, $refTypeName);
			} else {
				$this->Flash->error(__('Variable could not be saved. Please, try again.'));
			}
		} else {
			$this->request->data = $this->Variable->getDefaultValues($refType, $refId);
			$this->ViewExtension->setRedirectUrl(null, $refTypeName);
		}
		$pageHeader = __('Adding variable');

		$this->set(compact('breadCrumbs', 'pageHeader', 'fullName', 'refType', 'refId'));
	}

/**
 * Action `add`. Used to add variable.
 *  User role - administrator.
 *
 * @param int|string $refType ID type of object variables
 * @param int|string $refId Record ID of the node variables
 * @return void
 */
	public function admin_add($refType = null, $refId = null) {
		$this->_add($refType, $refId);
	}

/**
 * Base of action `edit`. Used to edit information about variable.
 *
 * POST Data:
 *  - Variable: array data of variable
 *
 * @param int|string $id ID of record for editing
 * @throws NotFoundException if record for parameter $id was not found
 * @return void
 */
	protected function _edit($id = null) {
		$this->view = 'edit';
		$variable = $this->Variable->get($id);
		if (empty($variable)) {
			return $this->ViewExtension->setExceptionMessage(new NotFoundException(__('Invalid ID for variable')));
		}

		$refId = $variable['Variable']['ref_id'];
		$refType = $variable['Variable']['ref_type'];
		$refTypeName = $this->Variable->getNameTypeFor($refType);
		$breadCrumbs = $this->Variable->getBreadcrumbInfo($id, $refType, null, $refId);
		$breadCrumbs[] = __('Editing');
		$fullName = $this->Variable->getFullName($id, $refType, null, $refId);
		if ($this->request->is(['post', 'put'])) {
			if ($this->Variable->saveAndUpdateDate($this->request->data, $breadCrumbs)) {
				$this->Flash->success(__('Variable has been saved.'));

				return $this->ViewExtension->redirectByUrl(null, $refTypeName);
			} else {
				$this->Flash->error(__('Variable could not be saved. Please, try again.'));
			}
		} else {
			$this->ViewExtension->setRedirectUrl(null, $refTypeName);
			$this->request->data = $variable;
		}
		$pageHeader = __('Editing variable');

		$this->set(compact('breadCrumbs', 'pageHeader', 'fullName', 'refType', 'refId'));
	}

/**
 * Action `edit`. Used to edit information about variable.
 *  User role - administrator.
 *
 * @param int|string $id ID of record for editing
 * @return void
 */
	public function admin_edit($id = null) {
		$this->_edit($id);
	}

/**
 * Base of action `delete`. Used to delete variable.
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
 * Action `delete`. Used to delete variable.
 *  User role - administrator.
 *
 * @param int|string $id ID of record for deleting
 * @return void
 */
	public function admin_delete($id = null) {
		$this->_delete($id);
	}

/**
 * Action `move`. Used to move variable to new position.
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
 * Action `move`. Used to move variable to new position.
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
 * Action `drop`. Used to drag and drop variable to new position
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
 * Action `drop`. Used to drag and drop variable to new position
 *  User role - administrator.
 *
 * @return void
 */
	public function admin_drop() {
		$this->_drop();
	}

/**
 * Base of action `verify`. Used to verify state list of variables.
 *
 * @param int|string $refType ID type of object variables
 * @param int|string $refId Record ID of the node variables
 * @return void
 */
	protected function _verify($refType = null, $refId = null) {
		$this->VerifyData->actionVerify($refType, $refId);
	}

/**
 * Action `verify`. Used to verify state list of variables.
 *  User role - administrator.
 *
 * @param int|string $refType ID type of object variables
 * @param int|string $refId Record ID of the node variables
 * @return void
 */
	public function admin_verify($refType = null, $refId = null) {
		$this->_verify($refType, $refId);
	}

/**
 * Base of action `recover`. Used to recover state list of
 *  variables.
 *
 * @param int|string $refType ID type of object variables
 * @param int|string $refId Record ID of the node variables
 * @return void
 */
	protected function _recover($refType = null, $refId = null) {
		$this->VerifyData->actionRecover($refType, $refId);
	}

/**
 * Action `recover`. Used to recover state list of
 *  variables.
 *  User role - administrator.
 *
 * @param int|string $refType ID type of object variables
 * @param int|string $refId Record ID of the node variables
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
 *  - `ref-type`: ID type of object variables;
 *  - `ref-id`: record ID of the node variables.
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
		$refType = $this->request->data('ref-type');
		$refId = $this->request->data('ref-id');
		$convertRef = $this->request->data('convert-ref');
		if (empty($query)) {
			$this->set(compact('data'));
			$this->set('_serialize', 'data');
			return;
		}

		$limit = $this->Setting->getConfig('AutocompleteLimit');
		$data = $this->Variable->getAutocomplete($query, $refType, $refId, $convertRef, $limit);
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
}
