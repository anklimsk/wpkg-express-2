<?php
/**
 * This file is the controller file of the application. Used for
 *  management information about the packages archive.
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
 * The controller is used for management information about the packages archive.
 *
 * This controller allows to perform the following operations:
 *  - to view, delete, store and restore package archive;
 *  - preview and download XML file of package;
 *  - clear packages archive.
 *
 * @package app.Controller
 */
class ArchivesController extends AppController {

/**
 * The name of this controller. Controller names are plural, named after the model they manipulate.
 *
 * @var string
 * @link http://book.cakephp.org/2.0/en/controllers.html#controller-attributes
 */
	public $name = 'Archives';

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
		'ViewData' => ['TargetModel' => 'Archive'],
		'ExportData' => ['TargetModel' => 'Archive'],
		'ChangeState' => ['TargetModel' => 'Archive'],
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
			'Archive.id',
			'Archive.ref_type',
			'Archive.ref_id',
			'Archive.revision',
			'Archive.name',
			'Archive.modified'
		],
		'order' => [
			'Archive.name' => 'asc',
			'Archive.revision' => 'desc'
		],
		'contain' => [
			'Package',
		]
	];

/**
 * Base of action `index`. Used to view a full list of packages archive.
 *
 * @return void
 */
	protected function _index() {
		$groupActions = [
			'group-data-del' => __('Delete selected items'),
		];
		$this->ViewData->actionIndex(null, $groupActions);
		$headerMenuActions = [
			[
				'far fa-trash-alt',
				__('Clear archive'),
				['controller' => 'archives', 'action' => 'clear'],
				[
					'title' => __('Clear archive'), 'action-type' => 'confirm-post',
					'data-confirm-msg' => __('Are you sure you wish to clear archive?'),
				]
			],
		];

		$this->set(compact('headerMenuActions'));
	}

/**
 * Action `index`. Used to view a full list of packages archive.
 *  User role - administrator.
 *
 * @return void
 */
	public function admin_index() {
		$this->_index();
		$pageHeader = __('Package archive');

		$this->set(compact('pageHeader'));
	}

/**
 * Base of action `view`. Used to view information about package archive.
 *
 * @param int|string $id ID of record for viewing.
 * @throws NotFoundException if record for parameter $id was not found
 * @return void
 */
	protected function _view($id = null) {
		$refType = GARBAGE_TYPE_PACKAGE;
		$fullName = $this->Archive->getFullName(null, $refType, null, $id);
		if (empty($fullName)) {
			return $this->ViewExtension->setExceptionMessage(new NotFoundException(__('Invalid ID for package')));
		}
		$defaultConditions = [
			'Archive.ref_id' => $id,
			'Archive.ref_type' => $refType
		];
		$groupActions = [
			'group-data-del' => __('Delete selected items'),
		];
		$this->paginate['order'] = ['Archive.revision' => 'desc'];
		$this->ViewData->actionIndex($defaultConditions, $groupActions);
		$this->view = 'view';
		$breadCrumbs = $this->Archive->getBreadcrumbInfo(null, null, null, $id);
		$breadCrumbs[] = __('Viewing');
		$pageHeader = __('Information of package archive');
		$headerMenuActions = [
			[
				'fas fa-plus',
				__('Add to archive'),
				['controller' => 'archives', 'action' => 'add', $id],
				[
					'title' => __('Add current revision of package to archive'),
					'data-toggle' => 'ajax'
				]
			],
			[
				'far fa-trash-alt',
				__('Clear archive'),
				['controller' => 'archives', 'action' => 'clear', $id],
				[
					'title' => __('Clear archive'), 'action-type' => 'confirm-post',
					'data-confirm-msg' => __('Are you sure you wish to clear archive of this package?'),
				]
			],
		];

		$this->set(compact('breadCrumbs', 'pageHeader', 'headerMenuActions', 'fullName'));
	}

/**
 * Action `view`. Used to view information about package archive.
 * User role - administrator.
 *
 * @param int|string $id ID of record for viewing
 * @return void
 */
	public function admin_view($id = null) {
		$this->_view($id);
	}

