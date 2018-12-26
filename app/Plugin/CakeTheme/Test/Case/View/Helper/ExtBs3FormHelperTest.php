<?php
App::uses('AppCakeTestCase', 'CakeTheme.Test');
App::uses('View', 'View');
App::uses('Helper', 'View');
App::uses('ExtBs3FormHelper', 'CakeTheme.View/Helper');
require_once App::pluginPath('CakeTheme') . 'Test' . DS . 'Model' . DS . 'modelsFixt.php';

/**
 * ExtBs3FormHelper Test Case
 */
class ExtBs3FormHelperTest extends AppCakeTestCase {

	public $EmployeeTest = null;

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = [
		'core.cake_session',
		'plugin.cake_theme.employees',
	];

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		$userInfo = [
			'user' => 'Моисеева Л.Б.',
			'role' => CAKE_THEME_TEST_USER_ROLE_USER | CAKE_THEME_TEST_USER_ROLE_ADMIN,
			'prefix' => 'admin',
			'id' => '1'
		];
		$this->setDefaultUserInfo($userInfo);
		Configure::write('Routing.prefixes', ['admin']);
		Router::reload();
		parent::setUp();

		$View = new View();
		$request = new CakeRequest();
		$request->addParams(array(
				'plugin' => null, 'controller' => 'employees', 'action' => 'add',
				'prefix' => 'admin', 'admin' => true,
				'url' => array('url' => 'admin/employees/add')
			))->addPaths(array(
				'base' => '', 'here' => '/admin/employees/add', 'webroot' => '/'
			))->query = [];
		$View->request = $request;
		Router::setRequestInfo($request);
		$this->_targetObject = new ExtBs3FormHelper($View);

