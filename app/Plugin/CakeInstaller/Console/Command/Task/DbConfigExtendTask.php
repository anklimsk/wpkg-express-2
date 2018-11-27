<?php
/**
 * This file is the console shell task file of the plugin.
 *
 * CakeInstaller: Installer of CakePHP web application.
 * @copyright Copyright 2016-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.Console.Command.Task
 */

App::uses('DbConfigTask', 'Console/Command/Task');
App::uses('CakeInstallerShellTrait', 'CakeInstaller.Utility');
App::uses('Hash', 'Utility');
App::uses('Debugger', 'Utility');
App::uses('CakeText', 'Utility');

/**
 * Task class for creating and updating the database configuration file.
 *
 * @package plugin.Console.Command.Task
 */
class DbConfigExtendTask extends DbConfigTask {

	use CakeInstallerShellTrait;

/**
 * Contains models to load and instantiate
 *
 * @var array
 * @link http://book.cakephp.org/2.0/en/console-and-shells.html#Shell::$uses
 */
	public $uses = [
		'CakeInstaller.ConfigInstaller',
		'CakeInstaller.InstallerCheck',
	];

/**
 * Default configuration settings to use
 *
 * @var array
 */
	protected $_defaultConfigs = [
		'default' => [
			'datasource' => [
				'defaultValue' => 'Mysql',
				'label' => 'Datasource',
				'options' => [
					'Mysql' => 'Database/Mysql',
					'Postgres' => 'Database/Postgres',
					'Sqlite' => 'Database/Sqlite',
					'Sqlserver' => 'Database/Sqlserver'
				]
			],
			'persistent' => [
				'defaultValue' => 'n',
				'label' => 'Persistent connection?',
				'options' => ['n' => false, 'y' => true],
			],
			'host' => [
				'defaultValue' => 'localhost',
				'label' => 'Database host',
			],
			'port' => [
				'label' => 'Port',
				'alowEmpty' => true,
				'validationPattern' => '/^[\d]*$/',
			],
			'login' => [
				'defaultValue' => 'root',
				'label' => 'User',
			],
			'password' => [
				'label' => 'Password',
				'alowEmpty' => true,
			],
			'database' => [
				'label' => 'Database name',
			],
			'schema' => [
				'label' => 'Table schema',
				'alowEmpty' => true,
			],
			'prefix' => [
				'label' => 'Table prefix',
				'alowEmpty' => true,
			],
			'encoding' => [
				'label' => 'Table encoding',
				'defaultValue' => 'utf8',
				'alowEmpty' => true,
			],
		],
	];

/**
 * initialization callback
 *
 * @return void
 */
	public function initialize() {
		Configure::write('Cache.disable', true);
		parent::initialize();

		if (!Configure::check('CakeInstaller')) {
			$this->ConfigInstaller->initConfig();
		}

		foreach ($this->_defaultConfigs['default'] as $paramName => &$paramOptions) {
			if (isset($paramOptions['label'])) {
				$paramOptions['label'] = __d('cake_installer_label', $paramOptions['label']);
			}
		}
	}

/**
 * Execution method always used for tasks
 *
 * @return void
 */
	public function execute() {
		if (empty($this->args)) {
			return $this->_interactive();
		}
	}

/**
 * Return default connection configuration
 *
 * @return array
 */
	protected function _getDefaultConfigs() {
		return $this->_defaultConfigs;
	}

/**
 * Return existing connection configuration to create
 *
 * @return array
 */
	protected function _getExistsConfig() {
		$defaultConfigsDBconn = $this->_getDefaultConfigs();
		$customConfigsDBconn = $this->ConfigInstaller->getCustomConnectionsConfig();
		$configsDBconn = $defaultConfigsDBconn + $customConfigsDBconn + ['test' => $defaultConfigsDBconn['default']];
		$currConfigDBconn = $this->ConfigInstaller->getListDbConnConfigs();
		$existConfigDBconn = array_intersect_key($configsDBconn, array_flip($currConfigDBconn));
		if (empty($existConfigDBconn)) {
			$existConfigDBconn = array_intersect_key($configsDBconn, ['default' => null]);
		}

		return $existConfigDBconn;
	}

/**
 * Check PCRE pattern
 *
 * @param string $pattern Pattern for checking
 * @return null|bool Return Null, if pattern is empty.
 *  True, if pattern is valid. False otherwise.
 */
	protected function _checkPcrePattern($pattern = null) {
		if (empty($pattern)) {
			return null;
		}

		$result = true;
		try {
			//@codingStandardsIgnoreStart
			$matchResult = @preg_match($pattern, 'some text');
			//@codingStandardsIgnoreEnd
			if ($matchResult === false) {
				$result = false;
			}
		} catch (Exception $exception) {
			$result = false;
		}

		return $result;
	}

/**
 * Get interactive parameter of connection
 *
 * @param array $defaultConfig Default connection configuration
 * @param array $paramConfig Configuration of parameter connection.
 * @param string $paramName Parameter name.
 * @return array Array information of parameter connection in format:
 *  - key `paramLabel`, value - label of parameter;
 *  - key `paramValue`, value - value of parameter.
 */
	protected function _getConfigParam($defaultConfig = null, $paramConfig = null, $paramName = null) {
		if (empty($defaultConfig)) {
			$defaultConfig = [];
		}

		if (empty($paramConfig)) {
			$paramConfig = [];
		}

		if (empty($paramName)) {
			$paramName = __d('cake_installer', 'Unknown parameter');
		}

		$paramLabel = Hash::get($paramConfig, 'label');
		if (empty($paramLabel)) {
			$paramLabel = Hash::get($defaultConfig, 'label');
			if (empty($paramLabel)) {
				$paramLabel = $paramName;
			}
		}

		$paramValue = Hash::get($paramConfig, 'value');
		if ($paramValue === null) {
			$paramValue = Hash::get($defaultConfig, 'value');
		}
		if ($paramValue !== null) {
			return compact('paramValue', 'paramLabel');
		}

		$paramDefaultValue = Hash::get($paramConfig, 'defaultValue');
		if ($paramDefaultValue === null) {
			$paramDefaultValue = Hash::get($defaultConfig, 'defaultValue');
		}
		if (empty($paramDefaultValue)) {
			$paramDefaultValue = null;
		}

		$paramOptions = Hash::get($paramConfig, 'options');
		if ($paramOptions === null) {
			$paramOptions = Hash::get($defaultConfig, 'options');
		}

		$paramOptionsOrig = $paramOptions;
		if (is_array($paramOptions)) {
			if (isAssoc($paramOptions)) {
				$paramOptions = array_keys($paramOptions);
			}
			if (!in_array($paramDefaultValue, $paramOptions)) {
				$paramDefaultValue = null;
			}
		} elseif (!is_null($paramOptions)) {
			$paramOptions = null;
		}

		$paramAlowEmpty = Hash::get($paramConfig, 'alowEmpty');
		if ($paramAlowEmpty === null) {
			$paramAlowEmpty = Hash::get($defaultConfig, 'alowEmpty');
		}

		$paramValidationPattern = Hash::get($paramConfig, 'validationPattern');
		if ($paramValidationPattern === null) {
			$paramValidationPattern = Hash::get($defaultConfig, 'validationPattern');
		}
		if ($this->_checkPcrePattern($paramValidationPattern) === false) {
			$paramValidationPattern = null;
			$this->out('<debug>' . __d('cake_installer', "Invalid validation pattern of parameter '%s'. Validation skipped.", $paramName) . '</debug>');
		}

		$requiredSymb = '';
		if (!$paramAlowEmpty) {
			$requiredSymb = '*';
		}

		while (true) {
			$paramValue = $this->in($paramLabel . ':' . $requiredSymb, $paramOptions, $paramDefaultValue);
			if (empty($paramValue) && !$paramAlowEmpty) {
				continue;
			}

			if (empty($paramValue) && $paramAlowEmpty) {
				break;
			}

			if (is_string($paramValue) && !empty($paramValidationPattern)) {
				if (preg_match($paramValidationPattern, $paramValue)) {
					break;
				}
			} elseif (!empty($paramValue)) {
				if (is_array($paramOptionsOrig) && isset($paramOptionsOrig[$paramValue])) {
					$paramValue = $paramOptionsOrig[$paramValue];
				}
				break;
			}
		}

		return compact('paramLabel', 'paramValue');
	}

/**
 * Interactive interface
 *
 * @param string $name Name of database configuration
 * @return bool Success
 */
	protected function _interactive($name = '') {
		$this->clear();
		$this->hr();
		$this->out(__d('cake_installer', 'Database Configuration:'));
		$this->hr();
		$done = false;
		$dbConfigs = [];

		$name = strtolower($name);
		$existConfigDBconn = $this->_getExistsConfig();
		$existConfigDBconnList = array_keys($existConfigDBconn);
		$appDirPath = dirname($this->path) . DS;
		$existDBconnState = $this->InstallerCheck->checkConnectDb($appDirPath);
		$existDBconnList = [];
		foreach ($existDBconnState as $connectionName => $connectionState) {
			if ($connectionState === true) {
				$existDBconnList[] = $connectionName;
			}
		}

		$notCfgDBconnList = Hash::diff($existConfigDBconnList, $existDBconnList);
		$connections = [];
		foreach ($existConfigDBconnList as $connName) {
			$connections[$connName] = mb_ucfirst($connName);
		}

		$notCfgConnect = 'exit';
		$useExit = true;
		if (!empty($notCfgDBconnList)) {
			$notCfgConnect = array_shift($notCfgDBconnList);
			$useExit = false;
		}
		if (!in_array($name, $existConfigDBconnList)) {
			$name = '';
		}

		while (!$done) {
			if (empty($name)) {
				$this->out(__d('cake_installer', 'Connection name') . ':');
				$this->hr();
				$inputMessage = __d('cake_installer', 'Input the number of connection name from list');
				$titleMessage = __d('cake_installer', 'Please choose connection name:');
				$name = $this->inputFromList($this, $connections, $inputMessage, $titleMessage, $notCfgConnect, $useExit);
			}

			$this->hr();
			$this->out(__d('cake_installer', 'Database Configuration: %s', mb_ucfirst($name)));
			$this->hr();

			$processConfig = $existConfigDBconn[$name];
			$defaultConfig = $existConfigDBconn['default'];
			$config = [];
			$labels = [];
			foreach ($processConfig as $processConfigParam => $processConfigOptions) {
				if (empty($processConfigParam)) {
					continue;
				}

				$defaultConfigOptions = Hash::get($defaultConfig, $processConfigParam);
				$configInfo = $this->_getConfigParam($defaultConfigOptions, $processConfigOptions, $processConfigParam);
				extract($configInfo);
				$config[$processConfigParam] = $paramValue;
				$labels[$processConfigParam] = $paramLabel;
			}

			if (!$this->_verify($config, $labels, $name)) {
				continue;
			}

			$dbConfigs[$name] = $config;
			$configured = array_keys($dbConfigs);
			if (!empty($existDBconnList)) {
				$configured = array_values(array_unique(array_merge($configured, $existDBconnList)));
			}

			$notConfigured = array_diff($existConfigDBconnList, $configured);
			if (empty($notConfigured)) {
				$done = true;
			} else {
				$optionsConfig = ['n' => __d('cake_installer', 'No')];
				foreach ($notConfigured as $connName) {
					$optionsConfig[$connName] = mb_ucfirst($connName);
				}

				$defaultValue = array_shift($notConfigured);
				$inputMessage = __d('cake_installer', 'Do you wish to add another database configuration?');
				$titleMessage = __d('cake_installer', 'Please choose connection name:');
				$doneYet = $this->inputFromList($this, $optionsConfig, $inputMessage, $titleMessage, $defaultValue, false);
				if (strtolower($doneYet === 'n')) {
					$done = true;
				} else {
					$name = $doneYet;
				}
			}
		}

		if (!$this->bake($dbConfigs)) {
			return false;
		}
		if (!config('database')) {
			return false;
		}

		return true;
	}

/**
 * Output verification message
 *
 * @param array $config The config data.
 * @param array $labels The labels of config data.
 * @param string $name Name of config data.
 * @return bool True if user says it looks good, false otherwise
 */
	protected function _verify($config = null, $labels = null, $name = null) {
		$this->clear();
		if (empty($name)) {
			$name = '-';
		}
		$this->out();
		$this->hr();
		$this->out(__d('cake_installer', 'The following database configuration will be created:'));
		$this->hr();
		$this->out(__d('cake_installer', 'Database Configuration: %s', ucfirst($name)));
		$this->hr();
		$data = [
			[
				__d('cake_installer', 'Parameter name'),
				__d('cake_installer', 'Parameter value'),
			]
		];
		foreach ($config as $param => $value) {
			$label = $param;
			if (isset($labels[$param])) {
				$label = $labels[$param];
			}

			$data[] = [
				$this->_prepareParamLabel($label, 30),
				$this->_prepareParamValue($param, $value, 30)
			];
		}
		$this->helper('table')->output($data);

		$this->hr();
		$looksOk = $this->in(__d('cake_installer', 'Look okay?'), ['y', 'n'], 'y');
		if (strtolower($looksOk) === 'y') {
			return true;
		}

		return false;
	}

/**
 * Return label truncated to the length
 *
 * @param string $label Label to truncate
 * @param int $length Length of returned string, including ellipsis.
 * @return string Return label truncated to the length
 */
	protected function _prepareParamLabel($label = null, $length = 0) {
		$truncateOpt = [
			'ellipsis' => '...',
			'exact' => false,
			'html' => false
		];
		if (empty($label) || ($length <= 0)) {
			return $label;
		}

		$label = CakeText::truncate($label, $length, $truncateOpt);

		return $label;
	}

/**
 * Returns parameter value truncated to the length. If the parameter
 *  is a password, replace it with the symbol `*`.
 *
 * @param string $param Name of parameter
 * @param string $value Value to truncate
 * @param int $length Length of returned string, including ellipsis.
 * @return string Return value truncated to the length
 */
	protected function _prepareParamValue($param = null, $value = null, $length = 0) {
		$param = strtolower($param);
		if ($param === 'password') {
			$value = str_repeat('*', strlen($value));
		} else {
			$value = $this->_exportParamValue($value, $length);
		}

		return $value;
	}

/**
 * Returns parameter value prepared to save and truncated to the length.
 *
 * @param string $value Value to truncate
 * @param int $length Length of returned string, including ellipsis.
 * @return string Return value truncated to the length
 */
	protected function _exportParamValue($value = null, $length = 0) {
		$truncateOpt = [
			'ellipsis' => '...',
			'exact' => false,
			'html' => false
		];

		$valueText = var_export($value, true);
		$valueType = Debugger::getType($value);
		switch ($valueType) {
			case 'string':
				if (ctype_digit($value)) {
					$valueText = (int)$value;
				}
				break;
			case 'resource':
			case 'unknown':
				$valueText = 'null';
				break;
		}
		if ($length > 0) {
			$valueText = CakeText::truncate($valueText, $length, $truncateOpt);
		}

		return $valueText;
	}

/**
 * Assembles and writes database.php
 *
 * @param array $configs Configuration settings to use
 * @return bool Success
 */
	public function bake($configs) {
		if (!is_dir($this->path)) {
			$this->out('<error>' . __d('cake_installer', '%s not found', $this->path) . '</error>');

			return false;
		}

		$filename = $this->path . 'database.php';
		$oldConfigs = [];

		if (file_exists($filename)) {
			config('database');
			$db = new $this->databaseClassName;
			$temp = get_class_vars(get_class($db));

			foreach ($temp as $configName => $info) {
				$oldConfigs[$configName] = $info;
			}
		}

		foreach ($oldConfigs as $oldConfigName => $oldConfig) {
			foreach ($configs as $configName => $configInfo) {
				if ($oldConfigName === $configName) {
					unset($oldConfigs[$oldConfigName]);
				}
			}
		}

		$configs = array_merge($oldConfigs, $configs);
		$out = "<?php\n";
		$out .= "class DATABASE_CONFIG {\n\n";

		foreach ($configs as $name => $config) {
			$out .= "\tpublic \${$name} = [\n";
			foreach ($config as $param => $value) {
				$valueText = $this->_exportParamValue($value, 0);
				$out .= "\t\t'{$param}' => " . $valueText . ",\n";
			}
			$out .= "\t];\n";
		}

		$out .= "}\n";
		$filename = $this->path . 'database.php';

		return $this->createFile($filename, $out);
	}
}
