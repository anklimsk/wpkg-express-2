<?php
App::uses('View', 'View');
App::uses('Helper', 'View');
App::uses('ActionScriptHelper', 'CakeTheme.View/Helper');
App::uses('AppCakeTestCase', 'CakeTheme.Test');
App::uses('CakeRequest', 'Network');
App::uses('Folder', 'Utility');

/**
 * ActionScriptHelperTest Test Case
 *
 */
class ActionScriptHelperTest extends AppCakeTestCase {

/**
 * Current path to web root.
 *
 * @var string
 */
	protected $_webRoot = '';

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();

		$View = new View();
		$View->request = new CakeRequest(null, false);
		$this->_targetObject = new ActionScriptHelper($View);

		$this->_webRoot = Configure::read('App.www_root');
		$testWebRoot = CakePlugin::path('CakeTheme') . 'Test' . DS . 'test_app' . DS . 'webroot' . DS;

		date_default_timezone_set('UTC');
		Configure::write('Config.timezone', 'UTC');
		$timestamp = strtotime('2018-08-08 14:27:06 UTC');
		$oFolder = new Folder();
		$aFilesWebRoot = $oFolder->tree($testWebRoot, false, 'file');
		foreach ($aFilesWebRoot as $filePath) {
			touch($filePath, $timestamp);
		}

		Configure::write('App.www_root', $testWebRoot);
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		if (!empty($this->_webRoot)) {
			Configure::write('App.www_root', $this->_webRoot);
		}
		unset($this->_webRoot);

		parent::tearDown();
	}

/**
 * testGetFilesForActionCssEmptyParamEmptyRequestParam method
 *
 * @return void
 */
	public function testGetFilesForActionCssEmptyParamEmptyRequestParam() {
		$result = $this->_targetObject->getFilesForAction('css');
		$expected = [];
		$this->assertData($expected, $result);
	}

/**
 * testGetFilesForActionInvalidTypeEmptyParam method
 *
 * @return void
 */
	public function testGetFilesForActionInvalidTypeEmptyParamValidRequestParam() {
		$params = [
			'controller' => 'tests',
			'action' => 'tst'];
		$this->_targetObject->request->addParams($params);
		$result = $this->_targetObject->getFilesForAction('test', [], false, false);
		$expected = [];
		$this->assertData($expected, $result);
	}

/**
 * testGetFilesForActionInvalidTypeEmptyParam method
 *
 * @return void
 */
	public function testGetFilesForActionTypeCssEmptyParamValidRequestParam() {
		$params = [
			'controller' => 'tests',
			'action' => 'tst'];
		$this->_targetObject->request->addParams($params);
		$result = $this->_targetObject->getFilesForAction('css', [], false, false);
		$expected = ['specific/tests/tst.css?v=5b6afdba'];
		$this->assertData($expected, $result);
	}

/**
 * testGetFilesForActionTypeJsValidParamValidRequestParam method
 *
 * @return void
 */
	public function testGetFilesForActionTypeJsValidParamValidRequestParam() {
		$params = [
			'controller' => 'tests',
			'action' => 'tst'];
		$this->_targetObject->request->addParams($params);
		$result = $this->_targetObject->getFilesForAction('js', ['somep'], false, false);
		$expected = [
			'specific/tests/tst.js?v=5b6afdba',
			'specific/tests/somep/tst.min.js?v=5b6afdba'];
		$this->assertData($expected, $result);
	}

/**
 * testGetFilesForActionTypeJsValidParamValidRequestParamIncludeOnlyParam method
 *
 * @return void
 */
	public function testGetFilesForActionTypeJsValidParamValidRequestParamIncludeOnlyParam() {
		$params = [
			'controller' => 'tests',
			'action' => 'tst'];
		$this->_targetObject->request->addParams($params);
		$result = $this->_targetObject->getFilesForAction('js', ['param'], true, false);
		$expected = ['specific/tests/param/tst.js?v=5b6afdba'];
		$this->assertData($expected, $result);
	}

/**
 * testGetFilesForActionTypeCssEmptyParamValidRequestParamMinimizedFiles method
 *
 * @return void
 */
	public function testGetFilesForActionTypeCssEmptyParamValidRequestParamMinimizedFiles() {
		$params = [
			'controller' => 'tests',
			'action' => 'test'];
		$this->_targetObject->request->addParams($params);
		$result = $this->_targetObject->getFilesForAction('css', [], false, false);
		$expected = [
			'specific/tests/test.min.css?v=5b6afdba'
		];
		$this->assertData($expected, $result);
	}

