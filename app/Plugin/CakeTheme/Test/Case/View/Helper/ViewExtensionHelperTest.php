<?php
App::uses('AppCakeTestCase', 'CakeTheme.Test');
App::uses('View', 'View');
App::uses('Helper', 'View');
App::uses('CakeRequest', 'Network');
App::uses('ViewExtensionHelper', 'CakeTheme.View/Helper');

/**
 * ViewExtensionHelper Test Case
 */
class ViewExtensionHelperTest extends AppCakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = [
		'core.cake_session',
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

		parent::setUp();
		$View = new View();
		$View->request = new CakeRequest(null, false);
		$this->_targetObject = new ViewExtensionHelper($View);
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		parent::tearDown();
	}

/**
 * testGetMenuList method
 *
 * @return void
 */
	public function testGetMenuList() {
		$iconList = [];
		$result = $this->_targetObject->getMenuList($iconList);
		$expected = '';
		$this->assertData($expected, $result);

		$iconList = [
			'<a href="/"><span class="fas fa-home fa-lg"></span><span class="menu-item-label visible-xs-inline">Home</span></a>',
			[
				'<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="fas fa-users fa-lg"></span><span class="menu-item-label visible-xs-inline">Employee</span><span class="caret"></span></a>' => [
					'<a href="/staff" ><span class="fas fa-list fa-lg fa-fw"></span><span class="menu-item-label">Employee</span></a>',
					'divider',
					'<a href="/staff/view" ><span class="fas fa-list fa-lg fa-fw"></span><span class="menu-item-label">Employee - view</span></a>'
				]
			]
		];
		$params = [
			'controller' => 'staff',
			'action' => 'index'
		];
		$this->_targetObject->request->addParams($params);
		$result = $this->_targetObject->getMenuList($iconList);
		$expected = '<ul class="nav navbar-nav navbar-right"><li><a href="/"><span class="fas fa-home fa-lg"></span><span class="menu-item-label visible-xs-inline">Home</span></a></li><li class="active"><a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="fas fa-users fa-lg"></span><span class="menu-item-label visible-xs-inline">Employee</span><span class="caret"></span></a><ul class="dropdown-menu"><li><a href="/staff" ><span class="fas fa-list fa-lg fa-fw"></span><span class="menu-item-label">Employee</span></a></li><li class="divider"></li><li><a href="/staff/view" ><span class="fas fa-list fa-lg fa-fw"></span><span class="menu-item-label">Employee - view</span></a></li></ul></li></ul>';
		$this->assertData($expected, $result);

		$params = [
			'controller' => 'staff',
			'action' => 'view',
			'pass' => [8]
		];
		$this->_targetObject->request->addParams($params);
		$result = $this->_targetObject->getMenuList($iconList);
		$this->assertData($expected, $result);

		$params = [
			'controller' => 'some_controller',
			'action' => 'act'
		];
		$this->_targetObject->request->addParams($params);
		$result = $this->_targetObject->getMenuList($iconList);
		$expected = '<ul class="nav navbar-nav navbar-right"><li><a href="/"><span class="fas fa-home fa-lg"></span><span class="menu-item-label visible-xs-inline">Home</span></a></li><li><a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="fas fa-users fa-lg"></span><span class="menu-item-label visible-xs-inline">Employee</span><span class="caret"></span></a><ul class="dropdown-menu"><li><a href="/staff" ><span class="fas fa-list fa-lg fa-fw"></span><span class="menu-item-label">Employee</span></a></li><li class="divider"></li><li><a href="/staff/view" ><span class="fas fa-list fa-lg fa-fw"></span><span class="menu-item-label">Employee - view</span></a></li></ul></li></ul>';
		$this->assertData($expected, $result);

		$params = [
			'controller' => 'some_controller',
			'action' => 'act'
		];
		$this->_targetObject->request->addParams($params);
		$this->_targetObject->_View->viewVars['activeMenuUrl'] = ['controller' => 'staff', 'action' => 'index'];
		$result = $this->_targetObject->getMenuList($iconList);
		$expected = '<ul class="nav navbar-nav navbar-right"><li><a href="/"><span class="fas fa-home fa-lg"></span><span class="menu-item-label visible-xs-inline">Home</span></a></li><li class="active"><a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="fas fa-users fa-lg"></span><span class="menu-item-label visible-xs-inline">Employee</span><span class="caret"></span></a><ul class="dropdown-menu"><li><a href="/staff" ><span class="fas fa-list fa-lg fa-fw"></span><span class="menu-item-label">Employee</span></a></li><li class="divider"></li><li><a href="/staff/view" ><span class="fas fa-list fa-lg fa-fw"></span><span class="menu-item-label">Employee - view</span></a></li></ul></li></ul>';
		$this->assertData($expected, $result);
	}

/**
 * testYesNo method
 *
 * @return void
 */
	public function testYesNo() {
		$params = [
			[
				'', // $data
			],
			[
				false, // $data
			],
			[
				12, // $data
			],
			[
				true, // $data
			],
		];
		$expected = [
			__d('view_extension', 'No'),
			__d('view_extension', 'No'),
			__d('view_extension', 'Yes'),
			__d('view_extension', 'Yes')
		];
		$this->runClassMethodGroup('yesNo', $params, $expected);
	}

/**
 * testYesNoList method
 *
 * @return void
 */
	public function testYesNoList() {
		$result = $this->_targetObject->yesNoList();
		$expected = [
			0 => __d('view_extension', 'No'),
			1 => __d('view_extension', 'Yes')
		];
		$this->assertData($expected, $result);
	}

/**
 * testShowEmpty method
 *
 * @return void
 */
	public function testShowEmpty() {
		$params = [
			[
				'', // $data
				null, // $dataRet
				null, // $emptyRet
				true, // $isHtml
			],
			[
				[], // $data
				null, // $dataRet
				'<b>Empty</b>', // $emptyRet
				false, // $isHtml
			],
			[
				null, // $data
				null, // $dataRet
				'<b>Empty</b>', // $emptyRet
				true, // $isHtml
			],
			[
				'test', // $data
				null, // $dataRet
				'<b>Empty</b>', // $emptyRet
				true, // $isHtml
			],
			[
				['test'], // $data
				'<i>Ok</i>', // $dataRet
				'<b>Empty</b>', // $emptyRet
				true, // $isHtml
			],
			[
				['test'], // $data
				'<i>Ok</i>', // $dataRet
				'<b>Empty</b>', // $emptyRet
				false, // $isHtml
			],
		];
		$expected = [
			__d('view_extension', '&lt;None&gt;'),
			'&lt;b&gt;Empty&lt;/b&gt;',
			'<b>Empty</b>',
			'test',
			'<i>Ok</i>',
			'&lt;i&gt;Ok&lt;/i&gt;',
		];
		$this->runClassMethodGroup('showEmpty', $params, $expected);
	}

/**
 * testPopupModalLink method
 *
 * @return void
 */
	public function testPopupModalLink() {
		$params = [
			[
				'Test title', // $title
				'', // $url
				[], // $options
			],
			[
				'Test title', // $title
				'/test/act', // $url
				[], // $options
			],
			[
				'Test title', // $title
				'/test/act', // $url
				'', // $options
			],
			[
				'Some title', // $title
				[
					'controller' => 'some_controller',
					'action' => 'some_action'
				], // $url
				['data-modal-title' => 'Modal title'], // $options
			],
		];
		$expected = [
			'<a href="/" data-toggle="modal-popover" class="popup-link text-nowrap" target="_blank" data-popover-placement="auto top" data-modal-title="' . __d('view_extension', 'Detail information') . '">Test title</a>',
			'<a href="/test/act" data-toggle="modal-popover" class="popup-link text-nowrap" target="_blank" data-popover-placement="auto top" data-modal-title="' . __d('view_extension', 'Detail information') . '">Test title</a>',
			'<a href="/test/act" data-toggle="modal-popover" class="popup-link text-nowrap" target="_blank" data-popover-placement="auto top" data-modal-title="' . __d('view_extension', 'Detail information') . '">Test title</a>',
			'<a href="/some_controller/some_action" data-modal-title="Modal title" data-toggle="modal-popover" class="popup-link text-nowrap" target="_blank" data-popover-placement="auto top">Some title</a>'
		];
		$this->runClassMethodGroup('popupModalLink', $params, $expected);
	}

/**
 * testPopupLink method
 *
 * @return void
 */
	public function testPopupLink() {
		$params = [
			[
				'Test title', // $title
				'', // $url
				[], // $options
			],
			[
				'Test title', // $title
				'/test/act', // $url
				[], // $options
			],
			[
				'Test title', // $title
				'/test/act', // $url
				'', // $options
			],
			[
				'Some title', // $title
				[
					'controller' => 'some_controller',
					'action' => 'some_action'
				], // $url
				['data-popover-placement' => 'auto bottom'], // $options
			],
		];
		$expected = [
			'<a href="/" data-toggle="popover" class="popup-link text-nowrap" target="_blank" data-popover-placement="auto top">Test title</a>',
			'<a href="/test/act" data-toggle="popover" class="popup-link text-nowrap" target="_blank" data-popover-placement="auto top">Test title</a>',
			'<a href="/test/act" data-toggle="popover" class="popup-link text-nowrap" target="_blank" data-popover-placement="auto top">Test title</a>',
			'<a href="/some_controller/some_action" data-popover-placement="auto bottom" data-toggle="popover" class="popup-link text-nowrap" target="_blank">Some title</a>'
		];
		$this->runClassMethodGroup('popupLink', $params, $expected);
	}

/**
 * testModalLink method
 *
 * @return void
 */
	public function testModalLink() {
		$params = [
			[
				'Test title', // $title
				'', // $url
				[], // $options
			],
			[
				'Test title', // $title
				'/test/act', // $url
				[], // $options
			],
			[
				'Test title', // $title
				'/test/act', // $url
				'', // $options
			],
			[
				'Some title', // $title
				[
					'controller' => 'some_controller',
					'action' => 'some_action'
				], // $url
				['data-modal-title' => 'Modal Title'], // $options
			],
		];
		$expected = [
			'<a href="/" data-toggle="modal" data-modal-title="' . __d('view_extension', 'Detail information') . '">Test title</a>',
			'<a href="/test/act" data-toggle="modal" data-modal-title="' . __d('view_extension', 'Detail information') . '">Test title</a>',
			'<a href="/test/act" data-toggle="modal" data-modal-title="' . __d('view_extension', 'Detail information') . '">Test title</a>',
			'<a href="/some_controller/some_action" data-modal-title="Modal Title" data-toggle="modal">Some title</a>',
		];
		$this->runClassMethodGroup('modalLink', $params, $expected);
	}

