<?php
/**
 * This file is the controller file of the application. Used for
 *  management information about the profiles.
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
 * The controller is used for management information about the profiles.
 *
 * This controller allows to perform the following operations:
 *  - to view, edit, delete and changing position of profile;
 *  - preview and download XML file of profile;
 *  - to enable or disable profile;
 *  - set or unset the profile usage flag as a template;
 *  - to copy profile;
 *  - to create new profile based on template;
 *  - to edit packages included in profile;
 *  - to edit profile inclusion in hosts.
 *
 * @package app.Controller
 */
class ProfilesController extends AppController {

/**
 * The name of this controller. Controller names are plural, named after the model they manipulate.
 *
 * @var string
 * @link http://book.cakephp.org/2.0/en/controllers.html#controller-attributes
 */
	public $name = 'Profiles';

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
		'ViewData' => ['TargetModel' => 'Profile'],
		'ExportData' => ['TargetModel' => 'Profile'],
		'ChangeState' => ['TargetModel' => 'Profile'],
		'TemplateData' => ['TargetModel' => 'Profile'],
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
			'Profile.id',
			'Profile.enabled',
			'Profile.template',
			'Profile.id_text',
			'Profile.notes',
			'Profile.modified',
		],
		'order' => [
			'Profile.id_text' => 'asc'
		],
		'recursive' => -1
	];

/**
 * Base of action `index`. Used to view a full list of profiles.
 *
 * @return void
 */
	protected function _index() {
		$this->ViewData->actionIndex();
		$pageHeader = __('Index of profiles');
		$this->set(compact('pageHeader'));
	}

/**
 * Action `index`. Used to export a XML data of profiles.
 *  User role - user.
 *
 * @return void
 */
	public function index() {
		$this->ExportData->export();
	}

/**
 * Action `index`. Used to view a full list of profiles.
 *  User role - administrator.
 *
 * @return void
 */
	public function admin_index() {
		$this->_index();
	}

/**
 * Base of action `view`. Used to view information about profile.
 *
 * @param int|string $id ID of record for viewing.
 * @throws NotFoundException if record for parameter $id was not found
 * @return void
 */
	protected function _view($id = null) {
		$this->ViewData->actionView($id);
		$pageHeader = __('Information of profile');
		$this->set(compact('pageHeader'));
	}

/**
 * Action `view`. Used to view information about profile.
 * User role - administrator.
 *
 * @param int|string $id ID of record for viewing
 * @return void
 */
	public function admin_view($id = null) {
		$this->_view($id);
	}

/**
 * Base of action `preview`. Used to preview XML information of profile.
 *
 * @param int|string $id ID of record for previewing.
 * @return void
 */
	protected function _preview($id = null) {
		$this->view = 'preview';
		$this->ExportData->preview($id);
	}

/**
 * Action `preview`. Used to preview XML information of profile.
 * User role - administrator.
 *
 * @param int|string $id ID of record for viewing
 * @return void
 */
	public function admin_preview($id = null) {
		$this->_preview($id);
	}

/**
 * Base of action `add`. Used to add profile.
 *
 * POST Data:
 *  - Profile: array data of profile;
 *  - DependsOn: array data of depends on profiles.
 *
 * @return void
 */
	protected function _add() {
		$this->view = 'add';
		$this->Profile->bindHabtmProfileDependecies(false);
		if ($this->request->is('post')) {
			$this->Profile->create();
			if ($this->Profile->saveProfile($this->request->data)) {
				$this->Flash->success(__('Profile has been saved.'));

				return $this->ViewExtension->redirectByUrl(null, 'profile');
			} else {
				$this->Flash->error(__('Profile could not be saved. Please, try again.'));
			}
		} else {
			$profile = [
				'Profile' => [
					'enabled' => true,
					'template' => false,
				]
			];
			$this->request->data = $profile;
			$this->ViewExtension->setRedirectUrl(null, 'profile');
		}
		$profileDependencies = $this->Profile->getList();
		$breadCrumbs = $this->Profile->getBreadcrumbInfo();
		$breadCrumbs[] = __('Adding');
		$pageHeader = __('Adding profile');

		$this->set(compact('profileDependencies', 'breadCrumbs', 'pageHeader'));
	}

/**
 * Action `add`. Used to add profile.
 *  User role - administrator.
 *
 * @return void
 */
	public function admin_add() {
		$this->_add();
	}

/**
 * Base of action `edit`. Used to edit information about profile.
 *
 * POST Data:
 *  - Profile: array data of profile;
 *  - DependsOn: array data of depends on profiles.
 *
 * @param int|string $id ID of record for editing
 * @throws NotFoundException if record for parameter $id was not found
 * @return void
 */
	protected function _edit($id = null) {
		$this->view = 'edit';
		$profile = $this->Profile->get($id, false);
		if (empty($profile)) {
			return $this->ViewExtension->setExceptionMessage(new NotFoundException(__('Invalid ID for profile')));
		}

		$this->Profile->bindHabtmProfileDependecies(false);
		if ($this->request->is(['post', 'put'])) {
			if ($this->Profile->saveProfile($this->request->data)) {
				$this->Flash->success(__('Profile has been saved.'));

				return $this->ViewExtension->redirectByUrl(null, 'profile');
			} else {
				$msg = $this->Profile->getMessageCheckDisable();
				if (!empty($msg)) {
					$this->Flash->warning($msg);
				} else {
					$this->Flash->error(__('Profile could not be saved. Please, try again.'));
				}
			}
		} else {
			$this->request->data = $profile;
			$this->ViewExtension->setRedirectUrl(null, 'profile');
		}
		$profileDependencies = $this->Profile->getListDependencyProfiles($id);
		$breadCrumbs = $this->Profile->getBreadcrumbInfo($id);
		$breadCrumbs[] = __('Editing');
		$pageHeader = __('Editing profile');
		$this->set(compact('profileDependencies', 'breadCrumbs', 'pageHeader'));
	}

