<?php
/**
 * This file is the model file of the application. Used to
 *  import information from XML.
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
App::uses('Xml', 'Utility');

/**
 * The model is used to import information from XML.
 *
 * @package app.Model
 */
class Import extends AppModel {

/**
 * Custom database table name, or null/false if no table association is desired.
 *
 * @var string
 * @link https://book.cakephp.org/2.0/en/models/model-attributes.html#usetable
 */
	public $useTable = false;

/**
 * List of behaviors to load when the model object is initialized.
 *
 * @var array
 * @link https://book.cakephp.org/2.0/en/models/behaviors.html#using-behaviors
 */
	public $actsAs = [
		'ParseData',
		'BreadCrumbExt'
	];

/**
 * Dependency tree cache
 *
 * @var bool
 */
	protected $_treeDependencies = [];

/**
 * Cache checks type
 *
 * @var bool
 */
	protected $_checkTypesCache = [];

/**
 * Flag of case sensitivity
 *
 * @var bool
 */
	protected $_caseSensitivity = false;

/**
 * Object of model `Package`
 *
 * @var object
 */
	protected $_modelPackage = null;

/**
 * Object of model `Profile`
 *
 * @var object
 */
	protected $_modelProfile = null;

/**
 * Object of model `Host`
 *
 * @var object
 */
	protected $_modelHost = null;

/**
 * Object of model `Report`
 *
 * @var object
 */
	protected $_modelReport = null;

/**
 * Object of model `Config`
 *
 * @var object
 */
	protected $_modelConfig = null;

/**
 * Object of model `ConfigLanguage`
 *
 * @var object
 */
	protected $_modelConfigLanguage = null;

/**
 * Object of model `Variable`
 *
 * @var object
 */
	protected $_modelVariable = null;

/**
 * Object of model `Attribute`
 *
 * @var object
 */
	protected $_modelAttribute = null;

/**
 * Object of model `Check`
 *
 * @var object
 */
	protected $_modelCheck = null;

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

		$this->_modelPackage = ClassRegistry::init('Package');
		$this->_modelProfile = ClassRegistry::init('Profile');
		$this->_modelHost = ClassRegistry::init('Host');
		$this->_modelReport = ClassRegistry::init('Report');
		$this->_modelConfig = ClassRegistry::init('Config');
		$this->_modelConfigLanguage = ClassRegistry::init('ConfigLanguage');
		$this->_modelVariable = ClassRegistry::init('Variable');
		$this->_modelAttribute = ClassRegistry::init('Attribute');
		$this->_modelCheck = ClassRegistry::init('Check');
		$this->_modelExtendQueuedTask = ClassRegistry::init('CakeTheme.ExtendQueuedTask');

		$modelConfig = ClassRegistry::init('Config');
		$this->_caseSensitivity = $modelConfig->getConfig('caseSensitivity');
	}

/**
 * Return limit of size for upload XML file.
 *
 * @return int Return limit of size XML file, bytes.
 */
	public function getLimitFileSize() {
		$result = (int)UPLOAD_FILE_SIZE_LIMIT;

		return $result;
	}

/**
 * Return allowed extensions of files for upload (PCRE).
 *
 * @param bool $returnServer If True, return result for server.
 *  Otherwise return for client.
 * @return string Return allowed extensions of files for upload.
 */
	public function getAcceptFileTypes($returnServer = false) {
		if ($returnServer) {
			$result = (string)UPLOAD_FILE_TYPES_SERVER;
		} else {
			$result = (string)UPLOAD_FILE_TYPES_CLIENT;
		}

		return $result;
	}

/**
 * Clear dependency tree cache
 *
 * @return void
 */
	protected function _resetTreeDependencies() {
		$this->_treeDependencies = [];
	}

/**
 * Add data to dependency tree cache
 *
 * @param int|string $id The ID of the record.
 * @param int|string $dependencyId The ID of the dependency record.
 * @return bool Success
 */
	protected function _putItemTreeDependencies($id = null, $dependencyId = null) {
		if (empty($id)) {
			return false;
		}

		if (isset($this->_treeDependencies[$id]) &&
			in_array($dependencyId, $this->_treeDependencies[$id])) {
				return true;
		}

		$this->_treeDependencies[$id][] = $dependencyId;

		return true;
	}

/**
 * Recursive build of a dependency list
 *
 * @param array &$result Result of processing data
 * @param int|string $id The ID of the record to process.
 * @param int|string $parentId The ID of the parent record.
 * @param int $level Current level of recursion
 * @param int $deepLimit Limit for deep recursion
 * @return void
 */
	protected function _buildListDependencies(array &$result, $id = null, $parentId = null, $level = 0, $deepLimit = IMPORT_DEPEND_DEEP_LIMIT) {
		if (!isset($result[$id]) || ($result[$id] < $level)) {
			$result[$id] = $level;
		}

		if (!isset($this->_treeDependencies[$id]) || ($level >= $deepLimit)) {
			return;
		}

		$level++;
		foreach ($this->_treeDependencies[$id] as $dependencyId) {
			if ($dependencyId === $parentId) {
				continue;
			}

			$this->_buildListDependencies($result, $dependencyId, $id, $level, $deepLimit);
		}
	}

/**
 * Return dependency list
 *
 * @param int $deepLimit Limit for deep recursion
 * @return array Return dependency list
 */
	protected function _getListFromTreeDependencies($deepLimit = IMPORT_DEPEND_DEEP_LIMIT) {
		$result = [];
		$level = 0;
		foreach ($this->_treeDependencies as $id => $dependencies) {
			$this->_buildListDependencies($result, $id, null, $level, $deepLimit);
		}
		arsort($result);
		return $result;
	}

/**
 * Create cache of checks type
 *
 * @return void
 */
	protected function _createCheckTypesCache() {
		$this->_checkTypesCache = [];
		$listConditions = constsToWords('CHECK_CONDITION_');
		foreach ($listConditions as $val => $words) {
			$words = mb_strtolower($words);
			$arrWords = explode(' ', $words);
			$type = array_shift($arrWords);
			$words = implode('', $arrWords);
			$sWords = [
				'thanor',
				'orequalto'
			];
			$rWords = [
				'or',
				'orequal'
			];
			$words = str_replace($sWords, $rWords, $words);
			if (!isset($this->_checkTypesCache[$type])) {
				$this->_checkTypesCache[$type] = [];
			}
			$this->_checkTypesCache[$type][$words] = $val;
		}
	}

/**
 * Return list of check types from cache
 *
 * @param string $type Type of check for retrieve data
 * @return array Return list of check types
 */
	protected function _getCheckTypeFromCache($type = null) {
		$result = [];
		if (empty($type)) {
			return $result;
		}

		$type = mb_strtolower($type);
		if (!isset($this->_checkTypesCache[$type])) {
			return $result;
		}

		return $this->_checkTypesCache[$type];
	}

/**
 * Retrieving extended attribute values from XML data array
 *
 * @param array $xmlData XML data array
 * @param array|string $attributes List of attributes for processing
 * @return array Return list of attribute values
 */
	protected function _extractAttributes($xmlData = [], $attributes = []) {
		$result = [];
		if (empty($xmlData) || empty($attributes) || !is_array($xmlData)) {
			return $result;
		}
		if (!is_array($attributes)) {
			$attributes = [$attributes];
		}
		foreach ($attributes as $attribute => $field) {
			if (is_int($attribute)) {
				$attribute = $field;
			}
			if (substr($attribute, 0, 1) !== '@') {
				$attribute = '@' . $attribute;
			}
			if (isset($xmlData[$attribute])) {
				$result[$field] = $xmlData[$attribute];
			}
		}

		return $result;
	}

/**
 * Convert array data to associative array, if needed
 *
 * @param array &$array Data to convert
 * @return void
 */
	protected function _arrayIfY(array &$array) {
		if (!isAssoc($array) || empty($array)) {
			return;
		}

		$array = [$array];
	}

/**
 * Compare version
 *
 * @param string $a Version one to compare
 * @param string $b Version two to compare
 * @return int Retrun result of comparison as:
 *  - `0`: versions are equal;
 *  - `-1`: version $a is less than version $b;
 *  - `1`: version $a is more than version $b.
 */
	public function versionCompare($a = '', $b = '') {
		$as = explode('.', $a);
		$bs = explode('.', $b);
		$al = count($as);
		$bl = count($bs);
		$length = ($al > $bl ? $al : $bl);
		$result = 0;
		for ($i = 0; $i < $length; $i++) {
			$av = (isset($as[$i]) && !empty($as[$i]) ? (int)$as[$i] : 0);
			$bv = (isset($bs[$i]) && !empty($bs[$i]) ? (int)$bs[$i] : 0);
			if ($av < $bv) {
				$result = -1;
				break;
			} elseif ($av > $bv) {
				$result = 1;
				break;
			}
		}

		return $result;
	}

/**
 * Retrieving extended attribute values from XML data array
 *
 * @param array $xmlData XML data array
 * @return array Return list of attribute values
 */
	protected function _prepareAttributes($xmlData) {
		$result = [];
		if (!is_array($xmlData) || empty($xmlData)) {
			return;
		}

		$listAttributes = [
			'hostname', 'os', 'architecture',
			'ipaddresses', 'domainname', 'groups', 'lcid', 'lcidOS'
		];
		$result = $this->_extractAttributes($xmlData, $listAttributes);
		return $result;
	}

/**
 * Return ID attribute name by type.
 *
 * @param string $xmlType Type for processing
 * @return string|bool Return ID attribute name, or
 *  False on failure.
 */
	protected function _getIdAttributeName($xmlType = null) {
		if (empty($xmlType)) {
			return false;
		}

		$xmlType = mb_strtolower($xmlType);
		switch ($xmlType) {
			case 'package':
			case 'profile':
				$idAttrName = 'id';
				break;
			case 'host':
				$idAttrName = 'name';
				break;
			default:
				$idAttrName = false;
		}

		return $idAttrName;
	}

/**
 * Return list of comments name by type, prefix and postfix.
 *
 * @param DOMDocument $xmlObject DOMDocument object for processing
 * @param string $xmlType Type for processing
 * @param string $commentPrefix Prefix of comment
 * @param string $commentPostfix Postfix of comment
 * @return array Return list of comments.
 */
	protected function _getListComments(DOMDocument $xmlObject, $xmlType = null, $commentPrefix = '', $commentPostfix = '') {
		$comments = [];
		if (empty($xmlType)) {
			return $comments;
		}

		$xmlType = mb_strtolower($xmlType);
		switch ($xmlType) {
			case 'package':
				$xpathComment = '/packages:packages/package';
				break;
			case 'profile':
				$xpathComment = '/profiles:profiles/profile';
				break;
			case 'host':
				$xpathComment = '/hosts:wpkg/host';
				break;
			default:
				return $comments;
		}
		$xpathComment .= '/comment()[starts-with(.,"' . $commentPrefix . '")]';

		$idAttrName = $this->_getIdAttributeName($xmlType);
		if (empty($idAttrName)) {
			return $comments;
		}

		$objXPath = new DOMXPath($xmlObject);
		$commentsList = $objXPath->query($xpathComment);
		foreach ($commentsList as $commentEl) {
			$commentText = $commentEl->textContent;
			if ($commentText === false) {
				continue;
			}

			$idText = $commentEl->parentNode->getAttribute($idAttrName);
			if (empty($idText)) {
				continue;
			}

			$comment = preg_replace('/^' . $commentPrefix . '(.+)(?:' . $commentPostfix . '|)$/su', "\$1", $commentText);
			if (isset($comments[$idText])) {
				$comments[$idText] .= "\n";
			} else {
				$comments[$idText] = '';
			}
			$comments[$idText] .= preg_replace(['/\s$/u', '/\n\s+/u'], ['', "\n"], $comment);
		}

		return $comments;
	}

