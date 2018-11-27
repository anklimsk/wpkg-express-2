<?php
/**
 * This file is the controller file of the application. Used for
 *  management information about the configuration of WPI.
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
 * The controller is used for management information about the configuration of WPI.
 *
 * This controller allows to perform the following operations:
 *  - to veiw, edit and delete packages for configuration of WPI;
 *  - download files of configuration WPI and extensions for WPI.
 *
 * @package app.Controller
 */
class WpiController extends AppController {

/**
 * The name of this controller. Controller names are plural, named after the model they manipulate.
 *
 * @var string
 * @link http://book.cakephp.org/2.0/en/controllers.html#controller-Wpi
 */
	public $name = 'Wpi';

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
		'ViewData' => ['TargetModel' => 'Wpi'],
		'ChangeState' => ['TargetModel' => 'Wpi'],
	];

/**
 * An array containing the names of helpers this controller uses. The array elements should
 * not contain the "Helper" part of the class name.
 *
 * @var mixed
 * @link http://book.cakephp.org/2.0/en/controllers.html#components-helpers-and-uses
 */
	public $helpers = [
		'Cache',
		'WpiJs',
	];

/**
 * An array containing the class names of models this controller uses.
 *
 * @var mixed
 * @link http://book.cakephp.org/2.0/en/controllers.html#components-helpers-and-uses
 */
	public $uses = [
		'Wpi',
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
			'Wpi.id',
			'Wpi.package_id',
			'Wpi.category_id',
			'Wpi.default',
			'Wpi.force',
		],
		'order' => [
			'Package.id_text' => 'asc'
		],
		'contain' => ['Package', 'WpiCategory']
	];

/**
 * Used to define methods a controller that will be cached.
 *
 * @var mixed
 * @link https://book.cakephp.org/2.0/en/core-libraries/helpers/cache.html#additional-configuration-options
 */
	public $cacheAction = [
		'config' => [
			'callbacks' => true,
			'duration' => WEEK
		]
	];

/**
 * Base of action `index`. Used to view a full list of packages for 
 *  configuration of WPI.
 *
 * @return void
 */
	protected function _index() {
		$groupActions = [
			'group-data-del' => __('Delete selected items'),
		];
		$this->ViewData->actionIndex(null, $groupActions);
		$pageHeader = __('Index of WPI packages');
		$headerMenuActions = [
			[
				'fas fa-plus',
				__('Add package WPI'),
				['controller' => 'wpi', 'action' => 'add'],
				[
					'title' => __('Add package WPI'),
					'data-toggle' => 'modal'
				]
			],
			'divider',
			[
				'fas fa-list',
				__('Categories'),
				['controller' => 'wpi_categories', 'action' => 'index', 'plugin' => null],
				[
					'title' => __('Categories of packages'),
					'data-toggle' => 'modal'
				]
			],
			'divider',
			[
				'fas fa-file-download',
				__('Configuration of WPI'),
				['controller' => 'wpi', 'action' => 'download', 'config', 'ext' => 'js'],
				[
					'title' => __('Download WPI configuration file'),
				]
			],
			'divider',
			[
				'fas fa-file-archive',
				__('Extension for WPI'),
				['controller' => 'wpi', 'action' => 'download', 'extension', 'ext' => 'zip'],
				[
					'title' => __('Download extension for WPI'),
				]
			],
		];
		$listWpiCategories = $this->Wpi->WpiCategory->getList();

		$this->set(compact('pageHeader', 'headerMenuActions', 'listWpiCategories'));
	}

/**
 * Action `index`. Used to view a full list of packages for 
 *  configuration of WPI.
 *  User role - administrator.
 *
 * @return void
 */
	public function admin_index() {
		$this->_index();
	}

/**
 * Base of action `view`. Used to view information about package for WPI.
 *
 * @param int|string $id ID of record for viewing.
 * @throws NotFoundException if record for parameter $id was not found
 * @return void
 */
	protected function _view($id = null) {
		$this->view = 'view';
		$wpiPackage = $this->Wpi->get($id, true);
		if (empty($wpiPackage)) {
			return $this->ViewExtension->setExceptionMessage(new NotFoundException(__('Invalid ID for WPI package')));
		}
		$breadCrumbs = $this->Wpi->getBreadcrumbInfo($id);
		$breadCrumbs[] = __('Viewing');
		$this->ViewExtension->setRedirectUrl(true, 'wpi');
		$specificJS = 'add';
		$pageHeader = __('Information of WPI package');
		$headerMenuActions = [
			[
				'fas fa-pencil-alt',
				__('Edit WPI package'),
				['controller' => 'wpi', 'action' => 'edit', $id],
				[
					'title' => __('Edit WPI package'),
					'action-type' => 'modal',
				]
			],
			[
				'far fa-trash-alt',
				__('Delete WPI package'),
				['controller' => 'wpi', 'action' => 'delete', $id],
				[
					'title' => __('Delete WPI package'), 'action-type' => 'confirm-post',
					'data-confirm-msg' => __('Are you sure you wish to delete this WPI package?'),
				]
			]
		];

		$this->set(compact('wpiPackage', 'breadCrumbs',
			'pageHeader', 'headerMenuActions'));
	}

