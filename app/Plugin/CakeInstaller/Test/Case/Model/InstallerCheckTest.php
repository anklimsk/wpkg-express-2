<?php
App::uses('AppCakeTestCase', 'CakeInstaller.Test');
App::uses('InstallerCheck', 'CakeInstaller.Model');

/**
 * InstallerCheck Test Case
 */
class InstallerCheckTest extends AppCakeTestCase {

/**
 * Path to marker file for checking application is installed.
 *
 * @var string
 */
	protected $_markerFileInstalled = TMP . 'tests' . DS . 'test_installed.txt';

/**
 * Path to marker file for checking if need restart installation process.
 *
 * @var string
 */
	protected $_markerFileRestart = TMP . 'tests' . DS . 'test_restart.txt';

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = [];

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		if (file_exists($this->_markerFileInstalled)) {
			unlink($this->_markerFileInstalled);
		}
		if (file_exists($this->_markerFileRestart)) {
			unlink($this->_markerFileRestart);
		}

		$this->_targetObject = $this->getMock('InstallerCheck', ['_getFilesForCheckingWritable', 'checkConnectDb']);
		$this->_targetObject->expects($this->any())
			->method('_getFilesForCheckingWritable')
			->will($this->returnValue([TMP]));
		$this->_targetObject->expects($this->any())
			->method('checkConnectDb')
			->will($this->returnCallback(function ($path = null, $returnBool = false) {
				if ($returnBool) {
					$result = true;
				} else {
					$result = [
						'default' => true,
					];
				}

				return $result;
			}));
		$this->_targetObject->markerFileInstalled = $this->_markerFileInstalled;
		$this->_targetObject->markerFileRestart = $this->_markerFileRestart;
		Configure::delete('CakeInstaller.symlinksCreationList');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		if (file_exists($this->_markerFileInstalled)) {
			unlink($this->_markerFileInstalled);
		}
		if (file_exists($this->_markerFileRestart)) {
			unlink($this->_markerFileRestart);
		}

		parent::tearDown();
	}

/**
 * testGetPathMarkerFileIsInstalled method
 *
 * @return void
 */
	public function testGetPathMarkerFileIsInstalled() {
		$result = $this->_targetObject->getPathMarkerFileIsInstalled();
		$expected = $this->_markerFileInstalled;
		$this->assertData($expected, $result);
	}

/**
 * testGetPathMarkerFileNeedRestart method
 *
 * @return void
 */
	public function testGetPathMarkerFileNeedRestart() {
		$result = $this->_targetObject->getPathMarkerFileNeedRestart();
		$expected = $this->_markerFileRestart;
		$this->assertData($expected, $result);
	}

/**
 * testIsAppInstalledEmptyListInstallerTasks method
 *
 * @return void
 */
	public function testIsAppInstalledEmptyListInstallerTasks() {
		Configure::write('CakeInstaller.installTasks', []);
		$result = $this->_targetObject->isAppInstalled(null, false);
		$this->assertFalse($result);
	}

/**
 * testIsAppInstalled method
 *
 * @return void
 */
	public function testIsAppInstalled() {
		$params = [
			[
				null, // $configKey
				false, // $createMarkerFile
			],
			[
				'test', // $configKey
				false, // $createMarkerFile
			],
		];
		$expected = [
			true,
			false
		];

		$this->runClassMethodGroup('isAppInstalled', $params, $expected);
	}

/**
 * testIsAppReadyToInstallSuccess method
 *
 * @return void
 */
	public function testIsAppReadyToInstallSuccess() {
		$result = $this->_targetObject->isAppReadyToInstall();
		$this->assertTrue($result);
	}

/**
 * testIsAppReadyToInstallUnsuccess method
 *
 * @return void
 */
	public function testIsAppReadyToInstallUnsuccess() {
		Configure::write('CakeInstaller.PHPextensions', [
			[
				'test',
				true
			]
		]);
		$result = $this->_targetObject->isAppReadyToInstall();
		$this->assertFalse($result);
	}

/**
 * testIsAppInstalledConfigKey method
 *
 * @return void
 */
	public function testIsAppInstalledConfigKey() {
		Configure::write('test.data', 'tst');
		$result = $this->_targetObject->isAppInstalled('test', false);
		$this->assertTrue($result);
	}

/**
 * testIsAppInstalledCreateFile method
 *
 * @return void
 */
	public function testIsAppInstalledCreateFile() {
		$result = $this->_targetObject->isAppInstalled(null, true);
		$this->assertTrue($result);

		if ($result) {
			$this->assertTrue(file_exists($this->_markerFileInstalled));
		}
	}

/**
 * testCheckPhpVersionNull method
 *
 * @return void
 */
	public function testCheckPhpVersionNull() {
		Configure::delete('CakeInstaller.PHPversion');
		$result = $this->_targetObject->checkPhpVersion();
		$this->assertNull($result);
	}

/**
 * testCheckPhpVersionFalse method
 *
 * @return void
 */
	public function testCheckPhpVersionFalse() {
		$PHPversion = [
			PHP_VERSION,
			'>'
		];
		Configure::write('CakeInstaller.PHPversion', $PHPversion);
		$result = $this->_targetObject->checkPhpVersion();
		$this->assertFalse($result);
	}