/**
 * Retrieving data from XML data array by type
 *
 * @param array $xmlData XML data array
 * @param string $xmlType Type for processing
 * @return array|bool Return data of XML, or False on failure.
 */
	protected function _extarctDataFromArray($xmlData = [], $xmlType = null) {
		if (empty($xmlData) || !is_array($xmlData) || empty($xmlType)) {
			return false;
		}

		$xmlType = mb_strtolower($xmlType);
		switch ($xmlType) {
			case 'package':
				$path = 'packages.package';
				break;
			case 'profile':
				$path = 'profiles.profile';
				break;
			case 'host':
				$path = 'wpkg.host';
				break;
			case 'database':
				$path = 'wpkg.package';
				break;
			case 'config':
				$path = 'config';
				break;
			default:
				return false;
		}

		$result = Hash::extract($xmlData, $path);
		$this->_arrayIfY($result);

		return $result;
	}

/**
 * Retrieving information from XML data array by type and XML file
 *
 * @param array $xmlData XML data array
 * @param string $xmlType Type for processing
 * @param string $xmlFile XML file for processing
 * @return array|bool Return information, or False on failure.
 */
	protected function _extarctInfoFromArray($xmlData = [], $xmlType = null, $xmlFile = null) {
		if (empty($xmlData) || !is_array($xmlData) || empty($xmlType)) {
			return false;
		}

		$result = [];
		$xmlType = mb_strtolower($xmlType);
		switch ($xmlType) {
			case 'database':
				$xmlData = Hash::extract($xmlData, 'wpkg');
				$Attribute = $this->_prepareAttributes($xmlData);
				$lastChange = null;
				if (is_file($xmlFile)) {
					$timestamp = filemtime($xmlFile);
					if ($timestamp) {
						$lastChange = date('Y-m-d H:i:s', $timestamp);
					}
				}
				$result = compact('Attribute', 'lastChange');
				break;
			default:
				return false;
		}

		return $result;
	}

/**
 * Saving variables information.
 *
 * @param array &$messages Array of messages.
 * @param array $info Array information variables to save.
 * @param int|string $refType ID of type
 * @param int|string $refId ID of associated record
 * @return bool Success.
 */
	protected function _saveVariables(array &$messages, $info = [], $refType = null, $refId = null) {
		if (empty($info) || !isset($info['Variable']) || empty($info['Variable'])) {
			return true;
		}

		$result = true;
		$attrType = null;
		$strRefId = constValToLcSingle('VARIABLE_TYPE', $refType, false, false, false);
		if (!empty($strRefId)) {
			// @codingStandardsIgnoreStart
			$attrType = @constant('ATTRIBUTE_TYPE_' . mb_strtoupper($strRefId));
			// @codingStandardsIgnoreEnd
		}

		if (!$this->_modelVariable->setScopeModel($refType, $refId)) {
			$messages[__('Errors')][__('Variables')][] = __('Error on settings scope');
			return false;
		}
		foreach ($info['Variable'] as $variableInfo) {
			$variable = ['Variable' => $variableInfo['Variable']];
			$variable['Variable']['ref_id'] = $refId;
			$variable['Variable']['ref_type'] = $refType;

			$this->_modelVariable->create(false);
			$resultSaving = (bool)$this->_modelVariable->save($variable);
			if (!$resultSaving) {
				$result = false;
				$errorType = $this->_modelVariable->getFullName($variable, $refType, null, $refId);
				$messages[__('Errors')][$errorType] = $this->_modelVariable->validationErrors;
				continue;
			}

			$varId = $this->_modelVariable->id;
			if (!$this->_saveExtAttributes($messages, $variableInfo, $attrType, ATTRIBUTE_NODE_VARIABLE, $varId)) {
				$result = false;
				continue;
			}
			if (!$this->_saveChecks($messages, $variableInfo, null, CHECK_PARENT_TYPE_VARIABLE, $varId, ATTRIBUTE_TYPE_VARIABLE)) {
				$result = false;
			}
		}

		return $result;
	}

/**
 * Saving extended attributes information.
 *
 * @param array &$messages Array of messages.
 * @param array $info Array information extended attributes to save.
 * @param int|string $refType ID of type
 * @param int|string $refNode ID of node
 * @param int|string $refId ID of associated record
 * @return bool Success.
 */
	protected function _saveExtAttributes(array &$messages, $info = [], $refType = null, $refNode = null, $refId = null) {
		if (empty($info) || !isset($info['Attribute']) || empty($info['Attribute'])) {
			return true;
		}

		$attributes = ['Attribute' => $info['Attribute']];
		$attributes['Attribute']['ref_id'] = $refId;
		$attributes['Attribute']['ref_type'] = $refType;
		$attributes['Attribute']['ref_node'] = $refNode;
		$attributes['Attribute']['pcre_parsing'] = true;

		$this->_modelAttribute->create(false);
		$resultSaving = (bool)$this->_modelAttribute->save($attributes);
		if ($resultSaving) {
			return true;
		}

		$errorType = $this->_modelAttribute->getFullName($attributes, $refType, $refNode, $refId);
		$messages[__('Errors')][$errorType] = $this->_modelAttribute->validationErrors;
		return false;
	}

/**
 * Return type from XML string or file
 *
 * @param string $xmlFile XML string or file to processing
 * @return string|bool Return type of XML, or False on failure.
 */
	public function getTypeFromXmlFile($xmlFile = null) {
		if (empty($xmlFile)) {
			return false;
		}

		if (is_file($xmlFile)) {
			// @codingStandardsIgnoreStart
			$xml = @simplexml_load_file($xmlFile);
			// @codingStandardsIgnoreEnd
		} else {
			// @codingStandardsIgnoreStart
			$xml = @simplexml_load_string($xmlFile);
			// @codingStandardsIgnoreEnd
		}
		if (!$xml) {
			return false;
		}

		$xmlRootName = $xml->getName();
		if (!$xmlRootName) {
			return false;
		}
		$xmlRootName = mb_strtolower($xmlRootName);
		$listValidRoot = [
			'packages',
			'profiles',
			'wpkg',
			'config'
		];
		if (!in_array($xmlRootName, $listValidRoot)) {
			return false;
		}
		if ($xmlRootName !== 'wpkg') {
			return $xmlRootName;
		}

		if ($xml->count() === 0) {
			return false;
		}

		$children = $xml->children();
		$childrenName = $children->getName();
		switch ($childrenName) {
			case 'package':
			case 'checkResults':
				$result = 'databases';
			break;
			case 'host':
				$result = 'hosts';
			break;
			default:
				return false;
		}

		return $result;
	}

/**
 * Return path to XSD file by type
 *
 * @param string $xmlType Type for processing
 * @return string|bool Return path to XSD file, or False on failure.
 */
	public function getXsdForType($xmlType = null) {
		if (empty($xmlType)) {
			return false;
		}

		// @codingStandardsIgnoreStart
		$xsdPath = @constant('XSD_PATH_' . mb_strtoupper($xmlType));
		// @codingStandardsIgnoreEnd

		return $xsdPath;
	}

/**
 * Return data and information from XML string or file by type
 *
 * @param string $xmlFile XML string or file to processing
 * @param string $xmlType Type for processing
 * @return array|bool Return array data and information,
 *  or False on failure.
 */
	protected function _parseXml($xmlFile = null, $xmlType = null) {
		if (empty($xmlType)) {
			return false;
		}
		try {
			$options = ['return' => 'domdocument'];
			$xmlObject = Xml::build($xmlFile, $options);
		} catch (XmlException $e) {
			return false;
		}

		try {
			$xmlArray = Xml::toArray($xmlObject);
		} catch (XmlException $e) {
			return false;
		}

		$data = $this->_extarctDataFromArray($xmlArray, $xmlType);
		$info = $this->_extarctInfoFromArray($xmlArray, $xmlType, $xmlFile);
		unset($xmlArray);
		$result = compact('data', 'info');
		if (empty($data)) {
			return $result;
		}

		$notes = $this->_getListComments($xmlObject, $xmlType, XML_EXPORT_NOTES_COMMENTS_PREFIX, XML_EXPORT_NOTES_COMMENTS_POSTFIX);
		$templates = $this->_getListComments($xmlObject, $xmlType, XML_EXPORT_TEMPLATE_PREFIX, XML_EXPORT_TEMPLATE_POSTFIX);
		unset($xmlObject);

		$idAttrName = $this->_getIdAttributeName($xmlType);
		if ((empty($notes) && empty($templates)) || empty($idAttrName)) {
			return $result;
		}

		$idAttrName = '@' . $idAttrName;
		foreach ($result['data'] as &$dataItem) {
			$idText = Hash::get($dataItem, $idAttrName);
			if (empty($idText)) {
				continue;
			}
			if (isset($notes[$idText])) {
				$dataItem['@notes'] = $notes[$idText];
			}
			if (isset($templates[$idText])) {
				$dataItem['@template'] = !empty($templates[$idText]);
			}
		}
		unset($dataItem);

		return $result;
	}

/**
 * Validates the XML string or file against the XSD file.
 *
 * @param string $xmlFile XML string or file to processing
 * @param string $xmlType Type for processing
 * @param int $idTask The ID of the QueuedTask
 * @return bool Success
 */
	protected function _validateXml($xmlFile = null, $xmlType = null, $idTask = null) {
		if (empty($xmlFile)) {
			$this->_modelExtendQueuedTask->updateTaskErrorMessage($idTask, __('Invalid file path'));
			return false;
		}

		$xsdPath = $this->getXsdForType($xmlType);
		if (empty($xsdPath)) {
			$this->_modelExtendQueuedTask->updateTaskErrorMessage($idTask, __('Invalid file type. Can be one of: package, profile, host or client database.'));
			return false;
		}

		$validateResult = $this->validateXML($xmlFile, $xsdPath);
		if ($validateResult !== true) {
			$errorMsg = RenderXmlData::renderValidateMessages($validateResult);
			$this->_modelExtendQueuedTask->updateTaskErrorMessage($idTask, $errorMsg);
			return false;
		}

		return true;
	}