/**
 * testConfirmLink method
 *
 * @return void
 */
	public function testConfirmLink() {
		$params = [
			[
				'Test title', // $title
				'', // $url
				[], // $options
			],
			[
				'Test title', // $title
				'/test/act', // $url
				[], // $options
			],
			[
				'Test title', // $title
				'/test/act', // $url
				'', // $options
			],
			[
				'Some title', // $title
				[
					'controller' => 'some_controller',
					'action' => 'some_action'
				], // $url
				['data-confirm-msg' => 'Are you sure?'], // $options
			],
		];
		$expected = [
			'<a href="/" data-confirm-msg="' . __d('view_extension', 'Are you sure you wish to delete this data?') . '" data-confirm-btn-ok="' . __d('view_extension', 'Yes') . '" data-confirm-btn-cancel="' . __d('view_extension', 'No') . '">Test title</a>',
			'<a href="/test/act" data-confirm-msg="' . __d('view_extension', 'Are you sure you wish to delete this data?') . '" data-confirm-btn-ok="' . __d('view_extension', 'Yes') . '" data-confirm-btn-cancel="' . __d('view_extension', 'No') . '">Test title</a>',
			'<a href="/test/act" data-confirm-msg="' . __d('view_extension', 'Are you sure you wish to delete this data?') . '" data-confirm-btn-ok="' . __d('view_extension', 'Yes') . '" data-confirm-btn-cancel="' . __d('view_extension', 'No') . '">Test title</a>',
			'<a href="/some_controller/some_action" data-confirm-msg="Are you sure?" data-confirm-btn-ok="' . __d('view_extension', 'Yes') . '" data-confirm-btn-cancel="' . __d('view_extension', 'No') . '">Some title</a>',
		];
		$this->runClassMethodGroup('confirmLink', $params, $expected);
	}

/**
 * testConfirmPostLink method
 *
 * @return void
 */
	public function testConfirmPostLink() {
		$params = [
			[
				'Test title', // $title
				'', // $url
				[], // $options
			],
			[
				'Test title', // $title
				'/test/act', // $url
				[], // $options
			],
			[
				'Test title', // $title
				'/test/act', // $url
				'', // $options
			],
			[
				'Some title', // $title
				[
					'controller' => 'some_controller',
					'action' => 'some_action'
				], // $url
				['title' => 'Confirm title'], // $options
			],
		];
		$expected = [
			[
				'assertRegExp' => '/\<form action\="\/" name\="post_[0-9a-f]+" id\="post_[0-9a-f]+" style\="display\:none;" method\="post"\>\<input type\="hidden" name\="_method" value\="POST"\/\>\<\/form\>\<a href\="#" data\-confirm\-msg\=".+" data\-confirm\-btn\-ok\=".+" data\-confirm\-btn\-cancel\=".+" role\="post\-link" onclick\="document\.post_[0-9a-f]+\.submit\(\); event\.returnValue \= false; return false;"\>Test title\<\/a\>/',
			],
			[
				'assertRegExp' => '/\<form action\="\/test\/act" name\="post_[0-9a-f]+" id\="post_[0-9a-f]+" style\="display\:none;" method\="post"\>\<input type\="hidden" name\="_method" value\="POST"\/\>\<\/form\>\<a href\="#" data\-confirm\-msg\=".+" data\-confirm\-btn\-ok\=".+" data\-confirm\-btn\-cancel\=".+" role\="post\-link" onclick\="document\.post_[0-9a-f]+\.submit\(\); event\.returnValue \= false; return false;"\>Test title\<\/a\>/',
			],
			[
				'assertRegExp' => '/\<form action\="\/test\/act" name\="post_[0-9a-f]+" id\="post_[0-9a-f]+" style\="display\:none;" method\="post"\>\<input type\="hidden" name\="_method" value\="POST"\/\>\<\/form\>\<a href\="#" data\-confirm\-msg\=".+" data\-confirm\-btn\-ok\=".+" data\-confirm\-btn\-cancel\=".+" role\="post\-link" onclick\="document\.post_[0-9a-f]+\.submit\(\); event\.returnValue \= false; return false;"\>Test title\<\/a\>/',
			],
			[
				'assertRegExp' => '/\<form action\="\/some_controller\/some_action" name\="post_[0-9a-f]+" id\="post_[0-9a-f]+" style\="display\:none;" method\="post"\>\<input type\="hidden" name\="_method" value\="POST"\/\>\<\/form\>\<a href\="#" title\=\"Confirm title\" data\-confirm\-msg\=".+" data\-confirm\-btn\-ok\=".+" data\-confirm\-btn\-cancel\=".+" role\="post\-link" onclick\="document\.post_[0-9a-f]+\.submit\(\); event\.returnValue \= false; return false;"\>Some title\<\/a\>/',
			]
		];
		$this->runClassMethodGroup('confirmPostLink', $params, $expected);
	}

/**
 * testAjaxLink method
 *
 * @return void
 */
	public function testAjaxLink() {
		$params = [
			[
				'Test title', // $title
				'', // $url
				[], // $options
			],
			[
				'Test title', // $title
				'/test/act', // $url
				[], // $options
			],
			[
				'Test title', // $title
				'/test/act', // $url
				'', // $options
			],
			[
				'Some title', // $title
				[
					'controller' => 'some_controller',
					'action' => 'some_action'
				], // $url
				['title' => 'Some title'], // $options
			],
		];
		$expected = [
			'<a href="/" data-toggle="ajax">Test title</a>',
			'<a href="/test/act" data-toggle="ajax">Test title</a>',
			'<a href="/test/act" data-toggle="ajax">Test title</a>',
			'<a href="/some_controller/some_action" title="Some title" data-toggle="ajax">Some title</a>'
		];
		$this->runClassMethodGroup('ajaxLink', $params, $expected);
	}

/**
 * testRequestOnlyLinkLink method
 *
 * @return void
 */
	public function testRequestOnlyLinkLink() {
		$params = [
			[
				'Test title', // $title
				'', // $url
				[], // $options
			],
			[
				'Test title', // $title
				'/test/act', // $url
				[], // $options
			],
			[
				'Test title', // $title
				'/test/act', // $url
				'', // $options
			],
			[
				'Some title', // $title
				[
					'controller' => 'some_controller',
					'action' => 'some_action'
				], // $url
				['title' => 'Some title'], // $options
			],
		];
		$expected = [
			'<a href="/" data-toggle="request-only">Test title</a>',
			'<a href="/test/act" data-toggle="request-only">Test title</a>',
			'<a href="/test/act" data-toggle="request-only">Test title</a>',
			'<a href="/some_controller/some_action" title="Some title" data-toggle="request-only">Some title</a>'
		];
		$this->runClassMethodGroup('requestOnlyLink', $params, $expected);
	}

/**
 * testPjaxLink method
 *
 * @return void
 */
	public function testPjaxLink() {
		$params = [
			[
				'Test title', // $title
				'', // $url
				[], // $options
			],
			[
				'Test title', // $title
				'/test/act', // $url
				[], // $options
			],
			[
				'Test title', // $title
				'/test/act', // $url
				'', // $options
			],
			[
				'Some title', // $title
				[
					'controller' => 'some_controller',
					'action' => 'some_action'
				], // $url
				['title' => 'Some title'], // $options
			],
		];
		$expected = [
			'<a href="/" data-toggle="pjax">Test title</a>',
			'<a href="/test/act" data-toggle="pjax">Test title</a>',
			'<a href="/test/act" data-toggle="pjax">Test title</a>',
			'<a href="/some_controller/some_action" title="Some title" data-toggle="pjax">Some title</a>'
		];
		$this->runClassMethodGroup('pjaxLink', $params, $expected);
	}

/**
 * testLightboxLink method
 *
 * @return void
 */
	public function testLightboxLink() {
		$params = [
			[
				'Test title', // $title
				'', // $url
				[], // $options
			],
			[
				'Test title', // $title
				'/test/act', // $url
				[], // $options
			],
			[
				'Test title', // $title
				'/test/act', // $url
				'', // $options
			],
			[
				'Some title', // $title
				[
					'controller' => 'some_controller',
					'action' => 'some_action'
				], // $url
				['title' => 'Some title'], // $options
			],
		];
		$expected = [
			'<a href="/" data-toggle="lightbox">Test title</a>',
			'<a href="/test/act" data-toggle="lightbox">Test title</a>',
			'<a href="/test/act" data-toggle="lightbox">Test title</a>',
			'<a href="/some_controller/some_action" title="Some title" data-toggle="lightbox">Some title</a>'
		];
		$this->runClassMethodGroup('lightboxLink', $params, $expected);
	}

/**
 * testPaginationSortPjax method
 *
 * @return void
 */
	public function testPaginationSortPjax() {
		$this->_targetObject->Paginator->request->addParams([
			'paging' => [
				'OrderModel' => [
					'options' => [
						'order' => [
							'OrderModel.test_field' => 'desc'
						],
					],
					'paramType' => 'named',
				]
			]
		]);

		$params = [
			[
				'SomeModel.test_field', // $key
				'Test field', // $title
				[], // $options
			],
			[
				'SomeModel.test_field', // $key
				'Test field', // $title
				'', // $options
			],
			[
				'TestModel.field', // $key
				'Some field', // $title
				['title' => 'Some title'], // $options
			],
			[
				'OrderModel.test_field', // $key
				'Order field', // $title
				[], // $options
			],
		];
		$expected = [
			'<a href="/index/sort:SomeModel.test_field/direction:asc" title="' . __d('view_extension', 'Click to sort by this fiels') . '" data-toggle="pjax">Test field</a>',
			'<a href="/index/sort:SomeModel.test_field/direction:asc" title="' . __d('view_extension', 'Click to sort by this fiels') . '" data-toggle="pjax">Test field</a>',
			'<a href="/index/sort:TestModel.field/direction:asc" title="Some title" data-toggle="pjax">Some field</a>',
			'<a href="/index/sort:OrderModel.test_field/direction:asc" title="' . __d('view_extension', 'Click to sort by this fiels') . '" data-toggle="pjax" class="desc">Order field<span class="fas fa-long-arrow-alt-down fa-lg"></span></a>'
		];
		$this->runClassMethodGroup('paginationSortPjax', $params, $expected);
	}

