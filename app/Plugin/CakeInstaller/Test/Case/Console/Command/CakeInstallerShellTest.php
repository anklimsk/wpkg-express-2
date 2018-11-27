<?php
App::uses('AppCakeTestCase', 'CakeInstaller.Test');
App::uses('ConsoleOutput', 'Console');
App::uses('ConsoleInput', 'Console');
App::uses('ShellDispatcher', 'Console');
App::uses('Shell', 'Console');
App::uses('CakeInstallerShell', 'CakeInstaller.Console/Command');
App::uses('Folder', 'Utility');
App::uses('File', 'Utility');

/**
 * CakeInstallerShell Test Case
 *
 */
class CakeInstallerShellTest extends AppCakeTestCase {

/**
 * Path to test directory
 *
 * @var string
 */
	protected $_testDir = TMP . 'tests' . DS;

/**
 * Path to marker file for checking if need restart installation process.
 *
 * @var string
 */
	protected $_markerFileRestart = TMP . 'tests' . DS . 'test_restart.txt';

/**
 * Path to marker file for checking if need restart installation process.
 *
 * @var string
 */
	protected $_markerFileInstalled = TMP . 'tests' . DS . 'test_installed.txt';

/**
 * setup test
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$out = $this->getMock('ConsoleOutput', [], [], '', false);
		$in = $this->getMock('ConsoleInput', [], [], '', false);

		$this->_targetObject = $this->getMock(
			'CakeInstallerShell',
			['in', 'out', 'hr', 'err', 'createFile', '_stop', '_checkUnitTest', 'clear'],
			[$out, $out, $in]
		);
		$this->_targetObject->initialize();
		$this->_targetObject->path = $this->_testDir;
		$this->_targetObject->useActionNotify = false;
		$this->_targetObject->InstallerCheck->markerFileInstalled = $this->_markerFileInstalled;
		$this->_targetObject->InstallerCheck->markerFileRestart = $this->_markerFileRestart;
		$oFolder = new Folder($this->_targetObject->path, true);
		$oFolder->create($this->_testDir . 'tmp');
		$oFile = new File($this->_testDir . 'Config' . DS . 'config.php', true);
		$oFile = new File($this->_testDir . 'Config' . DS . 'core.php', true);
		$tmpConfig = <<<EOD
	setLocale(LC_ALL, 'eng');
	Configure::write('Config.language', 'eng');
	Configure::write('Security.key', '8e2915eeca7cfc0c81f1bda7d7dea068bb7a8c283bca2b0e90e9862b5521e4af');
	Configure::write('Security.salt', 'afdac735d55de46cf77f589b052c0df6be8f65aa');
	Configure::write('Security.cipherSeed', '383466616662383061646636393766');
	date_default_timezone_set('Europe/Minsk');
	Configure::write('Config.timezone', 'Europe/Minsk');
	Configure::write('App.fullBaseUrl', 'http://test.com');

EOD;
		$oFile->write($tmpConfig);
		$oFile = new File($this->_testDir . 'Config' . DS . 'database.php', true);
		$tmpConfig = <<<EOD
<?php
class DATABASE_CONFIG {

	public \$default = [
		'datasource' => 'Database/Mysql',
		'persistent' => false,
		'host' => 'localhost',
		'login' => '',
		'password' => '',
		'database' => 'test',
	];
}
EOD;
		$oFile->write($tmpConfig);
		if (file_exists($this->_markerFileInstalled)) {
			unlink($this->_markerFileInstalled);
		}
		if (file_exists($this->_markerFileRestart)) {
			unlink($this->_markerFileRestart);
		}
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
		$Folder = new Folder($this->_testDir);
		$Folder->delete();

		parent::tearDown();
	}

/**
 * testInitialize method sets the path up.
 *
 * @return void
 */
	public function testInitialize() {
		$expected = [
			'default',
			'test',
		];
		$result = Configure::read('CakeInstaller.configDBconn');
		$this->assertData($expected, $result);

		$expected = [
			'eng',
			'rus',
		];
		$result = Configure::read('CakeInstaller.UIlangList');
		$this->assertData($expected, $result);

		$result = Configure::read('Cache.disable');
		$this->assertTrue($result);
	}

/**
 * testGetOptionParser method
 *
 * @return void
 */
	public function testGetOptionParser() {
		$parser = $this->_targetObject->getOptionParser();
		$subcommands = $parser->subcommands();
		$this->assertFalse(empty($subcommands));
		$result = array_keys($subcommands);
		$expected = [
			'setuilang',
			'check',
			'setdirpermiss',
			'setsecurkey',
			'settimezone',
			'setbaseurl',
			'configdb',
			'createdb',
			'createsymlinks',
			'install',
		];
		$this->assertData($expected, $result);
	}

/**
 * testHasMethod method
 *
 * @return void
 */
	public function testHasMethod() {
		$params = [
			[
				null, // $name
			],
			[
				'bad', // $name
			],
			[
				'install', // $name
			],
		];
		$expected = [
			false,
			false,
			true
		];

		$this->runClassMethodGroup('hasMethod', $params, $expected);
	}

/**
 * testMain method
 *
 * @return void
 */
	public function testMainNeedRestart() {
		$this->assertTrue(file_put_contents($this->_markerFileRestart, 'Some data...') !== false);
		Configure::write('CakeInstaller.installerCommands', [
			CAKE_INSTALLER_SHELL_INSTALLER_TASK_CHECK,
			CAKE_INSTALLER_SHELL_INSTALLER_TASK_SETSECURKEY,
		]);
		$this->_targetObject->expects($this->at(0))->method('in')->will($this->returnValue('n'));
		$this->_targetObject->expects($this->at(6))->method('out')->with('   1. ' . __d('cake_installer', 'Checking PHP environment'));
		$this->_targetObject->expects($this->at(7))->method('out')->with('   2. ' . __d('cake_installer', 'Setting security key'));
		$this->_targetObject->expects($this->any())->method('in')->will($this->returnValue('1'));
		$this->_targetObject->main();
	}

/**
 * testMain method
 *
 * @return void
 */
	public function testMain() {
		Configure::write('CakeInstaller.installerCommands', [
			CAKE_INSTALLER_SHELL_INSTALLER_TASK_CHECK,
			CAKE_INSTALLER_SHELL_INSTALLER_TASK_SETSECURKEY,
		]);
		$this->_targetObject->expects($this->at(5))->method('out')->with('   1. ' . __d('cake_installer', 'Checking PHP environment'));
		$this->_targetObject->expects($this->at(6))->method('out')->with('   2. ' . __d('cake_installer', 'Setting security key'));
		$this->_targetObject->expects($this->any())->method('in')->will($this->returnValue('1'));
		$this->_targetObject->main();
	}

/**
 * testCheck method
 *
 * @return void
 */
	public function testCheck() {
		$this->_targetObject->expects($this->at(2))->method('out')
			->with($this->stringContains('[<success>' . __d('cake_installer', 'Ok') . '</success>]'));

		$this->_targetObject->check();
	}

/**
 * testSetdirpermiss method
 *
 * @return void
 */
	public function testSetdirpermiss() {
		$tempDir = $this->_testDir . 'tmp';
		$fileConfig = $this->_testDir . 'Config' . DS . 'config.php';

		$this->_targetObject->expects($this->at(2))->method('out')
			->with($this->logicalOr(
				$this->stringContains(__d('cake_installer', 'Server OS is Windows. Change permissions skipped.')),
				$this->matchesRegularExpression('/' . __d('cake_installer', 'Access permissions on \'%s\' changed to \'%s\' successfully.', '.*', '.*') . '/')
			));

		$this->_targetObject->setdirpermiss();
	}

/**
 * testSetsecurkey method
 *
 * @return void
 */
	public function testSetsecurkey() {
		$fileConfig = $this->_testDir . 'Config' . DS . 'core.php';
		$this->_targetObject->setsecurkey();

		$File = new File($fileConfig);
		$contents = $File->read();
		$this->assertNotRegExp('/8e2915eeca7cfc0c81f1bda7d7dea068bb7a8c283bca2b0e90e9862b5521e4af/', $contents);
		$this->assertNotRegExp('/afdac735d55de46cf77f589b052c0df6be8f65aa/', $contents);
		$this->assertNotRegExp('/383466616662383061646636393766/', $contents);
	}

/**
 * testSettimezone method
 *
 * @return void
 */
	public function testSettimezone() {
		$timeZone = 'America/Anchorage';
		$fileConfig = $this->_testDir . 'Config' . DS . 'core.php';

		$this->_targetObject->expects($this->any())->method('in')->will($this->returnValue('2'));
		$this->_targetObject->settimezone();

		$File = new File($fileConfig);
		$contents = $File->read();
		$this->assertRegExp('/' . preg_quote('date_default_timezone_set(\'' . $timeZone . '\')', '/') . '/', $contents);
		$this->assertRegExp('/' . preg_quote('Configure::write(\'Config.timezone\', \'' . $timeZone . '\')', '/') . '/', $contents);
	}

/**
 * testSetbaseurl method
 *
 * @return void
 */
	public function testSetbaseurl() {
		$fileConfig = $this->_testDir . 'Config' . DS . 'core.php';

		$this->_targetObject->expects($this->any())->method('in')->will($this->returnValue('http://fabrikam.com'));
		$this->_targetObject->setbaseurl();

		$File = new File($fileConfig);
		$contents = $File->read();
		$this->assertRegExp('/' . preg_quote('http://fabrikam.com', '/') . '/', $contents);
	}

/**
 * testConnectdb method
 *
 * @return void
 */
	public function testConnectdb() {
		$this->_targetObject->expects($this->at(4))->method('out')
			->with($this->stringContains('[<success>' . __d('cake_installer', 'Ok') . '</success>]'));

		$this->_targetObject->connectdb();
	}

/**
 * testCreatesymlinks method
 *
 * @return void
 */
	public function testCreatesymlinks() {
		$this->_targetObject->expects($this->at(4))->method('out')
			->with($this->stringContains('<success>' . __d('cake_installer', 'Creating symbolic links completed successfully.') . '</success>'));
		$this->_targetObject->createsymlinks();
		$this->fileExists(TMP . 'tests' . DS . 'link.php');

		$result = readlink(TMP . 'tests' . DS . 'link.php');
		$expected = TMP . 'tests' . DS . 'Config' . DS . 'core.php';
		$result = mb_strtolower($result);
		$expected = mb_strtolower($expected);
		$this->assertData($expected, $result);

		Configure::write('CakeInstaller.symlinksCreationList', [
			TMP . 'tests' . DS . 'link.php' => TMP . 'BAD_PATH'
		]);
		$this->_targetObject->expects($this->at(4))->method('out')
			->with($this->stringContains('<error>' . __d('cake_installer', 'Creating symbolic links unsuccessfully.') . '</error>'));
		$this->_targetObject->createsymlinks();
	}

/**
 * testSetuilang method
 *
 * @return void
 */
	public function testSetuilang() {
		$fileConfig = $this->_testDir . 'Config' . DS . 'core.php';

		$this->_targetObject->expects($this->any())->method('in')->will($this->returnValue('1'));
		$this->_targetObject->setuilang();

		$File = new File($fileConfig);
		$contents = $File->read();
		$this->assertRegExp('/' . preg_quote('Configure::write(\'Config.language\', \'eng\')', '/') . '/', $contents);
		$this->assertRegExp('/' . preg_quote('setLocale(LC_ALL, \'eng\')', '/') . '/', $contents);
	}

/**
 * testInstall method
 *
 * @return void
 */
	public function testInstall() {
		Configure::write('CakeInstaller.installTasks', [
			CAKE_INSTALLER_SHELL_INSTALLER_TASK_CHECK,
			CAKE_INSTALLER_SHELL_INSTALLER_TASK_SETSECURKEY,
		]);
		$this->_targetObject->expects($this->at(1))->method('in')->will($this->returnValue('y'));
		$this->_targetObject->expects($this->at(30))->method('out')
			->with($this->stringContains('<success>' . __d('cake_installer', 'The installation process is completed successfully.') . '</success>'));
		$this->_targetObject->install();
		$this->assertTrue(file_exists($this->_markerFileInstalled));
		$this->assertTrue($this->_targetObject->InstallerCompleted->intsallCompletedState);
	}
}
