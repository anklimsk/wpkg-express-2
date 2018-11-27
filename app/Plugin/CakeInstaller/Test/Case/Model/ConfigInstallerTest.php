<?php
App::uses('AppCakeTestCase', 'CakeInstaller.Test');
App::uses('ConfigInstaller', 'CakeInstaller.Model');

/**
 * ConfigInstaller Test Case
 */
class ConfigInstallerTest extends AppCakeTestCase {

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
		$this->_targetObject = ClassRegistry::init('CakeInstaller.ConfigInstaller');
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
 * testGetPhpVersionConfigEmpty method
 *
 * @return void
 */
	public function testGetPhpVersionConfigEmpty() {
		Configure::delete('CakeInstaller.PHPversion');
		$result = $this->_targetObject->getPhpVersionConfig();
		$expected = [];
		$this->assertData($expected, $result);
	}

/**
 * testGetPhpVersionConfig method
 *
 * @return void
 */
	public function testGetPhpVersionConfigShortSuccess() {
		$data = PHP_VERSION;
		Configure::write('CakeInstaller.PHPversion', $data);
		$result = $this->_targetObject->getPhpVersionConfig();
		$expected = [[$data]];
		$this->assertData($expected, $result);
	}

/**
 * testGetPhpVersionConfig method
 *
 * @return void
 */
	public function testGetPhpVersionConfigFullSuccess() {
		$data = [PHP_VERSION, '>='];
		Configure::write('CakeInstaller.PHPversion', $data);
		$result = $this->_targetObject->getPhpVersionConfig();
		$expected = [$data];
		$this->assertData($expected, $result);
	}

/**
 * testGetPhpExtensionsConfigEmpty method
 *
 * @return void
 */
	public function testGetPhpExtensionsConfigEmpty() {
		Configure::delete('CakeInstaller.PHPextensions');
		$result = $this->_targetObject->getPhpExtensionsConfig();
		$expected = [];
		$this->assertData($expected, $result);
	}

/**
 * testGetPhpExtensionsConfigShortSuccess method
 *
 * @return void
 */
	public function testGetPhpExtensionsConfigShortSuccess() {
		$data = 'PDO';
		Configure::write('CakeInstaller.PHPextensions', $data);
		$result = $this->_targetObject->getPhpExtensionsConfig();
		$expected = [[$data]];
		$this->assertData($expected, $result);
	}

/**
 * testGetPhpExtensionsConfigFullSuccess method
 *
 * @return void
 */
	public function testGetPhpExtensionsConfigFullSuccess() {
		$data = ['PDO', false];
		Configure::write('CakeInstaller.PHPextensions', $data);
		$result = $this->_targetObject->getPhpExtensionsConfig();
		$expected = [$data];
		$this->assertData($expected, $result);
	}

/**
 * testGetListInstallerCommandsEmpty method
 *
 * @return void
 */
	public function testGetListInstallerCommandsEmpty() {
		Configure::delete('CakeInstaller.installerCommands');
		$result = $this->_targetObject->getListInstallerCommands();
		$expected = [];
		$this->assertData($expected, $result);
	}

/**
 * testGetListInstallerCommandsSuccess method
 *
 * @return void
 */
	public function testGetListInstallerCommandsSuccess() {
		$data = CAKE_INSTALLER_SHELL_INSTALLER_TASK_CHECK;
		Configure::write('CakeInstaller.installerCommands', $data);
		$result = $this->_targetObject->getListInstallerCommands();
		$expected = [$data];
		$this->assertData($expected, $result);
	}

/**
 * testGetListInstallerCommandsSuccessMultiple method
 *
 * @return void
 */
	public function testGetListInstallerCommandsSuccessMultiple() {
		$data = [CAKE_INSTALLER_SHELL_INSTALLER_TASK_CHECK, CAKE_INSTALLER_SHELL_INSTALLER_TASK_CONFIG_DB];
		Configure::write('CakeInstaller.installerCommands', $data);
		$result = $this->_targetObject->getListInstallerCommands(false);
		$expected = [
			CAKE_INSTALLER_SHELL_INSTALLER_TASK_CHECK => __d('cake_installer', 'Checking PHP environment'),
			CAKE_INSTALLER_SHELL_INSTALLER_TASK_CONFIG_DB => __d('cake_installer', 'Configure database connections'),
		];
		$this->assertData($expected, $result);
	}

/**
 * testGetListInstallerTasksEmpty method
 *
 * @return void
 */
	public function testGetListInstallerTasksEmpty() {
		Configure::delete('CakeInstaller.installTasks');
		$result = $this->_targetObject->getListInstallerTasks();
		$expected = [];
		$this->assertData($expected, $result);
	}

/**
 * testGetListInstallerTasksSuccess method
 *
 * @return void
 */
	public function testGetListInstallerTasksSuccess() {
		$data = CAKE_INSTALLER_SHELL_INSTALLER_TASK_CHECK;
		Configure::write('CakeInstaller.installTasks', $data);
		$result = $this->_targetObject->getListInstallerTasks();
		$expected = [$data];
		$this->assertData($expected, $result);
	}

/**
 * testGetListInstallerTasksSuccessMultiple method
 *
 * @return void
 */
	public function testGetListInstallerTasksSuccessMultiple() {
		$data = [CAKE_INSTALLER_SHELL_INSTALLER_TASK_CHECK, CAKE_INSTALLER_SHELL_INSTALLER_TASK_CONFIG_DB];
		Configure::write('CakeInstaller.installTasks', $data);
		$result = $this->_targetObject->getListInstallerTasks();
		$expected = $data;
		$this->assertData($expected, $result);
	}

/**
 * testGetListSchemaCreationEmpty method
 *
 * @return void
 */
	public function testGetListSchemaCreationEmpty() {
		Configure::delete('CakeInstaller.schemaCreationList');
		$result = $this->_targetObject->getListSchemaCreation();
		$expected = [];
		$this->assertData($expected, $result);
	}

/**
 * testGetListSchemaCreationSuccess method
 *
 * @return void
 */
	public function testGetListSchemaCreationSuccess() {
		$data = 'sessions';
		Configure::write('CakeInstaller.schemaCreationList', $data);
		$result = $this->_targetObject->getListSchemaCreation();
		$expected = [$data];
		$this->assertData($expected, $result);
	}

/**
 * testGetListSchemaCreationSuccessMultiple method
 *
 * @return void
 */
	public function testGetListSchemaCreationSuccessMultiple() {
		$data = ['sessions', '-p Queue'];
		Configure::write('CakeInstaller.schemaCreationList', $data);
		$result = $this->_targetObject->getListSchemaCreation();
		$expected = $data;
		$this->assertData($expected, $result);
	}

/**
 * testGetListSchemaCheckingEmpty method
 *
 * @return void
 */
	public function testGetListSchemaCheckingEmpty() {
		Configure::delete('CakeInstaller.schemaCheckingList');
		$result = $this->_targetObject->getListSchemaChecking();
		$expected = [];
		$this->assertData($expected, $result);
	}

/**
 * testGetListSchemaCheckingSuccess method
 *
 * @return void
 */
	public function testGetListSchemaCheckingSuccess() {
		$data = 'sessions';
		Configure::write('CakeInstaller.schemaCheckingList', $data);
		$result = $this->_targetObject->getListSchemaChecking();
		$expected = [$data];
		$this->assertData($expected, $result);
	}

/**
 * testGetListSchemaCheckingSuccessMultiple method
 *
 * @return void
 */
	public function testGetListSchemaCheckingSuccessMultiple() {
		$data = ['sessions', '-p Queue'];
		Configure::write('CakeInstaller.schemaCheckingList', $data);
		$result = $this->_targetObject->getListSchemaChecking();
		$expected = $data;
		$this->assertData($expected, $result);
	}

/**
 * testGetListSymlinksCreationEmpty method
 *
 * @return void
 */
	public function testGetListSymlinksCreationEmpty() {
		Configure::delete('CakeInstaller.symlinksCreationList');
		$result = $this->_targetObject->getListSymlinksCreation();
		$expected = [];
		$this->assertData($expected, $result);
	}

/**
 * testGetListSymlinksCreationSuccess method
 *
 * @return void
 */
	public function testGetListSymlinksCreationSuccess() {
		$data = [APP . 'webroot' . DS . 'cake_installer' => APP . 'Plugin' . DS . 'CakeInstaller' . DS . 'webroot'];
		Configure::write('CakeInstaller.symlinksCreationList', $data);
		$result = $this->_targetObject->getListSymlinksCreation();
		$expected = $data;
		$this->assertData($expected, $result);
	}

/**
 * testGetListSymlinksCreationSuccessMultiple method
 *
 * @return void
 */
	public function testGetListSymlinksCreationSuccessMultiple() {
		$data = [
			APP . 'webroot' . DS . 'cake_installer' => APP . 'Plugin' . DS . 'CakeInstaller' . DS . 'webroot',
			APP . 'webroot' . DS . 'cake_ldap' => APP . 'Plugin' . DS . 'CakeLdap' . DS . 'webroot'
		];
		Configure::write('CakeInstaller.symlinksCreationList', $data);
		$result = $this->_targetObject->getListSymlinksCreation();
		$expected = $data;
		$this->assertData($expected, $result);
	}

/**
 * testGetListCronJobsCreationEmpty method
 *
 * @return void
 */
	public function testGetListCronJobsCreationEmpty() {
		Configure::delete('CakeInstaller.cronJobs');
		$result = $this->_targetObject->getListCronJobsCreation();
		$expected = [];
		$this->assertData($expected, $result);
	}

/**
 * testGetListUiLangsEmpty method
 *
 * @return void
 */
	public function testGetListUiLangsEmpty() {
		Configure::delete('CakeInstaller.UIlangList');
		$result = $this->_targetObject->getListUiLangs();
		$expected = [];
		$this->assertData($expected, $result);
	}

/**
 * testGetListUiLangsSuccess method
 *
 * @return void
 */
	public function testGetListUiLangsSuccess() {
		$data = 'eng';
		Configure::write('CakeInstaller.UIlangList', $data);
		$result = $this->_targetObject->getListUiLangs();
		$expected = [$data];
		$this->assertData($expected, $result);
	}

/**
 * testGetListUiLangsSuccessMultiple method
 *
 * @return void
 */
	public function testGetListUiLangsSuccessMultiple() {
		$data = ['eng', 'rus'];
		Configure::write('CakeInstaller.UIlangList', $data);
		$result = $this->_targetObject->getListUiLangs();
		$expected = $data;
		$this->assertData($expected, $result);
	}

/**
 * testGetCustomConnectionsConfigEmpty method
 *
 * @return void
 */
	public function testGetCustomConnectionsConfigEmpty() {
		Configure::delete('CakeInstaller.customConnections');
		$result = $this->_targetObject->getCustomConnectionsConfig();
		$expected = [];
		$this->assertData($expected, $result);
	}

/**
 * testGetCustomConnectionsConfigSuccess method
 *
 * @return void
 */
	public function testGetCustomConnectionsConfigSuccess() {
		$data = [
			'ldap' => [
				'datasource' => [
					'value' => 'CakeLdap.LdapExtSource',
				],
				'version' => [
					'defaultValue' => 3,
					'label' => __d('cake_installer', 'Version of LDAP protocol'),
					'options' => [2, 3],
				],
			]
		];
		Configure::write('CakeInstaller.customConnections', $data);
		$result = $this->_targetObject->getCustomConnectionsConfig();
		$expected = $data;
		$this->assertData($expected, $result);
	}

/**
 * testGetCustomConnectionsConfigSuccessMultiple method
 *
 * @return void
 */
	public function testGetCustomConnectionsConfigSuccessMultiple() {
		$data = [
			'ldap' => [
				'datasource' => [
					'value' => 'CakeLdap.LdapExtSource',
				],
				'version' => [
					'defaultValue' => 3,
					'label' => __d('cake_installer', 'Version of LDAP protocol'),
					'options' => [2, 3],
				],
			],
			'passage' => [
				'datasource' => [
					'value' => 'Database/Odbtp',
				],
				'port' => [
					'defaultValue' => 2799,
				],
			]
		];
		Configure::write('CakeInstaller.customConnections', $data);
		$result = $this->_targetObject->getCustomConnectionsConfig();
		$expected = $data;
		$this->assertData($expected, $result);
	}

/**
 * testGetListDbConnConfigsEmpty method
 *
 * @return void
 */
	public function testGetListDbConnConfigsEmpty() {
		Configure::delete('CakeInstaller.configDBconn');
		$result = $this->_targetObject->getListDbConnConfigs();
		$expected = [];
		$this->assertData($expected, $result);
	}

/**
 * testGetListDbConnConfigsSuccess method
 *
 * @return void
 */
	public function testGetListDbConnConfigsSuccess() {
		$data = 'ldap';
		Configure::write('CakeInstaller.configDBconn', $data);
		$result = $this->_targetObject->getListDbConnConfigs();
		$expected = [$data];
		$this->assertData($expected, $result);
	}

/**
 * testGetListDbConnConfigsSuccessMultiple method
 *
 * @return void
 */
	public function testGetListDbConnConfigsSuccessMultiple() {
		$data = ['ldap', 'passage'];
		Configure::write('CakeInstaller.configDBconn', $data);
		$result = $this->_targetObject->getListDbConnConfigs();
		$expected = $data;
		$this->assertData($expected, $result);
	}
}
