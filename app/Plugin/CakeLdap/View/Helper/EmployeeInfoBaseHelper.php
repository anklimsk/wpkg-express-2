<?php
/**
 * This file is the helper file of the plugin.
 * Employee information helper.
 *
 * CakeLdap: Authentication of users by member group of Active Directory.
 * @copyright Copyright 2017-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.View.Helper
 */

App::uses('CakeLdapAppHelper', 'CakeLdap.View/Helper');
App::uses('Hash', 'Utility');

/**
 * Employee information helper.
 *
 * @package plugin.View.Helper
 */
class EmployeeInfoBaseHelper extends CakeLdapAppHelper {

/**
 * List of helpers used by this helper
 *
 * @var array
 */
	public $helpers = [
		'Html',
		'Number',
		'Time',
		'CakeTheme.ViewExtension'
	];

/**
 * Base64 encoded JPEG image of `No photo`
 *
 * @var string
 */
	protected $_noPhotoData = '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';

/**
 * Return string Base64 encoded JPEG image of `No photo`
 *
 * @return string Return string Base64 encoded.
 */
	protected function _getNoPhotoData() {
		return $this->_noPhotoData;
	}

/**
 * Return string of method name for type
 *
 * @param string $type Type name
 * @return string Return string name of helper method.
 */
	protected function _getMethodNameForType($type = null) {
		$result = '_getValueForString';
		if (empty($type)) {
			return $result;
		}

		$type = mb_strtolower($type);
		$methodName = '_getValueFor' . Inflector::camelize($type);
		if (method_exists($this, $methodName)) {
			$result = $methodName;
		}

		return $result;
	}

/**
 * Return string of rendered element for extended information
 *
 * @param string $fieldName Field name in format `Model.field`.
 * @param mixed $data Data for rendered element.
 * @return string Return string of rendered element.
 */
	protected function _getExtendedInfo($fieldName = null, $data = null) {
		$result = $this->getEmptyText();
		if (empty($fieldName)) {
			return $result;
		}
		list($modelName, ) = pluginSplit($fieldName);
		if (empty($modelName)) {
			return $result;
		}
		$elementName = 'infoEmployeeExtend' . $modelName;
		if ($this->_View->elementExists('CakeLdap.' . $elementName)) {
			$result = $this->_View->element('CakeLdap.' . $elementName, [
				'data' => $data]);
		}

		return $result;
	}

/**
 * Return string used as empty text
 *
 * @return string Return string used as empty text.
 */
	public function getEmptyText() {
		$result = $this->ViewExtension->showEmpty('');

		return $result;
	}

/**
 * Return string of rendered item information of employee
 *
 * @param array $employee Information of employee.
 * @param array $fieldsLabel Labels for fields.
 * @param array $fieldsConfig Configuration of fields.
 * @param array $linkOpt Options for link.
 * @param bool $returnTableRow If True, return result for row of table.
 *  Otherwise, return as list item.
 * @return array Return array of rendered item in format:
 *  $returnTableRow is True:
 *  array(
 *      `Rendered item 1`,
 *      `Rendered item 2`,
 *      ...
 *  )
 *  $returnTableRow is False:
 *  array(
 *      `Label for field 1` => `Rendered item for field 1`,
 *      `Label for field 2` => `Rendered item for field 2`,
 *      ...
 *  )
 */
	public function getInfo($employee = [], $fieldsLabel = [], $fieldsConfig = [], $linkOpt = [], $returnTableRow = true) {
		$result = [];
		if (empty($employee) || empty($fieldsLabel) ||
			!is_array($employee) || !is_array($fieldsLabel)) {
			return $result;
		}

		$fieldsConfig = (array)$fieldsConfig;
		foreach ($fieldsLabel as $fieldName => $fieldLabel) {
			if (!is_string($fieldLabel)) {
				$fieldLabel = $fieldName;
			}

			$data = Hash::extract($employee, $fieldName);

			$type = '';
			if (isset($fieldsConfig[$fieldName]['type'])) {
				$type = $fieldsConfig[$fieldName]['type'];
			}

			if ($type === 'element') {
				$result[$fieldLabel] = $this->_getExtendedInfo($fieldName, $data);
				continue;
			}

			$truncate = false;
			if (isset($fieldsConfig[$fieldName]['truncate'])) {
				$truncate = $fieldsConfig[$fieldName]['truncate'];
			}

			if (empty($result) && $returnTableRow) {
				$type = 'employee';
			}

			$dataList = [];
			foreach ($data as $dataItem) {
				$methodName = $this->_getMethodNameForType($type);
				$dataInfo = call_user_func([$this, $methodName], $dataItem, $returnTableRow, $employee, $linkOpt);

				$dataText = '';
				if (!is_array($dataInfo)) {
					$dataText = $dataInfo;
				} elseif (is_array($dataInfo) && isset($dataInfo[0])) {
					$dataText = $dataInfo[0];
				}
				if (!empty($dataText) && $returnTableRow && $truncate) {
					$dataText = $this->ViewExtension->truncateText($dataText, CAKE_LDAP_EMPLOYEE_TABLE_TEXT_MAX_LENGTH);
				}
				if (is_array($dataInfo) && isset($dataInfo[0])) {
					$dataText = [$dataText] + $dataInfo;
				}
				$dataList[] = $dataText;
			}

			if (count($dataList) > 1) {
				$dataOut = $this->Html->nestedList($dataList, ['class' => 'list-unstyled list-compact'], [], 'ul');
			} else {
				$dataOut = $this->ViewExtension->showEmpty(array_shift($dataList));
			}
			$result[$fieldLabel] = $dataOut;
		}
		if (!empty($result) && $returnTableRow) {
			$result = array_values($result);
		}

		return $result;
	}

/**
 * Return string of formatted date
 *
 * @param string $data Date for format.
 * @param bool $returnTableRow If True, return result for row of table.
 *  Otherwise, return as list item.
 * @param string $timeFormat strftime format string.
 * @return array|string If $returnTableRow is True, Return array of table cell.
 *  Otherwise, return string.
 */
	protected function _getValueForDateTimeBase($data = null, $returnTableRow = true, $timeFormat = null) {
		if (empty($timeFormat)) {
			$timeFormat = '%x %X';
		}
		if (empty($data)) {
			$result = $this->ViewExtension->showEmpty($data);
		} else {
			$result = $this->Time->i18nFormat($data, $timeFormat);
		}

		if ($returnTableRow) {
			$result = [$result, ['class' => 'text-center']];
		}

		return $result;
	}

/**
 * Return string of IMG tag with base64 decoded string
 *
 * @param string $data Data for decode.
 * @param bool $returnTableRow If True, add class `center-block`.
 * @param int $size Size of image in px.
 * @return string Return HTML tag IMG.
 */
	protected function _getValueForPhotoImageBase($data = null, $returnTableRow = true, $size = 0) {
		$size = (int)$size;
		if ($size <= 0) {
			$size = CAKE_LDAP_PHOTO_SIZE_SMALL;
			if (!$returnTableRow) {
				$size = CAKE_LDAP_PHOTO_SIZE_LARGE;
			}
		}
		$dataStr = false;
		if (!empty($data) && isBinary($data)) {
			$dataStr = base64_encode($data);
		}
		if ($dataStr === false) {
			$dataStr = $this->_getNoPhotoData();
		}
		$src = 'data:' . CAKE_LDAP_PHOTO_DEFAULT_MIME_TYPE . ';base64,' . $dataStr;
		$width = $size;
		$height = $size;
		$class = 'img-thumbnail img-responsive';
		if ($returnTableRow) {
			$class .= ' center-block';
		}
		$style = 'max-width:' . $width . 'px;min-height:' . $height . 'px;max-height:' . $height . 'px;';
		$result = $this->Html->tag('img', null, compact('src', 'class', 'style'));

		return $result;
	}

/**
 * Return string of A tag with href to binded model
 *
 * @param string $data Data for checking empty.
 * @param bool $returnTableRow [optional] Unused - backward compatibility.
 * @param array $fullData Full data of employee.
 * @param array $linkOpt Options for link.
 * @param string $idField Hash path to ID value.
 * @param string $controller Controller name of binded model.
 * @return string Return HTML tag A.
 */
	protected function _getValueForBindModelBase($data = null, $returnTableRow = true, $fullData = null, $linkOpt = null, $idField = null, $controller = null) {
		$result = $this->ViewExtension->showEmpty($data);
		if (empty($idField) || empty($controller) ||
			empty($fullData) || !is_array($fullData)) {
			return $result;
		}

		$idField = (string)$idField;
		if (ctype_digit($idField)) {
			$id = $idField;
		} else {
			$id = Hash::get($fullData, $idField);
		}

		if (!empty($id)) {
			/*
			$methodLink = 'pjaxLink';
			if ($returnTableRow)
			*/
			$methodLink = 'popupModalLink';
			$result = $this->ViewExtension->$methodLink(
				$result,
				['controller' => $controller, 'action' => 'view', $id],
				$linkOpt
			);
		}

		return $result;
	}

/**
 * Return string of formatted number
 *
 * @param float $data A floating point number
 * @param bool $returnTableRow If True, return result for row of table.
 *  Otherwise, return as list item.
 * @param int $places The amount of desired precision.
 * @return array|string If $returnTableRow is True, Return array of table cell.
 *  Otherwise, return string.
 */
	protected function _getValueForNumberBase($data = null, $returnTableRow = true, $places = 0) {
		if ($data === null) {
			$result = $this->ViewExtension->showEmpty($data);
		} else {
			$result = $this->Number->format($data, ['thousands' => ' ', 'before' => '', 'places' => (int)$places]);
		}

		if ($returnTableRow) {
			$result = [$result, ['class' => 'text-right']];
		}

		return $result;
	}

/**
 * Return string with value, if value is not empty. Otherwise `Empty message`.
 *
 * @param string $data Data for checking empty.
 * @param bool $returnTableRow [optional] Unused - backward compatibility.
 * @return string Return value or `Empty message`.
 */
	protected function _getValueForStringBase($data = null, $returnTableRow = true) {
		$result = $this->ViewExtension->showEmpty($data);

		return $result;
	}

/**
 * Return string of formatted number
 *
 * @param int $data Number
 * @param bool $returnTableRow If True, return result for row of table.
 *  Otherwise, return as list item.
 * @return array|string If $returnTableRow is True, Return array of table cell.
 *  Otherwise, return string.
 */
	protected function _getValueForInteger($data = null, $returnTableRow = true) {
		return $this->_getValueForNumberBase($data, $returnTableRow, 0);
	}

/**
 * Return string of formatted number
 *
 * @param int $data Number
 * @param bool $returnTableRow If True, return result for row of table.
 *  Otherwise, return as list item.
 * @return array|string If $returnTableRow is True, Return array of table cell.
 *  Otherwise, return string.
 */
	protected function _getValueForBiginteger($data = null, $returnTableRow = true) {
		return $this->_getValueForNumberBase($data, $returnTableRow, 0);
	}

/**
 * Return string of formatted number
 *
 * @param float $data Number
 * @param bool $returnTableRow If True, return result for row of table.
 *  Otherwise, return as list item.
 * @return array|string If $returnTableRow is True, Return array of table cell.
 *  Otherwise, return string.
 */
	protected function _getValueForFloat($data = null, $returnTableRow = true) {
		return $this->_getValueForNumberBase($data, $returnTableRow, 2);
	}

/**
 * Return string of formatted date without time
 *
 * @param string $data Date for format
 * @param bool $returnTableRow If True, return result for row of table.
 *  Otherwise, return as list item.
 * @return array|string If $returnTableRow is True, Return array of table cell.
 *  Otherwise, return string.
 */
	protected function _getValueForDate($data = null, $returnTableRow = true) {
		return $this->_getValueForDateTimeBase($data, $returnTableRow, '%x');
	}

/**
 * Return string of formatted time without date
 *
 * @param string $data Date for format
 * @param bool $returnTableRow If True, return result for row of table.
 *  Otherwise, return as list item.
 * @return array|string If $returnTableRow is True, Return array of table cell.
 *  Otherwise, return string.
 */
	protected function _getValueForTime($data = null, $returnTableRow = true) {
		return $this->_getValueForDateTimeBase($data, $returnTableRow, '%X');
	}

/**
 * Return string of formatted date and time
 *
 * @param string $data Date for format
 * @param bool $returnTableRow If True, return result for row of table.
 *  Otherwise, return as list item.
 * @return array|string If $returnTableRow is True, Return array of table cell.
 *  Otherwise, return string.
 */
	protected function _getValueForDatetime($data = null, $returnTableRow = true) {
		return $this->_getValueForDateTimeBase($data, $returnTableRow, '%x %X');
	}

/**
 * Return string of formatted date and time
 *
 * @param int|string $data Date for format
 * @param bool $returnTableRow If True, return result for row of table.
 *  Otherwise, return as list item.
 * @return array|string If $returnTableRow is True, Return array of table cell.
 *  Otherwise, return string.
 */
	protected function _getValueForTimestamp($data = null, $returnTableRow = true) {
		return $this->_getValueForDateTimeBase($data, $returnTableRow, '%x %X');
	}

/**
 * Return string state of data `Yes` or `No`
 *
 * @param bool $data Data for checking
 * @param bool $returnTableRow If True, return result for row of table.
 *  Otherwise, return as list item.
 * @return array|string If $returnTableRow is True, Return array of table cell.
 *  Otherwise, return string.
 */
	protected function _getValueForBoolean($data = null, $returnTableRow = true) {
		$result = $this->ViewExtension->yesNo($data);
		if ($returnTableRow) {
			$result = [$result, ['class' => 'text-center']];
		}

		return $result;
	}

/**
 * Return string with GUID
 *
 * @param string $data GUID for display
 * @param bool $returnTableRow If True, return result for row of table.
 *  Otherwise, return as list item.
 * @return array|string If $returnTableRow is True, Return array of table cell.
 *  Otherwise, return string.
 */
	protected function _getValueForGuid($data = null, $returnTableRow = true) {
		$result = $this->ViewExtension->showEmpty($data, '{' . h($data) . '}');

		return $result;
	}

/**
 * Return string of IMG tag with base64 decoded string
 *
 * @param string $data Data for decode
 * @param bool $returnTableRow If True, add class `center-block`.
 * @return array|string If $returnTableRow is True, Return array of table cell.
 *  Otherwise, return string.
 */
	protected function _getValueForPhoto($data = null, $returnTableRow = true) {
		return $this->_getValueForPhotoImageBase($data, $returnTableRow);
	}

/**
 * Return string of A tag with href with `mailto:`
 *
 * @param string $data E-mail
 * @param bool $returnTableRow If True, return result for row of table.
 *  Otherwise, return as list item.
 * @return array|string If $returnTableRow is True, Return array of table cell.
 *  Otherwise, return string.
 */
	protected function _getValueForMail($data = null, $returnTableRow = true) {
		$mailLink = $this->ViewExtension->showEmpty($data);
		if (filter_var($data, FILTER_VALIDATE_EMAIL)) {
			$mailLink = $this->Html->link(h($data), 'mailto:' . $data);
		}
		$result = $this->ViewExtension->showEmpty($data, $mailLink);

		return $result;
	}

/**
 * Return string with value, if value is not empty. Otherwise `Empty message`
 *
 * @param string $data Data for display
 * @param bool $returnTableRow If True, return result for row of table.
 *  Otherwise, return as list item.
 * @return array|string If $returnTableRow is True, Return array of table cell.
 *  Otherwise, return string.
 */
	protected function _getValueForString($data = null, $returnTableRow = true) {
		return $this->_getValueForStringBase($data);
	}

/**
 * Return string with value, if value is not empty. Otherwise `Empty message`
 *
 * @param string $data Data for display
 * @param bool $returnTableRow If True, return result for row of table.
 *  Otherwise, return as list item.
 * @return array|string If $returnTableRow is True, Return array of table cell.
 *  Otherwise, return string.
 */
	protected function _getValueForText($data = null, $returnTableRow = true) {
		return $this->_getValueForStringBase($data);
	}

/**
 * Return string `?????`
 *
 * @param string $data [optional] Unused - backward compatibility.
 * @param bool $returnTableRow If True, return result for row of table.
 *  Otherwise, return as list item.
 * @return array|string If $returnTableRow is True, Return array of table cell.
 *  Otherwise, return string.
 */
	protected function _getValueForBinary($data = null, $returnTableRow = true) {
		$result = '?????';

		return $result;
	}

/**
 * Return string of A tag with href to binded model `Employee`
 *
 * @param string $data Data for checking empty.
 * @param bool $returnTableRow [optional] Unused - backward compatibility.
 * @param array $fullData Full data of employee.
 * @param array $linkOpt Options for link.
 * @return string Return HTML tag A.
 */
	protected function _getValueForEmployee($data = null, $returnTableRow = true, $fullData = null, $linkOpt = null) {
		return $this->_getValueForBindModelBase($data, $returnTableRow, $fullData, $linkOpt, 'Employee.id', 'employees');
	}

/**
 * Return string of A tag with href to binded model `Manager`
 *
 * @param string $data Data for checking empty.
 * @param bool $returnTableRow [optional] Unused - backward compatibility.
 * @param array $fullData Full data of employee.
 * @param array $linkOpt Options for link.
 * @return string Return HTML tag A.
 */
	protected function _getValueForManager($data = null, $returnTableRow = true, $fullData = null, $linkOpt = null) {
		$result = $this->_getValueForBindModelBase($data, $returnTableRow, $fullData, $linkOpt, 'Manager.id', 'employees');
		if (empty($data)) {
			return $result;
		}

		$managerTitle = Hash::get($fullData, 'Manager.' . CAKE_LDAP_LDAP_ATTRIBUTE_TITLE);
		if (empty($managerTitle)) {
			return $result;
		}

		$result .= ' - ' . h($managerTitle);

		return $result;
	}

/**
 * Return string of A tag with href to binded model `Subordinate`
 *
 * @param string $data Data for checking empty.
 * @param bool $returnTableRow [optional] Unused - backward compatibility.
 * @param array $fullData Full data of employee.
 * @param array $linkOpt Options for link.
 * @return string Return HTML tag A.
 */
	protected function _getValueForSubordinate($data = null, $returnTableRow = true, $fullData = null, $linkOpt = null) {
		$subordinateId = Hash::get($data, 'id');
		$subordinateName = Hash::get($data, 'name');
		$result = $this->_getValueForBindModelBase($subordinateName, $returnTableRow, $fullData, $linkOpt, $subordinateId, 'employees');
		if (empty($data)) {
			return $result;
		}

		$subordinateTitle = Hash::get($data, CAKE_LDAP_LDAP_ATTRIBUTE_TITLE);
		if (empty($subordinateTitle)) {
			return $result;
		}

		$result .= ' - ' . h($subordinateTitle);

		return $result;
	}

/**
 * Return string of A tag with href to binded model `Department`
 *
 * @param string $data Data for checking empty.
 * @param bool $returnTableRow [optional] Unused - backward compatibility.
 * @param array $fullData Full data of employee.
 * @param array $linkOpt Options for link.
 * @return string Return HTML tag A.
 */
	protected function _getValueForDepartment($data = null, $returnTableRow = true, $fullData = null, $linkOpt = null) {
		if (!is_array($linkOpt)) {
			$linkOpt = [];
		}
		$linkOpt['data-modal-size'] = 'sm';

		return $this->_getValueForBindModelBase($data, $returnTableRow, $fullData, $linkOpt, 'Department.id', 'departments');
	}

/**
 * Return string of IMG tag with base64 decoded string
 *
 * @param array $data Data of photo.
 * @param bool $returnTableRow If True, add class `center-block`.
 * @param int $size Size of image in px.
 * @return string Return HTML tag IMG.
 */
	public function getPhotoImage($data = null, $returnTableRow = true, $size = 0) {
		$result = $this->_getValueForPhotoImageBase($data, $returnTableRow, $size);

		return $result;
	}

/**
 * Return string full name of employee include: last name,
 *  given name (first name) and middle name, or short name:
 *  last name and initials.
 *
 * @param array $fullData Full data of employee.
 * @return string Return full name of employee
 */
	public function getFullName($fullData = null) {
		$result = $this->getEmptyText();
		if (empty($fullData)) {
			return $result;
		}

		$fieldsFullName = [
			[
				CAKE_LDAP_LDAP_ATTRIBUTE_SURNAME,
				CAKE_LDAP_LDAP_ATTRIBUTE_GIVEN_NAME,
				CAKE_LDAP_LDAP_ATTRIBUTE_MIDDLE_NAME
			],
			[
				CAKE_LDAP_LDAP_ATTRIBUTE_DISPLAY_NAME,
				CAKE_LDAP_LDAP_ATTRIBUTE_NAME
			]
		];
		$dataFullName = [];
		foreach ($fieldsFullName[0] as $field) {
			$dataItem = h(Hash::get($fullData, $field));
			if (!empty($dataItem)) {
				$dataFullName[] = mb_ucfirst($dataItem);
			}
		}
		if (count($dataFullName) == 3) {
			$result = implode(' ', $dataFullName);

			return $result;
		}

		foreach ($fieldsFullName[1] as $field) {
			$dataItem = h(Hash::get($fullData, $field));
			if (empty($dataItem)) {
				continue;
			}

			$result = mb_ucfirst($dataItem);
		}

		return $result;
	}
}
