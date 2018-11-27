<?php
/**
 * Mock models file
 *
 * Mock classes for use in Model and related test cases
 *
 * CakePHP(tm) Tests <http://book.cakephp.org/2.0/en/development/testing.html>
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://book.cakephp.org/2.0/en/development/testing.html CakePHP(tm) Tests
 * @package       Cake.Test.Case.Model
 * @since         CakePHP(tm) v 1.2.0.6464
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

/**
 * EmployeeTest class
 *
 * @package       Cake.Test.Case.Model
 */
class EmployeeTest extends CakeTestModel {

/**
 * Name of the model.
 *
 * @var string
 */
	public $name = 'EmployeeTest';

/**
 * Custom database table name, or null/false if no table association is desired.
 *
 * @var string
 */
	public $useTable = 'employees';

/**
 * Array of virtual fields this model has. Virtual fields are aliased
 * SQL expressions. Fields added to this property will be read as other fields in a model
 * but will not be saveable.
 *
 * Is a simplistic example of how to set virtualFields
 *
 * @var array
 * @link http://book.cakephp.org/2.0/en/models/model-attributes.html#virtualfields
 */
	public $virtualFields = [
		'name' => 'CONCAT_WS(" ", EmployeeTest.last_name, EmployeeTest.first_name, EmployeeTest.middle_name)'
	];
}

/**
 * TreeDataTest class
 *
 * @package       Cake.Test.Case.Model
 */
class TreeDataTest extends CakeTestModel {

/**
 * Name of the model.
 *
 * @var string
 */
	public $name = 'TreeDataTest';

/**
 * Custom database table name, or null/false if no table association is desired.
 *
 * @var string
 */
	public $useTable = 'trees';

/**
 * List of behaviors to load when the model object is initialized. Settings can be
 * passed to behaviors by using the behavior name as index. Eg:
 *
 * @var array
 * @link http://book.cakephp.org/2.0/en/models/behaviors.html#using-behaviors
 */
	public $actsAs = [
		'Tree' => [
			'scope' => ['TreeDataTest.type' => 1]
		],
		'CakeTheme.Move'
	];

/**
 * List of callback actions
 *
 * @var array
 */
	public $callbackActions = [];

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
 * Called before each update tree. Return a non-true result
 * to halt the update tree.
 *
 * @param array $options Options:
 *  - `id`: ID of moved record,
 *  - `newParentId`: ID of new parent for moved record,
 *  - `method`: method of move - moveUp or moveDown,
 *  - `delta`: delta for moving.
 *
 * @return bool True if the operation should continue, false if it should abort
 */
	public function beforeUpdateTree($options = []) {
		$this->callbackActions['beforeUpdateTree'] = $options;

		return true;
	}

/**
 * Called after each successful update tree operation.
 *
 * @return void
 */
	public function afterUpdateTree() {
		$this->callbackActions['afterUpdateTree'] = null;
	}

}

/**
 * BreadCrumbTest class
 *
 * @package       Cake.Test.Case.Model
 */
class BreadCrumbTest extends CakeTestModel {

/**
 * Name of the model.
 *
 * @var string
 */
	public $name = 'BreadCrumbTest';

/**
 * Custom database table name, or null/false if no table association is desired.
 *
 * @var string
 */
	public $useTable = 'employees';

/**
 * Custom display field name. Display fields are used by Scaffold, in SELECT boxes' OPTION elements.
 *
 * This field is also used in `find('list')` when called with no extra parameters in the fields list
 *
 * @var string
 */
	public $displayField = 'full_name';

/**
 * List of behaviors to load when the model object is initialized. Settings can be
 * passed to behaviors by using the behavior name as index. Eg:
 *
 * @var array
 * @link http://book.cakephp.org/2.0/en/models/behaviors.html#using-behaviors
 */
	public $actsAs = [
		'CakeTheme.BreadCrumb'
	];

}
