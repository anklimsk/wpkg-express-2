<?php
/**
 * This file is the controller file of the application. Used for
 *  management the employees.
 *
 * LogbookTrips: Logbook of trips. The generation of accompanying documents.
 * @copyright Copyright 2016-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.Controller
 */

App::uses('CakeLdapAppController', 'CakeLdap.Controller');
App::uses('ClassRegistry', 'Utility');
App::uses('EmployeeInfoHelper', 'View/Helper');

/**
 * The controller is used to manage information about employees.
 *
 * This controller allows to perform the following operations:
 *  - viewing information of employees;
 *  - viewing information of tree subordinate employees;
 *  - to synchronize information of employees with AD;
 *  - reorder, checking and reocvery state tree of subordinate employees;
 *  - to move and change manager of employee using drag and drop.
 *
 * @package plugin.Controller
 */
class EmployeesController extends CakeLdapAppController {

/**
 * The name of this controller. Controller names are plural, named after the model they manipulate.
 *
 * @var string
 * @link http://book.cakephp.org/2.0/en/controllers.html#controller-attributes
 */
	public $name = 'Employees';

/**
 * Array containing the names of components this controller uses. Component names
 * should not contain the "Component" portion of the class name.
 *
 * @var array
 * @link http://book.cakephp.org/2.0/en/controllers/components.html
 */
	public $components = [
		'Paginator',
		'RequestHandler',
		'CakeTheme.Filter',
		'CakeTheme.Move' => ['model' => 'CakeLdap.SubordinateDb'],
		'CakeLdap.EmployeeAction',
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
		'Tools.Tree',
	];

/**
 * Settings for component 'Paginator'
 *
 * @var array
 */
	public $paginate = [];

/**
 * Constructor.
 *
 * @param CakeRequest $request Request object for this controller. Can be null for testing,
 *  but expect that features that use the request parameters will not work.
 * @param CakeResponse $response Response object for this controller.
 */
	public function __construct($request = null, $response = null) {
		if (ClassRegistry::init('Employee', true) !== false) {
			$this->uses[] = 'Employee';
		} else {
			$this->uses[] = 'CakeLdap.Employee';
		}

		if (class_exists('EmployeeInfoHelper')) {
			$this->helpers[] = 'EmployeeInfo';
		} else {
			$this->helpers[] = 'CakeLdap.EmployeeInfo';
		}

		parent::__construct($request, $response);
	}

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
		$this->Auth->allow('drop');
		$this->Security->unlockedActions = ['drop', 'move'];

		parent::beforeFilter();
	}

/**
 * Base of action `index`. Used to view a complete list of employees.
 *
 * @return void
 */
	public function index() {
		$this->view = 'index';
		$this->EmployeeAction->actionIndex();
	}

/**
 * Base of action `view`. Used to view information of employee.
 *
 * @param int $id ID of employee for viewing
 * @return void
 */
	public function view($id = null) {
		$this->view = 'view';
		$this->EmployeeAction->actionView($id);
	}

/**
 * Action `sync`. Is used to synchronization information of employee
 *  with Active Directory.
 *
 * @param string|null $guid GUID of employee for synchronization
 * @return void
 */
	public function sync($guid = null) {
		$this->view = 'sync';
		$this->EmployeeAction->actionSync($guid);
	}

/**
 * Action `tree`. Used to view tree of subordinate employees.
 *
 * @param int $id ID of employee for viewing tree of subordinate
 * @throws InternalErrorException if tree of subordinate is disabled
 * @return void
 */
	public function tree($id = null) {
		$this->view = 'tree';
		$this->EmployeeAction->actionTree($id);
	}

/**
 * Action `move`. Used to move employee to new position.
 *
 * @param string $direct Direction for moving: `up`, `down`, `top`, `bottom`
 * @param int $id ID of record for moving
 * @param int $delta Delta for moving
 * @throws InternalErrorException if tree of subordinate is disabled
 * @return void
 */
	public function move($direct = null, $id = null, $delta = 1) {
		$this->loadModel('CakeLdap.ConfigSync');
		if (!$this->ConfigSync->getFlagTreeSubordinateEnable()) {
			throw new InternalErrorException(__d('cake_ldap', 'The database does not contain information on the tree view'));
		}
		$this->Move->moveItem($direct, $id, $delta);
	}

/**
 * Action `drop`. Used to drag and drop employee to new position,
 *  include new manager.
 *
 * POST Data:
 *  - `target` The ID of the item to moving to new position;
 *  - `parent` New parent ID of item;
 *  - `parentStart` Old parent ID of item;
 *  - `tree` Array of ID subtree for item.
 *
 * @throws InternalErrorException if tree of subordinate is disabled
 * @return void
 */
	public function drop() {
		$this->loadModel('CakeLdap.ConfigSync');
		if (!$this->ConfigSync->getFlagTreeSubordinateEnable()) {
			throw new InternalErrorException(__d('cake_ldap', 'The database does not contain information on the tree view'));
		}
		$this->Move->dropItem();
	}

/**
 * Action `order`. Used to reorder tree of subordinate employees.
 *
 * @throws InternalErrorException if tree of subordinate is disabled
 * @throws MethodNotAllowedException if request is not POST
 * @return void
 */
	public function order() {
		$this->EmployeeAction->actionOrder();
	}

/**
 * Action `check`. Used to check state tree of subordinate employees.
 *
 * @throws InternalErrorException if tree of subordinate is disabled
 * @return void
 */
	public function check() {
		$this->EmployeeAction->actionCheck();
	}

/**
 * Action `recover`. Used to recover state tree of subordinate employees.
 *
 * @throws InternalErrorException if tree of subordinate is disabled
 * @return void
 */
	public function recover() {
		$this->EmployeeAction->actionRecover();
	}
}
