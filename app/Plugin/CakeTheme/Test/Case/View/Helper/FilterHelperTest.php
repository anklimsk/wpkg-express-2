<?php
App::uses('AppCakeTestCase', 'CakeTheme.Test');
App::uses('View', 'View');
App::uses('Helper', 'View');
App::uses('FilterHelper', 'CakeTheme.View/Helper');
App::uses('CakeRequest', 'Network');
App::uses('Hash', 'Utility');
require_once App::pluginPath('CakeTheme') . 'Test' . DS . 'Model' . DS . 'modelsFixt.php';

/**
 * FilterHelper Test Case
 */
class FilterHelperTest extends AppCakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = [
		'plugin.cake_theme.employees',
	];

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();

		$View = new View();
		$View->request = new CakeRequest('/employees/filter', false);
		$this->_targetObject = new FilterHelper($View);
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
 * testOpenFilterForm method
 *
 * @return void
 */
	public function testOpenFilterForm() {
		$result = $this->_targetObject->openFilterForm(false);
		$expected = '<form action="/employees/filter" role="form" data-toggle="pjax-form" class="filter-form clone-wrapper" autocomplete="off" data-max-clone="' . CAKE_THEME_FILTER_ROW_LIMIT . '" id="Form" method="get" accept-charset="utf-8">';
		$this->assertData($expected, $result);

		$result = $this->_targetObject->openFilterForm(true);
		$expected = '<form action="/employees/filter" role="form" data-toggle="ajax-form" class="filter-form clone-wrapper" autocomplete="off" data-max-clone="' . CAKE_THEME_FILTER_ROW_LIMIT . '" id="Form" method="post" accept-charset="utf-8">' .
			'<div style="display:none;"><input type="hidden" name="_method" value="POST"/></div>';
		$this->assertData($expected, $result);
	}

/**
 * testCloseFilterForm method
 *
 * @return void
 */
	public function testCloseFilterForm() {
		$this->_targetObject->openFilterForm(false);
		$result = $this->_targetObject->closeFilterForm();
		$expected = '</form>';
		$this->assertData($expected, $result);

		$this->_targetObject->openFilterForm(true);
		$result = $this->_targetObject->closeFilterForm();
		$expected = '</form>';
		$this->assertData($expected, $result);
	}

