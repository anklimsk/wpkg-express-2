<?php
/**
 * This file is the model file of the application. Used to
 *  manage logs.
 *
 * This file is part of wpkgExpress II.
 *
 * wpkgExpress II is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * wpkgExpress II is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with wpkgExpress II. If not, see <https://www.gnu.org/licenses/>.
 *
 * wpkgExpress II: A web-based frontend to WPKG.
 *  Based on wpkgExpress by Brian White.
 * @copyright Copyright 2009, Brian White.
 * @copyright Copyright 2018, Andrey Klimov.
 * @package app.Model
 */

App::uses('AppModel', 'Model');
App::uses('ClassRegistry', 'Utility');
App::uses('Hash', 'Utility');
App::uses('File', 'Utility');
App::uses('Folder', 'Utility');
App::uses('Router', 'Routing');
App::import(
	'Vendor',
	'SMB',
	['file' => 'SMB' . DS . 'vendor' . DS . 'autoload.php']
);

/**
 * The model is used to manage logs.
 *
 * @package app.Model
 */
class Log extends AppModel {

/**
 * Custom display field name.
 *
 * @var string
 * @link https://book.cakephp.org/2.0/en/models/model-attributes.html#displayfield
 */
	public $displayField = 'message';

/**
 * List of behaviors to load when the model object is initialized.
 *
 * @var array
 * @link https://book.cakephp.org/2.0/en/models/behaviors.html#using-behaviors
 */
	public $actsAs = [
		'BreadCrumbExt',
		'GroupAction',
		'ParseData',
		'GetNumber' => ['cacheConfig' => CACHE_KEY_STATISTICS_INFO_LOG],
		'ValidationRules'
	];

/**
 * List of validation rules.
 *
 * @var array
 * @link https://book.cakephp.org/2.0/en/models/model-attributes.html#validate
 * @link https://book.cakephp.org/2.0/en/models/data-validation.html
 */
	public $validate = [
		'type_id' => [
			'isinteger' => [
				'rule' => 'numeric',
				'message' => "The log's type is invalid.",
				'last' => true
			],
			'validrange' => [
				'rule' => ['checkRange', 'LOG_TYPE_', false],
				'message' => "The log's type is invalid.",
				'last' => true
			]
		],
		'host_id' => [
			'rule' => 'naturalNumber',
			'message' => 'Incorrect foreign key',
			'allowEmpty' => false,
			'required' => false,
			'last' => true,
		],
		'message' => [
			'rule' => 'notBlank',
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Log message is invalid.'
		],
		'date' => [
			'rule' => ['datetime', 'ymd'],
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Log date is invalid.'
		],
	];

/**
 * Object of model `ExtendQueuedTask`
 *
 * @var object
 */
	protected $_modelExtendQueuedTask = null;

/**
 * Constructor. Binds the model's database table to the object.
 *
 * @param bool|int|string|array $id Set this ID for this model on startup,
 * can also be an array of options, see above.
 * @param string $table Name of database table to use.
 * @param string $ds DataSource connection name.
 */
	public function __construct($id = false, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);

		$this->_modelExtendQueuedTask = ClassRegistry::init('CakeTheme.ExtendQueuedTask');
	}

/**
 * Detailed list of belongsTo associations.
 *
 * @var array
 * @link https://book.cakephp.org/2.0/en/models/associations-linking-models-together.html#belongsto
 */
	public $belongsTo = [
		'LogType' => [
			'className' => 'LogType',
			'foreignKey' => 'type_id',
			'conditions' => '',
			'fields' => ['LogType.name'],
			'order' => ''
		],
		'LogHost' => [
			'className' => 'LogHost',
			'foreignKey' => 'host_id',
			'conditions' => '',
			'fields' => ['LogHost.name'],
			'order' => ''
		]
	];

