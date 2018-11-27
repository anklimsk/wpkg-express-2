<?php
/**
 * This file is the controller file of the application. Used for
 *  management information about the categories of WPI.
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
 * The controller is used for management information about the categories of WPI.
 *
 * This controller allows to perform the following operations:
 *  - to view, edit and delete categories of WPI.
 *
 * @package app.Controller
 */
class WpiCategoriesController extends AppController {

/**
 * The name of this controller. Controller names are plural, named after the model they manipulate.
 *
 * @var string
 * @link http://book.cakephp.org/2.0/en/controllers.html#controller-attributes
 */
	public $name = 'WpiCategories';

/**
 * Array containing the names of components this controller uses. Component names
 * should not contain the "Component" portion of the class name.
 *
 * @var array
 * @link http://book.cakephp.org/2.0/en/controllers/components.html
 */
	public $components = [
		'Paginator',
		'ChangeState' => ['TargetModel' => 'WpiCategory'],
	];

/**
 * An array containing the class names of models this controller uses.
 *
 * @var mixed
 * @link http://book.cakephp.org/2.0/en/controllers.html#components-helpers-and-uses
 */
	public $uses = [
		'WpiCategory',
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
			'WpiCategory.id',
			'WpiCategory.name',
			'WpiCategory.builtin',
		],
		'order' => [
			'WpiCategory.name' => 'asc',
		],
		'recursive' => -1
	];

/**
 * Base of action `index`. Used to view a full list of categories of WPI.
 *
 * @return void
 */
	protected function _index() {
		$this->view = 'index';
		$this->Paginator->settings = $this->paginate;
		$WPIcategories = $this->Paginator->paginate('WpiCategory');
		if (empty($WPIcategories)) {
			$this->Flash->information(__('WPI categories not found'));
		}
		$breadCrumbs = $this->WpiCategory->getBreadcrumbInfo();
		$breadCrumbs[] = __('Index');
		$pageHeader = __('Index of WPI categories');
		$this->ViewExtension->setRedirectUrl(true, 'wpi');

		$this->set(compact('WPIcategories', 'breadCrumbs', 'pageHeader'));
	}

/**
 * Action `index`. Used to view a full list of categories of WPI.
 *  User role - administrator.
 *
 * @return void
 */
	public function admin_index() {
		$this->_index();
	}

/**
 * Base of action `add`. Used to add category of WPI.
 *
 * POST Data:
 *  - WpiCategory: array data of category of WPI
 *
 * @return void
 */
	protected function _add() {
		$this->view = 'add';
		$breadCrumbs = $this->WpiCategory->getBreadcrumbInfo();
		$breadCrumbs[] = __('Adding');
		if ($this->request->is('post')) {
			$this->WpiCategory->create();
			if ($this->WpiCategory->save($this->request->data)) {
				$this->Flash->success(__('WPI category has been saved.'));

				return $this->ViewExtension->redirectByUrl(null, 'wpi');
			} else {
				$this->Flash->error(__('WPI category could not be saved. Please, try again.'));
			}
		} else {
			$wpiCategory = [
				'WpiCategory' => [
					'builtin' => false,
				]
			];
			$this->request->data = $wpiCategory;
			$this->ViewExtension->setRedirectUrl(null, 'wpi');
		}
		$pageHeader = __('Adding WPI category');

		$this->set(compact('breadCrumbs', 'pageHeader'));
	}

/**
 * Action `add`. Used to add category of WPI.
 *  User role - administrator.
 *
 * @return void
 */
	public function admin_add() {
		$this->_add();
	}

/**
 * Base of action `edit`. Used to edit information about category of WPI.
 *
 * POST Data:
 *  - WpiCategory: array data of category of WPI
 *
 * @param int|string $id ID of record for editing
 * @throws NotFoundException if record for parameter $id was not found
 * @return void
 */
	protected function _edit($id = null) {
		$this->view = 'edit';
		$wpiCategory = $this->WpiCategory->get($id, false);
		if (empty($wpiCategory)) {
			return $this->ViewExtension->setExceptionMessage(new NotFoundException(__('Invalid ID for WPI category')));
		}

		$breadCrumbs = $this->WpiCategory->getBreadcrumbInfo($id);
		$breadCrumbs[] = __('Editing');
		if ($this->request->is(['post', 'put'])) {
			if ($this->WpiCategory->save($this->request->data)) {
				$this->Flash->success(__('WPI category has been saved.'));

				return $this->ViewExtension->redirectByUrl(null, 'wpi');
			} else {
				$this->Flash->error(__('WPI category could not be saved. Please, try again.'));
			}
		} else {
			$this->ViewExtension->setRedirectUrl(null, 'wpi');
			$this->request->data = $wpiCategory;
		}
		$pageHeader = __('Editing WPI category');
		$headerMenuActions = [
			[
				'far fa-trash-alt',
				__('Delete WPI category'),
				['controller' => 'wpi_categories', 'action' => 'delete', $wpiCategory['WpiCategory']['id']],
				[
					'title' => __('Delete WPI category'), 'action-type' => 'confirm-post',
					'data-confirm-msg' => __('Are you sure you wish to delete this WPI category?'),
				]
			]
		];

		$this->set(compact('breadCrumbs', 'pageHeader', 'headerMenuActions'));
	}

/**
 * Action `edit`. Used to edit information about category of WPI.
 *  User role - administrator.
 *
 * @param int|string $id ID of record for editing
 * @return void
 */
	public function admin_edit($id = null) {
		$this->_edit($id);
	}

/**
 * Base of action `delete`. Used to delete category of WPI.
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
 * Action `delete`. Used to delete category of WPI.
 *  User role - administrator.
 *
 * @param int|string $id ID of record for deleting
 * @return void
 */
	public function admin_delete($id = null) {
		$this->_delete($id);
	}

}