/**
 * testCreateFilterForm method
 *
 * @return void
 */
	public function testCreateFilterForm() {
		Configure::write('Config.language', 'eng');
		$params = [
			[
				[], // $formInputs
				null, // $plugin
				true, // $usePrint
				null, // $exportType
			],
			[
				[
					'EmployeeTest',
					'EmployeeTest.upn' => [
						'options' => [
							'a.sazonov@fabrikam.com' => 'Сазонов А.П.',
							'd.kostin@fabrikam.com' => 'Костин Д.И.'
						]
					],
					'EmployeeTest.position' => ['class-header' => 'some-class'],
					'EmployeeTest.birthday',
					'EmployeeTest.full_name' => ['not-use-input' => true],
					'EmployeeTestlast_name',
					'EmployeeTest.manager',
					'EmployeeTest.block' => [
						'label' => '<b>Block</b>',
						'escape' => false,
						'pagination-field' => 'SomeModel.some_field'
					],
					'EmployeeTest.mail' => [
						'disabled' => true,
					]
				], // $formInputs
				null, // $plugin
				true, // $usePrint
				'docx', // $exportType
			],
		];
		$expected = [
			'',
			'<tr><th><a href="/index/sort:EmployeeTest.upn/direction:asc" title="' . __d('view_extension', 'Click to sort by this fiels') . '" data-toggle="pjax">EmployeeTest.upn</a></th> <th class="some-class"><a href="/index/sort:EmployeeTest.position/direction:asc" title="' . __d('view_extension', 'Click to sort by this fiels') . '" data-toggle="pjax">EmployeeTest.position</a></th> <th><a href="/index/sort:EmployeeTest.birthday/direction:asc" title="' . __d('view_extension', 'Click to sort by this fiels') . '" data-toggle="pjax">EmployeeTest.birthday</a></th> <th><a href="/index/sort:EmployeeTest.full_name/direction:asc" title="' . __d('view_extension', 'Click to sort by this fiels') . '" data-toggle="pjax">EmployeeTest.full_name</a></th> <th><a href="/index/sort:EmployeeTest.manager/direction:asc" title="' . __d('view_extension', 'Click to sort by this fiels') . '" data-toggle="pjax">EmployeeTest.manager</a></th> <th><a href="/index/sort:SomeModel.some_field/direction:asc" title="' . __d('view_extension', 'Click to sort by this fiels') . '" data-toggle="pjax"><b>Block</b></a></th> <th>EmployeeTest.mail</th> <th class="action hide-popup">' . __d('view_extension', 'Actions') . '</th></tr><tr class="active filter-header-row"><th colspan="10" class="text-center"><span class="action pull-left hidden-print hide-popup"><button class="btn btn-default btn-xs show-filter-btn" title="' . __d('view_extension', 'Show or hide filter') . '" data-toggle="collapse" data-target=".filter-collapse" aria-expanded="false" data-toggle-icons="fa-caret-square-down,fa-caret-square-up" type="button"><span class="far fa-caret-square-down fa-fw fa-lg"></span></button><a href=".prt" title="' . __d('view_extension', 'Print informations') . '" data-toggle="tooltip" target="_blank" class="btn btn-default btn-xs"><span class="fas fa-print fa-fw fa-lg"></span></a><a href=".docx" title="' . __d('view_extension', 'Export informations') . '" data-toggle="tooltip" class="btn btn-default btn-xs"><span class="far fa-file-word fa-fw fa-lg"></span></a></span>' . __d('view_extension', 'Data filter for the table: %s', '<code data-toggle="filter-conditions" data-empty-text="' . htmlentities(__d('view_extension', '&lt;None&gt;')) . '">' . __d('view_extension', '&lt;None&gt;') . '</code>') . '<span class="action pull-right hidden-print hide-popup"><select name="data[FilterCond][group]" title="' . __d('view_extension', 'Change the logical condition of the group') . '" class="form-control show-tick filter-condition input-xs" autocomplete="off" id="FilterCondGroup" data-toggle="select" data-style="btn-default btn-filter-condition btn-xs" data-width="fit" data-live-search="false">' . "\n" .
				'<option value="" title="&amp;nbsp;&amp;&amp;" data-subtext="' . __d('view_extension', 'And') . '">&nbsp;&&</option>' . "\n" .
				'<option value="or" title="&amp;nbsp;||" data-subtext="' . __d('view_extension', 'Or') . '">&nbsp;||</option>' . "\n" .
				'<option value="not" title="&amp;nbsp;!" data-subtext="' . __d('view_extension', 'Not') . '">&nbsp;!</option>' . "\n" .
				'</select><button type="submit" class="btn btn-info btn-xs exclude-clone filter-apply" title="' . __d('view_extension', 'Apply filter') . '" data-toggle="tooltip" value="filter-apply" name="data[FilterAction]"><span class="fas fa-filter fa-fw fa-lg"></span></button><button type="reset" class="btn btn-warning btn-xs exclude-clone filter-clear" title="' . __d('view_extension', 'Clear filter') . '" data-toggle="tooltip"><span class="fas fa-eraser fa-fw fa-lg"></span></button></span></th></tr><tr data-toggle="clone-source" class="filter-collapse collapse filter-controls-row"><th><div class="form-group"><div class="input select"><select name="data[FilterData][0][EmployeeTest][upn]" class="form-control input-xs" id="FilterData0EmployeeTestUpn" data-toggle="select" data-style="btn-default btn-xs">' . "\n" .
					'<option value="">--' . __d('view_extension', 'Sel.') . '--</option>' . "\n" .
					'<option value="a.sazonov@fabrikam.com">Сазонов А.П.</option>' . "\n" .
					'<option value="d.kostin@fabrikam.com">Костин Д.И.</option>' . "\n" .
				'</select></div></div></th> <th><div class="form-group"><input name="data[FilterData][0][EmployeeTest][position]" data-toggle="autocomplete" class="form-control input-xs clear-btn" id="FilterData0EmployeeTestPosition" data-autocomplete-type="EmployeeTest.position" data-autocomplete-url="/cake_theme/filter/autocomplete.json" type="text"/></div></th> <th><div class="form-group"><div class="input-group"><div class="input-group-btn"><select name="data[FilterCond][0][EmployeeTest][birthday]" title="' . __d('view_extension', 'Change the logical condition of the field') . '" class="form-control show-tick filter-condition input-xs" autocomplete="off" id="FilterCond0EmployeeTestBirthday" data-toggle="select" data-style="btn-default btn-filter-condition btn-xs" data-width="fit" data-live-search="false">' . "\n" .
					'<option value="" title="=" data-subtext="' . __d('view_extension', 'Equal') . '">=</option>' . "\n" .
					'<option value="gt" title="&amp;gt;" data-subtext="' . __d('view_extension', 'Greater than') . '">&gt;</option>' . "\n" .
					'<option value="ge" title="&amp;ge;" data-subtext="' . __d('view_extension', 'Greater than or equal to') . '">&ge;</option>' . "\n" .
					'<option value="lt" title="&amp;lt;" data-subtext="' . __d('view_extension', 'Less than') . '">&lt;</option>' . "\n" .
					'<option value="le" title="&amp;le;" data-subtext="' . __d('view_extension', 'Less than or equal to') . '">&le;</option>' . "\n" .
					'<option value="ne" title="&amp;ne;" data-subtext="' . __d('view_extension', 'Not equal') . '">&ne;</option>' . "\n" .
					'</select></div><input name="data[FilterData][0][EmployeeTest][birthday]" data-toggle="datetimepicker" class="form-control input-xs" id="FilterData0EmployeeTestBirthday" data-inputmask-alias="yyyy-mm-dd" data-date-locale="en" data-widget-position-vertical="bottom" data-icon-type="false" data-date-format="YYYY-MM-DD" type="text"/></div></div></th> <th></th> <th><div class="form-group"><div class="input select"><select name="data[FilterData][0][EmployeeTest][manager]" class="form-control input-xs" id="FilterData0EmployeeTestManager" data-toggle="select" data-style="btn-default btn-xs">' . "\n" .
				'<option value="">' . '--' . __d('view_extension', 'Sel.') . '--' . '</option>' . "\n" .
				'<option value="0">' . __d('view_extension', 'No') . '</option>' . "\n" .
				'<option value="1">' . __d('view_extension', 'Yes') . '</option>' . "\n" .
				'</select></div></div></th> <th><div class="form-group"><div class="input select"><select name="data[FilterData][0][EmployeeTest][block]" class="form-control input-xs" data-filter-label="&lt;b&gt;Block&lt;/b&gt;" id="FilterData0EmployeeTestBlock" data-toggle="select" data-style="btn-default btn-xs">' . "\n" .
				'<option value="">' . '--' . __d('view_extension', 'Sel.') . '--' . '</option>' . "\n" .
				'<option value="0">' . __d('view_extension', 'No') . '</option>' . "\n" .
				'<option value="1">' . __d('view_extension', 'Yes') . '</option>' . "\n" .
				'</select></div></div></th> <th><div class="form-group"><input name="data[FilterData][0][EmployeeTest][mail]" data-toggle="autocomplete" disabled="disabled" class="form-control input-xs clear-btn" id="FilterData0EmployeeTestMail" data-autocomplete-type="EmployeeTest.mail" data-autocomplete-url="/cake_theme/filter/autocomplete.json" type="text"/></div></th> <th class="filter-action text-center"><button title="' . __d('view_extension', 'Add row of filter') . '" data-toggle="btn-action-clone" class="btn btn-success btn-xs" type="button"><span class="fas fa-plus fa-fw fa-lg"></span></button><button title="' . __d('view_extension', 'Delete row of filter') . '" data-toggle="btn-action-delete" class="btn btn-danger btn-xs" type="button"><span class="fas fa-trash-alt fa-fw fa-lg"></span></button></th></tr>'
		];
		$this->runClassMethodGroup('createFilterForm', $params, $expected);
	}