/**
 * testGetFilesForActionTypeJsEmptyParamValidRequestParamMinimizedFiles method
 *
 * @return void
 */
	public function testGetFilesForActionTypeJsEmptyParamValidRequestParamMinimizedFiles() {
		$params = [
			'controller' => 'tests',
			'action' => 'test'];
		$this->_targetObject->request->addParams($params);
		$result = $this->_targetObject->getFilesForAction('js', [], false, false);
		$expected = [
			'specific/tests/test.min.js?v=5b6afdba'
		];
		$this->assertData($expected, $result);
	}

/**
 * testGetFilesForActionTypeJsEmptyParamValidRequestParamSetViewVarSpecificJs method
 *
 * @return void
 */
	public function testGetFilesForActionTypeJsEmptyParamValidRequestParamSetViewVarSpecificJs() {
		$params = [
			'controller' => 'tests',
			'action' => 'test'];
		$this->_targetObject->request->addParams($params);
		$this->_targetObject->_View->set('specificJS', 'spec');
		$result = $this->_targetObject->getFilesForAction('js', [], false, false);
		$expected = [
			'specific/tests/test.min.js?v=5b6afdba',
			'specific/tests/spec.js?v=5b6afdba'
		];
		$this->assertData($expected, $result);
	}

/**
 * testGetFilesForActionTypeJsEmptyParamValidRequestParamSetViewVarSpecificJsArrayWithIncludeFile method
 *
 * @return void
 */
	public function testGetFilesForActionTypeJsEmptyParamValidRequestParamSetViewVarSpecificJsArrayWithIncludeFile() {
		$params = [
			'controller' => 'tests',
			'action' => 'test'];
		$this->_targetObject->request->addParams($params);
		$this->_targetObject->_View->set('specificJS', ['spec', 'somec' . DS . 'file']);
		$result = $this->_targetObject->getFilesForAction('js', [], false, false);
		$expected = [
			'specific/tests/test.min.js?v=5b6afdba',
			'specific/tests/spec.js?v=5b6afdba',
			'specific/somec/file.min.js?v=5b6afdba',
		];
		$this->assertData($expected, $result);
	}

/**
 * testGetFilesForActionTypeCssEmptyParamValidRequestParamUseUserRolePrefix method
 *
 * @return void
 */
	public function testGetFilesForActionTypeCssEmptyParamValidRequestParamUseUserRolePrefix() {
		$params = [
			'controller' => 'tests',
			'action' => 'admin_test',
			'prefix' => 'admin'];
		$this->_targetObject->request->addParams($params);
		$result = $this->_targetObject->getFilesForAction('css', [], false, true);
		$expected = [
			'specific/tests/admin_test.min.css?v=5b6afdba'
		];
		$this->assertData($expected, $result);
	}

/**
 * testGetFilesForActionTypeJsEmptyParamValidRequestParamUseUserRolePrefix method
 *
 * @return void
 */
	public function testGetFilesForActionTypeJsEmptyParamValidRequestParamUseUserRolePrefix() {
		$params = [
			'controller' => 'tests',
			'action' => 'admin_test',
			'prefix' => 'admin'];
		$this->_targetObject->request->addParams($params);
		$result = $this->_targetObject->getFilesForAction('js', [], false, true);
		$expected = [
			'specific/tests/admin_test.min.js?v=5b6afdba'
		];
		$this->assertData($expected, $result);
	}

/**
 * testGetCssBlockEmptyParamEmptyRequestParam method
 *
 * @return void
 */
	public function testGetCssBlockEmptyParamEmptyRequestParam() {
		$result = $this->_targetObject->css();
		$this->assertNull($result);
	}

/**
 * testGetCssBlockForActionInvalidTypeEmptyParam method
 *
 * @return void
 */
	public function testGetCssBlockForActionTypeCssEmptyParamValidRequestParam() {
		$params = [
			'controller' => 'tests',
			'action' => 'tst'];
		$this->_targetObject->request->addParams($params);
		$result = $this->_targetObject->css([], [], false, false);
		$expected = "\n\t<link rel=\"stylesheet\" type=\"text/css\" href=\"/css/specific/tests/tst.css?v=5b6afdba\"/>\n";
		$this->assertData($expected, $result);
	}