/**
 * testPaginationSortModal method
 *
 * @return void
 */
	public function testPaginationSortModal() {
		$this->_targetObject->Paginator->request->addParams([
			'paging' => [
				'OrderModel' => [
					'options' => [
						'order' => [
							'OrderModel.test_field' => 'desc'
						],
					],
					'paramType' => 'named',
				]
			],
		]);
		$this->_targetObject->request->addParams([
			'ext' => 'mod'
		]);
		$params = [
			[
				'SomeModel.test_field', // $key
				'Test field', // $title
				[], // $options
			],
			[
				'SomeModel.test_field', // $key
				'Test field', // $title
				'', // $options
			],
			[
				'TestModel.field', // $key
				'Some field', // $title
				['title' => 'Some title'], // $options
			],
			[
				'OrderModel.test_field', // $key
				'Order field', // $title
				[], // $options
			],
		];
		$expected = [
			'<a href="/index/sort:SomeModel.test_field/direction:asc" title="' . __d('view_extension', 'Click to sort by this fiels') . '" data-toggle="modal">Test field</a>',
			'<a href="/index/sort:SomeModel.test_field/direction:asc" title="' . __d('view_extension', 'Click to sort by this fiels') . '" data-toggle="modal">Test field</a>',
			'<a href="/index/sort:TestModel.field/direction:asc" title="Some title" data-toggle="modal">Some field</a>',
			'<a href="/index/sort:OrderModel.test_field/direction:asc" title="' . __d('view_extension', 'Click to sort by this fiels') . '" data-toggle="modal" class="desc">Order field<span class="fas fa-long-arrow-alt-down fa-lg"></span></a>'
		];
		$this->runClassMethodGroup('paginationSortPjax', $params, $expected);
	}

/**
 * testProgressSseLink method
 *
 * @return void
 */
	public function testProgressSseLink() {
		$params = [
			[
				'Test title', // $title
				'', // $url
				[], // $options
			],
			[
				'Test title', // $title
				'/test/act', // $url
				[], // $options
			],
			[
				'Test title', // $title
				'/test/act', // $url
				'', // $options
			],
			[
				'Some title', // $title
				[
					'controller' => 'some_controller',
					'action' => 'some_action'
				], // $url
				['title' => 'Some title'], // $options
			],
		];
		$expected = [
			'<a href="/" data-toggle="progress-sse">Test title</a>',
			'<a href="/test/act" data-toggle="progress-sse">Test title</a>',
			'<a href="/test/act" data-toggle="progress-sse">Test title</a>',
			'<a href="/some_controller/some_action" title="Some title" data-toggle="progress-sse">Some title</a>'
		];
		$this->runClassMethodGroup('progressSseLink', $params, $expected);
	}

/**
 * testGetClassForElementDefault method
 *
 * @return void
 */
	public function testGetClassForElementDefault() {
		$targetObject = $this->createProxyObject($this->_targetObject);
		$result = $targetObject->_getClassForElement('fa-camera-retro');
		$expected = 'fas fa-camera-retro fa-lg';
		$this->assertData($expected, $result);

		$result = $targetObject->_getClassForElement('btn-default');
		$expected = 'btn btn-default btn-xs';
		$this->assertData($expected, $result);
	}

/**
 * testGetClassForElementWithSize method
 *
 * @return void
 */
	public function testGetClassForElementWithSize() {
		$targetObject = $this->createProxyObject($this->_targetObject);
		$result = $targetObject->_getClassForElement('fa-pencil-alt fa-xs');
		$expected = 'fas fa-pencil-alt fa-xs';
		$this->assertData($expected, $result);

		$result = $targetObject->_getClassForElement('btn-primary btn-sm');
		$expected = 'btn btn-primary btn-sm';
		$this->assertData($expected, $result);
	}

/**
 * testGetClassForElementWithConfig method
 *
 * @return void
 */
	public function testGetClassForElementWithConfig() {
		Configure::write('CakeTheme.ViewExtension.Helper.defaultIconPrefix', 'far');
		Configure::write('CakeTheme.ViewExtension.Helper.defaultIconSize', '');
		Configure::write('CakeTheme.ViewExtension.Helper.defaultBtnPrefix', '');
		Configure::write('CakeTheme.ViewExtension.Helper.defaultBtnSize', 'btn-lg');
		$View = new View();
		$ViewExtensionHelper = new ViewExtensionHelper($View);
		$targetObject = $this->createProxyObject($ViewExtensionHelper);
		$result = $targetObject->_getClassForElement('fa-square');
		$expected = 'far fa-square';
		$this->assertData($expected, $result);

		$result = $targetObject->_getClassForElement('btn-warning');
		$expected = 'btn-warning btn-lg';
		$this->assertData($expected, $result);
	}

/**
 * testGetClassForElementBadSize method
 *
 * @return void
 */
	public function testGetClassForElementBadSize() {
		$targetObject = $this->createProxyObject($this->_targetObject);
		$result = $targetObject->_getClassForElement('fa-pencil-alt fa-bad');
		$expected = 'fas fa-pencil-alt fa-bad fa-lg';
		$this->assertData($expected, $result);

		$result = $targetObject->_getClassForElement('btn-primary btn-bad');
		$expected = 'btn btn-primary btn-xs';
		$this->assertData($expected, $result);
	}

/**
 * testGetClassForElementBadParam method
 *
 * @return void
 */
	public function testGetClassForElementBadParam() {
		$targetObject = $this->createProxyObject($this->_targetObject);
		$result = $targetObject->_getClassForElement('bad fa-pencil-alt');
		$expected = 'fas bad fa-pencil-alt fa-lg';
		$this->assertData($expected, $result);

		$result = $targetObject->_getClassForElement('btn-bad');
		$expected = '';
		$this->assertData($expected, $result);
	}

/**
 * testGetBtnClass method
 *
 * @return void
 */
	public function testGetBtnClass() {
		$params = [
			[
				'', // $btn
			],
			[
				'btn-bad', // $btn
			],
			[
				'btn-warning', // $btn
			],
			[
				'btn-primary btn-sm', // $btn
			],
		];
		$expected = [
			'btn btn-default btn-xs',
			'btn btn-default btn-xs',
			'btn btn-warning btn-xs',
			'btn btn-primary btn-sm'
		];
		$this->runClassMethodGroup('getBtnClass', $params, $expected);
	}

/**
 * testIconTag method
 *
 * @return void
 */
	public function testIconTag() {
		$params = [
			[
				'', // $icon
				[], // $options
			],
			[
				'bad-icon', // $icon
				[], // $options
			],
			[
				'fa-eraser fa-5x', // $icon
				[], // $options
			],
			[
				'fa-eraser fa-10x', // $icon
				'', // $options
			],
			[
				'fas fa-pencil', // $icon
				'testOpt', // $options
			],
			[
				'fa-pencil', // $icon
				['title' => 'Icon title'], // $options
			],
		];
		$expected = [
			'',
			'',
			'<span class="fas fa-eraser fa-5x"></span>',
			'<span class="fas fa-eraser fa-10x"></span>',
			'<span testOpt="testOpt" class="fas fa-pencil fa-lg"></span>',
			'<span title="Icon title" class="fas fa-pencil fa-lg"></span>',
		];
		$this->runClassMethodGroup('iconTag', $params, $expected);
	}

/**
 * testButtonLink method
 *
 * @return void
 */
	public function testButtonLink() {
		$params = [
			[
				'', // $icon
				'', // $btn
				'/test/act', // $url
				[], // $options
			],
			[
				'bad-icon', // $icon
				'', // $btn
				[
					'controller' => 'some_controller',
					'action' => 'some_action'
				], // $url
				['title' => 'Some title'], // $options
			],
			[
				'<i>fa-pencil-alt</i>', // $icon
				'', // $btn
				[
					'controller' => 'some_controller',
					'action' => 'some_action'
				], // $url
				['title' => 'Some title'], // $options
			],
			[
				'fa-pencil-alt', // $icon
				'btn-bad', // $btn
				[
					'controller' => 'some_controller',
					'action' => 'some_action'
				], // $url
				'', // $options
			],
			[
				'fas fa-pencil-alt', // $icon
				'btn-bad', // $btn
				[
					'controller' => 'some_controller',
					'action' => 'some_action'
				], // $url
				['title' => 'Some title'], // $options
			],
			[
				'fas fa-pencil-alt fa-xs', // $icon
				'', // $btn
				'', // $url
				['title' => 'Some title'], // $options
			],
			[
				'fas fa-pencil-alt', // $icon
				'', // $btn
				[
					'controller' => 'some_controller',
					'action' => 'some_action'
				], // $url
				'testOpt', // $options
			],
			[
				'fa-pencil-alt', // $icon
				'', // $btn
				[
					'controller' => 'some_controller',
					'action' => 'some_action'
				], // $url
				['title' => 'Some title'], // $options
			],
			[
				'fa-eraser', // $icon
				'btn-success', // $btn
				[
					'controller' => 'some_controller',
					'action' => 'test'
				], // $url
				['action-type' => 'post'], // $options
			],
			[
				'fa-eraser', // $icon
				'btn-success', // $btn
				[
					'controller' => 'some_controller',
					'action' => 'test'
				], // $url
				['action-type' => 'confirm'], // $options
			],
			[
				'fa-eraser', // $icon
				'btn-success', // $btn
				[
					'controller' => 'some_controller',
					'action' => 'test'
				], // $url
				['action-type' => 'confirm-post'], // $options
			],
			[
				'fa-eraser', // $icon
				'btn-success', // $btn
				[
					'controller' => 'some_controller',
					'action' => 'test'
				], // $url
				['action-type' => 'modal'], // $options
			],
		];
		$expected = [
			'',
			'<a href="/some_controller/some_action" title="Some title" class="btn btn-default btn-xs" data-toggle="title">bad-icon</a>',
			'<a href="/some_controller/some_action" title="Some title" class="btn btn-default btn-xs" data-toggle="title"><i>fa-pencil-alt</i></a>',
			'<a href="/some_controller/some_action" class="btn btn-default btn-xs"><span class="fas fa-pencil-alt fa-fw fa-lg"></span></a>',
			'<a href="/some_controller/some_action" title="Some title" class="btn btn-default btn-xs" data-toggle="title"><span class="fas fa-pencil-alt fa-fw fa-lg"></span></a>',
			'<a href="/" title="Some title" class="btn btn-default btn-xs" data-toggle="title"><span class="fas fa-pencil-alt fa-fw fa-xs"></span></a>',
			'<a href="/some_controller/some_action" testOpt="testOpt" class="btn btn-default btn-xs"><span class="fas fa-pencil-alt fa-fw fa-lg"></span></a>',
			'<a href="/some_controller/some_action" title="Some title" class="btn btn-default btn-xs" data-toggle="title"><span class="fas fa-pencil-alt fa-fw fa-lg"></span></a>',
			['assertRegExp' => '/\<a href\="#" class\="btn btn\-success btn\-xs" role\="post\-link" onclick\="document\.post_[0-9a-f]+\.submit\(\); event\.returnValue \= false; return false;"\>\<span class\="fas fa\-eraser fa\-fw fa\-lg">\<\/span\>\<\/a\>/'],
			'<a href="/some_controller/test" class="btn btn-success btn-xs" data-confirm-msg="' . __d('view_extension', 'Are you sure you wish to delete this data?') . '" data-confirm-btn-ok="' . __d('view_extension', 'Yes') . '" data-confirm-btn-cancel="' . __d('view_extension', 'No') . '"><span class="fas fa-eraser fa-fw fa-lg"></span></a>',
			['assertRegExp' => '/\<a href\="#" class\="btn btn\-success btn\-xs" data\-confirm\-msg\="' . preg_quote(__d('view_extension', 'Are you sure you wish to delete this data?'), '/') . '" data\-confirm\-btn\-ok\="' . __d('view_extension', 'Yes') . '" data\-confirm\-btn\-cancel\="' . __d('view_extension', 'No') . '" role\="post\-link" onclick\="document\.post_[0-9a-f]+\.submit\(\); event\.returnValue \= false; return false;"\>\<span class\="fas fa\-eraser fa\-fw fa\-lg">\<\/span\>\<\/a\>/'],
			'<a href="/some_controller/test" class="btn btn-success btn-xs" data-toggle="modal"><span class="fas fa-eraser fa-fw fa-lg"></span></a>'
		];
		$this->runClassMethodGroup('buttonLink', $params, $expected);
	}

