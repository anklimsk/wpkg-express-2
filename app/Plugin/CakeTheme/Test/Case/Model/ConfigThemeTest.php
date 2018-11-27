<?php
App::uses('AppCakeTestCase', 'CakeTheme.Test');
App::uses('ConfigTheme', 'CakeTheme.Model');

/**
 * ConfigTheme Test Case
 */
class ConfigThemeTest extends AppCakeTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->_targetObject = ClassRegistry::init('CakeTheme.ConfigTheme');
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
 * testGetConfig method
 *
 * @return void
 */
	public function testGetConfig() {
		$params = [
			[
				null, // $key
			],
			[
				'badKey', // $key
			],
			[
				'AdditionalFiles.css', // $key
			],
		];
		$expected = [
			[
				'AdditionalFiles' => [
					'css' => [
						'extendCssFile',
					],
					'js' => [
						'someJsFile',
					],
				],
				'AjaxFlash' => [
					'flashKeys' => [
						'flash',
						'auth',
						'test',
					],
					'timeOut' => 15,
					'delayDeleteFlash' => 5,
					'globalAjaxComplete' => false,
					'theme' => 'mint',
					'layout' => 'top',
					'open' => 'animated flipInX',
					'close' => 'animated flipOutX',
				],
				'TourApp' => [
					'Steps' => [
						[
							'path' => '/',
							'element' => 'ul.nav',
							'title' => 'Title',
							'content' => 'Some text.'
						],
						[
							'element' => '#content',
							'title' => 'Content area',
							'content' => 'Content'
						],
					],
				],
				'ViewExtension' => [
					// Autocomplete limit for filter of table
					'AutocompleteLimit' => 3,
					// Server-Sent Events
					'SSE' => [
						// Default text for Noty message
						'text' => 'Waiting to run task',
						// Labels for data
						'label' => [
							// Task name
							'task' => 'Task',
							// Completed percentage
							'completed' => 'completed'
						],
						// The number of repeated attempts to start pending tasks
						'retries' => 5,
						// Delay to delete flash messages
						'delayDeleteTask' => 5
					],
					// ViewExtension Helper
					'Helper' => [
						// Default FontAwesome icon prefix
						'defaultIconPrefix' => 'fas',
						// Default FontAwesome icon size
						'defaultIconSize' => 'fa-lg',
						// Default Bootstrap button prefix
						'defaultBtnPrefix' => 'btn',
						// Default Bootstrap button size
						'defaultBtnSize' => 'btn-xs',
					],
					// PHP Unoconv
					'Unoconv' => [
						// The timeout for the underlying process.
						'timeout' => 30,
						// The path (or an array of paths) for a custom binary.
						'binaries' => ''
					]
				],
			],
			null,
			['extendCssFile'],
		];

		$this->runClassMethodGroup('getConfig', $params, $expected);
	}

/**
 * testGetListCssFiles method
 *
 * @return void
 */
	public function testGetListCssFiles() {
		$result = $this->_targetObject->getListCssFiles();
		$expected = [
			'extendCssFile'
		];
		$this->assertData($expected, $result);
	}

/**
 * testGetListJsFiles method
 *
 * @return void
 */
	public function testGetListJsFiles() {
		$result = $this->_targetObject->getListJsFiles();
		$expected = [
			'someJsFile'
		];
		$this->assertData($expected, $result);
	}

/**
 * testGetConfigAjaxFlash method
 *
 * @return void
 */
	public function testGetConfigAjaxFlash() {
		$result = $this->_targetObject->getConfigAjaxFlash();
		$expected = [
			'flashKeys' => [
				'flash',
				'auth',
				'test'
			],
			'timeOut' => 15,
			'delayDeleteFlash' => 5,
			'globalAjaxComplete' => false,
			'theme' => 'mint',
			'layout' => 'top',
			'open' => 'animated flipInX',
			'close' => 'animated flipOutX',
		];
		$this->assertData($expected, $result);
	}

/**
 * testGetStepsConfigTourApp method
 *
 * @return void
 */
	public function testGetStepsConfigTourApp() {
		$result = $this->_targetObject->getStepsConfigTourApp();
		$expected = [
			[
				'path' => '/',
				'element' => 'ul.nav',
				'title' => 'Title',
				'content' => 'Some text.'
			],
			[
				'element' => '#content',
				'title' => 'Content area',
				'content' => 'Content'
			],
		];
		$this->assertData($expected, $result);
	}

/**
 * testGetUnoconvConfig method
 *
 * @return void
 */
	public function testGetUnoconvConfig() {
		$result = $this->_targetObject->getUnoconvConfig();
		$expected = [
			'timeout' => 30,
			'binaries' => ''
		];
		$this->assertData($expected, $result);
	}

/**
 * testGetSseConfig method
 *
 * @return void
 */
	public function testGetSseConfig() {
		$result = $this->_targetObject->getSseConfig();
		$expected = [
			'text' => 'Waiting to run task',
			'label' => [
				'task' => 'Task',
				'completed' => 'completed'
			],
			'retries' => 5,
			'delayDeleteTask' => 5
		];
		$this->assertData($expected, $result);
	}

/**
 * testGetAutocompleteLimitConfig method
 *
 * @return void
 */
	public function testGetAutocompleteLimitConfig() {
		$result = $this->_targetObject->getAutocompleteLimitConfig();
		$expected = 3;
		$this->assertData($expected, $result);
	}
}
