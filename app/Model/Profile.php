<?php
/**
 * This file is the model file of the application. Used to
 *  manage profiles.
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

/**
 * The model is used to manage profiles.
 *
 * @package app.Model
 */
class Profile extends AppModel {

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
		'CanDisable',
		'GetXmlInfo',
		'BreadCrumbExt',
		'GetInfo',
		'GetList' => ['cacheConfig' => CACHE_KEY_LISTS_INFO_PROFILE],
		'GetNumber' => ['cacheConfig' => CACHE_KEY_STATISTICS_INFO_PROFILE],
		'GroupAction',
		'ChangeState',
		'GetGraphInfo',
		'TemplateData',
		'ValidationRules',
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
			'alphaNumeric' => [
				'rule' => ['custom', '/^[\p{Ll}\p{Lm}\p{Lo}\p{Lt}\p{Lu}\p{Nd}]{1}[\p{Ll}\p{Lm}\p{Lo}\p{Lt}\p{Lu}\p{Nd}_\-]*$/u'], // '/^[a-z0-9]{1}[a-z0-9_\-]*$/i'
				'required' => true,
				'message' => 'The profile id must start with a letter or number and only contain: letters, numbers, underscores, and hyphens.',
				'last' => true
			],
			'uniqueID' => [
				'rule' => 'isUniqueID',
				'required' => true,
				'message' => 'That profile already exists.',
				'last' => true
			],
		],
		'ProfileDependency' => [
			'selfDependency' => [
				'rule' => ['selfDependency', 'id'],
				'message' => 'Depended on self',
				'allowEmpty' => true,
				'required' => false,
				'last' => true,
			]
		]
	];

/**
 * Detailed list of hasAndBelongsToMany associations.
 *
 * @var array
 * @link https://book.cakephp.org/2.0/en/models/associations-linking-models-together.html#hasandbelongstomany-habtm
 */
	public $hasAndBelongsToMany = [
		'ProfileDependency' => [
			'className' => 'ProfileDependency',
			'joinTable' => 'profiles_profiles',
			'foreignKey' => 'profile_id',
			'associationForeignKey' => 'dependency_id',
			'unique' => true,
			'conditions' => '',
			'dependent' => true,
			'fields' => [
				'ProfileDependency.id',
				'ProfileDependency.id_text',
				'ProfileDependency.enabled'
			],
			'order' => ['ProfileDependency.id_text' => 'asc']
		],
		'Host' => [
			'className' => 'Host',
			'joinTable' => 'hosts_profiles',
			'foreignKey' => 'profile_id',
			'associationForeignKey' => 'host_id',
			'unique' => true,
			'conditions' => '',
			'fields' => [
				'Host.id',
				'Host.id_text',
				'Host.enabled'
			],
			'order' => ['Host.id_text' => 'asc']
		]
	];

/**
 * Detailed list of hasMany associations.
 *
 * @var array
 * @link https://book.cakephp.org/2.0/en/models/associations-linking-models-together.html#hasmany
 */
	public $hasMany = [
		'PackagesProfile' => [
			'className' => 'PackagesProfile',
			'foreignKey' => 'profile_id',
			'dependent' => true,
			'fields' => [
				'PackagesProfile.id',
				'PackagesProfile.profile_id,
				PackagesProfile.package_id'
			]
		],
		'HostsProfile' => [
			'className' => 'HostsProfile',
			'foreignKey' => 'profile_id',
			'dependent' => true
		],
		'Variable' => [
			'className' => 'Variable',
			'foreignKey' => 'ref_id',
			'dependent' => true,
			'conditions' => ['ref_type' => VARIABLE_TYPE_PROFILE],
			'order' => ['Variable.lft' => 'asc'],
			'fields' => [
				'Variable.id',
				'Variable.ref_type',
				'Variable.name',
				'Variable.value'
			]
		]
	];

