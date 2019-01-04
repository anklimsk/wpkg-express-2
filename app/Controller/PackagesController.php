<?php
/**
 * This file is the controller file of the application. Used for
 *  management information about the packages.
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
 * The controller is used for management information about the packages.
 *
 * This controller allows to perform the following operations:
 *  - to view, edit, delete and changing position of package;
 *  - preview and download XML file of package;
 *  - to enable or disable package;
 *  - set or unset the package usage flag as a template;
 *  - to copy package;
 *  - to create new package based on template;
 *  - to edit package inclusion in profiles.
 *
 * @package app.Controller
 */
class PackagesController extends AppController {

/**
 * The name of this controller. Controller names are plural, named after the model they manipulate.
 *
 * @var string
 * @link http://book.cakephp.org/2.0/en/controllers.html#controller-attributes
 */
	public $name = 'Packages';

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
		'ViewData' => ['TargetModel' => 'Package'],
		'ExportData' => ['TargetModel' => 'Package'],
		'ChangeState' => ['TargetModel' => 'Package'],
		'TemplateData' => ['TargetModel' => 'Package'],
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
		'GeshiExt',
		'Tools.Tree',
		'Indicator'
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
			'Package.id',
			'Package.reboot_id',
			'Package.execute_id',
			'Package.notify_id',
			'Package.enabled',
			'Package.template',
			'Package.name',
			'Package.id_text',
			'Package.revision',
			'Package.priority',
			'Package.notes',
			'Package.modified',
		],
		'order' => [
			'Package.priority' => 'desc',
			'Package.id_text' => 'asc'
		],
		'contain' => [
			'PackagePriority',
			'PackageRebootType',
			'PackageExecuteType',
			'PackageNotifyType',
		]
	];

/**
 * Base of action `index`. Used to view a full list of packages.
 *
 * @return void
 */
	protected function _index() {
		$this->ViewData->actionIndex();
		$pageHeader = __('Index of packages');
		$headerMenuActions = $this->viewVars['headerMenuActions'];
		$headerMenuActions[] = 'divider';
		$headerMenuActions[] = [
			'fas fa-archive',
			__('Archive'),
			['controller' => 'archives', 'action' => 'index', 'plugin' => null],
			['title' => __('Archive of packages'), 'target' => '_blank']
		];
		$listReboot = $this->Package->PackageRebootType->getListPackageRebootTypes();
		$listExecute = $this->Package->PackageExecuteType->getListPackageExecuteTypes();
		$listNotify = $this->Package->PackageNotifyType->getListPackageNotifyTypes();

		$this->set(compact('pageHeader', 'headerMenuActions', 'listReboot', 'listExecute', 'listNotify'));
	}

/**
 * Action `index`. Used to export a XML data of packages.
 *  User role - user.
 *
 * @return void
 */
	public function index() {
		$this->ExportData->export();
	}

/**
 * Action `index`. Used to view a full list of packages.
 *  User role - administrator.
 *
 * @return void
 */
	public function admin_index() {
		$this->_index();
	}

/**
 * Base of action `view`. Used to view information about package.
 *
 * @param int|string $id ID of record for viewing.
 * @throws NotFoundException if record for parameter $id was not found
 * @return void
 */
	protected function _view($id = null) {
		$this->ViewData->actionView($id);
		$pageHeader = __('Information of package');
		$headerMenuActions = $this->viewVars['headerMenuActions'];
		$headerMenuActions[] = 'divider';
		$headerMenuActions[] = [
			'fas fa-archive',
			__('Archive'),
			['controller' => 'archives', 'action' => 'view', $id],
			[
				'title' => __('Archive of package'),
				'target' => '_blank',
				'skip-modal' => true
			]
		];
		$autoVarRevision = $this->Setting->getConfig('AutoVarRevision');

		$this->set(compact('pageHeader', 'headerMenuActions', 'autoVarRevision'));
	}

/**
 * Action `view`. Used to view information about package.
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
	}

/**
 * Action `preview`. Used to preview XML information of package.
 * User role - administrator.
 *
 * @param int|string $id ID of record for viewing
 * @return void
 */
	public function admin_preview($id = null) {
		$this->_preview($id);
	}

