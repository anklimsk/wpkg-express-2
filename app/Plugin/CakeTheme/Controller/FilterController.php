<?php
/**
 * This file is the controller file of the plugin.
 * Autocomplete for input fields of table filter
 *
 * CakeTheme: Set theme for application.
 * @copyright Copyright 2016-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.Controller
 */

App::uses('CakeThemeAppController', 'CakeTheme.Controller');

/**
 * The controller is used for autocomplete input fields of table filter
 *
 * @package plugin.Controller
 */
class FilterController extends CakeThemeAppController {

/**
 * An array containing the class names of models this controller uses.
 *
 * @var mixed
 * @link http://book.cakephp.org/2.0/en/controllers.html#components-helpers-and-uses
 */
	public $uses = ['CakeTheme.Filter'];

/**
 * Called before the controller action. You can use this method to configure and customize components
 * or perform logic that needs to happen before each controller action.
 *
 * Actions:
 *  - Configure components.
 *
 * @return void
 * @link http://book.cakephp.org/2.0/en/controllers.html#request-life-cycle-callbacks
 */
	public function beforeFilter() {
		$this->Auth->allow('autocomplete');
		$this->Security->unlockedActions = ['autocomplete'];

		parent::beforeFilter();
	}

/**
 * Action `autocomplete`. Is used to autocomplte input fields.
 *
 * POST Data:
 *  - `query`: query string for autocomple.
 *  - `type`: type for autocomplete suggestions, e.g. Model.Field.
 *  - `plugin`: plugin name for autocomplete field.
 *
 * @throws BadRequestException if request is not `AJAX`, or not `POST`
 *  or not `JSON`
 * @return void
 */
	public function autocomplete() {
		Configure::write('debug', 0);
		if (!$this->request->is('ajax') || !$this->request->is('post') ||
			!$this->RequestHandler->prefers('json')) {
			throw new BadRequestException();
		}

		$data = [];
		$query = $this->request->data('query');
		if (empty($query)) {
			$this->set(compact('data'));
			$this->set('_serialize', 'data');

			return;
		}

		$type = $this->request->data('type');
		$plugin = $this->request->data('plugin');
		$limit = $this->ConfigTheme->getAutocompleteLimitConfig();
		$data = $this->Filter->getAutocomplete($query, $type, $plugin, $limit);
		if (empty($data)) {
			$data = [];
		}

		$this->set(compact('data'));
		$this->set('_serialize', 'data');
	}
}