/**
 * Called before each save operation, after validation.
 *
 * Actions:
 *  - Reset flag using as template if not set.
 *
 * @param array $options Options passed from Model::save().
 * @return bool True if the operation should continue, false if it should abort
 * @link https://book.cakephp.org/2.0/en/models/callback-methods.html#beforesave
 * @see Model::save()
 */
	public function beforeSave($options = []) {
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
		return $modelGarbage->storeData(GARBAGE_TYPE_PROFILE, $this->id);
	}

/**
 * Return information of profile
 *
 * @param int|string $id The ID of the record to read.
 * @param bool $full Flag of inclusion in the result
 *  full information.
 * @return array|bool Return information of profile,
 *  or False on failure.
 */
	public function get($id = null, $full = true) {
		if (empty($id)) {
			return false;
		}

		$fields = [
			$this->alias . '.id',
			$this->alias . '.enabled',
			$this->alias . '.template',
			$this->alias . '.id_text',
			$this->alias . '.notes',
			$this->alias . '.created',
			$this->alias . '.modified'
		];
		$conditions = [$this->alias . '.id' => $id];
		$contain = [
			'ProfileDependency',
		];
		if ($full) {
			if (!$this->_bindHabtmProfileDependedOnBy() || !$this->_bindHasManyHostMainProfiles()) {
				return false;
			};
			$containExt = [
				'DependedOnBy',
				'HostMainProfiles',
				'PackagesProfile.Package',
				'PackagesProfile.Attribute' => ['fields' => '*'],
				'PackagesProfile.Check',
				'PackagesProfile.Check.Attribute' => ['fields' => '*'],
				'Variable',
				'Variable.Attribute' => ['fields' => '*'],
				'Variable.Check',
				'Variable.Check.Attribute' => ['fields' => '*'],
				'Host'
			];
			$contain = array_merge($contain, $containExt);
		}

		$result = $this->find('first', compact('conditions', 'fields', 'contain'));
		if (empty($result) || !$full) {
			return $result;
		}

		if (isset($result['PackagesProfile'])) {
			$result['PackagesProfile'] = Hash::sort(
				$result['PackagesProfile'],
				'{n}.Package.name',
				'asc',
				[
					'type' => 'string',
					'ignoreCase' => true
				]
			);
		}

		return $result;
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

		$order = [$this->alias . '.id_text' => 'asc'];

		$contain = [
			'Variable',
			'Variable.Attribute',
			'Variable.Check',
			'Variable.Check.Attribute',
			'ProfileDependency' => [
				'fields' => [
					'ProfileDependency.id',
					'ProfileDependency.id_text'],
				'conditions' => ['ProfileDependency.enabled' => true]
			],
			'PackagesProfile.Package',
			'PackagesProfile.Attribute',
			'PackagesProfile.Check',
			'PackagesProfile.Check.Attribute'
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
		$result = [];
		if (is_array($id)) { // Build XML data for Profiles in Host
			foreach ($id as $profile) {
				$result['profile'][] = ['@id' => $profile['id_text']];
			}

			return $result;
		}

		// Build XML data for Profiles
		$baseUrl = Configure::read('App.fullBaseUrl');
		$result = [
			'profiles:profiles' => [
				'xmlns:profiles' => 'http://www.wpkg.org/profiles',
				'xmlns:xsi' => 'http://www.w3.org/2001/XMLSchema-instance',
				'@xsi:schemaLocation' => 'http://www.wpkg.org/profiles ' . $baseUrl . '/xsd/profiles.xsd'
			]];

		if ($exportdisable) {
			$xmlItemArray = [
				'@id' => 'STOP_EXPORT_XML_PROFILE'
			];
			$result['profiles:profiles']['profile'][] = $xmlItemArray;
			return $result;
		}

		$profiles = $this->getAllForXML($id, $exportnotes, $exportdisabled);
		if (empty($profiles)) {
			return $result;
		}

		foreach ($profiles as $profile) {
			if (!$profile[$this->alias]['enabled'] && !$exportdisabled) {
				continue;
			}

			$profileAttribs = [
				'@id' => $profile[$this->alias]['id_text']
			];

			if (isset($profile[$this->alias]['notes']) && !empty($profile[$this->alias]['notes'])) {
				$profileAttribs[XML_SPECIFIC_TAG_NOTES] = preg_replace('/[\-]{2,}/', '-', $profile[$this->alias]['notes']);
			}

			if (isset($profile[$this->alias]['template']) && $profile[$this->alias]['template']) {
				$profileAttribs[XML_SPECIFIC_TAG_TEMPLATE] = __('Use as template');
			}

			$xmlItemArray = $profileAttribs;
			if (isset($profile['Variable'])) {
				$xmlItemArray += $this->Variable->getXMLdata($profile['Variable']);
			}

			if (isset($profile['ProfileDependency'])) {
				$xmlItemArray += $this->ProfileDependency->getXMLdata($profile['ProfileDependency']);
			}

			if (isset($profile['PackagesProfile'])) {
				$xmlItemArray += $this->PackagesProfile->getXMLdata($profile['PackagesProfile']);
			}

			if (!$profile[$this->alias]['enabled'] && $exportdisabled) {
				$result['profiles:profiles'][XML_SPECIFIC_TAG_DISABLED]['profile'][] = $xmlItemArray;
			} else {
				$result['profiles:profiles']['profile'][] = $xmlItemArray;
			}
		}

		return $result;
	}

/**
 * Temporarily bind an additional hasMany relationship,
 *  which gives us which host includes this profile as main profile.
 *
 * @return bool Success.
 */
	protected function _bindHasManyHostMainProfiles() {
		$result = $this->bindModel(
			[
				'hasMany' => [
					'HostMainProfiles' => [
						'className' => 'Host',
						'foreignKey' => 'mainprofile_id',
						'fields' => [
							'HostMainProfiles.id',
							'HostMainProfiles.enabled',
							'HostMainProfiles.id_text'
						],
						'order' => [
							'HostMainProfiles.id_text' => 'asc'
						],
						'dependent' => false
					]
				]
			],
			true
		);

		return $result;
	}

/**
 * Checking the ability to perform operations `delete` or `disable`
 *
 * @param int|string $id Record ID to check
 * @return bool|string Return True, if possible. False on failure or 
 *  error message if not possible.
 */
	public function checkDisable($id = null) {
		if (empty($id) || !$this->_bindHasManyHostMainProfiles()) {
			return false;
		}

		$fields = [
			$this->alias . '.id',
			$this->alias . '.id_text',
		];
		$conditions = [
			$this->alias . '.id' => $id
		];
		$contain = [
			'HostMainProfiles' => [
				'conditions' => ['HostMainProfiles.enabled' => true]
			]
		];
		$data = $this->find('first', compact('fields', 'conditions', 'contain'));
		if (empty($data)) {
			return false;
		}
		if (empty($data['HostMainProfiles'])) {
			return true;
		}

		$listHosts = '<ul>';
		foreach ($data['HostMainProfiles'] as $host) {
			$listHosts .= '<li>' . h($host['id_text']) . '</li>';
		}
		$listHosts .= '</ul>';
		$result = __("The profile '%s' cannot be deleted or disabled because it is the main profile for the following hosts: %s",
			$data['Profile']['id_text'],
			$listHosts
		);

		return $result;
	}

/**
 * Temporarily bind an additional new HABTM relationship,
 *  which gives us which packages included on this profile.
 *
 * @return bool Success.
 */
	public function bindHabtmPackages() {
		$hasAndBelongsToMany = $this->getAssociated('hasAndBelongsToMany');
		if (!empty($hasAndBelongsToMany) && in_array('Package', $hasAndBelongsToMany)) {
			return true;
		}

		$result = $this->bindModel(
			[
				'hasAndBelongsToMany' => [
					'Package' => [
						'className' => 'Package',
						'joinTable' => 'packages_profiles',
						'foreignKey' => 'profile_id',
						'associationForeignKey' => 'package_id',
						'unique' => 'keepExisting',
						'order' => ['Package.name' => 'asc']
					]
				]
			],
			false
		);

		return $result;
	}

/**
 * Return list of profiles exclude one profile by ID
 *
 * @param int|string $id The ID of the record to exclude
 * @return array Return list of profiles
 */
	public function getListDependencyProfiles($id = null) {
		$conditions = [];
		if (!empty($id)) {
			$conditions = ['Profile.id <>' => $id];
		}

		return $this->getList($conditions);
	}

/**
 * Return list of packages for profile by ID
 *
 * @param int|string $id The ID of the record to retrieve data
 * @return array|bool Return list of packages, or False
 *  on failure.
 */
	public function getListPackagesForProfile($id = null) {
		if (empty($id)) {
			return false;
		}
		$this->bindHabtmPackages();

		$fields = [
			$this->alias . '.id',
			$this->alias . '.enabled',
			$this->alias . '.template',
			$this->alias . '.id_text',
			$this->alias . '.notes',
			$this->alias . '.created',
			$this->alias . '.modified'
		];
		$conditions = [$this->alias . '.id' => $id];
		$contain = [
			'Package',
		];

		return $this->find('first', compact('conditions', 'fields', 'contain', 'order'));
	}

/**
 * Return list of hosts for profile by ID
 *
 * @param int|string $id The ID of the record to retrieve data
 * @return array|bool Return list of hosts, or False
 *  on failure.
 */
	public function getListHostsForProfile($id = null) {
		if (empty($id)) {
			return false;
		}

		$fields = [
			$this->alias . '.id',
			$this->alias . '.enabled',
			$this->alias . '.template',
			$this->alias . '.id_text',
			$this->alias . '.notes',
			$this->alias . '.created',
			$this->alias . '.modified'
		];
		$conditions = [$this->alias . '.id' => $id];
		$contain = [
			'Host',
		];

		return $this->find('first', compact('conditions', 'fields', 'contain'));
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
 * Return Xpath for `id` attribute of profile
 *
 * @return string Return Xpath
 */
	public function getIdAttributeXpath() {
		$idXpath = 'profiles:profiles.profile.0.@id';
		return $idXpath;
	}

/**
 * Return Xpath for `template` element of profile
 *
 * @return string Return Xpath
 */
	public function getTemplateElementXpath() {
		$idXpath = 'profiles:profiles.profile.0.template';
		return $idXpath;
	}

/**
 * Return name of data.
 *
 * @return string Return name of data
 */
	public function getTargetName() {
		$result = __('Profile');

		return $result;
	}

/**
 * Return name of group data.
 *
 * @return string Return name of group data
 */
	public function getGroupName() {
		$result = __('Profiles');

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
			$result = __("Profile '%s'", $name);
		} else {
			$result = __("profile '%s'", $name);
		}

		return $result;
	}

/**
 * Return full name of data.
 *
 * @return string|bool Return full name of data.
 */
	public function getFullDataName() {
		$result = __('full data of profiles');
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
			$this->Variable->alias . '.ref_type' => VARIABLE_TYPE_PROFILE
		];
		if (!$this->Variable->setScopeModel(VARIABLE_TYPE_PROFILE, $id) ||
			!$this->Variable->deleteAll($conditions, true, false)) {
			return false;
		}
		$conditions = [$this->HostsProfile->alias . '.profile_id' => $id];
		if (!$this->HostsProfile->deleteAll($conditions, true, false)) {
			return false;
		}
		$conditions = [$this->PackagesProfile->alias . '.profile_id' => $id];
		if (!$this->PackagesProfile->deleteAll($conditions, true, false)) {
			return false;
		}
		$modelProfilesProfile = ClassRegistry::init('ProfilesProfile');
		$conditions = [$modelProfilesProfile->alias . '.profile_id' => $id];
		if (!$modelProfilesProfile->deleteAll($conditions, true, false)) {
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
 * Temporarily bind an additional new HABTM relationship,
 *  which gives us which profiles depend on this profile
 *
 * @return bool Success.
 */
	protected function _bindHabtmProfileDependedOnBy() {
		$result = $this->bindModel(
			[
				'hasAndBelongsToMany' => [
					'DependedOnBy' => [
						'className' => 'ProfileDependency',
						'joinTable' => 'profiles_profiles',
						'foreignKey' => 'dependency_id',
						'associationForeignKey' => 'profile_id',
						'unique' => true,
						'conditions' => '',
						'dependent' => false,
						'fields' => [
							'DependedOnBy.id',
							'DependedOnBy.id_text',
							'DependedOnBy.enabled'
						],
						'order' => ['DependedOnBy.id_text' => 'asc']
					]
				]
			],
			true
		);

		return $result;
	}

/**
 * Return list of hosts and depended profiles for profile by ID
 *
 * @param int|string $profileId The ID of the record to retrieve data
 * @return array|bool Return list of hosts and profiles, or False
 *  on failure.
 */
	public function getListHostsAndDependProfiles($profileId = null) {
		if (!$this->_bindHasManyHostMainProfiles() || !$this->_bindHabtmProfileDependedOnBy()) {
			return false;
		}
		$fields = [
			$this->alias . '.id',
			$this->alias . '.enabled',
			$this->alias . '.' . $this->displayField
		];
		$conditions = [$this->alias . '.id' => $profileId];
		$contain = [
			'Host',
			'HostMainProfiles',
			'DependedOnBy'
		];

		return $this->find('first', compact('fields', 'conditions', 'contain'));
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
			'ProfileDependency'
		];

		return $this->find('first', compact('conditions', 'fields', 'contain'));
	}

/**
 * Return information about graph data style
 *
 * @return array Return information about graph data style
 */
	public function getGraphDataStyle() {
		$result = ['rounded'];
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
			'ProfileDependency' => ['dependLabel' => __x('dependency', 'Depends on'), 'arrowhead' => 'normal'],
		];

		return $result;
	}

