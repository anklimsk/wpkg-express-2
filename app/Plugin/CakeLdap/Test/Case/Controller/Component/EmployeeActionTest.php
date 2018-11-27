<?php
App::uses('AppCakeTestCase', 'CakeLdap.Test');
App::uses('ComponentCollection', 'Controller');
App::uses('Controller', 'Controller');
App::uses('Component', 'Controller');
App::uses('CakeRequest', 'Network');
App::uses('CakeResponse', 'Network');
App::uses('CakeSession', 'Model/Datasource');
App::uses('Hash', 'Utility');
App::uses('EmployeeActionComponent', 'CakeLdap.Controller/Component');

/**
 * EmployeesTestController class
 *
 */
class EmployeesTestController extends Controller {

/**
 * Array containing the names of components this controller uses. Component names
 * should not contain the "Component" portion of the class name.
 *
 * @var array
 * @link http://book.cakephp.org/2.0/en/controllers/components.html
 */
	public $components = [
			'RequestHandler',
			'Paginator',
			'Flash',
			'CakeTheme.Filter',
	];

/**
 * An array containing the class names of models this controller uses.
 *
 * @var mixed
 * @link http://book.cakephp.org/2.0/en/controllers.html#components-helpers-and-uses
 */
	public $uses = [
		'CakeLdap.Employee'
	];

/**
 * @var string|array A string or array-based URL pointing to another location within the app,
 *  or an absolute URL for redirect
 */
	public $redirectUrl = null;

/**
 * Redirects to given $url, after turning off $this->autoRender.
 * Script execution is halted after the redirect.
 *
 * @param string|array $url A string or array-based URL pointing to another location within the app,
 *     or an absolute URL
 * @param int|array|null $status HTTP status code (eg: 301). Defaults to 302 when null is passed.
 * @param bool $exit If true, exit() will be called after the redirect
 * @return \Cake\Network\Response|null
 * @triggers Controller.beforeRedirect $this, array($url, $status, $exit)
 * @link http://book.cakephp.org/2.0/en/controllers.html#Controller::redirect
 */
	public function redirect($url, $status = null, $exit = true) {
		$this->redirectUrl = $url;

		return null;
	}

}

/**
 * EmployeeActionComponent Test Case
 */
class EmployeeActionComponentTest extends AppCakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = [
		'core.cake_session',
		'plugin.cake_ldap.department',
		'plugin.cake_ldap.employee',
		'plugin.cake_ldap.employee_ldap',
		'plugin.cake_ldap.othermobile',
		'plugin.cake_ldap.othertelephone',
		'plugin.cake_ldap.subordinate',
		'plugin.queue.queued_task'
	];

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->EmployeeAction);
		CakeSession::destroy();
		parent::tearDown();
	}

