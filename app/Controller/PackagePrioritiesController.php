<?php
/**
 * This file is the controller file of the application. Used for
 *  management information about the package priorities.
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
 * The controller is used for management information about the package priorities.
 *
 * This controller allows to perform the following operations:
 *  - to view, edit, and delete package priorities.
 *
 * @package app.Controller
 */
class PackagePrioritiesController extends AppController {

/**
 * The name of this controller. Controller names are plural, named after the model they manipulate.
 *
 * @var string
 * @link http://book.cakephp.org/2.0/en/controllers.html#controller-attributes
 */
	public $name = 'PackagePriorities';

/**
 * Array containing the names of components this controller uses. Component names
 * should not contain the "Component" portion of the class name.
 *
 * @var array
 * @link http://book.cakephp.org/2.0/en/controllers/components.html
 */
	public $components = [
		'Paginator',
		'ChangeState' => ['TargetModel' => 'PackagePriority'],
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
			'PackagePriority.id',
			'PackagePriority.name',
			'PackagePriority.value',
		],
		'order' => [
			'PackagePriority.value' => 'asc'
		],
		'recursive' => -1
	];

/**
 * Base of action `index`. Used to view a full list of package priorities.
 *
 * @return void
 */
	protected function _index() {
		$this->view = 'index';
		$this->Paginator->settings = $this->paginate;
		$packagePriorities = $this->Paginator->paginate('PackagePriority');
		if (empty($packagePriorities)) {
			$this->Flash->information(__('Package priorities not found'));
		}
		$breadCrumbs = $this->PackagePriority->getBreadcrumbInfo();
		$breadCrumbs[] = __('Index');
		$pageHeader = __('Index of package priorities');
		$this->ViewExtension->setRedirectUrl(true, 'package');

		$this->set(compact('packagePriorities', 'breadCrumbs', 'pageHeader'));
	}

/**
 * Action `index`. Used to view a full list of package priorities.
 *  User role - administrator.
 *
 * @return void
 */
	public function admin_index() {
		$this->_index();
	}

/**
 * Base of action `add`. Used to add package priority.
 *
 * POST Data:
 *  - PackagePriority: array data of package priority
 *
 * @return void
 */
	protected function _add() {
		$this->view = 'add';
		$breadCrumbs = $this->PackagePriority->getBreadcrumbInfo();
		$breadCrumbs[] = __('Adding');
		if ($this->request->is('post')) {
			$this->PackagePriority->create();
			if ($this->PackagePriority->save($this->request->data)) {
				$this->Flash->success(__('Package priority has been saved.'));

				return $this->ViewExtension->redirectByUrl(null, 'package');
			} else {
				$this->Flash->error(__('Package priority could not be saved. Please, try again.'));
			}
		} else {
			$this->ViewExtension->setRedirectUrl(null, 'package');
		}
		$pageHeader = __('Adding package priority');

		$this->set(compact('breadCrumbs', 'pageHeader'));
	}

/**
 * Action `add`. Used to add package priority.
 *  User role - administrator.
 *
 * @return void
 */
	public function admin_add() {
		$this->_add();
	}

/**
 * Base of action `edit`. Used to edit information about package priority.
 *
 * POST Data:
 *  - PackagePriority: array data of package priority
 *
 * @param int|string $id ID of record for editing
 * @throws NotFoundException if record for parameter $id was not found
 * @return void
 */
	protected function _edit($id = null) {
		$this->view = 'edit';
		$packagePriority = $this->PackagePriority->get($id);
		if (empty($packagePriority)) {
			return $this->ViewExtension->setExceptionMessage(new NotFoundException(__('Invalid ID for package priority')));
		}

		$breadCrumbs = $this->PackagePriority->getBreadcrumbInfo($id);
		$breadCrumbs[] = __('Editing');
		if ($this->request->is(['post', 'put'])) {
			if ($this->PackagePriority->save($this->request->data)) {
				$this->Flash->success(__('Package priority has been saved.'));

				return $this->ViewExtension->redirectByUrl(null, 'package');
			} else {
				$this->Flash->error(__('Package priority could not be saved. Please, try again.'));
			}
		} else {
			$this->ViewExtension->setRedirectUrl(null, 'package');
			$this->request->data = $packagePriority;
		}
		$pageHeader = __('Editing package priority');
		$headerMenuActions = [
			[
				'far fa-trash-alt',
				__('Delete package priority'),
				['controller' => 'action_types', 'action' => 'delete', $packagePriority['PackagePriority']['id']],
				[
					'title' => __('Delete package priority'), 'action-type' => 'confirm-post',
					'data-confirm-msg' => __('Are you sure you wish to delete this package priority?'),
				]
			]
		];

		$this->set(compact('breadCrumbs', 'pageHeader', 'headerMenuActions'));
	}

/**
 * Action `edit`. Used to edit information about package priority.
 *  User role - administrator.
 *
 * @param int|string $id ID of record for editing
 * @return void
 */
	public function admin_edit($id = null) {
		$this->_edit($id);
	}

/**
 * Base of action `delete`. Used to delete package priority.
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
 * Action `delete`. Used to delete package priority.
 *  User role - administrator.
 *
 * @param int|string $id ID of record for deleting
 * @return void
 */
	public function admin_delete($id = null) {
		$this->_delete($id);
	}

}
