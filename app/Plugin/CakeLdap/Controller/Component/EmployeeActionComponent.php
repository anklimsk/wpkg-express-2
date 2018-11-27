<?php
/**
 * This file is the componet file of the plugin.
 * The base actions of the controller, used to manage
 *  information about employees.
 *
 * CakeLdap: Authentication of users by member group of Active Directory.
 * @copyright Copyright 2017-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.Controller.Component
 */

App::uses('Component', 'Controller');
App::uses('ClassRegistry', 'Utility');

/**
 * EmployeeAction Component.
 *
 * The base actions of the controller, used to manage
 *  information about employees.
 * @package plugin.Controller.Component
 */
class EmployeeActionComponent extends Component {

/**
 * Object of model `Employee`
 *
 * @var object
 */
	protected $_modelEmployee = null;

/**
 * Object of model `Subordinate`
 *
 * @var object
 */
	protected $_modelSubordinate = null;

/**
 * Object of model `ConfigSync`
 *
 * @var object
 */
	protected $_modelConfigSync = null;

/**
 * Controller for the request.
 *
 * @var Controller
 */
	protected $_controller = null;

/**
 * Constructor
 *
 * @param ComponentCollection $collection A ComponentCollection for this component
 * @param array $settings Array of settings.
 */
	public function __construct(ComponentCollection $collection, $settings = []) {
		$this->_modelEmployee = ClassRegistry::init('Employee', true);
		if ($this->_modelEmployee === false) {
			$this->_modelEmployee = ClassRegistry::init('CakeLdap.Employee');
		}
		$this->_modelSubordinate = ClassRegistry::init('CakeLdap.SubordinateDb');
		$this->_modelConfigSync = ClassRegistry::init('CakeLdap.ConfigSync');

		parent::__construct($collection, $settings);
	}

/**
 * Initialize component
 *
 * @param Controller $controller Instantiating controller
 * @return void
 */
	public function initialize(Controller $controller) {
		$this->_controller = $controller;
		if (!$this->_controller->Components->loaded('RequestHandler')) {
			$this->_controller->RequestHandler = $this->_controller->Components->load('RequestHandler');
			$this->_controller->RequestHandler->initialize($this->_controller);
		}
		if (!$this->_controller->Components->loaded('CakeTheme.ViewExtension')) {
			$this->_controller->ViewExtension = $this->_controller->Components->load('CakeTheme.ViewExtension');
			$this->_controller->ViewExtension->initialize($this->_controller);
		}
	}

/**
 * Action `index`. Used to view a complete list of employees.
 *
 * @param array $excludeFieldsPaginate List of fields for excluding
 *  from pagination.
 * @return void
 */
	public function actionIndex($excludeFieldsPaginate = []) {
		if (!$this->_controller->Components->loaded('Paginator')) {
			$this->_controller->Paginator = $this->_controller->Components->load('Paginator');
			$this->_controller->Paginator->initialize($this->_controller);
		}
		if (!$this->_controller->Components->loaded('Filter')) {
			$this->_controller->Filter = $this->_controller->Components->load('CakeTheme.Filter');
			$this->_controller->Filter->initialize($this->_controller);
		}

		$excludeFieldsPaginateDefault = [
			'department_id'
		];
		$excludeFieldsPaginateFull = array_unique(array_merge($excludeFieldsPaginateDefault, (array)$excludeFieldsPaginate));
		$this->_controller->Paginator->settings = $this->_modelEmployee->getPaginateOptions($excludeFieldsPaginateFull);
		$conditions = $this->_controller->Filter->getFilterConditions();
		$employees = $this->_controller->Paginator->paginate('Employee', $conditions);
		if (empty($employees)) {
			$this->_controller->Flash->information(__d('cake_ldap', 'Employees not found'));
		}

		$filterOptions = $this->_modelEmployee->getFilterOptions();
		$fieldsConfig = $this->_modelEmployee->getFieldsConfig();
		$isTreeReady = $this->_modelConfigSync->getFlagTreeSubordinateEnable();
		$pageHeader = __d('cake_ldap', 'Index of employees');
		$headerMenuActions = [
			[
				'fas fa-sync-alt',
				__d('cake_ldap', 'Synchronize information with LDAP server'),
				['controller' => 'employees', 'action' => 'sync', 'prefix' => false],
				[
					'title' => __d('cake_ldap', 'Synchronize information of employees with LDAP server'),
					'data-toggle' => 'request-only',
				]
			]
		];
		if ($isTreeReady) {
			$headerMenuActions[] = [
				'fas fa-sitemap',
				__d('cake_ldap', 'Tree of employees'),
				['controller' => 'employees', 'action' => 'tree', 'prefix' => false],
				['title' => __d('cake_ldap', 'Tree view of employees')]
			];
		}
		$breadCrumbs = $this->_modelEmployee->getBreadcrumbInfo();
		$breadCrumbs[] = __d('cake_ldap', 'Index');
		$this->_controller->ViewExtension->setRedirectUrl(true, 'employee');

		$this->_controller->set(compact(
			'employees',
			'filterOptions',
			'fieldsConfig',
			'isTreeReady',
			'pageHeader',
			'headerMenuActions',
			'breadCrumbs'
		));
	}

/**
 * Action `view`. Used to view information of employee.
 *
 * @param int $id ID of record or Distinguished Name of
 *  employee for viewing
 * @param array $excludeFields List of fields for excluding
 *  from view.
 * @param array $excludeFieldsLabel List of labels for excluding
 *  from view.
 * @param array|string $contain List of binded models
 * @return void
 */
	public function actionView($id = null, $excludeFields = [], $excludeFieldsLabel = [], $contain = []) {
		if (!$this->_modelEmployee->existsEmployee($id)) {
			return $this->_controller->ViewExtension->setExceptionMessage(new NotFoundException(__d('cake_ldap', 'Invalid ID for employee')));
		}

		$excludeFieldsDefault = [];
		$excludeFieldsLabelDefault = [
			'Employee.id',
			'Employee.department_id',
			'Employee.manager_id',
			'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_OBJECT_GUID,
			'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_DISTINGUISHED_NAME,
			'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_INITIALS,
			'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_COMPANY,
		];
		$isModal = $this->_controller->request->is('modal');
		$isPopup = $this->_controller->request->is('popup');
		$includeExtend = !$isPopup;
		$isTreeReady = $this->_modelConfigSync->getFlagTreeSubordinateEnable();
		$fieldsLabelExtend = [];
		if (!$includeExtend) {
			$excludeFieldsDefault = [
				'block',
				'manager_id',
				CAKE_LDAP_LDAP_ATTRIBUTE_PHOTO,
				CAKE_LDAP_LDAP_ATTRIBUTE_COMPUTER,
				CAKE_LDAP_LDAP_ATTRIBUTE_EMPLOYEE_ID,
				CAKE_LDAP_LDAP_ATTRIBUTE_BIRTHDAY,
			];
			foreach ($excludeFieldsDefault as $excludeField) {
				$excludeFieldsLabelDefault[] = 'Employee.' . $excludeField;
			}
		} else {
			$fieldsLabelExtend = $this->_modelEmployee->getListFieldsLabelExtend(false, $excludeFieldsLabel);
		}
		$excludeFieldsLabelFull = array_unique(array_merge($excludeFieldsLabelDefault, (array)$excludeFieldsLabel));
		$excludeFields = array_unique(array_merge($excludeFieldsDefault, (array)$excludeFields));
		$fieldsLabel = $this->_modelEmployee->getListFieldsLabel($excludeFieldsLabelFull, false);
		if (!$isModal && !$isPopup) {
			$redirect = true;
			if ($isModal) {
				$redirect = null;
			}
			$this->_controller->ViewExtension->setRedirectUrl($redirect, 'employee');
		}
		$employee = $this->_modelEmployee->get($id, $excludeFields, $includeExtend, null, $contain);
		$fieldsConfig = $this->_modelEmployee->getFieldsConfig();
		$pageHeader = __d('cake_ldap', 'Information of employees');
		$headerMenuActions = [
			[
				'fas fa-sync-alt',
				__d('cake_ldap', 'Synchronize'),
				['controller' => 'employees', 'action' => 'sync', $employee['Employee'][CAKE_LDAP_LDAP_ATTRIBUTE_OBJECT_GUID], 'prefix' => false],
				['title' => __d('cake_ldap', 'Synchronize information of this employee with LDAP server')]
			]
		];
		if ($isTreeReady) {
			$headerMenuActions[] = [
				'fas fa-sitemap',
				__d('cake_ldap', 'Tree of subordinate'),
				['controller' => 'employees', 'action' => 'tree', $employee['Employee']['id'], 'prefix' => false],
				['title' => __d('cake_ldap', 'Edit tree of subordinate employee')]
			];
		}
		$breadCrumbs = $this->_modelEmployee->getBreadcrumbInfo();
		$breadCrumbs[] = __d('cake_ldap', 'Viewing');

		$this->_controller->set(compact(
			'employee',
			'fieldsLabel',
			'fieldsLabelExtend',
			'fieldsConfig',
			'id',
			'isTreeReady',
			'pageHeader',
			'headerMenuActions',
			'breadCrumbs'
		));
	}

/**
 * Action `sync`. Used to synchronization information of employee
 *  with Active Directory.
 *
 * @param string|null $guid GUID of employee for synchronization
 * @param bool $useQueue If True, use queue of task for synchronization.
 *  Otherwise, synchronization is started now.
 * @return void
 */
	public function actionSync($guid = null, $useQueue = true) {
		if (empty($guid) || $useQueue) {
			$modelQueuedTask = ClassRegistry::init('Queue.QueuedTask');
			$data = null;
			if (!empty($guid)) {
				$data = compact('guid');
			}
			if ((bool)$modelQueuedTask->createJob('SyncEmployee', $data, null, 'sync')) {
				$this->_controller->Flash->success(__d('cake_ldap', 'Synchronization information of employees put in queue...'));
				$this->_controller->ViewExtension->setProgressSseTask('SyncEmployee');
			} else {
				$this->_controller->Flash->error(__d('cake_ldap', 'Synchronization information of employees put in queue unsuccessfully'));
			}
		} else {
			$modelSync = ClassRegistry::init('CakeLdap.Sync');
			if ($modelSync->syncInformation($guid)) {
				$this->_controller->Flash->success(__d('cake_ldap', 'Synchronization information of employees has been finished successfully'));
			} else {
				$this->_controller->Flash->error(__d('cake_ldap', 'Synchronization information of employees has been finished unsuccessfully'));
			}
		}
		$this->_controller->ViewExtension->setRedirectUrl(null, 'employee');

		return $this->_controller->ViewExtension->redirectByUrl(null, 'employee');
	}

/**
 * Action `tree`. Used to view tree of subordinate employees.
 *
 * @param int $id ID of employee for viewing tree of subordinate
 * @param bool $includeBlocked If False, include only non-blocked employees.
 * @param array $includeFields List of fields for encluding to tree.
 * @throws InternalErrorException if tree of subordinate is disabled
 * @return void
 */
	public function actionTree($id = null, $includeBlocked = false, $includeFields = null) {
		if (!$this->_modelConfigSync->getFlagTreeSubordinateEnable()) {
			throw new InternalErrorException(__d('cake_ldap', 'The database does not contain information on the tree view'));
		}

		$fieldsDb = $this->_modelConfigSync->getListFieldsDb();
		$excludeFields = [
			CAKE_LDAP_LDAP_ATTRIBUTE_OTHER_TELEPHONE_NUMBER,
			CAKE_LDAP_LDAP_ATTRIBUTE_OTHER_MOBILE_TELEPHONE_NUMBER,
		];
		$fieldsList = [
			'id',
			'block',
			CAKE_LDAP_LDAP_ATTRIBUTE_NAME,
			CAKE_LDAP_LDAP_ATTRIBUTE_TITLE
		];
		if (!empty($id) && !$this->_modelEmployee->exists($id)) {
			return $this->_controller->ViewExtension->setExceptionMessage(new NotFoundException(__d('cake_ldap', 'Invalid ID for employee')));
		}

		if ($this->_controller->Components->loaded('ViewExtension')) {
			$this->_controller->ViewExtension->setRedirectUrl(true, 'employee');
		}
		$employees = $this->_modelSubordinate->getArrayTreeEmployee($id, true, $includeBlocked, $includeFields);
		$isTreeDraggable = $this->_modelConfigSync->getFlagTreeSubordinateDraggable();
		$expandAll = !empty($id);
		$pageHeader = __d('cake_ldap', 'Tree view information of employees');
		$headerMenuActions = [
			[
				'fas fa-sync-alt',
				__d('cake_ldap', 'Synchronize information with LDAP server'),
				['controller' => 'employees', 'action' => 'sync', 'prefix' => false],
				[
					'title' => __d('cake_ldap', 'Synchronize information of employees with LDAP server'),
					'data-toggle' => 'request-only',
				]
			],
			[
				'fas fa-sort-alpha-down',
				__d('cake_ldap', 'Order tree of employees'),
				['controller' => 'employees', 'action' => 'order', 'prefix' => false],
				[
					'title' => __d('cake_ldap', 'Order tree of employees by alphabet'),
					'action-type' => 'confirm-post',
					'data-confirm-msg' => __d('cake_ldap', 'Are you sure you wish to re-order tree of employees?')
				]
			],
			[
				'fas fa-check',
				__d('cake_ldap', 'Check state tree of employees'),
				['controller' => 'employees', 'action' => 'check', 'prefix' => false],
				['title' => __d('cake_ldap', 'Check state tree of employees')]
			]
		];
		$breadCrumbs = $this->_modelEmployee->getBreadcrumbInfo();
		$breadCrumbs[] = __d('cake_ldap', 'Tree viewing');

		$this->_controller->set(compact(
			'employees',
			'isTreeDraggable',
			'expandAll',
			'pageHeader',
			'headerMenuActions',
			'breadCrumbs'
		));
	}

/**
 * Action `order`. Used to reorder tree of subordinate employees.
 *
 * @throws InternalErrorException if tree of subordinate is disabled
 * @return void
 */
	public function actionOrder() {
		if (!$this->_modelConfigSync->getFlagTreeSubordinateEnable()) {
			throw new InternalErrorException(__d('cake_ldap', 'The database does not contain information on the tree view'));
		}

		$this->_controller->request->allowMethod('post');
		$modelQueuedTask = ClassRegistry::init('Queue.QueuedTask');
		if ((bool)$modelQueuedTask->createJob('OrderEmployee', null, null, 'order')) {
			$this->_controller->Flash->success(__d('cake_ldap', 'Order tree of employees put in queue...'));
			$this->_controller->ViewExtension->setProgressSseTask('OrderEmployee');
		} else {
			$this->_controller->Flash->error(__d('cake_ldap', 'Order tree of employees put in queue unsuccessfully'));
		}
		$redirectUrl = ['controller' => 'employees', 'action' => 'tree'];

		return $this->_controller->redirect($redirectUrl);
	}

/**
 * Action `check`. Used to check state tree of subordinate employees.
 *
 * @throws InternalErrorException if tree of subordinate is disabled
 * @return void
 */
	public function actionCheck() {
		if (!$this->_modelConfigSync->getFlagTreeSubordinateEnable()) {
			throw new InternalErrorException(__d('cake_ldap', 'The database does not contain information on the tree view'));
		}

		set_time_limit(CHECK_TREE_EMPLOYEE_TIME_LIMIT);
		$treeState = $this->_modelSubordinate->verify();
		$pageHeader = __d('cake_ldap', 'Checking state tree of employees');
		$headerMenuActions = [];
		if ($treeState !== true) {
			$headerMenuActions[] = [
				'fas fa-redo-alt',
				__d('cake_ldap', 'Recovery state of tree'),
				['controller' => 'employees', 'action' => 'recover', 'prefix' => false],
				[
					'title' => __d('cake_ldap', 'Recovery state of tree'),
					'data-toggle' => 'request-only',
				]
			];
		}
		$breadCrumbs = $this->_modelEmployee->getBreadcrumbInfo();
		$breadCrumbs[] = __d('cake_ldap', 'Checking tree');

		$this->_controller->set(compact('treeState', 'pageHeader', 'headerMenuActions', 'breadCrumbs'));
	}

/**
 * Action `recover`. Used to recover state tree of subordinate employees.
 *
 * @throws InternalErrorException if tree of subordinate is disabled
 * @return void
 */
	public function actionRecover() {
		if (!$this->_modelConfigSync->getFlagTreeSubordinateEnable()) {
			throw new InternalErrorException(__d('cake_ldap', 'The database does not contain information on the tree view'));
		}

		$modelQueuedTask = ClassRegistry::init('Queue.QueuedTask');
		if ((bool)$modelQueuedTask->createJob('RecoveryEmployee', null, null, 'recovery')) {
			$this->_controller->Flash->success(__d('cake_ldap', 'Recovery tree of employees put in queue...'));
			$this->_controller->ViewExtension->setProgressSseTask('RecoveryEmployee');
		} else {
			$this->_controller->Flash->error(__d('cake_ldap', 'Recovery tree of employees put in queue unsuccessfully'));
		}
		$redirectUrl = ['controller' => 'employees', 'action' => 'check'];

		return $this->_controller->redirect($redirectUrl);
	}
}
