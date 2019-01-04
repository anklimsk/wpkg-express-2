<?php
/**
 * This file is the model file of the application. Used to
 *  manage hosts.
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
App::uses('CheckPcrePattern', 'Utility');

/**
 * The model is used to manage hosts.
 *
 * @package app.Model
 */
class Host extends AppModel {

/**
 * Custom display field name.
 *
 * @var string
 * @link https://book.cakephp.org/2.0/en/models/model-attributes.html#displayfield
 */
	public $displayField = 'id_text';

/**
 * List of behaviors to load when the model object is initialized.
 *
 * @var array
 * @link https://book.cakephp.org/2.0/en/models/behaviors.html#using-behaviors
 */
	public $actsAs = [
		'Tree',
		'MoveExt',
		'TrimStringField',
		'GetXmlInfo',
		'BreadCrumbExt',
		'GetInfo',
		'GetList' => ['cacheConfig' => CACHE_KEY_LISTS_INFO_HOST],
		'GetNumber' => ['cacheConfig' => CACHE_KEY_STATISTICS_INFO_HOST],
		'GroupAction',
		'ChangeState',
		'GetGraphInfo',
		'TemplateData',
		'ClearViewCache'
	];

/**
 * List of validation rules. It must be an array with the field name as key and using
 * as value one of the following possibilities
 *
 * @var array
 * @link https://book.cakephp.org/2.0/en/models/model-attributes.html#validate
 * @link https://book.cakephp.org/2.0/en/models/data-validation.html
 */
	public $validate = [
		'id_text' => [
			'validRegex' => [
				'rule' => ['checkRegex'],
				'required' => true,
				'allowEmpty' => false,
				'message' => "The host's name is an invalid regular expression.",
				'last' => true
			],
			'uniqueName' => [
				'rule' => ['isUnique'],
				'message' => 'That host already exists.',
				'last' => true
			]
		],
		'mainprofile_id' => [
			'rule' => 'naturalNumber',
			'message' => 'Incorrect foreign key',
			'allowEmpty' => false,
			'required' => true,
			'last' => true,
		]
	];

/**
 * Detailed list of hasMany associations.
 *
 * @var array
 * @link https://book.cakephp.org/2.0/en/models/associations-linking-models-together.html#hasmany
 */
	public $hasMany = [
		'HostsProfile' => [
			'className' => 'HostsProfile',
			'foreignKey' => 'host_id',
			'dependent' => true,
			'fields' => [
				'HostsProfile.id',
				'HostsProfile.host_id',
				'HostsProfile.profile_id'
			]
		],
		'Variable' => [
			'className' => 'Variable',
			'foreignKey' => 'ref_id',
			'dependent' => true,
			'conditions' => ['ref_type' => VARIABLE_TYPE_HOST],
			'order' => ['Variable.lft' => 'asc'],
			'fields' => [
				'Variable.id',
				'Variable.ref_type',
				'Variable.name',
				'Variable.value'
			]
		],
		'Attribute' => [
			'className' => 'Attribute',
			'foreignKey' => 'ref_id',
			'dependent' => true,
			'conditions' => [
				'ref_type' => ATTRIBUTE_TYPE_HOST,
				'ref_node' => ATTRIBUTE_NODE_HOST
			],
			'fields' => [
				'Attribute.pcre_parsing',
				'Attribute.hostname',
				'Attribute.os',
				'Attribute.architecture',
				'Attribute.ipaddresses',
				'Attribute.domainname',
				'Attribute.groups',
				'Attribute.lcid',
				'Attribute.lcidOS'
			]
		],
	];

/**
 * Detailed list of belongsTo associations.
 *
 * @var array
 * @link https://book.cakephp.org/2.0/en/models/associations-linking-models-together.html#belongsto
 */
	public $belongsTo = [
		'MainProfile' => [
			'className' => 'Profile',
			'foreignKey' => 'mainprofile_id',
			'fields' => [
				'MainProfile.id',
				'MainProfile.enabled',
				'MainProfile.id_text'
			]
		]
	];

/**
 * PCRE pattern validation
 *
 * @param array $data Data to check
 * @return bool Return True, if valid
 */
	public function checkRegex($data = null) {
		$value = reset($data);
		return CheckPcrePattern::checkPattern($value);
	}

/**
 * Returns a list of all events that will fire in the model during it's lifecycle.
 * Add listener callbacks for events `Model.afterUpdateTree`.
 *
 * @return array
 */
	public function implementedEvents() {
		$events = parent::implementedEvents();
		$events['Model.afterUpdateTree'] = ['callable' => 'afterUpdateTree'];
		return $events;
	}

/**
 * Called after each successful update tree operation.
 *
 * Actions:
 *  - Clear View cache after drag and drop.
 *
 * @return void 
 */
	public function afterUpdateTree() {
		$this->clearCache(null, false);
	}

/**
 * Called before each save operation, after validation.
 *
 * Actions:
 *  - Set field `parent_id` to Null;
 *  - Reset flag using as template if not set.
 *
 * @param array $options Options passed from Model::save().
 * @return bool True if the operation should continue, false if it should abort
 * @link https://book.cakephp.org/2.0/en/models/callback-methods.html#beforesave
 * @see Model::save()
 */
	public function beforeSave($options = []) {
		$this->data[$this->alias]['parent_id'] = null;
		if (!isset($this->data[$this->alias]['template'])) {
			$this->data[$this->alias]['template'] = false;
		}

		return true;
	}

/**
 * Called before every deletion operation.
 *
 * Actions:
 *  - Store data as garbage.
 *
 * @param bool $cascade If true records that depend on this record will also be deleted
 * @return bool True if the operation should continue, false if it should abort
 * @link https://book.cakephp.org/2.0/en/models/callback-methods.html#beforedelete
 */
	public function beforeDelete($cascade = true) {
		$modelGarbage = ClassRegistry::init('Garbage');
		return $modelGarbage->storeData(GARBAGE_TYPE_HOST, $this->id);
	}

/**
 * Saving host information use transactions.
 *
 * @param array $data Array information host to save.
 * @return bool Success.
 */
	public function saveHost($data = []) {
		$result = true;
		$dataSource = $this->getDataSource();
		$dataSource->begin();

		$this->bindHabtmAssocProfiles();
		$result = $this->saveAll($data);
		if ($result) {
			if (!$this->HostsProfile->Attribute->clearUnusedAttributes(ATTRIBUTE_TYPE_HOST, ATTRIBUTE_NODE_PROFILE)) {
				$result = false;
			}
		}
		if ($result) {
			$dataSource->commit();
		} else {
			$dataSource->rollback();
		}

		return $result;
	}

/**
 * Return information of host
 *
 * @param int|string $id The ID of the record to read.
 * @param bool $full Flag of inclusion in the result
 *  full information.
 * @return array|bool Return information of host,
 *  or False on failure.
 */
	public function get($id = null, $full = true) {
		if (empty($id)) {
			return false;
		}

		$fields = [
			$this->alias . '.id',
			$this->alias . '.mainprofile_id',
			$this->alias . '.lft',
			$this->alias . '.enabled',
			$this->alias . '.template',
			$this->alias . '.id_text',
			$this->alias . '.notes',
			$this->alias . '.created',
			$this->alias . '.modified',
		];
		$conditions = [$this->alias . '.id' => $id];
		$contain = [
			'MainProfile',
		];
		if ($full) {
			$containExt = [
				'HostsProfile.Profile',
				'HostsProfile.Attribute' => ['fields' => '*'],
				'Variable',
				'Variable.Attribute' => ['fields' => '*'],
				'Variable.Check',
				'Variable.Check.Attribute' => ['fields' => '*'],
				'Attribute' => ['fields' => '*'],
			];
			$contain = array_merge($contain, $containExt);
		} else {
			$contain[] = 'Profile';
			$this->bindHabtmAssocProfiles();
		}

		$result = $this->find('first', compact('conditions', 'fields', 'contain'));
		if (empty($result) || !$full) {
			return $result;
		}

		$result = $this->HostsProfile->sortDependencyData($result);

		return $result;
	}

/**
 * Return default values of host
 *
 * @param bool $includeModelAlias Flag of including the model alias in the result
 * @return array Return default values of host.
 */
	public function getDefaultValues($includeModelAlias = true) {
		$defaultValues = [
			'mainprofile_id' => null,
			'parent_id' => null,
			'enabled' => true,
			'template' => false,
			'id_text' => '',
			'notes' => ''
		];
		if ($includeModelAlias) {
			$defaultValues = [$this->alias => $defaultValues];
		}

		return $defaultValues;
	}

/**
 * Return data array for XML
 *
 * @param int|string $id The ID of the record to retrieve data.
 * @param bool $exportnotes Flag of inclusion in the result
 *  notes of host.
 * @param bool $exportdisabled Flag of inclusion in the result
 *  disabled hosts.
 * @return array Return data array for XML
 */
	public function getAllForXML($id = null, $exportnotes = false, $exportdisabled = false) {
		$conditions = [];
		if (!$exportdisabled) {
			$conditions[$this->alias . '.enabled'] = true;
		}
		if (!empty($id)) {
			$conditions[$this->alias . '.id'] = $id;
		}

		$fields = [
			$this->alias . '.id',
			$this->alias . '.id_text',
			$this->alias . '.enabled',
			$this->alias . '.template',
		];
		if ($exportnotes) {
			$fields[] = $this->alias . '.notes';
		}

		$order = [$this->alias . '.lft' => 'asc'];
		$contain = [
			'MainProfile' => [
				'fields' => ['MainProfile.id_text']
			],
			'Attribute',
			'Variable',
			'Variable.Attribute',
			'Variable.Check',
			'Variable.Check.Attribute',
			'HostsProfile.Profile',
			'HostsProfile.Attribute'
		];

		return $this->find('all', compact('conditions', 'fields', 'order', 'contain'));
	}

/**
 * Return data array for render XML
 *
 * @param int|string $id The ID of the record to retrieve data.
 * @param bool $exportdisable The flag of disable data export to XML.
 * @param bool $exportnotes Flag of inclusion in the result
 *  notes of host.
 * @param bool $exportdisabled Flag of inclusion in the result
 *  disabled hosts.
 * @return array Return data array for render XML
 * @see RenderXmlData::renderXml()
 */
	public function getXMLdata($id = null, $exportdisable = false, $exportnotes = false, $exportdisabled = false) {
		$baseUrl = Configure::read('App.fullBaseUrl');
		$result = [
			'hosts:wpkg' => [
				'xmlns:hosts' => 'http://www.wpkg.org/hosts',
				'xmlns:xsi' => 'http://www.w3.org/2001/XMLSchema-instance',
				'@xsi:schemaLocation' => 'http://www.wpkg.org/hosts ' . $baseUrl . '/xsd/hosts.xsd'
			]];
		if ($exportdisable) {
			$xmlItemArray = [
				'@name' => 'STOP_EXPORT_XML_HOST',
				'@profile-id' => 'STOP_EXPORT_XML_PROFILE'
			];
			$result['hosts:wpkg']['host'][] = $xmlItemArray;
			return $result;
		}

		$hosts = $this->getAllForXML($id, $exportnotes, $exportdisabled);
		if (empty($hosts)) {
			return $result;
		}

		foreach ($hosts as $host) {
			if (!$host[$this->alias]['enabled'] && !$exportdisabled) {
				continue;
			}

			$hostAttribs = [
				'@name' => $host[$this->alias]['id_text'],
				'@profile-id' => $host['MainProfile']['id_text']
			];

			if (isset($host['Attribute'])) {
				$hostAttribs += $this->Attribute->getXMLnodeAttr($host['Attribute']);
			}

			if (isset($host[$this->alias]['notes']) && !empty($host[$this->alias]['notes'])) {
				$hostAttribs[XML_SPECIFIC_TAG_NOTES] = preg_replace('/[\-]{2,}/', '-', $host[$this->alias]['notes']);
			}

			if (isset($host[$this->alias]['template']) && $host[$this->alias]['template']) {
				$hostAttribs[XML_SPECIFIC_TAG_TEMPLATE] = __('Use as template');
			}

			$xmlItemArray = $hostAttribs;
			if (isset($host['Variable'])) {
				$xmlItemArray += $this->Variable->getXMLdata($host['Variable']);
			}

			if (isset($host['HostsProfile'])) {
				$xmlItemArray += $this->HostsProfile->getXMLdata($host['HostsProfile']);
			}

			if (!$host[$this->alias]['enabled'] && $exportdisabled) {
				$result['hosts:wpkg'][XML_SPECIFIC_TAG_DISABLED]['host'][] = $xmlItemArray;
			} else {
				$result['hosts:wpkg']['host'][] = $xmlItemArray;
			}
		}

		return $result;
	}

/**
 * Temporarily bind an additional new HABTM relationship,
 *  which gives us which additional associated profiles of host.
 *
 * @return bool Success.
 */
	public function bindHabtmAssocProfiles() {
		$hasAndBelongsToMany = $this->getAssociated('hasAndBelongsToMany');
		if (!empty($hasAndBelongsToMany) && in_array('Profile', $hasAndBelongsToMany)) {
			return true;
		}

		$result = $this->bindModel(
			[
				'hasAndBelongsToMany' => [
					'Profile' => [
						'className' => 'Profile',
						'joinTable' => 'hosts_profiles',
						'foreignKey' => 'host_id',
						'associationForeignKey' => 'profile_id',
						'unique' => 'keepExisting',
						'order' => ['Profile.id_text' => 'asc']
					]
				]
			],
			false
		);

		return $result;
	}

/**
 * Return download name from XML data array
 *
 * @param array $xmlDataArray Array of XML data
 * @param bool $isFullData Flag of full data
 * @return string Return download name
 */
	public function getDownloadName($xmlDataArray = [], $isFullData = false) {
		$nameXpath = $this->getIdAttributeXpath();
		return $this->getDownloadNameFromXml($xmlDataArray, $nameXpath, $isFullData);
	}

/**
 * Return name from XML data array
 *
 * @param array $xmlDataArray Array of XML data
 * @return string Return name
 */
	public function getNameFromXml($xmlDataArray = []) {
		$nameXpath = $this->getIdAttributeXpath();
		return $this->getAttributeValueFromXml($xmlDataArray, $nameXpath);
	}

/**
 * Return Xpath for `name` attribute of host
 *
 * @return string Return Xpath
 */
	public function getIdAttributeXpath() {
		$idXpath = 'hosts:wpkg.host.0.@name';
		return $idXpath;
	}

/**
 * Return Xpath for `profile-id` attribute of host
 *
 * @return string Return Xpath
 */
	public function getAdditionalAttributeXpath() {
		$idXpath = 'hosts:wpkg.host.0.@profile-id';
		return $idXpath;
	}

/**
 * Return Xpath for `template` element of host
 *
 * @return string Return Xpath
 */
	public function getTemplateElementXpath() {
		$idXpath = 'hosts:wpkg.host.0.template';
		return $idXpath;
	}

/**
 * Return name of data.
 *
 * @return string Return name of data
 */
	public function getTargetName() {
		$result = __('Host');

		return $result;
	}

/**
 * Return name of group data.
 *
 * @return string Return name of group data
 */
	public function getGroupName() {
		$result = __('Hosts');
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
		$name = $id;
		if (is_array($id) || ctype_digit((string)$id)) {
			$name = (string)$this->getName($id);
		}
		if (empty($name)) {
			return false;
		}
		if ($primary) {
			$result = __("Host '%s'", $name);
		} else {
			$result = __("host '%s'", $name);
		}

		return $result;
	}

/**
 * Return full name of data.
 *
 * @return string|bool Return full name of data.
 */
	public function getFullDataName() {
		$result = __('full data of hosts');
		return $result;
	}

/**
 * Remove associated information.
 *
 * @param int|string $id Record ID to remove associated information.
 * @return bool Success
 */
	protected function _removeAssocData($id = null) {
		if (empty($id)) {
			return false;
		}

		$conditions = [
			$this->Variable->alias . '.ref_id' => $id,
			$this->Variable->alias . '.ref_type' => VARIABLE_TYPE_HOST
		];
		if (!$this->Variable->setScopeModel(VARIABLE_TYPE_HOST, $id) ||
			!$this->Variable->deleteAll($conditions, true, false)) {
			return false;
		}
		$conditions = [
			$this->Attribute->alias . '.ref_id' => $id,
			$this->Attribute->alias . '.ref_type' => ATTRIBUTE_TYPE_HOST,
			$this->Attribute->alias . '.ref_node' => ATTRIBUTE_NODE_HOST
		];
		if (!$this->Attribute->deleteAll($conditions, true, false)) {
			return false;
		}
		$conditions = [$this->HostsProfile->alias . '.host_id' => $id];
		if (!$this->HostsProfile->deleteAll($conditions, true, false)) {
			return false;
		}

		return true;
	}

/**
 * Remove associated information.
 *
 * @param int|string $id Record ID to remove associated information.
 * @return bool Success
 */
	public function removeAssocData($id = null) {
		if (empty($id)) {
			return false;
		}

		$dataSource = $this->getDataSource();
		$dataSource->begin();
		$result = $this->_removeAssocData($id);
		if ($result) {
			$dataSource->commit();
		} else {
			$dataSource->rollback();
		}

		return $result;
	}

/**
 * Return data array for graph
 *
 * @param int|string $id The ID of the record to retrieve data.
 * @return array|bool Return data array for graph, or False
 *  on failure.
 */
	public function getAllForGraph($id = null) {
		if (empty($id)) {
			return false;
		}

		$fields = [
			$this->alias . '.id',
			$this->alias . '.enabled',
			$this->alias . '.' . $this->displayField
		];
		$conditions = [$this->alias . '.id' => $id];
		$contain = [
			'MainProfile',
			'Profile'
		];
		$this->bindHabtmAssocProfiles();

		return $this->find('first', compact('conditions', 'fields', 'contain'));
	}

/**
 * Return data array for graph by computer name
 *
 * @param string $compName The computer name to retrieve data.
 * @return array Return data array for graph.
 */
	public function getAllHostsForGraph($compName = null) {
		$result = [];
		if (empty($compName)) {
			return $result;
		}

		$order = [$this->alias . '.lft' => 'asc'];
		$hosts = $this->getList(null, null, $order);
		if (empty($hosts)) {
			return $result;
		}

		$modelConfig = ClassRegistry::init('Config');
		$applyMultiple = $modelConfig->getConfig('applyMultiple');
		foreach ($hosts as $hostId => $hostName) {
			if (!preg_match('/^' . $hostName . '$/i', $compName)) {
				continue;
			}

			$result[] = $hostId;
			if (!$applyMultiple) {
				break;
			}
		}

		return $result;
	}

/**
 * Return the node ID of the graph by name
 *
 * @param string $name Name of graph node
 * @return string Return the node ID
 */
	public function getIdNode($name = null) {
		$result = parent::getIdNode($name);
		$initModelName = $this->getInitModelName();
		if ($initModelName === $this->name) {
			return $result;
		}

		$result .= '_' . uniqid();
		return $result;
	}

/**
 * Return information about graph data shape
 *
 * @return array Return information about graph data shape
 */
	public function getGraphDataShape() {
		$result = 'box';
		return $result;
	}

/**
 * Return information about data dependencies.
 *
 * @return array Return information about data dependencies
 */
	public function getGraphDependencyInfo() {
		$result = [
			'MainProfile' => ['dependLabel' => __('Main profile'), 'arrowhead' => 'normal', 'targetModel' => 'Profile'],
			'Profile' => ['dependLabel' => __('Assoc. profile'), 'arrowhead' => 'empty', 'targetModel' => 'Profile'],
		];

		return $result;
	}

/**
 * Return a list of computer names without a host by
 *  query string
 *
 * @param string $query Query string
 * @param int $limit Limit for find
 * @return array Return list of computer names
 */
	public function getListNotProcessedComputersByQuery($query = null, $limit = null) {
		$result = [];
		$modelLdapComputer = ClassRegistry::init('LdapComputer');
		$computers = $modelLdapComputer->getListComputers($query, $limit);
		if (empty($computers)) {
			return $result;
		}

		$modelConfig = ClassRegistry::init('Config');
		$caseSensitivity = $modelConfig->getConfig('caseSensitivity');
		$conditions = [
			$this->alias . '.id_text like' => $query . '%'
		];
		$hosts = $this->getList($conditions);
		if (!$caseSensitivity) {
			$hosts = array_map('mb_strtolower', $hosts);
		}
		foreach ($computers as $computerDN => $computerName) {
			$computerNameTarget = $computerName;
			if (!$caseSensitivity) {
				$computerNameTarget = mb_strtolower($computerName);
			}
			if (!empty($hosts) && in_array($computerNameTarget, $hosts)) {
				continue;
			}

			$result[] = [
				'value' => $computerName,
				'text' => $computerName,
				'data' => [
					'subtext' => $computerDN
				]
			];
		}

		return $result;
	}

/**
 * Return a list of computer names by query string
 *
 * @param string $query Query string
 * @param int $limit Limit for find
 * @return array Return list of computer names
 */
	public function getListComputersByQuery($query = null, $limit = null) {
		$result = [];
		$modelLdapComputer = ClassRegistry::init('LdapComputer');
		$computers = $modelLdapComputer->getListComputersFromCache($query, $limit);
		if (empty($computers)) {
			return $result;
		}
		$result = array_values($computers);

		return $result;
	}

/**
 * Generate new hosts based on template by name from LDAP
 *
 * @param array|string $computers List of computers for processing
 * @param int|string $hostTemplateId ID of template host for processing
 * @param int|string|null $profileTemplateId ID of template profile for processing.
 *  If empty, use empty profile.
 * @param int $idTask The ID of the QueuedTask
 * @return bool Success
 */
	public function generateFromTemplate($computers = null, $hostTemplateId = null, $profileTemplateId = null, $idTask = null) {
		$step = 0;
		$maxStep = 1;
		$result = true;
		set_time_limit(GENERATE_XML_TIME_LIMIT);
		$modelExtendQueuedTask = ClassRegistry::init('CakeTheme.ExtendQueuedTask');
		$modelExtendQueuedTask->updateProgress($idTask, 0);

		if (empty($computers)) {
			$modelExtendQueuedTask->updateTaskErrorMessage($idTask, __('The list of computers for generating XML is empty'));
			return false;
		}
		if (empty($hostTemplateId)) {
			$modelExtendQueuedTask->updateTaskErrorMessage($idTask, __('Invalid host template ID'));
			return false;
		}
		if (!is_array($computers)) {
			$computers = [$computers];
		}

		$maxStep += count($computers);
		foreach ($computers as $computerName) {
			$step++;
			if ($step % 10 == 0) {
				$modelExtendQueuedTask->updateTaskProgress($idTask, $step, $maxStep);
			}

			$mainProfile = null;
			if (!empty($profileTemplateId)) {
				$mainProfile = $computerName;
				$infoCheckUnique = ['id_text' => $computerName];
				if (!$this->MainProfile->isUniqueID($infoCheckUnique) ||
					!$this->MainProfile->createFromTemplate($profileTemplateId, $mainProfile, null, $idTask)) {
					$result = false;
					continue;
				}
			}
			if (!$this->createFromTemplate($hostTemplateId, $computerName, $mainProfile, $idTask)) {
				$result = false;
			}
		}
		$step = $maxStep - 1;
		$modelExtendQueuedTask->updateTaskProgress($idTask, $step, $maxStep);

		return $result;
	}

/**
 * Return a list of enabled host
 *
 * @param bool $includeTemplate Flag of inclusion in the result
 *  template hosts.
 * @return array Return list of enabled host
 */
	public function getListHosts($includeTemplate = false) {
		$conditions = [$this->alias . '.enabled' => true];
		if (!$includeTemplate) {
			$conditions[$this->alias . '.template'] = false;
		}
		return $this->getList($conditions);
	}

}
