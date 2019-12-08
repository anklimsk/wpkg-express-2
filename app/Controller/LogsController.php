<?php
/**
 * This file is the controller file of the application. Used for
 *  management information about the logs.
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
 * @copyright Copyright 2018-2019, Andrey Klimov.
 * @package app.Controller
 */

App::uses('AppController', 'Controller');

/**
 * The controller is used for management information about the logs.
 *
 * This controller allows to perform the following operations:
 *  - to view, and delete records of log;
 *  - to put in queue the task for processing log files;
 *  - clear logs.
 *
 * @package app.Controller
 */
class LogsController extends AppController {

/**
 * The name of this controller. Controller names are plural, named after the model they manipulate.
 *
 * @var string
 * @link http://book.cakephp.org/2.0/en/controllers.html#controller-attributes
 */
	public $name = 'Logs';

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
		'ViewData' => ['TargetModel' => 'Log'],
		'ChangeState' => ['TargetModel' => 'Log'],
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
			'Log.id',
			'Log.type_id',
			'Log.host_id',
			'Log.date',
			'Log.message',
		],
		'order' => [
			'Log.date' => 'desc',
		],
		'contain' => [
			'LogType',
			'LogHost',
		]
	];

/**
 * Base of action `index`. Used to view a full list of logs.
 *
 * @return void
 */
	protected function _index() {
		$groupActions = [
			'group-data-del' => __('Delete selected items'),
		];
		$this->ViewData->actionIndex(null, $groupActions);
		$listTypes = $this->Log->LogType->getListLogTypes();
		$pageHeader = __('Index of logs');
		$headerMenuActions = [
			[
				'fas fa-sync-alt',
				__('Refresh logs'),
				['controller' => 'logs', 'action' => 'parse'],
				['title' => __('Refresh logs'), 'data-toggle' => 'request-only']
			],
			'divider',
			[
				'far fa-trash-alt',
				__('Clear logs'),
				['controller' => 'logs', 'action' => 'clear'],
				[
					'title' => __('Clear logs'), 'action-type' => 'confirm-post',
					'data-confirm-msg' => __('Are you sure you wish to clear logs?'),
				]
			],
		];
		$stateData = $this->Log->getBarStateInfo();
		$showStateData = !(bool)$this->Filter->getFilterConditions();
		$shortInfo = false;
		$shortBtnPagination = false;

		$this->set(compact('listTypes', 'pageHeader', 'headerMenuActions',
			'stateData', 'showStateData', 'shortInfo', 'shortBtnPagination'));
	}

/**
 * Action `index`. Used to view a full list of logs.
 *  User role - administrator.
 *
 * @return void
 */
	public function admin_index() {
		$this->_index();
	}

/**
 * Base of action `preview`. Used to preview logs of host.
 *
 * @param int|string $id Host ID for previewing.
 * @return void
 */
	protected function _preview($id = null) {
		$hostName = $this->Log->LogHost->getName($id);
		if (!$hostName) {
			return $this->ViewExtension->setExceptionMessage(new NotFoundException(__('Invalid ID for host')));
		}

		$conditions = [
			'Log.host_id' => $id
		];
		$this->paginate['limit'] = 50;
		$this->paginate['order'] = ['Log.date' => 'asc'];
		$this->ViewData->actionIndex($conditions);
		$breadCrumbs = $this->Log->LogHost->getBreadcrumbInfo($id);
		$breadCrumbs[] = __('Previewing');
		$pageHeader = __("Preview logs of host '%s'", $hostName);
		$headerMenuActions = [
			[
				'far fa-trash-alt',
				__('Clear logs of host'),
				['controller' => 'logs', 'action' => 'clear', $id],
				[
					'title' => __('Clear logs of host'), 'action-type' => 'confirm-post',
					'data-confirm-msg' => __('Are you sure you wish to clear logs of host \'%s\'?', h($hostName)),
				]
			]
		];
		$shortInfo = true;
		$shortBtnPagination = true;

		$this->set(compact('breadCrumbs', 'pageHeader', 'headerMenuActions',
			'shortInfo', 'shortBtnPagination'));
	}

/**
 * Action `preview`. Used to preview logs of host.
 * User role - administrator.
 *
 * @param int|string $id Host ID for previewing
 * @return void
 */
	public function admin_preview($id = null) {
		$this->_preview($id);
	}

/**
 * Base of action `delete`. Used to delete record of log.
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
 * Action `delete`. Used to delete record of log.
 *  User role - administrator.
 *
 * @param int|string $id ID of record for deleting
 * @return void
 */
	public function admin_delete($id = null) {
		$this->_delete($id);
	}

/**
 * Base of action `parse`. Used to put in queue the task
 *  for processing log files.
 *
 * @param string $hostName Hostname for parsing log files
 * @return void
 */
	protected function _parse($hostName = null) {
		$this->loadModel('ExtendQueuedTask');
		$this->ViewExtension->setRedirectUrl(null, 'log');
		$taskParam = compact('hostName');
		if ((bool)$this->ExtendQueuedTask->createJob('ParseLogs', $taskParam, null, 'parse')) {
			$this->Flash->success(__('Parsing logs put in queue...'));
			$this->ViewExtension->setProgressSseTask('ParseLogs');
		} else {
			$this->Flash->error(__('Parsing logs put in queue unsuccessfully'));
		}

		return $this->ViewExtension->redirectByUrl(null, 'log');
	}

/**
 * Action `parse`. Used to put in queue the task
 *  for processing log files.
 *
 * @param string $hostName Hostname for parsing log files
 * @return void
 */
	public function admin_parse($hostName = null) {
		return $this->_parse($hostName);
	}

/**
 * Base of action `clear`. Used to clear logs.
 *
 * @param int|string $id ID of the host record for clear logs
 * @throws NotFoundException if record for parameter $id was not found
 * @throws MethodNotAllowedException if request is not `POST` or `DELETE`
 * @return void
 */
	protected function _clear($id = null) {
		$this->request->allowMethod('post', 'delete');
		$this->ViewExtension->setRedirectUrl(null, 'log');
		if ($this->Log->clearLog($id)) {
			$this->Flash->success(__('The logs has been cleared.'));
		} else {
			$this->Flash->error(__('The logs could not be cleared. Please, try again.'));
		}

		return $this->ViewExtension->redirectByUrl(null, 'log');
	}

/**
 * Action `clear`. Used to clear logs.
 *  User role - administrator.
 *
 * @param int|string $id ID of the host record for clear logs
 * @return void
 */
	public function admin_clear($id = null) {
		$this->_clear($id);
	}

}