/**
 * Return data for pagination
 *
 * @param array $conditions Conditions for pagination.
 * @param array $fields Fields list.
 * @param array|string $order Sorting order.
 * @param int $limit Limit for pagination.
 * @param int $page Page number for pagination.
 * @param int $recursive Number of associations to recurse through during find calls.
 *  Fetches only the first level by default.
 * @param array $extra Extra parametrs for pagination.
 * @return mixed On success array data or null|false on failure.
 */
	public function paginate($conditions, $fields, $order, $limit, $page = 1, $recursive = null, $extra = []) {
		if (!is_array($order)) {
			$order = [$order];
		}
		$order[$this->alias . '.id'] = 'asc';
		$parameters = compact('conditions', 'fields', 'order', 'limit', 'page');
		if ($recursive != $this->recursive) {
			$parameters['recursive'] = $recursive;
		}

		return $this->find('all', array_merge($parameters, $extra));
	}

/**
 * Return name of data.
 *
 * @return string Return name of data
 */
	public function getTargetName() {
		$result = __('Log record');

		return $result;
	}

/**
 * Return name of group data.
 *
 * @return string Return name of group data
 */
	public function getGroupName() {
		$result = __('Logs');

		return $result;
	}

/**
 * Return name of data.
 *
 * @param int|string|array $id ID of record or array data
 *  for retrieving name.
 * @return string|bool Return name of data,
 *  or False on failure.
 */
	public function getName($id = null) {
		if (empty($id)) {
			return false;
		}
		if (is_array($id)) {
			if (!isset($id[$this->LogHost->alias][$this->LogHost->displayField])) {
				return false;
			}
			return $id[$this->LogHost->alias][$this->LogHost->displayField];
		}

		$fields = [
			'LogHost.' . $this->LogHost->displayField,
		];
		$conditions = [$this->alias . '.id' => $id];
		$contain = ['LogHost'];
		$data = $this->find('first', compact('conditions', 'fields', 'contain'));
		if (empty($data)) {
			return false;
		}

		$result = $data['LogHost'][$this->LogHost->displayField];

		return $result;
	}

/**
 * Return name of data.
 *
 * @param int|string|array $id ID of record or array data
 *  for retrieving name.
 * @param string $typeName Object type name
 * @param bool $primary Flag of direct method call or nested
 * @return string|bool Return name of data,
 *  or False on failure.
 */
	public function getNameExt($id = null, $typeName = null, $primary = true) {
		$name = (string)$this->getName($id);
		if (!empty($name)) {
			$name = "'" . $name . "'";
		}
		$result = __('Log of host %s', $name);

		return $result;
	}

/**
 * Return full name of data.
 *
 * @param int|string|array $id ID of record or array data
 *  for retrieving full name
 * @param int|string $refType ID type of object
 * @param int|string $refNode ID node of object
 * @param int|string $refId Record ID of the node
 * @param bool $primary Flag of direct method call or nested
 * @return string|bool Return full name of data,
 *  or False on failure.
 */
	public function getFullName($id = null, $refType = null, $refNode = null, $refId = null, $primary = true) {
		return $this->getNameExt($id, null, $primary);
	}

/**
 * Return an array of information for creating a breadcrumbs.
 *
 * @param int|string|array $id ID of record or array data
 *  for retrieving name.
 * @param int|string $refType ID type of object
 * @param int|string $refNode ID node of object
 * @param int|string $refId Record ID of the node
 * @param bool|null $includeRoot If True, include information of root breadcrumb.
 *  If Null, include information of root breadcrumb if $ID is not empty.
 * @return array Return an array of information for creating a breadcrumbs.
 */
	public function getBreadcrumbInfo($id = null, $refType = null, $refNode = null, $refId = null, $includeRoot = null) {
		$result = [];
		$result[] = $this->createBreadcrumb();
		if (!empty($id)) {
			$result[] = $this->createBreadcrumb($id, false);
		}

		return $result;
	}

/**
 * Return PCRE pattern for parse log file name.
 *
 * @param string|null $hostName Host name for parsing log file.
 * @return string Return PCRE pattern.
 */
	protected function _getLogFilePattern($hostName = null) {
		if (empty($hostName)) {
			$logFilePattern = '^wpkg\-.*@?.*\.log$';
		} else {
			$logFilePattern = '^wpkg\-' . preg_quote($hostName, '/') . '@?.*\.log$';
		}

		return $logFilePattern;
	}

