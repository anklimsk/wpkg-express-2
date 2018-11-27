<?php
App::uses('View', 'View');
App::uses('Helper', 'View');
App::uses('Controller', 'Controller');
App::uses('StyleHelper', 'CakeNotify.View/Helper');
App::uses('AppCakeTestCase', 'CakeNotify.Test');

class StyleTestController extends Controller {

/**
 * helpers property
 *
 * @var array
 */
	public $helpers = ['Html', 'CakeNotify.Style'];

/**
 * The name of the layout file to render the view inside of. The name specified
 * is the filename of the layout in /app/View/Layouts without the .ctp
 * extension.
 *
 * @var string
 */
	public $layout = 'test';

/**
 * index method
 *
 * @return void
 */
	public function index() {
	}

}

/**
 * StyleHelper Test Case
 */
class StyleHelperTest extends AppCakeTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		$testView = CakePlugin::path('CakeNotify') . 'Test' . DS . 'test_app' . DS . 'View' . DS;
		App::build(['View' => [$testView]]);
		parent::setUp();
		$request = new CakeRequest('/style_test/index');
		$this->Controller = new StyleTestController($request);
		$this->View = new View($this->Controller);
		$this->_targetObject = new StyleHelper($this->View);
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
 * testGetStyle method
 *
 * @return void
 */
	public function testGetStyle() {
		$result = $this->_targetObject->getStyle();
		$expected = '/body\s\{/';
		$this->assertRegExp($expected, $result);

		$expected = '/a\s\{/';
		$this->assertRegExp($expected, $result);

		$expected = '/h1,\s\.h1\s\{/';
		$this->assertRegExp($expected, $result);
	}

/**
 * testAfterLayout method
 *
 * @return void
 */
	public function testAfterLayout() {
		$result = $this->View->render('index');
		$expected = '<h2 style="box-sizing: border-box; font-size: 30px; color: inherit; font-family: inherit; font-weight: 500; line-height: 1.1; margin-bottom: 10px; margin-top: 20px;">Test header</h2>';
		$this->assertContains($expected, $result);

		$expected = '<p  style="box-sizing: border-box; margin: 0 0 10px; font-size: 21px; font-weight: 300; line-height: 1.4; margin-bottom: 20px;">';
		$this->assertContains($expected, $result);

		$expected = '<a href="http://localhost/style_test" style="box-sizing: border-box; color: #337ab7; text-decoration: none;">Test link</a>';
		$this->assertContains($expected, $result);
	}
}
