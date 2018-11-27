<?php
/**
 * This file is the controller file of the application. Used to management
 *  removed information.
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
 * The controller is used to management removed information.
 *
 * This controller allows to perform the following operations:
 *  - to view, delete, and restore information;
 *  - preview and download XML file with removed information;
 *  - clear all garbage.
 *
 * @package app.Controller
 */
class GarbageController extends AppController {

/**
 * The name of this controller. Controller names are plural, named after the model they manipulate.
 *
 * @var string
 * @link http://book.cakephp.org/2.0/en/controllers.html#controller-attributes
 */
	public $name = 'Garbage';

/**
 * Array containing the names of components this controller uses. Component names
 * should not contain the "Component" portion of the class name.
 *
 * @var array
 * @link http://book.cakephp.org/2.0/en/controllers/components.html
 */
	public $components = [
		'Paginator',
		'CakeTheme.Filter',
		'ViewData' => ['TargetModel' => 'Garbage'],
		'ExportData' => ['TargetModel' => 'Garbage'],
		'ChangeState' => ['TargetModel' => 'Garbage'],
	];

/**
 * An array containing the names of helpers this controller uses. The array elements should
 * not contain the "Helper" part of the class name.
 *
 * @var mixed
 * @link http://book.cakephp.org/2.0/en/controllers.html#components-helpers-and-uses
 */
	public $helpers = [
		'GeshiExt',
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
			'Garbage.id',
			'Garbage.ref_type',
			'Garbage.ref_id',
			'Garbage.name',
			'Garbage.modified'
		],
		'order' => [
			'Garbage.modified' => 'desc'
		],
		'contain' => [
			'GarbageType',
		]
	];

/**
 * Base of action `index`. Used to view a full list of removed information.
 *
 * @return void
 */
	protected function _index() {
		$groupActions = [
			'group-data-del' => __('Delete selected items'),
		];
		$this->ViewData->actionIndex(null, $groupActions);
		$pageHeader = __('Recycle bin');
		$headerMenuActions = [
			[
				'far fa-trash-alt',
				__('Clear recycle bin'),
				['controller' => 'garbage', 'action' => 'clear'],
				[
					'title' => __('Clear recycle bin'), 'action-type' => 'confirm-post',
					'data-confirm-msg' => __('Are you sure you wish to clear recycle bin?'),
				]
			],
		];
		$listTypes = $this->Garbage->GarbageType->getListGarbageTypes();

		$this->set(compact('pageHeader', 'headerMenuActions', 'listTypes'));
	}

/**
 * Action `index`. Used to view a full list of removed information.
 *  User role - administrator.
 *
 * @return void
 */
	public function admin_index() {
		$this->_index();
	}

/**
 * Base of action `preview`. Used to preview XML information.
 *
 * @param int|string $id ID of record for previewing.
 * @return void
 */
	protected function _preview($id = null) {
		$this->view = 'preview';
		$this->ExportData->preview($id);
		$headerMenuActions = $this->viewVars['headerMenuActions'];
		$headerMenuActions[] = 'divider';
		$headerMenuActions[] = [
			'fas fa-undo-alt',
			__('Restore data'),
			['controller' => 'garbage', 'action' => 'restore', $id],
			[
				'title' => __('Restore this data from recycle bin'),
				'action-type' => 'confirm-post',
				'data-confirm-msg' => __('Are you sure you wish to restore this data from recycle bin?'),
			]
		];
		$headerMenuActions[] = [
			'fas fa-trash-alt',
			__('Delete data'),
			['controller' => 'garbage', 'action' => 'delete', $id],
			[
				'title' => __('Delete data'), 'action-type' => 'confirm-post',
				'data-confirm-msg' => __('Are you sure you wish to delete this data from recycle bin?'),
			]
		];

		$this->set(compact('headerMenuActions'));
	}

/**
 * Action `preview`. Used to preview XML information.
 *  User role - administrator.
 *
 * @param int|string $id ID of record for viewing
 * @return void
 */
	public function admin_preview($id = null) {
		$this->_preview($id);
	}

/**
 * Base of action `delete`. Used to delete garbage.
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
 * Action `delete`. Used to delete garbage.
 *  User role - administrator.
 *
 * @param int|string $id ID of record for deleting
 * @return void
 */
	public function admin_delete($id = null) {
		$this->_delete($id);
	}

/**
 * Base of action `clear`. Used to clear garbage.
 *
 * @throws NotFoundException if record for parameter $id was not found
 * @throws MethodNotAllowedException if request is not `POST` or `DELETE`
 * @return void
 */
	protected function _clear() {
		$this->request->allowMethod('post', 'delete');
		$this->ViewExtension->setRedirectUrl(null, 'garbage');
		if ($this->Garbage->clearData()) {
			$this->Flash->success(__('The recycle bin has been cleared.'));
		} else {
			$this->Flash->error(__('The recycle bin could not be cleared. Please, try again.'));
		}

		return $this->ViewExtension->redirectByUrl(null, 'garbage');
	}

/**
 * Action `clear`. Used to clear garbage.
 *  User role - administrator.
 *
 * @return void
 */
	public function admin_clear() {
		$this->_clear();
	}

/**
 * Base of action `restore`. Used to restore garbage.
 *
 * @param int|string $id ID of record to restore.
 * @throws MethodNotAllowedException if request is not `POST` or `DELETE`
 * @return void
 */
	protected function _restore($id = null) {
		$this->request->allowMethod('post');
		$this->ViewExtension->setRedirectUrl(null, 'garbage');
		if ($this->Garbage->restoreData($id, true)) {
			$this->Flash->success(__('The information has been restored from recycle bin successfully.'));
		} else {
			$this->Flash->error(__('The information could not be restored. Please, try again.'));
		}

		return $this->ViewExtension->redirectByUrl(null, 'garbage');
	}

/**
 * Action `restore`. Used to restore garbage.
 *  User role - administrator.
 *
 * @param int|string $id ID of record to restore.
 * @return void
 */
	public function admin_restore($id = null) {
		$this->_restore($id);
	}

/**
 * Base of action `download`. Used to download XML file
 *  of package.
 *
 * @param int|string $id ID of record for download
 * @return void
 */
	protected function _download($id = null) {
		$this->ExportData->download($id);
	}

/**
 * Action `download`. Used to download XML file
 *  of package.
 *  User role - administrator.
 *
 * @param int|string $id ID of record for download
 * @return void
 */
	public function admin_download($id = null) {
		$this->_download($id);
	}
}