/**
 * Saving log information.
 *
 * @param array $logInfo Array information log to save.
 * @return bool Success.
 */
	protected function _saveLog($logInfo = []) {
		if (empty($logInfo)) {
			return false;
		}

		$hostName = $logInfo['LogHost']['name'];
		if (!isset($logInfo[$this->alias]['host_id'])) {
			$hostId = $this->getIdFromNamesCache('LogHost', $hostName);
		} else {
			$hostId = $logInfo[$this->alias]['host_id'];
		}
		if (!empty($hostId)) {
			$logInfo[$this->alias]['host_id'] = $hostId;
			unset($logInfo['LogHost']);
		}
		unset($logInfo['LogType']);

		$this->create(false);
		$result = (bool)$this->saveAll($logInfo, ['validate' => false]);
		if ($result && isset($logInfo['LogHost']['name'])) {
			$hostId = $this->LogHost->getLastInsertID();
			$this->setIdNamesCache('LogHost', $hostName, $hostId);
		}

		return $result;
	}

/**
 * Parsing log files.
 *
 * @param string|null $hostName Host name for parsing log files.
 * @param int $idTask The ID of the QueuedTask
 * @return bool Success.
 */
	public function parseLogs($hostName = null, $idTask = null) {
		$step = 0;
		$maxStep = 1;
		$errorMessages = [];
		$result = true;
		set_time_limit(LOG_PARSE_TIME_LIMIT);

		$this->_modelExtendQueuedTask->updateProgress($idTask, 0);
		$oLocalLogDir = new Folder(LOG_DIR, true, 0755);
		list(, $localFiles) = $oLocalLogDir->read(false, false, true);
		if (!empty($localFiles)) {
			array_map('unlink', $localFiles);
		}

		$logFilePattern = $this->_getLogFilePattern($hostName);

		$modelSetting = ClassRegistry::init('Setting');
		$user = $modelSetting->getConfig('SmbAuthUser');
		$pswd = $modelSetting->getConfig('SmbAuthPassword');
		$workgroup = $modelSetting->getConfig('SmbWorkgroup');
		$host = $modelSetting->getConfig('SmbServer');
		$shareName = $modelSetting->getConfig('SmbLogShare');

		$auth = new \Icewind\SMB\BasicAuth($user, $workgroup, $pswd);
		$serverFactory = new \Icewind\SMB\ServerFactory();
		$server = $serverFactory->createServer($host, $auth);
		$share = $server->getShare($shareName);

		$aRemoteFiles = [];
		$files = $share->dir('/');
		foreach ($files as $info) {
			if ($info->isDirectory()) {
				continue;
			}

			$filePath = $info->getName();
			if (!preg_match('/' . $logFilePattern . '/i', $filePath)) {
				continue;
			}
			$aRemoteFiles[] = $filePath;
		}

		if (empty($aRemoteFiles)) {
			$this->_modelExtendQueuedTask->updateTaskErrorMessage($idTask, __('No log files to parsing.'));

			return true;
		}

		foreach ($aRemoteFiles as $filePath) {
			$fileName = pathinfo($filePath, PATHINFO_FILENAME);
			if (empty($fileName)) {
				$errorMessages['Error'][__('Error on retrieving log file name')][] = $fileName;
				continue;
			}

			$localFile = LOG_DIR . $fileName;
			if ($share->get($filePath, $localFile)) {
				try {
					if ($share->del($filePath)) {
						unlink($localFile);
					}
				} catch (Exception $e) {
					unlink($localFile);
				}
			} else {
				$errorMessages['Errors'][__('Error on copying files')][] = $filePath;
			}
		}
		if (!empty($errorMessages)) {
			$errorMessagesText = $this->renderErrorMessages($errorMessages);
			$this->_modelExtendQueuedTask->updateTaskErrorMessage($idTask, $errorMessagesText, true);
			return false;
		}

		list(, $localFiles) = $oLocalLogDir->read(true, false, true);
		if (empty($localFiles)) {
			$this->_modelExtendQueuedTask->updateTaskErrorMessage($idTask, __('No log files to parsing.'));

			return true;
		}

		$this->createNamesCache('Package', 'id_text', false, 'id_text');
		$this->createNamesCache('Package', 'name', false, 'name');
		$this->createNamesCache('Profile', null, false);
		$this->createNamesCache('Host', null, false);
		$this->createNamesCache('LogHost');
		$this->createNamesCache('LogType');
		$dataToSave = $this->_prepareLogData($errorMessages, $localFiles);

		$maxStep += count($dataToSave);
		foreach ($dataToSave as $i => &$dataToSaveItem) {
			if (!$this->_saveLog($dataToSaveItem)) {
				unset($dataToSave[$i]);
				$result = false;
			}

			$step++;
			if ($step % 10 == 0) {
				$this->_modelExtendQueuedTask->updateTaskProgress($idTask, $step, $maxStep);
			}
		}
		unset($dataToSaveItem);

		if (!$this->_sendEmail($errorMessages, $dataToSave)) {
			$result = false;
		}
		if (!$this->LogHost->clearUnusedHosts()) {
			$errorMessages['Errors'][] = __('Error on removing unused hosts');
			$result = false;
		}

		$step = $maxStep - 1;
		$this->_modelExtendQueuedTask->updateTaskProgress($idTask, $step, $maxStep);
		if (!empty($idTask) && !empty($errorMessages)) {
			$errorMessagesText = $this->renderErrorMessages($errorMessages);
			$this->_modelExtendQueuedTask->updateTaskErrorMessage($idTask, $errorMessagesText, true);
		}

		return $result;
	}

