<?php
/**
 * This file is the model file of the application. Used for
 *  synchronize informations with Active Directory.
 *
 * CakeLdap: Authentication of users by member group of Active Directory.
 * @copyright Copyright 2017-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.Model
 */

App::uses('CakeLdapAppModel', 'CakeLdap.Model');
App::uses('Hash', 'Utility');
App::uses('CakeText', 'Utility');
App::uses('ClassRegistry', 'Utility');
App::uses('QueuedTask', 'Queue.Model');

/**
 * The model is used to synchronize informations with Active Directory
 *
 * @package plugin.Model
 */
class Sync extends CakeLdapAppModel {

/**
 * Custom database table name, or null/false if no table association is desired.
 *
 * @var string
 * @link http://book.cakephp.org/2.0/en/models/model-attributes.html#usetable
 */
	public $useTable = false;

/**
 * Sync informations of employees with Active Directory
 * TODOS:
 *   - Add synchronization by parts of the data set instead 
 *     of a solid array
 *
 * @param string $guid GUID of employees for synchronize
 * @param int $idTask The ID of the QueuedTask
 * @return bool Return success.
 * @see Sync::_compareEmployeeInfo()
 */
	public function syncInformation($guid = null, $idTask = null) {
		$modelExtendQueuedTask = ClassRegistry::init('CakeTheme.ExtendQueuedTask');
		$modelEmployeeLdap = ClassRegistry::init('CakeLdap.EmployeeLdap');
		$modelEmployeeDb = ClassRegistry::init('Employee', true);
		if ($modelEmployeeDb === false) {
			$modelEmployeeDb = ClassRegistry::init('CakeLdap.Employee');
		}
		$modelDepartmentDb = null;
		if ($modelEmployeeDb->hasField('department_id')) {
			$modelDepartmentDb = ClassRegistry::init('Department', true);
			if ($modelDepartmentDb === false) {
				$modelDepartmentDb = ClassRegistry::init('CakeLdap.Department');
			}
		}
		$modelSubordinateDb = null;
		if ($modelEmployeeDb->hasField('manager_id')) {
			$modelSubordinateDb = ClassRegistry::init('CakeLdap.SubordinateDb');
		}
		$modelConfigSync = ClassRegistry::init('CakeLdap.ConfigSync');
		$companyName = $modelConfigSync->getCompanyName();
		$syncLymit = $modelConfigSync->getSyncLimit();
		$deleteDepartments = $modelConfigSync->getFlagDeleteDepartments();
		$useBlockEmployee = $modelEmployeeDb->hasField('block');
		$useBlockDepartment = $modelDepartmentDb->hasField('block');

		$step = 0;
		$maxStep = 5;
		set_time_limit(SYNC_EMPLOYEE_TIME_LIMIT);
		$modelExtendQueuedTask->updateProgress($idTask, 0);

		$dataToSave = [];
		$dataToBlock = [];
		$dataToDelete = [];
		$employeeDbDnCache = [];
		$emploeeDbGuidCache = [];
		$departmentCache = [];
		$errorMessages = [];
		$result = true;
		if (!empty($modelDepartmentDb)) {
			if (!$modelDepartmentDb->syncInformation($idTask)) {
				return false;
			}

			$departmentDB = $modelDepartmentDb->getListDepartments();
			if (!empty($departmentDB)) {
				$departmentCache = array_flip($departmentDB);
			}
			unset($departmentDB);
		}
		$modelExtendQueuedTask->updateTaskProgress($idTask, $step, $maxStep);

		$employeesDB = $modelEmployeeDb->getAllEmployees($guid, $syncLymit);
		$employeesDBfull = $modelEmployeeDb->getAllEmployees(null, $syncLymit);
		if (!empty($employeesDB)) {
			$employeeDbDnCache = $modelEmployeeDb->createCache($employeesDBfull, CAKE_LDAP_LDAP_ATTRIBUTE_DISTINGUISHED_NAME, 'id');
			$emploeeDbGuidCache = $modelEmployeeDb->createCache($employeesDB, CAKE_LDAP_LDAP_ATTRIBUTE_OBJECT_GUID);
		}
		unset($employeesDB);
		unset($employeesDBfull);

		$employeesLdap = $modelEmployeeLdap->getAllEmployees(
			$companyName,
			$guid,
			CAKE_LDAP_LDAP_ATTRIBUTE_OBJECT_GUID,
			$syncLymit
		);
		if (empty($employeesLdap)) {
			$errorMessage = __d('cake_ldap', 'Error getting employees information from LDAP server');
			$modelExtendQueuedTask->updateTaskErrorMessage($idTask, $errorMessage, true);

			return false;
		}
		$defaultValueFields = $modelEmployeeDb->getFieldsDefaultValue();
		$deleteBindData = [];
		$extendInfo = compact(
			'employeeDbDnCache',
			'departmentCache',
			'defaultValueFields',
			'deleteBindData'
		);
		$modelExtendQueuedTask->updateTaskProgress($idTask, $step, $maxStep);
		foreach ($employeesLdap as $employee) {
			if (!isset($employee[$modelEmployeeLdap->alias][CAKE_LDAP_LDAP_ATTRIBUTE_OBJECT_GUID])) {
				continue;
			}

			$employeeGuid = $employee[$modelEmployeeLdap->alias][CAKE_LDAP_LDAP_ATTRIBUTE_OBJECT_GUID];
			$newData = $this->_compareEmployeeInfo($emploeeDbGuidCache, $extendInfo, $employee[$modelEmployeeLdap->alias], $useBlockEmployee, $useBlockDepartment);
			if (($newData === true) || ($newData === false)) {
				continue;
			}

			$modelEmployeeDb->clear();
			if (!$modelEmployeeDb->saveAll($newData, ['validate' => 'only'])) {
				$validationMessages = [];
				if (isset($newData[$modelEmployeeDb->alias][CAKE_LDAP_LDAP_ATTRIBUTE_DISTINGUISHED_NAME]) && !empty($newData[$modelEmployeeDb->alias][CAKE_LDAP_LDAP_ATTRIBUTE_DISTINGUISHED_NAME])) {
					$validationMessages[] = __d('cake_ldap', 'Employee DN: "%s".', $newData[$modelEmployeeDb->alias][CAKE_LDAP_LDAP_ATTRIBUTE_DISTINGUISHED_NAME]);
				}

				foreach ($modelEmployeeDb->validationErrors as $validationField => $validationError) {
					if (isAssoc($validationError)) {
						foreach ($validationError as $validationFieldBind => $validationErrorBind) {
							$validationErrorList = CakeText::toList($validationErrorBind, __d('cake_ldap', 'and'));
							$validationMessages[] = ' * ' . __d('cake_ldap', 'Model: "%s; "Field: "%s"; Message: "%s".', $validationField, $validationFieldBind, $validationErrorList);
						}
					} else {
						$validationErrorList = CakeText::toList($validationError, __d('cake_ldap', 'and'));
						$validationMessages[] = ' * ' . __d('cake_ldap', 'Field: "%s"; Message: "%s".', $validationField, $validationErrorList);
					}
				}
				$errorMessages[] = __d('cake_ldap', 'Error validation employee: %s', implode("\n", $validationMessages));
				continue;
			}
			$dataToSave[] = $newData;
		}

		if (!empty($extendInfo['deleteBindData'])) {
			$dataToDelete = array_merge($dataToDelete, $extendInfo['deleteBindData']);
		}

		if (!empty($emploeeDbGuidCache)) {
			foreach ($emploeeDbGuidCache as $employeeGUID => $employeeInfo) {
				if (!$useBlockEmployee || ($useBlockEmployee && $employeeInfo[$modelEmployeeDb->alias]['block'] !== true)) {
					$dataToBlock[$modelEmployeeDb->alias . '.id'][] = $employeeInfo[$modelEmployeeDb->alias]['id'];
				}
			}
		}

		$modelExtendQueuedTask->updateTaskProgress($idTask, $step, $maxStep);
		if (!empty($dataToBlock)) {
			if ($useBlockEmployee) {
				if (!$modelEmployeeDb->updateAll([$modelEmployeeDb->alias . '.block' => true], $dataToBlock)) {
					$result = false;
				}
			} else {
				if (!$modelEmployeeDb->deleteAll($dataToBlock)) {
					$result = false;
				}
			}
		}

		if (!empty($dataToDelete)) {
			foreach ($dataToDelete as $bindModel => $ids) {
				if (!isset($modelEmployeeDb->hasMany[$bindModel])) {
					continue;
				}

				if (!$modelEmployeeDb->$bindModel->deleteAll([$modelEmployeeDb->$bindModel->alias . '.id' => $ids])) {
					$result = false;
				}
			}
		}

		if (!empty($dataToSave)) {
			$modelEmployeeDb->clear();
			if (!$modelEmployeeDb->saveAll($dataToSave, ['deep' => true, 'validate' => false])) {
				$result = false;
			}
		}
		$modelExtendQueuedTask->updateTaskProgress($idTask, $step, $maxStep);

		if (!empty($modelDepartmentDb)) {
			if ($result && $deleteDepartments) {
				if (!$modelDepartmentDb->syncInformation($idTask)) {
					$result = false;
				}
			}
		}

		if ((!empty($dataToBlock) || !empty($dataToSave)) && !empty($modelSubordinateDb)) {
			if (!$modelSubordinateDb->syncInformation($guid, $idTask)) {
				$result = false;
			}
		}

		if ($result && !empty($modelSubordinateDb)) {
			$modelSubordinateDb->afterUpdateTree();
		}

		$infoMessages = [
			[
				'data' => $dataToBlock,
				'label' => ($useBlockEmployee ? __d('cake_ldap', 'Blocked') : __d('cake_ldap', 'Deleted')),
			],
			[
				'data' => $dataToDelete,
				'deep' => true,
				'label' => __d('cake_ldap', 'Deleted binded'),
			],
			[
				'data' => $dataToSave,
				'label' => __d('cake_ldap', 'Saved'),
			]
		];
		$msgType = __dx('cake_ldap', 'res_msg_type', 'employees');
		$resultMessages = $modelEmployeeDb->getResultMessage($infoMessages, $msgType);
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
 * To prepare information about employee from Active Directory:
 *  set manager ID, department ID and convert current name of field  to
 *  the name of field from database table employee.
 *
 * @param array $employeeInfoLdap Data for prepare
 * @param array &$extendInfo Array of caches in format:
 *  - key `employeeInfoLocal`, value - information of employee from
 *   database;
 *  - key `employeeDbDnCache`, value - cache of employees from
 *   database. Key: Employee Distinguished Name, value: Employee ID.
 *  - key `departmentCache`, value - cache of departments from
 *   database. Key: Delartment Name, value: Delartment ID.
 *  - key `defaultValueFields`, value - cache of default value for
 *   local fields of database. Key: employee field name, value: default value.
 *  - key `deleteBindData`, value - list of IDs for deleting from binded models.
 *   Key: name of binded model, value: list of IDs.
 * @param bool $useBlockDepartment Use blocking department instead delete on synchronize
 * @return array|bool Return array of prepared data, or False on failure.
 */
	protected function _prepareEmployeeData($employeeInfoLdap = [], &$extendInfo = [], $useBlockDepartment = false) {
		if (empty($employeeInfoLdap) || !isset($employeeInfoLdap[CAKE_LDAP_LDAP_ATTRIBUTE_OBJECT_GUID])) {
			return false;
		}

		$extendInfoDefault = [
			'employeeInfoLocal' => [],
			'employeeDbDnCache' => [],
			'departmentCache' => [],
			'defaultValueFields' => [],
			'deleteBindData' => [],
		];
		$extendInfo = array_intersect_key($extendInfo, $extendInfoDefault);
		$extendInfo += $extendInfoDefault;
		$result = ['Employee' => $extendInfo['defaultValueFields']];
		$resultBindModel = [];
		foreach ($employeeInfoLdap as $employeeInfoLdapField => $employeeInfoLdapVal) {
			$bindModel = null;
			switch ($employeeInfoLdapField) {
				case CAKE_LDAP_LDAP_ATTRIBUTE_MANAGER:
					if (isset($extendInfo['employeeDbDnCache'][$employeeInfoLdapVal]) &&
						!empty($extendInfo['employeeDbDnCache'][$employeeInfoLdapVal])) {
						$result['Employee']['manager_id'] = $extendInfo['employeeDbDnCache'][$employeeInfoLdapVal];
					}
					break;
				case CAKE_LDAP_LDAP_ATTRIBUTE_DEPARTMENT:
					if (!empty($employeeInfoLdapVal)) {
						$result['Department'] = ['value' => $employeeInfoLdapVal];
						if ($useBlockDepartment) {
							$result['Department']['block'] = false;
						}
						if (isset($extendInfo['departmentCache'][$employeeInfoLdapVal])) {
							$result['Department']['id'] = $extendInfo['departmentCache'][$employeeInfoLdapVal];
							$result['Employee']['department_id'] = $extendInfo['departmentCache'][$employeeInfoLdapVal];
						}
					} else {
						$result['Department'] = [
							'id' => null,
							'value' => null,
						];
						if ($useBlockDepartment) {
							$result['Department']['block'] = null;
						}
					}
					break;
				case CAKE_LDAP_LDAP_ATTRIBUTE_OTHER_TELEPHONE_NUMBER:
					$bindModel = 'Othertelephone';
					// no break
				case CAKE_LDAP_LDAP_ATTRIBUTE_OTHER_MOBILE_TELEPHONE_NUMBER:
					if (empty($bindModel)) {
						$bindModel = 'Othermobile';
					}

					$bindData = [];
					$bindDataLocal = [];
					$localData = [];
					if (isset($extendInfo['employeeInfoLocal'][$bindModel])) {
						$localData = $extendInfo['employeeInfoLocal'][$bindModel];
					}
					if (!empty($employeeInfoLdapVal)) {
						if (!is_array($employeeInfoLdapVal)) {
							$employeeInfoLdapVal = [$employeeInfoLdapVal];
						}
						foreach ($employeeInfoLdapVal as $employeeInfoLdapValItem) {
							if (empty($localData)) {
								$bindData[] = ['value' => $employeeInfoLdapValItem];
								continue;
							}

							foreach ($localData as $i => &$localDataItem) {
								if ($localDataItem['value'] === $employeeInfoLdapValItem) {
									$bindDataLocal[$i] = $localDataItem;
									unset($localData[$i]);
									continue 2;
								}
							}
							unset($localDataItem);

							$bindData[] = ['value' => $employeeInfoLdapValItem];
						}
					}
					if (!empty($localData)) {
						$idsToRemove = Hash::extract($localData, '{n}.id');
						if (!isset($extendInfo['deleteBindData'][$bindModel])) {
							$extendInfo['deleteBindData'][$bindModel] = [];
						}
						$extendInfo['deleteBindData'][$bindModel] = array_merge($extendInfo['deleteBindData'][$bindModel], $idsToRemove);
					}
					if (!empty($bindDataLocal)) {
						ksort($bindDataLocal);
						$bindData = array_merge($bindDataLocal, $bindData);
					}
					$resultBindModel[$bindModel] = $bindData;
					unset($result['Employee'][$employeeInfoLdapField]);
					break;
				case 'dn':
					unset($result['Employee'][$employeeInfoLdapField]);
					break;
				default:
					$result['Employee'][$employeeInfoLdapField] = $employeeInfoLdapVal;
			}
		}
		if (isset($extendInfo['employeeInfoLocal']['Employee'])) {
			$internalFieldsData = array_intersect_key($extendInfo['employeeInfoLocal']['Employee'], ['id' => null, 'block' => null]);
			$result['Employee'] = array_merge($result['Employee'], $internalFieldsData);
		}

		$result = $resultBindModel + $result;

		return $result;
	}

/**
 * Compare information about employee from Active Directory and
 *  from database.
 *
 * @param array &$emploeeDbGuidCache Array information about employee
 *  from database
 * @param array &$extendInfo Array of caches in format:
 *  - key `employeeInfoLocal`, value - information of employee from
 *   database;
 *  - key `employeeDbDnCache`, value - information about employee
 *  from database. Key: Employee Distinguished Name, value: Employee ID.
 *  - key `departmentCache`, value - cache of departments from
 *   database. Key: Delartment Name, value: Delartment ID.
 *  - key `defaultValueFields`, value - cache of default value for
 *   local fields of database. Key: employee field name, value: default value.
 *  - key `deleteBindData`, value - list of IDs for deleting from binded models.
 *   Key: name of binded model, value: list of IDs.
 * @param array $employeeInfoLdap Array information about employee
 *  from LDAP.
 * @param bool $useBlockEmployee Use blocking employee instead delete on synchronize
 * @param bool $useBlockDepartment Use blocking department instead delete on synchronize
 * @return array|bool Return array of information about employee
 *  from Active Directory if input data is not equal. Return False
 *  on failure. Return True, input data is equal.
 * @see Sync::_prepareEmployeeData()
 */
	protected function _compareEmployeeInfo(array &$emploeeDbGuidCache, array &$extendInfo = [], $employeeInfoLdap = null, $useBlockEmployee = false, $useBlockDepartment = false) {
		if (empty($employeeInfoLdap)) {
			return false;
		}

		$extendInfoDefault = [
			'employeeInfoLocal' => [],
			'employeeDbDnCache' => [],
			'departmentCache' => [],
			'defaultValueFields' => [],
			'deleteBindData' => [],
		];
		$extendInfo = array_intersect_key($extendInfo, $extendInfoDefault);
		$extendInfo += $extendInfoDefault;

		$guid = $employeeInfoLdap[CAKE_LDAP_LDAP_ATTRIBUTE_OBJECT_GUID];
		if (isset($emploeeDbGuidCache[$guid])) {
			$extendInfo['employeeInfoLocal'] = $emploeeDbGuidCache[$guid];
			unset($emploeeDbGuidCache[$guid]);
		} else {
			$extendInfo['employeeInfoLocal'] = [];
		}

		$employeeInfoLdap = $this->_prepareEmployeeData($employeeInfoLdap, $extendInfo, $useBlockDepartment);
		if ($employeeInfoLdap === false) {
			return false;
		}

		$diffInfo = Hash::diff($employeeInfoLdap, $extendInfo['employeeInfoLocal']);
		if (empty($diffInfo)) {
			if (!$useBlockEmployee || ($useBlockEmployee && !$employeeInfoLdap['Employee']['block'])) {
				return true;
			}
		}
		if ($useBlockEmployee) {
			$employeeInfoLdap['Employee']['block'] = false;
		}

		if (isset($employeeInfoLdap['Department'])) {
			if (empty($employeeInfoLdap['Department']['value'])) {
				unset($employeeInfoLdap['Department']);
			} elseif ($useBlockDepartment) {
				$employeeInfoLdap['Department']['block'] = false;
			}
		}

		$resultData = $employeeInfoLdap;

		return $resultData;
	}
}
