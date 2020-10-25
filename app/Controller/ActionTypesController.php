<?php
/**
 * This file is the controller file of the application. Used for
 *  management information about the package action types.
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
 * The controller is used for management information about the
 *  package action types.
 *
 * This controller allows to perform the following operations:
 *  - to view, edit and delete package action types.
 *
 * @package app.Controller
 */
class ActionTypesController extends AppController {

/**
 * The name of this controller. Controller names are plural, named after the model they manipulate.
 *
 * @var string
 * @link http://book.cakephp.org/2.0/en/controllers.html#controller-attributes
 */
	public $name = 'ActionTypes';

/**
 * Array containing the names of components this controller uses. Component names
 * should not contain the "Component" portion of the class name.
 *
 * @var array
 * @link http://book.cakephp.org/2.0/en/controllers/components.html
 */
	public $components = [
		'Paginator',
		'BookmarkTable' => ['TargetModel' => 'PackageActionType'],
		'ChangeState' => ['TargetModel' => 'PackageActionType'],
	];

/**
 * An array containing the class names of models this controller uses.
 *
 * @var mixed
 * @link http://book.cakephp.org/2.0/en/controllers.html#components-helpers-and-uses
 */
	public $uses = [
		'PackageActionType',
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
			'PackageActionType.id',
			'PackageActionType.builtin',
			'PackageActionType.name',
			'PackageActionType.command',
		],
		'order' => [
			'PackageActionType.name' => 'asc'
		],
		'recursive' => -1
	];

/**
 * Base of action `index`. Used to view a full list of package
 *  action types.
 *
 * @return void
 */
	protected function _index() {
		$this->view = 'index';
		$this->BookmarkTable->restoreBookmark();
		$this->Paginator->settings = $this->paginate;
		try {
			$actionTypes = $this->Paginator->paginate('PackageActionType');
		} catch (Exception $e) {
			$this->BookmarkTable->clearBookmark();
			return $this->ViewExtension->setExceptionMessage($e);
		}
		$this->BookmarkTable->storeBookmark();
		if (empty($actionTypes)) {
			$this->Flash->information(__('Package action types not found'));
		}
		$breadCrumbs = $this->PackageActionType->getBreadcrumbInfo();
		$breadCrumbs[] = __('Index');
		$pageHeader = __('Index of types of package actions');
		$this->ViewExtension->setRedirectUrl(true, 'package');

		$this->set(compact('actionTypes', 'breadCrumbs', 'pageHeader'));
	}

/**
 * Action `index`. Used to view a full list of package
 *  action types.
 *  User role - administrator.
 *
 * @return void
 */
	public function admin_index() {
		$this->_index();
	}

/**
 * Base of action `add`. Used to add package action type.
 *
 * POST Data:
 *  - PackageActionType: array data of package action type
 *
 * @return void
 */
	protected function _add() {
		$this->view = 'add';
		$breadCrumbs = $this->PackageActionType->getBreadcrumbInfo();
		$breadCrumbs[] = __('Adding');
		if ($this->request->is('post')) {
			$this->PackageActionType->create();
			if ($this->PackageActionType->save($this->request->data)) {
				$this->Flash->success(__('Package action type has been saved.'));

				return $this->ViewExtension->redirectByUrl(null, 'package');
			} else {
				$this->Flash->error(__('Package action type could not be saved. Please, try again.'));
			}
		} else {
			$this->request->data = $this->PackageActionType->getDefaultValues();
			$this->ViewExtension->setRedirectUrl(null, 'package');
		}
		$pageHeader = __('Adding package action type');

		$this->set(compact('breadCrumbs', 'pageHeader'));
	}

/**
 * Action `add`. Used to add package action type.
 *  User role - administrator.
 *
 * @return void
 */
	public function admin_add() {
		$this->_add();
	}

/**
 * Base of action `edit`. Used to edit information about package
 *  action type.
 *
 * POST Data:
 *  - PackageActionType: array data of package action type
 *
 * @param int|string $id ID of record for editing
 * @throws NotFoundException if record for parameter $id was not found
 * @return void
 */
	protected function _edit($id = null) {
		$this->view = 'edit';
		$packageActionType = $this->PackageActionType->get($id);
		if (empty($packageActionType)) {
			return $this->ViewExtension->setExceptionMessage(new NotFoundException(__('Invalid ID for package action type')));
		}

		$breadCrumbs = $this->PackageActionType->getBreadcrumbInfo($id);
		$breadCrumbs[] = __('Editing');
		if ($this->request->is(['post', 'put'])) {
			if ($this->PackageActionType->save($this->request->data)) {
				$this->Flash->success(__('Package action type has been saved.'));

				return $this->ViewExtension->redirectByUrl(null, 'package');
			} else {
				$this->Flash->error(__('Package action type could not be saved. Please, try again.'));
			}
		} else {
			$this->ViewExtension->setRedirectUrl(null, 'package');
			$this->request->data = $packageActionType;
		}
		$pageHeader = __('Editing package action type');
		$headerMenuActions = [
			[
				'far fa-trash-alt',
				__('Delete package action type'),
				['controller' => 'action_types', 'action' => 'delete', $packageActionType['PackageActionType']['id']],
				[
					'title' => __('Delete package action type'), 'action-type' => 'confirm-post',
					'data-confirm-msg' => __('Are you sure you wish to delete this package action type?'),
				]
			]
		];

		$this->set(compact('breadCrumbs', 'pageHeader', 'headerMenuActions'));
	}

/**
 * Action `edit`. Used to edit information about package
 *  action type.
 *  User role - administrator.
 *
 * @param int|string $id ID of record for editing
 * @return void
 */
	public function admin_edit($id = null) {
		$this->_edit($id);
	}

/**
 * Base of action `delete`. Used to delete package action type.
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
 * Action `delete`. Used to delete package action type.
 *  User role - administrator.
 *
 * @param int|string $id ID of record for deleting
 * @return void
 */
	public function admin_delete($id = null) {
		$this->_delete($id);
	}

}
