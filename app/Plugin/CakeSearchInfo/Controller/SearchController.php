<?php
/**
 * This file is the controller file of the plugin.
 * Process search information request and show result
 *
 * CakeSearchInfo: Search information in project database
 * @copyright Copyright 2016-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.Controller
 */

App::uses('CakeSearchInfoAppController', 'CakeSearchInfo.Controller');

/**
 * The controller is used for process search request and show
 *  result of search.
 *
 * @package plugin.Controller
 */
class SearchController extends CakeSearchInfoAppController {

/**
 * The name of this controller. Controller names are plural, named after the model they manipulate.
 *
 * @var string
 * @link http://book.cakephp.org/2.0/en/controllers.html#controller-attributes
 */
	public $name = 'Search';

/**
 * An array containing the class names of models this controller uses.
 *
 * @var mixed
 * @link http://book.cakephp.org/2.0/en/controllers.html#components-helpers-and-uses
 */
	public $uses = [
		'CakeSearchInfo.Search'
	];

/**
 * Array containing the names of components this controller uses. Component names
 * should not contain the "Component" portion of the class name.
 *
 * @var array
 * @link http://book.cakephp.org/2.0/en/controllers/components.html
 */
	public $components = [
		'Paginator',
		'CakeSearchInfo.SearchFilter'
	];

/**
 * An array containing the names of helpers this controller uses. The array elements should
 * not contain the "Helper" part of the class name.
 *
 * @var mixed
 * @link http://book.cakephp.org/2.0/en/controllers.html#components-helpers-and-uses
 */
	public $helpers = [
		'Text',
		'Number',
		'Paginator',
		'CakeSearchInfo.Search',
		'CakeTheme.ViewExtension',
	];

/**
 * Settings for component 'Paginator'
 *
 * @var array
 */
	public $paginate = [
		'page' => 1,
		'limit' => 10,
		'maxLimit' => 250,
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
		$this->Auth->allow('autocomplete');
		$this->Security->unlockedActions = ['autocomplete'];

		parent::beforeFilter();
	}

/**
 * Action `index`. Used to begin search.
 *
 * @return void
 */
	public function index() {
		$pageTitle = __d('cake_search_info', 'Search information');
		$breadCrumbs = $this->Search->getBreadcrumbInfo();
		$breadCrumbs[] = __d('cake_search_info', 'New search');
		$this->set(compact('pageTitle', 'breadCrumbs'));
		$this->set('search_urlActionSearch', null);
	}

/**
 * Action `search`. Used to view a result of search.
 *
 * @return void
 */
	public function search() {
		$this->SearchFilter->search();
		$pageTitle = __d('cake_search_info', 'Search information');
		$breadCrumbs = $this->Search->getBreadcrumbInfo();
		$breadCrumbs[] = __d('cake_search_info', 'Results of search');
		$this->set(compact('pageTitle', 'breadCrumbs'));
	}

/**
 * Action `autocomplete`. Is used to autocomplte input fields.
 *
 * @return void
 */
	public function autocomplete() {
		$this->SearchFilter->autocomplete();
	}
}
