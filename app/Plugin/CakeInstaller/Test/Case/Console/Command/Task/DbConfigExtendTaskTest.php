<?php
/**
 * DbConfigExtendTask Test Case
 *
 */

App::uses('AppCakeTestCase', 'CakeInstaller.Test');
App::uses('ShellDispatcher', 'Console');
App::uses('ConsoleOutput', 'Console');
App::uses('ConsoleInput', 'Console');
App::uses('Shell', 'Console');
App::uses('DbConfigExtendTask', 'CakeInstaller.Console/Command/Task');
App::uses('File', 'Utility');

/**
 * DbConfigTest class
 *
 */
class DbConfigExtendTaskTest extends AppCakeTestCase {

/**
 * Path to test directory
 *
 * @var string
 */
	protected $_testDir = TMP . 'tests' . DS;

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$out = $this->getMock('ConsoleOutput', [], [], '', false);
		$in = $this->getMock('ConsoleInput', [], [], '', false);

		$this->_targetObject = $this->getMock(
			'DbConfigExtendTask',
			['in', 'out', 'err', 'hr', 'createFile', '_stop', '_checkUnitTest', '_verify', 'clear'],
			[$out, $out, $in]
		);

		$oFile = new File($this->_testDir . 'Config' . DS . 'database.php', true);
		$tmpConfig = <<<EOD
<?php
class DATABASE_CONFIG {

	public \$default = array(
		'datasource' => 'Database/Mysql',
		'persistent' => false,
		'host' => 'localhost',
		'login' => '',
		'password' => '',
		'database' => 'app_db',
	);
	
	public \$test = array(
		'datasource' => 'Database/Mysql',
		'persistent' => false,
		'host' => 'localhost',
		'login' => '',
		'password' => '',
		'database' => 'test',
	);
	
EOD;
		$oFile->write($tmpConfig);
		$this->_targetObject->path = $this->_testDir . 'Config' . DS;
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		$Folder = new Folder($this->_testDir);
		$Folder->delete();

		parent::tearDown();
	}

/**
 * Test the getConfig method.
 *
 * @return void
 */
	public function testGetConfig() {
		$this->_targetObject->expects($this->any())
			->method('in')
			->will($this->returnValue('test'));

		$result = $this->_targetObject->getConfig();
		$this->assertData('test', $result);
	}

/**
 * test that initialize sets the path up.
 *
 * @return void
 */
	public function testInitialize() {
		$this->_targetObject->initialize();
		$this->assertFalse(empty($this->_targetObject->path));
		$this->assertData(CONFIG, $this->_targetObject->path);
	}

/**
 * test execute and by extension _interactive
 *
 * @return void
 */
	public function testExecuteIntoInteractiveDefault() {
		$out = $this->getMock('ConsoleOutput', [], [], '', false);
		$in = $this->getMock('ConsoleInput', [], [], '', false);
		$this->_targetObject = $this->getMock(
			'DbConfigExtendTask',
			['in', '_stop', 'createFile', 'bake', 'clear'],
			[$out, $out, $in]
		);
		$this->_targetObject->initialize();

		$this->_targetObject->expects($this->at(0))->method('clear'); //_interactive
		$this->_targetObject->expects($this->at(1))->method('in')->will($this->returnValue('1')); //name
		$this->_targetObject->expects($this->at(2))->method('in')->will($this->returnValue('Mysql')); //db type
		$this->_targetObject->expects($this->at(3))->method('in')->will($this->returnValue('n')); //persistent
		$this->_targetObject->expects($this->at(4))->method('in')->will($this->returnValue('localhost')); //server
		$this->_targetObject->expects($this->at(5))->method('in')->will($this->returnValue('bad')); //port
		$this->_targetObject->expects($this->at(6))->method('in')->will($this->returnValue('3306')); //port
		$this->_targetObject->expects($this->at(7))->method('in')->will($this->returnValue('root')); //user
		$this->_targetObject->expects($this->at(8))->method('in')->will($this->returnValue('password')); //password
		$this->_targetObject->expects($this->at(9))->method('in')->will($this->returnValue('cake_test')); //db
		$this->_targetObject->expects($this->at(10))->method('in')->will($this->returnValue('')); //schema
		$this->_targetObject->expects($this->at(11))->method('in')->will($this->returnValue('')); //prefix
		$this->_targetObject->expects($this->at(12))->method('in')->will($this->returnValue('utf8')); //encoding
		$this->_targetObject->expects($this->at(13))->method('clear'); //_verify
		$this->_targetObject->expects($this->at(14))->method('in')->will($this->returnValue('y')); //looks good
		$this->_targetObject->expects($this->at(15))->method('bake')
			->with([
				'default' => [
					'datasource' => 'Database/Mysql',
					'persistent' => false,
					'host' => 'localhost',
					'port' => '3306',
					'login' => 'root',
					'password' => 'password',
					'database' => 'cake_test',
					'schema' => '',
					'prefix' => '',
					'encoding' => 'utf8',
				]
			]);

		$this->_targetObject->execute();
	}
}
