<?php
/**
 * This file is the model file of the application. Used for
 *  management employees.
 *
 * CakeLdap: Authentication of users by member group of Active Directory.
 * @copyright Copyright 2017-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.Model
 */

App::uses('EmployeeDb', 'CakeLdap.Model');

/**
 * The model is used for management employees (default model).
 *
 * @package plugin.Model
 */
class Employee extends EmployeeDb {

/**
 * Name of the model.
 *
 * @var string
 * @link http://book.cakephp.org/2.0/en/models/model-attributes.html#name
 */
	public $name = 'Employee';

/**
 * Return information about a employee
 *
 * @param int|string $id ID of record or Distinguished name for retieve information
 * @param array|string $excludeFields List of fields for excluding
 *  from result
 * @param bool $includeExtend Flag of including extended information in result
 *  e.g. information about tree subordinate employees.
 * @param array|string $fieldsList List of fields for retieve information
 * @param array|string $contain List of binded models
 * @return array|bool Return array of informationa about a employee,
 *  or False on failure.
 */
	public function get($id = null, $excludeFields = null, $includeExtend = false, $fieldsList = null, $contain = null) {
		return parent::get($id, $excludeFields, $includeExtend, $fieldsList, $contain);
	}

/**
 * Get information about extended fields
 *
 * @return array Return array of information about extended
 *  fields in format:
 *   array(
 *      'modelName' => array(
 *              'label' => 'Full label',
 *              'altLabel' => 'Short label',
 *              'priority' => 10,
 *          )
 *  ),
 *  where: modelName - is name of model, or Hash::path
 */
	public function getExtendFieldsInfo() {
		return parent::getExtendFieldsInfo();
	}

/**
 * Return fields configuration for helper
 *
 * @return array Return array of information about extended
 *  fields in format:
 *   array(
 *      'modelName' => array(
 *              'type' => 'string',
 *              'truncate' => false,
 *          )
 *  ),
 *  where:
 *   - modelName - is name of model, or Hash::path.
 *   - type - type of data. Can be one of:
 *   integer, biginteger, float, date, time, datetime,
 *   timestamp, boolean, guid, photo, mail, string, text,
 *   binary, employee, manager, subordinate, department or
 *   element.
 *   - truncate - truncate text.
 */
	public function getExtendFieldsConfig() {
		return parent::getExtendFieldsConfig();
	}
}
