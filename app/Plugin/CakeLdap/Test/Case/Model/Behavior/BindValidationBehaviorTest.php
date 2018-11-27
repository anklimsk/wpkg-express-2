<?php
App::uses('AppCakeTestCase', 'CakeLdap.Test');
App::uses('BindValidationBehavior', 'CakeLdap.Model/Behavior');

/**
 * BindValidationBehavior Test Case
 */
class BindValidationBehaviorTest extends AppCakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = [
		'plugin.cake_ldap.department',
		'plugin.cake_ldap.employee_ldap',
	];

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->_targetObject = new BindValidationBehaviorModel();
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->_targetObject);

		parent::tearDown();
	}

/**
 * testBeforeValidateDefaultRules method
 *
 * @return void
 */
	public function testBeforeValidateDefaultRules() {
		$rule = [];
		Configure::write('CakeLdap.LdapSync.LdapFields.' . CAKE_LDAP_LDAP_ATTRIBUTE_DEPARTMENT . '.rules', $rule);
		$this->_targetObject->validates();
		$validator = $this->_targetObject->validator();
		$validationSetFieldValue = $validator->getField('value');
		$rules = $validationSetFieldValue->getRules();
		$result = array_keys($rules);
		$expected = [
			'notBlank',
			'isUnique',
		];
		$this->assertData($expected, $result);
	}

/**
 * testBeforeValidateNewRules method
 *
 * @return void
 */
	public function testBeforeValidateNewRules() {
		$rule = [
			'validDepart' => [
				'rule' => ['notBlank'],
				'message' => 'Incorrect department name',
				'allowEmpty' => false,
				'required' => true,
				'last' => true
			]
		];
		Configure::write('CakeLdap.LdapSync.LdapFields.' . CAKE_LDAP_LDAP_ATTRIBUTE_DEPARTMENT . '.rules', $rule);
		$this->_targetObject->validates();
		$validator = $this->_targetObject->validator();
		$validationSetFieldValue = $validator->getField('value');
		$rules = $validationSetFieldValue->getRules();
		$result = array_keys($rules);
		$expected = [
			'validDepart'
		];
		$this->assertData($expected, $result);
	}
}
