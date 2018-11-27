<?php
/**
 * This file is the trait ExtendTestTrait
 *
 * CakeExtendTest: Extended test tools for CakePHP.
 * @copyright Copyright 2016-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.Test
 */

App::uses('CakeSession', 'Model/Datasource');
App::uses('PhpReader', 'Configure');
App::import(
	'Vendor',
	'CakeExtendTest.PeekAndPoke',
	['file' => 'PeekAndPoke' . DS . 'autoload.php']
);
App::import(
	'Vendor',
	'CakeExtendTest.PHPHtmlParser',
	['file' => 'PHPHtmlParser' . DS . 'autoload.php']
);

/**
 * ExtendTestTrait trait
 *
 */
trait ExtendTestTrait {

/**
 * Current language of the user interface.
 *
 * @var array
 */
	protected static $_prevLanguage = null;

/**
 * Current locale.
 *
 * @var array
 */
	protected static $_prevLocale = null;

/**
 * Current session information.
 *
 * @var array
 */
	protected static $_prevSessionInfo = null;

/**
 * Flag of init session information
 *
 * @var bool
 */
	protected static $_initialized = false;

/**
 * Information about the logged in user.
 *
 * @var array
 */
	protected $_userInfo = [];

/**
 * Init session information.
 *
 * @return void
 */
	public function initSession() {
		if (static::$_initialized) {
			return;
		}

		$defaultUserInfo = $this->getDefaultUserInfo();
		if (empty($defaultUserInfo)) {
			return;
		}

		$this->storeSessionInfo();
		$this->clearUserInfo();
		$this->applyUserInfo();
		static::$_initialized = true;
	}

/**
 * Restore current session information.
 *
 * @return void
 */
	public function restoreSession() {
		if (!static::$_initialized) {
			return;
		}

		$this->clearUserInfo();
		$this->restoreSessionInfo();
		static::$_initialized = false;
	}

/**
 * Set default information about the logged in user
 *
 * @param array $userInfo Information for set
 * @return void
 */
	public function setDefaultUserInfo($userInfo = []) {
		$this->_userInfo = (array)$userInfo;
	}

/**
 * Return default information about the logged in user
 *
 * @return array Return default information about the logged in user
 */
	public function getDefaultUserInfo() {
		return $this->_userInfo;
	}

/**
 * Apply session information about the logged in user
 *
 * @param array|null $userInfo Information for set
 * @return bool Success
 */
	public function applyUserInfo($userInfo = null) {
		$userInfoDefault = $this->getDefaultUserInfo();
		if (!empty($userInfoDefault)) {
			if (!empty($userInfo)) {
				$userInfo += $userInfoDefault;
			} else {
				$userInfo = $userInfoDefault;
			}
		}

		if (empty($userInfo)) {
			return false;
		}

		return CakeSession::write('Auth.User', $userInfo);
	}

/**
 * Clear session information about the logged in user
 *
 * @param bool $renew If the session should also be renewed.
 *  Defaults to true.
 * @return void
 */
	public function clearUserInfo($renew = false) {
		CakeSession::clear($renew);
	}

/**
 * Checking for messages set using Flash::set().
 *
 * @param CakeTestCase $testCase CakeTestCase using this function
 * @param string $message Message for checking.
 * @param bool $usePcre Flag of using PCRE for checking message.
 * @param bool $invert Flag of inverting result.
 * @param string $key Session key of Flash message.
 * @return void
 */
	public function checkFlashMessage(CakeTestCase $testCase = null, $message = null, $usePcre = false, $invert = false, $key = null) {
		if (empty($key)) {
			$key = 'flash';
		}

		$path = 'Message.' . $key;
		$result = CakeSession::check($path);
		$assertMethod = 'assertTrue';
		if ($invert && !$result) {
			$assertMethod = 'assertFalse';
		}

		$testCase->$assertMethod($result);
		if ($invert && !$result) {
			return;
		}

		$result = (array)CakeSession::read($path);
		if (isAssoc($result)) {
			$result = [$result];
		}

		$assertMethod = 'assert';
		if ($invert) {
			$assertMethod .= 'Not';
		}
		if ($usePcre) {
			$assertMethod .= 'RegExp';
		} else {
			$assertMethod .= 'Contains';
		}

		foreach ($result as $resultItem) {
			$testCase->assertTrue(isset($resultItem['message']));
			$testCase->$assertMethod($message, $resultItem['message']);
		}
	}

/**
 * Apply configuration for testing.
 *
 * @param string $path Path to testing config folder
 * @return bool Success
 */
	public function applyTestConfig($path = null) {
		$configFile = 'TestConfig';
		if (empty($path) || !file_exists($path . $configFile . '.php')) {
			return false;
		}

		Configure::config('phpcfg', new PhpReader($path));
		$result = Configure::load($configFile, 'phpcfg', false);
		Configure::drop('phpcfg');

		return $result;
	}

/**
 * Store current language of the user interface.
 *
 * @return string Current language of the user interface
 */
	public static function storeUiLang() {
		static::$_prevLanguage = (string)Configure::read('Config.language');

		return static::$_prevLanguage;
	}

/**
 * Restore current language of the user interface.
 *
 * @return bool Success
 */
	public static function restoreUiLang() {
		if (empty(static::$_prevLanguage)) {
			return false;
		}

		return Configure::write('Config.language', static::$_prevLanguage);
	}

/**
 * Set English locale.
 *
 * @return bool Success
 */
	public static function setEngLocale() {
		$engLocale = 'en_US.utf8';
		if (DS === '\\') {
			$engLocale = 'english';
		}

		return (bool)setlocale(LC_ALL, $engLocale);
	}

/**
 * Store current locale.
 *
 * @return string Current locale
 */
	public static function storeLocale() {
		static::$_prevLocale = setlocale(LC_ALL, 0);

		return static::$_prevLocale;
	}

/**
 * Restore current locale.
 *
 * @return bool Success
 */
	public static function restoreLocale() {
		if (empty(static::$_prevLocale)) {
			return false;
		}

		return (bool)setlocale(LC_ALL, static::$_prevLocale);
	}

/**
 * Store current session information.
 *
 * @return string Current session information
 */
	public static function storeSessionInfo() {
		static::$_prevSessionInfo = CakeSession::read();

		return static::$_prevSessionInfo;
	}

/**
 * Restore current session information.
 *
 * @return mixed The value of the session variable, null if session not available,
 *  session not started, or provided name not found in the session, false on failure.
 */
	public static function restoreSessionInfo() {
		if (empty(static::$_prevSessionInfo)) {
			return false;
		}

		return CakeSession::write(static::$_prevSessionInfo);
	}

/**
 * Simulate request by type
 *
 * @param string $type Type of request
 * @return void
 */
	public function setRequestType($type = 'POST') {
		$_SERVER['REQUEST_METHOD'] = mb_strtoupper($type);
	}

/**
 * Reset simulate request
 *
 * @return void
 */
	public function resetRequestType() {
		if (isset($_SERVER['REQUEST_METHOD'])) {
			unset($_SERVER['REQUEST_METHOD']);
		}
	}

/**
 * Simulate JSON request
 *
 * @return void
 */
	public function setJsonRequest() {
		if (isset($_SERVER['HTTP_ACCEPT']) && (stripos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)) {
			return;
		}

		$_SERVER['HTTP_ACCEPT'] = 'application/json' .
			(isset($_SERVER['HTTP_ACCEPT']) && !empty($_SERVER['HTTP_ACCEPT'])? ',' . $_SERVER['HTTP_ACCEPT'] : '');
	}

/**
 * Reset simulate JSON request
 *
 * @return void
 */
	public function resetJsonRequest() {
		if (!isset($_SERVER['HTTP_ACCEPT'])) {
			return;
		}

		$data = explode(',', (string)$_SERVER['HTTP_ACCEPT']);
		$result = [];
		foreach ($data as $dataItem) {
			if (stripos($dataItem, 'application/json') !== false) {
				continue;
			}
			$result[] = $dataItem;
		}

		$_SERVER['HTTP_ACCEPT'] = implode(',', $result);
	}

/**
 * Simulate AJAX request
 *
 * @return void
 */
	public static function setAjaxRequest() {
		$_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
	}

/**
 * Reset simulate AJAX request
 *
 * @return void
 */
	public static function resetAjaxRequest() {
		if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
			unset($_SERVER['HTTP_X_REQUESTED_WITH']);
		}
	}