/**
 * Action `edit`. Used to edit information about profile.
 *  User role - administrator.
 *
 * @param int|string $id ID of record for editing
 * @return void
 */
	public function admin_edit($id = null) {
		$this->_edit($id);
	}

/**
 * Base of action `delete`. Used to delete profile.
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
 * Action `delete`. Used to delete profile.
 *  User role - administrator.
 *
 * @param int|string $id ID of record for deleting
 * @return void
 */
	public function admin_delete($id = null) {
		$this->_delete($id);
	}

/**
 * Base of action `enable`. Used to enable profile.
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
 * Action `enable`. Used to enable profile.
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
 * Base of action `template`. Used to set or unset the profile
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
 * Action `template`. Used to set or unset the profile
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
 *  of profile.
 *
 * @param int|string $id ID of record for download
 * @return void
 */
	protected function _download($id = null) {
		$this->ExportData->download($id);
	}

/**
 * Action `download`. Used to download XML file
 *  of profile.
 *  User role - administrator.
 *
 * @param int|string $id ID of record for download
 * @return void
 */
	public function admin_download($id = null) {
		$this->_download($id);
	}

/**
 * Action `download`. Used to download XML file
 *  of profile.
 *  User role - administrator.
 *
 * @param int|string $id ID of record for download
 * @return void
 */
	protected function _copy($id = null) {
		$this->TemplateData->actionCopy($id);
	}

/**
 * Action `copy`. Used to copy profile.
 * User role - administrator.
 *   
 * @param int|string $id ID of record for copying
 * @return void
 */
	public function admin_copy($id = null) {
		$this->_copy($id);
	}

/**
 * Base of action `create`. Used to create new profile based on template.
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
 * Action `create`. Used to create new profile based on template.
 *  User role - administrator.
 *
 * @param int|string $id ID of record for createing
 * @return void
 */
	public function admin_create($id = null) {
		$this->_create($id);
	}

/**
 * Base of action `packages`. Used to edit packages included in profile.
 *
 * POST Data:
 *  - Profile: array data of profile;
 *  - Package: array data of packages included in profile.
 *
 * @param int|string $id ID of profile record for editing
 * @throws NotFoundException if record for parameter $id was not found
 * @return void
 */
	protected function _packages($id = null) {
		$this->view = 'packages';
		$this->Profile->bindHabtmPackages();
		$profile = $this->Profile->getListPackagesForProfile($id);
		if (empty($profile)) {
			return $this->ViewExtension->setExceptionMessage(new NotFoundException(__('Invalid ID for profile')));
		}

		$packages = $this->Profile->Package->getListPackages();
		if (empty($packages)) {
			return $this->ViewExtension->setExceptionMessage(new InternalErrorException(__('There are no packages to add/remove')));
		}

		if ($this->request->is(['post', 'put'])) {
			if ($this->Profile->savePackagesProfile($this->request->data)) {
				$this->Flash->success(__('Profile has been saved.'));

				return $this->ViewExtension->redirectByUrl(null, 'profile');
			} else {
				$this->Flash->error(__('Profile could not be saved. Please, try again.'));
			}
		} else {
			$this->ViewExtension->setRedirectUrl(null, 'profile');
			$this->request->data = $profile;
		}
		$breadCrumbs = $this->Profile->getBreadcrumbInfo($id);
		$breadCrumbs[] = __('Associating packages');
		$pageHeader = __('Modifying associated packages for profile');

		$this->set(compact('packages', 'breadCrumbs', 'pageHeader'));
	}

/**
 * Action `packages`. Used to edit packages included in profile.
 *  User role - administrator.
 *
 * @param int|string $id ID of profile record for editing
 * @return void
 */
	public function admin_packages($id = null) {
		$this->_packages($id);
	}

/**
 * Base of action `hosts`. Used to edit profile inclusion in hosts.
 *
 * POST Data:
 *  - Profile: array data of profile;
 *  - Host: array data of hosts including this profile.
 *
 * @param int|string $id ID of profile record for editing
 * @throws NotFoundException if record for parameter $id was not found
 * @return void
 */
	protected function _hosts($id = null) {
		$this->view = 'hosts';
		$profile = $this->Profile->getListHostsForProfile($id);
		if (empty($profile)) {
			return $this->ViewExtension->setExceptionMessage(new NotFoundException(__('Invalid ID for profile')));
		}

		$hosts = $this->Profile->Host->getList();
		if (empty($hosts)) {
			return $this->ViewExtension->setExceptionMessage(new InternalErrorException(__('There are no hosts to add/remove')));
		}

		if ($this->request->is(['post', 'put'])) {
			if ($this->Profile->saveAll($this->request->data)) {
				$this->Flash->success(__('Profile has been saved.'));

				return $this->ViewExtension->redirectByUrl(null, 'profile');
			} else {
				$this->Flash->error(__('Profile could not be saved. Please, try again.'));
			}
		} else {
			$this->ViewExtension->setRedirectUrl(null, 'profile');
			$this->request->data = $profile;
		}
		$breadCrumbs = $this->Profile->getBreadcrumbInfo($id);
		$breadCrumbs[] = __('Exists in hosts');
		$pageHeader = __('Modifying list of hosts');

		$this->set(compact('hosts', 'breadCrumbs', 'pageHeader'));
	}

/**
 * Action `hosts`. Used to edit profile inclusion in hosts.
 *  User role - administrator.
 *
 * @param int|string $id ID of profile record for editing
 * @return void
 */
	public function admin_hosts($id = null) {
		$this->_hosts($id);
	}

}
