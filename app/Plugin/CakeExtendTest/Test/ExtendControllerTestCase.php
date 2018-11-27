<?php
/**
 * This file is the extended class ControllerTestCase
 *
 * CakeExtendTest: Extended test tools for CakePHP.
 * @copyright Copyright 2016-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.Test
 */

App::uses('Router', 'Routing');
App::uses('Hash', 'Utility');
App::uses('ExtendTestTrait', 'CakeExtendTest.Utility');

/**
 * Extended ControllerTestCase class
 *
 */
class ExtendControllerTestCase extends ControllerTestCase {

	use ExtendTestTrait {
		checkFlashMessage as traitCheckFlashMessage;
		assertData as traitAssertData;
		prepareUploadTest as traitPrepareUploadTest;
	}

/**
 * Controller name for testing
 *
 * @var string
 */
	public $targetController = '';

/**
 * Instance of mocked Controller.
 *
 * @var Controller
 */
	public $Controller = null;

/**
 * Setup the test case, backup the static object values so they can be restored.
 * Specifically backs up the contents of Configure and paths in App if they have
 * not already been backed up.
 *
 * - Write application language setting;
 * - Store session.
 *
 * @return void
 */
	public function setUp() {
		Configure::write('debug', 1);
		Configure::write('Cache.disable', true);
		Configure::write('App.fullBaseUrl', 'http://localhost');
		if (PHP_SAPI === 'cli') {
			Configure::write('App.base', '');
		}
		parent::setUp();

		$this->Controller = null;
		$this->storeUIlang();
		$this->storeSessionInfo();
		$this->clearUserInfo();
		Configure::write('Config.language', 'eng');
		set_time_limit(0);
	}

/**
 * Teardown any static object changes and restore them.
 *
 * Actions:
 * - Restore application language setting;
 * - Restore session.
 *
 * @return void
 */
	public function tearDown() {
		$this->clearUserInfo();
		$this->restoreUIlang();
		$this->restoreSessionInfo();
		set_time_limit(0);

		unset($this->Controller);
		parent::tearDown();
	}

/**
 * Checking for messages set using Flash::set().
 *
 * @param string $message Message for checking.
 * @param bool $usePcre Flag of using PCRE for checking message.
 * @param bool $invert Flag of inverting result.
 * @param string $key Session key of Flash message.
 * @return void
 */
	public function checkFlashMessage($message = null, $usePcre = false, $invert = false, $key = null) {
		$this->traitCheckFlashMessage($this, $message, $usePcre, $invert, $key);
	}

/**
 * Reports an error identified by $message if the two variables $expected and $actual do
 *  not have the same type and value.
 *
 * @param mixed $expected Expected data
 * @param mixed $result Result data
 * @param string $message Message for display
 * @return void
 */
	public function assertData($expected = null, $result = null, $message = '') {
		$this->traitAssertData($this, $expected, $result, $message);
	}

/**
 * Override internal function:
 *  - `is_uploaded_file()`
 *  - `move_uploaded_file()`
 *
 * @return void
 */
	public function prepareUploadTest() {
		$this->traitPrepareUploadTest($this);
	}

/**
 * Generate mocked Controller.
 *
 * @param array $mocks List of classes and methods to mock.
 * @return bool Success
 */
	public function generateMockedController($mocks = []) {
		if (empty($this->targetController)) {
			return false;
		}

		$this->Controller = $this->generate($this->targetController, $mocks);

		return true;
	}

/**
 * Check redirect to the URL.
 *
 * @param string|array|bool $url Cake-relative URL, like "/products/edit/92" or "/presidents/elect/4"
 *   or an array specifying any of the following: 'controller', 'action',
 *   and/or 'plugin', in addition to named arguments (keyed array elements),
 *   and standard URL arguments (indexed array elements).
 *   If True, checking for redirect is exists. If False, checking for redirect is not exists.
 * @param bool|array $full If (bool) true, the full base URL will be prepended to the result.
 *   If an array accepts the following keys
 *	- escape - used when making URLs embedded in html escapes query string '&'
 *	- full - if true the full base URL will be prepended.
 * @return void
 */
	public function checkRedirect($url = null, $full = true) {
		$this->skipIf(($url !== false) && empty($url), __d('cake_extend_test', 'Empty URL for checking redirect'));
		$result = (string)Hash::get((array)$this->headers, 'Location');
		$expected = null;
		if ($url === true) {
			if (!empty($result)) {
				$expected = $result;
			}
		} elseif ($url === false) {
			$expected = '';
		} else {
			$expected = Router::url($url, $full);
		}
		$this->assertEquals($expected, $result);
	}

/**
 * Checking set headers to download the file.
 *
 * @param string $fileName File name for download as part of
 *  regular expression pattern
 * @param int $fileSize Size of file for download
 * @return void
 */
	public function checkDownloadFile($fileName = null, $fileSize = 0) {
		$fileSize = (int)$fileSize;
		$this->skipIf(empty($fileName), __d('cake_extend_test', 'Empty file name for checking download'));
		$this->skipIf($fileSize < 0, __d('cake_extend_test', 'Invalid file size for checking download'));

		$result = (string)Hash::get($this->headers, 'Content-Disposition');
		$expected = '/attachment; filename="' . $fileName . '"/i';
		$this->assertRegExp($expected, $result);

		$result = (string)Hash::get($this->headers, 'Content-Length');
		$expected = (string)$fileSize;
		$this->assertEquals($expected, $result);
	}

/**
 * Checking user authorization through a method Controller::isAuthorized().
 *
 * @param bool $invert Flag of inverting result.
 * @return void
 */
	public function checkIsNotAuthorized($invert = false) {
		$this->skipIf(!is_object($this->Controller), __d('cake_extend_test', 'Invalid object Controller for checking "Is Authorized"'));
		$this->skipIf(!is_object($this->Controller->Auth), __d('cake_extend_test', 'Invalid object Controller component Auth for checking "Is Authorized"'));

		$this->traitCheckFlashMessage($this, $this->Controller->Auth->authError, false, $invert, $this->Controller->Auth->flash['key']);
	}
}