/**
 * Simulate PJAX request
 *
 * @return void
 */
	public static function setPjaxRequest() {
		$_SERVER['HTTP_X_PJAX'] = true;
	}

/**
 * Reset simulate PJAX request
 *
 * @return void
 */
	public static function resetPjaxRequest() {
		if (isset($_SERVER['HTTP_X_PJAX'])) {
			unset($_SERVER['HTTP_X_PJAX']);
		}
	}

/**
 * Set data for GET request
 *
 * @param array $data Data for set
 * @return void
 */
	public function setGetData($data = null) {
		if (empty($data)) {
			return;
		}

		$_GET = (array)$data;
	}

/**
 * Reset data from GET request
 *
 * @return void
 */
	public function resetGetData() {
		$_GET = [];
	}

/**
 * Set data from POST request
 *
 * @param array $data Data for set
 * @return void
 */
	public function setPostData($data = null) {
		if (empty($data)) {
			return;
		}

		$_POST = (array)$data;
	}

/**
 * Reset data from POST request
 *
 * @return void
 */
	public function resetPostData() {
		$_POST = [];
	}

/**
 * Reports an error identified by $message if the two variables $expected and $actual do
 *  not have the same type and value.
 *
 * @param CakeTestCase $testCase CakeTestCase using this function
 * @param mixed $expected Expected data
 * @param mixed $result Result data
 * @param string $message Message for display
 * @return void
 */
	public function assertData(CakeTestCase $testCase = null, $expected = null, $result = null, $message = '') {
		if (empty($message)) {
			$message = __d('cake_extend_test', 'Result is not equal expected');
		}
		if (is_array($result) && !empty($result) &&
			is_array($expected) && !empty($expected)) {
			$testCase->assertSame(Hash::diff($expected, $result), Hash::diff($result, $expected), $message);
			$testCase->assertTrue($expected === $result, $message);
		} else {
			$testCase->assertSame($expected, $result, $message);
		}
	}