/**
 * testCreateGroupActionControls method
 *
 * @return void
 */
	public function testCreateGroupActionControls() {
		$selectOptions = [
			[
				'text' => 'Delete data',
				'value' => 'del',
			],
			[
				'text' => 'Some action',
				'value' => 'test',
			]
		];
		$params = [
			[
				[], // $formInputs
				null, // $groupActions
				false, // $useSelectAll
			],
			[
				[
					'EmployeeTest',
					'EmployeeTest.position',
					'EmployeeTest.birthday',
					'EmployeeTest.manager',
					'EmployeeTest.block',
					'EmployeeTest.upn',
					'EmployeeTest.mail'
				], // $formInputs
				null, // $groupActions
				false, // $useSelectAll
			],
			[
				[
					'EmployeeTest',
					'EmployeeTest.position',
					'EmployeeTest.birthday',
					'EmployeeTest.manager',
					'EmployeeTest.block',
					'EmployeeTest.upn',
					'EmployeeTest.mail'
				], // $formInputs
				['del' => 'Delete data', 'test' => 'Some action'], // $groupActions
				true, // $useSelectAll
			],
			[
				[
					'EmployeeTest',
					'EmployeeTest.position',
					'EmployeeTest.birthday',
				], // $formInputs
				null, // $groupActions
				true, // $useSelectAll
			],
			[
				[
					'EmployeeTest.position',
				], // $formInputs
				null, // $groupActions
				true, // $useSelectAll
			],
		];
		$expected = [
			'',
			'',
			'<tr class="active"><td><button class="btn btn-info btn-xs hidden-print" title="' . __d('view_extension', 'Select / deselect all') . '" data-toggle="btn-action-select-all" data-toggle-icons="fa-check-square,fa-square" type="button"><span class="far fa-check-square fa-fw fa-lg"></span></button></td> <td colspan="6" class="text-center"><input type="hidden" name="data[FilterGroup][action]" id="FilterGroupAction"/><strong>' . __d('view_extension', 'Group data processing') . '</strong></td> <td class="action text-center"><button data-dialog-sel-options="' . htmlentities(json_encode($selectOptions)) . '" type="submit" title="' . __d('view_extension', 'Perform action') . '" data-toggle="tooltip" value="group-action" name="data[FilterAction]" data-dialog-title="' . __d('view_extension', 'Action to perform') . '" data-dialog-sel-placeholder="' . __d('view_extension', 'Select a action') . '" data-dialog-btn-ok="' . __d('view_extension', 'Perform') . '" data-dialog-btn-cancel="' . __d('view_extension', 'Cancel') . '" class="btn btn-warning btn-xs"><span class="fas fa-cog fa-fw fa-lg"></span></button></td></tr>',
			'<tr class="active"><td><button class="btn btn-info btn-xs hidden-print" title="' . __d('view_extension', 'Select / deselect all') . '" data-toggle="btn-action-select-all" data-toggle-icons="fa-check-square,fa-square" type="button"><span class="far fa-check-square fa-fw fa-lg"></span></button></td> <td colspan="3"></td></tr>',
			'<tr class="active"><td><button class="btn btn-info btn-xs hidden-print" title="' . __d('view_extension', 'Select / deselect all') . '" data-toggle="btn-action-select-all" data-toggle-icons="fa-check-square,fa-square" type="button"><span class="far fa-check-square fa-fw fa-lg"></span></button></td> <td></td></tr>',
		];
		$this->runClassMethodGroup('createGroupActionControls', $params, $expected);
	}