/**
 * Base of action `add`. Used to add package.
 *
 * POST Data:
 *  - Package: array data of package;
 *  - DependsOn: array data of depends on packages;
 *  - Includes: array data of includes packages;
 *  - Chains: array data of chains packages.
 *
 * @return void
 */
	protected function _add() {
		$this->view = 'add';
		$this->Package->bindHabtmPackageDependecies(false);
		if ($this->request->is('post')) {
			$this->Package->create();
			if ($this->Package->savePackage($this->request->data)) {
				$this->Flash->success(__('Package has been saved.'));

				return $this->ViewExtension->redirectByUrl(null, 'package');
			} else {
				$this->Flash->error(__('Package could not be saved. Please, try again.'));
			}
		} else {
			$this->request->data = $this->Package->getDefaultValues();
			$this->ViewExtension->setRedirectUrl(null, 'package');
		}
		$listReboot = $this->Package->PackageRebootType->getListPackageRebootTypes();
		$listExecute = $this->Package->PackageExecuteType->getListPackageExecuteTypes();
		$listNotify = $this->Package->PackageNotifyType->getListPackageNotifyTypes();
		$listPriority = $this->Package->PackagePriority->getListPriorities();
		$listPrecheck = $this->Package->PackagePrecheckTypeInstall->getListPackagePrecheckTypes();
		$packageDependencies = $this->Package->getList();
		$breadCrumbs = $this->Package->getBreadcrumbInfo();
		$breadCrumbs[] = __('Adding');
		$pageHeader = __('Adding package');

		$this->set(compact('listReboot', 'listExecute', 'listNotify', 'listPriority',
			'listPrecheck', 'packageDependencies', 'breadCrumbs', 'pageHeader'));
	}

/**
 * Action `add`. Used to add package.
 *  User role - administrator.
 *
 * @return void
 */
	public function admin_add() {
		$this->_add();
	}

/**
 * Base of action `edit`. Used to edit information about package.
 *
 * POST Data:
 *  - Package: array data of package;
 *  - DependsOn: array data of depends on packages;
 *  - Includes: array data of includes packages;
 *  - Chains: array data of chains packages.
 *
 * @param int|string $id ID of record for editing
 * @throws NotFoundException if record for parameter $id was not found
 * @return void
 */
	protected function _edit($id = null) {
		$this->view = 'edit';
		$package = $this->Package->get($id, false);
		if (empty($package)) {
			return $this->ViewExtension->setExceptionMessage(new NotFoundException(__('Invalid ID for package')));
		}

		$this->Package->bindHabtmPackageDependecies(false);
		if ($this->request->is(['post', 'put'])) {
			if ($this->Package->savePackage($this->request->data)) {
				$this->Flash->success(__('Package has been saved.'));

				return $this->ViewExtension->redirectByUrl(null, 'package');
			} else {
				$msg = $this->Package->getMessageCheckDisable();
				if (!empty($msg)) {
					$this->Flash->warning($msg);
				} else {
					$this->Flash->error(__('Package could not be saved. Please, try again.'));
				}
			}
		} else {
			$this->request->data = $package;
			$this->ViewExtension->setRedirectUrl(null, 'package');
		}
		$listReboot = $this->Package->PackageRebootType->getListPackageRebootTypes();
		$listExecute = $this->Package->PackageExecuteType->getListPackageExecuteTypes();
		$listNotify = $this->Package->PackageNotifyType->getListPackageNotifyTypes();
		$listPriority = $this->Package->PackagePriority->getListPriorities();
		$listPrecheck = $this->Package->PackagePrecheckTypeInstall->getListPackagePrecheckTypes();
		$packageDependencies = $this->Package->getListDependencyPackages($id);
		$breadCrumbs = $this->Package->getBreadcrumbInfo($id);
		$breadCrumbs[] = __('Editing');
		$pageHeader = __('Editing package');

		$this->set(compact('listReboot', 'listExecute', 'listNotify', 'listPriority',
			'listPrecheck', 'packageDependencies', 'breadCrumbs', 'pageHeader'));
	}

/**
 * Action `edit`. Used to edit information about package.
 *  User role - administrator.
 *
 * @param int|string $id ID of record for editing
 * @return void
 */
	public function admin_edit($id = null) {
		$this->_edit($id);
	}

/**
 * Base of action `delete`. Used to delete package.
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
 * Action `delete`. Used to delete package.
 *  User role - administrator.
 *
 * @param int|string $id ID of record for deleting
 * @return void
 */
	public function admin_delete($id = null) {
		$this->_delete($id);
	}

