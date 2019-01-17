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
App::uses('CakeNumber', 'Utility');
App::uses('RenderXmlData', 'Utility');
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
		$orderDir = 'asc';
		if (!is_array($order)) {
			$order = [$order];
		} else {
			$orderDir = reset($order);
		}

		$order[$this->alias . '.id'] = $orderDir;
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
				$errorMessages[__('Errors')][__('Error on retrieving log file name')][] = $fileName;
				continue;
			}

			$localFile = LOG_DIR . $fileName;
			if ($share->get($filePath, $localFile)) {
				try {
					if (!$share->del($filePath)) {
						unlink($localFile);
					}
				} catch (Exception $e) {
					unlink($localFile);
				}
			} else {
				$errorMessages[__('Errors')][__('Error on copying files')][] = $filePath;
			}
		}
		if (!empty($errorMessages)) {
			$errorMessagesText = RenderXmlData::renderErrorMessages($errorMessages);
			$this->_modelExtendQueuedTask->updateTaskErrorMessage($idTask, $errorMessagesText, true);
			return false;
		}

		list(, $localFiles) = $oLocalLogDir->read(true, false, true);
		if (empty($localFiles)) {
			$this->_modelExtendQueuedTask->updateTaskErrorMessage($idTask, __('No log files to parsing.'));

			return true;
		}

		$modelImport = ClassRegistry::init('Import');
		$maxStep += count($localFiles);
		$dataToEmail = [];
		foreach ($localFiles as $i => $localFilePath) {
			$resultImport = $modelImport->importTextLogs($localFilePath, $idTask, false);
			if (!$resultImport) {
				$result = false;
			} elseif (is_array($resultImport)) {
				$dataToEmail[] = $resultImport;
			}

			$step++;
			if ($step % 10 == 0) {
				$this->_modelExtendQueuedTask->updateTaskProgress($idTask, $step, $maxStep);
			}
		}

		if (!empty($dataToEmail) && !$this->_sendEmail($errorMessages, $dataToEmail)) {
			$result = false;
		}
		if (!$this->LogHost->clearUnusedHosts()) {
			$errorMessages[__('Errors')][] = __('Error on removing unused hosts');
			$result = false;
		}

		$step = $maxStep - 1;
		$this->_modelExtendQueuedTask->updateTaskProgress($idTask, $step, $maxStep);
		if (!empty($idTask) && !empty($errorMessages)) {
			$errorMessagesText = RenderXmlData::renderErrorMessages($errorMessages);
			$this->_modelExtendQueuedTask->updateTaskErrorMessage($idTask, $errorMessagesText, true);
		}

		return $result;
	}

/**
 * Preparing data of records for log for E-mail.
 *
 * @param array $data Data array for processing
 * @param int $limitLogs Limit for logs.
 * @param int $limitRecords Limit for record of log.
 * @return array Information of for E-mail.
 */
	protected function _prepareEmailData($data = [], $limitLogs = EMAIL_REPORT_ERRORS_SHOW_LOGS_LIMIT, $limitRecords = EMAIL_REPORT_ERRORS_SHOW_RECORDS_LIMIT) {
		if (empty($data)) {
			return false;
		}

		$moreLogs = count($data) - $limitLogs;
		if ($moreLogs < 0) {
			$moreLogs = 0;
		}
		$logsInfo = array_slice($data, 0, $limitLogs);
		if (count($logsInfo) == 0) {
			return false;
		}

		$logs = [];
		$listLogTypes = $this->LogType->getList();
		foreach ($logsInfo as $logInfoFull) {
			if (!isset($logInfoFull['Log']) || empty($logInfoFull['Log']) ||
				!isset($logInfoFull['LogHost']) || empty($logInfoFull['LogHost'])) {
				continue;
			}

			$logInfoPart = array_slice($logInfoFull['Log'], 0, $limitRecords);
			$moreRecords = count($logInfoFull['Log']) - $limitRecords;
			foreach ($logInfoPart as $logRecord) {
				$logType = [
					'LogType' => [
						'name' => Hash::get($listLogTypes, $logRecord['Log']['type_id'])
					]
				];
				$logs[] = $logRecord + $logInfoFull['LogHost'] + $logType;
			}
			if ($moreRecords <= 0) {
				continue;
			}

			$logRecord['Log']['type_id'] = LOG_TYPE_INFORMATION;
			$logRecord['Log']['message'] = '<i>' . __(
				'...And %s more %s',
				CakeNumber::format($moreRecords, ['thousands' => ' ', 'before' => '', 'places' => 0]),
				__n('record', 'records', $moreLogs) . '</i>'
			);
			$logType = [
				'LogType' => [
					'name' => Hash::get($listLogTypes, LOG_TYPE_INFORMATION)
				]
			];
			$logs[] = $logRecord + $logInfoFull['LogHost'] + $logType;
		}

		$result = compact('logs', 'moreLogs');
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
			return false;
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

		$preparedData = $this->_prepareEmailData($data);
		if (!$preparedData) {
			return false;
		}
		extract($preparedData);

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
			'moreLogs',
			'projectName'
		);
		foreach ($listEmails as $email => $name) {
			$to = [$email, $name];
			if (!$modelSendEmail->putQueueEmail(compact('config', 'from', 'to', 'subject', 'template', 'vars', 'helpers'))) {
				$errorMessages[__('Errors')][__('Error on sending e-mail')][] = __('Error on putting sending e-mail for "%s" in queue...', $to);
				$result = false;
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
