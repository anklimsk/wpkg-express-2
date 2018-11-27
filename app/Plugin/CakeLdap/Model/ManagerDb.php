<?php
/**
 * This file is the model file of the application. Used for
 *  management manager employees from DB.
 *
 * CakeLdap: Authentication of users by member group of Active Directory.
 * @copyright Copyright 2017-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.Model
 */

App::uses('EmployeeDb', 'CakeLdap.Model');

/**
 * The model is used to obtain information about manager employee from DB.
 *
 * @package plugin.Model
 */
class ManagerDb extends EmployeeDb {

/**
 * Name of the model.
 *
 * @var string
 * @link http://book.cakephp.org/2.0/en/models/model-attributes.html#name
 */
	public $name = 'ManagerDb';

/**
 * Custom database table name, or null/false if no table association is desired.
 *
 * @var string
 */
	public $useTable = 'employees';

/**
 * Detailed list of associated models, grouped by binded field.
 *
 * @var array
 */
	protected $_bindModelCfg = [];
}