/**
 * Return data and information from XML string or file by type
 *  after XML validation.
 *
 * @param string $xmlFile XML string or file to processing
 * @param string $xmlType Type for processing
 * @param int $idTask The ID of the QueuedTask
 * @return array|bool Return array data and information.
 *  Trun if data is empty, or False on failure.
 */
	protected function _extarctDataFromXml($xmlFile = null, $xmlType = null, $idTask = null) {
		if (!$this->_validateXml($xmlFile, $xmlType, $idTask)) {
			return false;
		}

		$xmlDataArray = $this->_parseXml($xmlFile, $xmlType);
		if ($xmlDataArray === false) {
			$this->_modelExtendQueuedTask->updateTaskErrorMessage($idTask, __('Invalid input XML file'));
			return false;
		}
		if (empty($xmlDataArray['data'])) {
			$this->_modelExtendQueuedTask->updateTaskErrorMessage($idTask, __('Input XML file is empty'));
			return true;
		}

		return $xmlDataArray;
	}

/**
 * Import information of packages from XML string or file
 *
 * @param string $xmlFile XML string or file to processing
 * @param int $idTask The ID of the QueuedTask
 * @return bool Success
 */
	public function importXmlPackages($xmlFile = '', $idTask = null) {
		$step = 0;
		$maxStep = 1;
		$dataToSave = [];
		$errorMessages = [];
		$result = true;
		set_time_limit(IMPORT_TIME_LIMIT);
		$this->_modelExtendQueuedTask->updateProgress($idTask, 0);
		$xmlDataArray = $this->_extarctDataFromXml($xmlFile, 'package', $idTask);
		if (is_bool($xmlDataArray)) {
			return $xmlDataArray;
		}

		$this->_resetTreeDependencies();
		$this->createNamesCache('Package', 'id_text', !$this->_caseSensitivity);
		$this->createNamesCache('PackageExecuteType');
		$this->createNamesCache('PackageNotifyType');
		$this->createNamesCache('PackageRebootType');
		$this->createNamesCache('PackageActionType');
		$this->createNamesCache('ExitcodeRebootType');
		$this->_createCheckTypesCache();
		foreach ($xmlDataArray['data'] as $xmlDataItem) {
			$dataToSave += $this->_preparePackage($xmlDataItem);
		}
		$listDependencies = $this->_getListFromTreeDependencies();
		if (!empty($listDependencies) && !empty($dataToSave)) {
			$listDependencies = array_intersect_key($listDependencies, $dataToSave);
			$dataToSave = array_replace($listDependencies, $dataToSave) + $dataToSave;
		}

		$maxStep += count($dataToSave);
		foreach ($dataToSave as $dataToSaveItem) {
			if (!$this->_savePackage($errorMessages, $dataToSaveItem)) {
				$result = false;
			}

			$step++;
			if ($step % 10 == 0) {
				$this->_modelExtendQueuedTask->updateTaskProgress($idTask, $step, $maxStep);
			}
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
 * Extracting data of checks from XML data array.
 *
 * @param array $xmlData XML data array for processing
 * @return array Data of checks.
 */
	protected function _extractCheck($xmlData = []) {
		$result = [];
		if (empty($xmlData)) {
			return $result;
		}

		$listAttributes = [
			'path',
			'value'
		];
		$result = ['Check' => $this->_extractAttributes($xmlData, $listAttributes)];
		$checkCondition = mb_strtolower($xmlData['@condition']);
		$cacheCheckType = $this->_getCheckTypeFromCache($xmlData['@type']);
		$result['Check']['condition'] = Hash::get($cacheCheckType, $checkCondition);
		$result['Check']['type'] = constant('CHECK_TYPE_' . mb_strtoupper($xmlData['@type']));
		$result['Attribute'] = $this->_prepareAttributes($xmlData);
		if (isset($xmlData['check']) && ($result['Check']['type'] == CHECK_TYPE_LOGICAL)) {
			$this->_arrayIfY($xmlData['check']);
			foreach ($xmlData['check'] as $check) {
				$result['Child'][] = $this->_extractCheck($check);
			}
		}

		return $result;
	}

/**
 * Preparing data of checks from XML data array.
 *
 * @param array $xmlData XML data array for processing
 * @return array Data of checks.
 */
	protected function _prepareCheck($xmlData = []) {
		$result = [];
		if (empty($xmlData)) {
			return $result;
		}

		$this->_arrayIfY($xmlData);
		foreach ($xmlData as $xmlDataItem) {
			$result[] = $this->_extractCheck($xmlDataItem);
		}

		return $result;
	}

/**
 * Preparing data of variables from XML data array.
 *
 * @param array $xmlData XML data array for processing
 * @return array Data of variables.
 */
	protected function _prepareVariable($xmlData = []) {
		$result = [];
		if (empty($xmlData)) {
			return $result;
		}

		$this->_arrayIfY($xmlData);
		$listAttributes = [
			'name', 'value'
		];
		foreach ($xmlData as $xmlDataItem) {
			$variableInfo = ['Variable' => $this->_extractAttributes($xmlDataItem, $listAttributes)];
			$variableInfo['Attribute'] = $this->_prepareAttributes($xmlDataItem);
			$variableInfo['Check'] = [];
			if (isset($xmlDataItem['condition']['check'])) {
				$variableInfo['Check'] = $this->_prepareCheck($xmlDataItem['condition']['check']);
			}
			$result[] = $variableInfo;
		}

		return $result;
	}

/**
 * Filter valid data of package actions from XML data array.
 *
 * @param array $xmlData XML data array for processing
 * @return array Data of valid package actions.
 */
	protected function _filterPackageAction($xmlData = []) {
		$result = [];
		if (empty($xmlData) || !is_array($xmlData)) {
			return $result;
		}
		foreach ($xmlData as $tagName => $tagListAttributes) {
			if (!is_array($tagListAttributes)) {
				continue;
			}

			$this->_arrayIfY($tagListAttributes);
			foreach ($tagListAttributes as $tagAttributes) {
				if (array_key_exists('@cmd', $tagAttributes) ||
					array_key_exists('@include', $tagAttributes) ||
					($tagName === 'download')) {
					$result[] = ['@type' => $tagName] + $tagAttributes;
				}
			}
		}

		return $result;
	}

/**
 * Preparing data of package action exit codes from XML data array.
 *
 * @param array $xmlData XML data array for processing
 * @return array Data of package action exit codes.
 */
	protected function _preparePackageActionExitCode($xmlData = []) {
		$result = [];
		if (empty($xmlData)) {
			return $result;
		}

		$this->_arrayIfY($xmlData);
		$listAttributes = [
			'code'
		];
		foreach ($xmlData as $xmlDataItem) {
			$exitCodeInfo = ['ExitCode' => $this->_extractAttributes($xmlDataItem, $listAttributes)];
			if (isset($xmlDataItem['@reboot'])) {
				$exitCodeInfo['ExitCode']['reboot_id'] = $this->getIdFromNamesCache('ExitcodeRebootType', $xmlDataItem['@reboot']);
			} else {
				$exitCodeInfo['ExitCode']['reboot_id'] = EXITCODE_REBOOT_NULL;
			}
			$result[] = $exitCodeInfo;
		}

		return $result;
	}

/**
 * Create data of package action type
 *
 * @param string $actionType Package action type for processing
 * @return array Return data of package action type
 */
	protected function _createPackageActionType($actionType = '') {
		$result = [
			'name' => $actionType,
			'builtin' => false,
			'command' => true,
		];

		return $result;
	}

/**
 * Extracting data of package actions from XML data array.
 *
 * @param array $xmlData XML data array for processing
 * @return array Data of package actions.
 */
	protected function _extractPackageAction($xmlData = []) {
		$result = [];
		if (empty($xmlData)) {
			return $result;
		}

		$this->_arrayIfY($xmlData);
		$listAttributes = [
			'@type' => 'action_type_id',
			'@cmd' => 'command',
			'timeout',
			'workdir',
			'@include' => 'include_action_id',
			'@url' => 'command',
			'@target' => 'workdir',
			'@expandURL' => 'timeout'
		];
		$listFieldsActionType = [
			'action_type_id',
			'include_action_id'
		];
		foreach ($xmlData as $xmlDataItem) {
			$packageActionInfo = ['PackageAction' => $this->_extractAttributes($xmlDataItem, $listAttributes)];
			foreach ($listFieldsActionType as $fieldActionType) {
				if (!isset($packageActionInfo['PackageAction'][$fieldActionType])) {
					continue;
				}

				$packageActionTypeId = $this->getIdFromNamesCache('PackageActionType', $packageActionInfo['PackageAction'][$fieldActionType]);
				if (!empty($packageActionTypeId)) {
					$packageActionInfo['PackageAction'][$fieldActionType] = $packageActionTypeId;
				} else {
					$packageActionInfo['PackageActionType'] = $this->_createPackageActionType($packageActionInfo['PackageAction'][$fieldActionType]);
				}
			}
			$packageActionInfo['PackageAction']['command_type_id'] = ACTION_COMMAND_TYPE_COMMAND;
			if (isset($packageActionInfo['PackageAction']['include_action_id']) && !empty($packageActionInfo['PackageAction']['include_action_id'])) {
				$packageActionInfo['PackageAction']['command_type_id'] = ACTION_COMMAND_TYPE_INCLUDE;
			} else {
				$packageActionInfo['PackageAction']['include_action_id'] = null;
			}
			if ($packageActionInfo['PackageAction']['action_type_id'] == ACTION_TYPE_DOWNLOAD) {
				if (isset($packageActionInfo['PackageAction']['timeout']) && (mb_stripos($packageActionInfo['PackageAction']['timeout'], 'true') !== false)) {
					$packageActionInfo['PackageAction']['timeout'] = 1;
				} else {
					$packageActionInfo['PackageAction']['timeout'] = 0;
				}
			}
			$packageActionInfo['Attribute'] = $this->_prepareAttributes($xmlDataItem);
			$packageActionInfo['Check'] = [];
			$packageActionInfo['ExitCode'] = [];
			if (isset($xmlDataItem['condition']['check'])) {
				$packageActionInfo['Check'] = $this->_prepareCheck($xmlDataItem['condition']['check']);
			}
			if (isset($xmlDataItem['exit'])) {
				$packageActionInfo['ExitCode'] = $this->_preparePackageActionExitCode($xmlDataItem['exit']);
			}
			$result[] = $packageActionInfo;
		}

		return $result;
	}

/**
 * Preparing data of package actions from XML data array.
 *
 * @param array $xmlData XML data array for processing
 * @return array Data of package actions.
 */
	protected function _preparePackageAction($xmlData = []) {
		$result = [];
		if (empty($xmlData)) {
			return $result;
		}

		if (isset($xmlData['commands']['command'])) {
			$xmlDataCommand = $xmlData['commands']['command'];
			$result = $this->_extractPackageAction($xmlDataCommand);
		}
		$xmlDataCommand = $this->_filterPackageAction($xmlData);
		$result = array_merge($result, $this->_extractPackageAction($xmlDataCommand));

		return $result;
	}

/**
 * Extracting data of package dependencies from XML data array.
 *
 * @param array $xmlData XML data array for processing
 * @param string $pkgIdText ID of package
 * @param string $modelName Name of the dependent model
 * @throws InternalErrorException if invalid name of dependency model
 * @return array Data of package dependencies.
 */
	protected function _extractPackageDependecies($xmlData = [], $pkgIdText = null, $modelName = null) {
		$listValidModels = [
			'PackagesPackage',
			'PackagesInclude',
			'PackagesChain'
		];
		if (!in_array($modelName, $listValidModels)) {
			throw new InternalErrorException(__('Invalid name of dependency model'));
		}

		$result = [];
		if (empty($xmlData) || empty($modelName) || empty($pkgIdText)) {
			return $result;
		}

		$this->_arrayIfY($xmlData);
		foreach ($xmlData as $xmlDataItem) {
			$dependencyIdText = $xmlDataItem['@package-id'];
			$dependencyId = $this->getIdFromNamesCache('Package', $dependencyIdText, null, !$this->_caseSensitivity);
			if (empty($dependencyId)) {
				$dependencyId = $dependencyIdText;
				$this->_putItemTreeDependencies($pkgIdText, $dependencyId);
			}
			$dependencyInfo = [
				$modelName => ['dependency_id' => $dependencyId]
			];
			$dependencyInfo['Attribute'] = $this->_prepareAttributes($xmlDataItem);
			$result[] = $dependencyInfo;
		}

		return $result;
	}

/**
 * Preparing data of package dependencies from XML data array.
 *
 * @param array $xmlData XML data array for processing
 * @return array Data of package dependencies.
 */
	protected function _preparePackageDependecies($xmlData = []) {
		$result = [];
		if (empty($xmlData)) {
			return $result;
		}

		$bindInfoCfg = [
			'depends' => 'PackagesPackage',
			'include' => 'PackagesInclude',
			'chain' => 'PackagesChain',
		];
		foreach ($bindInfoCfg as $tagName => $bindModel) {
			if (isset($xmlData[$tagName])) {
				$result[$bindModel] = $this->_extractPackageDependecies($xmlData[$tagName], $xmlData['@id'], $bindModel);
			}
		}

		return $result;
	}

/**
 * Extracting data of package attributes from XML data array.
 *
 * @param array $xmlData XML data array for processing
 * @return array Data of package attributes.
 */
	protected function _extractPackageAttributes($xmlData = []) {
		$result = [];
		if (empty($xmlData) || !is_array($xmlData)) {
			return $result;
		}

		$bindInfoCfg = [
			'@reboot' => [
				'bindModel' => 'PackageRebootType',
				'bindField' => 'reboot_id',
				'default' => PACKAGE_REBOOT_FALSE,
			],
			'@execute' => [
				'bindModel' => 'PackageExecuteType',
				'bindField' => 'execute_id',
				'default' => PACKAGE_EXECUTE_DEFAULT,
			],
			'@notify' => [
				'bindModel' => 'PackageNotifyType',
				'bindField' => 'notify_id',
				'default' => PACKAGE_NOTIFY_TRUE,
			],
		];
		foreach ($bindInfoCfg as $attribute => $bindCfg) {
			if (isset($xmlData[$attribute])) {
				$result[$bindCfg['bindField']] = $this->getIdFromNamesCache($bindCfg['bindModel'], $xmlData[$attribute], $bindCfg['default']);
			}
		}

		return $result;
	}

/**
 * Preparing data of profile dependencies from XML data array.
 *
 * @param array $xmlData XML data array for processing
 * @param string $profileIdText ID of profile
 * @return array Data of profile dependencies.
 */
	protected function _prepareProfileDependecies($xmlData = [], $profileIdText = null) {
		$result = [];
		if (empty($xmlData) || empty($profileIdText)) {
			return $result;
		}

		$this->_arrayIfY($xmlData);
		foreach ($xmlData as $xmlDataItem) {
			$dependencyIdText = $xmlDataItem['@profile-id'];
			$dependencyId = $this->getIdFromNamesCache('Profile', $dependencyIdText, null, !$this->_caseSensitivity);
			if (empty($dependencyId)) {
				$dependencyId = $dependencyIdText;
				$this->_putItemTreeDependencies($profileIdText, $dependencyId);
			}
			$dependencyInfo = [
				'ProfilesProfile' => ['dependency_id' => $dependencyId]
			];
			$result[] = $dependencyInfo;
		}

		return $result;
	}

/**
 * Preparing data of profile packages from XML data array.
 *
 * @param array $xmlData XML data array for processing
 * @return array Data of package packages.
 */
	protected function _prepareProfilePackages($xmlData = []) {
		$result = [];
		if (empty($xmlData)) {
			return $result;
		}

		$this->_arrayIfY($xmlData);
		foreach ($xmlData as $xmlDataItem) {
			$packageId = $this->getIdFromNamesCache('Package', $xmlDataItem['@package-id'], $xmlDataItem['@package-id'], !$this->_caseSensitivity);
			$packageInfo = ['PackagesProfile' => ['package_id' => $packageId]];
			$packageInfo['Attribute'] = $this->_prepareAttributes($xmlDataItem);
			$packageInfo['Check'] = [];
			if (isset($xmlDataItem['condition']['check'])) {
				$packageInfo['Check'] = $this->_prepareCheck($xmlDataItem['condition']['check']);
			}
			$result[] = $packageInfo;
		}

		return $result;
	}

/**
 * Preparing data of additional associated profiles of host from XML data array.
 *
 * @param array $xmlData XML data array for processing
 * @return array Data of additional associated profiles of host.
 */
	protected function _prepareHostProfiles($xmlData = []) {
		$result = [];
		if (empty($xmlData)) {
			return $result;
		}

		$this->_arrayIfY($xmlData);
		foreach ($xmlData as $xmlDataItem) {
			$profileId = $this->getIdFromNamesCache('Profile', $xmlDataItem['@id'], $xmlDataItem['@id'], !$this->_caseSensitivity);
			$packageInfo = ['HostsProfile' => ['profile_id' => $profileId]];
			$result[] = $packageInfo;
		}

		return $result;
	}

/**
 * Preparing data of packages from XML data array.
 *
 * @param array $xmlData XML data array for processing
 * @return array Data of packages.
 */
	protected function _preparePackage($xmlData = []) {
		$result = [];
		if (empty($xmlData) || !is_array($xmlData)) {
			return $result;
		}

		$packageInfoDefault = [
			'reboot_id' => PACKAGE_REBOOT_FALSE,
			'execute_id' => PACKAGE_EXECUTE_DEFAULT,
			'notify_id' => PACKAGE_NOTIFY_TRUE,
			'name' => '',
			'id_text' => '',
			'enabled' => true,
			'template' => false,
			'revision' => '',
			'priority' => 0,
			'notes' => ''
		];
		$listAttributes = [
			'@id' => 'id_text',
			'name',
			'revision',
			'priority',
			'notes',
			'template'
		];
		$packageInfo = $this->_extractAttributes($xmlData, $listAttributes);
		$packageInfo += $this->_extractPackageAttributes($xmlData);
		$packageId = $this->getIdFromNamesCache('Package', $xmlData['@id'], null, !$this->_caseSensitivity);
		if (!empty($packageId)) {
			$packageInfo['id'] = $packageId;
		}
		$package = [$this->_modelPackage->alias => $packageInfo + $packageInfoDefault];

		$package += $this->_preparePackageDependecies($xmlData);

		if (isset($xmlData['check'])) {
			$package['Check'] = $this->_prepareCheck($xmlData['check']);
		}

		if (isset($xmlData['variable'])) {
			$package['Variable'] = $this->_prepareVariable($xmlData['variable']);
		}

		$package['PackageAction'] = $this->_preparePackageAction($xmlData);
		$result = [$packageInfo['id_text'] => $package];

		return $result;
	}

/**
 * Preparing data of profiles from XML data array.
 *
 * @param array $xmlData XML data array for processing
 * @return array Data of profiles.
 */
	protected function _prepareProfile($xmlData = []) {
		$result = [];
		if (empty($xmlData) || !is_array($xmlData)) {
			return $result;
		}

		$profileInfoDefault = [
			'id_text' => '',
			'enabled' => true,
			'template' => false,
			'notes' => ''
		];
		$listAttributes = [
			'@id' => 'id_text',
			'notes',
			'template'
		];
		$profileInfo = $this->_extractAttributes($xmlData, $listAttributes);
		$profileInfo += $this->_extractPackageAttributes($xmlData);
		$profileId = $this->getIdFromNamesCache('Profile', $xmlData['@id'], null, !$this->_caseSensitivity);
		if (!empty($profileId)) {
			$profileInfo['id'] = $profileId;
		}
		$profile = [$this->_modelProfile->alias => $profileInfo + $profileInfoDefault];
		if (isset($xmlData['depends'])) {
			$profile['ProfilesProfile'] = $this->_prepareProfileDependecies($xmlData['depends'], $xmlData['@id']);
		}
		if (isset($xmlData['variable'])) {
			$profile['Variable'] = $this->_prepareVariable($xmlData['variable']);
		}
		if (isset($xmlData['package'])) {
			$profile['PackagesProfile'] = $this->_prepareProfilePackages($xmlData['package']);
		}

		$result = [$profileInfo['id_text'] => $profile];

		return $result;
	}

/**
 * Preparing data of hosts from XML data array.
 *
 * @param array $xmlData XML data array for processing
 * @return array Data of hosts.
 */
	protected function _prepareHost($xmlData = []) {
		$result = [];
		if (empty($xmlData) || !is_array($xmlData)) {
			return $result;
		}

		$hostInfoDefault = [
			'mainprofile_id' => null,
			'id_text' => '',
			'enabled' => true,
			'template' => false,
			'notes' => ''
		];
		$listAttributes = [
			'@name' => 'id_text',
			'notes',
			'template'
		];
		$hostInfo = $this->_extractAttributes($xmlData, $listAttributes);
		$hostId = $this->getIdFromNamesCache('Host', $xmlData['@name'], null, true);
		if (!empty($hostId)) {
			$hostInfo['id'] = $hostId;
		}
		$mainprofileId = $this->getIdFromNamesCache('Profile', $xmlData['@profile-id'], null, !$this->_caseSensitivity);
		if (!empty($mainprofileId)) {
			$hostInfo['mainprofile_id'] = $mainprofileId;
		}
		$host = [$this->_modelHost->alias => $hostInfo + $hostInfoDefault];
		$host['Attribute'] = $this->_prepareAttributes($xmlData);

		if (isset($xmlData['variable'])) {
			$host['Variable'] = $this->_prepareVariable($xmlData['variable']);
		}

		if (isset($xmlData['profile'])) {
			$host['HostsProfile'] = $this->_prepareHostProfiles($xmlData['profile']);
		}

		$result = [$hostInfo['id_text'] => $host];

		return $result;
	}

/**
 * Retrieving extended attribute values for report host
 *  from XML information array
 *
 * @param array $xmlInfo XML information array
 * @return array Return list of attribute values
 */
	protected function _prepareDatabaseAttributes($xmlInfo = []) {
		$result = [];
		if (empty($xmlInfo)) {
			return $result;
		}

		if (!empty($xmlInfo['Attribute'])) {
			$result['Attribute'] = $xmlInfo['Attribute'];
		}

		return $result;
	}

/**
 * Preparing data of hosts for report from XML information array.
 *
 * @param array $xmlInfo XML information array for processing
 * @return array Information of hosts.
 */
	protected function _prepareDatabaseHost($xmlInfo = []) {
		$result = [];
		if (empty($xmlInfo)) {
			return $result;
		}

		$name = Hash::get($xmlInfo, 'Attribute.hostname');
		$date = Hash::get($xmlInfo, 'lastChange');
		$id = $this->getIdFromNamesCache('ReportHost', $name);
		if (empty($name) || empty($date)) {
			return $result;
		}
		$result = ['ReportHost' => compact('name', 'date')];
		if (!empty($id)) {
			$result['ReportHost']['id'] = $id;
		}

		return $result;
	}

/**
 * Preparing data of packages for report from XML information array.
 *
 * @param array &$listRevisions List of packages revision
 * @param array &$listPackages List of packages for host
 * @param array $xmlData XML data array for processing
 * @param array $xmlInfo XML information array for processing
 * @return array Information of packages for report.
 */
	protected function _prepareDatabasePackage(array &$listRevisions, array &$listPackages, $xmlData = [], $xmlInfo = []) {
		$result = [];
		if (empty($listRevisions) || empty($xmlData) ||
			!is_array($xmlData) || empty($xmlInfo)) {
			return $result;
		}

		$hostName = Hash::get($xmlInfo, 'Attribute.hostname');
		$packageIdText = $xmlData['@id'];
		$revision = $xmlData['@revision'];
		$packageId = $this->getIdFromNamesCache('Package', $packageIdText, null, !$this->_caseSensitivity);
		if (empty($hostName) || empty($packageIdText) || empty($packageId) ||
			(empty($revision) && ($revision !== '0')) ||
			!isset($listRevisions[$packageIdText])) {
			return $result;
		}
		$hostId = $this->getIdFromNamesCache('ReportHost', $hostName);
		if (empty($hostId)) {
			$hostId = $hostName;
		}
		$stateId = null;
		$packageManualInstall = (string)Hash::get($xmlData, '@manualInstall');
		if (mb_stripos($packageManualInstall, 'true') === 0) {
			$stateId = REPORT_STATE_OK_MANUAL;
		} else {
			$currentRevision = $listRevisions[$packageIdText];
			$resultVerCompare = $this->versionCompare($currentRevision, $revision);
			if ($resultVerCompare === 0) {
				$stateId = REPORT_STATE_OK;
			} elseif ($resultVerCompare > 0) {
				$stateId = REPORT_STATE_UPGRADE;
			} else {
				$stateId = REPORT_STATE_DOWNGRADE;
			}
		}
		$result['Report'] = [
			'host_id' => $hostId,
			'package_id' => $packageId,
			'state_id' => $stateId,
			'revision' => $revision
		];
		if (isset($listPackages[$packageId])) {
			$result['Report']['id'] = $listPackages[$packageId];
			unset($listPackages[$packageId]);
		}

		return $result;
	}

/**
 * Preparing data of WPKG configuration parameters from XML data array.
 *
 * @param array $xmlData XML data array for processing
 * @return array Data of WPKG configuration parameters.
 */
	protected function _prepareConfigParam($xmlData = []) {
		$result = [];
		if (empty($xmlData)) {
			return $result;
		}

		$this->_arrayIfY($xmlData);
		$listAttributes = [
			'name' => 'key', 'value'
		];
		foreach ($xmlData as $xmlDataItem) {
			$configInfo = $this->_extractAttributes($xmlDataItem, $listAttributes);
			if ($configInfo['key'] === 'logLevel') {
				$configInfo['value'] = hexdec($configInfo['value']);
			} else {
				$paramValueType = $this->_modelConfig->getConfigValueType($configInfo['key']);
				switch ($paramValueType) {
					case 'bool':
						$configInfo['value'] = ($configInfo['value'] === 'true');
						break;
					case 'string':
						$configInfo['value'] = str_replace('\\\\', '\\', (string)$configInfo['value']);
						break;
					default:
						if (!empty($paramValueType)) {
							settype($configInfo['value'], $paramValueType);
						}
				}
			}
			$result[] = ['Config' => $configInfo];
		}

		return $result;
	}

/**
 * Preparing data of WPKG configuration languages from XML data array.
 *
 * @param array $xmlData XML data array for processing
 * @return array Data of WPKG configuration languages.
 */
	protected function _prepareConfigLanguage($xmlData = []) {
		$result = [];
		if (empty($xmlData)) {
			return $result;
		}

		$this->_arrayIfY($xmlData);
		$listAttributes = [
			'lcid'
		];
		foreach ($xmlData as $xmlDataItem) {
			$languageInfo = ['ConfigLanguage' => $this->_extractAttributes($xmlDataItem, $listAttributes)];
			if (isset($xmlDataItem['string'])) {
				foreach ($xmlDataItem['string'] as $stringItem) {
					$languageInfo['ConfigLanguage'][$stringItem['@id']] = $stringItem['@'];
				}
			}
			$result[] = $languageInfo;
		}

		return $result;
	}

/**
 * Preparing data of configuration WPKG from XML information array.
 *
 * @param array $xmlData XML data array for processing
 * @return array Information of configuration WPKG.
 */
	protected function _prepareConfig($xmlData = []) {
		$result = [];
		if (empty($xmlData) || !is_array($xmlData)) {
			return $result;
		}

		if (isset($xmlData['param'])) {
			$result['Config'] = $this->_prepareConfigParam($xmlData['param']);
		}
		if (isset($xmlData['languages']['language'])) {
			$result['ConfigLanguage'] = $this->_prepareConfigLanguage($xmlData['languages']['language']);
		}
		if (isset($xmlData['variables']['variable'])) {
			$result['Variable'] = $this->_prepareVariable($xmlData['variables']['variable']);
		}

		return $result;
	}

/**
 * Saving package action types information.
 *
 * @param array &$messages Array of messages.
 * @param array $info Array information package action types to save.
 * @return bool Success.
 */
	protected function _saveActionType(array &$messages, $info = []) {
		if (empty($info) || !isset($info['PackageActionType']) ||
			empty($info['PackageActionType'])) {
			return true;
		}

		$actionType = ['PackageActionType' => $info['PackageActionType']];
		if (isset($actionType['PackageActionType']['name']) &&
			$this->getIdFromNamesCache('PackageActionType', $actionType['PackageActionType']['name'])) {
			return true;
		}

		$result = true;
		$this->_modelPackage->PackageAction->PackageActionType->create(false);
		$resultSaving = (bool)$this->_modelPackage->PackageAction->PackageActionType->save($actionType);
		if ($resultSaving) {
			$pkgActTypeName = $actionType['PackageActionType']['name'];
			$pkgActTypeId = $this->_modelPackage->PackageAction->PackageActionType->id;
			$this->setIdNamesCache('PackageActionType', $pkgActTypeName, $pkgActTypeId);
		} else {
			$result = false;
			$errorType = $this->_modelPackage->PackageAction->PackageActionType->getFullName($actionType);
			$messages[__('Errors')][$errorType] = $this->_modelPackage->PackageAction->PackageActionType->validationErrors;
		}

		return $result;
	}

/**
 * Saving package action exit codes information.
 *
 * @param array &$messages Array of messages.
 * @param array $info Array information package action exit
 *  codes to save.
 * @param int|string $refId ID of associated package action record
 * @return bool Success.
 */
	protected function _saveExitCodes(array &$messages, $info = [], $refId = null) {
		if (empty($info) || !isset($info['ExitCode']) || empty($info['ExitCode'])) {
			return true;
		}

		$result = true;
		foreach ($info['ExitCode'] as $exitCodeInfo) {
			$exitCode = ['ExitCode' => $exitCodeInfo['ExitCode']];
			$exitCode['ExitCode']['package_action_id'] = $refId;
			$this->_modelPackage->PackageAction->ExitCode->create(false);
			$resultSaving = (bool)$this->_modelPackage->PackageAction->ExitCode->save($exitCode);
			if (!$resultSaving) {
				$result = false;
				$errorType = $this->_modelPackage->PackageAction->ExitCode->getFullName($exitCode, null, null, $refId);
				$messages[__('Errors')][$errorType] = $this->_modelPackage->PackageAction->ExitCode->validationErrors;
			}
		}

		return $result;
	}

/**
 * Saving package actions information.
 *
 * @param array &$messages Array of messages.
 * @param array $info Array information package actions.
 * @param int|string $refId ID of associated package record
 * @return bool Success.
 */
	protected function _saveActions(array &$messages, $info = [], $refId = null) {
		if (empty($info) || !isset($info['PackageAction']) ||
			empty($info['PackageAction'])) {
			return true;
		}

		$result = true;
		foreach ($info['PackageAction'] as $actionInfo) {
			if (isset($actionInfo['PackageActionType'])) {
				if (!$this->_saveActionType($messages, $actionInfo)) {
					$result = false;
					continue;
				}

				$actionTypeName = $actionInfo['PackageActionType']['name'];
				$listFieldsActionType = ['action_type_id', 'include_action_id'];
				foreach ($listFieldsActionType as $fieldActionType) {
					if (!isset($actionInfo['PackageAction'][$fieldActionType]) ||
						($actionInfo['PackageAction'][$fieldActionType] !== $actionTypeName)) {
						continue;
					}

					$actionInfo['PackageAction'][$fieldActionType] = $this->getIdFromNamesCache('PackageActionType', $actionTypeName);
				}
			}

			$packageAction = ['PackageAction' => $actionInfo['PackageAction']];
			$packageAction['PackageAction']['package_id'] = $refId;
			if (!$this->_modelPackage->PackageAction->setScopeModel($packageAction['PackageAction']['action_type_id'], $refId)) {
				$messages[__('Errors')][__('Package actions')][] = __('Error on settings scope');
				continue;
			}

			$this->_modelPackage->PackageAction->create(false);
			$resultSaving = (bool)$this->_modelPackage->PackageAction->save($packageAction);
			if (!$resultSaving) {
				$result = false;
				$errorType = $this->_modelPackage->PackageAction->getFullName($packageAction, null, null, $refId);
				$messages[__('Errors')][$errorType] = $this->_modelPackage->PackageAction->validationErrors;
				continue;
			}

			$pkgActId = $this->_modelPackage->PackageAction->id;
			if (!$this->_saveExitCodes($messages, $actionInfo, $pkgActId)) {
				$result = false;
				continue;
			}
			if (!$this->_saveExtAttributes($messages, $actionInfo, ATTRIBUTE_TYPE_PACKAGE, ATTRIBUTE_NODE_ACTION, $pkgActId)) {
				$result = false;
				continue;
			}
			if (!$this->_saveChecks($messages, $actionInfo, null, CHECK_PARENT_TYPE_ACTION, $pkgActId, ATTRIBUTE_TYPE_ACTION)) {
				$result = false;
			}
		}

		return $result;
	}

/**
 * Saving checks information.
 *
 * @param array &$messages Array of messages.
 * @param array $info Array information checks to save.
 * @param int|string $parentCheckId ID of parent check
 * @param int|string $refType ID of type
 * @param int|string $refId ID of associated record
 * @param int|string $attrRefType ID of type extended attributes
 * @return bool Success.
 */
	protected function _saveChecks(array &$messages, $info = [], $parentCheckId = null, $refType = null, $refId = null, $attrRefType = null) {
		if (empty($info) || !isset($info['Check']) || empty($info['Check'])) {
			return true;
		}

		$result = true;
		if (!$this->_modelCheck->setScopeModel($refType, $refId)) {
			$messages[__('Errors')][__('Checks')][] = __('Error on settings scope');
			return false;
		}
		foreach ($info['Check'] as $checkInfo) {
			$check = ['Check' => $checkInfo['Check']];
			$check['Check']['ref_id'] = $refId;
			$check['Check']['ref_type'] = $refType;
			if ($parentCheckId != null) {
				$check['Check']['parent_id'] = $parentCheckId;
			}

			$this->_modelCheck->create(false);
			$resultSaving = (bool)$this->_modelCheck->save($check);
			if (!$resultSaving) {
				$result = false;
				$errorType = $this->_modelCheck->getFullName($check, $refType, null, $refId);
				$messages[__('Errors')][$errorType] = $this->_modelCheck->validationErrors;
				continue;
			}

			$pkgChkId = $this->_modelCheck->id;
			if (!$this->_saveExtAttributes($messages, $checkInfo, $attrRefType, ATTRIBUTE_NODE_CHECK, $pkgChkId)) {
				$result = false;
				continue;
			}

			if (($checkInfo['Check']['type'] == CHECK_TYPE_LOGICAL) && isset($checkInfo['Child'])) {
				$child = ['Check' => $checkInfo['Child']];
				if (!$this->_saveChecks($messages, $child, $pkgChkId, $refType, $refId, $attrRefType)) {
					$result = false;
				}
			}
		}

		return $result;
	}

/**
 * Saving package dependencies information.
 *
 * @param array &$messages Array of messages.
 * @param array $info Array information package dependencies to save.
 * @param int|string $pkgId ID of package record
 * @return bool Success.
 */
	protected function _saveDependenciesPackage(array &$messages, $info = [], $pkgId = null) {
		if (empty($info)) {
			return true;
		}

		if (empty($pkgId)) {
			return false;
		}

		$pkgDependenciesCfg = [
			'PackagesPackage' => ATTRIBUTE_NODE_DEPENDS,
			'PackagesInclude' => ATTRIBUTE_NODE_INCLUDE,
			'PackagesChain' => ATTRIBUTE_NODE_CHAIN
		];
		$result = true;
		foreach ($pkgDependenciesCfg as $modelName => $attrRefNode) {
			if (!isset($info[$modelName]) || empty($info[$modelName])) {
				continue;
			}

			foreach ($info[$modelName] as $pkgDependencyInfo) {
				$pkgDependency = [$modelName => $pkgDependencyInfo[$modelName]];
				$pkgDependency[$modelName]['package_id'] = $pkgId;
				$dependencyId = $pkgDependency[$modelName]['dependency_id'];
				if (!empty($dependencyId) && !ctype_digit((string)$dependencyId)) {
					$pkgDependency[$modelName]['dependency_id'] = $this->getIdFromNamesCache('Package', $dependencyId, $dependencyId, !$this->_caseSensitivity);
				}

				$this->_modelPackage->$modelName->create(false);
				$resultSaving = (bool)$this->_modelPackage->$modelName->save($pkgDependency);
				if (!$resultSaving) {
					$result = false;
					$errorType = $this->_modelPackage->$modelName->getFullName($pkgDependency, null, null, $pkgId);
					$messages[__('Errors')][$errorType] = $this->_modelPackage->$modelName->validationErrors;
					continue;
				}

				$pkgDepId = $this->_modelPackage->$modelName->id;
				if (!$this->_saveExtAttributes($messages, $pkgDependencyInfo, ATTRIBUTE_TYPE_PACKAGE, $attrRefNode, $pkgDepId)) {
					$result = false;
				}
			}
		}

		return $result;
	}

/**
 * Saving package information.
 *
 * @param array &$messages Array of messages.
 * @param array $packageInfo Array information package to save.
 * @return bool Success.
 */
	protected function _saveInfoPackage(array &$messages, $packageInfo = []) {
		if (empty($packageInfo)) {
			return false;
		}

		$package = ['Package' => $packageInfo['Package']];
		$this->_modelPackage->setImportState(true);
		$this->_modelPackage->create(false);
		$resultSaving = (bool)$this->_modelPackage->save($package);
		if (!$resultSaving) {
			$errorType = $this->_modelPackage->getFullName($package);
			$messages[__('Errors')][$errorType] = $this->_modelPackage->validationErrors;
			return false;
		}

		$pkgId = $this->_modelPackage->id;
		$pkgIdLast = $this->_modelPackage->getLastInsertID();
		if ($pkgId === $pkgIdLast) {
			$pkgIdText = $package['Package']['id_text'];
			$this->setIdNamesCache('Package', $pkgIdText, $pkgId, !$this->_caseSensitivity);
		} elseif (!$this->_modelPackage->removeAssocData($pkgId)) {
			return false;
		}

		$this->_saveDependenciesPackage($messages, $packageInfo, $pkgId);
		if (!$this->_saveVariables($messages, $packageInfo, VARIABLE_TYPE_PACKAGE, $pkgId)) {
			return false;
		}
		if (!$this->_saveChecks($messages, $packageInfo, null, CHECK_PARENT_TYPE_PACKAGE, $pkgId, ATTRIBUTE_TYPE_PACKAGE)) {
			return false;
		}
		if (!$this->_saveActions($messages, $packageInfo, $pkgId)) {
			return false;
		}

		return true;
	}

/**
 * Saving package information use transactions.
 *
 * @param array &$messages Array of messages.
 * @param array $packageInfo Array information package to save.
 * @return bool Success.
 */
	protected function _savePackage(array &$messages, $packageInfo = []) {
		$dataSource = $this->_modelPackage->getDataSource();
		$dataSource->begin();

		$result = $this->_saveInfoPackage($messages, $packageInfo);
		if ($result) {
			$dataSource->commit();
		} else {
			$dataSource->rollback();
			$pkgIdText = Hash::get($packageInfo, 'Package.id_text');
			$this->resetIdNamesCache('Package', $pkgIdText, !$this->_caseSensitivity);
		}

		return $result;
	}

/**
 * Import information of profiles from XML string or file
 *
 * @param string $xmlFile XML string or file to processing
 * @param int $idTask The ID of the QueuedTask
 * @return bool Success
 */
	public function importXmlProfiles($xmlFile = '', $idTask = null) {
		$step = 0;
		$maxStep = 1;
		$dataToSave = [];
		$errorMessages = [];
		$result = true;
		set_time_limit(IMPORT_TIME_LIMIT);
		$this->_modelExtendQueuedTask->updateProgress($idTask, 0);
		$xmlDataArray = $this->_extarctDataFromXml($xmlFile, 'profile', $idTask);
		if (is_bool($xmlDataArray)) {
			return $xmlDataArray;
		}

		$this->_resetTreeDependencies();
		$this->createNamesCache('Package', 'id_text', !$this->_caseSensitivity);
		$this->createNamesCache('Profile', null, !$this->_caseSensitivity);
		$this->_createCheckTypesCache();
		foreach ($xmlDataArray['data'] as $xmlDataItem) {
			$dataToSave += $this->_prepareProfile($xmlDataItem);
		}
		$listDependencies = $this->_getListFromTreeDependencies();
		if (!empty($listDependencies) && !empty($dataToSave)) {
			$listDependencies = array_intersect_key($listDependencies, $dataToSave);
			$dataToSave = array_replace($listDependencies, $dataToSave) + $dataToSave;
		}

		$maxStep += count($dataToSave);
		foreach ($dataToSave as $dataToSaveItem) {
			if (!$this->_saveProfile($errorMessages, $dataToSaveItem)) {
				$result = false;
			}

			$step++;
			if ($step % 10 == 0) {
				$this->_modelExtendQueuedTask->updateTaskProgress($idTask, $step, $maxStep);
			}
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
 * Saving profile packages information.
 *
 * @param array &$messages Array of messages.
 * @param array $info Array information profile packages to save.
 * @param int|string $refId ID of profile record
 * @return bool Success.
 */
	protected function _saveProfilePackages(array &$messages, $info = [], $refId = null) {
		if (empty($info) || !isset($info['PackagesProfile']) ||
			empty($info['PackagesProfile'])) {
			return true;
		}

		$result = true;
		foreach ($info['PackagesProfile'] as $packageInfo) {
			$packageAction = ['PackagesProfile' => $packageInfo['PackagesProfile']];
			$packageAction['PackagesProfile']['profile_id'] = $refId;
			$this->_modelProfile->PackagesProfile->create(false);
			$resultSaving = (bool)$this->_modelProfile->PackagesProfile->save($packageAction);
			if (!$resultSaving) {
				$result = false;
				$errorType = $this->_modelProfile->PackagesProfile->getFullName($packageAction, null, null, $refId);
				$messages[__('Errors')][$errorType] = $this->_modelProfile->PackagesProfile->validationErrors;
				continue;
			}

			$pkgProfId = $this->_modelProfile->PackagesProfile->id;
			if (!$this->_saveExtAttributes($messages, $packageInfo, ATTRIBUTE_TYPE_PROFILE, ATTRIBUTE_NODE_PACKAGE, $pkgProfId)) {
				$result = false;
				continue;
			}
			if (!$this->_saveChecks($messages, $packageInfo, null, CHECK_PARENT_TYPE_PROFILE, $pkgProfId, ATTRIBUTE_TYPE_PROFILE)) {
				$result = false;
			}
		}

		return $result;
	}

/**
 * Saving profile dependencies information.
 *
 * @param array &$messages Array of messages.
 * @param array $info Array information profile dependencies to save.
 * @param int|string $profId ID of profile record
 * @return bool Success.
 */
	protected function _saveDependenciesProfile(array &$messages, $info = [], $profId = null) {
		if (empty($info) || !isset($info['ProfilesProfile']) ||
			empty($info['ProfilesProfile'])) {
			return true;
		}

		if (empty($profId)) {
			return false;
		}

		$result = true;
		$modelProfilesProfile = ClassRegistry::init('ProfilesProfile');
		foreach ($info['ProfilesProfile'] as $profDependencyInfo) {
			$profDependency = ['ProfilesProfile' => $profDependencyInfo['ProfilesProfile']];
			$profDependency['ProfilesProfile']['profile_id'] = $profId;
			$dependencyId = $profDependency['ProfilesProfile']['dependency_id'];
			if (!empty($dependencyId) && !ctype_digit((string)$dependencyId)) {
				$profDependency['ProfilesProfile']['dependency_id'] = $this->getIdFromNamesCache('Profile', $dependencyId, $dependencyId, !$this->_caseSensitivity);
			}

			$modelProfilesProfile->create(false);
			$resultSaving = (bool)$modelProfilesProfile->save($profDependency);
			if (!$resultSaving) {
				$result = false;
				$errorType = $modelProfilesProfile->getFullName($profDependency);
				$messages[__('Errors')][$errorType] = $modelProfilesProfile->validationErrors;
			}
		}

		return $result;
	}

/**
 * Saving profile information.
 *
 * @param array &$messages Array of messages.
 * @param array $profileInfo Array information profile to save.
 * @return bool Success.
 */
	protected function _saveInfoProfile(array &$messages, $profileInfo = []) {
		if (empty($profileInfo)) {
			return false;
		}

		$profile = ['Profile' => $profileInfo['Profile']];
		$this->_modelProfile->create(false);
		$resultSaving = (bool)$this->_modelProfile->save($profile);
		if (!$resultSaving) {
			$errorType = $this->_modelProfile->getFullName($profile);
			$messages[__('Errors')][$errorType] = $this->_modelProfile->validationErrors;
			return false;
		}

		$profId = $this->_modelProfile->id;
		$profIdLast = $this->_modelProfile->getLastInsertID();
		if ($profId === $profIdLast) {
			$profIdText = $profile['Profile']['id_text'];
			$this->setIdNamesCache('Profile', $profIdText, $profId, !$this->_caseSensitivity);
		} elseif (!$this->_modelProfile->removeAssocData($profId)) {
			return false;
		}

		$this->_saveDependenciesProfile($messages, $profileInfo, $profId);
		if (!$this->_saveVariables($messages, $profileInfo, VARIABLE_TYPE_PROFILE, $profId)) {
			return false;
		}
		$this->_saveProfilePackages($messages, $profileInfo, $profId);

		return true;
	}

/**
 * Saving profile information use transactions.
 *
 * @param array &$messages Array of messages.
 * @param array $profileInfo Array information profile to save.
 * @return bool Success.
 */
	protected function _saveProfile(array &$messages, $profileInfo = []) {
		$dataSource = $this->_modelProfile->getDataSource();
		$dataSource->begin();

		$result = $this->_saveInfoProfile($messages, $profileInfo);
		if ($result) {
			$dataSource->commit();
		} else {
			$dataSource->rollback();
			$profIdText = Hash::get($profileInfo, 'Profile.id_text');
			$this->resetIdNamesCache('Profile', $profIdText, !$this->_caseSensitivity);
		}

		return $result;
	}

/**
 * Import information of hosts from XML string or file
 *
 * @param string $xmlFile XML string or file to processing
 * @param int $idTask The ID of the QueuedTask
 * @return bool Success
 */
	public function importXmlHosts($xmlFile = '', $idTask = null) {
		$step = 0;
		$maxStep = 1;
		$dataToSave = [];
		$errorMessages = [];
		$result = true;
		set_time_limit(IMPORT_TIME_LIMIT);
		$this->_modelExtendQueuedTask->updateProgress($idTask, 0);
		$xmlDataArray = $this->_extarctDataFromXml($xmlFile, 'host', $idTask);
		if (is_bool($xmlDataArray)) {
			return $xmlDataArray;
		}

		$this->createNamesCache('Host', null, true);
		$this->createNamesCache('Profile', null, !$this->_caseSensitivity);
		$this->_createCheckTypesCache();
		foreach ($xmlDataArray['data'] as $xmlDataItem) {
			$dataToSave += $this->_prepareHost($xmlDataItem);
		}

		$maxStep += count($dataToSave);
		foreach ($dataToSave as $dataToSaveItem) {
			if (!$this->_saveHost($errorMessages, $dataToSaveItem)) {
				$result = false;
			}

			$step++;
			if ($step % 10 == 0) {
				$this->_modelExtendQueuedTask->updateTaskProgress($idTask, $step, $maxStep);
			}
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
 * Saving additional associated profiles of host information.
 *
 * @param array &$messages Array of messages.
 * @param array $info Array information additional associated profiles
 *  of host to save.
 * @param int|string $hostId ID of host record
 * @return bool Success.
 */
	protected function _saveHostProfiles(array &$messages, $info = [], $hostId = null) {
		if (empty($info) || !isset($info['HostsProfile']) ||
			empty($info['HostsProfile'])) {
			return true;
		}

		if (empty($hostId)) {
			return false;
		}

		$result = true;
		$modelHostsProfile = ClassRegistry::init('HostsProfile');
		foreach ($info['HostsProfile'] as $hostProfileInfo) {
			$hostProfile = ['HostsProfile' => $hostProfileInfo['HostsProfile']];
			$hostProfile['HostsProfile']['host_id'] = $hostId;
			$profileId = $hostProfile['HostsProfile']['profile_id'];
			if (!empty($profileId) && !ctype_digit((string)$profileId)) {
				$hostProfile['HostsProfile']['profile_id'] = $this->getIdFromNamesCache('Profile', $profileId, $profileId, !$this->_caseSensitivity);
			}

			$modelHostsProfile->create(false);
			$resultSaving = (bool)$modelHostsProfile->save($hostProfile);
			if (!$resultSaving) {
				$result = false;
				$errorType = $modelHostsProfile->getFullName($hostProfile);
				$messages[__('Errors')][$errorType] = $modelHostsProfile->validationErrors;
			}
		}

		return $result;
	}

/**
 * Saving host information.
 *
 * @param array &$messages Array of messages.
 * @param array $hostInfo Array information host to save.
 * @return bool Success.
 */
	protected function _saveInfoHost(array &$messages, $hostInfo = []) {
		if (empty($hostInfo)) {
			return false;
		}

		$host = ['Host' => $hostInfo['Host']];
		$this->_modelHost->create(false);
		$resultSaving = (bool)$this->_modelHost->save($host);
		if (!$resultSaving) {
			$errorType = $this->_modelHost->getFullName($host);
			$messages[__('Errors')][$errorType] = $this->_modelHost->validationErrors;
			return false;
		}

		$hostId = $this->_modelHost->id;
		$hostIdLast = $this->_modelHost->getLastInsertID();
		if ($hostId === $hostIdLast) {
			$hostIdText = $host['Host']['id_text'];
			$this->setIdNamesCache('Host', $hostIdText, $hostId);
		} elseif (!$this->_modelHost->removeAssocData($hostId)) {
			return false;
		}

		if (!$this->_saveExtAttributes($messages, $hostInfo, ATTRIBUTE_TYPE_HOST, ATTRIBUTE_NODE_HOST, $hostId)) {
			return false;
		}
		if (!$this->_saveVariables($messages, $hostInfo, VARIABLE_TYPE_HOST, $hostId)) {
			return false;
		}
		$this->_saveHostProfiles($messages, $hostInfo, $hostId);

		return true;
	}

/**
 * Saving host information use transactions.
 *
 * @param array &$messages Array of messages.
 * @param array $hostInfo Array information host to save.
 * @return bool Success.
 */
	protected function _saveHost(array &$messages, $hostInfo = []) {
		$result = true;
		$dataSource = $this->_modelHost->getDataSource();
		$dataSource->begin();

		$result = $this->_saveInfoHost($messages, $hostInfo);
		if ($result) {
			$dataSource->commit();
		} else {
			$dataSource->rollback();
		}

		return $result;
	}

/**
 * Import information of client databases from XML string or file.
 *  Used for report.
 *
 * @param string $xmlFile XML string or file to processing
 * @param int $idTask The ID of the QueuedTask
 * @param array $cacheLastUpdate Cache of date last update reports
 * @return bool Success
 */
	public function importXmlDatabases($xmlFile = '', $idTask = null, $cacheLastUpdate = []) {
		$step = 0;
		$maxStep = 2;
		$dataToSave = [];
		$errorMessages = [];
		$result = true;
		$updateProgress = true;
		set_time_limit(IMPORT_TIME_LIMIT);
		if (!empty($cacheLastUpdate)) {
			$updateProgress = false;
		}
		if ($updateProgress) {
			$this->_modelExtendQueuedTask->updateProgress($idTask, 0);
		}
		$xmlDataArray = $this->_extarctDataFromXml($xmlFile, 'database', $idTask);
		if (is_bool($xmlDataArray)) {
			return $xmlDataArray;
		}

		$hostName = (string)Hash::get($xmlDataArray, 'info.Attribute.hostname');
		$hostName = mb_strtolower($hostName);
		$lastChange = Hash::get($xmlDataArray, 'info.lastChange');
		if (empty($cacheLastUpdate)) {
			$cacheLastUpdate = $this->_modelReport->ReportHost->getListLastUpdate($hostName);
		}
		if (!empty($cacheLastUpdate) && isset($cacheLastUpdate[$hostName]) &&
			($lastChange === $cacheLastUpdate[$hostName])) {
			return true;
		}

		$this->createNamesCache('Package', 'id_text', !$this->_caseSensitivity);
		$this->createNamesCache('ReportHost');
		$listRevisions = $this->_modelPackage->getListRevisions();
		$listPackages = $this->_modelReport->getListPackagesForHost($hostName);
		foreach ($xmlDataArray['data'] as $xmlDataItem) {
			$dataToSaveItem = $this->_prepareDatabasePackage($listRevisions, $listPackages, $xmlDataItem, $xmlDataArray['info']);
			if (!empty($dataToSaveItem)) {
				$dataToSave['Report'][] = $dataToSaveItem;
			}
		}
		$dataToSave['ReportHost'] = $this->_prepareDatabaseHost($xmlDataArray['info']);
		$dataToSave['Attribute'] = $this->_prepareDatabaseAttributes($xmlDataArray['info']);

		if ($updateProgress) {
			$this->_modelExtendQueuedTask->updateTaskProgress($idTask, $step, $maxStep);
		}
		if (!$this->_saveReport($errorMessages, $dataToSave)) {
			$result = false;
		}

		if (!$this->_modelReport->deleteReportRecords($listPackages)) {
			$errorMessages['Errors'][] = __('Error on deleting records of report');
			$result = false;
		}

		if ($updateProgress) {
			$this->_modelExtendQueuedTask->updateTaskProgress($idTask, $step, $maxStep);
		}
		if (!empty($idTask) && !empty($errorMessages)) {
			$errorMessagesText = $this->renderErrorMessages($errorMessages);
			$this->_modelExtendQueuedTask->updateTaskErrorMessage($idTask, $errorMessagesText, true);
		}

		return $result;
	}

/**
 * Saving information of the extended attributes of the report host.
 *
 * @param array &$messages Array of messages.
 * @param array $info Array information extended attributes to save.
 * @return bool Success.
 */
	protected function _saveAttributesHostReport(array &$messages, $info = []) {
		if (empty($info) || !isset($info['Attribute']) ||
			empty($info['Attribute'])) {
			return false;
		}

		$hostName = Hash::get($info, 'Attribute.Attribute.hostname');
		$hostId = $this->getIdFromNamesCache('ReportHost', $hostName);
		if (empty($hostId)) {
			return false;
		}

		$attrId = $this->_modelAttribute->getIdFor(ATTRIBUTE_TYPE_HOST, ATTRIBUTE_NODE_REPORT, $hostId);
		if (!empty($attrId)) {
			$info['Attribute']['Attribute']['id'] = $attrId;
		}

		if (!$this->_saveExtAttributes($messages, $info['Attribute'], ATTRIBUTE_TYPE_HOST, ATTRIBUTE_NODE_REPORT, $hostId)) {
			return false;
		}

		return true;
	}

/**
 * Saving report hosts information.
 *
 * @param array &$messages Array of messages.
 * @param array $info Array information report hosts to save.
 * @return bool Success.
 */
	protected function _saveHostReport(array &$messages, $info = []) {
		if (empty($info) || !isset($info['ReportHost']) ||
			empty($info['ReportHost'])) {
			return false;
		}

		$this->_modelReport->ReportHost->create(false);
		$result = (bool)$this->_modelReport->ReportHost->save($info['ReportHost']);
		if (!$result) {
			$errorType = $this->_modelReport->ReportHost->getFullName($info['ReportHost']);
			$messages[__('Errors')][$errorType] = $this->_modelReport->ReportHost->validationErrors;
			return false;
		}

		$hostId = $this->_modelReport->ReportHost->id;
		$hostIdLast = $this->_modelReport->ReportHost->getLastInsertID();
		if ($hostId === $hostIdLast) {
			$this->setIdNamesCache('ReportHost', $info['ReportHost']['ReportHost']['name'], $hostId);
		}

		return true;
	}

/**
 * Saving report information.
 *
 * @param array &$messages Array of messages.
 * @param array $reportInfo Array information report to save.
 * @return bool Success.
 */
	protected function _saveInfoReport(array &$messages, $reportInfo = []) {
		if (empty($reportInfo)) {
			return false;
		}

		if (!$this->_saveHostReport($messages, $reportInfo)) {
			return false;
		}

		if (!$this->_saveAttributesHostReport($messages, $reportInfo)) {
			return false;
		}

		if (!$this->_saveRecordReport($messages, $reportInfo)) {
			return false;
		}

		return true;
	}

/**
 * Saving report record information.
 *
 * @param array &$messages Array of messages.
 * @param array $info Array information report record to save.
 * @return bool Success.
 */
	protected function _saveRecordReport(array &$messages, $info = []) {
		if (empty($info) || !isset($info['Report']) ||
			empty($info['Report'])) {
			return true;
		}

		$result = true;
		foreach ($info['Report'] as $reportInfo) {
			$hostId = $reportInfo['Report']['host_id'];
			if (!empty($hostId) && !ctype_digit((string)$hostId)) {
				$reportInfo['Report']['host_id'] = $this->getIdFromNamesCache('ReportHost', $hostId, $hostId);
			}

			$this->_modelReport->create(false);
			$resultSaving = (bool)$this->_modelReport->save($reportInfo);
			if (!$resultSaving) {
				$result = false;
				$messages[__('Errors')][__('Error on saving report record')][] = $this->_modelReport->validationErrors;
			}
		}

		return $result;
	}

/**
 * Saving report information use transactions.
 *
 * @param array &$messages Array of messages.
 * @param array $reportInfo Array information report to save.
 * @return bool Success.
 */
	protected function _saveReport(array &$messages, $reportInfo = []) {
		$dataSource = $this->_modelReport->getDataSource();
		$dataSource->begin();

		$result = $this->_saveInfoReport($messages, $reportInfo);
		if ($result) {
			$dataSource->commit();
		} else {
			$dataSource->rollback();
		}

		return $result;
	}

/**
 * Import information of client databases from XML string or file.
 *  Used for report.
 *
 * @param string $xmlFile XML string or file to processing
 * @param int $idTask The ID of the QueuedTask
 * @return bool Success
 */
	public function importXmlConfig($xmlFile = '', $idTask = null) {
		$step = 0;
		$maxStep = 2;
		$dataToSave = [];
		$errorMessages = [];
		$result = true;
		set_time_limit(IMPORT_TIME_LIMIT);
		$this->_modelExtendQueuedTask->updateProgress($idTask, 0);
		$xmlDataArray = $this->_extarctDataFromXml($xmlFile, 'config', $idTask);
		if (is_bool($xmlDataArray)) {
			return $xmlDataArray;
		}

		$xmlData = reset($xmlDataArray['data']);
		$dataToSave = $this->_prepareConfig($xmlData);
		$this->_modelExtendQueuedTask->updateTaskProgress($idTask, $step, $maxStep);
		if (!$this->_saveConfig($errorMessages, $dataToSave)) {
			$result = false;
		}

		$this->_modelExtendQueuedTask->updateTaskProgress($idTask, $step, $maxStep);
		if (!empty($idTask) && !empty($errorMessages)) {
			$errorMessagesText = $this->renderErrorMessages($errorMessages);
			$this->_modelExtendQueuedTask->updateTaskErrorMessage($idTask, $errorMessagesText, true);
		}

		return $result;
	}

/**
 * Saving WPKG configuration information use transactions.
 *
 * @param array &$messages Array of messages.
 * @param array $configInfo Array information WPKG configuration to save.
 * @return bool Success.
 */
	protected function _saveConfig(array &$messages, $configInfo = []) {
		$dataSource = $this->_modelConfig->getDataSource();
		$dataSource->begin();

		$result = $this->_saveInfoConfig($messages, $configInfo);
		if ($result) {
			$dataSource->commit();
		} else {
			$dataSource->rollback();
		}

		return $result;
	}

/**
 * Saving WPKG configuration information.
 *
 * @param array &$messages Array of messages.
 * @param array $configInfo Array information WPKG configuration to save.
 * @return bool Success.
 */
	protected function _saveInfoConfig(array &$messages, $configInfo = []) {
		if (empty($configInfo)) {
			return false;
		}

		if (!$this->_modelConfig->deleteConfig() ||
			!$this->_saveConfiguration($messages, $configInfo)) {
			return false;
		}

		if (!$this->_modelConfigLanguage->deleteConfigurationLanguages() ||
			!$this->_saveConfigurationLanguages($messages, $configInfo)) {
			return false;
		}

		if (!$this->_modelVariable->deleteGlobalVariables() ||
			!$this->_saveVariables($messages, $configInfo, VARIABLE_TYPE_CONFIG, 1)) {
			return false;
		}

		return true;
	}

/**
 * Saving WPKG configuration information.
 *
 * @param array &$messages Array of messages.
 * @param array $info Array information WPKG configuration to save.
 * @return bool Success.
 */
	protected function _saveConfiguration(array &$messages, $info = []) {
		if (empty($info) || !isset($info['Config']) ||
			empty($info['Config'])) {
			return true;
		}

		$result = true;
		$this->_modelConfig->create(false);
		if (!$this->_modelConfig->saveAll($info['Config'])) {
			$result = false;
			$messages[__('Errors')][__('Error on saving WPKG configuration')][] = $this->_modelConfig->validationErrors;
		}

		return $result;
	}

/**
 * Saving WPKG configuration language information.
 *
 * @param array &$messages Array of messages.
 * @param array $info Array information WPKG configuration language record to save.
 * @return bool Success.
 */
	protected function _saveConfigurationLanguages(array &$messages, $info = []) {
		if (empty($info) || !isset($info['ConfigLanguage']) ||
			empty($info['ConfigLanguage'])) {
			return true;
		}

		$result = true;
		foreach ($info['ConfigLanguage'] as $reportInfo) {
			$this->_modelConfigLanguage->create(false);
			$resultSaving = (bool)$this->_modelConfigLanguage->save($reportInfo);
			if (!$resultSaving) {
				$result = false;
				$messages[__('Errors')][__('Error on saving WPKG configuration language')][] = $this->_modelConfigLanguage->validationErrors;
			}
		}

		return $result;
	}

/**
 * Return formatted error messages from `libXMLError` object.
 *
 * @param libXMLError $error Error object for processing.
 * @return array Return formatted error messages.
 */
	public function getLibxmlFormatError(libXMLError $error) {
		$message = [];
		$type = __('Other messages');
		switch ($error->level) {
			case LIBXML_ERR_WARNING:
				$type = __('Warnings');
				break;
			case LIBXML_ERR_ERROR:
				$type = __('Errors');
				break;
			case LIBXML_ERR_FATAL:
				$type = __('Fatal errors');
				break;
		}
		$message['code'] = $error->code;
		$message['message'] = $error->message;
		if ($error->file) {
			$message['file'] = $error->file;
		}
		$message['line'] = $error->line;
		$result = [$type => [$message]];

		return $result;
	}

/**
 * Return formatted error messages from libxml.
 *
 * @return array Return formatted error messages.
 */
	public function getLibxmlFormattedErrors() {
		$result = [];
		$errors = libxml_get_errors();
		foreach ($errors as $error) {
			$result = array_merge_recursive($result, $this->getLibxmlFormatError($error));
		}

		return $result;
	}

/**
 * Validates the XML string or file against the XSD file.
 *
 * @param string $xmlFile XML string or file to processing
 * @param string $xsdFile Path to XSD schema file
 * @throws InternalErrorException if invalid path to XSD schema file
 * @return array|bool 
 */
	public function validateXML($xmlFile = null, $xsdFile = null) {
		if (empty($xmlFile)) {
			return true;
		}

		if (empty($xsdFile) || !file_exists($xsdFile)) {
			throw new InternalErrorException(__('Invalid path to XSD schema file'));
		}

		libxml_use_internal_errors(true);
		$xml = new DOMDocument();

		if (is_file($xmlFile)) {
			$xml->load($xmlFile);
		} else {
			$xml->loadXML($xmlFile);
		}

		$result = true;
		if (!$xml->schemaValidate($xsdFile)) {
			$result = $this->getLibxmlFormattedErrors();
		}
		libxml_clear_errors();

		return $result;
	}

/**
 * Import information from XML string or file
 *
 * @param string $xmlFile XML string or file to processing
 * @param int $idTask The ID of the QueuedTask
 * @return bool Success
 */
	public function importXml($xmlFile = '', $idTask = null) {
		$xmlType = $this->getTypeFromXmlFile($xmlFile);
		if (empty($xmlType)) {
			$this->_modelExtendQueuedTask->updateTaskErrorMessage($idTask, __('Invalid file type. Can be one of: package, profile, host or client database.'));
			return false;
		}

		return $this->{'importXml' . ucfirst($xmlType)}($xmlFile, $idTask);
	}

/**
 * Return name of group data.
 *
 * @return string Return name of group data
 */
	public function getGroupName() {
		$result = __('Uploading XML files');

		return $result;
	}

}
