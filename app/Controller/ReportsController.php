<?php
/**
 * This file is the controller file of the application. Used for
 *  management information about the installation state of packages.
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
 * The controller is used for management information about the installation
 *  state of packages.
 *
 * This controller allows to perform the following operations:
 *  - to view installation state of packages;
 *  - to remove client database file;
 *  - to put in queue the task for processing client database files;
 *  - clear reports.
 *
 * @package app.Controller
 */
class ReportsController extends AppController {

/**
 * The name of this controller. Controller names are plural, named after the model they manipulate.
 *
 * @var string
 * @link http://book.cakephp.org/2.0/en/controllers.html#controller-attributes
 */
	public $name = 'Reports';

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
		'ViewData' => ['TargetModel' => 'Report'],
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
			'Report.id',
			'Report.state_id',
			'Report.host_id',
			'Report.package_id',
			'Report.revision',
		],
		'order' => [
			'ReportHost.name' => 'asc',
		],
		'contain' => [
			'ReportState',
			'ReportHost.Attribute',
			'Package',
		]
	];

/**
 * Base of action `index`. Used to view a full list of reports.
 *
 * @return void
 */
	protected function _index() {
		$groupActions = [];
		$this->ViewData->actionIndex(null, $groupActions);
		$listStates = $this->Report->ReportState->getListReportStates();
		$pageHeader = __('Index of reports');
		$headerMenuActions = [
			[
				'fas fa-sync-alt',
				__('Refresh reports'),
				['controller' => 'reports', 'action' => 'parse'],
				['title' => __('Refresh reports'), 'data-toggle' => 'request-only']
			],
			'divider',
			[
				'far fa-trash-alt',
				__('Clear reports'),
				['controller' => 'reports', 'action' => 'clear'],
				[
					'title' => __('Clear reports'), 'action-type' => 'confirm-post',
					'data-confirm-msg' => __('Are you sure you wish to clear reports?'),
				]
			],
		];
		$stateData = $this->Report->getBarStateInfo();
		$showStateData = !(bool)$this->Filter->getFilterConditions();

		$this->set(compact('listStates', 'pageHeader', 'headerMenuActions', 'stateData',
			'showStateData'));
	}

/**
 * Action `index`. Used to view a full list of reports.
 *  User role - administrator.
 *
 * @return void
 */
	public function admin_index() {
		$this->_index();
	}

/**
 * Base of action `parse`. Used to put in queue the task
 *  for processing client database files.
 *
 * @param string $hostName Hostname for parsing client database file
 * @return void
 */
	protected function _parse($hostName = null) {
		$this->loadModel('ExtendQueuedTask');
		$this->ViewExtension->setRedirectUrl(null, 'report');
		$taskParam = compact('hostName');
		if ((bool)$this->ExtendQueuedTask->createJob('ParseDatabases', $taskParam, null, 'parse')) {
			$this->Flash->success(__('Parsing client database files put in queue...'));
			$this->ViewExtension->setProgressSseTask('ParseDatabases');
		} else {
			$this->Flash->error(__('Parsing client database files put in queue unsuccessfully'));
		}

		return $this->ViewExtension->redirectByUrl(null, 'report');
	}

/**
 * Action `parse`. Used to put in queue the task
 *  for processing client database files.
 *
 * @param string $hostName Hostname for parsing client database file
 * @return void
 */
	public function admin_parse($hostName = null) {
		return $this->_parse($hostName);
	}

/**
 * Base of action `clear`. Used to clear reports.
 *
 * @param int|string $id ID of the host record for clear reports
 * @throws NotFoundException if record for parameter $id was not found
 * @throws MethodNotAllowedException if request is not `POST` or `DELETE`
 * @return void
 */
	protected function _clear($id = null) {
		$this->request->allowMethod('post', 'delete');
		$this->ViewExtension->setRedirectUrl(null, 'report');
		if ($this->Report->clearReport($id)) {
			$this->Flash->success(__('The reports has been cleared.'));
		} else {
			$this->Flash->error(__('The reports could not be cleared. Please, try again.'));
		}

		return $this->ViewExtension->redirectByUrl(null, 'report');
	}

/**
 * Action `clear`. Used to clear reports.
 *  User role - administrator.
 *
 * @param int|string $id ID of the host record for clear reports
 * @return void
 */
	public function admin_clear($id = null) {
		$this->_clear($id);
	}

/**
 * Base of action `rename`. Used to rename client database file.
 *
 * @param string $hostName Hostname for renaming client database file
 * @throws MethodNotAllowedException if request is not `POST`
 * @return void
 */
	protected function _rename($hostName = null) {
		$this->request->allowMethod('post');
		$this->ViewExtension->setRedirectUrl(null, 'report');
		if ($this->Report->renameDbFile($hostName)) {
			$this->Flash->success(__('The client database has been renamed.'));
		} else {
			$this->Flash->error(__('The client database could not be renamed. Please, try again.'));
		}

		return $this->ViewExtension->redirectByUrl(null, 'report');
	}

/**
 * Action `rename`. Used to rename client database file.
 *  User role - administrator.
 *
 * @param string $hostName Hostname for renaming client database file
 * @return void
 */
	public function admin_rename($hostName = null) {
		$this->_rename($hostName);
	}

}