/**
 * testCreateFilterRowCheckbox method
 *
 * @return void
 */
	public function testCreateFilterRowCheckbox() {
		$params = [
			[
				null, // $inputField
				null, // $value
			],
			[
				'EmployeeTest', // $inputField
				null, // $value
			],
			[
				'EmployeeTest', // $inputField
				'1999-01-01', // $value
			],
			[
				'EmployeeTest.birthday', // $inputField
				'1999-01-01', // $value
			],
		];
		$expected = [
			'',
			'',
			'',
			['assertRegExp' => '/\<div class\="checkbox"\>\<input type\="checkbox" name\="data\[FilterData\]\[0\]\[EmployeeTest\]\[birthday\]\[\]" value\="1999\-01\-01" id\="FilterData0EmployeeTestBirthday.+"\/\>\<label for\="FilterData0EmployeeTestBirthday.+"\>\<\/label\>\<\/div\>/'],
		];
		$this->runClassMethodGroup('createFilterRowCheckbox', $params, $expected);
	}

/**
 * testGetBtnConditionField method
 *
 * @return void
 */
	public function testGetBtnConditionField() {
		$result = $this->_targetObject->getBtnConditionField();
		$expected = '';
		$this->assertData($expected, $result);

		$result = $this->_targetObject->getBtnConditionField('FilterCond.0.EmployeeTest.position', false);
		$expected = '<select name="data[FilterCond][0][EmployeeTest][position]" title="' . __d('view_extension', 'Change the logical condition of the field') . '" class="form-control show-tick filter-condition input-xs" autocomplete="off" id="FilterCond0EmployeeTestPosition" data-toggle="select" data-style="btn-default btn-filter-condition btn-xs" data-width="fit" data-live-search="false">' . "\n" .
			'<option value="" title="=" data-subtext="' . __d('view_extension', 'Equal') . '">=</option>' . "\n" .
			'<option value="gt" title="&amp;gt;" data-subtext="' . __d('view_extension', 'Greater than') . '">&gt;</option>' . "\n" .
			'<option value="ge" title="&amp;ge;" data-subtext="' . __d('view_extension', 'Greater than or equal to') . '">&ge;</option>' . "\n" .
			'<option value="lt" title="&amp;lt;" data-subtext="' . __d('view_extension', 'Less than') . '">&lt;</option>' . "\n" .
			'<option value="le" title="&amp;le;" data-subtext="' . __d('view_extension', 'Less than or equal to') . '">&le;</option>' . "\n" .
			'<option value="ne" title="&amp;ne;" data-subtext="' . __d('view_extension', 'Not equal') . '">&ne;</option>' . "\n" .
			'</select>';
		$this->assertData($expected, $result);

		$data = [];
		$inputField = 'FilterCond.0.EmployeeTest.birthday';
		$this->_targetObject->request->data = Hash::insert($data, $inputField, 'le');
		$result = $this->_targetObject->getBtnConditionField($inputField, true);
		$expected = '<select name="data[FilterCond][0][EmployeeTest][birthday]" title="' . __d('view_extension', 'Change the logical condition of the field') . '" class="form-control show-tick filter-condition input-xs" autocomplete="off" id="FilterCond0EmployeeTestBirthday" data-toggle="select" data-style="btn-default btn-filter-condition btn-xs" data-width="fit" data-live-search="false">' . "\n" .
			'<option value="" title="=" data-subtext="' . __d('view_extension', 'Equal') . '">=</option>' . "\n" .
			'<option value="gt" title="&amp;gt;" data-subtext="' . __d('view_extension', 'Greater than') . '">&gt;</option>' . "\n" .
			'<option value="ge" title="&amp;ge;" data-subtext="' . __d('view_extension', 'Greater than or equal to') . '">&ge;</option>' . "\n" .
			'<option value="lt" title="&amp;lt;" data-subtext="' . __d('view_extension', 'Less than') . '">&lt;</option>' . "\n" .
			'<option value="le" title="&amp;le;" data-subtext="' . __d('view_extension', 'Less than or equal to') . '" selected="selected">&le;</option>' . "\n" .
			'<option value="ne" title="&amp;ne;" data-subtext="' . __d('view_extension', 'Not equal') . '">&ne;</option>' . "\n" .
			'</select>';
		$this->assertData($expected, $result);
	}