/**
 * Return proxy object for accessing non-public
 *  attributes and methods of an object.
 *
 * @param object $object Target object
 * @return object Return proxy object
 */
	public function createProxyObject($object = null) {
		$proxy = new SebastianBergmann\PeekAndPoke\Proxy($object);

		return $proxy;
	}

/**
 * Override internal function:
 *  - `is_uploaded_file()`
 *  - `move_uploaded_file()`
 *
 * @param CakeTestCase $testCase CakeTestCase using this function
 * @return void
 */
	public function prepareUploadTest(CakeTestCase $testCase = null) {
		$message = '';
		if (!extension_loaded('runkit')) {
			$message = __d('cake_extend_test', 'Extension "Runkit" is not loaded');
		} elseif (!ini_get('runkit.internal_override')) {
			$message = __d('cake_extend_test', 'Option "runkit.internal_override" is False');
		}

		$testCase->skipIf(!empty($message), $message);
		$result = runkit_function_redefine('is_uploaded_file', '$filename', 'return file_exists($filename);');
		$testCase->assertTrue($result);

		$result = runkit_function_redefine('move_uploaded_file', '$filename,$destination', 'return copy($filename, $destination);');
		$testCase->assertTrue($result);
	}

/**
 * Return number of items from HTML by CSS selector
 *
 * @param string $content HTML for parsing
 * @param string $selector CSS selector for parsing
 * @return int|bool Return number of items or False on failure.
 */
	public function getNumberItemsByCssSelector($content = null, $selector = null) {
		if (empty($content) || empty($selector) ||
			!is_string($content) || !is_string($selector)) {
			return false;
		}

		$dom = new PHPHtmlParser\Dom();
		$dom->load($content);
		$items = $dom->find($selector);
		$result = count($items);

		return $result;
	}
}