/**
 * testGetJsBlockValidParamValidRequestParam method
 *
 * @return void
 */
	public function testGetJsBlockValidParamValidRequestParam() {
		$params = [
			'controller' => 'tests',
			'action' => 'tst'];
		$this->_targetObject->request->addParams($params);
		$result = $this->_targetObject->script([], ['somep'], false, false);
		$expected = "\n\t<script type=\"text/javascript\" src=\"/js/specific/tests/tst.js?v=5b6afdba\"></script>\n\t<script type=\"text/javascript\" src=\"/js/specific/tests/somep/tst.min.js?v=5b6afdba\"></script>\n";
		$this->assertData($expected, $result);
	}

/**
 * testGetJsBlockValidParamValidRequestParamIncludeOnlyParam method
 *
 * @return void
 */
	public function testGetJsBlockValidParamValidRequestParamIncludeOnlyParam() {
		$params = [
			'controller' => 'tests',
			'action' => 'tst'];
		$this->_targetObject->request->addParams($params);
		$result = $this->_targetObject->script([], ['param'], true, false);
		$expected = "\n\t<script type=\"text/javascript\" src=\"/js/specific/tests/param/tst.js?v=5b6afdba\"></script>\n";
		$this->assertData($expected, $result);
	}

/**
 * testGetCssBlockEmptyParamValidRequestParamMinimizedFiles method
 *
 * @return void
 */
	public function testGetCssBlockEmptyParamValidRequestParamMinimizedFiles() {
		$params = [
			'controller' => 'tests',
			'action' => 'test'];
		$this->_targetObject->request->addParams($params);
		$result = $this->_targetObject->css([], [], false, false);
		$expected = "\n\t<link rel=\"stylesheet\" type=\"text/css\" href=\"/css/specific/tests/test.min.css?v=5b6afdba\"/>\n";
		$this->assertData($expected, $result);
	}

/**
 * testGetJsBlocEmptyParamValidRequestParamMinimizedFiles method
 *
 * @return void
 */
	public function testGetJsBlocEmptyParamValidRequestParamMinimizedFiles() {
		$params = [
			'controller' => 'tests',
			'action' => 'test'];
		$this->_targetObject->request->addParams($params);
		$result = $this->_targetObject->script([], [], false, false);
		$expected = "\n\t<script type=\"text/javascript\" src=\"/js/specific/tests/test.min.js?v=5b6afdba\"></script>\n";
		$this->assertData($expected, $result);
	}

/**
 * testGetJsBlockEmptyParamValidRequestParamSetViewVarSpecificJs method
 *
 * @return void
 */
	public function testGetJsBlockEmptyParamValidRequestParamSetViewVarSpecificJs() {
		$params = [
			'controller' => 'tests',
			'action' => 'test'];
		$this->_targetObject->request->addParams($params);
		$this->_targetObject->_View->set('specificJS', 'spec');
		$result = $this->_targetObject->script([], [], false, false);
		$expected = "\n\t<script type=\"text/javascript\" src=\"/js/specific/tests/test.min.js?v=5b6afdba\"></script>\n\t<script type=\"text/javascript\" src=\"/js/specific/tests/spec.js?v=5b6afdba\"></script>\n";
		$this->assertData($expected, $result);
	}

/**
 * testGetCssBlockEmptyParamValidRequestParamUseUserRolePrefix method
 *
 * @return void
 */
	public function testGetCssBlockEmptyParamValidRequestParamUseUserRolePrefix() {
		$params = [
			'controller' => 'tests',
			'action' => 'admin_test',
			'prefix' => 'admin'];
		$this->_targetObject->request->addParams($params);
		$result = $this->_targetObject->css([], [], false, true);
		$expected = "\n\t<link rel=\"stylesheet\" type=\"text/css\" href=\"/css/specific/tests/admin_test.min.css?v=5b6afdba\"/>\n";
		$this->assertData($expected, $result);
	}

/**
 * testGetJsBlockEmptyParamValidRequestParamUseUserRolePrefix method
 *
 * @return void
 */
	public function testGetJsBlockEmptyParamValidRequestParamUseUserRolePrefix() {
		$params = [
			'controller' => 'tests',
			'action' => 'admin_test',
			'prefix' => 'admin'];
		$this->_targetObject->request->addParams($params);
		$result = $this->_targetObject->script([], [], false, true);
		$expected = "\n\t<script type=\"text/javascript\" src=\"/js/specific/tests/admin_test.min.js?v=5b6afdba\"></script>\n";
		$this->assertData($expected, $result);
	}
}