/**
 * testGetBtnConditionGroup method
 *
 * @return void
 */
	public function testGetBtnConditionGroup() {
		$result = $this->_targetObject->getBtnConditionGroup();
		$expected = '';
		$this->assertData($expected, $result);

		$result = $this->_targetObject->getBtnConditionGroup('FilterCond.group');
		$expected = '<select name="data[FilterCond][group]" title="' . __d('view_extension', 'Change the logical condition of the group') . '" class="form-control show-tick filter-condition input-xs" autocomplete="off" id="FilterCondGroup" data-toggle="select" data-style="btn-default btn-filter-condition btn-xs" data-width="fit" data-live-search="false">' . "\n" .
			'<option value="" title="&amp;nbsp;&amp;&amp;" data-subtext="' . __d('view_extension', 'And') . '">&nbsp;&&</option>' . "\n" .
			'<option value="or" title="&amp;nbsp;||" data-subtext="' . __d('view_extension', 'Or') . '">&nbsp;||</option>' . "\n" .
			'<option value="not" title="&amp;nbsp;!" data-subtext="' . __d('view_extension', 'Not') . '">&nbsp;!</option>' . "\n" .
			'</select>';
		$this->assertData($expected, $result);

		$data = [];
		$inputField = 'FilterCond.group';
		$this->_targetObject->request->data = Hash::insert($data, $inputField, 'or');
		$result = $this->_targetObject->getBtnConditionGroup($inputField);
		$expected = '<select name="data[FilterCond][group]" title="' . __d('view_extension', 'Change the logical condition of the group') . '" class="form-control show-tick filter-condition input-xs" autocomplete="off" id="FilterCondGroup" data-toggle="select" data-style="btn-default btn-filter-condition btn-xs" data-width="fit" data-live-search="false">' . "\n" .
			'<option value="" title="&amp;nbsp;&amp;&amp;" data-subtext="' . __d('view_extension', 'And') . '">&nbsp;&&</option>' . "\n" .
			'<option value="or" title="&amp;nbsp;||" data-subtext="' . __d('view_extension', 'Or') . '" selected="selected">&nbsp;||</option>' . "\n" .
			'<option value="not" title="&amp;nbsp;!" data-subtext="' . __d('view_extension', 'Not') . '">&nbsp;!</option>' . "\n" .
			'</select>';
		$this->assertData($expected, $result);
	}