/**
 * Action `view`. Used to view information about package for WPI.
 * User role - administrator.
 *
 * @param int|string $id ID of record for viewing
 * @return void
 */
	public function admin_view($id = null) {
		$this->_view($id);
	}

/**
 * Base of action `config`. Used to export a JS file of configuration WPI.
 *
 * @param bool $download Flag of send header for download
 * @throws BadRequestException if request is not JS
 * @return void
 */
	protected function _config($download = false) {
		$this->view = 'config';
		$this->layout = 'wpi';
		if (!$this->RequestHandler->prefers('js')) {
			throw new BadRequestException();
		}

		$exportDisable = $this->Setting->getConfig('ExportDisable');
		$exportNotes = $this->Setting->getConfig('ExportNotes');
		$exportDisabled = $this->Setting->getConfig('ExportDisabled');
		$jsDataArray = $this->Wpi->getJSdata(null, $exportDisable, $exportNotes, $exportDisabled);
		$this->set(compact('jsDataArray'));
		if ($download) {
			$this->response->disableCache();
			$this->RequestHandler->renderAs($this, 'js', array('attachment' => 'config.js'));
		} else {
			$this->cacheAction = [
				$this->view => [
					'callbacks' => true,
					'duration' => WEEK
				]
			];
		}
	}

/**
 * Action `config`. Used to export a JS file of configuration WPI.
 *  User role - user.
 *
 * @return void
 */
	public function config() {
		$this->_config(false);
	}

/**
 * Base of action `add`. Used to add package for WPI.
 *
 * POST Data:
 *  - Wpi: array data of package for WPI
 *
 * @return void
 */
	protected function _add() {
		$listPackages = $this->Wpi->getListPackagesForWPI();
		if (empty($listPackages)) {
			return $this->ViewExtension->setExceptionMessage(new InternalErrorException(__('There are no packages to add/remove')));
		}

		$listWpiCategories = $this->Wpi->WpiCategory->getList();
		if (empty($listWpiCategories)) {
			return $this->ViewExtension->setExceptionMessage(new InternalErrorException(__('List of categories of WPI is empty')));
		}

		$this->view = 'add';
		$breadCrumbs = $this->Wpi->getBreadcrumbInfo();
		$breadCrumbs[] = __('Adding');
		if ($this->request->is('post')) {
			$this->Wpi->create();
			if ($this->Wpi->save($this->request->data)) {
				$this->Flash->success(__('WPI package has been saved.'));

				return $this->ViewExtension->redirectByUrl(null, 'wpi');
			} else {
				$this->Flash->error(__('WPI package could not be saved. Please, try again.'));
			}
		} else {
			$wpi = [
				'Wpi' => [
					'category_id' => null,
					'default' => false,
					'force' => false,
				]
			];
			$this->request->data = $wpi;
			$this->ViewExtension->setRedirectUrl(null, 'wpi');
		}
		$pageHeader = __('Adding WPI package');

		$this->set(compact('breadCrumbs', 'pageHeader', 'listWpiCategories', 'listPackages'));
	}

/**
 * Action `add`. Used to add package for WPI.
 *  User role - administrator.
 *
 * @return void
 */
	public function admin_add() {
		$this->_add();
	}