/**
 * Recursive retrieve data for build a dependency graph
 *
 * @param array &$result Result of retrieving data
 * @param int|string $id Record ID to retrieving data
 * @param string $parent Name of parent graph node
 * @param int $level Current level of recursion
 * @param int $deepLimit Limit for deep recursion
 * @return void
 */
	public function getGraphDataHostRec(array &$result, $id = null, $parent = null, $level = 1, $deepLimit = GRAPH_DEEP_LIMIT) {
		if (($level > $deepLimit) || $this->ProfileDependency->checkIsProcessedId($id) ||
			!$this->ProfileDependency->checkLimitListProcessedId()) {
			return;
		}
		if (empty($parent)) {
			return;
		}

		$hosts = $this->getListHostsAndDependProfiles($id);
		if (empty($hosts)) {
			return;
		}

		$this->ProfileDependency->addToListProcessedId($id);
		$dependencyInfo = [
			'HostMainProfiles' => ['dependLabel' => __('Through main profile'), 'arrowhead' => 'normal', 'dependModel' => 'Host'],
			'DependedOnBy' => ['dependLabel' => __('Through dependent profile'), 'arrowhead' => 'open', 'dependModel' => ''],
			'Host' => ['dependLabel' => __('Through prof. assoc. host'), 'arrowhead' => 'empty', 'dependModel' => 'Host']
		];

		$level++;
		foreach ($dependencyInfo as $dependName => $dependDataInfo) {
			if (!isset($hosts[$dependName])) {
				continue;
			}

			foreach ($hosts[$dependName] as $nodeData) {
				$dependModel = Hash::get($dependDataInfo, 'dependModel');
				$edgeLabel = Hash::get($dependDataInfo, 'dependLabel');
				$arrowhead = Hash::get($dependDataInfo, 'arrowhead');
				if (empty($dependModel)) {
					$objTargetModel = $this;
				} else {
					$objTargetModel = $this->$dependModel;
				}
				$graphDataNode = $objTargetModel->getGraphDataNode($nodeData, $parent, $arrowhead, $edgeLabel);
				if (empty($graphDataNode)) {
					continue;
				}
				$result[] = $graphDataNode;
				if (!empty($dependModel)) {
					continue;
				}

				$nodeDataId = Hash::get($nodeData, 'id');
				$parentNode = $graphDataNode['name'];
				$this->getGraphDataHostRec($result, $nodeDataId, $parentNode, $level, $deepLimit);
			}
		}
	}