/**
 * testActionIndexFilterConditionsInvalidData method
 *
 * @return void
 */
	public function testActionIndexFilterConditionsInvalidData() {
		$this->_createComponet('/cake_ldap/employees_test/index');
		$this->EmployeeAction->initialize($this->Controller);
		$this->EmployeeAction->actionIndex([CAKE_LDAP_LDAP_ATTRIBUTE_PHOTO, CAKE_LDAP_LDAP_ATTRIBUTE_COMPANY]);
		$result = $this->Controller->viewVars;
		$expected = [
			'employees' => [
				[
					'Employee' => [
						'id' => '8',
						'manager_id' => null,
						CAKE_LDAP_LDAP_ATTRIBUTE_OBJECT_GUID => '9d6cf30f-a579-4cbc-8dd1-c92a43b65aaf',
						CAKE_LDAP_LDAP_ATTRIBUTE_DISTINGUISHED_NAME => 'CN=Голубев Е.В.,OU=АТО,OU=Пользователи,DC=fabrikam,DC=com',
						CAKE_LDAP_LDAP_ATTRIBUTE_NAME => 'Голубев Е.В.',
						CAKE_LDAP_LDAP_ATTRIBUTE_DISPLAY_NAME => 'Голубев Е.В.',
						CAKE_LDAP_LDAP_ATTRIBUTE_INITIALS => 'Е.В.',
						CAKE_LDAP_LDAP_ATTRIBUTE_SURNAME => 'Голубев',
						CAKE_LDAP_LDAP_ATTRIBUTE_GIVEN_NAME => 'Егор',
						CAKE_LDAP_LDAP_ATTRIBUTE_MIDDLE_NAME => 'Владимирович',
						CAKE_LDAP_LDAP_ATTRIBUTE_TITLE => 'Водитель',
						CAKE_LDAP_LDAP_ATTRIBUTE_DIVISION => '',
						CAKE_LDAP_LDAP_ATTRIBUTE_TELEPHONE_NUMBER => '',
						CAKE_LDAP_LDAP_ATTRIBUTE_MOBILE_TELEPHONE_NUMBER => '+375295000005',
						CAKE_LDAP_LDAP_ATTRIBUTE_OFFICE_NAME => 'Гараж',
						CAKE_LDAP_LDAP_ATTRIBUTE_MAIL => 'e.golubev@fabrikam.com',
						CAKE_LDAP_LDAP_ATTRIBUTE_COMPUTER => '',
						CAKE_LDAP_LDAP_ATTRIBUTE_EMPLOYEE_ID => '1282',
						CAKE_LDAP_LDAP_ATTRIBUTE_BIRTHDAY => '1950-12-14',
						CAKE_LDAP_LDAP_ATTRIBUTE_SIP_PHONE => '',
						'block' => false,
					],
					'Department' => [
						'id' => '5',
						'value' => 'АТО',
						'block' => false,
					],
					'Manager' => [
						'id' => null,
						CAKE_LDAP_LDAP_ATTRIBUTE_NAME => null,
						CAKE_LDAP_LDAP_ATTRIBUTE_TITLE => null,
					],
					'Othertelephone' => [],
					'Othermobile' => [
						[
							'id' => '4',
							'value' => '+375291000004',
							'employee_id' => '8',
						],
					]
				],
				[
					'Employee' => [
						'id' => '4',
						'manager_id' => '8',
						CAKE_LDAP_LDAP_ATTRIBUTE_OBJECT_GUID => 'd4bd663f-37da-4737-bfd8-e6442e723722',
						CAKE_LDAP_LDAP_ATTRIBUTE_DISTINGUISHED_NAME => 'CN=Дементьева А.С.,OU=20-02,OU=ОИТ,OU=Пользователи,DC=fabrikam,DC=com',
						CAKE_LDAP_LDAP_ATTRIBUTE_NAME => 'Дементьева А.С.',
						CAKE_LDAP_LDAP_ATTRIBUTE_DISPLAY_NAME => 'Дементьева А.С.',
						CAKE_LDAP_LDAP_ATTRIBUTE_INITIALS => 'А.С.',
						CAKE_LDAP_LDAP_ATTRIBUTE_SURNAME => 'Дементьева',
						CAKE_LDAP_LDAP_ATTRIBUTE_GIVEN_NAME => 'Анна',
						CAKE_LDAP_LDAP_ATTRIBUTE_MIDDLE_NAME => 'Сергеевна',
						CAKE_LDAP_LDAP_ATTRIBUTE_TITLE => 'Инженер',
						CAKE_LDAP_LDAP_ATTRIBUTE_DIVISION => 'Сектор автоматизированной обработки информации',
						CAKE_LDAP_LDAP_ATTRIBUTE_TELEPHONE_NUMBER => '247',
						CAKE_LDAP_LDAP_ATTRIBUTE_MOBILE_TELEPHONE_NUMBER => '',
						CAKE_LDAP_LDAP_ATTRIBUTE_OFFICE_NAME => '123',
						CAKE_LDAP_LDAP_ATTRIBUTE_MAIL => 'a.dementeva@fabrikam.com',
						CAKE_LDAP_LDAP_ATTRIBUTE_COMPUTER => '',
						CAKE_LDAP_LDAP_ATTRIBUTE_EMPLOYEE_ID => '',
						CAKE_LDAP_LDAP_ATTRIBUTE_BIRTHDAY => '1991-11-07',
						CAKE_LDAP_LDAP_ATTRIBUTE_SIP_PHONE => '501247',
						'block' => false,
					],
					'Department' => [
						'id' => '3',
						'value' => 'ОИТ',
						'block' => false,
					],
					'Manager' => [
						'id' => '8',
						CAKE_LDAP_LDAP_ATTRIBUTE_NAME => 'Голубев Е.В.',
						CAKE_LDAP_LDAP_ATTRIBUTE_TITLE => 'Водитель',
					],
					'Othertelephone' => [],
					'Othermobile' => [
						[
							'id' => '3',
							'value' => '+375291000003',
							'employee_id' => '4',
						]
					]
				],
				[
					'Employee' => [
						'id' => '2',
						'manager_id' => '3',
						CAKE_LDAP_LDAP_ATTRIBUTE_OBJECT_GUID => '0010b7b8-d69a-4365-81ca-5f975584fe5c',
						CAKE_LDAP_LDAP_ATTRIBUTE_DISTINGUISHED_NAME => 'CN=Егоров Т.Г.,OU=14-01,OU=ОС,OU=Пользователи,DC=fabrikam,DC=com',
						CAKE_LDAP_LDAP_ATTRIBUTE_NAME => 'Егоров Т.Г.',
						CAKE_LDAP_LDAP_ATTRIBUTE_DISPLAY_NAME => 'Егоров Т.Г.',
						CAKE_LDAP_LDAP_ATTRIBUTE_INITIALS => 'Т.Г.',
						CAKE_LDAP_LDAP_ATTRIBUTE_SURNAME => 'Егоров',
						CAKE_LDAP_LDAP_ATTRIBUTE_GIVEN_NAME => 'Тимофей',
						CAKE_LDAP_LDAP_ATTRIBUTE_MIDDLE_NAME => 'Геннадьевич',
						CAKE_LDAP_LDAP_ATTRIBUTE_TITLE => 'Ведущий инженер',
						CAKE_LDAP_LDAP_ATTRIBUTE_DIVISION => 'Группа связи №1',
						CAKE_LDAP_LDAP_ATTRIBUTE_TELEPHONE_NUMBER => '261',
						CAKE_LDAP_LDAP_ATTRIBUTE_MOBILE_TELEPHONE_NUMBER => '',
						CAKE_LDAP_LDAP_ATTRIBUTE_OFFICE_NAME => '504',
						CAKE_LDAP_LDAP_ATTRIBUTE_MAIL => 't.egorov@fabrikam.com',
						CAKE_LDAP_LDAP_ATTRIBUTE_COMPUTER => 'pc0390',
						CAKE_LDAP_LDAP_ATTRIBUTE_EMPLOYEE_ID => '1631',
						CAKE_LDAP_LDAP_ATTRIBUTE_BIRTHDAY => '1996-07-27',
						CAKE_LDAP_LDAP_ATTRIBUTE_SIP_PHONE => '501261',
						'block' => false
					],
					'Department' => [
						'id' => '2',
						'value' => 'ОС',
						'block' => false,
					],
					'Manager' => [
						'id' => '3',
						CAKE_LDAP_LDAP_ATTRIBUTE_NAME => 'Суханова Л.Б.',
						CAKE_LDAP_LDAP_ATTRIBUTE_TITLE => 'Зам. начальника отдела - главный специалист',
					],
					'Othertelephone' => [
						[
							'id' => '2',
							'value' => '+375171000002',
							'employee_id' => '2',
						]
					],
					'Othermobile' => []
				],
				[
					'Employee' => [
						'id' => '6',
						'manager_id' => '7',
						CAKE_LDAP_LDAP_ATTRIBUTE_OBJECT_GUID => '81817f32-44a7-4b4a-8eff-b837ba387077',
						CAKE_LDAP_LDAP_ATTRIBUTE_DISTINGUISHED_NAME => 'CN=Козловская Е.М.,OU=20-02,OU=ОИТ,OU=Пользователи,DC=fabrikam,DC=com',
						CAKE_LDAP_LDAP_ATTRIBUTE_NAME => 'Козловская Е.М.',
						CAKE_LDAP_LDAP_ATTRIBUTE_DISPLAY_NAME => 'Козловская Е.М.',
						CAKE_LDAP_LDAP_ATTRIBUTE_INITIALS => 'Е.М.',
						CAKE_LDAP_LDAP_ATTRIBUTE_SURNAME => 'Козловская',
						CAKE_LDAP_LDAP_ATTRIBUTE_GIVEN_NAME => 'Евгения',
						CAKE_LDAP_LDAP_ATTRIBUTE_MIDDLE_NAME => 'Михайловна',
						CAKE_LDAP_LDAP_ATTRIBUTE_TITLE => 'Заведующий сектором',
						CAKE_LDAP_LDAP_ATTRIBUTE_DIVISION => 'Сектор автоматизированной обработки информации',
						CAKE_LDAP_LDAP_ATTRIBUTE_TELEPHONE_NUMBER => '302',
						CAKE_LDAP_LDAP_ATTRIBUTE_MOBILE_TELEPHONE_NUMBER => '+375295000003',
						CAKE_LDAP_LDAP_ATTRIBUTE_OFFICE_NAME => '114',
						CAKE_LDAP_LDAP_ATTRIBUTE_MAIL => 'e.kozlovskaya@fabrikam.com',
						CAKE_LDAP_LDAP_ATTRIBUTE_COMPUTER => 'pc0185',
						CAKE_LDAP_LDAP_ATTRIBUTE_EMPLOYEE_ID => '1302',
						CAKE_LDAP_LDAP_ATTRIBUTE_BIRTHDAY => '1996-01-28',
						CAKE_LDAP_LDAP_ATTRIBUTE_SIP_PHONE => '501302',
						'block' => false
					],
					'Department' => [
						'id' => '3',
						'value' => 'ОИТ',
						'block' => false,
					],
					'Manager' => [
						'id' => '7',
						CAKE_LDAP_LDAP_ATTRIBUTE_NAME => 'Хвощинский В.В.',
						CAKE_LDAP_LDAP_ATTRIBUTE_TITLE => 'Начальник отдела',
					],
					'Othertelephone' => [
						[
							'id' => '7',
							'value' => '+375171000007',
							'employee_id' => '6',
						]
					],
					'Othermobile' => []
				],
				[
					'Employee' => [
						'id' => '9',
						'manager_id' => null,
						CAKE_LDAP_LDAP_ATTRIBUTE_OBJECT_GUID => 'b3ec524a-69d0-4fce-b9c2-3b59956cfa25',
						CAKE_LDAP_LDAP_ATTRIBUTE_DISTINGUISHED_NAME => 'CN=Марчук А.М.,OU=Пользователи,DC=fabrikam,DC=com',
						CAKE_LDAP_LDAP_ATTRIBUTE_NAME => 'Марчук А.М.',
						CAKE_LDAP_LDAP_ATTRIBUTE_DISPLAY_NAME => 'Марчук А.М.',
						CAKE_LDAP_LDAP_ATTRIBUTE_INITIALS => 'А.М.',
						CAKE_LDAP_LDAP_ATTRIBUTE_SURNAME => 'Марчук',
						CAKE_LDAP_LDAP_ATTRIBUTE_GIVEN_NAME => 'Анатолий',
						CAKE_LDAP_LDAP_ATTRIBUTE_MIDDLE_NAME => 'Михайлович',
						CAKE_LDAP_LDAP_ATTRIBUTE_TITLE => 'Ведущий инженер по охране труда',
						CAKE_LDAP_LDAP_ATTRIBUTE_DIVISION => '',
						CAKE_LDAP_LDAP_ATTRIBUTE_TELEPHONE_NUMBER => '311',
						CAKE_LDAP_LDAP_ATTRIBUTE_MOBILE_TELEPHONE_NUMBER => '',
						CAKE_LDAP_LDAP_ATTRIBUTE_OFFICE_NAME => '219',
						CAKE_LDAP_LDAP_ATTRIBUTE_MAIL => 'a.marchuk@fabrikam.com',
						CAKE_LDAP_LDAP_ATTRIBUTE_COMPUTER => 'pc0320',
						CAKE_LDAP_LDAP_ATTRIBUTE_EMPLOYEE_ID => '1855',
						CAKE_LDAP_LDAP_ATTRIBUTE_BIRTHDAY => '1996-11-06',
						CAKE_LDAP_LDAP_ATTRIBUTE_SIP_PHONE => '501311',
						'block' => true
					],
					'Department' => [
						'id' => '6',
						'value' => 'Охрана труда',
						'block' => false,
					],
					'Manager' => [
						'id' => null,
						CAKE_LDAP_LDAP_ATTRIBUTE_NAME => null,
						CAKE_LDAP_LDAP_ATTRIBUTE_TITLE => null,
					],
					'Othertelephone' => [],
					'Othermobile' => [],
				],
				[
					'Employee' => [
						'id' => '5',
						'manager_id' => null,
						CAKE_LDAP_LDAP_ATTRIBUTE_OBJECT_GUID => '0400f8f5-6cba-4f1e-8471-fa6e73415673',
						CAKE_LDAP_LDAP_ATTRIBUTE_DISTINGUISHED_NAME => 'CN=Матвеев Р.М.,OU=Пользователи,DC=fabrikam,DC=com',
						CAKE_LDAP_LDAP_ATTRIBUTE_NAME => 'Матвеев Р.М.',
						CAKE_LDAP_LDAP_ATTRIBUTE_DISPLAY_NAME => 'Матвеев Р.М.',
						CAKE_LDAP_LDAP_ATTRIBUTE_INITIALS => 'Р.М.',
						CAKE_LDAP_LDAP_ATTRIBUTE_SURNAME => 'Матвеев',
						CAKE_LDAP_LDAP_ATTRIBUTE_GIVEN_NAME => 'Руслан',
						CAKE_LDAP_LDAP_ATTRIBUTE_MIDDLE_NAME => 'Михайлович',
						CAKE_LDAP_LDAP_ATTRIBUTE_TITLE => 'Ведущий инженер',
						CAKE_LDAP_LDAP_ATTRIBUTE_DIVISION => 'Группа №3',
						CAKE_LDAP_LDAP_ATTRIBUTE_TELEPHONE_NUMBER => '292',
						CAKE_LDAP_LDAP_ATTRIBUTE_MOBILE_TELEPHONE_NUMBER => '+375295000002',
						CAKE_LDAP_LDAP_ATTRIBUTE_OFFICE_NAME => '407',
						CAKE_LDAP_LDAP_ATTRIBUTE_MAIL => 'r.matveev@fabrikam.com',
						CAKE_LDAP_LDAP_ATTRIBUTE_COMPUTER => 'pc0276',
						CAKE_LDAP_LDAP_ATTRIBUTE_EMPLOYEE_ID => '6058',
						CAKE_LDAP_LDAP_ATTRIBUTE_BIRTHDAY => '1996-03-03',
						CAKE_LDAP_LDAP_ATTRIBUTE_SIP_PHONE => '501292',
						'block' => false
					],
					'Department' => [
						'id' => '4',
						'value' => 'ОРС',
						'block' => false,
					],
					'Manager' => [
						'id' => null,
						CAKE_LDAP_LDAP_ATTRIBUTE_NAME => null,
						CAKE_LDAP_LDAP_ATTRIBUTE_TITLE => null,
					],
					'Othertelephone' => [
						[
							'id' => '6',
							'value' => '+375171000006',
							'employee_id' => '5',
						]
					],
					'Othermobile' => []
				],
				[
					'Employee' => [
						'id' => '1',
						'manager_id' => null,
						CAKE_LDAP_LDAP_ATTRIBUTE_OBJECT_GUID => '1dde2cdc-5264-4286-9273-4a88b230237c',
						CAKE_LDAP_LDAP_ATTRIBUTE_DISTINGUISHED_NAME => 'CN=Миронов В.М.,OU=12-05,OU=УИЗ,OU=Пользователи,DC=fabrikam,DC=com',
						CAKE_LDAP_LDAP_ATTRIBUTE_NAME => 'Миронов В.М.',
						CAKE_LDAP_LDAP_ATTRIBUTE_DISPLAY_NAME => 'Миронов В.М.',
						CAKE_LDAP_LDAP_ATTRIBUTE_INITIALS => 'В.М.',
						CAKE_LDAP_LDAP_ATTRIBUTE_SURNAME => 'Миронов',
						CAKE_LDAP_LDAP_ATTRIBUTE_GIVEN_NAME => 'Вячеслав',
						CAKE_LDAP_LDAP_ATTRIBUTE_MIDDLE_NAME => 'Миронович',
						CAKE_LDAP_LDAP_ATTRIBUTE_TITLE => 'Ведущий геолог',
						CAKE_LDAP_LDAP_ATTRIBUTE_DIVISION => 'Геологический отдел (ГО)',
						CAKE_LDAP_LDAP_ATTRIBUTE_TELEPHONE_NUMBER => '380',
						CAKE_LDAP_LDAP_ATTRIBUTE_MOBILE_TELEPHONE_NUMBER => '',
						CAKE_LDAP_LDAP_ATTRIBUTE_OFFICE_NAME => '214',
						CAKE_LDAP_LDAP_ATTRIBUTE_MAIL => 'v.mironov@fabrikam.com',
						CAKE_LDAP_LDAP_ATTRIBUTE_COMPUTER => '',
						CAKE_LDAP_LDAP_ATTRIBUTE_EMPLOYEE_ID => '8060',
						CAKE_LDAP_LDAP_ATTRIBUTE_BIRTHDAY => '2015-07-20',
						CAKE_LDAP_LDAP_ATTRIBUTE_SIP_PHONE => '50380',
						'block' => false
					],
					'Department' => [
						'id' => '1',
						'value' => 'УИЗ',
						'block' => false,
					],
					'Manager' => [
						'id' => null,
						CAKE_LDAP_LDAP_ATTRIBUTE_NAME => null,
						CAKE_LDAP_LDAP_ATTRIBUTE_TITLE => null,
					],
					'Othertelephone' => [
						[
							'id' => '1',
							'value' => '+375171000001',
							'employee_id' => '1',
						]
					],
					'Othermobile' => [
						[
							'id' => '1',
							'value' => '+375291000001',
							'employee_id' => '1',
						],
						[
							'id' => '2',
							'value' => '+375291000002',
							'employee_id' => '1',
						]
					]
				],
				[
					'Employee' => [
						'id' => '3',
						'manager_id' => null,
						CAKE_LDAP_LDAP_ATTRIBUTE_OBJECT_GUID => 'dd518c55-35ce-4a5c-85c5-b5fb762220bf',
						CAKE_LDAP_LDAP_ATTRIBUTE_DISTINGUISHED_NAME => 'CN=Суханова Л.Б.,OU=ОС,OU=Пользователи,DC=fabrikam,DC=com',
						CAKE_LDAP_LDAP_ATTRIBUTE_NAME => 'Суханова Л.Б.',
						CAKE_LDAP_LDAP_ATTRIBUTE_DISPLAY_NAME => 'Суханова Л.Б.',
						CAKE_LDAP_LDAP_ATTRIBUTE_INITIALS => 'Л.Б.',
						CAKE_LDAP_LDAP_ATTRIBUTE_SURNAME => 'Суханова',
						CAKE_LDAP_LDAP_ATTRIBUTE_GIVEN_NAME => 'Лариса',
						CAKE_LDAP_LDAP_ATTRIBUTE_MIDDLE_NAME => 'Борисовна',
						CAKE_LDAP_LDAP_ATTRIBUTE_TITLE => 'Зам. начальника отдела - главный специалист',
						CAKE_LDAP_LDAP_ATTRIBUTE_DIVISION => '',
						CAKE_LDAP_LDAP_ATTRIBUTE_TELEPHONE_NUMBER => '203',
						CAKE_LDAP_LDAP_ATTRIBUTE_MOBILE_TELEPHONE_NUMBER => '+375295000001',
						CAKE_LDAP_LDAP_ATTRIBUTE_OFFICE_NAME => '507',
						CAKE_LDAP_LDAP_ATTRIBUTE_MAIL => 'l.suhanova@fabrikam.com',
						CAKE_LDAP_LDAP_ATTRIBUTE_COMPUTER => 'pc0216',
						CAKE_LDAP_LDAP_ATTRIBUTE_EMPLOYEE_ID => '1602',
						CAKE_LDAP_LDAP_ATTRIBUTE_BIRTHDAY => '1996-08-16',
						CAKE_LDAP_LDAP_ATTRIBUTE_SIP_PHONE => '501203',
						'block' => false
					],
					'Department' => [
						'id' => '2',
						'value' => 'ОС',
						'block' => false,
					],
					'Manager' => [
						'id' => null,
						CAKE_LDAP_LDAP_ATTRIBUTE_NAME => null,
						CAKE_LDAP_LDAP_ATTRIBUTE_TITLE => null,
					],
					'Othertelephone' => [
						[
							'id' => '3',
							'value' => '+375171000003',
							'employee_id' => '3',
						],
						[
							'id' => '4',
							'value' => '+375171000004',
							'employee_id' => '3',
						],
						[
							'id' => '5',
							'value' => '+375171000005',
							'employee_id' => '3',
						]
					],
					'Othermobile' => []
				],
				[
					'Employee' => [
						'id' => '7',
						'manager_id' => '4',
						CAKE_LDAP_LDAP_ATTRIBUTE_OBJECT_GUID => '8c149661-7215-47de-b40e-35320a1ea508',
						CAKE_LDAP_LDAP_ATTRIBUTE_DISTINGUISHED_NAME => 'CN=Хвощинский В.В.,OU=ОИТ,OU=Пользователи,DC=fabrikam,DC=com',
						CAKE_LDAP_LDAP_ATTRIBUTE_NAME => 'Хвощинский В.В.',
						CAKE_LDAP_LDAP_ATTRIBUTE_DISPLAY_NAME => 'Хвощинский В.В.',
						CAKE_LDAP_LDAP_ATTRIBUTE_INITIALS => 'В.В.',
						CAKE_LDAP_LDAP_ATTRIBUTE_SURNAME => 'Хвощинский',
						CAKE_LDAP_LDAP_ATTRIBUTE_GIVEN_NAME => 'Виктор',
						CAKE_LDAP_LDAP_ATTRIBUTE_MIDDLE_NAME => 'Владимирович',
						CAKE_LDAP_LDAP_ATTRIBUTE_TITLE => 'Начальник отдела',
						CAKE_LDAP_LDAP_ATTRIBUTE_DIVISION => '',
						CAKE_LDAP_LDAP_ATTRIBUTE_TELEPHONE_NUMBER => '320',
						CAKE_LDAP_LDAP_ATTRIBUTE_MOBILE_TELEPHONE_NUMBER => '+375295000004',
						CAKE_LDAP_LDAP_ATTRIBUTE_OFFICE_NAME => '217',
						CAKE_LDAP_LDAP_ATTRIBUTE_MAIL => 'v.hvoshchinskiy@fabrikam.com',
						CAKE_LDAP_LDAP_ATTRIBUTE_COMPUTER => 'pc0386',
						CAKE_LDAP_LDAP_ATTRIBUTE_EMPLOYEE_ID => '1304',
						CAKE_LDAP_LDAP_ATTRIBUTE_BIRTHDAY => '1996-08-08',
						CAKE_LDAP_LDAP_ATTRIBUTE_SIP_PHONE => '501321',
						'block' => false
					],
					'Department' => [
						'id' => '3',
						'value' => 'ОИТ',
						'block' => false,
					],
					'Manager' => [
						'id' => '4',
						CAKE_LDAP_LDAP_ATTRIBUTE_NAME => 'Дементьева А.С.',
						CAKE_LDAP_LDAP_ATTRIBUTE_TITLE => 'Инженер',
					],
					'Othertelephone' => [
						[
							'id' => '8',
							'value' => '+375171000008',
							'employee_id' => '7',
						],
						[
							'id' => '9',
							'value' => '+375171000009',
							'employee_id' => '7',
						]
					],
					'Othermobile' => []
				],
				[
					'Employee' => [
						'id' => '10',
						'manager_id' => null,
						CAKE_LDAP_LDAP_ATTRIBUTE_OBJECT_GUID => '971327c0-0863-4c83-8e57-91007b506e5d',
						CAKE_LDAP_LDAP_ATTRIBUTE_DISTINGUISHED_NAME => 'CN=Чижов Я.С.,OU=09-02,OU=ОРС,OU=Пользователи,DC=fabrikam,DC=com',
						CAKE_LDAP_LDAP_ATTRIBUTE_NAME => 'Чижов Я.С.',
						CAKE_LDAP_LDAP_ATTRIBUTE_DISPLAY_NAME => 'Чижов Я.С.',
						CAKE_LDAP_LDAP_ATTRIBUTE_INITIALS => 'Я.С.',
						CAKE_LDAP_LDAP_ATTRIBUTE_SURNAME => 'Чижов',
						CAKE_LDAP_LDAP_ATTRIBUTE_GIVEN_NAME => 'Ярослав',
						CAKE_LDAP_LDAP_ATTRIBUTE_MIDDLE_NAME => 'Сергеевич',
						CAKE_LDAP_LDAP_ATTRIBUTE_TITLE => 'Инженер 1 категории',
						CAKE_LDAP_LDAP_ATTRIBUTE_DIVISION => 'Группа №1',
						CAKE_LDAP_LDAP_ATTRIBUTE_TELEPHONE_NUMBER => '256',
						CAKE_LDAP_LDAP_ATTRIBUTE_MOBILE_TELEPHONE_NUMBER => '+375295000006',
						CAKE_LDAP_LDAP_ATTRIBUTE_OFFICE_NAME => '410',
						CAKE_LDAP_LDAP_ATTRIBUTE_MAIL => 'y.chizhov@fabrikam.com',
						CAKE_LDAP_LDAP_ATTRIBUTE_COMPUTER => 'pc0076',
						CAKE_LDAP_LDAP_ATTRIBUTE_EMPLOYEE_ID => '6079',
						CAKE_LDAP_LDAP_ATTRIBUTE_BIRTHDAY => '1984-09-06',
						CAKE_LDAP_LDAP_ATTRIBUTE_SIP_PHONE => '501256',
						'block' => false
					],
					'Department' => [
						'id' => '4',
						'value' => 'ОРС',
						'block' => false,
					],
					'Manager' => [
						'id' => null,
						CAKE_LDAP_LDAP_ATTRIBUTE_NAME => null,
						CAKE_LDAP_LDAP_ATTRIBUTE_TITLE => null,
					],
					'Othertelephone' => [
						[
							'id' => '10',
							'value' => '+375171000010',
							'employee_id' => '10',
						]
					],
					'Othermobile' => []
				]
			],
			'filterOptions' => [
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_NAME => [
					'label' => __d('cake_ldap_field_name', 'Full name'),
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_DISPLAY_NAME => [
					'label' => __d('cake_ldap_field_name', 'Displ. name'),
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_SURNAME => [
					'label' => __d('cake_ldap_field_name', 'Surn.'),
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_GIVEN_NAME => [
					'label' => __d('cake_ldap_field_name', 'Giv. name'),
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_MIDDLE_NAME => [
					'label' => __d('cake_ldap_field_name', 'Mid. name'),
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_MAIL => [
					'label' => __d('cake_ldap_field_name', 'E-mail'),
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_SIP_PHONE => [
					'label' => __d('cake_ldap_field_name', 'SIP tel.'),
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_TELEPHONE_NUMBER => [
					'label' => __d('cake_ldap_field_name', 'Tel.'),
				],
				'Othertelephone.{n}.value' => [
					'label' => __d('cake_ldap_field_name', 'Other tel.'),
					'disabled' => true
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_MOBILE_TELEPHONE_NUMBER => [
					'label' => __d('cake_ldap_field_name', 'Mob. tel.'),
				],
				'Othermobile.{n}.value' => [
					'label' => __d('cake_ldap_field_name', 'Other mob. tel.'),
					'disabled' => true
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_OFFICE_NAME => [
					'label' => __d('cake_ldap_field_name', 'Office'),
				],
				'Department.value' => [
					'label' => __d('cake_ldap_field_name', 'Depart.'),
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_DIVISION => [
					'label' => __d('cake_ldap_field_name', 'Subdiv.'),
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_TITLE => [
					'label' => __d('cake_ldap_field_name', 'Pos.'),
				],
				'Manager.' . CAKE_LDAP_LDAP_ATTRIBUTE_NAME => [
					'label' => __d('cake_ldap_field_name', 'Manag.'),
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_BIRTHDAY => [
					'label' => __d('cake_ldap_field_name', 'Birthd.'),
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_COMPUTER => [
					'label' => __d('cake_ldap_field_name', 'Comp.'),
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_EMPLOYEE_ID => [
					'label' => __d('cake_ldap_field_name', 'Empl. ID'),
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_PHOTO => [
					'label' => __d('cake_ldap_field_name', 'Photo'),
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_COMPANY => [
					'label' => __d('cake_ldap_field_name', 'Comp. name'),
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_INITIALS => [
					'label' => __d('cake_ldap_field_name', 'Init.'),
				],
				'Employee.block' => [
					'label' => __d('cake_ldap_field_name', 'Block'),
				]
			],
			'fieldsConfig' => [
				'Employee.id' => [
					'type' => 'integer',
					'truncate' => false
				],
				'Employee.department_id' => [
					'type' => 'integer',
					'truncate' => false
				],
				'Employee.manager_id' => [
					'type' => 'integer',
					'truncate' => false
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_OBJECT_GUID => [
					'type' => 'guid',
					'truncate' => false
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_DISTINGUISHED_NAME => [
					'type' => 'string',
					'truncate' => false
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_NAME => [
					'type' => 'string',
					'truncate' => false
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_DISPLAY_NAME => [
					'type' => 'string',
					'truncate' => false
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_INITIALS => [
					'type' => 'string',
					'truncate' => false
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_SURNAME => [
					'type' => 'string',
					'truncate' => false
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_GIVEN_NAME => [
					'type' => 'string',
					'truncate' => false
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_MIDDLE_NAME => [
					'type' => 'string',
					'truncate' => false
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_TITLE => [
					'type' => 'string',
					'truncate' => true
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_DIVISION => [
					'type' => 'string',
					'truncate' => true
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_TELEPHONE_NUMBER => [
					'type' => 'string',
					'truncate' => false
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_MOBILE_TELEPHONE_NUMBER => [
					'type' => 'string',
					'truncate' => false
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_OFFICE_NAME => [
					'type' => 'string',
					'truncate' => false
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_MAIL => [
					'type' => 'mail',
					'truncate' => false
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_PHOTO => [
					'type' => 'photo',
					'truncate' => false
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_COMPUTER => [
					'type' => 'string',
					'truncate' => true
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_EMPLOYEE_ID => [
					'type' => 'string',
					'truncate' => false
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_COMPANY => [
					'type' => 'string',
					'truncate' => true
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_BIRTHDAY => [
					'type' => 'date',
					'truncate' => false
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_SIP_PHONE => [
					'type' => 'string',
					'truncate' => false
				],
				'Employee.block' => [
					'type' => 'boolean',
					'truncate' => false
				],
				'Department.value' => [
					'type' => 'string',
					'truncate' => true
				],
				'Othertelephone.{n}.value' => [
					'type' => 'string',
					'truncate' => false
				],
				'Othermobile.{n}.value' => [
					'type' => 'string',
					'truncate' => false
				],
				'Manager.' . CAKE_LDAP_LDAP_ATTRIBUTE_NAME => [
					'type' => 'manager',
					'truncate' => false
				],
				'Subordinate.{n}' => [
					'type' => 'element',
					'truncate' => false
				]
			],
			'isTreeReady' => true,
			'pageHeader' => __d('cake_ldap', 'Index of employees'),
			'headerMenuActions' => [
				[
					'fas fa-sync-alt',
					__d('cake_ldap', 'Synchronize information with LDAP server'),
					['controller' => 'employees', 'action' => 'sync', 'prefix' => false],
					[
						'title' => __d('cake_ldap', 'Synchronize information of employees with LDAP server'),
						'data-toggle' => 'request-only',
					]
				],
				[
					'fas fa-sitemap',
					__d('cake_ldap', 'Tree of employees'),
					['controller' => 'employees', 'action' => 'tree', 'prefix' => false],
					['title' => __d('cake_ldap', 'Tree view of employees')]
				]
			],
			'breadCrumbs' => [
				[
					__d('cake_ldap', 'Employees'),
					[
						'plugin' => 'cake_ldap',
						'controller' => 'employees',
						'action' => 'index'
					]
				],
				__d('cake_ldap', 'Index')
			]
		];
		$this->assertData($expected, $result);
	}

/**
 * testActionIndexTreeNotReady method
 *
 * @return void
 */
	public function testActionIndexTreeNotReady() {
		Configure::write('CakeLdap.LdapSync.TreeSubordinate.Enable', false);
		$this->_createComponet('/cake_ldap/employees_test/index');
		$this->EmployeeAction->initialize($this->Controller);
		$this->EmployeeAction->actionIndex();
		$result = Hash::get($this->Controller->viewVars, 'isTreeReady');
		$this->assertFalse($result);
	}

/**
 * testActionViewEmptyId method
 *
 * @return void
 */
	public function testActionViewEmptyId() {
		$this->_createComponet('/cake_ldap/employees_test/view');
		$this->EmployeeAction->initialize($this->Controller);
		$this->EmployeeAction->actionView(null);
		$this->checkFlashMessage(__d('cake_ldap', 'Invalid ID for employee'));
		$this->assertNotEmpty($this->Controller->redirectUrl);
	}

/**
 * testActionViewInvalidId method
 *
 * @return void
 */
	public function testActionViewInvalidId() {
		$id = 1000;
		$this->_createComponet('/cake_ldap/employees_test/view/' . $id);
		$this->EmployeeAction->initialize($this->Controller);
		$this->EmployeeAction->actionView($id);
		$this->checkFlashMessage(__d('cake_ldap', 'Invalid ID for employee'));
		$this->assertNotEmpty($this->Controller->redirectUrl);
	}

/**
 * testActionViewInvalidDn method
 *
 * @return void
 */
	public function testActionViewInvalidDn() {
		$dn = 'CN=SomeUser,OU=SomeUnit,DC=fabrikam,DC=com';
		$this->_createComponet('/cake_ldap/employees_test/view/' . $dn);
		$this->EmployeeAction->initialize($this->Controller);
		$this->EmployeeAction->actionView($dn);
		$this->checkFlashMessage(__d('cake_ldap', 'Invalid ID for employee'));
		$this->assertNotEmpty($this->Controller->redirectUrl);
	}

/**
 * testActionViewValidId method
 *
 * @return void
 */
	public function testActionViewValidId() {
		$id = 4;
		$this->_createComponet('/cake_ldap/employees_test/view/' . $id);
		$this->EmployeeAction->initialize($this->Controller);
		$this->EmployeeAction->actionView($id, [CAKE_LDAP_LDAP_ATTRIBUTE_PHOTO, CAKE_LDAP_LDAP_ATTRIBUTE_BIRTHDAY], [CAKE_LDAP_LDAP_ATTRIBUTE_COMPANY]);
		$result = $this->Controller->viewVars;
		$expected = [
			'employee' => [
				'Employee' => [
					'id' => '4',
					'department_id' => '3',
					'manager_id' => '8',
					CAKE_LDAP_LDAP_ATTRIBUTE_OBJECT_GUID => 'd4bd663f-37da-4737-bfd8-e6442e723722',
					CAKE_LDAP_LDAP_ATTRIBUTE_DISTINGUISHED_NAME => 'CN=Дементьева А.С.,OU=20-02,OU=ОИТ,OU=Пользователи,DC=fabrikam,DC=com',
					CAKE_LDAP_LDAP_ATTRIBUTE_NAME => 'Дементьева А.С.',
					CAKE_LDAP_LDAP_ATTRIBUTE_DISPLAY_NAME => 'Дементьева А.С.',
					CAKE_LDAP_LDAP_ATTRIBUTE_INITIALS => 'А.С.',
					CAKE_LDAP_LDAP_ATTRIBUTE_SURNAME => 'Дементьева',
					CAKE_LDAP_LDAP_ATTRIBUTE_GIVEN_NAME => 'Анна',
					CAKE_LDAP_LDAP_ATTRIBUTE_MIDDLE_NAME => 'Сергеевна',
					CAKE_LDAP_LDAP_ATTRIBUTE_TITLE => 'Инженер',
					CAKE_LDAP_LDAP_ATTRIBUTE_DIVISION => 'Сектор автоматизированной обработки информации',
					CAKE_LDAP_LDAP_ATTRIBUTE_TELEPHONE_NUMBER => '247',
					CAKE_LDAP_LDAP_ATTRIBUTE_MOBILE_TELEPHONE_NUMBER => '',
					CAKE_LDAP_LDAP_ATTRIBUTE_OFFICE_NAME => '123',
					CAKE_LDAP_LDAP_ATTRIBUTE_MAIL => 'a.dementeva@fabrikam.com',
					CAKE_LDAP_LDAP_ATTRIBUTE_COMPUTER => '',
					CAKE_LDAP_LDAP_ATTRIBUTE_EMPLOYEE_ID => '',
					CAKE_LDAP_LDAP_ATTRIBUTE_COMPANY => 'ТестОрг',
					CAKE_LDAP_LDAP_ATTRIBUTE_SIP_PHONE => '501247',
					'block' => false
				],
				'Department' => [
					'id' => '3',
					'value' => 'ОИТ',
					'block' => false,
				],
				'Manager' => [
					'id' => '8',
					CAKE_LDAP_LDAP_ATTRIBUTE_NAME => 'Голубев Е.В.',
					CAKE_LDAP_LDAP_ATTRIBUTE_TITLE => 'Водитель',
				],
				'Subordinate' => [
					[
						'SubordinateDb' => [
							'id' => '7',
							'parent_id' => '4',
							'lft' => '11',
							'rght' => '14',
						],
						'Employee' => [
							'id' => '7',
							CAKE_LDAP_LDAP_ATTRIBUTE_NAME => 'Хвощинский В.В.',
							CAKE_LDAP_LDAP_ATTRIBUTE_TITLE => 'Начальник отдела',
						],
						'children' => [
							[
								'SubordinateDb' => [
									'id' => '6',
									'parent_id' => '7',
									'lft' => '12',
									'rght' => '13',
								],
								'Employee' => [
									'id' => '6',
									CAKE_LDAP_LDAP_ATTRIBUTE_NAME => 'Козловская Е.М.',
									CAKE_LDAP_LDAP_ATTRIBUTE_TITLE => 'Заведующий сектором',
								],
								'children' => []
							]
						]
					]
				],
				'Othertelephone' => [],
				'Othermobile' => [
					[
						'id' => '3',
						'value' => '+375291000003',
						'employee_id' => '4',
					]
				]
			],
			'fieldsLabel' => [
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_NAME => __d('cake_ldap_field_name', 'Full name'),
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_DISPLAY_NAME => __d('cake_ldap_field_name', 'Display name'),
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_SURNAME => __d('cake_ldap_field_name', 'Surname'),
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_GIVEN_NAME => __d('cake_ldap_field_name', 'Given name'),
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_MIDDLE_NAME => __d('cake_ldap_field_name', 'Middle name'),
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_MAIL => __d('cake_ldap_field_name', 'E-mail'),
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_SIP_PHONE => __d('cake_ldap_field_name', 'SIP telephone'),
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_TELEPHONE_NUMBER => __d('cake_ldap_field_name', 'Telephone'),
				'Othertelephone.{n}.value' => __d('cake_ldap_field_name', 'Other telephone'),
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_MOBILE_TELEPHONE_NUMBER => __d('cake_ldap_field_name', 'Mobile telephone'),
				'Othermobile.{n}.value' => __d('cake_ldap_field_name', 'Other mobile telephone'),
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_OFFICE_NAME => __d('cake_ldap_field_name', 'Office room'),
				'Department.value' => __d('cake_ldap_field_name', 'Department'),
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_DIVISION => __d('cake_ldap_field_name', 'Subdivision'),
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_TITLE => __d('cake_ldap_field_name', 'Position'),
				'Manager.' . CAKE_LDAP_LDAP_ATTRIBUTE_NAME => __d('cake_ldap_field_name', 'Manager'),
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_BIRTHDAY => __d('cake_ldap_field_name', 'Birthday'),
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_COMPUTER => __d('cake_ldap_field_name', 'Computer'),
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_EMPLOYEE_ID => __d('cake_ldap_field_name', 'Employee ID'),
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_PHOTO => __d('cake_ldap_field_name', 'Photo'),
				'Employee.block' => __d('cake_ldap_field_name', 'Block'),
			],
			'fieldsLabelExtend' => [
				'Subordinate.{n}' => __d('cake_ldap_field_name', 'Subordinate'),
			],
			'fieldsConfig' => [
				'Employee.id' => [
					'type' => 'integer',
					'truncate' => false
				],
				'Employee.department_id' => [
					'type' => 'integer',
					'truncate' => false
				],
				'Employee.manager_id' => [
					'type' => 'integer',
					'truncate' => false
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_OBJECT_GUID => [
					'type' => 'guid',
					'truncate' => false
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_DISTINGUISHED_NAME => [
					'type' => 'string',
					'truncate' => false
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_NAME => [
					'type' => 'string',
					'truncate' => false
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_DISPLAY_NAME => [
					'type' => 'string',
					'truncate' => false
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_INITIALS => [
					'type' => 'string',
					'truncate' => false
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_SURNAME => [
					'type' => 'string',
					'truncate' => false
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_GIVEN_NAME => [
					'type' => 'string',
					'truncate' => false
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_MIDDLE_NAME => [
					'type' => 'string',
					'truncate' => false
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_TITLE => [
					'type' => 'string',
					'truncate' => true
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_DIVISION => [
					'type' => 'string',
					'truncate' => true
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_TELEPHONE_NUMBER => [
					'type' => 'string',
					'truncate' => false
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_MOBILE_TELEPHONE_NUMBER => [
					'type' => 'string',
					'truncate' => false
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_OFFICE_NAME => [
					'type' => 'string',
					'truncate' => false
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_MAIL => [
					'type' => 'mail',
					'truncate' => false
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_PHOTO => [
					'type' => 'photo',
					'truncate' => false
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_COMPUTER => [
					'type' => 'string',
					'truncate' => true
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_EMPLOYEE_ID => [
					'type' => 'string',
					'truncate' => false
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_COMPANY => [
					'type' => 'string',
					'truncate' => true
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_BIRTHDAY => [
					'type' => 'date',
					'truncate' => false
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_SIP_PHONE => [
					'type' => 'string',
					'truncate' => false
				],
				'Employee.block' => [
					'type' => 'boolean',
					'truncate' => false
				],
				'Department.value' => [
					'type' => 'string',
					'truncate' => true
				],
				'Othertelephone.{n}.value' => [
					'type' => 'string',
					'truncate' => false
				],
				'Othermobile.{n}.value' => [
					'type' => 'string',
					'truncate' => false
				],
				'Manager.' . CAKE_LDAP_LDAP_ATTRIBUTE_NAME => [
					'type' => 'manager',
					'truncate' => false
				],
				'Subordinate.{n}' => [
					'type' => 'element',
					'truncate' => false
				],
			],
			'id' => 4,
			'isTreeReady' => true,
			'pageHeader' => __d('cake_ldap', 'Information of employees'),
			'headerMenuActions' => [
				[
					'fas fa-sync-alt',
					__d('cake_ldap', 'Synchronize'),
					['controller' => 'employees', 'action' => 'sync', 'd4bd663f-37da-4737-bfd8-e6442e723722', 'prefix' => false],
					['title' => __d('cake_ldap', 'Synchronize information of this employee with LDAP server')]
				],
				[
					'fas fa-sitemap',
					__d('cake_ldap', 'Tree of subordinate'),
					['controller' => 'employees', 'action' => 'tree', '4', 'prefix' => false],
					['title' => __d('cake_ldap', 'Edit tree of subordinate employee')]
				]
			],
			'breadCrumbs' => [
				[
					__d('cake_ldap', 'Employees'),
					[
						'plugin' => 'cake_ldap',
						'controller' => 'employees',
						'action' => 'index'
					]
				],
				__d('cake_ldap', 'Viewing')
			]
		];
		$this->assertData($expected, $result);
	}

/**
 * testActionViewValidIdPopup method
 *
 * @return void
 */
	public function testActionViewValidIdPopup() {
		$id = 4;
		$this->_createComponet('/cake_ldap/employees_test/view/' . $id . '.pop', 'GET', ['ext' => 'pop']);
		$this->EmployeeAction->initialize($this->Controller);
		$this->EmployeeAction->actionView($id, [CAKE_LDAP_LDAP_ATTRIBUTE_PHOTO, CAKE_LDAP_LDAP_ATTRIBUTE_BIRTHDAY], [CAKE_LDAP_LDAP_ATTRIBUTE_COMPANY]);
		$result = $this->Controller->viewVars;
		$expected = [
			'employee' => [
				'Employee' => [
					'id' => '4',
					'department_id' => '3',
					CAKE_LDAP_LDAP_ATTRIBUTE_OBJECT_GUID => 'd4bd663f-37da-4737-bfd8-e6442e723722',
					CAKE_LDAP_LDAP_ATTRIBUTE_DISTINGUISHED_NAME => 'CN=Дементьева А.С.,OU=20-02,OU=ОИТ,OU=Пользователи,DC=fabrikam,DC=com',
					CAKE_LDAP_LDAP_ATTRIBUTE_NAME => 'Дементьева А.С.',
					CAKE_LDAP_LDAP_ATTRIBUTE_DISPLAY_NAME => 'Дементьева А.С.',
					CAKE_LDAP_LDAP_ATTRIBUTE_INITIALS => 'А.С.',
					CAKE_LDAP_LDAP_ATTRIBUTE_SURNAME => 'Дементьева',
					CAKE_LDAP_LDAP_ATTRIBUTE_GIVEN_NAME => 'Анна',
					CAKE_LDAP_LDAP_ATTRIBUTE_MIDDLE_NAME => 'Сергеевна',
					CAKE_LDAP_LDAP_ATTRIBUTE_TITLE => 'Инженер',
					CAKE_LDAP_LDAP_ATTRIBUTE_DIVISION => 'Сектор автоматизированной обработки информации',
					CAKE_LDAP_LDAP_ATTRIBUTE_TELEPHONE_NUMBER => '247',
					CAKE_LDAP_LDAP_ATTRIBUTE_MOBILE_TELEPHONE_NUMBER => '',
					CAKE_LDAP_LDAP_ATTRIBUTE_OFFICE_NAME => '123',
					CAKE_LDAP_LDAP_ATTRIBUTE_MAIL => 'a.dementeva@fabrikam.com',
					CAKE_LDAP_LDAP_ATTRIBUTE_COMPANY => 'ТестОрг',
					CAKE_LDAP_LDAP_ATTRIBUTE_SIP_PHONE => '501247',
				],
				'Department' => [
					'id' => '3',
					'value' => 'ОИТ',
					'block' => false,
				],
				'Othertelephone' => [],
				'Othermobile' => [
					[
						'id' => '3',
						'value' => '+375291000003',
						'employee_id' => '4'
					]
				]
			],
			'fieldsLabel' => [
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_NAME => __d('cake_ldap_field_name', 'Full name'),
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_DISPLAY_NAME => __d('cake_ldap_field_name', 'Display name'),
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_SURNAME => __d('cake_ldap_field_name', 'Surname'),
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_GIVEN_NAME => __d('cake_ldap_field_name', 'Given name'),
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_MIDDLE_NAME => __d('cake_ldap_field_name', 'Middle name'),
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_MAIL => __d('cake_ldap_field_name', 'E-mail'),
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_SIP_PHONE => __d('cake_ldap_field_name', 'SIP telephone'),
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_TELEPHONE_NUMBER => __d('cake_ldap_field_name', 'Telephone'),
				'Othertelephone.{n}.value' => __d('cake_ldap_field_name', 'Other telephone'),
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_MOBILE_TELEPHONE_NUMBER => __d('cake_ldap_field_name', 'Mobile telephone'),
				'Othermobile.{n}.value' => __d('cake_ldap_field_name', 'Other mobile telephone'),
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_OFFICE_NAME => __d('cake_ldap_field_name', 'Office room'),
				'Department.value' => __d('cake_ldap_field_name', 'Department'),
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_DIVISION => __d('cake_ldap_field_name', 'Subdivision'),
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_TITLE => __d('cake_ldap_field_name', 'Position'),
				'Manager.' . CAKE_LDAP_LDAP_ATTRIBUTE_NAME => __d('cake_ldap_field_name', 'Manager'),
			],
			'fieldsLabelExtend' => [],
			'fieldsConfig' => [
				'Employee.id' => [
					'type' => 'integer',
					'truncate' => false
				],
				'Employee.department_id' => [
					'type' => 'integer',
					'truncate' => false
				],
				'Employee.manager_id' => [
					'type' => 'integer',
					'truncate' => false
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_OBJECT_GUID => [
					'type' => 'guid',
					'truncate' => false
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_DISTINGUISHED_NAME => [
					'type' => 'string',
					'truncate' => false
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_NAME => [
					'type' => 'string',
					'truncate' => false
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_DISPLAY_NAME => [
					'type' => 'string',
					'truncate' => false
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_INITIALS => [
					'type' => 'string',
					'truncate' => false
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_SURNAME => [
					'type' => 'string',
					'truncate' => false
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_GIVEN_NAME => [
					'type' => 'string',
					'truncate' => false
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_MIDDLE_NAME => [
					'type' => 'string',
					'truncate' => false
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_TITLE => [
					'type' => 'string',
					'truncate' => true
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_DIVISION => [
					'type' => 'string',
					'truncate' => true
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_TELEPHONE_NUMBER => [
					'type' => 'string',
					'truncate' => false
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_MOBILE_TELEPHONE_NUMBER => [
					'type' => 'string',
					'truncate' => false
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_OFFICE_NAME => [
					'type' => 'string',
					'truncate' => false
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_MAIL => [
					'type' => 'mail',
					'truncate' => false
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_PHOTO => [
					'type' => 'photo',
					'truncate' => false
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_COMPUTER => [
					'type' => 'string',
					'truncate' => true
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_EMPLOYEE_ID => [
					'type' => 'string',
					'truncate' => false
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_COMPANY => [
					'type' => 'string',
					'truncate' => true
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_BIRTHDAY => [
					'type' => 'date',
					'truncate' => false
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_SIP_PHONE => [
					'type' => 'string',
					'truncate' => false
				],
				'Employee.block' => [
					'type' => 'boolean',
					'truncate' => false
				],
				'Department.value' => [
					'type' => 'string',
					'truncate' => true
				],
				'Othertelephone.{n}.value' => [
					'type' => 'string',
					'truncate' => false
				],
				'Othermobile.{n}.value' => [
					'type' => 'string',
					'truncate' => false
				],
				'Manager.' . CAKE_LDAP_LDAP_ATTRIBUTE_NAME => [
					'type' => 'manager',
					'truncate' => false
				],
				'Subordinate.{n}' => [
					'type' => 'element',
					'truncate' => false
				]
			],
			'id' => 4,
			'isTreeReady' => true,
			'pageHeader' => __d('cake_ldap', 'Information of employees'),
			'headerMenuActions' => [
				[
					'fas fa-sync-alt',
					__d('cake_ldap', 'Synchronize'),
					['controller' => 'employees', 'action' => 'sync', 'd4bd663f-37da-4737-bfd8-e6442e723722', 'prefix' => false],
					['title' => __d('cake_ldap', 'Synchronize information of this employee with LDAP server')]
				],
				[
					'fas fa-sitemap',
					__d('cake_ldap', 'Tree of subordinate'),
					['controller' => 'employees', 'action' => 'tree', '4', 'prefix' => false],
					['title' => __d('cake_ldap', 'Edit tree of subordinate employee')]
				]
			],
			'breadCrumbs' => [
				[
					__d('cake_ldap', 'Employees'),
					[
						'plugin' => 'cake_ldap',
						'controller' => 'employees',
						'action' => 'index'
					]
				],
				__d('cake_ldap', 'Viewing')
			]
		];
		$this->assertData($expected, $result);
	}

/**
 * testActionViewValidDn method
 *
 * @return void
 */
	public function testActionViewValidDn() {
		$dn = 'CN=Суханова Л.Б.,OU=ОС,OU=Пользователи,DC=fabrikam,DC=com';
		$this->_createComponet('/cake_ldap/employees_test/view/' . $dn);
		$this->EmployeeAction->initialize($this->Controller);
		$this->EmployeeAction->actionView($dn, [CAKE_LDAP_LDAP_ATTRIBUTE_TITLE, CAKE_LDAP_LDAP_ATTRIBUTE_COMPUTER, 'id'], [CAKE_LDAP_LDAP_ATTRIBUTE_COMPUTER, CAKE_LDAP_LDAP_ATTRIBUTE_EMPLOYEE_ID]);
		$result = $this->Controller->viewVars;
		$expected = [
			'employee' => [
				'Employee' => [
					'department_id' => '2',
					'manager_id' => null,
					CAKE_LDAP_LDAP_ATTRIBUTE_OBJECT_GUID => 'dd518c55-35ce-4a5c-85c5-b5fb762220bf',
					CAKE_LDAP_LDAP_ATTRIBUTE_DISTINGUISHED_NAME => 'CN=Суханова Л.Б.,OU=ОС,OU=Пользователи,DC=fabrikam,DC=com',
					CAKE_LDAP_LDAP_ATTRIBUTE_NAME => 'Суханова Л.Б.',
					CAKE_LDAP_LDAP_ATTRIBUTE_DISPLAY_NAME => 'Суханова Л.Б.',
					CAKE_LDAP_LDAP_ATTRIBUTE_INITIALS => 'Л.Б.',
					CAKE_LDAP_LDAP_ATTRIBUTE_SURNAME => 'Суханова',
					CAKE_LDAP_LDAP_ATTRIBUTE_GIVEN_NAME => 'Лариса',
					CAKE_LDAP_LDAP_ATTRIBUTE_MIDDLE_NAME => 'Борисовна',
					CAKE_LDAP_LDAP_ATTRIBUTE_DIVISION => '',
					CAKE_LDAP_LDAP_ATTRIBUTE_TELEPHONE_NUMBER => '203',
					CAKE_LDAP_LDAP_ATTRIBUTE_MOBILE_TELEPHONE_NUMBER => '+375295000001',
					CAKE_LDAP_LDAP_ATTRIBUTE_OFFICE_NAME => '507',
					CAKE_LDAP_LDAP_ATTRIBUTE_MAIL => 'l.suhanova@fabrikam.com',
					CAKE_LDAP_LDAP_ATTRIBUTE_PHOTO => base64_decode('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'),
					CAKE_LDAP_LDAP_ATTRIBUTE_EMPLOYEE_ID => '1602',
					CAKE_LDAP_LDAP_ATTRIBUTE_COMPANY => 'ТестОрг',
					CAKE_LDAP_LDAP_ATTRIBUTE_BIRTHDAY => '1996-08-16',
					CAKE_LDAP_LDAP_ATTRIBUTE_SIP_PHONE => '501203',
					'block' => false,
					'id' => '3',
				],
				'Department' => [
					'id' => '2',
					'value' => 'ОС',
					'block' => false,
				],
				'Manager' => [
					'id' => null,
					CAKE_LDAP_LDAP_ATTRIBUTE_NAME => null,
					CAKE_LDAP_LDAP_ATTRIBUTE_TITLE => null,
				],
				'Subordinate' => [
					[
						'SubordinateDb' => [
							'id' => '2',
							'parent_id' => '3',
							'lft' => '4',
							'rght' => '5',
						],
						'Employee' => [
							'id' => '2',
							CAKE_LDAP_LDAP_ATTRIBUTE_NAME => 'Егоров Т.Г.',
							CAKE_LDAP_LDAP_ATTRIBUTE_TITLE => 'Ведущий инженер',
						],
						'children' => []
					]
				],
				'Othertelephone' => [
					[
						'id' => '3',
						'value' => '+375171000003',
						'employee_id' => '3',
					],
					[
						'id' => '4',
						'value' => '+375171000004',
						'employee_id' => '3',
					],
					[
						'id' => '5',
						'value' => '+375171000005',
						'employee_id' => '3',
					]
				],
				'Othermobile' => []
			],
			'fieldsLabel' => [
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_NAME => __d('cake_ldap_field_name', 'Full name'),
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_DISPLAY_NAME => __d('cake_ldap_field_name', 'Display name'),
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_SURNAME => __d('cake_ldap_field_name', 'Surname'),
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_GIVEN_NAME => __d('cake_ldap_field_name', 'Given name'),
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_MIDDLE_NAME => __d('cake_ldap_field_name', 'Middle name'),
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_MAIL => __d('cake_ldap_field_name', 'E-mail'),
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_SIP_PHONE => __d('cake_ldap_field_name', 'SIP telephone'),
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_TELEPHONE_NUMBER => __d('cake_ldap_field_name', 'Telephone'),
				'Othertelephone.{n}.value' => __d('cake_ldap_field_name', 'Other telephone'),
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_MOBILE_TELEPHONE_NUMBER => __d('cake_ldap_field_name', 'Mobile telephone'),
				'Othermobile.{n}.value' => __d('cake_ldap_field_name', 'Other mobile telephone'),
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_OFFICE_NAME => __d('cake_ldap_field_name', 'Office room'),
				'Department.value' => __d('cake_ldap_field_name', 'Department'),
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_DIVISION => __d('cake_ldap_field_name', 'Subdivision'),
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_TITLE => __d('cake_ldap_field_name', 'Position'),
				'Manager.' . CAKE_LDAP_LDAP_ATTRIBUTE_NAME => __d('cake_ldap_field_name', 'Manager'),
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_BIRTHDAY => __d('cake_ldap_field_name', 'Birthday'),
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_COMPUTER => __d('cake_ldap_field_name', 'Computer'),
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_EMPLOYEE_ID => __d('cake_ldap_field_name', 'Employee ID'),
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_PHOTO => __d('cake_ldap_field_name', 'Photo'),
				'Employee.block' => __d('cake_ldap_field_name', 'Block'),
			],
			'fieldsLabelExtend' => [
				'Subordinate.{n}' => __d('cake_ldap_field_name', 'Subordinate'),
			],
			'fieldsConfig' => [
				'Employee.id' => [
					'type' => 'integer',
					'truncate' => false
				],
				'Employee.department_id' => [
					'type' => 'integer',
					'truncate' => false
				],
				'Employee.manager_id' => [
					'type' => 'integer',
					'truncate' => false
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_OBJECT_GUID => [
					'type' => 'guid',
					'truncate' => false
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_DISTINGUISHED_NAME => [
					'type' => 'string',
					'truncate' => false
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_NAME => [
					'type' => 'string',
					'truncate' => false
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_DISPLAY_NAME => [
					'type' => 'string',
					'truncate' => false
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_INITIALS => [
					'type' => 'string',
					'truncate' => false
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_SURNAME => [
					'type' => 'string',
					'truncate' => false
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_GIVEN_NAME => [
					'type' => 'string',
					'truncate' => false
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_MIDDLE_NAME => [
					'type' => 'string',
					'truncate' => false
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_TITLE => [
					'type' => 'string',
					'truncate' => true
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_DIVISION => [
					'type' => 'string',
					'truncate' => true
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_TELEPHONE_NUMBER => [
					'type' => 'string',
					'truncate' => false
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_MOBILE_TELEPHONE_NUMBER => [
					'type' => 'string',
					'truncate' => false
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_OFFICE_NAME => [
					'type' => 'string',
					'truncate' => false
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_MAIL => [
					'type' => 'mail',
					'truncate' => false
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_PHOTO => [
					'type' => 'photo',
					'truncate' => false
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_COMPUTER => [
					'type' => 'string',
					'truncate' => true
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_EMPLOYEE_ID => [
					'type' => 'string',
					'truncate' => false
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_COMPANY => [
					'type' => 'string',
					'truncate' => true
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_BIRTHDAY => [
					'type' => 'date',
					'truncate' => false
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_SIP_PHONE => [
					'type' => 'string',
					'truncate' => false
				],
				'Employee.block' => [
					'type' => 'boolean',
					'truncate' => false
				],
				'Department.value' => [
					'type' => 'string',
					'truncate' => true
				],
				'Othertelephone.{n}.value' => [
					'type' => 'string',
					'truncate' => false
				],
				'Othermobile.{n}.value' => [
					'type' => 'string',
					'truncate' => false
				],
				'Manager.' . CAKE_LDAP_LDAP_ATTRIBUTE_NAME => [
					'type' => 'manager',
					'truncate' => false
				],
				'Subordinate.{n}' => [
					'type' => 'element',
					'truncate' => false
				]
			],
			'id' => 'CN=Суханова Л.Б.,OU=ОС,OU=Пользователи,DC=fabrikam,DC=com',
			'isTreeReady' => true,
			'pageHeader' => __d('cake_ldap', 'Information of employees'),
			'headerMenuActions' => [
				[
					'fas fa-sync-alt',
					__d('cake_ldap', 'Synchronize'),
					['controller' => 'employees', 'action' => 'sync', 'dd518c55-35ce-4a5c-85c5-b5fb762220bf', 'prefix' => false],
					['title' => __d('cake_ldap', 'Synchronize information of this employee with LDAP server')]
				],
				[
					'fas fa-sitemap',
					__d('cake_ldap', 'Tree of subordinate'),
					['controller' => 'employees', 'action' => 'tree', '3', 'prefix' => false],
					['title' => __d('cake_ldap', 'Edit tree of subordinate employee')]
				]
			],
			'breadCrumbs' => [
				[
					__d('cake_ldap', 'Employees'),
					[
						'plugin' => 'cake_ldap',
						'controller' => 'employees',
						'action' => 'index'
					]
				],
				__d('cake_ldap', 'Viewing')
			]
		];
		$this->assertData($expected, $result);
	}

/**
 * testActionViewTreeNotReady method
 *
 * @return void
 */
	public function testActionViewTreeNotReady() {
		Configure::write('CakeLdap.LdapSync.TreeSubordinate.Enable', false);
		$id = 8;
		$this->_createComponet('/cake_ldap/employees_test/view/' . $id);
		$this->EmployeeAction->initialize($this->Controller);
		$this->EmployeeAction->actionView($id, [], []);
		$result = $this->Controller->viewVars;
		$expected = [
			'employee' => [
				'Employee' => [
					'id' => '8',
					'department_id' => '5',
					'manager_id' => null,
					CAKE_LDAP_LDAP_ATTRIBUTE_OBJECT_GUID => '9d6cf30f-a579-4cbc-8dd1-c92a43b65aaf',
					CAKE_LDAP_LDAP_ATTRIBUTE_DISTINGUISHED_NAME => 'CN=Голубев Е.В.,OU=АТО,OU=Пользователи,DC=fabrikam,DC=com',
					CAKE_LDAP_LDAP_ATTRIBUTE_NAME => 'Голубев Е.В.',
					CAKE_LDAP_LDAP_ATTRIBUTE_DISPLAY_NAME => 'Голубев Е.В.',
					CAKE_LDAP_LDAP_ATTRIBUTE_INITIALS => 'Е.В.',
					CAKE_LDAP_LDAP_ATTRIBUTE_SURNAME => 'Голубев',
					CAKE_LDAP_LDAP_ATTRIBUTE_GIVEN_NAME => 'Егор',
					CAKE_LDAP_LDAP_ATTRIBUTE_MIDDLE_NAME => 'Владимирович',
					CAKE_LDAP_LDAP_ATTRIBUTE_TITLE => 'Водитель',
					CAKE_LDAP_LDAP_ATTRIBUTE_DIVISION => '',
					CAKE_LDAP_LDAP_ATTRIBUTE_TELEPHONE_NUMBER => '',
					CAKE_LDAP_LDAP_ATTRIBUTE_MOBILE_TELEPHONE_NUMBER => '+375295000005',
					CAKE_LDAP_LDAP_ATTRIBUTE_OFFICE_NAME => 'Гараж',
					CAKE_LDAP_LDAP_ATTRIBUTE_MAIL => 'e.golubev@fabrikam.com',
					CAKE_LDAP_LDAP_ATTRIBUTE_PHOTO => base64_decode('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'),
					CAKE_LDAP_LDAP_ATTRIBUTE_COMPUTER => '',
					CAKE_LDAP_LDAP_ATTRIBUTE_EMPLOYEE_ID => '1282',
					CAKE_LDAP_LDAP_ATTRIBUTE_COMPANY => 'ТестОрг',
					CAKE_LDAP_LDAP_ATTRIBUTE_BIRTHDAY => '1950-12-14',
					CAKE_LDAP_LDAP_ATTRIBUTE_SIP_PHONE => '',
					'block' => false
				],
				'Department' => [
					'id' => '5',
					'value' => 'АТО',
					'block' => false,
				],
				'Manager' => [
					'id' => null,
					CAKE_LDAP_LDAP_ATTRIBUTE_NAME => null,
					CAKE_LDAP_LDAP_ATTRIBUTE_TITLE => null,
				],
				'Subordinate' => [
					[
						'id' => '4',
						CAKE_LDAP_LDAP_ATTRIBUTE_NAME => 'Дементьева А.С.',
						CAKE_LDAP_LDAP_ATTRIBUTE_TITLE => 'Инженер',
						'manager_id' => '8',
					]
				],
				'Othertelephone' => [],
				'Othermobile' => [
					[
						'id' => '4',
						'value' => '+375291000004',
						'employee_id' => '8',
					]
				]
			],
			'fieldsLabel' => [
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_NAME => __d('cake_ldap_field_name', 'Full name'),
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_DISPLAY_NAME => __d('cake_ldap_field_name', 'Display name'),
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_SURNAME => __d('cake_ldap_field_name', 'Surname'),
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_GIVEN_NAME => __d('cake_ldap_field_name', 'Given name'),
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_MIDDLE_NAME => __d('cake_ldap_field_name', 'Middle name'),
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_MAIL => __d('cake_ldap_field_name', 'E-mail'),
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_SIP_PHONE => __d('cake_ldap_field_name', 'SIP telephone'),
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_TELEPHONE_NUMBER => __d('cake_ldap_field_name', 'Telephone'),
				'Othertelephone.{n}.value' => __d('cake_ldap_field_name', 'Other telephone'),
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_MOBILE_TELEPHONE_NUMBER => __d('cake_ldap_field_name', 'Mobile telephone'),
				'Othermobile.{n}.value' => __d('cake_ldap_field_name', 'Other mobile telephone'),
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_OFFICE_NAME => __d('cake_ldap_field_name', 'Office room'),
				'Department.value' => __d('cake_ldap_field_name', 'Department'),
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_DIVISION => __d('cake_ldap_field_name', 'Subdivision'),
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_TITLE => __d('cake_ldap_field_name', 'Position'),
				'Manager.' . CAKE_LDAP_LDAP_ATTRIBUTE_NAME => __d('cake_ldap_field_name', 'Manager'),
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_BIRTHDAY => __d('cake_ldap_field_name', 'Birthday'),
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_COMPUTER => __d('cake_ldap_field_name', 'Computer'),
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_EMPLOYEE_ID => __d('cake_ldap_field_name', 'Employee ID'),
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_PHOTO => __d('cake_ldap_field_name', 'Photo'),
				'Employee.block' => __d('cake_ldap_field_name', 'Block'),
			],
			'fieldsLabelExtend' => [
				'Subordinate.{n}' => __d('cake_ldap_field_name', 'Subordinate'),
			],
			'fieldsConfig' => [
				'Employee.id' => [
					'type' => 'integer',
					'truncate' => false
				],
				'Employee.department_id' => [
					'type' => 'integer',
					'truncate' => false
				],
				'Employee.manager_id' => [
					'type' => 'integer',
					'truncate' => false
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_OBJECT_GUID => [
					'type' => 'guid',
					'truncate' => false
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_DISTINGUISHED_NAME => [
					'type' => 'string',
					'truncate' => false
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_NAME => [
					'type' => 'string',
					'truncate' => false
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_DISPLAY_NAME => [
					'type' => 'string',
					'truncate' => false
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_INITIALS => [
					'type' => 'string',
					'truncate' => false
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_SURNAME => [
					'type' => 'string',
					'truncate' => false
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_GIVEN_NAME => [
					'type' => 'string',
					'truncate' => false
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_MIDDLE_NAME => [
					'type' => 'string',
					'truncate' => false
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_TITLE => [
					'type' => 'string',
					'truncate' => true
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_DIVISION => [
					'type' => 'string',
					'truncate' => true
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_TELEPHONE_NUMBER => [
					'type' => 'string',
					'truncate' => false
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_MOBILE_TELEPHONE_NUMBER => [
					'type' => 'string',
					'truncate' => false
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_OFFICE_NAME => [
					'type' => 'string',
					'truncate' => false
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_MAIL => [
					'type' => 'mail',
					'truncate' => false
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_PHOTO => [
					'type' => 'photo',
					'truncate' => false
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_COMPUTER => [
					'type' => 'string',
					'truncate' => true
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_EMPLOYEE_ID => [
					'type' => 'string',
					'truncate' => false
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_COMPANY => [
					'type' => 'string',
					'truncate' => true
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_BIRTHDAY => [
					'type' => 'date',
					'truncate' => false
				],
				'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_SIP_PHONE => [
					'type' => 'string',
					'truncate' => false
				],
				'Employee.block' => [
					'type' => 'boolean',
					'truncate' => false
				],
				'Department.value' => [
					'type' => 'string',
					'truncate' => true
				],
				'Othertelephone.{n}.value' => [
					'type' => 'string',
					'truncate' => false
				],
				'Othermobile.{n}.value' => [
					'type' => 'string',
					'truncate' => false
				],
				'Manager.' . CAKE_LDAP_LDAP_ATTRIBUTE_NAME => [
					'type' => 'manager',
					'truncate' => false
				],
				'Subordinate.{n}' => [
					'type' => 'element',
					'truncate' => false
				]
			],
			'id' => 8,
			'isTreeReady' => false,
			'pageHeader' => __d('cake_ldap', 'Information of employees'),
			'headerMenuActions' => [
				[
					'fas fa-sync-alt',
					__d('cake_ldap', 'Synchronize'),
					['controller' => 'employees', 'action' => 'sync', '9d6cf30f-a579-4cbc-8dd1-c92a43b65aaf', 'prefix' => false],
					['title' => __d('cake_ldap', 'Synchronize information of this employee with LDAP server')]
				]
			],
			'breadCrumbs' => [
				[
					__d('cake_ldap', 'Employees'),
					[
						'plugin' => 'cake_ldap',
						'controller' => 'employees',
						'action' => 'index'
					]
				],
				__d('cake_ldap', 'Viewing')
			]
		];
		$this->assertData($expected, $result);
	}

/**
 * testActionSyncEmptyGuid method
 *
 * @return void
 */
	public function testActionSyncEmptyGuid() {
		$this->_createComponet('/cake_ldap/employees_test/sync');
		$this->EmployeeAction->initialize($this->Controller);
		$this->EmployeeAction->actionSync(null, false);
		$this->checkFlashMessage(__d('cake_ldap', 'Synchronization information of employees put in queue...'));
	}

/**
 * testActionSyncInvalidGuid method
 *
 * @return void
 */
	public function testActionSyncInvalidGuid() {
		$guid = '4c087dae-1891-4f74-acb2-bbbe0a7ba25a';
		$this->_createComponet('/cake_ldap/employees_test/sync/' . $guid);
		$this->EmployeeAction->initialize($this->Controller);
		$this->EmployeeAction->actionSync($guid, false);
		$this->checkFlashMessage(__d('cake_ldap', 'Synchronization information of employees has been finished unsuccessfully'));
	}

/**
 * testActionSyncGuidSuccess method
 *
 * @return void
 */
	public function testActionSyncGuidSuccess() {
		$guid = '0010b7b8-d69a-4365-81ca-5f975584fe5c';
		$this->_createComponet('/cake_ldap/employees_test/sync/' . $guid);
		$this->EmployeeAction->initialize($this->Controller);
		$this->EmployeeAction->actionSync($guid, false);
		$this->checkFlashMessage(__d('cake_ldap', 'Synchronization information of employees has been finished successfully'));
	}

/**
 * testActionSyncGuidUseQueueSuccess method
 *
 * @return void
 */
	public function testActionSyncGuidUseQueueSuccess() {
		$guid = '0010b7b8-d69a-4365-81ca-5f975584fe5c';
		$this->_createComponet('/cake_ldap/employees_test/sync/' . $guid);
		$this->EmployeeAction->initialize($this->Controller);
		$this->EmployeeAction->actionSync($guid, true);
		$this->checkFlashMessage(__d('cake_ldap', 'Synchronization information of employees put in queue...'));
	}

/**
 * testActionTreeTreeNotReady method
 *
 * @return void
 */
	public function testActionTreeTreeNotReady() {
		Configure::write('CakeLdap.LdapSync.TreeSubordinate.Enable', false);
		$this->setExpectedException('InternalErrorException');
		$this->_createComponet('/cake_ldap/employees_test/tree');
		$this->EmployeeAction->initialize($this->Controller);
		$this->EmployeeAction->actionTree();
	}

/**
 * testActionTreeInvalidId method
 *
 * @return void
 */
	public function testActionTreeInvalidId() {
		$id = 1000;
		$this->_createComponet('/cake_ldap/employees_test/tree/' . $id);
		$this->EmployeeAction->initialize($this->Controller);
		$this->EmployeeAction->actionTree($id);
		$this->checkFlashMessage(__d('cake_ldap', 'Invalid ID for employee'));
		$this->assertNotEmpty($this->Controller->redirectUrl);
	}

/**
 * testActionTreeEmptyId method
 *
 * @return void
 */
	public function testActionTreeEmptyId() {
		$this->_createComponet('/cake_ldap/employees_test/tree');
		$this->EmployeeAction->initialize($this->Controller);
		$this->EmployeeAction->actionTree(null);
		$result = $this->Controller->viewVars;
		$expected = [
			'employees' => [
				[
					'SubordinateDb' => [
						'id' => '1',
						'parent_id' => null,
						'lft' => '1',
						'rght' => '2',
					],
					'Employee' => [
						'id' => '1',
						CAKE_LDAP_LDAP_ATTRIBUTE_NAME => 'Миронов В.М.',
						CAKE_LDAP_LDAP_ATTRIBUTE_TITLE => 'Ведущий геолог',
					],
					'children' => []
				],
				[
					'SubordinateDb' => [
						'id' => '3',
						'parent_id' => null,
						'lft' => '3',
						'rght' => '6',
					],
					'Employee' => [
						'id' => '3',
						CAKE_LDAP_LDAP_ATTRIBUTE_NAME => 'Суханова Л.Б.',
						CAKE_LDAP_LDAP_ATTRIBUTE_TITLE => 'Зам. начальника отдела - главный специалист',
					],
					'children' => [
						[
							'SubordinateDb' => [
								'id' => '2',
								'parent_id' => '3',
								'lft' => '4',
								'rght' => '5',
							],
							'Employee' => [
								'id' => '2',
								CAKE_LDAP_LDAP_ATTRIBUTE_NAME => 'Егоров Т.Г.',
								CAKE_LDAP_LDAP_ATTRIBUTE_TITLE => 'Ведущий инженер',
							],
							'children' => []
						]
					]
				],
				[
					'SubordinateDb' => [
						'id' => '5',
						'parent_id' => null,
						'lft' => '7',
						'rght' => '8',
					],
					'Employee' => [
						'id' => '5',
						CAKE_LDAP_LDAP_ATTRIBUTE_NAME => 'Матвеев Р.М.',
						CAKE_LDAP_LDAP_ATTRIBUTE_TITLE => 'Ведущий инженер',
					],
					'children' => []
				],
				[
					'SubordinateDb' => [
						'id' => '8',
						'parent_id' => null,
						'lft' => '9',
						'rght' => '16',
					],
					'Employee' => [
						'id' => '8',
						CAKE_LDAP_LDAP_ATTRIBUTE_NAME => 'Голубев Е.В.',
						CAKE_LDAP_LDAP_ATTRIBUTE_TITLE => 'Водитель',
					],
					'children' => [
						[
							'SubordinateDb' => [
								'id' => '4',
								'parent_id' => '8',
								'lft' => '10',
								'rght' => '15',
							],
							'Employee' => [
								'id' => '4',
								CAKE_LDAP_LDAP_ATTRIBUTE_NAME => 'Дементьева А.С.',
								CAKE_LDAP_LDAP_ATTRIBUTE_TITLE => 'Инженер',
							],
							'children' => [
								[
									'SubordinateDb' => [
										'id' => '7',
										'parent_id' => '4',
										'lft' => '11',
										'rght' => '14',
									],
									'Employee' => [
										'id' => '7',
										CAKE_LDAP_LDAP_ATTRIBUTE_NAME => 'Хвощинский В.В.',
										CAKE_LDAP_LDAP_ATTRIBUTE_TITLE => 'Начальник отдела',
									],
									'children' => [
										[
											'SubordinateDb' => [
												'id' => '6',
												'parent_id' => '7',
												'lft' => '12',
												'rght' => '13',
											],
											'Employee' => [
												'id' => '6',
												CAKE_LDAP_LDAP_ATTRIBUTE_NAME => 'Козловская Е.М.',
												CAKE_LDAP_LDAP_ATTRIBUTE_TITLE => 'Заведующий сектором',
											],
											'children' => []
										]
									]
								]
							]
						]
					]
				],
				[
					'SubordinateDb' => [
						'id' => '10',
						'parent_id' => null,
						'lft' => '17',
						'rght' => '18',
					],
					'Employee' => [
						'id' => '10',
						CAKE_LDAP_LDAP_ATTRIBUTE_NAME => 'Чижов Я.С.',
						CAKE_LDAP_LDAP_ATTRIBUTE_TITLE => 'Инженер 1 категории',
					],
					'children' => []
				]
			],
			'isTreeDraggable' => false,
			'expandAll' => false,
			'pageHeader' => __d('cake_ldap', 'Tree view information of employees'),
			'headerMenuActions' => [
				[
					'fas fa-sync-alt',
					__d('cake_ldap', 'Synchronize information with LDAP server'),
					['controller' => 'employees', 'action' => 'sync', 'prefix' => false],
					[
						'title' => __d('cake_ldap', 'Synchronize information of employees with LDAP server'),
						'data-toggle' => 'request-only',
					]
				],
				[
					'fas fa-sort-alpha-down',
					__d('cake_ldap', 'Order tree of employees'),
					['controller' => 'employees', 'action' => 'order', 'prefix' => false],
					[
						'title' => __d('cake_ldap', 'Order tree of employees by alphabet'),
						'action-type' => 'confirm-post',
						'data-confirm-msg' => __d('cake_ldap', 'Are you sure you wish to re-order tree of employees?')
					]
				],
				[
					'fas fa-check',
					__d('cake_ldap', 'Check state tree of employees'),
					['controller' => 'employees', 'action' => 'check', 'prefix' => false],
					['title' => __d('cake_ldap', 'Check state tree of employees')]
				]
			],
			'breadCrumbs' => [
				[
					__d('cake_ldap', 'Employees'),
					[
						'plugin' => 'cake_ldap',
						'controller' => 'employees',
						'action' => 'index'
					]
				],
				__d('cake_ldap', 'Tree viewing')
			]
		];
		$this->assertData($expected, $result);
	}

/**
 * testActionTreeEmptyIdInludeBlock method
 *
 * @return void
 */
	public function testActionTreeEmptyIdInludeBlock() {
		$this->_createComponet('/cake_ldap/employees_test/tree/0/1');
		$this->EmployeeAction->initialize($this->Controller);
		$this->EmployeeAction->actionTree(null, true);
		$result = $this->Controller->viewVars;
		$expected = [
			'employees' => [
				[
					'SubordinateDb' => [
						'id' => '1',
						'parent_id' => null,
						'lft' => '1',
						'rght' => '2',
					],
					'Employee' => [
						'id' => '1',
						CAKE_LDAP_LDAP_ATTRIBUTE_NAME => 'Миронов В.М.',
						CAKE_LDAP_LDAP_ATTRIBUTE_TITLE => 'Ведущий геолог',
						'block' => false,
					],
					'children' => []
				],
				[
					'SubordinateDb' => [
						'id' => '3',
						'parent_id' => null,
						'lft' => '3',
						'rght' => '6',
					],
					'Employee' => [
						'id' => '3',
						CAKE_LDAP_LDAP_ATTRIBUTE_NAME => 'Суханова Л.Б.',
						CAKE_LDAP_LDAP_ATTRIBUTE_TITLE => 'Зам. начальника отдела - главный специалист',
						'block' => false,
					],
					'children' => [
						[
							'SubordinateDb' => [
								'id' => '2',
								'parent_id' => '3',
								'lft' => '4',
								'rght' => '5',
							],
							'Employee' => [
								'id' => '2',
								CAKE_LDAP_LDAP_ATTRIBUTE_NAME => 'Егоров Т.Г.',
								CAKE_LDAP_LDAP_ATTRIBUTE_TITLE => 'Ведущий инженер',
								'block' => false,
							],
							'children' => []
						]
					]
				],
				[
					'SubordinateDb' => [
						'id' => '5',
						'parent_id' => null,
						'lft' => '7',
						'rght' => '8',
					],
					'Employee' => [
						'id' => '5',
						CAKE_LDAP_LDAP_ATTRIBUTE_NAME => 'Матвеев Р.М.',
						CAKE_LDAP_LDAP_ATTRIBUTE_TITLE => 'Ведущий инженер',
						'block' => false,
					],
					'children' => []
				],
				[
					'SubordinateDb' => [
						'id' => '8',
						'parent_id' => null,
						'lft' => '9',
						'rght' => '16',
					],
					'Employee' => [
						'id' => '8',
						CAKE_LDAP_LDAP_ATTRIBUTE_NAME => 'Голубев Е.В.',
						CAKE_LDAP_LDAP_ATTRIBUTE_TITLE => 'Водитель',
						'block' => false,
					],
					'children' => [
						[
							'SubordinateDb' => [
								'id' => '4',
								'parent_id' => '8',
								'lft' => '10',
								'rght' => '15',
							],
							'Employee' => [
								'id' => '4',
								CAKE_LDAP_LDAP_ATTRIBUTE_NAME => 'Дементьева А.С.',
								CAKE_LDAP_LDAP_ATTRIBUTE_TITLE => 'Инженер',
								'block' => false,
							],
							'children' => [
								[
									'SubordinateDb' => [
										'id' => '7',
										'parent_id' => '4',
										'lft' => '11',
										'rght' => '14',
									],
									'Employee' => [
										'id' => '7',
										CAKE_LDAP_LDAP_ATTRIBUTE_NAME => 'Хвощинский В.В.',
										CAKE_LDAP_LDAP_ATTRIBUTE_TITLE => 'Начальник отдела',
										'block' => false,
									],
									'children' => [
										[
											'SubordinateDb' => [
												'id' => '6',
												'parent_id' => '7',
												'lft' => '12',
												'rght' => '13',
											],
											'Employee' => [
												'id' => '6',
												CAKE_LDAP_LDAP_ATTRIBUTE_NAME => 'Козловская Е.М.',
												CAKE_LDAP_LDAP_ATTRIBUTE_TITLE => 'Заведующий сектором',
												'block' => false,
											],
											'children' => []
										]
									]
								]
							]
						]
					]
				],
				[
					'SubordinateDb' => [
						'id' => '10',
						'parent_id' => null,
						'lft' => '17',
						'rght' => '18',
					],
					'Employee' => [
						'id' => '10',
						CAKE_LDAP_LDAP_ATTRIBUTE_NAME => 'Чижов Я.С.',
						CAKE_LDAP_LDAP_ATTRIBUTE_TITLE => 'Инженер 1 категории',
						'block' => false,
					],
					'children' => []
				],
				[
					'SubordinateDb' => [
						'id' => '9',
						'parent_id' => null,
						'lft' => '19',
						'rght' => '20',
					],
					'Employee' => [
						'id' => '9',
						'name' => 'Марчук А.М.',
						'title' => 'Ведущий инженер по охране труда',
						'block' => true,
					],
					'children' => []
				]
			],
			'isTreeDraggable' => false,
			'expandAll' => false,
			'pageHeader' => __d('cake_ldap', 'Tree view information of employees'),
			'headerMenuActions' => [
				[
					'fas fa-sync-alt',
					__d('cake_ldap', 'Synchronize information with LDAP server'),
					['controller' => 'employees', 'action' => 'sync', 'prefix' => false],
					[
						'title' => __d('cake_ldap', 'Synchronize information of employees with LDAP server'),
						'data-toggle' => 'request-only',
					]
				],
				[
					'fas fa-sort-alpha-down',
					__d('cake_ldap', 'Order tree of employees'),
					['controller' => 'employees', 'action' => 'order', 'prefix' => false],
					[
						'title' => __d('cake_ldap', 'Order tree of employees by alphabet'),
						'action-type' => 'confirm-post',
						'data-confirm-msg' => __d('cake_ldap', 'Are you sure you wish to re-order tree of employees?')
					]
				],
				[
					'fas fa-check',
					__d('cake_ldap', 'Check state tree of employees'),
					['controller' => 'employees', 'action' => 'check', 'prefix' => false],
					['title' => __d('cake_ldap', 'Check state tree of employees')]
				]
			],
			'breadCrumbs' => [
				[
					__d('cake_ldap', 'Employees'),
					[
						'plugin' => 'cake_ldap',
						'controller' => 'employees',
						'action' => 'index'
					]
				],
				__d('cake_ldap', 'Tree viewing')
			]
		];
		$this->assertData($expected, $result);
	}

/**
 * testActionTreeValidIdWithIncludeField method
 *
 * @return void
 */
	public function testActionTreeValidIdWithIncludeField() {
		Configure::write('CakeLdap.LdapSync.TreeSubordinate.Draggable', true);
		$id = 4;
		$includeFields = 'Employee.' . CAKE_LDAP_LDAP_ATTRIBUTE_OBJECT_GUID;
		$this->_createComponet('/cake_ldap/employees_test/tree/' . $id);
		$this->EmployeeAction->initialize($this->Controller);
		$this->EmployeeAction->actionTree($id, false, $includeFields);
		$result = $this->Controller->viewVars;
		$expected = [
			'employees' => [
				[
					'SubordinateDb' => [
						'id' => '4',
						'parent_id' => '8',
						'lft' => '10',
						'rght' => '15',
					],
					'Employee' => [
						'id' => '4',
						CAKE_LDAP_LDAP_ATTRIBUTE_NAME => 'Дементьева А.С.',
						CAKE_LDAP_LDAP_ATTRIBUTE_TITLE => 'Инженер',
						CAKE_LDAP_LDAP_ATTRIBUTE_OBJECT_GUID => 'd4bd663f-37da-4737-bfd8-e6442e723722',
					],
					'children' => [
						[
							'SubordinateDb' => [
								'id' => '7',
								'parent_id' => '4',
								'lft' => '11',
								'rght' => '14',
							],
							'Employee' => [
								'id' => '7',
								CAKE_LDAP_LDAP_ATTRIBUTE_NAME => 'Хвощинский В.В.',
								CAKE_LDAP_LDAP_ATTRIBUTE_TITLE => 'Начальник отдела',
								CAKE_LDAP_LDAP_ATTRIBUTE_OBJECT_GUID => '8c149661-7215-47de-b40e-35320a1ea508',
							],
							'children' => [
								[
									'SubordinateDb' => [
										'id' => '6',
										'parent_id' => '7',
										'lft' => '12',
										'rght' => '13',
									],
									'Employee' => [
										'id' => '6',
										CAKE_LDAP_LDAP_ATTRIBUTE_NAME => 'Козловская Е.М.',
										CAKE_LDAP_LDAP_ATTRIBUTE_TITLE => 'Заведующий сектором',
										CAKE_LDAP_LDAP_ATTRIBUTE_OBJECT_GUID => '81817f32-44a7-4b4a-8eff-b837ba387077',
									],
									'children' => []
								]
							]
						]
					]
				]
			],
			'isTreeDraggable' => true,
			'expandAll' => true,
			'pageHeader' => __d('cake_ldap', 'Tree view information of employees'),
			'headerMenuActions' => [
				[
					'fas fa-sync-alt',
					__d('cake_ldap', 'Synchronize information with LDAP server'),
					['controller' => 'employees', 'action' => 'sync', 'prefix' => false],
					[
						'title' => __d('cake_ldap', 'Synchronize information of employees with LDAP server'),
						'data-toggle' => 'request-only',
					]
				],
				[
					'fas fa-sort-alpha-down',
					__d('cake_ldap', 'Order tree of employees'),
					['controller' => 'employees', 'action' => 'order', 'prefix' => false],
					[
						'title' => __d('cake_ldap', 'Order tree of employees by alphabet'),
						'action-type' => 'confirm-post',
						'data-confirm-msg' => __d('cake_ldap', 'Are you sure you wish to re-order tree of employees?')
					]
				],
				[
					'fas fa-check',
					__d('cake_ldap', 'Check state tree of employees'),
					['controller' => 'employees', 'action' => 'check', 'prefix' => false],
					['title' => __d('cake_ldap', 'Check state tree of employees')]
				]
			],
			'breadCrumbs' => [
				[
					__d('cake_ldap', 'Employees'),
					[
						'plugin' => 'cake_ldap',
						'controller' => 'employees',
						'action' => 'index'
					]
				],
				__d('cake_ldap', 'Tree viewing')
			]
		];
		$this->assertData($expected, $result);
	}

/**
 * testActionOrderTreeNotReady method
 *
 * @return void
 */
	public function testActionOrderTreeNotReady() {
		Configure::write('CakeLdap.LdapSync.TreeSubordinate.Enable', false);
		$this->setExpectedException('InternalErrorException');
		$this->_createComponet('/cake_ldap/employees_test/order');
		$this->EmployeeAction->initialize($this->Controller);
		$this->EmployeeAction->actionOrder();
	}

/**
 * testActionOrderTreeGet method
 *
 * @return void
 */
	public function testActionOrderTreeGet() {
		$this->setExpectedException('MethodNotAllowedException');
		$this->_createComponet('/cake_ldap/employees_test/order');
		$this->EmployeeAction->initialize($this->Controller);
		$this->EmployeeAction->actionOrder();
	}

/**
 * testActionOrderTreePost method
 *
 * @return void
 */
	public function testActionOrderTreePost() {
		$this->_createComponet('/cake_ldap/employees_test/order', 'POST');
		$this->EmployeeAction->initialize($this->Controller);
		$this->EmployeeAction->actionOrder();
		$this->checkFlashMessage(__d('cake_ldap', 'Order tree of employees put in queue...'));
	}

/**
 * testActionCheckTreeNotReady method
 *
 * @return void
 */
	public function testActionCheckTreeNotReady() {
		Configure::write('CakeLdap.LdapSync.TreeSubordinate.Enable', false);
		$this->setExpectedException('InternalErrorException');
		$this->_createComponet('/cake_ldap/employees_test/check');
		$this->EmployeeAction->initialize($this->Controller);
		$this->EmployeeAction->actionCheck();
	}

/**
 * testActionCheckTreeSuccess method
 *
 * @return void
 */
	public function testActionCheckTreeSuccess() {
		$this->_createComponet('/cake_ldap/employees_test/check');
		$this->EmployeeAction->initialize($this->Controller);
		$this->EmployeeAction->actionCheck();
		$result = $this->Controller->viewVars;
		$expected = [
			'treeState' => true,
			'pageHeader' => __d('cake_ldap', 'Checking state tree of employees'),
			'headerMenuActions' => [],
			'breadCrumbs' => [
				[
					__d('cake_ldap', 'Employees'),
					[
						'plugin' => 'cake_ldap',
						'controller' => 'employees',
						'action' => 'index'
					]
				],
				__d('cake_ldap', 'Checking tree')
			]
		];
		$this->assertData($expected, $result);
	}

/**
 * testActionCheckTreeUnsuccess method
 *
 * @return void
 */
	public function testActionCheckTreeUnsuccess() {
		$modelSubordinateDb = ClassRegistry::init('CakeLdap.SubordinateDb');
		$modelSubordinateDb->id = 4;
		$result = (bool)$modelSubordinateDb->saveField('rght', null);
		$this->assertTrue($result);

		$this->_createComponet('/cake_ldap/employees_test/check');
		$this->EmployeeAction->initialize($this->Controller);
		$this->EmployeeAction->actionCheck();
		$result = $this->Controller->viewVars;
		$expected = [
			'treeState' => [
				[
					'index',
					15,
					'missing',
				],
				[
					'node',
					'4',
					'has invalid left or right values',
				],
				[
					'node',
					'7',
					'right greater than parent (node 4).',
				]
			],
			'pageHeader' => __d('cake_ldap', 'Checking state tree of employees'),
			'headerMenuActions' => [
				[
					'fas fa-redo-alt',
					__d('cake_ldap', 'Recovery state of tree'),
					['controller' => 'employees', 'action' => 'recover', 'prefix' => false],
					[
						'title' => __d('cake_ldap', 'Recovery state of tree'),
						'data-toggle' => 'request-only',
					]
				]
			],
			'breadCrumbs' => [
				[
					__d('cake_ldap', 'Employees'),
					[
						'plugin' => 'cake_ldap',
						'controller' => 'employees',
						'action' => 'index'
					]
				],
				__d('cake_ldap', 'Checking tree')
			]
		];
		$this->assertData($expected, $result);
	}

/**
 * testActionRecoverTreeNotReady method
 *
 * @return void
 */
	public function testActionRecoverTreeNotReady() {
		Configure::write('CakeLdap.LdapSync.TreeSubordinate.Enable', false);
		$this->setExpectedException('InternalErrorException');
		$this->_createComponet('/cake_ldap/employees_test/order');
		$this->EmployeeAction->initialize($this->Controller);
		$this->EmployeeAction->actionRecover();
	}

/**
 * testActionRecoverSuccess method
 *
 * @return void
 */
	public function testActionRecoverSuccess() {
		$this->_createComponet('/cake_ldap/employees_test/order', 'POST');
		$this->EmployeeAction->initialize($this->Controller);
		$this->EmployeeAction->actionRecover();
		$this->checkFlashMessage(__d('cake_ldap', 'Recovery tree of employees put in queue...'));
	}

/**
 * Create EmployeeActionComponent
 *
 * @param string|array $url A string or array-based URL
 * @param string $type Request type: `GET` or `POST`.
 * @param array $params Array of parameters for request
 * @param bool $useDefaultController Flag of use default controller
 * @return void
 */
	protected function _createComponet($url = null, $type = 'GET', $params = [], $useDefaultController = false) {
		$request = new CakeRequest($url);
		$response = new CakeResponse();
		if (!empty($type)) {
			$this->setRequestType($type);
		}
		if (!empty($params)) {
			$request->addParams($params);
		}

		$Collection = new ComponentCollection();
		if ($useDefaultController) {
			$this->Controller = new Controller($request, $response);
			$this->Controller->constructClasses();
		} else {
			$this->Controller = new EmployeesTestController($request, $response);
			$this->Controller->constructClasses();
			$this->Controller->RequestHandler->initialize($this->Controller);
			$this->Controller->Paginator->initialize($this->Controller);
			$this->Controller->Filter->initialize($this->Controller);
			$this->Controller->Flash->initialize($this->Controller);
		}
		$this->EmployeeAction = new EmployeeActionComponent($Collection);
	}
}