/**
 * testButton method
 *
 * @return void
 */
	public function testButton() {
		$params = [
			[
				'', // $icon
				'', // $btn
				[], // $options
			],
			[
				'bad-icon', // $icon
				'btn-success', // $btn
				[], // $options
			],
			[
				'fa-eraser', // $icon
				'btn-warning', // $btn
				[], // $options
			],
			[
				'fa-eraser', // $icon
				'btn-warning', // $btn
				'', // $options
			],
			[
				'fas fa-pencil-alt', // $icon
				'btn-danger', // $btn
				'testOpt', // $options
			],
			[
				'fas fa-pencil-alt fa-sm', // $icon
				'btn-primary', // $btn
				['title' => 'Button title'], // $options
			],
		];
		$expected = [
			'',
			'<button class="btn btn-success btn-xs" type="button">bad-icon</button>',
			'<button class="btn btn-warning btn-xs" type="button"><span class="fas fa-eraser fa-fw fa-lg"></span></button>',
			'<button class="btn btn-warning btn-xs" type="button"><span class="fas fa-eraser fa-fw fa-lg"></span></button>',
			'<button testOpt="testOpt" class="btn btn-danger btn-xs" type="button"><span class="fas fa-pencil-alt fa-fw fa-lg"></span></button>',
			'<button title="Button title" class="btn btn-primary btn-xs" type="button" data-toggle="title"><span class="fas fa-pencil-alt fa-fw fa-sm"></span></button>',
		];
		$this->runClassMethodGroup('button', $params, $expected);
	}

/**
 * testAddUserPrefixUrl method
 *
 * @return void
 */
	public function testAddUserPrefixUrl() {
		$params = [
			[
				'', // $url
			],
			[
				'/', // $url
			],
			[
				'mailto:some_mail@fabrikam.com?subject=test', // $url
			],
			[
				'/test/act', // $url
			],
			[
				[
					'controller' => 'some_controller',
					'action' => 'some_action'
				], // $url
			],
			[
				[
					'controller' => 'some_controller',
					'action' => 'some_action',
					'prefix' => false,
				], // $url
			],
		];
		$expected = [
			'',
			'/',
			'mailto:some_mail@fabrikam.com?subject=test',
			[
				'controller' => 'test',
				'action' => 'act',
				'named' => [],
				'pass' => [],
				'plugin' => null,
				'admin' => true,
			],
			[
				'controller' => 'some_controller',
				'action' => 'some_action',
				'admin' => true,
			],
			[
				'controller' => 'some_controller',
				'action' => 'some_action',
				'admin' => false,
			],
		];
		$this->runClassMethodGroup('addUserPrefixUrl', $params, $expected);
	}

/**
 * testMenuItemLabel method
 *
 * @return void
 */
	public function testMenuItemLabel() {
		$params = [
			[
				'', // $title
			],
			[
				'Some title', // $title
			],
		];
		$expected = [
			'',
			'<span class="menu-item-label visible-xs-inline">Some title</span>'
		];
		$this->runClassMethodGroup('menuItemLabel', $params, $expected);
	}

/**
 * testMenuItemLink method
 *
 * @return void
 */
	public function testMenuItemLink() {
		$params = [
			[
				'', // $icon
				'Some title', // $title
				'/test/act', // $url
				'', // $options
				0, // $badgeNumber
			],
			[
				'bad-icon', // $icon
				'Some title', // $title
				'', // $url
				'', // $options
				0, // $badgeNumber
			],
			[
				'bad-icon', // $icon
				'Some title', // $title
				'/test/act', // $url
				'', // $options
				0, // $badgeNumber
			],
			[
				'fas fa-pencil-alt', // $icon
				'Test title', // $title
				[
					'controller' => 'some_controller',
					'action' => 'some_action'
				], // $url
				'', // $options
				5, // $badgeNumber
			],
			[
				'fa-pencil-alt', // $icon
				'Test title', // $title
				[
					'controller' => 'some_controller',
					'action' => 'some_action'
				], // $url
				['action-type' => 'confirm'], // $options
				8, // $badgeNumber
			],
		];
		$expected = [
			'',
			'<a href="#" title="Some title" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="menu-item-label visible-xs-inline">Some title</span><span class="caret"></span></a>',
			'<a href="/admin/test/act" title="Some title" data-toggle="tooltip"><span class="menu-item-label visible-xs-inline">Some title</span></a>',
			'<a href="/admin/some_controller/some_action" title="Test title" data-toggle="tooltip"><span class="fas fa-pencil-alt fa-fw fa-lg"></span><span class="menu-item-label visible-xs-inline">Test title</span>&nbsp;<span class="badge">5</span></a>',
			'<a href="/admin/some_controller/some_action" action-type="confirm" title="Test title" data-toggle="tooltip"><span class="fas fa-pencil-alt fa-fw fa-lg"></span><span class="menu-item-label visible-xs-inline">Test title</span>&nbsp;<span class="badge">8</span></a>'
		];
		$this->runClassMethodGroup('menuItemLink', $params, $expected);
	}

/**
 * testMenuActionLink method
 *
 * @return void
 */
	public function testMenuActionLink() {
		$params = [
			[
				'', // $icon
				'Test title', // $titleText
				'/test/act', // $url
				'', // $options
			],
			[
				'bad-icon', // $icon
				'Test title', // $titleText
				'/test/act', // $url
				'', // $options
			],
			[
				'fa-eraser', // $icon
				'Title text', // $titleText
				'/test/act', // $url
				'testOpt', // $options
			],
			[
				'fa-eraser', // $icon
				'Test title', // $titleText
				[
					'controller' => 'some_controller',
					'action' => 'some_action'
				], // $url
				['title' => 'Some title'], // $options
			],
			[
				'fa-eraser', // $icon
				'Test title', // $titleText
				[
					'controller' => 'some_controller',
					'action' => 'some_action'
				], // $url
				['action-type' => 'post'], // $options
			],
			[
				'fa-eraser', // $icon
				'Test title', // $titleText
				[
					'controller' => 'some_controller',
					'action' => 'some_action'
				], // $url
				['action-type' => 'confirm'], // $options
			],
			[
				'fa-eraser', // $icon
				'Test title', // $titleText
				[
					'controller' => 'some_controller',
					'action' => 'some_action'
				], // $url
				['action-type' => 'confirm-post'], // $options
			],
		];
		$expected = [
			'',
			'',
			'<a href="/admin/test/act" testOpt="testOpt"><span class="fas fa-eraser fa-fw fa-lg"></span><span class="menu-item-label">Title text</span></a>',
			'<a href="/admin/some_controller/some_action" title="Some title" data-toggle="title"><span class="fas fa-eraser fa-fw fa-lg"></span><span class="menu-item-label">Test title</span></a>',
			[
				'assertRegExp' => '/\<form action\="\/admin\/some_controller\/some_action" name\="post_[0-9a-f]+" id\="post_[0-9a-f]+" style\="display\:none;" method\="post"\>\<input type\="hidden" name\="_method" value\="POST"\/\>\<\/form\>\<a href\="#" role\="post\-link" onclick\="document\.post_[0-9a-f]+\.submit\(\); event\.returnValue \= false; return false;"\>\<span class\="fas fa-eraser fa\-fw fa\-lg"\>\<\/span\>\<span class\="menu\-item\-label"\>Test title\<\/span\>\<\/a\>/'
			],
			'<a href="/admin/some_controller/some_action" data-confirm-msg="' . __d('view_extension', 'Are you sure you wish to delete this data?') . '" data-confirm-btn-ok="' . __d('view_extension', 'Yes') . '" data-confirm-btn-cancel="' . __d('view_extension', 'No') . '"><span class="fas fa-eraser fa-fw fa-lg"></span><span class="menu-item-label">Test title</span></a>',
			[
				'assertRegExp' => '/\<form action\="\/admin\/some_controller\/some_action" name\="post_[0-9a-f]+" id\="post_[0-9a-f]+" style\="display\:none;" method\="post"\>\<input type\="hidden" name\="_method" value\="POST"\/\>\<\/form\>\<a href\="#" data\-confirm\-msg\=".+" data\-confirm\-btn\-ok\=".+" data\-confirm\-btn\-cancel\=".+" role\="post\-link" onclick\="document\.post_[0-9a-f]+\.submit\(\); event\.returnValue \= false; return false;"\>\<span class\="fas fa-eraser fa\-fw fa\-lg"\>\<\/span\>\<span class\="menu\-item\-label"\>Test title\<\/span\>\<\/a\>/'
			],
		];
		$this->runClassMethodGroup('menuActionLink', $params, $expected);
	}

/**
 * testTimeAgo method
 *
 * @return void
 */
	public function testTimeAgo() {
		$result = $this->_targetObject->timeAgo('', '');
		$expected = '/\<time data\-toggle\="timeago" datetime\="\d{4}\-\d{2}\-\d{2}T\d{2}\:\d{2}\:\d{2}[+-]{1}\d{2}\:\d{2}" class\="help"\>.+\<\/time\>/';
		$this->assertRegExp($expected, $result);

		$time = time();
		$result = $this->_targetObject->timeAgo($time, '%x');
		$this->assertRegExp($expected, $result);

		$time = new DateTime();
		$result = $this->_targetObject->timeAgo($time, '%X');
		$this->assertRegExp($expected, $result);
	}

/**
 * testGetIconForExtension method
 *
 * @return void
 */
	public function testGetIconForExtension() {
		$params = [
			[
				'', // $extension
			],
			[
				23, // $extension
			],
			[
				'bad', // $extension
			],
			[
				'rar', // $extension
			],
			[
				'docx', // $extension
			],
			[
				'xml', // $extension
			],
			[
				'jpg', // $extension
			],
			[
				'pdf', // $extension
			],
			[
				'avi', // $extension
			],
			[
				'ppt', // $extension
			],
			[
				'mp3', // $extension
			],
			[
				'xls', // $extension
			],
			[
				'txt', // $extension
			],
		];
		$expected = [
			'far fa-file',
			'far fa-file',
			'far fa-file',
			'far fa-file-archive',
			'far fa-file-word',
			'far fa-file-code',
			'far fa-file-image',
			'far fa-file-pdf',
			'far fa-file-video',
			'far fa-file-powerpoint',
			'far fa-file-audio',
			'far fa-file-excel',
			'far fa-file-alt',
		];
		$this->runClassMethodGroup('getIconForExtension', $params, $expected);
	}

