<?php
/**
 * This file is the controller file of the plugin.
 * Receive data of tour
 *
 * CakeNotify: Sending email from CakePHP using task queues
 * @copyright Copyright 2017-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.Controller
 */

App::uses('CakeNotifyAppController', 'CakeNotify.Controller');
App::uses('Hash', 'Utility');
App::uses('Language', 'CakeBasicFunctions.Utility');

/**
 * The controller is used to receive data of notifications
 *
 * @package plugin.Controller
 */
class NotificationsController extends CakeNotifyAppController {

/**
 * Array containing the names of components this controller uses. Component names
 * should not contain the "Component" portion of the class name.
 *
 * @var array
 * @link http://book.cakephp.org/2.0/en/controllers/components.html
 */
	public $components = [
		'CakeTheme.ViewExtension'
	];

/**
 * An array containing the class names of models this controller uses.
 *
 * @var mixed
 * @link http://book.cakephp.org/2.0/en/controllers.html#components-helpers-and-uses
 */
	public $uses = ['CakeNotify.Notification'];

/**
 * Called before the controller action. You can use this method to configure and customize components
 * or perform logic that needs to happen before each controller action.
 *
 * Actions:
 *  - Configure components;
 *
 * @return void
 * @link http://book.cakephp.org/2.0/en/controllers.html#request-life-cycle-callbacks
 */
	public function beforeFilter() {
		$this->Auth->allow('message');

		parent::beforeFilter();
	}

/**
 * Action `message`. Is used to get data of notifications
 *
 * @throws BadRequestException if request is not `SSE`
 * @return void
 */
	public function message() {
		$this->response->disableCache();
		Configure::write('debug', 0);
		if (!$this->RequestHandler->prefers('sse')) {
			throw new BadRequestException();
		}

		$retry = CAKE_NOTIFY_SSE_RETRY;
		$event = 'webNotification';
		$result = [
			'result' => false,
			'messages' => [],
			'retry' => $retry
		];
		if (!$this->Session->started()) {
			$this->Session->renew();
		}

		$userInfo = $this->Auth->user();
		if (empty($userInfo)) {
			$userInfo = [];
		}
		$userId = Hash::get($userInfo, 'id');
		$userRole = Hash::get($userInfo, 'role');
		$prefix = Hash::get($userInfo, 'prefix');

		$lastId = 0;
		if ($this->Session->check('Notifications.lastId')) {
			$lastId = (int)$this->Session->read('Notifications.lastId');
		}

		$notifications = $this->Notification->getNotifications($lastId, $userId, $userRole);
		if (empty($notifications)) {
			$data = json_encode($result);
			$this->set(compact('retry', 'data', 'event'));

			return;
		}

		$iconDefault = '/favicon.ico';
		/*
		$language = new Language();
		$lang = mb_strtoupper($language->getCurrentUiLang(true));
		*/
		foreach ($notifications as $notificationItem) {
			extract($notificationItem['Notification']);
			$lastId = $id;
			$icon = $iconDefault;
			if (isset($data['url']) && is_array($data['url'])) {
				if (!empty($prefix)) {
					$data['url'][$prefix] = true;
				}
				$data['url'] = Router::url($data['url']);
			}
			if (isset($data['icon'])) {
				if (!empty($data['icon'])) {
					$icon = $data['icon'];
				}
				unset($data['icon']);
			}
			$result['messages'][] = compact('tag', 'title', 'body', 'icon', 'data'/*, 'lang'*/);
		}
		if (!empty($result['messages'])) {
			$result['result'] = true;
			$this->Session->write('Notifications.lastId', $lastId);
		}

		$data = json_encode($result);
		$this->set(compact('retry', 'data', 'event'));
	}
}
