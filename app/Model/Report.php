<?php
/**
 * This file is the model file of the application. Used to
 *  manage reports.
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
 * The model is used to manage reports.
 *
 * @package app.Model
 */
class Report extends AppModel {

/**
 * Custom display field name.
 *
 * @var string
 * @link https://book.cakephp.org/2.0/en/models/model-attributes.html#displayfield
 */
	public $displayField = 'revision';

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
		'GetNumber' => ['cacheConfig' => CACHE_KEY_STATISTICS_INFO_REPORT],
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
		'state_id' => [
			'isinteger' => [
				'rule' => 'numeric',
				'message' => "The report's type is invalid.",
				'last' => true
			],
			'validrange' => [
				'rule' => ['checkRange', 'REPORT_STATE_', false],
				'message' => "The report's type is invalid.",
				'last' => true
			]
		],
		'host_id' => [
			'rule' => ['naturalNumber'],
			'message' => 'Incorrect foreign key',
			'allowEmpty' => false,
			'required' => false,
			'last' => true,
		],
		'package_id' => [
			'naturalNumber' => [
				'rule' => ['naturalNumber'],
				'message' => 'Incorrect foreign key',
				'allowEmpty' => false,
				'required' => false,
				'last' => true,
			],
			'isUnique' => [
				'rule' => [
					'isUnique',
					[
						'host_id',
						'package_id'
					],
					false
				],
				'on' => 'create',
				'required' => true,
				'message' => 'That package for this host already exists.',
				'last' => true
			],
		],
		'revision' => [
			'rule' => 'notBlank',
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Package revision is invalid.'
		]
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
		'ReportState' => [
			'className' => 'ReportState',
			'foreignKey' => 'state_id',
			'conditions' => '',
			'fields' => ['ReportState.name'],
			'order' => ''
		],
		'ReportHost' => [
			'className' => 'ReportHost',
			'foreignKey' => 'host_id',
			'conditions' => '',
			'fields' => [
				'ReportHost.name',
				'ReportHost.date'
			],
			'order' => ''
		],
		'Package' => [
			'className' => 'Package',
			'foreignKey' => 'package_id',
			'conditions' => '',
			'fields' => [
				'Package.id',
				'Package.enabled',
				'Package.id_text',
				'Package.name',
				'Package.revision',
			],
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

		if (!isset($order['Package.name'])) {
			$order['Package.name'] = 'asc';
		}
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
		$result = __('Report');

		return $result;
	}

/**
 * Return name of group data.
 *
 * @return string Return name of group data
 */
	public function getGroupName() {
		$result = __('Reports');

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
			if (!isset($id[$this->ReportHost->alias][$this->ReportHost->displayField])) {
				return false;
			}
			return $id[$this->ReportHost->alias][$this->ReportHost->displayField];
		}

		$fields = [
			'ReportHost.' . $this->ReportHost->displayField,
		];
		$conditions = [$this->alias . '.id' => $id];
		$contain = ['ReportHost'];
		$data = $this->find('first', compact('conditions', 'fields', 'contain'));
		if (empty($data)) {
			return false;
		}

		$result = $data['ReportHost'][$this->ReportHost->displayField];

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
		$result = __('Report of host %s', $name);

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
 * Return PCRE pattern for parse client database file name.
 *
 * @param string|null $hostName Host name for parsing client
 *  database file.
 * @return string Return PCRE pattern.
 */
	protected function _getDbFilePattern($hostName = null) {
		if (empty($hostName)) {
			$dbFilePattern = '^.*\.xml$';
		} else {
			$dbFilePattern = '^' . preg_quote($hostName, '/') . '\.xml$';
		}

		return $dbFilePattern;
	}

/**
 * Parsing client database files for create reports.
 *
 * @param string|null $hostName Host name for parsing client
 *  database files.
 * @param int $idTask The ID of the QueuedTask
 * @return bool Success.
 */
	public function createReports($hostName = null, $idTask = null) {
		$step = 0;
		$maxStep = 1;
		$errorMessages = [];
		$result = true;
		set_time_limit(CLIENT_DATABASE_PARSE_TIME_LIMIT);

		$this->_modelExtendQueuedTask->updateProgress($idTask, 0);
		$oLocalReportDir = new Folder(REPORT_DIR, true, 0755);

		list(, $localFiles) = $oLocalReportDir->read(false, false, true);
		if (!empty($localFiles)) {
			array_map('unlink', $localFiles);
		}

		$dbFilePattern = $this->_getDbFilePattern($hostName);

		$modelSetting = ClassRegistry::init('Setting');
		$user = $modelSetting->getConfig('SmbAuthUser');
		$pswd = $modelSetting->getConfig('SmbAuthPassword');
		$workgroup = $modelSetting->getConfig('SmbWorkgroup');
		$host = $modelSetting->getConfig('SmbServer');
		$shareName = $modelSetting->getConfig('SmbDbShare');

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

			$fileName = $info->getName();
			if (!preg_match('/' . $dbFilePattern . '/i', $fileName)) {
				continue;
			}
			$aRemoteFiles[] = $fileName;
		}