/**
 * testTruncateText method
 *
 * @return void
 */
	public function testTruncateText() {
		$params = [
			[
				'', // $text
				0, // $length
			],
			[
				'Some text', // $text
				0, // $length
			],
			[
				'Some text', // $text
				5, // $length
			],
		];
		$expected = [
			'',
			'Some text',
			'<div class="collapse-text-expanded"><div class="collapse-text-truncated">Some<a href="#" role="button" data-toggle="collapse-text-expand" class="collapse-text-action-btn" title="' .
				__d('view_extension', 'Expand text') . '"><span class="fas fa-angle-double-right fa-lg"></span></a></div><div class="collapse-text-original">Some text<a href="#" role="button" data-toggle="collapse-text-roll-up" class="collapse-text-action-btn" title="' .
				__d('view_extension', 'Roll up text') . '"><span class="fas fa-angle-double-left fa-lg"></span></a></div></div>'
		];
		$this->runClassMethodGroup('truncateText', $params, $expected);
	}

/**
 * testGetFormOptions method
 *
 * @return void
 */
	public function testGetFormOptions() {
		$params = [
			[
				[], // $options
			],
			[
				'bad', // $options
			],
			[
				[
					'class' => 'form-tabs'
				], // $options
			],
		];
		$expected = [
			[
				'role' => 'form',
				'requiredcheck' => true,
				'data-required-msg' => __d('view_extension', 'Please fill in this field')
			],
			[
				'role' => 'form',
				'requiredcheck' => true,
				'data-required-msg' => __d('view_extension', 'Please fill in this field')
			],
			[
				'role' => 'form',
				'requiredcheck' => true,
				'data-required-msg' => __d('view_extension', 'Please fill in this field'),
				'class' => 'form-tabs'
			],
		];
		$this->runClassMethodGroup('getFormOptions', $params, $expected);
	}

/**
 * testGetFormOptions method
 *
 * @return void
 */
	public function testNumberText() {
		$this->skipIf(!CakePlugin::loaded('Tools'), "Plugin 'Tools' is not loaded");

		Configure::write('Config.language', 'rus');
		$params = [
			[
				'10', // $number
				'', // $langCode
			],
			[
				'-5', // $number
				'eng', // $langCode
			],
			[
				'7.5', // $number
				'de', // $langCode
			],
			[
				'134', // $number
				'bad', // $langCode
			],
		];
		$expected = [
			'десять',
			'negative five',
			'sieben Komma fünf',
			'one hundred and thirty-four',
		];
		$this->runClassMethodGroup('numberText', $params, $expected);
	}

/**
 * testBarState method
 *
 * @return void
 */
	public function testBarState() {
		$params = [
			[
				[], // $stateData
			],
			[
				[
					[
						'stateName' => 'Ok',
						'stateId' => 1,
						'amount' => 3,
						'stateUrl' => '',
					],
					[
						'stateName' => 'Bad',
						'stateId' => 2,
						'amount' => 1,
						'class',
					],
				], // $stateData
			],
			[
				[
					[
						'stateName' => 'Ok',
						'stateId' => 1,
						'amount' => 3,
						'stateUrl' => [
							'controller' => 'some_controller',
							'action' => 'some_action',
							1
						],
						'class' => 'progress-bar-success'
					],
					[
						'stateName' => 'Bad',
						'stateId' => 2,
						'amount' => 1,
						'stateUrl' => [
							'controller' => 'some_controller',
							'action' => 'some_action',
							2
						],
						'class' => 'progress-bar-danger'
					],
				], // $stateData
			],
		];
		$expected = [
			'<div class="progress"><div role="progressbar" style="width:100%" title="' . htmlentities(__d('view_extension', '&lt;None&gt;')) . ': 0 (100%)" data-toggle="tooltip" class="progress-bar">' . __d('view_extension', '&lt;None&gt;') . '</div></div>',
			'<div class="progress"><div role="progressbar" style="width:75%" title="Ok: 3 (75%)" data-toggle="tooltip" class="progress-bar">Ok</div><div role="progressbar" style="width:25%" title="Bad: 1 (25%)" data-toggle="tooltip" class="progress-bar">Bad</div></div>',
			'<div class="progress"><a href="/some_controller/some_action/1" target="_blank"><div role="progressbar" style="width:75%" title="Ok: 3 (75%)" data-toggle="tooltip" class="progress-bar progress-bar-success">Ok</div></a><a href="/some_controller/some_action/2" target="_blank"><div role="progressbar" style="width:25%" title="Bad: 1 (25%)" data-toggle="tooltip" class="progress-bar progress-bar-danger">Bad</div></a></div>',
		];
		$this->runClassMethodGroup('barState', $params, $expected);
	}

/**
 * testListLastInfo method
 *
 * @return void
 */
	public function testListLastInfo() {
		date_default_timezone_set('UTC');
		Configure::write('Config.timezone', 'UTC');
		$params = [
			[
				[], // $lastInfo
				'Some label', // $labelList
				'some_controller', // $controllerName
				'some_action', // $actionName
				10, // $length
			],
			[
				[
					[
						'label' => 'Some record',
						'modified' => mktime(12, 10, 27, 3, 6, 2017),
						'id' => '1',
					],
					[
						'label' => 'Some long record name for test',
						'modified' => mktime(12, 14, 42, 3, 6, 2017),
						'id' => '2',
					]
				], // $lastInfo
				'', // $labelList
				'some_controller', // $controllerName
				'some_action', // $actionName
				['data-modal-size' => 'lg'], // $linkOptions
				20, // $length
			],
		];
		$expected = [
			'<dl class="dl-horizontal"><dt>Some label:</dt><dd>' . __d('view_extension', '&lt;None&gt;') . '</dd></dl>',
			[
				'assertRegExp' => '/<dl class="dl-horizontal"><dt>Some Controller:<\/dt><dd><ol><li><a href="\/admin\/some_controller\/some_action\/1" data-modal-size="lg" data-toggle="modal-popover" class="popup-link text-nowrap" target="_blank" data-popover-placement="auto top" data-modal-title="' . __d('view_extension', 'Detail information') . '">Some record<\/a> \(<time data-toggle="timeago" datetime="2017-03-06T12:10:27\+00:00" class="help">.+<\/time>\)<\/li><li><a href="\/admin\/some_controller\/some_action\/2" data-modal-size="lg" data-toggle="modal-popover" class="popup-link text-nowrap" target="_blank" data-popover-placement="auto top" data-modal-title="' . __d('view_extension', 'Detail information') . '"><div class="collapse-text-expanded"><div class="collapse-text-truncated">Some long record<a href="#" role="button" data-toggle="collapse-text-expand" class="collapse-text-action-btn" title="' . __d('view_extension', 'Expand text') . '"><span class="fas fa-angle-double-right fa-lg"><\/span><\/a><\/div><div class="collapse-text-original">Some long record name for test<a href="#" role="button" data-toggle="collapse-text-roll-up" class="collapse-text-action-btn" title="' . __d('view_extension', 'Roll up text') . '"><span class="fas fa-angle-double-left fa-lg"><\/span><\/a><\/div><\/div><\/a> \(<time data-toggle="timeago" datetime="2017-03-06T12:14:42\+00:00" class="help">.+<\/time>\)<\/li><\/ol><\/dd><\/dl>/'
			]
		];
		$this->runClassMethodGroup('listLastInfo', $params, $expected);
	}

/**
 * testBarPagingPageCount0 method
 *
 * @return void
 */
	public function testBarPagingPageCount0() {
		$requestParams = [
			'paging' => [
				'Employee' => [
					'page' => 1,
					'current' => 0,
					'count' => 0,
					'prevPage' => false,
					'nextPage' => false,
					'pageCount' => 0,
					'order' => null,
					'limit' => 20,
					'options' => [
						'conditions' => []
					],
					'paramType' => 'named'
				]
			]
		];
		$this->_preparePaginationHelper($requestParams);
		$result = $this->_targetObject->barPaging(false, true, false);
		$expected = '';
		$this->assertData($expected, $result);
	}

/**
 * testBarPagingPage1 method
 *
 * @return void
 */
	public function testBarPagingPage1() {
		$requestParams = [
			'paging' => [
				'Employee' => [
					'page' => 1,
					'current' => 20,
					'count' => 44,
					'prevPage' => false,
					'nextPage' => true,
					'pageCount' => 3,
					'order' => null,
					'limit' => 20,
					'options' => [
						'conditions' => []
					],
					'paramType' => 'named'
				]
			]
		];
		$this->_preparePaginationHelper($requestParams);
		$params = [
			[
				true, // $showCounterInfo
				false, // $useShowList
				false, // $useGoToPage
				false, // $useChangeNumLines
			],
			[
				false, // $showCounterInfo
				true, // $useShowList
				true, // $useGoToPage
				true, // $useChangeNumLines
			],
		];
		$tags = [
			'{:page}',
			'{:pages}',
			'{:current}',
			'{:count}',
			'{:start}',
			'{:end}',
		];
		$vals = [
			'1',
			'3',
			'20',
			'44',
			'1',
			'20',
		];
		$records = __dn('view_extension', 'record', 'records', 20);
		$counterInfo = str_replace($tags, $vals, __d('view_extension', 'Page {:page} of {:pages}, showing {:current} %s out of {:count} total, starting on record {:start}, ending on {:end}', $records));
		$expected = [
			'<div class="paging"><p class="small">' . $counterInfo . '</p><ul class="pagination pagination-sm hidden-print hide-popup" data-toggle="pjax"><li class="active"><a>1</a></li><li><a href="/some_controller/some_action/page:2" currentLink="1">2</a></li><li><a href="/some_controller/some_action/page:3" currentLink="1">3</a></li><li class="next"><a href="/some_controller/some_action/page:2" title="' . __d('view_extension', 'Next page') . '" data-toggle="tooltip" rel="next"><span class="fas fa-angle-right fa-lg"></span></a></li><li><a href="/some_controller/some_action/page:3" title="' . __d('view_extension', 'Last page') . '" data-toggle="tooltip" rel="last"><span class="fas fa-angle-double-right fa-lg"></span></a></li></ul></div>',
			'<div class="paging"><ul class="pagination pagination-sm hidden-print hide-popup" data-toggle="pjax"><li class="active"><a>1</a></li><li><a href="/some_controller/some_action/page:2" currentLink="1">2</a></li><li><a href="/some_controller/some_action/page:3" currentLink="1">3</a></li><li class="next"><a href="/some_controller/some_action/page:2" title="' . __d('view_extension', 'Next page') . '" data-toggle="tooltip" rel="next"><span class="fas fa-angle-right fa-lg"></span></a></li><li><a href="/some_controller/some_action/page:3" title="' . __d('view_extension', 'Last page') . '" data-toggle="tooltip" rel="last"><span class="fas fa-angle-double-right fa-lg"></span></a></li><li><a href="/some_controller/some_action/show:list" title="' . __d('view_extension', 'Show as list') . '" data-toggle="pjax"><span class="fas fa-align-justify fa-lg"></span></a></li><li><a href="#" role="button" data-toggle="collapse" aria-expanded="false" data-target=".control-go-to-page" title="' . __d('view_extension', 'Go to the page') . '"><span class="fas fa-ellipsis-h fa-lg"></span></a><div class="control-go-to-page collapse"><input name="data[]" data-toggle="spin" data-spin-verticalbuttons="false" data-url="/some_controller/some_action/page:0" data-curr-value="1" value="1" id="gotopagebar" class="input-sm" autocomplete="off" title="' . __d('view_extension', 'The page number to quickly jump') . '" data-spin-max="3" data-spin-min="1" data-spin-step="1" data-spin-postfix="&lt;span class=&quot;fas fa-share fa-lg&quot;&gt;&lt;/span&gt;" data-spin-postfix_extraclass="btn btn-default btn-go-to-page" data-inputmask-mask="9{1,1}" type="text"/></div></li></ul><div class="control-change-num-lines"><select name="data[]" data-url="/some_controller/some_action/limit:20" data-curr-value="20" id="numlinesbar" class="form-control show-tick filter-condition input-sm" autocomplete="off" title="' . __d('view_extension', 'Number of lines') . '" data-toggle="select" data-style="btn-default btn-sm" data-width="false" data-live-search="false" data-size="auto">' . "\n" .
				'<option value="10">10</option>' . "\n" .
				'<option value="20" selected="selected">20</option>' . "\n" .
				'<option value="30">30</option>' . "\n" .
				'<option value="40">40</option>' . "\n" .
				'<option value="50">50</option>' . "\n" .
			'</select></div></div>',
		];
		$this->runClassMethodGroup('barPaging', $params, $expected);
	}