/**
 * Return full data for build a dependency graph.
 *
 * @param int|string $id Record ID to retrieve data
 * @param string $parent Name of parent graph node
 * @return array Return full data for build a dependency graph.
 */
	public function getGraphDataFull($id = null, $parent = null) {
		$result = [];
		if (empty($id) || empty($parent)) {
			return $result;
		}

		$packages = $this->getListPackagesForProfile($id);
		if (empty($packages)) {
			return $result;
		}

		$edgeLabel = __('Contains');
		foreach ($packages['Package'] as $package) {
			$result[] = $this->Package->getGraphDataNode($package, $parent, null, $edgeLabel);
		}

		$initModelName = $this->getInitModelName();
		if ($initModelName !== $this->name) {
			return $result;
		}

		$level = 1;
		$deepLimit = $this->getLimitGraphDeep();
		$this->getGraphDataHostRec($result, $id, $parent, $level, $deepLimit);

		return $result;
	}

/**
 * Return list of unused profiles to disable
 *
 * @return array Return list of unused profiles to disable
 */
	protected function _getListProfilesToDisableUnused() {
		$conditions = [
			$this->alias . '.enabled' => true,
			$this->alias . '.template' => false,
			'HostsProfile.profile_id' => null,
			'ProfilesProfile.dependency_id' => null,
			'Host.mainprofile_id' => null,
		];
		$fields = [
			$this->alias . '.id',
			$this->alias . '.id',
		];
		$order = [$this->alias . '.id_text' => 'asc'];
		$joins = [
			[
				'table' => 'hosts',
				'alias' => 'Host',
				'type' => 'LEFT',
				'conditions' => [
					'Profile.id = Host.mainprofile_id',
				]
			],
			[
				'table' => 'hosts_profiles',
				'alias' => 'HostsProfile',
				'type' => 'LEFT',
				'conditions' => [
					'Profile.id = HostsProfile.profile_id',
				]
			],
			[
				'table' => 'profiles_profiles',
				'alias' => 'ProfilesProfile',
				'type' => 'LEFT',
				'conditions' => [
					'Profile.id = ProfilesProfile.dependency_id',
				]
			]
		];
		$recursive = -1;
		return $this->find('list', compact('conditions', 'fields', 'order', 'joins', 'recursive'));
	}

