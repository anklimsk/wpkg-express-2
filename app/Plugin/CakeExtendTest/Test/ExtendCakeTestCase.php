<?php
/**
 * This file is the extended class CakeTestCase
 *
 * CakeExtendTest: Extended test tools for CakePHP.
 * @copyright Copyright 2016-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.Test
 */

App::uses('Hash', 'Utility');
App::uses('Debugger', 'Utility');
App::uses('ExtendTestTrait', 'CakeExtendTest.Utility');

/**
 * Extended CakeTestCase class
 *
 */
class ExtendCakeTestCase extends CakeTestCase {

	use ExtendTestTrait {
		checkFlashMessage as traitCheckFlashMessage;
		assertData as traitAssertData;
		prepareUploadTest as traitPrepareUploadTest;
	}

/**
 * Object for testing
 *
 * @var object
 */
	protected $_targetObject = null;

/**
 * Setup the test case, backup the static object values so they can be restored.
 * Specifically backs up the contents of Configure and paths in App if they have
 * not already been backed up.
 *
 * Actions:
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

		$this->storeUIlang();
		$this->initSession();
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
		$this->restoreUIlang();
		$this->restoreSession();
		set_time_limit(0);

		unset($this->_targetObject);
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
 * Check expected key is assert method
 *
 * @param mixed $expectedKey Key for checking
 * @return bool Return True, if key is assert method.
 *  False otherwise.
 */
	protected function _checkExpectedKey($expectedKey = null) {
		if (!is_string($expectedKey)) {
			return false;
		}

		if ((stripos($expectedKey, 'assert') === 0) && method_exists($this, $expectedKey)) {
			return true;
		}

		return false;
	}

/**
 * Parse expected for test as assert method and value.
 *
 * @param array &$assertActions Array of assert methods and value
 *  in format:
 *   - key - assert method name;
 *   - value - value for assert method.
 * @param int &$deepLimit Current value of deep on recursion. Used for break recursion.
 * @param mixed $expected Expected value
 * @return void
 */
	protected function _parseExpected(array &$assertActions, &$deepLimit, $expected = null) {
		if ($deepLimit < 0) {
			return;
		}

		if (!is_array($expected)) {
			$assertMethod = 'assertData';
			$expectedVal = $expected;
			$assertActions[][$assertMethod] = $expectedVal;
		} else {
			$assertMethod = 'assertData';
			$expectedVal = $expected;
			$useDefaultAssertMethod = true;
			if (count($expected) == 1) {
				foreach ($expected as $expectedKey => $expectedItem) {
					if (!is_string($expectedKey)) {
						continue;
					}

					if ($this->_checkExpectedKey($expectedKey)) {
						$assertMethod = $expectedKey;
						$assertActions[][$assertMethod] = $expectedItem;
						$useDefaultAssertMethod = false;
					} elseif ((stripos($expectedKey, 'expecteds') === 0) && is_array($expectedItem)) {
						$deepLimit--;
						foreach ($expectedItem as $expectedItemKey => $expectedItemVal) {
							if ($this->_checkExpectedKey($expectedItemKey)) {
								$expectedData = [$expectedItemKey => $expectedItemVal];
							} else {
								$expectedData = $expectedItemVal;
							}
							$this->_parseExpected($assertActions, $deepLimit, $expectedData);
							$useDefaultAssertMethod = false;
						}
					}
				}
			}
			if ($useDefaultAssertMethod) {
				$assertActions[][$assertMethod] = $expectedVal;
			}
		}
	}

/**
 * Return target object.
 *
 * @return object Return target object for testing
 */
	protected function _getTargetObject() {
		return $this->_targetObject;
	}

/**
 * Check the target method is a class method or function.
 *
 * @param object $targetObject Target testing object
 * @param string $method Method name for check
 * @throws InvalidArgumentException If target object is not a object.
 * @throws InvalidArgumentException Method does not exist in target object.
 * @return bool Return True, if target method is a class method.
 *  False, otherwise.
 */
	protected function _checkTargetIsClassMethod($targetObject = null, $method = null) {
		if (($targetObject === null) && !empty($method) && function_exists($method)) {
			$targetIsClass = false;
		} else {
			$targetIsClass = true;
			if (!is_object($targetObject)) {
				throw new InvalidArgumentException(__d('cake_extend_test', 'Target object is not object'));
			}

			if (!method_exists($targetObject, $method)) {
				throw new InvalidArgumentException(__d('cake_extend_test', 'Method %s does not exist in target object', $method));
			}
		}

		return $targetIsClass;
	}

/**
 * Run class method for group of params.
 *  Extended analog Data Providers.
 *
 * @param string $method Method name for run
 * @param array $params Params for method of class
 * @param array $expected Expected data.
 *  Extend format as array:
 *   - key - assert method name;
 *   - value - value for assert method.
 *  Single assert: `$expected = array('assertRegExp' => '/<p>Test message as HTML<\/p>/')` or
 *   simple $expected = array('/<p>Test message as HTML<\/p>') for test use assertData method.
 *  For multiple assert method in one result of test use key `expecteds`, e.g.:
 *   `$expected = array(
 *	  'expecteds' => array(
 *		  'assertRegExp' => '/<p>Test message as HTML<\/p>/',
 *		  'assertSame' => '<p>This email was sent using the CakePHP TEST - HTML<\/p>',
 *		  '<p>This email was sent using the CakePHP TEST - HTML<\/p> // use assertData method
 *	  )
 *  )`
 * @throws InvalidArgumentException If invalid $expected argument.
 * @return void
 */
	public function runClassMethodGroup($method = '', $params = null, $expected = null) {
		$targetObject = $this->_getTargetObject();
		$targetIsClass = $this->_checkTargetIsClassMethod($targetObject, $method);
		$method = (string)$method;
		$this->skipIf(empty($params) || !is_array($params), __d('cake_extend_test', 'Invalid params for method %s', $method));
		$this->skipIf(empty($expected) || !is_array($expected), __d('cake_extend_test', 'Invalid expectation for the method %s', $method));
		$this->skipIf(array_keys($params) !== array_keys($expected), __d('cake_extend_test', 'Keys for params and expectations is not equals'));
		$countStep = count($params);
		$targetType = ($targetIsClass ? __d('cake_extend_test', 'method') : __d('cake_extend_test', 'function'));
		foreach ($params as $i => $param) {
			if (!is_array($param)) {
				$param = [$param];
			}
			$methodName = $this->_getClassInfo($targetObject, $method, $param);
			$message = __d('cake_extend_test', "Testing %s %s.\n\nStep %d of %d.", $targetType, $methodName, $i + 1, $countStep);
			if ($targetIsClass) {
				$methodCall = [$targetObject, $method];
			} else {
				$methodCall = $method;
			}
			$result = call_user_func_array($methodCall, $param);
			$assertActions = [];
			$deepLimit = 2;
			$this->_parseExpected($assertActions, $deepLimit, $expected[$i]);
			if (empty($assertActions)) {
				throw new InvalidArgumentException(__d('cake_extend_test', 'Error parse expected'));
			}
			foreach ($assertActions as $assertActionItem) {
				foreach ($assertActionItem as $assertMethod => $expectedVal) {
					if (!method_exists($this, $assertMethod)) {
						continue;
					}

					$this->$assertMethod($expectedVal, $result, $message);
				}
			}
		}
	}

/**
 * Get information about class menthod.
 *
 * @param object $targetObject Target testing object
 * @param string $method Method name
 * @param mixed|array $params Params for method
 * @return string Information about class menthod include params
 */
	protected function _getClassInfo($targetObject = null, $method = null, $params = null) {
		$targetIsClass = $this->_checkTargetIsClassMethod($targetObject, $method);
		if ($targetIsClass) {
			$reflectClass = new ReflectionClass($targetObject);
			$reflectMethod = new ReflectionMethod($targetObject, $method);
			$result = $reflectClass->getName() . '::' . $reflectMethod->getName() . '(';
		} else {
			$reflectClass = null;
			$reflectMethod = new ReflectionFunction($method);
			$result = $reflectMethod->getName() . '(';
		}
		$reflectParams = $reflectMethod->getParameters();
		if (!is_array($params)) {
			$params = [$params];
		}
		$args = [];
		$vals = [];
		foreach ($reflectParams as $reflectParam) {
			$pos = $reflectParam->getPosition();
			$defVal = null;
			$isDefValAvail = $reflectParam->isDefaultValueAvailable();
			if ($isDefValAvail) {
				$defVal = $reflectParam->getDefaultValue();
			}
			if (!empty($params) && isset($params[$pos])) {
				$val = $params[$pos];
			} elseif ($isDefValAvail) {
				$val = $defVal;
			} else {
				$val = null;
			}
			if ((!is_string($val) || !is_numeric($val))) {
				$val = Debugger::exportVar($val, 3);
			}
			$varName = '$' . $reflectParam->getName();
			if ($isDefValAvail && !is_string($defVal) && !is_numeric($defVal)) {
				$defVal = Debugger::exportVar($defVal, 3);
			}
			if ($isDefValAvail) {
				$args[] = $varName . ' = ' . (string)$defVal;
			} else {
				$args[] = $varName;
			}
			$vals[] = $varName . ' = ' . (string)$val;
		}
		$result .= implode(', ', $args) . '),' . "\r\n";
		$result .= __d('cake_extend_test', 'where') . ":\r\n";
		$result .= implode(",\r\n", $vals);

		return $result;
	}
}