		if (empty($aRemoteFiles)) {
			$this->_modelExtendQueuedTask->updateTaskErrorMessage($idTask, __('No database files to parsing.'));
			$this->_modelExtendQueuedTask->updateProgress($idTask, 1);

			return true;
		}

		foreach ($aRemoteFiles as $fileName) {
			$localFile = REPORT_DIR . uniqid('db_');
			if (!$share->get($fileName, $localFile)) {
				$errorMessages['Errors'][__('Error on copying files')][] = $fileName;
			}
		}
		if (!empty($errorMessages)) {
			$errorMessagesText = $this->renderErrorMessages($errorMessages);
			$this->_modelExtendQueuedTask->updateTaskErrorMessage($idTask, $errorMessagesText, true);
			return false;
		}

		list(, $localFiles) = $oLocalReportDir->read(true, false, true);
		if (empty($localFiles)) {
			$this->_modelExtendQueuedTask->updateTaskErrorMessage($idTask, __('No database files to parsing.'));
			$this->_modelExtendQueuedTask->updateProgress($idTask, 1);

			return true;
		}

		$modelImport = ClassRegistry::init('Import');
		$cacheLastUpdate = $this->ReportHost->getListLastUpdate();
		$maxStep += count($localFiles);
		foreach ($localFiles as $i => $localFilePath) {
			if (!$modelImport->importXmlDatabases($localFilePath, $idTask, $cacheLastUpdate)) {
				$result = false;
			}

			$step++;
			if ($step % 10 == 0) {
				$this->_modelExtendQueuedTask->updateTaskProgress($idTask, $step, $maxStep);
			}
		}

		if (!$this->ReportHost->clearUnusedHosts()) {
			$errorMessages['Errors'][] = __('Error on removing unused hosts');
			$result = false;
		}
		$step = $maxStep - 1;
		$this->_modelExtendQueuedTask->updateTaskProgress($idTask, $step, $maxStep);

		return $result;
	}

/**
 * Rename client database file
 *
 * @param string|null $hostName Host name for rename client
 *  database file.
 * @return bool Success.
 */
	public function renameDbFile($hostName = null) {
		if (empty($hostName)) {
			return false;
		}

		$dbFilePattern = $this->_getDbFilePattern($hostName);

		$modelSetting = ClassRegistry::init('Setting');
		$user = $modelSetting->getConfig('SmbAuthUser');
		$pswd = $modelSetting->getConfig('SmbAuthPassword');
		$workgroup = $modelSetting->getConfig('SmbWorkgroup');
		$host = $modelSetting->getConfig('SmbServer');
		$share = $modelSetting->getConfig('SmbDbShare');

		$auth = new \Icewind\SMB\BasicAuth($user, $workgroup, $pswd);
		$serverFactory = new \Icewind\SMB\ServerFactory();
		$server = $serverFactory->createServer($host, $auth);
		$share = $server->getShare($share);
		$files = $share->dir('/');
		$dbFileNamePath = null;
		foreach ($files as $info) {
			if ($info->isDirectory()) {
				continue;
			}

			$fileName = $info->getName();
			if (preg_match('/' . $dbFilePattern . '/i', $fileName)) {
				$dbFileNamePath = $info->getPath();
				break;
			}
		}
		if (empty($dbFileNamePath)) {
			return false;
		}

		$from = $dbFileNamePath;
		$to = $dbFileNamePath . '.bkp';
		return $share->rename($from, $to);
	}