/**
 * Return list of unused profiles to disable from main profile hosts
 *
 * @return array Return list of unused profiles to disable
 */
	protected function _getListProfilesToDisableMainProfileHost() {
		$result = [];
		$conditions = [
			$this->alias . '.enabled' => true,
			$this->alias . '.template' => false,
		];
		$fields = [
			$this->alias . '.id',
			'COUNT(DISTINCT Host.id_text) AS CountFull',
			'COUNT(DISTINCT HostInactive.id_text) AS CountInactive',
		];
		$order = [$this->alias . '.id_text' => 'asc'];
		$joins = [
			[
				'table' => 'hosts',
				'alias' => 'Host',
				'type' => 'INNER',
				'conditions' => [
					'Profile.id = Host.mainprofile_id',
				]
			],
			[
				'table' => 'hosts',
				'alias' => 'HostInactive',
				'type' => 'INNER',
				'conditions' => [
					'Profile.id = HostInactive.mainprofile_id',
					'HostInactive.enabled = 0',
				]
			],
		];
		$group = $this->alias . '.id';
		$having = 'CountFull = CountInactive';
		$recursive = -1;
		$data = $this->find('all', compact('conditions', 'fields', 'order', 'joins',
			'group', 'having', 'recursive'));
		if (empty($data)) {
			return $result;
		}
		$result = Hash::combine($data, '{n}.' . $this->alias . '.id', '{n}.' . $this->alias . '.id');

		return $result;
	}

/**
 * Return list of unused profiles to disable
 *
 * @return array Return list of unused profiles to disable
 */
	public function getListProfilesToDisable() {
		$result = [];
		$result += $this->_getListProfilesToDisableUnused();
		$result += $this->_getListProfilesToDisableMainProfileHost();

		return $result;
	}
}