/**
 * Sending E-mail report.
 *
 * @param array &$errorMessages Array of error messages.
 * @param array $data Data of report.
 * @return bool Success.
 */
	protected function _sendEmail(array &$errorMessages, $data = []) {
		if (empty($data)) {
			return true;
		}

		$result = true;
		$modelSetting = ClassRegistry::init('Setting');
		$modelLdap = ClassRegistry::init('CakeSettingsApp.Ldap');
		$listEmails = $modelLdap->getListGroupEmail('AdminGroupMember');
		if (empty($listEmails)) {
			$emailContact = $modelSetting->getConfig('EmailContact');
			if (!empty($emailContact)) {
				$listEmails = [$emailContact => __('Administrator of WPKG')];
			}
		}

		$moreRecords = count($data) - EMAIL_REPORT_ERRORS_SHOW_RECORDS_LIMIT;
		$logs = array_slice($data, 0, EMAIL_REPORT_ERRORS_SHOW_RECORDS_LIMIT);
		$modelSendEmail = ClassRegistry::init('CakeNotify.SendEmail');
		$projectName = __dx('project', 'mail', PROJECT_NAME);
		$config = 'smtp';
		$domain = $modelSendEmail->getDomain();
		$from = ['noreply@' . $domain, __d('project', PROJECT_NAME)];
		$subject = __('Found new errors of WPKG');
		$template = 'error_report';
		$helpers = [
			'Number',
			'Time',
			'CakeTheme.ViewExtension'
		];
		$shortInfo = true;
		$logsUrl = [
			'controller' => 'logs',
			'action' => 'index',
			'prefix' => 'admin',
			'admin' => true,
			'full_base' => true
		];
		$created = date('Y-m-d H:i:s');
		$vars = compact(
			'logs',
			'shortInfo',
			'logsUrl',
			'created',
			'moreRecords',
			'projectName'
		);
		foreach ($listEmails as $email => $name) {
			$to = [$email, $name];
			if (!$modelSendEmail->putQueueEmail(compact('config', 'from', 'to', 'subject', 'template', 'vars', 'helpers'))) {
				$errorMessages['Errors'][__('Error on sending e-mail')][] = __('Error on putting sending e-mail for "%s" in queue...', $to);
				$result = false;
			}
		}

		return $result;
	}