/**
 * Clear report.
 *
 * @param int|string|null $id Record ID of host
 * @return bool Success
 */
	public function clearReport($id = null) {
		$dataSource = $this->getDataSource();
		$dataSource->begin();
		$result = true;
		if (empty($id)) {
			if (!$dataSource->truncate($this)) {
				$result = false;
			}
			if (!$dataSource->truncate($this->ReportHost)) {
				$result = false;
			}
		} else {
			$conditions = [
				$this->alias . '.host_id' => $id,
			];

			if (!$this->deleteAll($conditions, false, false)) {
				$result = false;
			}
			if (!$this->ReportHost->delete($id)) {
				$result = false;
			}
		}

		if (!$this->ReportHost->removeHostAttributes($id)) {
			$result = false;
		}
		if ($result) {
			Cache::clear(false, CACHE_KEY_STATISTICS_INFO_REPORT);
		}

		if ($result) {
			$dataSource->commit();
		} else {
			$dataSource->rollback();
		}

		return $result;
	}

/**
 * Deleting report records.
 *
 * @param array $dataToDelete Array Data to delete.
 * @return bool Success.
 */
	public function deleteReportRecords($dataToDelete = []) {
		if (empty($dataToDelete)) {
			return true;
		}

		$conditions = [$this->alias . '.id' => $dataToDelete];
		return $this->deleteAll($conditions, false);
	}

/**
 * Return a list of classes for report record state.
 *
 * @return array Return array list of classes for report
 *  record state.
 */
	public function getListBarStateClass() {
		$result = [
			REPORT_STATE_OK => 'progress-bar-success',
			REPORT_STATE_OK_MANUAL => 'progress-bar-success progress-bar-striped',
			REPORT_STATE_UPGRADE => 'progress-bar-info',
			REPORT_STATE_DOWNGRADE => 'progress-bar-warning',
		];

		return $result;
	}

/**
 * Return information about report records for bar state
 *
 * @return array Return array information about report records.
 */
	public function getBarStateInfo() {
		$result = [];
		$fields = [
			'count(*) AS amount',
			'ReportState.id',
			'ReportState.name',
		];
		$group = 'ReportState.name';
		$order = ['ReportState.id' => 'asc'];
		$contain = ['ReportState'];
		$data = $this->find('all', compact('fields', 'group', 'order', 'contain'));
		if (empty($data)) {
			return $result;
		}

		$taskClassList = $this->getListBarStateClass();
		$controller = $this->getControllerName();
		foreach ($data as $dataItem) {
			$stateId = $dataItem['ReportState']['id'];
			$stateName = __d('report_state', h($dataItem['ReportState']['name']));
			$class = Hash::get($taskClassList, $stateId);
			$amount = (int)$dataItem[0]['amount'];
			$stateUrl = ['controller' => $controller, 'action' => 'index',
				'admin' => true,
				'?' => ['data[FilterData][0][' . $this->alias . '][state_id]' => $stateId]];
			$result[] = compact('stateName', 'stateId', 'amount', 'stateUrl', 'class');
		}

		return $result;
	}

/**
 * Return list of packages for host by host name
 *
 * @param string|null $hostName Host name to retrieve data
 * @return array|bool Return list of packages.
 */
	public function getListPackagesForHost($hostName = null) {
		$result = [];
		if (empty($hostName)) {
			return $result;
		}
		$conditions = ['ReportHost.name' => $hostName];
		$fields = [
			$this->alias . '.package_id',
			$this->alias . '.id',
		];
		$order = [$this->alias . '.package_id' => 'asc'];
		$contain = ['ReportHost'];

		return $this->find('list', compact('conditions', 'fields', 'order', 'contain'));
	}

/**
 * Return parameters for clearCache
 *
 * @param int|string $id Record ID to retrieve parameters
 * @return string Return parameters for clearCache
 */
	public function getParamClearCache($id = null) {
		return false;
	}
}