/**
 * testGetFilterData method
 *
 * @return void
 */
	public function testGetFilterData() {
		$targetObject = $this->createProxyObject($this->_targetObject);
		$result = $targetObject->getFilterData();
		$expected = [];
		$this->assertData($expected, $result);

		$data = [];
		$targetObject->request->query = Hash::insert($data, 'data.BadData.someField', 'test');
		$result = $targetObject->getFilterData();
		$expected = [];
		$this->assertData($expected, $result);

		$data = [];
		$targetObject->request->query = Hash::insert($data, 'data.FilterData.EmployeeTest.birthday', '1995-08-16');
		$result = $targetObject->getFilterData();
		$expected = [
			'EmployeeTest' => [
				'birthday' => '1995-08-16'
			]
		];
		$this->assertData($expected, $result);

		$targetObject->_setFlagUsePost(true);
		$data = [];
		$targetObject->request->data = Hash::insert($data, 'BadData.someField', 'test');
		$result = $targetObject->getFilterData();
		$expected = [];
		$this->assertData($expected, $result);

		$data = [];
		$targetObject->request->data = Hash::insert($data, 'FilterData.EmployeeTest.birthday', '1995-08-16');
		$result = $targetObject->getFilterData();
		$expected = [
			'EmployeeTest' => [
				'birthday' => '1995-08-16'
			]
		];
		$this->assertData($expected, $result);

		$result = $targetObject->getFilterData('EmployeeTest.birthday');
		$expected = '1995-08-16';
		$this->assertData($expected, $result);
	}