/**
 * Base of action `edit`. Used to edit information about package for WPI.
 *
 * POST Data:
 *  - Wpi: array data of package for WPI
 *
 * @param int|string $id ID of record for editing
 * @throws NotFoundException if record for parameter $id was not found
 * @return void
 */
	protected function _edit($id = null) {
		$this->view = 'edit';
		$wpi = $this->Wpi->get($id, false);
		if (empty($wpi)) {
			return $this->ViewExtension->setExceptionMessage(new NotFoundException(__('Invalid ID for WPI package')));
		}

		$listWpiCategories = $this->Wpi->WpiCategory->getList();
		if (empty($listWpiCategories)) {
			return $this->ViewExtension->setExceptionMessage(new InternalErrorException(__('List of categories of WPI is empty')));
		}

		$breadCrumbs = $this->Wpi->getBreadcrumbInfo($id);
		$breadCrumbs[] = __('Editing');
		if ($this->request->is(['post', 'put'])) {
			if ($this->Wpi->save($this->request->data)) {
				$this->Flash->success(__('WPI package has been saved.'));

				return $this->ViewExtension->redirectByUrl(null, 'package');
			} else {
				$this->Flash->error(__('WPI package could not be saved. Please, try again.'));
			}
		} else {
			$this->ViewExtension->setRedirectUrl(null, 'package');
			$this->request->data = $wpi;
		}
		$pageHeader = __('Editing WPI package');
		$headerMenuActions = [
			[
				'far fa-trash-alt',
				__('Delete WPI package'),
				['controller' => 'wpi', 'action' => 'delete', $wpi['Wpi']['id']],
				[
					'title' => __('Delete WPI package'), 'action-type' => 'confirm-post',
					'data-confirm-msg' => __('Are you sure you wish to delete this WPI package?'),
				]
			]
		];
		$packageId = $this->request->data('Wpi.package_id');
		$packageName = $this->Wpi->Package->getName($packageId);

		$this->set(compact('breadCrumbs', 'pageHeader', 'headerMenuActions', 'listWpiCategories', 'packageName'));
	}

/**
 * Action `edit`. Used to edit information about package for WPI.
 *  User role - administrator.
 *
 * @param int|string $id ID of record for editing
 * @return void
 */
	public function admin_edit($id = null) {
		$this->_edit($id);
	}

/**
 * Base of action `delete`. Used to delete package for WPI.
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
 * Action `delete`. Used to delete package for WPI.
 *  User role - administrator.
 *
 * @param int|string $id ID of record for deleting
 * @return void
 */
	public function admin_delete($id = null) {
		$this->_delete($id);
	}

/**
 * Base of action `default`. Used to set or unset the package
 *  selected by default.
 *
 * @param int|string $id ID of record for set flag
 * @param bool $state State of flag
 * @throws NotFoundException if record for parameter $id was not found
 * @return void
 */
	protected function _default($id = null, $state = false) {
		$stateName = __x('change state', 'selected by default');
		if (!$state) {
			$stateName = __x('change state', 'don\'t selected by default');
		}

		return $this->ChangeState->changeStateField($id, 'default', $state, $stateName, false);
	}

/**
 * Action `default`. Used to set or unset the package
 *  selected by default.
 *  User role - administrator.
 *
 * @param int|string $id ID of record for set flag
 * @param bool $state State of flag
 * @return void
 */
	public function admin_default($id = null, $state = false) {
		$this->_default($id, $state);
	}

/**
 * Base of action `force`. Used to set or unset the package
 *  selected by force.
 *
 * @param int|string $id ID of record for set flag
 * @param bool $state State of flag
 * @throws NotFoundException if record for parameter $id was not found
 * @return void
 */
	protected function _force($id = null, $state = false) {
		$stateName = __x('change state', 'forced selected');
		if (!$state) {
			$stateName = __x('change state', 'don\'t selected forcibly');
		}

		return $this->ChangeState->changeStateField($id, 'force', $state, $stateName, false);
	}

/**
 * Action `force`. Used to set or unset the package
 *  selected by force.
 *  User role - administrator.
 *
 * @param int|string $id ID of record for set flag
 * @param bool $state State of flag
 * @return void
 */
	public function admin_force($id = null, $state = false) {
		$this->_force($id, $state);
	}

/**
 * Base of action `download`. Used to download files for WPI.
 *
 * @param string $type Type of file for download: `config` or `extension`
 * @throws BadRequestException if $type is not `config` or `extension`
 * @return void
 */
	protected function _download($type = null) {
		if (empty($type)) {
			throw new BadRequestException();
		}

		$type = mb_strtolower($type);
		switch ($type) {
			case 'config':
				return $this->_config(true);
				// break;
			case 'extension':
				$filePath = WWW_ROOT . 'files' . DS . 'WPI' . DS . 'WPI-extension.zip';
				$fileName = 'WPI extension.zip';
				$this->response->file($filePath, ['download' => true, 'name' => $fileName]);
				return $this->response;
				// break;
			default:
				throw new BadRequestException();
		}
	}

/**
 * Action `download`. Used to download files for WPI.
 *  User role - administrator.
 *
 * @param string $type Type of file for download: `config` or `extension`
 * @return void
 */
	public function admin_download($type = null) {
		return $this->_download($type);
	}

}
