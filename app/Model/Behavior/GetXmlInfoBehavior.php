<?php
/**
 * This file is the behavior file of the application. Used to
 *  get information from an array of XML data.
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
 * @package app.Model.Behavior
 */

App::uses('ModelBehavior', 'Model');
App::uses('Inflector', 'Utility');
App::uses('Hash', 'Utility');

/**
 * The behavior is used to get information from an array of XML data.
 *
 * @package app.Model.Behavior
 */
class GetXmlInfoBehavior extends ModelBehavior {

/**
 * Return XML attribute value from data array
 *
 * @param Model $model Model using this behavior
 * @param array $xmlDataArray Data for processing
 * @param string $valueXpath The path to extract from $xmlDataArray
 * @return mixed Return XML attribute value, or False on failure
 */
	public function getAttributeValueFromXml(Model $model, $xmlDataArray = [], $valueXpath = null) {
		if (empty($xmlDataArray) || !is_array($xmlDataArray) || empty($valueXpath)) {
			return false;
		}

		$value = Hash::get($xmlDataArray, $valueXpath);
		if (!empty($value)) {
			return $value;
		}

		$valueXpathArr = explode('.', $valueXpath);
		if (count($valueXpathArr) > 1) {
			$disabledTag = [XML_SPECIFIC_TAG_DISABLED];
			array_splice($valueXpathArr, 1, 0, $disabledTag);
		}
		$valueXpath = implode('.', $valueXpathArr);
		$value = Hash::get($xmlDataArray, $valueXpath);

		return $value;
	}

/**
 * Return download name from data array
 *
 * @param Model $model Model using this behavior
 * @param array $xmlDataArray Data for processing
 * @param string $nameXpath The path to extract from $xmlDataArray
 * @param bool $isFullData Flag of full data
 * @throws InternalErrorException if method "getTargetName" and "getGroupName" is not found in model
 * @return string Return download name
 */
	public function getDownloadNameFromXml(Model $model, $xmlDataArray = [], $nameXpath = null, $isFullData = false) {
		$requiredMethods = [
			'getTargetName',
			'getGroupName'
		];
		foreach ($requiredMethods as $requiredMethod) {
			if (!method_exists($model, $requiredMethod)) {
				throw new InternalErrorException(__("Method '%s' is not found in model %s", $requiredMethod, $model->name));
			}
		}

		$ext = '.xml';
		$name = $model->getTargetName();
		if (empty($xmlDataArray) || !is_array($xmlDataArray) || (!$isFullData && empty($nameXpath))) {
			$name .= '_' . __('unknown') . $ext;
			return $name;
		}

		if ($isFullData) {
			$name = $model->getGroupName();
			$name .= $ext;
			return $name;
		}

		$nameVal = $this->getAttributeValueFromXml($model, $xmlDataArray, $nameXpath);
		if (empty($nameVal)) {
			$name .= '_' . __('unknown') . $ext;
			return $name;
		}
		$name .= '_' . $nameVal;

		$name = Inflector::slug($name);
		$name .= $ext;

		return $name;
	}

/**
 * Return prepared comment string for XML
 *
 * @param Model $model Model using this behavior
 * @param string $comment Comment text for processing
 * @return string Return prepared comment
 */
	public function prepareXmlComment(Model $model, $comment = '') {
		if (empty($comment)) {
			return $comment;
		}

		return preg_replace('/[\-]{2,}/', '-', $comment);
	}
}
