<?php
/**
 * This file is the model file of the application. Used for
 *  management alternate mobile phone numbers.
 *
 * CakeLdap: Authentication of users by member group of Active Directory.
 * @copyright Copyright 2017-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.Model
 */

App::uses('CakeLdapAppModel', 'CakeLdap.Model');

/**
 * The model is used to obtain information about alternate
 *  mobile phone numbers.
 *
 * @package plugin.Model
 */
class OthermobileDb extends CakeLdapAppModel {

/**
 * Name of the model.
 *
 * @var string
 * @link http://book.cakephp.org/2.0/en/models/model-attributes.html#name
 */
	public $name = 'OthermobileDb';

/**
 * Custom database table name, or null/false if no table association is desired.
 *
 * @var string
 */
	public $useTable = 'othermobiles';

/**
 * Custom display field name. Display fields are used by Scaffold, in SELECT boxes' OPTION elements.
 *
 * This field is also used in `find('list')` when called with no extra parameters in the fields list
 *
 * @var string
 * @link http://book.cakephp.org/2.0/en/models/model-attributes.html#displayfield
 */
	public $displayField = 'value';

/**
 * List of behaviors to load when the model object is initialized.
 *
 * @var array
 * @link http://book.cakephp.org/2.0/en/models/behaviors.html#using-behaviors
 */
	public $actsAs = [
		'CakeLdap.BindValidation' => [
			'ldapField' => CAKE_LDAP_LDAP_ATTRIBUTE_OTHER_MOBILE_TELEPHONE_NUMBER
		]
	];

/**
 * List of validation rules. It must be an array with the field name as key and using
 * as value one of the following possibilities
 *
 * @var array
 * @link http://book.cakephp.org/2.0/en/models/model-attributes.html#validate
 * @link http://book.cakephp.org/2.0/en/models/data-validation.html
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
				'message' => 'Incorrect value',
				'allowEmpty' => false,
				'required' => true,
				'last' => true
			],
		],
	];
}
