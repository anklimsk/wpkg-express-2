<?php
/**
 * This file is the controller file of the plugin.
 * Management queues of task.
 *
 * CakeSettingsApp: Manage settings of application.
 * @copyright Copyright 2016-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.Controller
 */

App::uses('CakeSettingsAppAppController', 'CakeSettingsApp.Controller');

/**
 * The controller is used for management queues of task.
 *
 * @package plugin.Controller
 */
class QueuesController extends CakeSettingsAppAppController {

/**
 * The name of this controller. Controller names are plural, named after the model they manipulate.
 *
 * @var string
 * @link http://book.cakephp.org/2.0/en/controllers.html#controller-attributes
 */
	public $name = 'Queues';

/**
 * An array containing the class names of models this controller uses.
 *
 * @var mixed
 * @link http://book.cakephp.org/2.0/en/controllers.html#components-helpers-and-uses
 */
	public $uses = [
		'CakeSettingsApp.Setting',
		'CakeSettingsApp.QueueInfo'
	];

/**
 * Array containing the names of components this controller uses. Component names
 * should not contain the "Component" portion of the class name.
 *
 * @var array
 * @link http://book.cakephp.org/2.0/en/controllers/components.html
 */
	public $components = [
		'Flash',
		'Paginator',
		'CakeTheme.Filter',
		'CakeTheme.ViewExtension',
	];

/**
 * An array containing the names of helpers this controller uses. The array elements should
 * not contain the "Helper" part of the class name.
 *
 * @var mixed
 * @link http://book.cakephp.org/2.0/en/controllers.html#components-helpers-and-uses
 */
	public $helpers = [
		'Time',
		'Number',
		'CakeTheme.Filter',
		'CakeTheme.ViewExtension'
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
			'QueueInfo.id',
			'QueueInfo.jobtype',
			'QueueInfo.created',
			'QueueInfo.fetched',
			'QueueInfo.progress',
			'QueueInfo.completed',
			'QueueInfo.reference',
			'QueueInfo.failed',
			'QueueInfo.failure_message',
			'QueueInfo.status',
		],
		'order' => [
			'QueueInfo.created' => 'desc',
			'QueueInfo.fetched' => 'desc',
		]
	];

/**
 * Constructor.
 *
 * @param CakeRequest $request Request object for this controller. Can be null for testing,
 *  but expect that features that use the request parameters will not work.
 * @param CakeResponse $response Response object for this controller.
 * @throws MissingPluginException if plugin 'Queue' is not loaded.
 */
	public function __construct($request = null, $response = null) {
		if (!CakePlugin::loaded('Queue')) {
			throw new MissingPluginException(['plugin' => 'Queue']);
		}

		parent::__construct($request, $response);
	}

/**
 * Action `index`. Used to view queue of task
 *
 * @return void
 */
	public function index() {
		$groupActions = [
			'group-data-del' => __d('cake_settings_app', 'Delete selected tasks')
		];
		$conditions = $this->Filter->getFilterConditions('CakeTheme');
		$usePost = true;
		if ($this->request->is('post')) {
			$groupAction = $this->Filter->getGroupAction(array_keys($groupActions));
			$resultGroupProcess = $this->QueueInfo->processGroupAction($groupAction, $conditions);
			if ($resultGroupProcess !== null) {
				if ($resultGroupProcess) {
					$conditions = null;
					$this->Flash->success(__d('cake_settings_app', 'Selected tasks has been deleted.'));
				} else {
					$this->Flash->error(__d('cake_settings_app', 'Selected tasks could not be deleted. Please, try again.'));
				}
			}
		} else {
			if (!empty($conditions)) {
				$usePost = false;
			}
		}
		$this->Paginator->settings = $this->paginate;
		$queue = $this->Paginator->paginate('QueueInfo', $conditions);
		$taskStateList = $this->QueueInfo->getListTaskState();
		$stateData = [];
		if ($usePost) {
			$stateData = $this->QueueInfo->getBarStateInfo();
		}
		$pageHeader = __d('cake_settings_app', 'Queue of tasks');
		$headerMenuActions = [];
		if (!empty($queue)) {
			$headerMenuActions[] = [
				'fas fa-trash-alt',
				__d('cake_settings_app', 'Clear queue of tasks'),
				['controller' => 'queues', 'action' => 'clear', 'plugin' => 'cake_settings_app', 'prefix' => false],
				[
					'title' => __d('cake_settings_app', 'Clear queue of tasks'),
					'action-type' => 'confirm-post',
					'data-confirm-msg' => __d('cake_settings_app', 'Are you sure you wish to clear queue of tasks?'),
				]
			];
		}
		$breadCrumbs = $this->Setting->getBreadcrumbInfo();
		$breadCrumbs[] = __d('cake_settings_app', 'Queue of tasks');

		$this->set(compact(
			'queue',
			'groupActions',
			'taskStateList',
			'stateData',
			'usePost',
			'pageHeader',
			'headerMenuActions',
			'breadCrumbs'
		));
	}

/**
 * Action `delete`. Used to delete task from queue
 *
 * @throws InternalErrorException if data of query is empty
 * @return void
 */
	public function delete() {
		$this->request->allowMethod('post', 'delete');
		$data = $this->request->query;
		if (empty($data)) {
			throw new InternalErrorException(__d('cake_settings_app', 'Invalid task'));
		}

		if ($this->QueueInfo->deleteTasks($data)) {
			$this->Flash->success(__d('cake_settings_app', 'The task has been deleted.'));
		} else {
			$this->Flash->error(__d('cake_settings_app', 'The task could not be deleted. Please, try again.'));
		}

		$urlRedirect = ['plugin' => 'cake_settings_app', 'controller' => 'queues', 'action' => 'index'];
		return $this->redirect($urlRedirect);
	}

/**
 * Action `clear`. Used to clear queue of tasks
 *
 * @return void
 */
	public function clear() {
		$this->request->allowMethod('post', 'delete');
		$ds = $this->QueueInfo->getDataSource();
		if ($ds->truncate($this->QueueInfo)) {
			$this->Flash->success(__d('cake_settings_app', 'The task queue has been cleared.'));
		} else {
			$this->Flash->error(__d('cake_settings_app', 'The task queue could not be cleared. Please, try again.'));
		}

		$urlRedirect = ['plugin' => 'cake_settings_app', 'controller' => 'queues', 'action' => 'index'];
		return $this->redirect($urlRedirect);
	}
}
