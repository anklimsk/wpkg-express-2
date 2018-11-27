<?php
/**
 * This file is the controller file of the plugin.
 * Used for process Server-Sent Events.
 *
 * CakeTheme: Set theme for application.
 * @copyright Copyright 2016-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.Controller
 */

App::uses('CakeThemeAppController', 'CakeTheme.Controller');
App::uses('QueuedTask', 'Queue.Model');

/**
 * The controller is used for process Server-Sent Events
 *
 * @package plugin.Controller
 * @link https://github.com/byjg/jquery-sse
 */
class EventsController extends CakeThemeAppController {

/**
 * The name of this controller. Controller names are plural, named after the model they manipulate.
 *
 * @var string
 * @link http://book.cakephp.org/2.0/en/controllers.html#controller-attributes
 */
	public $name = 'Events';

/**
 * An array containing the class names of models this controller uses.
 *
 * @var mixed
 * @link http://book.cakephp.org/2.0/en/controllers.html#components-helpers-and-uses
 */
	public $uses = [
		'CakeTheme.SseTask',
		'CakeTheme.ExtendQueuedTask',
	];

/**
 * Array containing the names of components this controller uses. Component names
 * should not contain the "Component" portion of the class name.
 *
 * @var array
 * @link http://book.cakephp.org/2.0/en/controllers/components.html
 */
	public $components = [
		'CakeTheme.ViewExtension',
	];

/**
 * Called before the controller action. You can use this method to configure and customize components
 * or perform logic that needs to happen before each controller action.
 *
 * Actions:
 *  - Configure Auth component;
 *
 * @return void
 * @link http://book.cakephp.org/2.0/en/controllers.html#request-life-cycle-callbacks
 */
	public function beforeFilter() {
		$this->Auth->allow('ssecfg', 'tasks', 'queue');
		$this->Security->unlockedActions = ['ssecfg', 'tasks'];

		parent::beforeFilter();
	}

/**
 * Action `ssecfg`. Is used to get configuration for SSE object
 *
 * @throws BadRequestException if request is not `AJAX`, or not `POST`
 *  or not `JSON`
 * @return void
 */
	public function ssecfg() {
		Configure::write('debug', 0);
		if (!$this->request->is('ajax') || !$this->request->is('post') ||
			!$this->RequestHandler->prefers('json')) {
			throw new BadRequestException();
		}

		$data = $this->ConfigTheme->getSseConfig();
		$this->set(compact('data'));
		$this->set('_serialize', 'data');
	}

/**
 * Action `tasks`. Is used to get list of queued tasks
 *
 * POST Data array:
 *  - `tasks` The name task to obtain list of tasks.
 *  - `delete` If True, delete task from session.
 *
 * @throws BadRequestException if request is not `AJAX`, or not `POST`
 *  or not `JSON`
 * @return void
 */
	public function tasks() {
		$this->response->disableCache();
		Configure::write('debug', 0);
		if (!$this->request->is('ajax') || !$this->request->is('post') ||
			!$this->RequestHandler->prefers('json')) {
			throw new BadRequestException();
		}

		$data = [
			'result' => false,
			'tasks' => []
		];
		$delete = (bool)$this->request->data('delete');
		if ($delete) {
			$tasks = $this->request->data('tasks');
			$data['result'] = $this->SseTask->deleteQueuedTask($tasks);
		} else {
			$tasks = $this->SseTask->getListQueuedTask();
			$data['tasks'] = $tasks;
			$data['result'] = !empty($tasks);
		}
		$this->set(compact('data'));
		$this->set('_serialize', 'data');
	}

/**
 * Action `queue`. Is used to Server-Sent Events.
 *
 * @param string $type Type of task
 * @param int $retry Repeat time of events
 * @throws BadRequestException if request is not `SSE`
 * @return void
 */
	public function queue($type = null, $retry = 3000) {
		$this->response->disableCache();
		Configure::write('debug', 0);
		if (!$this->request->is('sse')) {
			throw new BadRequestException();
		}

		$type = (string)$type;
		$result = [
			'type' => '',
			'progress' => 0,
			'msg' => '',
			'result' => null
		];
		$event = 'progressBar';
		$retry = (int)$retry;
		if (empty($type)) {
			$data = json_encode($result);
			$this->set(compact('retry', 'data', 'event'));

			return;
		}

		$timestamp = null;
		$workermaxruntime = (int)Configure::read('Queue.workermaxruntime');
		if ($workermaxruntime > 0) {
			$timestamp = time() - $workermaxruntime;
		}
		$jobInfo = $this->ExtendQueuedTask->getPendingJob($type, true, $timestamp);
		if (empty($jobInfo)) {
			$result['type'] = $type;
			$result['result'] = false;
			$data = json_encode($result);
			$this->set(compact('retry', 'data', 'event'));

			return;
		}

		switch ($jobInfo['ExtendQueuedTask']['status']) {
			case 'COMPLETED':
				$resultFlag = true;
				break;
			case 'NOT_READY':
			case 'NOT_STARTED':
			case 'FAILED':
			case 'UNKNOWN':
				$resultFlag = false;
				break;
			case 'IN_PROGRESS':
			default:
				$resultFlag = null;
		}
		$result['type'] = $type;
		$result['progress'] = (float)$jobInfo['ExtendQueuedTask']['progress'];
		$result['msg'] = (string)$jobInfo['ExtendQueuedTask']['failure_message'];
		$result['result'] = $resultFlag;
		$data = json_encode($result);
		$this->set(compact('retry', 'data', 'event'));
	}
}