		ClassRegistry::addObject('EmployeeTest', new EmployeeTest());
		$this->EmployeeTest = ClassRegistry::init('EmployeeTest');
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
 * testLabel method
 *
 * @return void
 */
	public function testLabel() {
		$params = [
			[
				'', // $fieldName
				'', // $text
				[], // $options
			],
			[
				'EmployeeTest.full_name', // $fieldName
				'', // $text
				[], // $options
			],
			[
				'EmployeeTest.full_name', // $fieldName
				'Label text', // $text
				['class' => 'some-class'], // $options
			],
			[
				'EmployeeTest.full_name', // $fieldName
				['Label text'], // $text
				['class' => 'some-class'], // $options
			],
			[
				'EmployeeTest.full_name', // $fieldName
				[
					'Label text',
					'Tooltip text',
					':',
					'pad_param'
				], // $text
				['class' => 'some-class'], // $options
			],
		];
		$expected = [
			'<label for=""></label>',
			'<label for="EmployeeTestFullName"></label>',
			'<label for="EmployeeTestFullName" class="some-class">Label text</label>',
			'<label for="EmployeeTestFullName" class="some-class">Label text</label>',
			'<label for="EmployeeTestFullName" class="some-class">Label text&nbsp;<abbr title="Tooltip text" data-toggle="tooltip">[?]</abbr>:</label>',
		];
		$this->runClassMethodGroup('label', $params, $expected);
	}

/**
 * testGetLabelTextFromField method
 *
 * @return void
 */
	public function testGetLabelTextFromField() {
		$params = [
			[
				'', // $fieldName
			],
			[
				'full_name', // $fieldName
			],
			[
				'EmployeeTest.full_name', // $fieldName
			],
		];
		$expected = [
			'',
			'Full Name',
			'Full Name',
		];
		$this->runClassMethodGroup('getLabelTextFromField', $params, $expected);
	}

/**
 * testSelect method
 *
 * @return void
 */
	public function testSelect() {
		$this->_targetObject->create('EmployeeTest');
		$params = [
			[
				'', // $fieldName
				[], // $options
				[], // $attributes
			],
			[
				'EmployeeTest.department_id', // $fieldName
				[], // $options
				[], // $attributes
			],
			[
				'EmployeeTest.department_id', // $fieldName
				['1' => 'HR', '2' => 'IT'], // $options
				[
					'header' => '-- Header --',
					'label' => ['Department', 'Name of department', ':']
				], // $attributes
			],
			[
				'EmployeeTest.department_id', // $fieldName
				['1' => 'HR', '2' => 'IT'], // $options
				[
					'multiple' => 'checkbox',
					'label' => ['Department', 'Name of department', ':']
				], // $attributes
			],
		];
		$expected = [
			'<select name="data[EmployeeTest]" data-toggle="select" id="EmployeeTest">' . "\n" .
				'<option value=""></option>' . "\n" .
			'</select>',
			'<select name="data[EmployeeTest][department_id]" data-toggle="select" id="EmployeeTestDepartmentId">' . "\n" .
				'<option value=""></option>' . "\n" .
			'</select>',
			'<div class="form-group"><label for="EmployeeTestDepartmentId">Department&nbsp;<abbr title="Name of department" data-toggle="tooltip">[?]</abbr>:</label><select name="data[EmployeeTest][department_id]" data-toggle="select" data-header="-- Header --" id="EmployeeTestDepartmentId">' . "\n" .
				'<option value=""></option>' . "\n" .
				'<option value="1">HR</option>' . "\n" .
				'<option value="2">IT</option>' . "\n" .
			'</select></div>',
			'<div class="form-group"><label for="EmployeeTestDepartmentId">Department&nbsp;<abbr title="Name of department" data-toggle="tooltip">[?]</abbr>:</label><input type="hidden" name="data[EmployeeTest][department_id]" value="" id="EmployeeTestDepartmentId"/>' . "\n\n" .
				'<div class="checkbox"><label for="EmployeeTestDepartmentId1" class=""><input type="checkbox" name="data[EmployeeTest][department_id][]" value="1" id="EmployeeTestDepartmentId1" /> HR</label></div>' . "\n" .
				'<div class="checkbox"><label for="EmployeeTestDepartmentId2" class=""><input type="checkbox" name="data[EmployeeTest][department_id][]" value="2" id="EmployeeTestDepartmentId2" /> IT</label></div>' . "\n" .
			'</div>'
		];
		$this->runClassMethodGroup('select', $params, $expected);
	}

/**
 * testEmail method
 *
 * @return void
 */
	public function testEmail() {
		$this->_targetObject->create('EmployeeTest');
		$params = [
			[
				'', // $fieldName
				[], // $options
			],
			[
				'EmployeeTest.mail', // $fieldName
				['title' => 'Some title'], // $options
			],
		];
		$expected = [
			'<input name="data[EmployeeTest]" data-inputmask-alias="email" type="text" id="EmployeeTest"/>',
			'<input name="data[EmployeeTest][mail]" title="Some title" data-inputmask-alias="email" type="text" id="EmployeeTestMail"/>',
		];
		$this->runClassMethodGroup('email', $params, $expected);
	}

/**
 * testInteger method
 *
 * @return void
 */
	public function testInteger() {
		$this->_targetObject->create('EmployeeTest');
		$params = [
			[
				'', // $fieldName
				[], // $options
			],
			[
				'EmployeeTest.position', // $fieldName
				['title' => 'Some title'], // $options
			],
		];
		$expected = [
			'<input name="data[EmployeeTest]" data-inputmask-alias="integer" type="text" id="EmployeeTest"/>',
			'<input name="data[EmployeeTest][position]" title="Some title" data-inputmask-alias="integer" type="text" id="EmployeeTestPosition"/>',
		];
		$this->runClassMethodGroup('integer', $params, $expected);
	}

/**
 * testFloat method
 *
 * @return void
 */
	public function testFloat() {
		$this->_targetObject->create('EmployeeTest');
		$params = [
			[
				'', // $fieldName
				[], // $options
			],
			[
				'EmployeeTest.position', // $fieldName
				['title' => 'Some title'], // $options
			],
		];
		$expected = [
			'<input name="data[EmployeeTest]" data-inputmask-alias="decimal" type="text" id="EmployeeTest"/>',
			'<input name="data[EmployeeTest][position]" title="Some title" data-inputmask-alias="decimal" type="text" id="EmployeeTestPosition"/>',
		];
		$this->runClassMethodGroup('float', $params, $expected);
	}

/**
 * testDateSelect method
 *
 * @return void
 */
	public function testDateSelect() {
		Configure::write('Config.language', 'eng');
		$this->_targetObject->create('EmployeeTest');
		$params = [
			[
				'', // $fieldName
				[], // $options
			],
			[
				'EmployeeTest.birthday', // $fieldName
				['title' => 'Some title'], // $options
			],
		];
		$expected = [
			'<input name="data[EmployeeTest]" data-toggle="datetimepicker" data-inputmask-alias="yyyy-mm-dd" data-date-locale="en" data-icon-type="date" data-date-format="YYYY-MM-DD" type="text" id="EmployeeTest"/>',
			'<input name="data[EmployeeTest][birthday]" data-toggle="datetimepicker" title="Some title" data-inputmask-alias="yyyy-mm-dd" data-date-locale="en" data-icon-type="date" data-date-format="YYYY-MM-DD" type="text" id="EmployeeTestBirthday"/>',
		];
		$this->runClassMethodGroup('dateSelect', $params, $expected);
	}

/**
 * testTimeSelect method
 *
 * @return void
 */
	public function testTimeSelect() {
		Configure::write('Config.language', 'eng');
		$this->_targetObject->create('EmployeeTest');
		$params = [
			[
				'', // $fieldName
				[], // $options
			],
			[
				'EmployeeTest.birthday', // $fieldName
				['title' => 'Some title'], // $options
			],
		];
		$expected = [
			'<input name="data[EmployeeTest]" data-toggle="datetimepicker" data-inputmask-alias="hh:mm:ss" data-date-locale="en" data-icon-type="time" data-date-format="HH:mm:ss" type="text" id="EmployeeTest"/>',
			'<input name="data[EmployeeTest][birthday]" data-toggle="datetimepicker" title="Some title" data-inputmask-alias="hh:mm:ss" data-date-locale="en" data-icon-type="time" data-date-format="HH:mm:ss" type="text" id="EmployeeTestBirthday"/>',
		];
		$this->runClassMethodGroup('timeSelect', $params, $expected);
	}

/**
 * testDateTimeSelect method
 *
 * @return void
 */
	public function testDateTimeSelect() {
		Configure::write('Config.language', 'eng');
		$this->_targetObject->create('EmployeeTest');
		$params = [
			[
				'', // $fieldName
				[], // $options
			],
			[
				'EmployeeTest.birthday', // $fieldName
				['title' => 'Some title'], // $options
			],
		];
		$expected = [
			'<input name="data[EmployeeTest]" data-toggle="datetimepicker" data-inputmask-alias="yyyy-mm-dd hh:mm:ss" data-date-locale="en" data-icon-type="date" data-date-format="YYYY-MM-DD HH:mm:ss" type="text" id="EmployeeTest"/>',
			'<input name="data[EmployeeTest][birthday]" data-toggle="datetimepicker" title="Some title" data-inputmask-alias="yyyy-mm-dd hh:mm:ss" data-date-locale="en" data-icon-type="date" data-date-format="YYYY-MM-DD HH:mm:ss" type="text" id="EmployeeTestBirthday"/>',
		];
		$this->runClassMethodGroup('dateTimeSelect', $params, $expected);
	}

/**
 * testSpin method
 *
 * @return void
 */
	public function testSpin() {
		$this->_targetObject->create('EmployeeTest');
		$params = [
			[
				'', // $fieldName
				[], // $options
			],
			[
				'EmployeeTest.position', // $fieldName
				[
					'title' => 'Some title',
					'verticalbuttons' => 'false',
					'min' => '2',
					'max' => '10'
				], // $options
			],
		];
		$expected = [
			'<input name="data[EmployeeTest]" data-toggle="spin" data-spin-verticalbuttons="true" data-inputmask-mask="9{1,}" type="text" id="EmployeeTest"/>',
			'<input name="data[EmployeeTest][position]" data-toggle="spin" data-spin-verticalbuttons="false" title="Some title" data-spin-min="2" data-spin-max="10" data-inputmask-mask="9{1,2}" type="text" id="EmployeeTestPosition"/>',
		];
		$this->runClassMethodGroup('spin', $params, $expected);
	}

/**
 * testFlag method
 *
 * @return void
 */
	public function testFlag() {
		$this->_targetObject->create('EmployeeTest');
		$params = [
			[
				'', // $fieldName
				[], // $options
			],
			[
				'EmployeeTest.position', // $fieldName
				[
					'options' => [
						'US' => 'Eng',
						'RU' => 'Rus',
					]
				], // $options
			],
		];
		$expected = [
			'<div class="form-group"><div id="EmployeeTest" data-input-name="data[EmployeeTest]" data-toggle="flag-select" data-button-type="btn-default form-control"></div></div>',
			'<div class="form-group"><div id="EmployeeTestPosition" data-input-name="data[EmployeeTest][position]" data-countries="{&quot;US&quot;:&quot;Eng&quot;,&quot;RU&quot;:&quot;Rus&quot;}" data-toggle="flag-select" data-button-type="btn-default form-control"></div></div>',
		];
		$this->runClassMethodGroup('flag', $params, $expected);
	}

/**
 * testAutocomplete method
 *
 * @return void
 */
	public function testAutocomplete() {
		$this->_targetObject->create('EmployeeTest');
		$params = [
			[
				'', // $fieldName
				[], // $options
			],
			[
				'EmployeeTest.position', // $fieldName
				[
					'title' => 'Some title',
					'min-length' => '2',
					'local' => ['Водитель', 'Ведущий инженер']
				], // $options
			],
			[
				'EmployeeTest.position', // $fieldName
				[
					'type' => 'EmployeeTest.mail',
				], // $options
			],
			[
				'EmployeeTest.position', // $fieldName
				[
					'type' => 'EmployeeTest.position',
					'url' => '/admin/some_controller/autocompl.json'
				], // $options
			],
		];
		$expected = [
			'<input name="data[EmployeeTest]" data-toggle="autocomplete" data-autocomplete-url="/cake_theme/filter/autocomplete.json" type="text" id="EmployeeTest"/>',
			'<input name="data[EmployeeTest][position]" data-toggle="autocomplete" title="Some title" data-autocomplete-min-length="2" data-autocomplete-local="[&quot;\u0412\u043e\u0434\u0438\u0442\u0435\u043b\u044c&quot;,&quot;\u0412\u0435\u0434\u0443\u0449\u0438\u0439 \u0438\u043d\u0436\u0435\u043d\u0435\u0440&quot;]" type="text" id="EmployeeTestPosition"/>',
			'<input name="data[EmployeeTest][position]" data-toggle="autocomplete" data-autocomplete-type="EmployeeTest.mail" data-autocomplete-url="/cake_theme/filter/autocomplete.json" type="text" id="EmployeeTestPosition"/>',
			'<input name="data[EmployeeTest][position]" data-toggle="autocomplete" data-autocomplete-type="EmployeeTest.position" data-autocomplete-url="/admin/some_controller/autocompl.json" type="text" id="EmployeeTestPosition"/>',
		];
		$this->runClassMethodGroup('autocomplete', $params, $expected);
	}

/**
 * testCreateUploadForm method
 *
 * @return void
 */
	public function testCreateUploadForm() {
		$params = [
			[
				'', // $model
				[], // $options
			],
			[
				'EmployeeTest', // $model
				[], // $options
			],
		];
		$expected = [
			'<form  role="form" requiredcheck="1" data-required-msg="' . __d('view_extension', 'Please fill in this field') . '" id="addForm" onsubmit="event.returnValue = false; return false;" enctype="multipart/form-data" method="post" accept-charset="utf-8"><div style="display:none;"><input type="hidden" name="_method" value="POST"/></div>',
			'<form  role="form" requiredcheck="1" data-required-msg="' . __d('view_extension', 'Please fill in this field') . '" id="EmployeeTestAddForm" onsubmit="event.returnValue = false; return false;" enctype="multipart/form-data" method="post" accept-charset="utf-8"><div style="display:none;"><input type="hidden" name="_method" value="POST"/></div>',
		];
		$this->runClassMethodGroup('createUploadForm', $params, $expected);
	}

/**
 * testUpload method
 *
 * @return void
 */
	public function testUpload() {
		$params = [
			[
				'', // $url
				1024, // $maxfilesize
				'/\.(jpe?g)$/i', // $acceptfiletypes
				'/admin/some_controller/some_action', // $redirecturl
				'Upload...', // $btnTitle
				'btn-danger', // $btnClass
			],
			[
				'/admin/some_controller/upload.json', // $url
				1024, // $maxfilesize
				'/\.(jpe?g)$/i', // $acceptfiletypes
				'/admin/some_controller/some_action', // $redirecturl
				'', // $btnTitle
				'', // $btnClass
			],
			[
				'/admin/some_controller/upload.json', // $url
				1024, // $maxfilesize
				'/\.(jpe?g)$/i', // $acceptfiletypes
				'/admin/some_controller/some_action', // $redirecturl
				'Upload...', // $btnTitle
				'btn-danger', // $btnClass
			],
		];
		$expected = [
			null,
			[
				'assertRegExp' => '/\<span class\="fileinput\-button btn btn\-success"\>\<span class\="fas fa\-file\-upload fa\-lg"\>\<\/span\>&nbsp;\<span\>' . __d('view_extension', 'Select file...') . '\<\/span\>\<input type\="file" name\="files" id\="input_[0-9a-f]+" data\-progress\-id\="progress_[0-9a-f]+" data\-files\-id\="files_[0-9a-f]+" data\-fileupload\-url\="\/admin\/some_controller\/upload.json" data\-fileupload\-maxfilesize\="1024" data\-fileupload\-acceptfiletypes\="\/\\\.\(jpe\?g\)\$\/i" data\-fileupload\-redirecturl\="\/admin\/some_controller\/some_action"\ data\-toggle\="fileupload" data\-fileupload\-btntext\-upload\="' . __d('view_extension', 'Upload') . '" data\-fileupload\-btntext\-abort\="' . __d('view_extension', 'Abort') . '" data\-fileupload\-msgtext\-processing\="' . __d('view_extension', 'Processing...') . '" data\-fileupload\-msgtext\-error\="' . __d('view_extension', 'File upload failed') . '"\/\>\<\/span\>\<br\>\<br\>\<div id\="progress_[0-9a-f]+" class\="progress"\>\<div class\="progress\-bar progress\-bar\-success"\>\<\/div\>\<\/div\>\<hr\>\<div id\="files_[0-9a-f]+" class\="files"\>\<\/div\>\<br\>/',
			],
			[
				'assertRegExp' => '/\<span class\="fileinput\-button btn btn\-danger"\>Upload...\<input type\="file" name\="files" id\="input_[0-9a-f]+" data\-progress\-id\="progress_[0-9a-f]+" data\-files\-id\="files_[0-9a-f]+" data\-fileupload\-url\="\/admin\/some_controller\/upload.json" data\-fileupload\-maxfilesize\="1024" data\-fileupload\-acceptfiletypes\="\/\\\.\(jpe\?g\)\$\/i" data\-fileupload\-redirecturl\="\/admin\/some_controller\/some_action" data\-toggle\="fileupload" data\-fileupload\-btntext\-upload\="' . __d('view_extension', 'Upload') . '" data\-fileupload\-btntext\-abort\="' . __d('view_extension', 'Abort') . '" data\-fileupload\-msgtext\-processing\="' . __d('view_extension', 'Processing...') . '" data\-fileupload\-msgtext\-error\="' . __d('view_extension', 'File upload failed') . '"\/\>\<\/span\>\<br\>\<br\>\<div id\="progress_[0-9a-f]+" class\="progress"\>\<div class\="progress\-bar progress\-bar\-success"\>\<\/div\>\<\/div\>\<hr\>\<div id\="files_[0-9a-f]+" class\="files"\>\<\/div\>\<br\>/',
			]
		];
		$this->runClassMethodGroup('upload', $params, $expected);
	}

/**
 * testHiddenFields method
 *
 * @return void
 */
	public function testHiddenFields() {
		$this->EmployeeTest->validator()->add('id', 'required', [
			'rule' => 'notBlank',
			'required' => true,
		]);
		$this->EmployeeTest->invalidate('id', 'Some error...');
		$this->_targetObject->create('EmployeeTest');
		$params = [
			[
				'', // $hiddenFields
			],
			[
				'EmployeeTest.id', // $hiddenFields
			],
			[
				[
					'EmployeeTest.id',
					'EmployeeTest.department_id',
				] // $hiddenFields
			],
		];
		$expected = [
			null,
			'<input type="hidden" name="data[EmployeeTest][id]" id="EmployeeTestId" class="form-error"/><div class="form-group has-error"><div class="help-block">Some error...</div></div>',
			'<input type="hidden" name="data[EmployeeTest][id]" id="EmployeeTestId" class="form-error"/><div class="form-group has-error"><div class="help-block">Some error...</div></div><input type="hidden" name="data[EmployeeTest][department_id]" id="EmployeeTestDepartmentId"/>',
		];
		$this->runClassMethodGroup('hiddenFields', $params, $expected);
	}

/**
 * testCreateFormTabs method
 *
 * @return void
 */
	public function testCreateFormTabs() {
		$this->EmployeeTest->validator()->add('id', 'required', [
			'rule' => 'notBlank',
			'required' => true,
		]);
		$this->EmployeeTest->validator()->add('upn', 'required', [
			'rule' => 'notBlank',
			'required' => true,
		]);
		$this->EmployeeTest->validator()->add('full_name', 'required', [
			'rule' => 'notBlank',
			'required' => true,
		]);
		$this->EmployeeTest->validator()->add('mail', 'required', [
			'rule' => 'notBlank',
			'required' => true,
		]);
		$this->EmployeeTest->validator()->add('manager', 'required', [
			'rule' => 'notBlank',
			'required' => true,
		]);
		$this->EmployeeTest->validator()->add('block', 'required', [
			'rule' => 'notBlank',
			'required' => true,
		]);
		$this->EmployeeTest->invalidate('id', 'Invalid ID');
		$this->EmployeeTest->invalidate('full_name', 'Invalid full name');
		$this->EmployeeTest->invalidate('mail', 'Invalid E-mail');
		$this->EmployeeTest->invalidate('manager', 'Invalid manager');
		$this->EmployeeTest->invalidate('block', 'Invalid state');

		$params = [
			[
				[], // $inputList
				['EmployeeTest.position'], // $inputStaticList
				[
					'Name' => [
						'EmployeeTest.id',
						'EmployeeTest.department_id',
						'EmployeeTest.position',
						'EmployeeTest.upn',
						'EmployeeTest.last_name',
						'EmployeeTest.first_name',
						'EmployeeTest.middle_name',
						'EmployeeTest.full_name',
					],
					'E-mail' => [
						'EmployeeTest.mail',
						'EmployeeTest.manager',
						'EmployeeTest.block',
					],
				], // $tabsList
				'Some legend', // $legend
				'EmployeeTest', // $modelName
				[
					'url' => [
						'controller' => 'some_controller',
						'action' => 'some_action'
					]
				], // $options
			],
			[
				[
					'EmployeeTest.id' => ['type' => 'hidden'],
					'EmployeeTest.department_id' => ['type' => 'hidden'],
					'EmployeeTest.position' => ['type' => 'hidden'],
					'EmployeeTest.upn' => ['type' => 'text', 'label' => ['User  principal name', 'UPN of employee', ':']],
					'EmployeeTest.last_name' => ['type' => 'text', 'label' => ['Last name', 'Last name of employee', ':']],
					'EmployeeTest.first_name' => ['type' => 'text', 'label' => ['First name', 'First name of employee', ':']],
					'EmployeeTest.middle_name' => ['type' => 'text', 'label' => ['Middle name', 'Middle name of employee', ':']],
					'EmployeeTest.full_name' => ['type' => 'text', 'label' => ['Full name', 'Full name of employee', ':']],
					'EmployeeTest.mail' => ['type' => 'email', 'label' => ['E-mail', 'E-mail name of employee', ':']],
					'EmployeeTest.manager' => ['type' => 'select', 'label' => ['Manager', 'Manager of employee', ':'], 'options' => ['1' => 'Сазонов А.П.', '2' => 'Костин Д.И.', '3' => 'Герасимова Н.М.']],
					'EmployeeTest.block' => ['type' => 'checkbox', 'label' => ['Block', 'State of employee', ':']],
				], // $inputList
				['EmployeeTest.position'], // $inputStaticList
				[], // $tabsList
				'Some legend', // $legend
				'EmployeeTest', // $modelName
				[
					'url' => [
						'controller' => 'some_controller',
						'action' => 'some_action'
					]
				], // $options
			],
			[
				[
					'EmployeeTest.id' => ['type' => 'hidden'],
					'EmployeeTest.department_id' => ['type' => 'hidden'],
					'EmployeeTest.position' => ['type' => 'hidden'],
					'EmployeeTest.upn' => ['type' => 'text', 'label' => ['User  principal name', 'UPN of employee', ':']],
					'EmployeeTest.last_name' => ['type' => 'text', 'label' => ['Last name', 'Last name of employee', ':']],
					'EmployeeTest.first_name' => ['type' => 'text', 'label' => ['First name', 'First name of employee', ':']],
					'EmployeeTest.middle_name' => ['type' => 'text', 'label' => ['Middle name', 'Middle name of employee', ':']],
					'EmployeeTest.full_name' => ['type' => 'text', 'label' => ['Full name', 'Full name of employee', ':']],
					'EmployeeTest.mail' => ['type' => 'email', 'label' => ['E-mail', 'E-mail name of employee', ':']],
					'EmployeeTest.manager' => ['type' => 'select', 'label' => ['Manager', 'Manager of employee', ':'], 'options' => ['1' => 'Сазонов А.П.', '2' => 'Костин Д.И.', '3' => 'Герасимова Н.М.']],
					'EmployeeTest.block' => ['type' => 'checkbox', 'label' => ['Block', 'State of employee', ':']],
				], // $inputList
				['EmployeeTest.position'], // $inputStaticList
				[
					'Name' => [
						'EmployeeTest.id',
						'EmployeeTest.department_id',
						'EmployeeTest.position',
						'EmployeeTest.upn',
						'EmployeeTest.last_name',
						'EmployeeTest.first_name',
						'EmployeeTest.middle_name',
						'EmployeeTest.full_name',
						'EmployeeTest.bad_field',
						'BadModel.SomeField',
					],
					'E-mail' => [
						'EmployeeTest.mail',
						'EmployeeTest.manager',
						'EmployeeTest.block',
					],
				], // $tabsList
				'Some legend', // $legend
				'EmployeeTest', // $modelName
				[
					'url' => [
						'controller' => 'some_controller',
						'action' => 'some_action'
					]
				], // $options
			],
		];
		$expected = [
			null,
			null,
			'<form action="/admin/some_controller/some_action" role="form" requiredcheck="1" data-required-msg="' . __d('view_extension', 'Please fill in this field') . '" class="form-tabs form-default" progressfill="1" id="EmployeeTestSomeActionForm" method="post" accept-charset="utf-8"><div style="display:none;"><input type="hidden" name="_method" value="POST"/></div><fieldset><legend>Some legend</legend><div class="row bottom-buffer"><div class="tabbable"><ul class="nav nav-pills nav-stacked col-xs-4 col-sm-4 col-md-4 col-lg-4"><li class="active"><a href="#tabForm1" data-toggle="tab">Name&nbsp;<span class="fas fa-exclamation-triangle fa-lg"></span></a></li><li><a href="#tabForm2" data-toggle="tab">E-mail&nbsp;<span class="fas fa-exclamation-triangle fa-lg"></span></a></li></ul><div class="tab-content col-xs-8 col-lg-8 col-sm-8 col-md-8 col-lg-8"><div id="tabForm1" class="tab-pane active"><input type="hidden" name="data[EmployeeTest][id]" id="EmployeeTestId" class="form-error"/><div class="form-group has-error"><div class="help-block">Invalid ID</div></div><input type="hidden" name="data[EmployeeTest][department_id]" id="EmployeeTestDepartmentId"/><input type="hidden" name="data[EmployeeTest][position]" id="EmployeeTestPosition"/><div class="form-group required"><label for="EmployeeTestUpn" class="control-label">User  principal name&nbsp;<abbr title="UPN of employee" data-toggle="tooltip">[?]</abbr>:</label> <input name="data[EmployeeTest][upn]" class="form-control" maxlength="255" type="text" id="EmployeeTestUpn" required="required"/></div><div class="form-group"><label for="EmployeeTestLastName" class="control-label">Last name&nbsp;<abbr title="Last name of employee" data-toggle="tooltip">[?]</abbr>:</label> <input name="data[EmployeeTest][last_name]" class="form-control" maxlength="255" type="text" id="EmployeeTestLastName"/></div><div class="form-group"><label for="EmployeeTestFirstName" class="control-label">First name&nbsp;<abbr title="First name of employee" data-toggle="tooltip">[?]</abbr>:</label> <input name="data[EmployeeTest][first_name]" class="form-control" maxlength="255" type="text" id="EmployeeTestFirstName"/></div><div class="form-group"><label for="EmployeeTestMiddleName" class="control-label">Middle name&nbsp;<abbr title="Middle name of employee" data-toggle="tooltip">[?]</abbr>:</label> <input name="data[EmployeeTest][middle_name]" class="form-control" maxlength="255" type="text" id="EmployeeTestMiddleName"/></div><div class="form-group required has-error error"><label for="EmployeeTestFullName" class="control-label">Full name&nbsp;<abbr title="Full name of employee" data-toggle="tooltip">[?]</abbr>:</label> <input name="data[EmployeeTest][full_name]" class="form-control form-error" maxlength="255" type="text" id="EmployeeTestFullName" required="required"/><div class="help-block">Invalid full name</div></div></div><div id="tabForm2" class="tab-pane"><div class="form-group required has-error error"><label for="EmployeeTestMail" class="control-label">E-mail&nbsp;<abbr title="E-mail name of employee" data-toggle="tooltip">[?]</abbr>:</label> <input name="data[EmployeeTest][mail]" class="form-control form-error" maxlength="256" data-inputmask-alias="email" type="text" id="EmployeeTestMail" required="required"/><div class="help-block">Invalid E-mail</div></div><div class="form-group required has-error error"><label for="EmployeeTestManager" class="control-label">Manager&nbsp;<abbr title="Manager of employee" data-toggle="tooltip">[?]</abbr>:</label> <select name="data[EmployeeTest][manager]" class="form-control form-error" data-toggle="select" id="EmployeeTestManager" required="required">' . "\n" .
				'<option value="1">Сазонов А.П.</option>' . "\n" .
				'<option value="2">Костин Д.И.</option>' . "\n" .
				'<option value="3">Герасимова Н.М.</option>' . "\n" .
			'</select><div class="help-block">Invalid manager</div></div><div class="form-group required has-error error"><label for="EmployeeTestBlock" class="control-label">Block&nbsp;<abbr title="State of employee" data-toggle="tooltip">[?]</abbr>:</label> <div class="checkbox"><input type="hidden" name="data[EmployeeTest][block]" id="EmployeeTestBlock_" value="0" class="form-error"/><input type="checkbox" name="data[EmployeeTest][block]" value="1" id="EmployeeTestBlock" class="form-error"/></div><div class="help-block">Invalid state</div></div></div></div></div></div></fieldset><div class="form-group text-center"><input class="btn btn-success btn-md" type="submit" value="' . __d('view_extension', 'Save') . '"/></div></form>',
		];
		$this->runClassMethodGroup('createFormTabs', $params, $expected);
	}
}
