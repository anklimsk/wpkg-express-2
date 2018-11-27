<?php
App::uses('AppControllerTestCase', 'CakeInstaller.Test');
App::uses('CheckController', 'CakeInstaller.Controller');

/**
 * InstallerController Test Case
 */
class CheckControllerTest extends AppControllerTestCase {

/**
 * Target Controller name
 *
 * @var string
 */
	public $targetController = 'CakeInstaller.Check';

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = [
		'core.cake_session',
	];

/**
 * testIndex method
 *
 * @return void
 */
	public function testIndexIsAppInstalledFalse() {
		$this->_generateMockedController(false);
		$opt = [
			'method' => 'GET',
			'return' => 'vars',
		];
		$result = $this->testAction('/cake_installer/check/index', $opt);
		$expected = [
			'isAppInstalled' => false,
			'isAppReadyInstall' => true,
			'phpVesion' => true,
			'phpModules' => [
				'PDO' => 2
			],
			'filesWritable' => [
				TMP => true
			],
			'connectDB' => [
				'default' => true,
				'test' => true
			]
		];
		$this->assertData($expected, $result);
	}

/**
 * testIndex method
 *
 * @return void
 */
	public function testIndexIsAppInstalledTrue() {
		$this->_generateMockedController(true);
		$opt = [
			'method' => 'GET',
		];
		$this->testAction('/cake_installer/check/index', $opt);
		$this->checkRedirect('/');
	}

/**
 * Generate mocked CheckController.
 *
 * @param bool $isAppInstalled State of flag application is installed.
 * @return bool Success
 */
	protected function _generateMockedController($isAppInstalled = false) {
		$mocks = [
			'models' => [
				'CakeInstaller.InstallerCheck' => [
					'checkPhpVersion',
					'checkPhpExtensions',
					'isAppReadyToInstall',
					'checkFilesWritable',
					'checkConnectDb',
				]
			],
			'components' => [
				'Auth',
				'Security',
				'CakeInstaller.Installer' => ['isAppInstalled'],
			],
		];
		if (!$this->generateMockedController($mocks)) {
			return false;
		}

		$this->Controller->InstallerCheck->expects($this->any())
			->method('checkPhpVersion')
			->will($this->returnValue(true));
		$this->Controller->InstallerCheck->expects($this->any())
			->method('checkPhpExtensions')
			->will($this->returnValue([
				'PDO' => 2
			]));
		$this->Controller->InstallerCheck->expects($this->any())
			->method('checkFilesWritable')
			->will($this->returnValue([
				TMP => true
			]));
		$this->Controller->InstallerCheck->expects($this->any())
			->method('isAppReadyToInstall')
			->will($this->returnValue(true));
		$this->Controller->InstallerCheck->expects($this->any())
			->method('checkConnectDb')
			->will($this->returnValue([
				'default' => true,
				'test' => true
			]));

		$this->Controller->Installer->expects($this->any())
			->method('isAppInstalled')
			->will($this->returnValue($isAppInstalled));

		return true;
	}
}
