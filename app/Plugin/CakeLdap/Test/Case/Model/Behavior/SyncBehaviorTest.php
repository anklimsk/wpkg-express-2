<?php
App::uses('AppCakeTestCase', 'CakeLdap.Test');
App::uses('SyncBehavior', 'CakeLdap.Model/Behavior');

/**
 * SyncBehavior Test Case
 */
class SyncBehaviorTest extends AppCakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = [
		'plugin.cake_ldap.department',
	];

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->_targetObject = new SyncBehaviorModel();
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
 * testCreateCacheEmptyData method
 *
 * @return void
 */
	public function testCreateCacheEmptyData() {
		$result = $this->_targetObject->createCache([], '', '');
		$expected = [];
		$this->assertData($expected, $result);
	}

/**
 * testCreateCacheEmptyKey method
 *
 * @return void
 */
	public function testCreateCacheEmptyKey() {
		$data = $this->_targetObject->find('all');
		$result = $this->_targetObject->createCache($data, '', '');
		$expected = [];
		$this->assertData($expected, $result);
	}

/**
 * testCreateCacheInvalidKey method
 *
 * @return void
 */
	public function testCreateCacheInvalidKey() {
		$data = $this->_targetObject->find('all');
		$result = $this->_targetObject->createCache($data, 'bad_key', 'value');
		$expected = [];
		$this->assertData($expected, $result);
	}

/**
 * testCreateCacheEmptyValue method
 *
 * @return void
 */
	public function testCreateCacheEmptyValue() {
		$data = $this->_targetObject->find('all');
		$result = $this->_targetObject->createCache($data, 'value', '');
		$expected = [
			'АТО' => [
				'DepartmentDb' => [
					'id' => '5',
					'value' => 'АТО',
					'block' => false,
				]
			],
			'ОИТ' => [
				'DepartmentDb' => [
					'id' => '3',
					'value' => 'ОИТ',
					'block' => false,
				]
			],
			'ОРС' => [
				'DepartmentDb' => [
					'id' => '4',
					'value' => 'ОРС',
					'block' => false,
				]
			],
			'ОС' => [
				'DepartmentDb' => [
					'id' => '2',
					'value' => 'ОС',
					'block' => false,
				]
			],
			'Охрана труда' => [
				'DepartmentDb' => [
					'id' => '6',
					'value' => 'Охрана труда',
					'block' => false,
				]
			],
			'СО' => [
				'DepartmentDb' => [
					'id' => '7',
					'value' => 'СО',
					'block' => true,
				]
			],
			'УИЗ' => [
				'DepartmentDb' => [
					'id' => '1',
					'value' => 'УИЗ',
					'block' => false,
				]
			]
		];
		$this->assertData($expected, $result);
	}

/**
 * testCreateCacheInvalidValue method
 *
 * @return void
 */
	public function testCreateCacheInvalidValue() {
		$data = $this->_targetObject->find('all');
		$result = $this->_targetObject->createCache($data, 'id', 'bad_value');
		$expected = [
			1 => null,
			2 => null,
			3 => null,
			4 => null,
			5 => null,
			6 => null,
			7 => null,
		];
		$this->assertData($expected, $result);
	}

/**
 * testCreateCache method
 *
 * @return void
 */
	public function testCreateCache() {
		$data = $this->_targetObject->find('all');
		$result = $this->_targetObject->createCache($data, 'value', 'id');
		$expected = [
			'АТО' => '5',
			'ОИТ' => '3',
			'ОРС' => '4',
			'ОС' => '2',
			'Охрана труда' => '6',
			'СО' => '7',
			'УИЗ' => '1',
		];
		$this->assertData($expected, $result);
	}

/**
 * testGetResultMessageEmptyInfoData method
 *
 * @return void
 */
	public function testGetResultMessageEmptyInfoData() {
		$result = $this->_targetObject->getResultMessage([], 'test');
		$this->assertFalse($result);
	}

/**
 * testGetResultMessageInvalidInfoData method
 *
 * @return void
 */
	public function testGetResultMessageInvalidInfoData() {
		$info = ['bad_info'];
		$result = $this->_targetObject->getResultMessage($info, 'test');
		$expected = '';
		$this->assertData($expected, $result);
	}

/**
 * testGetResultMessage method
 *
 * @return void
 */
	public function testGetResultMessageEmptyType() {
		$info = [
			[
				'data' => [
					'1',
					'2',
				]
			],
			[
				'data' => [
					'lavel 1' => [
						1,
						2,
						3
					],
					'lavel 2' => [
						1,
						2,
					]
				],
				'label' => 'Use deep',
				'deep' => true,
			],
			[
				'data' => [
					'lavel 1' => [],
					'lavel 2' => []
				],
				'label' => 'Empty data',
				'deep' => true,
			],
		];
		$result = $this->_targetObject->getResultMessage($info, '');
		$resultMessages = [
			' * 2 ' . __dn('cake_ldap', 'record', 'records', 2),
			' * Use deep: 5 ' . __dn('cake_ldap', 'record', 'records', 5),
		];
		$expected = __d('cake_ldap', 'Result of synchronization') . "\n" . implode("\n", $resultMessages);
		$this->assertData($expected, $result);
	}

/**
 * testGetResultMessage method
 *
 * @return void
 */
	public function testGetResultMessage() {
		$info = [
			[
				'data' => [
					'1',
					'2',
				],
				'deep' => false,
				'label' => 'Some label'
			],
			[],
		];
		$result = $this->_targetObject->getResultMessage($info, 'type_result');
		$resultMessages = [
			' * Some label: 2 ' . __dn('cake_ldap', 'record', 'records', 2),
		];
		$expected = __d('cake_ldap', 'Result of synchronization') . ' type_result' . "\n" . implode("\n", $resultMessages);
		$this->assertData($expected, $result);
	}
}