/**
 * Base of action `enable`. Used to enable host.
 *
 * @param int|string $id ID of record for enabling
 * @param bool $state State of flag
 * @throws NotFoundException if record for parameter $id was not found
 * @return void
 */
	protected function _enabled($id = null, $state = false) {
		$this->ChangeState->enabled($id, $state);
	}

/**
 * Action `enable`. Used to enable host.
 *  User role - administrator.
 *
 * @param int|string $id ID of record for enabling
 * @param bool $state State of flag
 * @return void
 */
	public function admin_enabled($id = null, $state = false) {
		$this->_enabled($id, $state);
	}

/**
 * Base of action `template`. Used to set or unset the package
 *  usage flag as a template.
 *
 * @param int|string $id ID of record for set flag
 * @param bool $state State of flag
 * @throws NotFoundException if record for parameter $id was not found
 * @return void
 */
	protected function _template($id = null, $state = false) {
		$this->ChangeState->template($id, $state);
	}

/**
 * Action `template`. Used to set or unset the package
 *  usage flag as a template.
 *  User role - administrator.
 *
 * @param int|string $id ID of record for set flag
 * @param bool $state State of flag
 * @return void
 */
	public function admin_template($id = null, $state = false) {
		$this->_template($id, $state);
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

/**
 * Base of action `copy`. Used to copy package.
 *
 * @param int|string $id ID of record for copying
 * @throws NotFoundException if record for parameter $id was not found
 * @throws MethodNotAllowedException if request is not `POST`
 * @return void
 */
	protected function _copy($id = null) {
		$this->TemplateData->actionCopy($id);
	}

/**
 * Action `copy`. Used to copy package.
 *  User role - administrator.
 *
 * @param int|string $id ID of record for copying
 * @return void
 */
	public function admin_copy($id = null) {
		$this->_copy($id);
	}

/**
 * Base of action `create`. Used to create new package based on template.
 *
 * @param int|string $id ID of record for createing
 * @throws NotFoundException if record for parameter $id was not found
 * @throws MethodNotAllowedException if request is not `POST`
 * @return void
 */
	protected function _create($id = null) {
		$this->TemplateData->actionCreate($id);
	}

/**
 * Action `create`. Used to create new package based on template.
 *  User role - administrator.
 *
 * @param int|string $id ID of record for createing
 * @return void
 */
	public function admin_create($id = null) {
		$this->_create($id);
	}

/**
 * Base of action `profiles`. Used to edit package inclusion in profiles.
 *
 * POST Data:
 *  - Package: array data of package;
 *  - Profile: array data of profiles including this package.
 *
 * @param int|string $id ID of package record for editing
 * @throws NotFoundException if record for parameter $id was not found
 * @return void
 */
	protected function _profiles($id = null) {
		$this->view = 'profiles';
		$package = $this->Package->getListProfiles($id);
		if (empty($package)) {
			return $this->ViewExtension->setExceptionMessage(new NotFoundException(__('Invalid ID for package')));
		}

		$profiles = $this->Package->Profile->getList();
		if (empty($profiles)) {
			return $this->ViewExtension->setExceptionMessage(new InternalErrorException(__('There are no packages to add/remove')));
		}

		if ($this->request->is(['post', 'put'])) {
			if ($this->Package->saveAll($this->request->data)) {
				$this->Flash->success(__('Package has been saved.'));

				return $this->ViewExtension->redirectByUrl(null, 'package');
			} else {
				$this->Flash->error(__('Package could not be saved. Please, try again.'));
			}
		} else {
			$this->ViewExtension->setRedirectUrl(null, 'package');
			$this->request->data = $package;
		}
		$breadCrumbs = $this->Package->getBreadcrumbInfo($id);
		$breadCrumbs[] = __('Exists in profiles');
		$pageHeader = __('Modifying list of profiles');

		$this->set(compact('profiles', 'breadCrumbs', 'pageHeader'));
	}

/**
 * Action `profiles`. Used to edit package inclusion in profiles.
 *  User role - administrator.
 *
 * @param int|string $id ID of package record for editing
 * @return void
 */
	public function admin_profiles($id = null) {
		$this->_profiles($id);
	}
}
