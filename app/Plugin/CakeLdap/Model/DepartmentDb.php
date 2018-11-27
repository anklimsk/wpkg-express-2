<?php
/**
 * This file is the model file of the application. Used for
 *  management departments.
 *
 * CakeLdap: Authentication of users by member group of Active Directory.
 * @copyright Copyright 2017-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.Model
 */

App::uses('CakeLdapAppModel', 'CakeLdap.Model');
App::uses('ClassRegistry', 'Utility');
App::uses('Hash', 'Utility');

/**
 * The model is used to obtain information about departments and manage it.
 *
 * This model allows to obtain the following information:
 *  - list of departments name;
 *  - prepare save data;
 *  - synchronize list of departments with Active Directory.
 *
 * @package plugin.Model
 */
class DepartmentDb extends CakeLdapAppModel {

/**
 * Name of the model.
 *
 * @var string
 */
	public $name = 'DepartmentDb';

/**
 * Custom database table name, or null/false if no table association is desired.
 *
 * @var string
 */
	public $useTable = 'departments';

/**
 * Custom display field name.
 *
 * @var string
 */
	public $displayField = 'value';

/**
 * List of behaviors to load when the model object is initialized.
 *
 * @var array
 * @link http://book.cakephp.org/2.0/en/models/behaviors.html#using-behaviors
 */
	public $actsAs = [
		'CakeLdap.Sync',
		'CakeLdap.BindValidation' => [
			'ldapField' => CAKE_LDAP_LDAP_ATTRIBUTE_DEPARTMENT
		]
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
		'value' => [
			'notBlank' => [
				'rule' => ['notBlank'],
				'message' => 'Incorrect name of department',
				'allowEmpty' => false,
				'required' => true,
				'last' => true
			],
			'isUnique' => [
				'rule' => ['isUnique'],
				'message' => 'Name of department is not unique',
				'allowEmpty' => false,
				'required' => true,
				'last' => true
			],
		],
	];

/**
 * Called before each save operation, after validation. Return a non-true result
 * to halt the save.
 *
 * Actions:
 * - Converts register the name of the department.
 *
 * @param array $options Options passed from Model::save().
 * @return bool True if the operation should continue, false if it should abort
 * @link http://book.cakephp.org/2.0/en/models/callback-methods.html#beforesave
 * @see Model::save()
 */
	public function beforeSave($options = []) {
		if (isset($this->data[$this->alias]['value']) && !empty($this->data[$this->alias]['value'])) {
			$value = trim($this->data[$this->alias]['value']);
			$this->data[$this->alias]['value'] = (mb_strtolower($value, 'UTF-8') === $value ? mb_ucfirst($value, 'UTF-8') : $value);
		}

		return parent::beforeSave($options);
	}

/**
 * Return name of department by record ID.
 *
 * @param int|string $id The ID of the record to read.
 * @return array Name of department
 */
	public function get($id = null) {
		if (empty($id)) {
			return false;
		}

		$fields = [
			$this->alias . '.id',
			$this->alias . '.value',
		];
		$blockExists = $this->hasField('block');
		if ($blockExists) {
			$fields[] = $this->alias . '.block';
		}
		$conditions = [$this->alias . '.id' => $id];
		$this->recursive = -1;

		return $this->find('first', compact('fields', 'conditions'));
	}

/**
 * Return List of departments
 *
 * @param bool $includeBlock Flag of inclusion in result the blocked
 *  departments (if True).
 * @return array List of departments
 */
	public function getListDepartments($includeBlock = true) {
		$fields = [
			$this->alias . '.id',
			$this->alias . '.value',
		];
		$conditions = [];
		$blockExists = $this->hasField('block');
		if (!$includeBlock && $blockExists) {
			$conditions[$this->alias . '.block'] = false;
		}
		$order = [$this->alias . '.value' => 'asc'];
		$this->recursive = -1;

		return $this->find('list', compact('fields', 'conditions', 'order'));
	}

/**
 * Return array information of all departments
 *
 * @param int|string $limit Limit for result
 * @return array|null Return array of informationa about a departments,
 *  or Null if no result.
 */
	public function getAllDepartments($limit = CAKE_LDAP_SYNC_AD_LIMIT) {
		$fields = [
			$this->alias . '.id',
			$this->alias . '.value',
		];
		$blockExists = $this->hasField('block');
		if ($blockExists) {
			$fields[] = $this->alias . '.block';
		}
		$conditions = [];
		$order = [$this->alias . '.value' => 'asc'];
		$this->recursive = -1;

		return $this->find('all', compact('fields', 'conditions', 'order'));
	}

/**
 * Synchronization list of departments with Active Directory
 *
 * @param int $idTask The ID of the QueuedTask
 * @return bool Return success.
 */
	public function syncInformation($idTask = null) {
		$modelEmployeeLdap = ClassRegistry::init('CakeLdap.EmployeeLdap');
		$modelConfigSync = ClassRegistry::init('CakeLdap.ConfigSync');
		$modelExtendQueuedTask = ClassRegistry::init('CakeTheme.ExtendQueuedTask');
		$companyName = $modelConfigSync->getCompanyName();
		$syncLymit = $modelConfigSync->getSyncLimit();
		$useBlockDepartment = $this->hasField('block');

		$step = 0;
		$maxStep = 3;
		$modelExtendQueuedTask->updateProgress($idTask, 0);
		$dataToSave = [];
		$dataToBlock = [];
		$departmentCache = [];
		$errorMessages = [];

		$dbDepartmentsList = $this->getAllDepartments();
		if (!empty($dbDepartmentsList)) {
			$departmentCache = $this->createCache($dbDepartmentsList, 'value');
		}
		unset($dbDepartmentsList);

		$ldapDepartmentsList = $modelEmployeeLdap->getAllDepartmentsList($companyName, $syncLymit);
		if (empty($ldapDepartmentsList)) {
			$errorMessage = __d('cake_ldap', 'Error getting departments information from LDAP server');
			$modelExtendQueuedTask->updateTaskErrorMessage($idTask, $errorMessage, true);

			return false;
		}

		$modelExtendQueuedTask->updateTaskProgress($idTask, $step, $maxStep);
		foreach ($ldapDepartmentsList as $department) {
			if (isset($departmentCache[$department])) {
				if (!$useBlockDepartment || ($useBlockDepartment && !$departmentCache[$department][$this->alias]['block'])) {
					unset($departmentCache[$department]);
					continue;
				}
				$departmentInfo = $departmentCache[$department];
				if ($useBlockDepartment) {
					$departmentInfo[$this->alias]['block'] = false;
				}
				unset($departmentCache[$department]);
			} else {
				$departmentInfo = [$this->alias => ['value' => $department]];
			}
			$dataToSave[] = $departmentInfo;
		}

		if (!empty($departmentCache)) {
			foreach ($departmentCache as $departmentName => $departmentInfo) {
				if (!$useBlockDepartment || ($useBlockDepartment && $departmentInfo[$this->alias]['block'] !== true)) {
					$dataToBlock[$this->alias . '.id'][] = $departmentInfo[$this->alias]['id'];
				}
			}
		}

		$result = true;
		$modelExtendQueuedTask->updateTaskProgress($idTask, $step, $maxStep);
		if (!empty($dataToBlock)) {
			if ($useBlockDepartment) {
				if (!$this->updateAll([$this->alias . '.block' => true], $dataToBlock)) {
					$result = false;
				}
			} else {
				if (!$this->deleteAll($dataToBlock)) {
					$result = false;
				}
			}
		}
		if (!empty($dataToSave)) {
			if (!$this->saveAll($dataToSave)) {
				$result = false;
			}
		}

		$infoMessages = [
			[
				'data' => $dataToBlock,
				'label' => ($useBlockDepartment ? __d('cake_ldap', 'Blocked') : __d('cake_ldap', 'Deleted')),
			],
			[
				'data' => $dataToSave,
				'label' => __d('cake_ldap', 'Saved'),
			]
		];
		$msgType = __dx('cake_ldap', 'res_msg_type', 'departments');
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
}