/**
 * testBarPagingPage1of1 method
 *
 * @return void
 */
	public function testBarPagingPage1of1() {
		$requestParams = [
			'paging' => [
				'Employee' => [
					'page' => 1,
					'current' => 8,
					'count' => 8,
					'prevPage' => false,
					'nextPage' => false,
					'pageCount' => 1,
					'order' => null,
					'limit' => 20,
					'options' => [
						'conditions' => []
					],
					'paramType' => 'named'
				]
			]
		];
		$this->_preparePaginationHelper($requestParams);
		$params = [
			[
				true, // $showCounterInfo
				false, // $useShowList
				false, // $useGoToPage
				false, // $useChangeNumLines
			],
			[
				false, // $showCounterInfo
				true, // $useShowList
				true, // $useGoToPage
				true, // $useChangeNumLines
			],
		];
		$tags = [
			'{:page}',
			'{:pages}',
			'{:current}',
			'{:count}',
			'{:start}',
			'{:end}',
		];
		$vals = [
			'1',
			'1',
			'8',
			'8',
			'1',
			'8',
		];
		$records = __dn('view_extension', 'record', 'records', 8);
		$counterInfo = str_replace($tags, $vals, __d('view_extension', 'Showing {:count} %s', $records));
		$expected = [
			'<div class="paging"><p class="small">' . $counterInfo . '</p></div>',
			'',
		];
		$this->runClassMethodGroup('barPaging', $params, $expected);
	}

/**
 * testBarPagingPage2 method
 *
 * @return void
 */
	public function testBarPagingPage2() {
		$requestParams = [
			'paging' => [
				'Employee' => [
					'page' => 2,
					'current' => 20,
					'count' => 44,
					'prevPage' => true,
					'nextPage' => true,
					'pageCount' => 3,
					'order' => null,
					'limit' => 20,
					'options' => [
						'conditions' => []
					],
					'paramType' => 'named'
				]
			]
		];
		$this->_preparePaginationHelper($requestParams);
		$result = $this->_targetObject->barPaging(false, true, false, false);
		$expected = '<div class="paging"><ul class="pagination pagination-sm hidden-print hide-popup" data-toggle="pjax"><li class="prev"><a href="/some_controller/some_action" title="' . __d('view_extension', 'Previous page') . '" data-toggle="tooltip" rel="prev"><span class="fas fa-angle-left fa-lg"></span></a></li><li><a href="/some_controller/some_action" currentLink="1">1</a></li><li class="active"><a>2</a></li><li><a href="/some_controller/some_action/page:3" currentLink="1">3</a></li><li class="next"><a href="/some_controller/some_action/page:3" title="' . __d('view_extension', 'Next page') . '" data-toggle="tooltip" rel="next"><span class="fas fa-angle-right fa-lg"></span></a></li></ul></div>';
		$this->assertData($expected, $result);
	}

/**
 * testBarPagingPage3of5 method
 *
 * @return void
 */
	public function testBarPagingPage3of5() {
		$requestParams = [
			'paging' => [
				'Employee' => [
					'page' => 3,
					'current' => 30,
					'count' => 145,
					'prevPage' => true,
					'nextPage' => true,
					'pageCount' => 5,
					'order' => null,
					'limit' => 30,
					'options' => [
						'conditions' => []
					],
					'paramType' => 'named'
				]
			]
		];
		$this->_preparePaginationHelper($requestParams);
		$params = [
			[
				true, // $showCounterInfo
				false, // $useShowList
				false, // $useGoToPage
				false, // $useChangeNumLines
			],
			[
				false, // $showCounterInfo
				true, // $useShowList
				true, // $useGoToPage
				true, // $useChangeNumLines
			],
		];
		$tags = [
			'{:page}',
			'{:pages}',
			'{:current}',
			'{:count}',
			'{:start}',
			'{:end}',
		];
		$vals = [
			'3',
			'5',
			'30',
			'145',
			'61',
			'90',
		];
		$records = __dn('view_extension', 'record', 'records', 30);
		$counterInfo = str_replace($tags, $vals, __d('view_extension', 'Page {:page} of {:pages}, showing {:current} %s out of {:count} total, starting on record {:start}, ending on {:end}', $records));
		$expected = [
			'<div class="paging"><p class="small">' . $counterInfo . '</p><ul class="pagination pagination-sm hidden-print hide-popup" data-toggle="pjax"><li><a href="/some_controller/some_action" title="' . __d('view_extension', 'First page') . '" data-toggle="tooltip" rel="first"><span class="fas fa-angle-double-left fa-lg"></span></a></li><li class="prev"><a href="/some_controller/some_action/page:2" title="' . __d('view_extension', 'Previous page') . '" data-toggle="tooltip" rel="prev"><span class="fas fa-angle-left fa-lg"></span></a></li><li><a href="/some_controller/some_action" currentLink="1">1</a></li><li><a href="/some_controller/some_action/page:2" currentLink="1">2</a></li><li class="active"><a>3</a></li><li><a href="/some_controller/some_action/page:4" currentLink="1">4</a></li><li><a href="/some_controller/some_action/page:5" currentLink="1">5</a></li><li class="next"><a href="/some_controller/some_action/page:4" title="' . __d('view_extension', 'Next page') . '" data-toggle="tooltip" rel="next"><span class="fas fa-angle-right fa-lg"></span></a></li><li><a href="/some_controller/some_action/page:5" title="' . __d('view_extension', 'Last page') . '" data-toggle="tooltip" rel="last"><span class="fas fa-angle-double-right fa-lg"></span></a></li></ul></div>',
			'<div class="paging"><ul class="pagination pagination-sm hidden-print hide-popup" data-toggle="pjax"><li><a href="/some_controller/some_action" title="' . __d('view_extension', 'First page') . '" data-toggle="tooltip" rel="first"><span class="fas fa-angle-double-left fa-lg"></span></a></li><li class="prev"><a href="/some_controller/some_action/page:2" title="' . __d('view_extension', 'Previous page') . '" data-toggle="tooltip" rel="prev"><span class="fas fa-angle-left fa-lg"></span></a></li><li><a href="/some_controller/some_action" currentLink="1">1</a></li><li><a href="/some_controller/some_action/page:2" currentLink="1">2</a></li><li class="active"><a>3</a></li><li><a href="/some_controller/some_action/page:4" currentLink="1">4</a></li><li><a href="/some_controller/some_action/page:5" currentLink="1">5</a></li><li class="next"><a href="/some_controller/some_action/page:4" title="' . __d('view_extension', 'Next page') . '" data-toggle="tooltip" rel="next"><span class="fas fa-angle-right fa-lg"></span></a></li><li><a href="/some_controller/some_action/page:5" title="' . __d('view_extension', 'Last page') . '" data-toggle="tooltip" rel="last"><span class="fas fa-angle-double-right fa-lg"></span></a></li><li><a href="#" role="button" data-toggle="collapse" aria-expanded="false" data-target=".control-go-to-page" title="' . __d('view_extension', 'Go to the page') . '"><span class="fas fa-ellipsis-h fa-lg"></span></a><div class="control-go-to-page collapse"><input name="data[]" data-toggle="spin" data-spin-verticalbuttons="false" data-url="/some_controller/some_action/page:0" data-curr-value="3" value="3" id="gotopagebar" class="input-sm" autocomplete="off" title="' . __d('view_extension', 'The page number to quickly jump') . '" data-spin-max="5" data-spin-min="1" data-spin-step="1" data-spin-postfix="&lt;span class=&quot;fas fa-share fa-lg&quot;&gt;&lt;/span&gt;" data-spin-postfix_extraclass="btn btn-default btn-go-to-page" data-inputmask-mask="9{1,1}" type="text"/></div></li></ul><div class="control-change-num-lines"><select name="data[]" data-url="/some_controller/some_action/limit:30" data-curr-value="30" id="numlinesbar" class="form-control show-tick filter-condition input-sm" autocomplete="off" title="' . __d('view_extension', 'Number of lines') . '" data-toggle="select" data-style="btn-default btn-sm" data-width="false" data-live-search="false" data-size="auto">' . "\n" .
				'<option value="10">10</option>' . "\n" .
				'<option value="20">20</option>' . "\n" .
				'<option value="30" selected="selected">30</option>' . "\n" .
				'<option value="40">40</option>' . "\n" .
				'<option value="50">50</option>' . "\n" .
				'<option value="100">100</option>' . "\n" .
				'<option value="150">150</option>' . "\n" .
			'</select></div></div>',
		];
		$this->runClassMethodGroup('barPaging', $params, $expected);
	}