/**
 * testGetFilterConditions method
 *
 * @return void
 */
	public function testGetFilterConditions() {
		$targetObject = new SebastianBergmann\PeekAndPoke\Proxy($this->_targetObject);
		$result = $targetObject->getFilterConditions();
		$expected = [];
		$this->assertData($expected, $result);

		$data = [];
		$targetObject->request->query = Hash::insert($data, 'data.BadData.someField', 'test');
		$result = $targetObject->getFilterConditions();
		$expected = [];
		$this->assertData($expected, $result);

		$data = [];
		$targetObject->request->query = Hash::insert($data, 'data.FilterCond.EmployeeTest.birthday', 'gt');
		$result = $targetObject->getFilterConditions();
		$expected = [
			'EmployeeTest' => [
				'birthday' => 'gt'
			]
		];
		$this->assertData($expected, $result);

		$targetObject->_setFlagUsePost(true);
		$data = [];
		$targetObject->request->data = Hash::insert($data, 'BadData.someField', 'test');
		$result = $targetObject->getFilterConditions();
		$expected = [];
		$this->assertData($expected, $result);

		$data = [];
		$targetObject->request->data = Hash::insert($data, 'FilterCond.EmployeeTest.birthday', 'lt');
		$result = $targetObject->getFilterConditions();
		$expected = [
			'EmployeeTest' => [
				'birthday' => 'lt'
			]
		];
		$this->assertData($expected, $result);

		$result = $targetObject->getFilterConditions('EmployeeTest.birthday');
		$expected = 'lt';
		$this->assertData($expected, $result);
	}

/**
 * testSetFilterInputData method
 *
 * @return void
 */
	public function testSetFilterInputData() {
		$this->_targetObject->request->data = [];
		$this->_targetObject->setFilterInputData();
		$result = $this->_targetObject->request->data;
		$expected = ['FilterData' => null];
		$this->assertData($expected, $result);

		$data = [
			'EmployeeTest' => [
				'birthday' => '1995-08-16'
			]
		];
		$this->_targetObject->request->data = [];
		$this->_targetObject->setFilterInputData($data);
		$result = $this->_targetObject->request->data;
		$expected = [
			'FilterData' => $data
		];
		$this->assertData($expected, $result);
	}

/**
 * testSetFilterInputConditions method
 *
 * @return void
 */
	public function testSetFilterInputConditions() {
		$this->_targetObject->request->data = [];
		$this->_targetObject->setFilterInputConditions();
		$result = $this->_targetObject->request->data;
		$expected = ['FilterCond' => null];
		$this->assertData($expected, $result);

		$data = [
			'EmployeeTest' => [
				'birthday' => 'ne'
			]
		];
		$this->_targetObject->request->data = [];
		$this->_targetObject->setFilterInputConditions($data);
		$result = $this->_targetObject->request->data;
		$expected = [
			'FilterCond' => $data
		];
		$this->assertData($expected, $result);
	}

