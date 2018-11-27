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
 * User class
 *
 * @package       Cake.Test.Case.Model
 */
class City extends CakeTestModel {

/**
 * name property
 *
 * @var string
 */
	public $name = 'City';

/**
 * Constructor. Binds the model's database table to the object.
 *
 * @param bool|int|string|array $id Set this ID for this model on startup,
 * can also be an array of options, see above.
 * @param string $table Name of database table to use.
 * @param string $ds DataSource connection name.
 */
	public function __construct($id = false, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);
		$this->virtualFields['virt_zip_name'] = sprintf('CONCAT(%s.zip, " ", %s.name)', $this->alias, $this->alias);
	}

}

/**
 * User class
 *
 * @package       Cake.Test.Case.Model
 */
class Pcode extends CakeTestModel {

/**
 * name property
 *
 * @var string
 */
	public $name = 'Pcode';
}

/**
 * User class
 *
 * @package       Cake.Test.Case.Model
 */
class CityPcode extends City {

/**
 * name property
 *
 * @var string
 */
	public $name = 'CityPcode';

/**
 * Custom database table name, or null/false if no table association is desired.
 *
 * @var string
 * @link http://book.cakephp.org/2.0/en/models/model-attributes.html#usetable
 */
	public $useTable = 'cities';

/**
 * List of behaviors to load when the model object is initialized. Settings can be
 * passed to behaviors by using the behavior name as index. Eg:
 *
 * @var array
 * @link http://book.cakephp.org/2.0/en/models/behaviors.html#using-behaviors
 */
	public $actsAs = ['Containable'];

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = [
		'Pcode' => [
			'className' => 'Pcode',
			'foreignKey' => 'pcode_id',
			'dependent' => false,
		]
	];
}