/**
 * testBarPagingPage2Modal method
 *
 * @return void
 */
	public function testBarPagingPage2Modal() {
		$requestParams = [
			'paging' => [
				'Employee' => [
					'page' => 2,
					'current' => 20,
					'count' => 44,
					'prevPage' => true,
					'nextPage' => true,
					'pageCount' => 3,
					'order' => null,
					'limit' => 20,
					'options' => [
						'conditions' => []
					],
					'paramType' => 'named'
				]
			]
		];
		$this->_targetObject->request->addParams([
			'ext' => 'mod'
		]);
		$this->_preparePaginationHelper($requestParams);
		$result = $this->_targetObject->barPaging(false, true, false, false);
		$expected = '<div class="paging"><ul class="pagination pagination-sm hidden-print hide-popup" data-toggle="modal"><li class="prev"><a href="/some_controller/some_action" title="' . __d('view_extension', 'Previous page') . '" data-toggle="tooltip" rel="prev"><span class="fas fa-angle-left fa-lg"></span></a></li><li><a href="/some_controller/some_action" currentLink="1">1</a></li><li class="active"><a>2</a></li><li><a href="/some_controller/some_action/page:3" currentLink="1">3</a></li><li class="next"><a href="/some_controller/some_action/page:3" title="' . __d('view_extension', 'Next page') . '" data-toggle="tooltip" rel="next"><span class="fas fa-angle-right fa-lg"></span></a></li></ul></div>';
		$this->assertData($expected, $result);
	}

/**
 * testButtonsMove method
 *
 * @return void
 */
	public function testButtonsMove() {
		$params = [
			[
				[
					'controller' => 'some_controller',
					'action' => 'move',
					4
				], // $url
				false, // $useDrag
				'', // $glue
				false, // $useGroup
			],
			[
				[
					'controller' => 'some_controller',
					'action' => 'move',
					4
				], // $url
				true, // $useDrag
				'', // $glue
				true, // $useGroup
			],
			[
				[
					'controller' => 'some_controller',
					'action' => 'move',
					4
				], // $url
				false, // $useDrag
				'&nbsp;', // $glue
				true, // $useGroup
			],
		];
		$expected = [
			'<a href="/some_controller/move/top/4" title="' . __d('view_extension', 'Move top') . '" data-toggle="move" class="btn btn-info btn-xs"><span class="fas fa-angle-double-up fa-fw fa-lg"></span></a><a href="/some_controller/move/up/4" title="' . __d('view_extension', 'Move up') . '" data-toggle="move" class="btn btn-info btn-xs"><span class="fas fa-angle-up fa-fw fa-lg"></span></a><a href="/some_controller/move/down/4" title="' . __d('view_extension', 'Move down') . '" data-toggle="move" class="btn btn-info btn-xs"><span class="fas fa-angle-down fa-fw fa-lg"></span></a><a href="/some_controller/move/bottom/4" title="' . __d('view_extension', 'Move bottom') . '" data-toggle="move" class="btn btn-info btn-xs"><span class="fas fa-angle-double-down fa-fw fa-lg"></span></a>',
			'<div role="group" class="btn-group"><a href="#" role="button" data-toggle="drag" title="' . __d('view_extension', 'Drag and drop this item') . '" class="btn btn-primary btn-xs"><span class="fas fa-arrows-alt fa-fw fa-lg"></span></a><a href="/some_controller/move/top/4" title="' . __d('view_extension', 'Move top') . '" data-toggle="move" class="btn btn-info btn-xs"><span class="fas fa-angle-double-up fa-fw fa-lg"></span></a><a href="/some_controller/move/up/4" title="' . __d('view_extension', 'Move up') . '" data-toggle="move" class="btn btn-info btn-xs"><span class="fas fa-angle-up fa-fw fa-lg"></span></a><a href="/some_controller/move/down/4" title="' . __d('view_extension', 'Move down') . '" data-toggle="move" class="btn btn-info btn-xs"><span class="fas fa-angle-down fa-fw fa-lg"></span></a><a href="/some_controller/move/bottom/4" title="' . __d('view_extension', 'Move bottom') . '" data-toggle="move" class="btn btn-info btn-xs"><span class="fas fa-angle-double-down fa-fw fa-lg"></span></a></div>',
			'<a href="/some_controller/move/top/4" title="' . __d('view_extension', 'Move top') . '" data-toggle="move" class="btn btn-info btn-xs"><span class="fas fa-angle-double-up fa-fw fa-lg"></span></a>&nbsp;<a href="/some_controller/move/up/4" title="' . __d('view_extension', 'Move up') . '" data-toggle="move" class="btn btn-info btn-xs"><span class="fas fa-angle-up fa-fw fa-lg"></span></a>&nbsp;<a href="/some_controller/move/down/4" title="' . __d('view_extension', 'Move down') . '" data-toggle="move" class="btn btn-info btn-xs"><span class="fas fa-angle-down fa-fw fa-lg"></span></a>&nbsp;<a href="/some_controller/move/bottom/4" title="' . __d('view_extension', 'Move bottom') . '" data-toggle="move" class="btn btn-info btn-xs"><span class="fas fa-angle-double-down fa-fw fa-lg"></span></a>',
		];
		$this->runClassMethodGroup('buttonsMove', $params, $expected);
	}

/**
 * testButtonLoadMoreFirstPage method
 *
 * @return void
 */
	public function testButtonLoadMoreFirstPage() {
		$requestParams = [
			'paging' => [
				'Employee' => [
					'page' => 1,
					'current' => 20,
					'count' => 44,
					'prevPage' => false,
					'nextPage' => true,
					'pageCount' => 3,
					'order' => null,
					'limit' => 20,
					'options' => [
						'conditions' => []
					],
					'paramType' => 'named'
				]
			]
		];
		$tags = [
			'{:page}',
			'{:pages}',
			'{:current}',
			'{:count}',
			'{:start}',
			'{:end}',
		];
		$vals = [
			'1',
			'3',
			'20',
			'44',
			'1',
			'20',
		];
		$records = __dn('view_extension', 'record', 'records', 20);
		$counterInfo = str_replace($tags, $vals, __d('view_extension', 'Current loaded page {:page} of {:pages}, showing {:end} %s of {:count} total', $records));
		$this->_preparePaginationHelper($requestParams);
		$result = $this->_targetObject->buttonLoadMore('.some-class');
		$expected = '<div class="load-more"><p class="small">' . $counterInfo . '</p><a href="/some_controller/some_action/page:2/show:list" class="btn btn-default btn-sm btn-block hidden-print" data-target-selector=".some-class" role="button" data-toggle="load-more" title="' . __d('view_extension', 'Load more informations') . '">' . __d('view_extension', 'Load more') . '</a></div>';
		$this->assertData($expected, $result);
	}

/**
 * testButtonLoadMoreLastPage method
 *
 * @return void
 */
	public function testButtonLoadMoreLastPage() {
		$requestParams = [
			'paging' => [
				'Employee' => [
					'page' => 3,
					'current' => 4,
					'count' => 44,
					'prevPage' => true,
					'nextPage' => false,
					'pageCount' => 3,
					'order' => null,
					'limit' => 20,
					'options' => [
						'conditions' => []
					],
					'paramType' => 'named'
				]
			]
		];
		$tags = [
			'{:page}',
			'{:pages}',
			'{:current}',
			'{:count}',
			'{:start}',
			'{:end}',
		];
		$vals = [
			'1',
			'3',
			'20',
			'44',
			'1',
			'20',
		];
		$records = __dn('view_extension', 'record', 'records', 4);
		$counterInfo = str_replace($tags, $vals, __d('view_extension', 'Showing {:count} %s', $records));
		$this->_preparePaginationHelper($requestParams);
		$result = $this->_targetObject->buttonLoadMore('.some-class');
		$expected = '<div class="load-more"><p class="small">' . $counterInfo . '</p></div>';
		$this->assertData($expected, $result);
	}

/**
 * testButtonsPagingWithLoadMoreBtn method
 *
 * @return void
 */
	public function testButtonsPagingWithLoadMoreBtn() {
		$params = [
			'named' => [
				'show' => 'list'
			]
		];
		$this->_targetObject->request->addParams($params);
		$requestParams = [
			'paging' => [
				'Employee' => [
					'page' => 1,
					'current' => 10,
					'count' => 44,
					'prevPage' => false,
					'nextPage' => true,
					'pageCount' => 3,
					'order' => null,
					'limit' => 20,
					'options' => [
						'conditions' => []
					],
					'paramType' => 'named'
				]
			]
		];
		$this->_preparePaginationHelper($requestParams);
		$result = $this->_targetObject->buttonsPaging('.some-class', true, true, true, true);
		$expected = '/\<div class\="load\-more"\>.+\<a\s.+data\-target\-selector\="\.some\-class"\s.+\>/';
		$this->assertRegExp($expected, $result);
	}

/**
 * testButtonsPagingWoLoadMoreBtn method
 *
 * @return void
 */
	public function testButtonsPagingWoLoadMoreBtn() {
		$requestParams = [
			'paging' => [
				'Employee' => [
					'page' => 1,
					'current' => 10,
					'count' => 44,
					'prevPage' => false,
					'nextPage' => true,
					'pageCount' => 3,
					'order' => null,
					'limit' => 20,
					'options' => [
						'conditions' => []
					],
					'paramType' => 'named'
				]
			]
		];
		$this->_preparePaginationHelper($requestParams);
		$result = $this->_targetObject->buttonsPaging('.some-class', true, true, true, true);
		$expected = '/\<div class\="paging"\>.+<ul class\="pagination pagination\-sm hidden\-print hide\-popup" data\-toggle\="pjax"\>.+\<li\>/';
		$this->assertRegExp($expected, $result);
	}

/**
 * testButtonPrint method
 *
 * @return void
 */
	public function testButtonPrint() {
		$result = $this->_targetObject->buttonPrint();
		$expected = '<button data-toggle="print" class="btn btn-default view-extension-print-btn" title="' . __d('view_extension', 'Print this page') . '" type="button"><span class="fas fa-print fa-fw fa-lg"></span></button>';
		$this->assertData($expected, $result);
	}