/**
 * testCheckPhpVersionFalseBadVer method
 *
 * @return void
 */
	public function testCheckPhpVersionFalseBadVer() {
		$PHPversion = [
			'test'
		];
		Configure::write('CakeInstaller.PHPversion', $PHPversion);
		$result = $this->_targetObject->checkPhpVersion();
		$this->assertFalse($result);
	}

/**
 * testCheckPhpVersion method
 *
 * @return void
 */
	public function testCheckPhpVersionTrue() {
		$PHPversion = [
			PHP_VERSION,
		];
		Configure::write('CakeInstaller.PHPversion', $PHPversion);
		$result = $this->_targetObject->checkPhpVersion();
		$this->assertTrue($result);
	}

/**
 * testCheckPhpVersionTrueBadCfg method
 *
 * @return void
 */
	public function testCheckPhpVersionTrueBadCfg() {
		$PHPversion = [
			[
				PHP_VERSION,
				'=='
			],
			'test'
		];
		Configure::write('CakeInstaller.PHPversion', $PHPversion);
		$result = $this->_targetObject->checkPhpVersion();
		$this->assertTrue($result);
	}

/**
 * testCheckPhpExtensionsNull method
 *
 * @return void
 */
	public function testCheckPhpExtensionsNull() {
		Configure::delete('CakeInstaller.PHPextensions');
		$result = $this->_targetObject->checkPhpExtensions();
		$this->assertNull($result);
	}

/**
 * testCheckPhpExtensionsFalse method
 *
 * @return void
 */
	public function testCheckPhpExtensionsFalse() {
		Configure::write('CakeInstaller.PHPextensions', [
			[
				'test',
				true
			]
		]);
		$result = $this->_targetObject->checkPhpExtensions(true);
		$this->assertFalse($result);

		$result = $this->_targetObject->checkPhpExtensions(false);
		$expected = [
			'test' => 0
		];
		$this->assertData($expected, $result);
	}

/**
 * testCheckPhpExtensionsTrueCritical method
 *
 * @return void
 */
	public function testCheckPhpExtensionsTrueCritical() {
		Configure::write('CakeInstaller.PHPextensions', [
			[
				'PDO',
				true
			]
		]);
		$result = $this->_targetObject->checkPhpExtensions(true);
		$this->assertTrue($result);

		$result = $this->_targetObject->checkPhpExtensions(false);
		$expected = [
			'PDO' => 2
		];
		$this->assertData($expected, $result);
	}

/**
 * testCheckPhpExtensionsTrueCriticalBadCfg method
 *
 * @return void
 */
	public function testCheckPhpExtensionsTrueCriticalBadCfg() {
		Configure::write('CakeInstaller.PHPextensions', [
			[
				'PDO',
				true
			],
			'Test'
		]);
		$result = $this->_targetObject->checkPhpExtensions(true);
		$this->assertTrue($result);

		$result = $this->_targetObject->checkPhpExtensions(false);
		$expected = [
			'PDO' => 2
		];
		$this->assertData($expected, $result);
	}

/**
 * testCheckPhpExtensionsTrueNotCritical method
 *
 * @return void
 */
	public function testCheckPhpExtensionsTrueNotCritical() {
		Configure::write('CakeInstaller.PHPextensions', [
			[
				'test',
				false
			]
		]);
		$result = $this->_targetObject->checkPhpExtensions(true);
		$this->assertTrue($result);

		$result = $this->_targetObject->checkPhpExtensions(false);
		$expected = [
			'test' => 1
		];
		$this->assertData($expected, $result);
	}

/**
 * testCheckFilesWritable method
 *
 * @return void
 */
	public function testCheckFilesWritable() {
		$result = $this->_targetObject->checkFilesWritable(true);
		$this->assertTrue($result);
	}

/**
 * testIsNeedRestart method
 *
 * @return void
 */
	public function testIsNeedRestart() {
		$this->assertTrue(file_put_contents($this->_markerFileRestart, 'Some data...') !== false);
		$result = $this->_targetObject->isNeedRestart();
		$this->assertTrue($result);
	}

/**
 * testSetNeedRestart method
 *
 * @return void
 */
	public function testSetNeedRestart() {
		$result = $this->_targetObject->setNeedRestart();
		$this->assertTrue($result);

		if ($result) {
			$this->assertTrue(file_exists($this->_markerFileRestart));
		}
	}

/**
 * testRemoveMarkerFileIsInstalled method
 *
 * @return void
 */
	public function testRemoveMarkerFileIsInstalled() {
		$this->assertTrue(file_put_contents($this->_markerFileInstalled, 'Some data...') !== false);
		$this->assertTrue(file_exists($this->_markerFileInstalled));
		$result = $this->_targetObject->removeMarkerFileIsInstalled();
		$this->assertTrue($result);
		$this->assertFalse(file_exists($this->_markerFileInstalled));
	}

/**
 * testRemoveMarkerFileNeedRestart method
 *
 * @return void
 */
	public function testRemoveMarkerFileNeedRestart() {
		$this->assertTrue(file_put_contents($this->_markerFileRestart, 'Some data...') !== false);
		$this->assertTrue(file_exists($this->_markerFileRestart));
		$result = $this->_targetObject->removeMarkerFileNeedRestart();
		$this->assertTrue($result);
		$this->assertFalse(file_exists($this->_markerFileRestart));
	}
}
