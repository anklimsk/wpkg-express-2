<?php
/**
 * This file is the controller file of the plugin.
 * Receive data of tour
 *
 * CakeTheme: Set theme for application.
 * @copyright Copyright 2017-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.Controller
 */

App::uses('CakeThemeAppController', 'CakeTheme.Controller');

/**
 * The controller is used to receive data of tour
 *
 * @package plugin.Controller
 */
class ToursController extends CakeThemeAppController {

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
		$this->Auth->allow('steps');
		$this->Security->unlockedActions = ['steps'];

		parent::beforeFilter();
	}

/**
 * Action `steps`. Is used to get data of tour steps
 *
 * @throws BadRequestException if request is not `AJAX`, or not `POST`
 *  or not `JSON`
 * @return void
 */
	public function steps() {
		Configure::write('debug', 0);
		if (!$this->request->is('ajax') || !$this->request->is('post') ||
			!$this->RequestHandler->prefers('json')) {
			throw new BadRequestException();
		}

		$data = $this->ConfigTheme->getStepsConfigTourApp();
		$this->set(compact('data'));
		$this->set('_serialize', 'data');
	}
}