/**
 * testMenuHeaderPage method
 *
 * @return void
 */
	public function testMenuHeaderPage() {
		$params = [
			[
				'', // $headerMenuActions
			],
			[
				'divider', // $headerMenuActions
			],
			[
				'<a href="/admin/test/act"><span class="menu-item-label">Some title</span></a>', // $headerMenuActions
			],
			[
				[
					'<a href="/admin/test/act"><span class="menu-item-label">Some title</span></a>',
					'<a href="/admin/test/edit"><span class="fas fa-pencil-alt fa-lg fa-fw"></span><span class="menu-item-label">Edit</span></a>'
				], // $headerMenuActions
			],
			[
				[
					[]
				], // $headerMenuActions
			],
			[
				[
					'<a href="/admin/test/act"><span class="menu-item-label">Some title</span></a>',
					[
						'<a href="/admin/test/edit"><span class="fas fa-pencil-alt fa-lg fa-fw"></span><span class="menu-item-label">Edit</span></a>'
					]
				], // $headerMenuActions
			],
			[
				[
					'<a href="/admin/test/act"><span class="menu-item-label">Some title</span></a>',
					[
						'<a href="/admin/test/edit"><span class="fas fa-pencil-alt fa-lg fa-fw"></span><span class="menu-item-label">Edit</span></a>',
						['class' => 'disabled']
					]
				], // $headerMenuActions
			],
			[
				[
					[
						'far fa-trash-alt',
						'Delete this item',
						['controller' => 'test', 'action' => 'delete', 1],
						[
							'title' => 'Delete this item', 'action-type' => 'confirm-post',
							'data-confirm-msg' => 'Are you sure you wish to delete this item?',
						]
					]
				], // $headerMenuActions
			],
		];
		$expected = [
			'',
			'<div class="btn-group page-header-menu hidden-print"><button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" title="' . __d('view_extension', 'Menu of operations') . '" aria-haspopup="true" aria-expanded="false" id="pageHeaderMenu"><span class="fas fa-bars fa-lg"></span>&nbsp;<span class="caret"></span></button><ul class="dropdown-menu" aria-labelledby="pageHeaderMenu"><li class="divider"></li></ul></div>',
			'<div class="btn-group page-header-menu hidden-print"><button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" title="' . __d('view_extension', 'Menu of operations') . '" aria-haspopup="true" aria-expanded="false" id="pageHeaderMenu"><span class="fas fa-bars fa-lg"></span>&nbsp;<span class="caret"></span></button><ul class="dropdown-menu" aria-labelledby="pageHeaderMenu"><li><a href="/admin/test/act"><span class="menu-item-label">Some title</span></a></li></ul></div>',
			'<div class="btn-group page-header-menu hidden-print"><button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" title="' . __d('view_extension', 'Menu of operations') . '" aria-haspopup="true" aria-expanded="false" id="pageHeaderMenu"><span class="fas fa-bars fa-lg"></span>&nbsp;<span class="caret"></span></button><ul class="dropdown-menu" aria-labelledby="pageHeaderMenu"><li><a href="/admin/test/act"><span class="menu-item-label">Some title</span></a></li><li><a href="/admin/test/edit"><span class="fas fa-pencil-alt fa-lg fa-fw"></span><span class="menu-item-label">Edit</span></a></li></ul></div>',
			'',
			'<div class="btn-group page-header-menu hidden-print"><button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" title="' . __d('view_extension', 'Menu of operations') . '" aria-haspopup="true" aria-expanded="false" id="pageHeaderMenu"><span class="fas fa-bars fa-lg"></span>&nbsp;<span class="caret"></span></button><ul class="dropdown-menu" aria-labelledby="pageHeaderMenu"><li><a href="/admin/test/act"><span class="menu-item-label">Some title</span></a></li><li><a href="/admin/test/edit"><span class="fas fa-pencil-alt fa-lg fa-fw"></span><span class="menu-item-label">Edit</span></a></li></ul></div>',
			'<div class="btn-group page-header-menu hidden-print"><button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" title="' . __d('view_extension', 'Menu of operations') . '" aria-haspopup="true" aria-expanded="false" id="pageHeaderMenu"><span class="fas fa-bars fa-lg"></span>&nbsp;<span class="caret"></span></button><ul class="dropdown-menu" aria-labelledby="pageHeaderMenu"><li><a href="/admin/test/act"><span class="menu-item-label">Some title</span></a></li><li class="disabled"><a href="/admin/test/edit"><span class="fas fa-pencil-alt fa-lg fa-fw"></span><span class="menu-item-label">Edit</span></a></li></ul></div>',
			[
				'assertRegExp' => '/\<div class\="btn\-group page\-header\-menu hidden\-print"\>\<button type\="button" class\="btn btn\-default dropdown\-toggle" data\-toggle\="dropdown" title\="' . __d('view_extension', 'Menu of operations') . '" aria\-haspopup\="true" aria\-expanded\="false" id\="pageHeaderMenu"\>\<span class\="fas fa\-bars fa\-lg"\>\<\/span\>&nbsp;\<span class\="caret"\>\<\/span\>\<\/button\>\<ul class\="dropdown\-menu" aria\-labelledby\="pageHeaderMenu"\>\<li\>\<form action\="\/admin\/test\/delete\/1" name\="post_[0-9a-f]+" id\="post_[0-9a-f]+" style\="display\:none;" method\="post"\>\<input type\="hidden" name\="_method" value\="POST"\/\>\<\/form\>\<a href\="#" title\="Delete this item" data\-confirm\-msg\=".+" data\-confirm\-btn\-ok\="' . __d('view_extension', 'Yes') . '" data\-confirm\-btn\-cancel\="' . __d('view_extension', 'No') . '" role\="post\-link" data\-toggle\="title" onclick\="document\.post_[0-9a-f]+\.submit\(\); event\.returnValue \= false; return false;"\>\<span class\="far fa\-trash\-alt fa\-fw fa\-lg"\>\<\/span\>\<span class\="menu\-item\-label"\>Delete this item\<\/span\>\<\/a\>\<\/li\>\<\/ul\>\<\/div\>/'
			]
		];
		$this->runClassMethodGroup('menuHeaderPage', $params, $expected);
	}

/**
 * testHeaderPage method
 *
 * @return void
 */
	public function testHeaderPage() {
		$params = [
			[
				'', // $pageHeader
				'', // $headerMenuActions
			],
			[
				'Some header', // $pageHeader
				'', // $headerMenuActions
			],
			[
				'Some header', // $pageHeader
				'<a href="/admin/test/act"><span class="menu-item-label">Some title</span></a>', // $headerMenuActions
			],
			[
				'Some header', // $pageHeader
				[
					'<a href="/admin/test/act"><span class="menu-item-label">Some title</span></a>',
					'<a href="/admin/test/edit"><span class="fas fa-pencil-alt fa-lg fa-fw"></span><span class="menu-item-label">Edit</span></a>'
				], // $headerMenuActions
			],
		];
		$expected = [
			'',
			'<div class="page-header well"><h2 class="header">Some header</h2></div>',
			'<div class="page-header well"><h2 class="header">Some header<div class="btn-group page-header-menu hidden-print"><button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" title="' . __d('view_extension', 'Menu of operations') . '" aria-haspopup="true" aria-expanded="false" id="pageHeaderMenu"><span class="fas fa-bars fa-lg"></span>&nbsp;<span class="caret"></span></button><ul class="dropdown-menu" aria-labelledby="pageHeaderMenu"><li><a href="/admin/test/act"><span class="menu-item-label">Some title</span></a></li></ul></div></h2></div>',
			'<div class="page-header well"><h2 class="header">Some header<div class="btn-group page-header-menu hidden-print"><button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" title="' . __d('view_extension', 'Menu of operations') . '" aria-haspopup="true" aria-expanded="false" id="pageHeaderMenu"><span class="fas fa-bars fa-lg"></span>&nbsp;<span class="caret"></span></button><ul class="dropdown-menu" aria-labelledby="pageHeaderMenu"><li><a href="/admin/test/act"><span class="menu-item-label">Some title</span></a></li><li><a href="/admin/test/edit"><span class="fas fa-pencil-alt fa-lg fa-fw"></span><span class="menu-item-label">Edit</span></a></li></ul></div></h2></div>',
		];
		$this->runClassMethodGroup('headerPage', $params, $expected);
	}

/**
 * testCollapsibleList method
 *
 * @return void
 */
	public function testCollapsibleList() {
		$params = [
			[
				[], // $listData
				10, // $showLimit
				'', // $listClass
				'ul', // $showLimit
			],
			[
				[
					'Item 1',
					'Item 2'
				], // $listData
				0, // $showLimit
				'some-class', // $listClass
				'ul', // $showLimit
			],
			[
				[
					'Item 1',
					'Item 2',
					'Item 3',
				], // $listData
				5, // $showLimit
				'some-class', // $listClass
				'bad', // $showLimit
			],
			[
				[
					'Item 1',
					'Item 2',
					'Item 3',
					'Item 4',
				], // $listData
				2, // $showLimit
				'list-class', // $listClass
				'ol', // $showLimit
			],
		];
		$expected = [
			'',
			'',
			'<ul class="list-collapsible-compact some-class"><li>Item 1</li><li>Item 2</li><li>Item 3</li></ul>',
			[
				'assertRegExp' => '/<ol class\="list\-collapsible\-compact list\-class"><li>Item 1<\/li><li>Item 2<\/li><\/ol><ol class\="list\-collapsible\-compact collapse list\-class" id\="collapsible\-list\-[0-9a-f]+"><li>Item 3<\/li><li>Item 4<\/li><\/ol><button class\="btn btn\-default btn\-xs top\-buffer hide\-popup" title\="' . __d('view_extension', 'Show or hide full list') . '" data\-toggle\="collapse" data\-target\="#collapsible\-list\-[0-9a-f]+" aria\-expanded\="false" data\-toggle\-icons\="fa\-angle\-double\-down,fa\-angle\-double\-up" type\="button"><span class\="fas fa\-angle\-double\-down fa\-fw fa\-lg"><\/span><\/button>/',
			],
		];
		$this->runClassMethodGroup('collapsibleList', $params, $expected);
	}

/**
 * testAddBreadCrumbsInvalidParam method
 *
 * @return void
 */
	public function testAddBreadCrumbsInvalidParam() {
		$breadCrumbs = 'bad';
		$this->_targetObject->addBreadCrumbs($breadCrumbs);
		$result = $this->_targetObject->Html->getCrumbs();
		$this->assertNull($result);
	}

/**
 * testAddBreadCrumbsEmptyParam method
 *
 * @return void
 */
	public function testAddBreadCrumbsEmptyParam() {
		$breadCrumbs = [];
		$this->_targetObject->addBreadCrumbs($breadCrumbs);
		$result = $this->_targetObject->Html->getCrumbs();
		$this->assertNull($result);
	}

/**
 * testAddBreadCrumbsValidParam method
 *
 * @return void
 */
	public function testAddBreadCrumbsValidParam() {
		$breadCrumbs = [
			[
				'root'
			],
			[
				'Some name',
				[
					'controller' => 'some_controller',
					'action' => 'act'
				]
			],
		];
		$this->_targetObject->addBreadCrumbs($breadCrumbs);
		$result = $this->_targetObject->Html->getCrumbs();
		$expected = 'root&raquo;<a href="/some_controller/act">Some name</a>';
		$this->assertData($expected, $result);
	}

/**
 * Prepare options for Paginator Helper
 *
 * @param array $params Array of parameters for request
 * @return void
 */
	protected function _preparePaginationHelper($params = null) {
		$this->_targetObject->Paginator->options = ['url' => ['controller' => 'some_controller', 'action' => 'some_action']];
		$this->_targetObject->Paginator->request->addParams($params);
	}
}
