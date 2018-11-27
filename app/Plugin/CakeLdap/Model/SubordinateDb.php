<?php
/**
 * This file is the model file of the application. Used for
 *  management employee subordination tree.
 *
 * CakeLdap: Authentication of users by member group of Active Directory.
 * @copyright Copyright 2017-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.Model
 */

App::uses('CakeLdapAppModel', 'CakeLdap.Model');
App::uses('ClassRegistry', 'Utility');
App::uses('Hash', 'Utility');
App::uses('CakeEvent', 'Event');

/**
 * The model is used to obtain information about the employee subordination tree.
 *
 * This model allows to obtain the following information:
 *  - tree of subordinate employees;
 *  - synchronize list of departments with Active Directory;
 *  - reorder and recovery tree of subordinate employees.
 *
 * @package plugin.Model
 */
class SubordinateDb extends CakeLdapAppModel {

/**
 * Name of the model.
 *
 * @var string
 */
	public $name = 'SubordinateDb';

/**
 * Custom database table name, or null/false if no table association is desired.
 *
 * @var string
 */
	public $useTable = 'subordinates';

/**
 * Custom display field name. Display fields are used by Scaffold, in SELECT boxes' OPTION elements.
 *
 * This field is also used in `find('list')` when called with no extra parameters in the fields list
 *
 * @var string
 * @link http://book.cakephp.org/2.0/en/models/model-attributes.html#displayfield
 */
	public $displayField = 'name';

/**
 * List of behaviors to load when the model object is initialized. Settings can be
 * passed to behaviors by using the behavior name as index. Eg:
 *
 * @var array
 * @link http://book.cakephp.org/2.0/en/models/behaviors.html#using-behaviors
 */
	public $actsAs = [
		'Tree' => [
			'recursive' => -1
		],
		'CakeTheme.Move',
		'Containable',
		'CakeLdap.Sync'
	];

/**
 * List of validation rules.
 *
 * @var array
 */
	public $validate = [
		'id' => [
			'naturalNumber' => [
				'rule' => ['naturalNumber'],
				'message' => 'Incorrect primary key',
				'allowEmpty' => false,
				'required' => true,
				'last' => true,
				'on' => 'update'
			],
		],
	];

/**
 * Detailed list of hasOne associations.
 *
 * @var array
 * @link http://book.cakephp.org/2.0/en/models/associations-linking-models-together.html#hasone
 */
	public $hasOne = [
		'Employee' => [
			'className' => 'CakeLdap.EmployeeDb',
			'foreignKey' => false,
			'conditions' => ['Employee.id = SubordinateDb.id'],
			'dependent' => false,
		],
	];

/**
 * Returns a list of all events that will fire in the model during it's lifecycle.
 * Add listener callbacks for events `Model.beforeUpdateTree` and `Model.afterUpdateTree`.
 *
 * @return array
 */
	public function implementedEvents() {
		$events = parent::implementedEvents();
		$events['Model.beforeUpdateTree'] = ['callable' => 'beforeUpdateTree', 'passParams' => true];
		$events['Model.afterUpdateTree'] = ['callable' => 'afterUpdateTree'];

		return $events;
	}

/**
 * Called after each successful save operation.
 *
 * Actions:
 *  - Clear cache.
 *
 * @param bool $created True if this save created a new record
 * @param array $options Options passed from Model::save().
 * @return void
 * @link http://book.cakephp.org/2.0/en/models/callback-methods.html#aftersave
 * @see Model::save()
 */
	public function afterSave($created, $options = []) {
		$this->afterUpdateTree();
		parent::afterSave($created, $options);
	}

/**
 * Called after every deletion operation.
 *
 * Actions:
 *  - Clear cache.
 *
 * @return void
 * @link http://book.cakephp.org/2.0/en/models/callback-methods.html#afterdelete
 */
	public function afterDelete() {
		$this->afterUpdateTree();
		parent::afterDelete();
	}

/**
 * Called before each update tree. Return a non-true result
 * to halt the update tree.
 *
 * @param array $options Options:
 *  - `id`: ID of moved record,
 *  - `newParentId`: ID of new parent for moved record,
 *  - `method`: method of move - moveUp or moveDown,
 *  - `delta`: delta for moving.
 * @return bool True if the operation should continue, false if it should abort
 */
	public function beforeUpdateTree($options = []) {
		return true;
	}

/**
 * Called after each successful update tree operation.
 *
 * Actions:
 *  - Delete data from the cache.
 *
 * @return void
 */
	public function afterUpdateTree() {
		Cache::clear(false, CAKE_LDAP_CACHE_KEY_TREE_EMPLOYEES);
	}

/**
 * Return array information of all subordinate employees
 *
 * @param string $guid GUID of employee
 * @param int|string $limit Limit for result
 * @return array|null Return array of informationa about a employees,
 *  or Null if no result.
 */
	public function getAllSubordinates($guid = null, $limit = CAKE_LDAP_SYNC_AD_LIMIT) {
		$result = [];
		$conditions = [];
		$contain = [];
		if (!empty($guid)) {
			$conditions[$this->Employee->alias . '.' . CAKE_LDAP_LDAP_ATTRIBUTE_OBJECT_GUID] = $guid;
			$contain[] = 'Employee';
		}
		$fields = [
			$this->alias . '.id',
			$this->alias . '.parent_id',
			$this->alias . '.lft',
			$this->alias . '.rght',
			$this->alias . '.name',
		];
		$order = [
			$this->alias . '.id' => 'asc',
		];
		$result = $this->find('all', compact('fields', 'conditions', 'order', 'limit', 'contain'));

		return $result;
	}

/**
 * Change parent of subordinate employees
 *
 * @param array $data Data to change parent
 * @return bool Success
 */
	protected function _changeParentInfo($data = null) {
		if (empty($data)) {
			return false;
		}

		$result = true;
		foreach ($data as $dataItem) {
			if (!isset($dataItem[$this->alias]['id']) && empty($dataItem[$this->alias]['id'])) {
				$result = false;
				continue;
			}
			$this->clear();
			$this->recursive = -1;
			$savedInfo = $this->read(null, $dataItem[$this->alias]['id']);
			$savedInfo[$this->alias] = $dataItem[$this->alias] + $savedInfo[$this->alias];
			if (!$this->save($savedInfo)) {
				$result = false;
			}
		}

		return $result;
	}

/**
 * Sync tree of subordinate employees with Active Directory
 *
 * @param string $guid GUID of employee
 * @param int $idTask The ID of the QueuedTask
 * @return bool Return success.
 */
	public function syncInformation($guid = null, $idTask = null) {
		$modelConfigSync = ClassRegistry::init('CakeLdap.ConfigSync');
		if (!$modelConfigSync->getFlagTreeSubordinateEnable()) {
			return true;
		}

		$modelExtendQueuedTask = ClassRegistry::init('CakeTheme.ExtendQueuedTask');
		$syncLymit = $modelConfigSync->getSyncLimit();

		$step = 0;
		$maxStep = 4;
		set_time_limit(SYNC_TREE_EMPLOYEE_TIME_LIMIT);
		$modelExtendQueuedTask->updateProgress($idTask, 0);

		$dataToSave = [];
		$dataToChangeParent = [];
		$dataToRemove = [];
		$errorMessages = [];

		if ($this->verify() !== true) {
			$errorMessage = __d('cake_ldap', 'Tree of employees is broken. Perform a restore.');
			$modelExtendQueuedTask->updateTaskErrorMessage($idTask, $errorMessage, true);

			return false;
		}

		$employeesData = $this->Employee->getListEmployeesManager($guid, $syncLymit);
		if (empty($employeesData)) {
			$errorMessage = __d('cake_ldap', 'Error getting employees information from database');
			$modelExtendQueuedTask->updateTaskErrorMessage($idTask, $errorMessage, true);

			return false;
		}
		$modelExtendQueuedTask->updateTaskProgress($idTask, $step, $maxStep);

		$subordinateEmployeeIdCache = [];
		$subordinatesData = $this->getAllSubordinates($guid, $syncLymit);
		if (!empty($subordinatesData)) {
			$subordinateEmployeeIdCache = $this->createCache($subordinatesData, 'id');
			unset($subordinatesData);
		}
		$excludeFields = [
			'lft' => null,
			'rght' => null
		];
		foreach ($employeesData as $employeeId => $employeeInfo) {
			$employeeInfo['parent_id'] = $employeeInfo['manager_id'];
			unset($employeeInfo['manager_id']);
			$diffInfo = [];
			if (isset($subordinateEmployeeIdCache[$employeeId])) {
				$employeeInfoLocal = $subordinateEmployeeIdCache[$employeeId][$this->alias];
				$employeeInfoLocalPrep = array_diff_key($employeeInfoLocal, $excludeFields);
				$diffInfo = Hash::diff($employeeInfo, $employeeInfoLocalPrep);
				if (empty($diffInfo)) {
					continue;
				}

				$employeeInfo += $employeeInfoLocal;
			}
			if (array_key_exists('parent_id', $diffInfo)) {
				$dataToChangeParent[][$this->alias] = array_diff_key($employeeInfo, $excludeFields);
			} else {
				$dataToSave[][$this->alias] = $employeeInfo;
			}
		}
		$modelExtendQueuedTask->updateTaskProgress($idTask, $step, $maxStep);

		$blockedEmployees = array_diff_key($subordinateEmployeeIdCache, $employeesData);
		if (!empty($blockedEmployees)) {
			$dataToRemove[$this->alias . '.id'] = Hash::extract($blockedEmployees, '{n}.id');
		}

		if (!empty($dataToSave)) {
			$deepLimit = CAKE_LDAP_TREE_EMPLOYEE_DEEP_LIMIT;
			$dataToSaveTemp = [];
			$dataToSaveNest = Hash::nest($dataToSave);
			foreach ($dataToSaveNest as $dataToSaveNestItem) {
				$this->_prepareDataToSave($dataToSaveTemp, $deepLimit, $dataToSaveNestItem);
			}
			$dataToSave = $dataToSaveTemp;
		}
		$modelExtendQueuedTask->updateTaskProgress($idTask, $step, $maxStep);
		$result = true;
		if (!empty($dataToRemove) || !empty($dataToSave) || !empty($dataToChangeParent)) {
			$dataSource = $this->getDataSource();
			$dataSource->begin();
			if (!empty($dataToRemove)) {
				if (!$this->deleteAll($dataToRemove, false, true)) {
					$result = false;
				}
			}
			if (!empty($dataToChangeParent)) {
				if (!$this->_changeParentInfo($dataToChangeParent)) {
					$result = false;
				}
			}
			if (!empty($dataToSave)) {
				if (!$this->saveAll($dataToSave, ['deep' => false, 'atomic' => false])) {
					$result = false;
				}
			}
			if ($result) {
				$dataSource->commit();
				$event = new CakeEvent('Model.afterUpdateTree', $this);
				$this->getEventManager()->dispatch($event);
			} else {
				$dataSource->rollback();
			}
		}

		$infoMessages = [
			[
				'data' => $dataToRemove,
				'label' => __d('cake_ldap', 'Deleted'),
			],
			[
				'data' => $dataToChangeParent,
				'label' => __d('cake_ldap', 'Changed manager'),
			],
			[
				'data' => $dataToSave,
				'label' => __d('cake_ldap', 'Saved'),
			]
		];
		$msgType = __dx('cake_ldap', 'res_msg_type', 'subordinates');
		$resultMessages = $this->getResultMessage($infoMessages, $msgType);
		if (!empty($resultMessages)) {
			$errorMessages[] = $resultMessages;
		}

		$modelExtendQueuedTask->updateTaskProgress($idTask, $step, $maxStep);
		if (!empty($idTask) && !empty($errorMessages)) {
			$modelExtendQueuedTask->updateTaskErrorMessage($idTask, implode("\n", $errorMessages), true);
		}

		return $result;
	}

/**
 * Convert data from Hash::nest() to Model::save() format.
 *
 * @param array &$result Result array
 * @param int &$deepLimit Limit of deep for exit from recursion
 * @param array $dataToSave Data for prepare
 * @return void
 */
	protected function _prepareDataToSave(&$result, &$deepLimit, $dataToSave = []) {
		if (isset($dataToSave[$this->alias]) && !empty($dataToSave[$this->alias])) {
			$result[][$this->alias] = $dataToSave[$this->alias];
		}

		if (!isset($dataToSave['children']) || empty($dataToSave['children'])) {
			return;
		}

		if ($deepLimit < 0) {
			return;
		}

		$deepLimitCurrent = $deepLimit;
		foreach ($dataToSave['children'] as $child) {
			$this->_prepareDataToSave($result, $deepLimitCurrent, $child);
		}
		--$deepLimit;
	}

/**
 * Return condition for build tree of subordinate employees
 *
 * @param int|string $id ID of record as root element
 * @param bool $includeRoot If True, include root element in result
 * @param bool $includeBlocked If False, include only non-blocked employees.
 * @return array Return array condition for build tree of subordinate employees,
 *  use Model::find().
 */
	protected function _getConditionsForEmployeeTreeInfo($id = null, $includeRoot = true, $includeBlocked = false) {
		$result = [];
		if (!$includeBlocked && $this->Employee->hasField('block')) {
			$result[$this->Employee->alias . '.block'] = false;
		}
		if (empty($id)) {
			return $result;
		}

		$conditions = [
			$this->alias . '.id' => $id
		];
		$fields = [
			$this->alias . '.parent_id',
			$this->alias . '.lft',
			$this->alias . '.rght',
		];
		$this->recursive = -1;
		$subordinateTreeInfo = $this->find('first', compact('conditions', 'fields'));
		if (empty($subordinateTreeInfo)) {
			return false;
		}

		$conditionItem = '';
		if ($includeRoot) {
			$conditionItem = '=';
		}
		$result[$this->alias . '.lft >' . $conditionItem] = $subordinateTreeInfo[$this->alias]['lft'];
		$result[$this->alias . '.rght <' . $conditionItem] = $subordinateTreeInfo[$this->alias]['rght'];

		return $result;
	}

/**
 * Return tree of subordinate employees as list
 *
 * @param int|string $id ID of record as root element
 * @param bool $includeRoot If True, include root element in result
 * @param bool $includeBlocked If False, include only non-blocked employees.
 * @return array Return list of subordinate employees.
 */
	public function getListTreeEmployee($id = null, $includeRoot = true, $includeBlocked = false) {
		$cachePath = 'employees_tree_list_' . md5(serialize(func_get_args()));
		$cached = Cache::read($cachePath, CAKE_LDAP_CACHE_KEY_TREE_EMPLOYEES);
		if ($cached !== false) {
			return $cached;
		}

		$result = [];
		$conditions = $this->_getConditionsForEmployeeTreeInfo($id, $includeRoot, $includeBlocked);
		if ($conditions === false) {
			return $result;
		}

		$keyPath = '{n}.' . $this->Employee->alias . '.id';
		$valuePath = '{n}.' . $this->Employee->alias . '.' . $this->Employee->displayField;
		$spacer = '--';
		$recursive = 0;
		$result = $this->generateTreeList($conditions, $keyPath, $valuePath, $spacer, $recursive);

		Cache::write($cachePath, $result, CAKE_LDAP_CACHE_KEY_TREE_EMPLOYEES);

		return $result;
	}

/**
 * Return tree of subordinate employees
 *
 * @param int|string $id ID of record as root element
 * @param bool $includeRoot If True, include root element in result
 * @param bool $includeBlocked If False, include only non-blocked employees.
 * @param array|string $includeFields List of fields for encluding to tree.
 * @return array Return tree of subordinate employees.
 */
	public function getArrayTreeEmployee($id = null, $includeRoot = true, $includeBlocked = false, $includeFields = null) {
		$cachePath = 'employees_tree_arr_' . md5(serialize(func_get_args()));
		$cached = Cache::read($cachePath, CAKE_LDAP_CACHE_KEY_TREE_EMPLOYEES);
		if ($cached !== false) {
			return $cached;
		}

		$result = [];
		$conditions = $this->_getConditionsForEmployeeTreeInfo($id, $includeRoot, $includeBlocked);
		if ($conditions === false) {
			return $result;
		}

		if (empty($includeFields)) {
			$includeFields = [];
		} elseif (!is_array($includeFields)) {
			$includeFields = [$includeFields];
		}

		$fields = [
			$this->alias . '.id',
			$this->alias . '.parent_id',
			$this->alias . '.lft',
			$this->alias . '.rght',
			$this->Employee->alias . '.id',
			$this->Employee->alias . '.' . $this->Employee->displayField
		];
		if ($this->Employee->hasField(CAKE_LDAP_LDAP_ATTRIBUTE_TITLE)) {
			$fields[] = $this->Employee->alias . '.' . CAKE_LDAP_LDAP_ATTRIBUTE_TITLE;
		}
		if ($includeBlocked && $this->Employee->hasField('block')) {
			$fields[] = $this->Employee->alias . '.block';
		}
		if (!empty($includeFields)) {
			$fields = array_values(array_unique(array_merge($fields, $includeFields)));
		}
		$order = [
			$this->alias . '.lft' => 'asc'
		];
		$contain = [
			'Employee'
		];
		$data = $this->find('threaded', compact('conditions', 'fields', 'order', 'contain'));
		if ($data !== false) {
			$result = $data;
		}

		Cache::write($cachePath, $result, CAKE_LDAP_CACHE_KEY_TREE_EMPLOYEES);

		return $result;
	}

/**
 * Reorder tree of subordinate employees.
 *
 * @param bool $verify Whether or not to verify the tree before reorder.
 * @return bool true on success, false on failure
 */
	public function reorderEmployeeTree($verify = true) {
		set_time_limit(REORDER_TREE_EMPLOYEE_TIME_LIMIT);
		if ($verify && ($this->verify() !== true)) {
			return false;
		}

		$dataSource = $this->getDataSource();
		$dataSource->begin();
		$result = $this->reorder(['verify' => false]);
		if ($result) {
			$dataSource->commit();
			$event = new CakeEvent('Model.afterUpdateTree', $this);
			$this->getEventManager()->dispatch($event);
		} else {
			$dataSource->rollback();
		}

		return $result;
	}

/**
 * Recover a corrupted tree
 *
 * @param bool $verify Whether or not to verify the tree before recover.
 * @return bool true on success, false on failure
 */
	public function recoverEmployeeTree($verify = true) {
		set_time_limit(RECOVER_TREE_EMPLOYEE_TIME_LIMIT);
		if ($verify && ($this->verify() === true)) {
			return true;
		}

		$dataSource = $this->getDataSource();
		$dataSource->begin();
		$result = $this->recover('parent');
		if ($result) {
			$dataSource->commit();
			$event = new CakeEvent('Model.afterUpdateTree', $this);
			$this->getEventManager()->dispatch($event);
		} else {
			$dataSource->rollback();
		}

		return $result;
	}
}