/**
 * Extracting data from a log file.
 *
 * @param array &$errorMessages Array of error messages.
 * @param string $filePath Log file path.
 * @return array Data of logs.
 */
	protected function _extractDataFromLogFile(array &$errorMessages, $filePath = null) {
		$result = [];
		$oLogFile = new File($filePath);
		if (!$oLogFile->exists()) {
			$errorMessages['Error'][__('Invalid file for parsing')][] = $filePath;
			return $result;
		}

		$fileName = $oLogFile->name();
		if (!preg_match(LOG_PARSE_PCRE_FILE_NAME, $fileName, $matches)) {
			$errorMessages['Error'][__('Error on parsing log file name')][] = $fileName;
			return $result;
		}

		$logContent = $oLogFile->read();
		if (empty($logContent)) {
			return $result;
		}
		$oLogFile->close();

		$hostName = $matches[1];
		if (empty($hostName)) {
			return $result;
		}

		$hostName = mb_strtoupper($hostName);
		$hostId = $this->getIdFromNamesCache('LogHost', $hostName);
		if (!preg_match_all(LOG_PARSE_PCRE_CONTENT, $logContent, $logLines, PREG_SET_ORDER)) {
			$errorMessages['Error'][__('Error on parsing log file content')][] = $hostName;
			return $result;
		}

		$msgPatterns = [
			'/(http[s]?\:\/{2}[^\s]+)/',
			'/([^|][\w\s]+\:\s)/',
			'/(\'[^\']+\')/',
			'/\|$/',
			'/\|/',
			'/\s{2,}/'
		];
		$msgReplaces = [
			'<a target="_blank" href="$1">$1</a>',
			'<b>$1</b>',
			'<i>$1</i>',
			'',
			'<br />',
			' '
		];

		$prevDate = null;
		$prevTypeId = null;
		$prevMessage = '';
		$lastLine = count($logLines) - 1;
		$result = [];
		foreach ($logLines as $numLine => $logLine) {
			$message = trim($logLine[3]);
			if (empty($message)) {
				continue;
			}
			$message = iconv('CP1251', 'UTF-8', $message);

			$msgType = mb_strtoupper($logLine[2]);
			$typeId = $this->getIdFromNamesCache('LogType', $msgType, LOG_TYPE_DEBUG);
			$date = $logLine[1];

			$patternPCRE = [
				'packages' => [
					'patterns' => [],
					'cacheModel' => 'Package'
				],
				'profiles' => [
					'patterns' => [],
					'cacheModel' => 'Profile'
				],
				'hosts' => [
					'patterns' => [],
					'cacheModel' => 'Host'
				]
			];
			switch ($typeId) {
				case LOG_TYPE_INFORMATION:
					$patternPCRE['packages']['patterns'] = [
						LOG_PKG_PCRE_INFO_PACKAGE_NAME => ['name']
					];
					break;
				case LOG_TYPE_DEBUG:
					$patternPCRE['packages']['patterns'] = [
						LOG_PKG_PCRE_DEBUG_PACKAGE_ID_TEXT => ['id_text'],
						LOG_PKG_PCRE_DEBUG_PACKAGE_ID_TEXT_NAME => ['id_text', 'name']
					];
					$patternPCRE['profiles']['patterns'] = [
						LOG_PKG_PCRE_DEBUG_PROFILE_ID_TEXT => [null]
					];
					break;
				case LOG_TYPE_ERROR:
					$patternPCRE['packages']['patterns'] = [
						LOG_PKG_PCRE_ERROR_PACKAGE_NAME => ['name']
					];
					break;
			}

			foreach ($patternPCRE as $controller => $patternPCREcfg) {
				foreach ($patternPCREcfg['patterns'] as $pattern => $cacheKey) {
					$matches = [];
					if (!preg_match('/' . $pattern . '/iu', $message, $matches)) {
						continue;
					}

					foreach ($cacheKey as $cacheKeyItem) {
						$id = $this->getIdFromNamesCache($patternPCREcfg['cacheModel'], $matches[1], null, false, $cacheKeyItem);
						if (!empty($id)) {
							break;
						}
					}

					if (!empty($id)) {
						$pkgUrl = Router::url(['controller' => $controller, 'action' => 'view', $id, 'admin' => true]);
						$message = str_replace($matches[1], '<a target="_blank" href="' . $pkgUrl . '">' . $matches[1] . '</a>', $message);
					}
				}
			}
			if (empty($message)) {
				continue;
			}
			$message = preg_replace($msgPatterns, $msgReplaces, $message);
			if (($prevDate === $date) && ($prevTypeId === $typeId)) {
				if (!empty($prevMessage)) {
					$prevMessage .= '<br />';
				}
				$prevMessage .= $message;
				if ($numLine === $lastLine) {
					$message = $prevMessage;
				} else {
					continue;
				}
			} else {
				$prevDate = $date;
				$prevTypeId = $typeId;
				$prevMessage = $message;
			}

			$dataToSave = [
				$this->alias => [
					'type_id' => $typeId,
					'message' => $message,
					'date' => $date
				]
			];
			if (!empty($hostId)) {
				$dataToSave[$this->alias]['host_id'] = $hostId;
			}
			$this->create($dataToSave);
			if ($this->validates()) {
				$dataToSave[$this->LogHost->alias]['name'] = $hostName;
				$dataToSave[$this->LogType->alias]['name'] = $msgType;
				$result[] = $dataToSave;
			} else {
				$errorType = $this->getFullName($dataToSave);
				$errorMessages['Errors'][$errorType][] = $this->validationErrors;
			}
		}

		return $result;
	}

