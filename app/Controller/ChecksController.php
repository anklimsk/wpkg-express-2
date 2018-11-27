<?php
/**
 * This file is the controller file of the application. Used for
 *  management information about the checks.
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
 * The controller is used for management information about the checks.
 *
 * This controller allows to perform the following operations:
 *  - to view, edit, delete and changing position of check;
 *  - verifying and recovery state tree of checks.
 *
 * @package app.Controller
 */
class ChecksController extends AppController {

/**
 * The name of this controller. Controller names are plural, named after the model they manipulate.
 *
 * @var string
 * @link http://book.cakephp.org/2.0/en/controllers.html#controller-attributes
 */
	public $name = 'Checks';

/**
 * Array containing the names of components this controller uses. Component names
 * should not contain the "Component" portion of the class name.
 *
 * @var array
 * @link http://book.cakephp.org/2.0/en/controllers/components.html
 */
	public $components = [
		'ChangeState' => ['TargetModel' => 'Check'],
		'VerifyData' => ['TargetModel' => 'Check'],
		'CakeTheme.Move' => ['model' => 'Check']
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
 * Base of action `view`. Used to view information about checks.
 *
 * @param int|string $refType ID type of object checks
 * @param int|string $refId Record ID of the node checks
 * @throws NotFoundException if record for parameter $refType and $refId
 *  was not found
 * @return void
 */
	protected function _view($refType = null, $refId = null) {
		$this->view = 'view';
		$refTypeName = $this->Check->getNameTypeFor($refType);

		$fullName = $this->Check->getFullName(null, $refType, null, $refId);
		if (empty($refTypeName) || empty($fullName)) {
			return $this->ViewExtension->setExceptionMessage(new NotFoundException(__('Invalid referrer ID or type for check')));
		}
		$breadCrumbs = $this->Check->getBreadcrumbInfo(null, $refType, null, $refId);
		$breadCrumbs[] = __('Viewing');
		$checks = $this->Check->getChecks($refType, $refId);
		$this->ViewExtension->setRedirectUrl(true, $refTypeName);
		$pageHeader = __('Information of checks');
		$headerMenuActions = [
			[
				'fas fa-plus',
				__('Add check'),
				['controller' => 'checks', 'action' => 'add', $refType, $refId],
				[
					'title' => __('Add check'),
					'data-toggle' => 'modal'
				]
			],
			[
				'fas fa-clipboard-check',
				__('Verify state of tree checks'),
				['controller' => 'checks', 'action' => 'verify', $refType, $refId],
				[
					'title' => __('Verify state of tree checks'),
					'data-toggle' => 'modal'
				]
			],
		];

		$this->set(compact('checks', 'breadCrumbs', 'fullName',
			'pageHeader', 'headerMenuActions'));
	}

/**
 * Action `view`. Used to view information about check.
 *  User role - administrator.
 *
 * @param int|string $refType ID type of object checks
 * @param int|string $refId Record ID of the node checks
 * @return void
 */
	public function admin_view($refType = null, $refId = null) {
		$this->_view($refType, $refId);
	}

/**
 * Base of action `add`. Used to add check.
 *
 * POST Data:
 *  - Check: array data of check
 *
 * @param int|string $refType ID type of object checks
 * @param int|string $refId Record ID of the node checks 
 * @return void
 */
	protected function _add($refType = null, $refId = null) {
		$this->view = 'add';
		$refTypeName = $this->Check->getNameTypeFor($refType);
		$fullName = $this->Check->getFullName(null, $refType, null, $refId);
		if (empty($refTypeName) || empty($fullName)) {
			return $this->ViewExtension->setExceptionMessage(new NotFoundException(__('Invalid referrer ID or type for check')));
		}
		$breadCrumbs = $this->Check->getBreadcrumbInfo(null, $refType, null, $refId);
		$breadCrumbs[] = __('Adding');
		if ($this->request->is('post')) {
			$this->Check->create();
			if ($this->Check->save($this->request->data)) {
				$this->Flash->success(__('Check has been saved.'));

				return $this->ViewExtension->redirectByUrl(null, $refTypeName);
			} else {
				$this->Flash->error(__('Check could not be saved. Please, try again.'));
			}
		} else {
			$check = [
				'Check' => [
					'ref_type' => $refType,
					'ref_id' => $refId,
					'parent_id' => null,
					'type' => CHECK_TYPE_UNINSTALL,
					'condition' => CHECK_CONDITION_UNINSTALL_EXISTS,
				]
			];
			$this->request->data = $check;
			$namedParams = $this->request->param('named');
			if (empty($namedParams)) {
				$this->ViewExtension->setRedirectUrl(null, $refTypeName);
			}
		}
		$listType = $this->Check->getListCheckTypes();
		$listParent = $this->Check->getLogicalChecksList($refType, $refId);
		$listCondition = $this->_getListCondition();
		$this->Check->createValidationRules(
			$this->request->data('Check.type'),
			$this->request->data('Check.condition')
		);
		$pageHeader = __('Adding check');

		$this->set(compact('breadCrumbs', 'fullName', 'refType', 'refId',
			'listType', 'listParent', 'listCondition', 'pageHeader'));
	}

/**
 * Action `add`. Used to add check.
 *  User role - administrator.
 *
 * @param int|string $refType ID type of object checks
 * @param int|string $refId Record ID of the node checks
 * @return void
 */
	public function admin_add($refType = null, $refId = null) {
		$this->_add($refType, $refId);
	}

/**
 * Base of action `edit`. Used to edit information about check.
 *
 * POST Data:
 *  - Check: array data of check
 *
 * @param int|string $id ID of record for editing
 * @throws NotFoundException if record for parameter $id was not found
 * @return void
 */
	protected function _edit($id = null) {
		$this->view = 'edit';
		$check = $this->Check->get($id);
		if (empty($check)) {
			return $this->ViewExtension->setExceptionMessage(new NotFoundException(__('Invalid ID for check')));
		}

		$refId = $check['Check']['ref_id'];
		$refType = $check['Check']['ref_type'];
		$refTypeName = $this->Check->getNameTypeFor($refType);
		$breadCrumbs = $this->Check->getBreadcrumbInfo(null, $refType, null, $refId);
		$breadCrumbs[] = __('Editing');
		$fullName = $this->Check->getFullName($id, $refType, null, $refId);
		if ($this->request->is(['post', 'put'])) {
			if ($this->Check->save($this->request->data)) {
				$this->Flash->success(__('Check has been saved.'));

				return $this->ViewExtension->redirectByUrl(null, $refTypeName);
			} else {
				$this->Flash->error(__('Check could not be saved. Please, try again.'));
			}
		} else {
			$namedParams = $this->request->param('named');
			if (empty($namedParams)) {
				$this->ViewExtension->setRedirectUrl(null, $refTypeName);
			}
			$this->request->data = $check;
		}
		$listType = $this->Check->getListCheckTypes();
		$listParent = $this->Check->getLogicalChecksList($refType, $refId);
		$listCondition = $this->_getListCondition();
		$this->Check->createValidationRules(
			$this->request->data('Check.type'),
			$this->request->data('Check.condition')
		);
		$pageHeader = __('Editing check');

		$this->set(compact('breadCrumbs', 'fullName', 'refType', 'refId',
			'listType', 'listParent', 'listCondition', 'pageHeader'));
	}

/**
 * Action `edit`. Used to edit information about check.
 *  User role - administrator.
 *
 * @param int|string $id ID of record for editing
 * @return void
 */
	public function admin_edit($id = null) {
		$this->_edit($id);
	}

/**
 * Base of action `delete`. Used to delete check.
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
 * Action `delete`. Used to delete check.
 *  User role - administrator.
 *
 * @param int|string $id ID of record for deleting
 * @return void
 */
	public function admin_delete($id = null) {
		$this->_delete($id);
	}

/**
 * Action `move`. Used to move check to new position.
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
 * Action `move`. Used to move check to new position.
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
 * Action `drop`. Used to drag and drop check to new position
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
 * Action `drop`. Used to drag and drop check to new position
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
 * @param int|string $refType ID type of object checks
 * @param int|string $refId Record ID of the node checks
 * @return void
 */
	protected function _verify($refType = null, $refId = null) {
		$this->VerifyData->actionVerify($refType, $refId);
	}

/**
 * Action `verify`. Used to verify state list of package actions.
 *  User role - administrator.
 *
 * @param int|string $refType ID type of object checks
 * @param int|string $refId Record ID of the node checks
 * @return void
 */
	public function admin_verify($refType = null, $refId = null) {
		$this->_verify($refType, $refId);
	}

/**
 * Base of action `recover`. Used to recover state list of
 *  package actions.
 *
 * @param int|string $refType ID type of object checks
 * @param int|string $refId Record ID of the node checks
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
 * @param int|string $refType ID type of object checks
 * @param int|string $refId Record ID of the node checks
 * @return void
 */
	public function admin_recover($refType = null, $refId = null) {
		$this->_recover($refType, $refId);
	}

/**
 * Processing named parameters from request.
 *  Used to set values for check parent ID, check type and 
 *  condition type in form fields.
 *
 * @return array List of check conditions
 */
	protected function _getListCondition() {
		$argParent = $this->request->param('named.parent');
		if (empty($argParent)) {
			$argParent = $this->request->data('Check.parent_id');
		}

		$argCond = $this->request->param('named.cond');
		if (empty($argCond)) {
			$argCond = $this->request->data('Check.condition');
		}
		if (!empty($argCond)) {
			$checkTypeCond = (int)$argCond;
		} else {
			$checkTypeCond = -1;
		}

		$argType = $this->request->param('named.type');
		if (empty($argType)) {
			$argType = $this->request->data('Check.type');
		}
		if (!empty($argType)) {
			$strCheckType = constValToLcSingle('CHECK_TYPE_', $argType, true);
		} else {
			$strCheckType = 'uninstall';
		}

		// The selected check type in the type combo box
		// @codingStandardsIgnoreStart
		$checkType = @constant('CHECK_TYPE_' . strtoupper($strCheckType));
		// @codingStandardsIgnoreEnd
		if (empty($checkType)) {
			$checkType = CHECK_TYPE_UNINSTALL;
		}

		$listCondition = $this->Check->getListCheckConditions($strCheckType);
		// Bounds checking on the selected check condition
		$keys = array_keys($listCondition);
		if ($checkTypeCond < min($keys) || $checkTypeCond > max($keys)) {
			$checkTypeCond = min($keys); // default check condition if invalid one passed in
		}
		$this->request->data('Check.parent_id', $argParent);
		$this->request->data('Check.type', $checkType);
		$this->request->data('Check.condition', $checkTypeCond);

		return $listCondition;
	}

}