/**
 * Base of action `preview`. Used to preview XML information of package.
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
			__('Restore package'),
			['controller' => 'archives', 'action' => 'restore', $id],
			[
				'title' => __('Restore package from archive'),
				'action-type' => 'confirm-post',
				'data-confirm-msg' => __('Are you sure you wish to restore this package from archive?'),
			]
		];
		$headerMenuActions[] = [
			'fas fa-trash-alt',
			__('Delete package'),
			['controller' => 'archives', 'action' => 'delete', $id],
			[
				'title' => __('Delete package'), 'action-type' => 'confirm-post',
				'data-confirm-msg' => __('Are you sure you wish to delete this package from archive?'),
			]
		];

		$this->set(compact('headerMenuActions'));
	}

/**
 * Action `preview`. Used to preview XML information of package.
 * User role - administrator.
 *
 * @param int|string $id ID of record for previewing
 * @return void
 */
	public function admin_preview($id = null) {
		$this->_preview($id);
	}

/**
 * Base of action `add`. Used for add package to archive.
 *
 * @param int|string $id ID of the package record for adding.
 * @return void
 */
	protected function _add($id = null) {
		$this->view = 'add';
		$this->ViewExtension->setRedirectUrl(null, 'archive');
		$result = $this->Archive->addPackage($id);
		if ($result) {
			$this->Flash->success(__('The package has been added to archive.'));
		} elseif ($result === null) {
			$this->Flash->warning(__('This package already added to archive.'));
		} else {
			$this->Flash->error(__('The package could not be added to archive. Please, try again.'));
		}

		return $this->ViewExtension->redirectByUrl(null, 'archive');
	}

/**
 * Action `add`. Used to add archive.
 *  User role - administrator.
 *
 * @param int|string $id ID of the package record for adding.
 * @return void
 */
	public function admin_add($id = null) {
		$this->_add($id);
	}

/**
 * Base of action `delete`. Used to remove package from archive.
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
 * Action `delete`. Used to remove package from archive.
 *  User role - administrator.
 *
 * @param int|string $id ID of record for deleting
 * @return void
 */
	public function admin_delete($id = null) {
		$this->_delete($id);
	}

/**
 * Base of action `clear`. Used to clear archive.
 *
 * @param int|string $id ID of the package record for clear archive
 * @throws NotFoundException if record for parameter $id was not found
 * @throws MethodNotAllowedException if request is not `POST` or `DELETE`
 * @return void
 */
	protected function _clear($id = null) {
		$this->request->allowMethod('post', 'delete');
		$this->ViewExtension->setRedirectUrl(null, 'archive');
		if ($this->Archive->clearArchive($id)) {
			$this->Flash->success(__('The archive of packages has been cleared.'));
		} else {
			$this->Flash->error(__('The archive of packages could not be cleared. Please, try again.'));
		}

		return $this->ViewExtension->redirectByUrl(null, 'archive');
	}

/**
 * Action `clear`. Used to clear archive.
 *  User role - administrator.
 *
 * @param int|string $id ID of the package record for clear archive
 * @return void
 */
	public function admin_clear($id = null) {
		$this->_clear($id);
	}

/**
 * Base of action `restore`. Used to restore package from archive.
 *
 * @param int|string $id ID of record for deleting
 * @throws NotFoundException if record for parameter $id was not found
 * @throws MethodNotAllowedException if request is not `POST` or `DELETE`
 * @return void
 */
	protected function _restore($id = null) {
		$this->request->allowMethod('post');
		$this->ViewExtension->setRedirectUrl(null, 'archive');
		if ($this->Archive->restoreData($id, false)) {
			$this->Flash->success(__('The information of package has been extracted from archive successfully.'));
		} else {
			$this->Flash->error(__('Package information could not be restored from archive. Please try again.'));
		}

		return $this->ViewExtension->redirectByUrl(null, 'archive');
	}

/**
 * Action `restore`. Used to restore package from archive.
 *  User role - administrator.
 *
 * @param int|string $id ID of record for deleting
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
