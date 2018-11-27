<?php
/**
 * This file is the controller file of the plugin.
 * Receive configuration data and flash messages
 *
 * CakeTheme: Set theme for application.
 * @copyright Copyright 2016-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.Controller
 */

App::uses('CakeThemeAppController', 'CakeTheme.Controller');

/**
 * The controller is used to receive configuration data and flash messages
 *
 * @package plugin.Controller
 */
class FlashController extends CakeThemeAppController {

/**
 * The name of this controller. Controller names are plural, named after the model they manipulate.
 *
 * @var string
 * @link http://book.cakephp.org/2.0/en/controllers.html#controller-attributes
 */
	public $name = 'Flash';

/**
 * An array containing the class names of models this controller uses.
 *
 * @var mixed
 * @link http://book.cakephp.org/2.0/en/controllers.html#components-helpers-and-uses
 */
	public $uses = [
		'CakeTheme.FlashMessage'
	];

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
		$this->Auth->allow('flashcfg', 'flashmsg');
		$this->Security->unlockedActions = ['flashcfg', 'flashmsg'];

		parent::beforeFilter();
	}

/**
 * Action `flashcfg`. Is used to get configuration for plugin
 *
 * @throws BadRequestException if request is not `AJAX`, or not `POST`
 *  or not `JSON`
 * @return void
 */
	public function flashcfg() {
		Configure::write('debug', 0);
		if (!$this->request->is('ajax') || !$this->request->is('post') ||
			!$this->RequestHandler->prefers('json')) {
			throw new BadRequestException();
		}

		$data = $this->ConfigTheme->getConfigAjaxFlash();
		$this->set(compact('data'));
		$this->set('_serialize', 'data');
	}

/**
 * Action `flashmsg`. Is used to get message from session data
 *
 * POST Data array:
 *  - `keys` The name of the session key for reading messages.
 *  - `delete` If True, delete message from session.
 *
 * @throws BadRequestException if request is not `AJAX`, or not `POST`
 *  or not `JSON`
 * @return void
 */
	public function flashmsg() {
		Configure::write('debug', 0);
		if (!$this->request->is('ajax') || !$this->request->is('post') ||
			!$this->RequestHandler->prefers('json')) {
			throw new BadRequestException();
		}

		$data = [];
		$dataDefault = [
			'result' => false,
			'key' => null,
			'messages' => [],
		];
		$keys = $this->request->data('keys');
		$delete = (bool)$this->request->data('delete');
		if (empty($keys)) {
			$data[] = $dataDefault;
			$this->set(compact('data'));
			$this->set('_serialize', 'data');

			return;
		}
		$keys = (array)$keys;
		foreach ($keys as $key) {
			if (empty($key)) {
				$key = 'flash';
			}

			$dataItem = $dataDefault;
			$dataItem['key'] = $key;
			if ($delete) {
				$dataItem['result'] = $this->FlashMessage->deleteMessage($key);
			} else {
				$messages = $this->FlashMessage->getMessage($key);
				$dataItem['messages'] = $messages;
				$dataItem['result'] = !empty($messages);
			}
			$data[] = $dataItem;
		}

		$this->set(compact('data'));
		$this->set('_serialize', 'data');
	}
}