/**
 * testPrepareOptions method
 *
 * @return void
 */
	public function testPrepareOptions() {
		$params = [
			[
				'', // $fieldName
				[], // $options
				0, // $index
				null, // $plugin
			],
			[
				'EmployeeTest.birthday', // $fieldName
				[
					'pagination-field' => 'SomeModel.birth',
					'disabled' => false
				], // $options
				1, // $index
				null, // $plugin
			],
			[
				'EmployeeTest.birthday', // $fieldName
				[
					'disabled' => true
				], // $options
				5, // $index
				null, // $plugin
			],
		];
		$expected = [
			[],
			[
				'div' => 'input-group',
				'widget-position-vertical' => 'bottom',
				'icon-type' => 'false',
				'disabled' => false,
				'name' => 'data[FilterData][1][EmployeeTest][birthday]',
				'value' => null,
				'id' => 'FilterData1EmployeeTestBirthday',
				'type' => 'dateSelect',
				'beforeInput' => '<div class="input-group-btn"><select name="data[FilterCond][1][EmployeeTest][birthday]" title="' . __d('view_extension', 'Change the logical condition of the field') . '" class="form-control show-tick filter-condition input-xs" autocomplete="off" id="FilterCond1EmployeeTestBirthday" data-toggle="select" data-style="btn-default btn-filter-condition btn-xs" data-width="fit" data-live-search="false">' . "\n" .
					'<option value="" title="=" data-subtext="' . __d('view_extension', 'Equal') . '">=</option>' . "\n" .
					'<option value="gt" title="&amp;gt;" data-subtext="' . __d('view_extension', 'Greater than') . '">&gt;</option>' . "\n" .
					'<option value="ge" title="&amp;ge;" data-subtext="' . __d('view_extension', 'Greater than or equal to') . '">&ge;</option>' . "\n" .
					'<option value="lt" title="&amp;lt;" data-subtext="' . __d('view_extension', 'Less than') . '">&lt;</option>' . "\n" .
					'<option value="le" title="&amp;le;" data-subtext="' . __d('view_extension', 'Less than or equal to') . '">&le;</option>' . "\n" .
					'<option value="ne" title="&amp;ne;" data-subtext="' . __d('view_extension', 'Not equal') . '">&ne;</option>' . "\n" .
					'</select></div>',
			],
			[
				'div' => 'input-group',
				'widget-position-vertical' => 'bottom',
				'icon-type' => 'false',
				'disabled' => true,
				'name' => 'data[FilterData][5][EmployeeTest][birthday]',
				'value' => null,
				'id' => 'FilterData5EmployeeTestBirthday',
				'type' => 'dateSelect',
				'beforeInput' => '<div class="input-group-btn"><select name="data[FilterCond][5][EmployeeTest][birthday]" title="' . __d('view_extension', 'Change the logical condition of the field') . '" class="form-control show-tick filter-condition input-xs" autocomplete="off" id="FilterCond5EmployeeTestBirthday" data-toggle="select" data-style="btn-default btn-filter-condition btn-xs" data-width="fit" data-live-search="false">' . "\n" .
					'<option value="" title="=" data-subtext="' . __d('view_extension', 'Equal') . '">=</option>' . "\n" .
					'<option value="gt" title="&amp;gt;" data-subtext="' . __d('view_extension', 'Greater than') . '">&gt;</option>' . "\n" .
					'<option value="ge" title="&amp;ge;" data-subtext="' . __d('view_extension', 'Greater than or equal to') . '">&ge;</option>' . "\n" .
					'<option value="lt" title="&amp;lt;" data-subtext="' . __d('view_extension', 'Less than') . '">&lt;</option>' . "\n" .
					'<option value="le" title="&amp;le;" data-subtext="' . __d('view_extension', 'Less than or equal to') . '">&le;</option>' . "\n" .
					'<option value="ne" title="&amp;ne;" data-subtext="' . __d('view_extension', 'Not equal') . '">&ne;</option>' . "\n" .
					'</select></div>',
			],
		];
		$this->runClassMethodGroup('prepareOptions', $params, $expected);
	}
}