/**
 * Preparing data from a log files to save.
 *
 * @param array &$errorMessages Array of error messages.
 * @param array $logFiles List of log files path.
 * @return array Data of logs.
 */
	protected function _prepareLogData(array &$errorMessages, $logFiles = null) {
		$result = [];
		if (empty($logFiles)) {
			return $result;
		}

		foreach ($logFiles as $logFilePath) {
			$logData = $this->_extractDataFromLogFile($errorMessages, $logFilePath);
			if (!empty($logData)) {
				$result = array_merge($result, $logData);
			}
		}

		return $result;
	}

/**
 * Clear logs.
 *
 * @param int|string|null $id Record ID of host
 * @return bool Success
 */
	public function clearLog($id = null) {
		$dataSource = $this->getDataSource();
		$dataSource->begin();
		$result = true;
		if (empty($id)) {
			if (!$dataSource->truncate($this)) {
				$result = false;
			}
			if (!$dataSource->truncate($this->LogHost)) {
				$result = false;
			}
		} else {
			$conditions = [
				$this->alias . '.host_id' => $id,
			];

			if (!$this->deleteAll($conditions, false, false)) {
				$result = false;
			}
			if (!$this->LogHost->delete($id)) {
				$result = false;
			}
		}
		if ($result) {
			Cache::clear(false, CACHE_KEY_STATISTICS_INFO_LOG);
		}

		if ($result) {
			$dataSource->commit();
		} else {
			$dataSource->rollback();
		}

		return $result;
	}

/**
 * Returns the number of errors in the logs.
 *
 * @return int Number of errors
 */
	public function getNumberErrors() {
		$conditions = [
			$this->alias . '.type_id' => LOG_TYPE_ERROR
		];

		return $this->getNumberOf($conditions);
	}

/**
 * Return a list of classes for log record state.
 *
 * @return array Return array list of classes for log
 *  record state.
 */
	public function getListBarStateClass() {
		$result = [
			LOG_TYPE_SUCCESS => 'progress-bar-success',
			LOG_TYPE_INFORMATION => 'progress-bar-info',
			LOG_TYPE_DEBUG => 'progress-bar-info progress-bar-striped',
			LOG_TYPE_WARNING => 'progress-bar-warning',
			LOG_TYPE_ERROR => 'progress-bar-danger',
		];

		return $result;
	}

/**
 * Return information about log records for bar state
 *
 * @return array Return array information about log records.
 */
	public function getBarStateInfo() {
		$result = [];
		$fields = [
			'count(*) AS amount',
			'LogType.id',
			'LogType.name',
		];
		$group = 'LogType.name';
		$order = ['LogType.id' => 'asc'];
		$contain = ['LogType'];
		$data = $this->find('all', compact('fields', 'group', 'order', 'contain'));
		if (empty($data)) {
			return $result;
		}

		$taskClassList = $this->getListBarStateClass();
		$controller = $this->getControllerName();
		foreach ($data as $dataItem) {
			$stateId = $dataItem['LogType']['id'];
			$stateName = __d('log', h($dataItem['LogType']['name']));
			$class = Hash::get($taskClassList, $stateId);
			$amount = (int)$dataItem[0]['amount'];
			$stateUrl = ['controller' => $controller, 'action' => 'index',
				'admin' => true,
				'?' => ['data[FilterData][0][' . $this->alias . '][type_id]' => $stateId]];
			$result[] = compact('stateName', 'stateId', 'amount', 'stateUrl', 'class');
		}

		return $result;
	}
}
