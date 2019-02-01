<?php
/**
 * This file is the controller file of the application. Used to 
 *  manage WPKG script configuration.
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
 * The controller is used to manage WPKG script configuration.
 *
 * This controller allows to perform the following operations:
 *  - to edit WPKG script configuration;
 *  - preview and download XML file of WPKG script configuration.
 *
 * @package app.Controller
 */
class ConfigsController extends AppController {

/**
 * The name of this controller. Controller names are plural, named after the model they manipulate.
 *
 * @var string
 * @link http://book.cakephp.org/2.0/en/controllers.html#controller-attributes
 */
	public $name = 'Configs';

/**
 * Array containing the names of components this controller uses. Component names
 * should not contain the "Component" portion of the class name.
 *
 * @var array
 * @link http://book.cakephp.org/2.0/en/controllers/components.html
 */
	public $components = [
		'ExportData' => ['TargetModel' => 'Config'],
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
 * Base of action `preview`. Used to preview XML information of configuration
 *  WPKG script.
 *
 * @return void
 */
	protected function _preview() {
		$this->view = 'index';
		$this->ExportData->preview();
		$fullName = $this->Config->getFullName();
		$headerMenuActions = [
			[
				'fas fa-pencil-alt',
				__('Edit settings'),
				['controller' => 'configs', 'action' => 'setting'],
				['title' => __('Editing settings of WPKG')]
			],
			[
				'fas fa-undo-alt',
				__('Reset to default'),
				['controller' => 'configs', 'action' => 'default'],
				[
					'title' => __('Reset WPKG configuration to default'), 'action-type' => 'confirm-post',
					'data-confirm-msg' => __('Are you sure you wish to reset WPKG configuration to default, including global variables?'),
				]
			],
			'divider',
			[
				'fas fa-percentage',
				__('Global variables'),
				['controller' => 'variables', 'action' => 'global'],
				['title' => __('Variables of the global environment')]
			],
			'divider'
		];
		$headerMenuActions = array_merge($headerMenuActions, $this->viewVars['headerMenuActions']);

		$this->set(compact('fullName', 'headerMenuActions'));
	}

/**
 * Action `index`. Used to preview XML information of configuration
 *  WPKG script.
 *  User role - administrator.
 *
 * @return void
 */
	public function admin_index() {
		$this->_preview();
	}

/**
 * Action `preview`. Used to preview XML information of configuration 
 *  WPKG script.
 * User role - administrator.
 *
 * @return void
 */
	public function admin_preview() {
		$this->_preview();
	}

/**
 * Base of action `setting`. Used to edit WPKG script configuration.
 *
 * POST Data:
 *  - Config: array data of WPKG script configuration
 *
 * @return void
 */
	protected function _setting() {
		$this->view = 'setting';
		if ($this->request->is('post')) {
			$defaultLogLevel = 19;
			$this->request->data('Config.logLevel', $this->Config->encodeBitmask($this->request->data('Config.logLevel'), $defaultLogLevel));
			if ($this->Config->saveConfig($this->request->data)) {
				$this->Flash->success(__('Settings of WPKG has been saved.'));

				return $this->ViewExtension->redirectByUrl(null, 'config');
			} else {
				$this->Flash->error(__('Settings of WPKG could not be saved. Please, try again.'));
			}
		} else {
			$this->request->data = $this->Config->getAllConfig(true);
			$this->request->data('Config.logLevel', $this->Config->decodeBitmask($this->request->data('Config.logLevel')));
			$this->ViewExtension->setRedirectUrl(null, 'config');
		}
		$listQueryMode = $this->Config->getListQueryMode();
		$listlogLevel = $this->Config->getListLogLevel();
		$breadCrumbs = $this->Config->getBreadcrumbInfo();
		$breadCrumbs[] = __('Editing settings');
		$pageHeader = __('Editing settings of WPKG');
		$headerMenuActions = [
			[
				'fas fa-undo-alt',
				__('Reset to default'),
				['controller' => 'configs', 'action' => 'default'],
				[
					'title' => __('Reset WPKG configuration to default'), 'action-type' => 'confirm-post',
					'data-confirm-msg' => __('Are you sure you wish to reset WPKG configuration to default, including global variables?'),
				]
			],
		];

		$this->set(compact('listQueryMode', 'listlogLevel', 'breadCrumbs', 'pageHeader', 'headerMenuActions'));
	}

/**
 * Action `setting`. Used to edit WPKG script configuration.
 *  User role - administrator.
 *
 * @return void
 */
	public function admin_setting() {
		$this->_setting();
	}

/**
 * Base of action `download`. Used to download XML file
 *  of WPKG script configuration.
 *
 * @return void
 */
	protected function _download() {
		$this->ExportData->download();
	}

/**
 * Action `download`. Used to download XML file
 *  of WPKG script configuration.
 * User role - administrator.
 *
 * @return void
 */
	public function admin_download() {
		$this->_download();
	}

/**
 * Base of action `default`. Used to set default WPKG configuration.
 *
 * @throws MethodNotAllowedException if request is not `POST` or `DELETE`
 * @return void
 */
	protected function _default() {
		$this->request->allowMethod('post', 'delete');
		$this->ViewExtension->setRedirectUrl(null, 'config');
		if ($this->Config->setDefault()) {
			$this->Flash->success(__('The WPKG configuration has been set default. Information will be processed by queue.'));
			$this->ViewExtension->setProgressSseTask('ImportXml');

			return $this->ViewExtension->redirectByUrl(null, 'host');
		} else {
			$this->Flash->error(__('The WPKG configuration could not be set default. Please, try again.'));
		}

		return $this->ViewExtension->redirectByUrl(null, 'config');
	}

/**
 * Action `default`. Used to set default configuration.
 *  User role - administrator.
 *
 * @return void
 */
	public function admin_default() {
		$this->_default();
	}
}
