<?php
/**
 * This file is the behavior file of the application. Is used to
 *  copy data item.
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
App::uses('Hash', 'Utility');
App::uses('ClassRegistry', 'Utility');
App::uses('RenderXmlData', 'Utility');

/**
 * The behavior is used to copy data item.
 *
 * @package app.Model.Behavior
 */
class CopyItemBehavior extends ModelBehavior {

/**
 * Setup this behavior with the specified configuration settings
 *
 * @param Model $model Model using this behavior
 * @param array $config Configuration settings for $model
 * @throws InternalErrorException if method "getXMLdata" and "getIdAttributeXpath" is not found in model
 * @return void
 */
	public function setup(Model $model, $config = array()) {
		$requiredMethods = [
			'getXMLdata',
			'getIdAttributeXpath'
		];
		foreach ($requiredMethods as $requiredMethod) {
			if (!method_exists($model, $requiredMethod)) {
				throw new InternalErrorException(__("Method '%s' is not found in model %s", $requiredMethod, $model->name));
			}
		}
	}

/**
 * Copy data item by record ID
 *
 * @param Model $model Model using this behavior.
 * @param int|string $id Record ID to copy data
 * @return bool Success.
 */
	public function makeCopy(Model $model, $id = null) {
		if (empty($id) || !$model->exists($id)) {
			return false;
		}

		$xmlDataArray = $model->getXMLdata($id, false, true, true);
		if (empty($xmlDataArray)) {
			return false;
		}

		reset($xmlDataArray);
		$root = key($xmlDataArray);
		if (isset($xmlDataArray[$root][XML_SPECIFIC_TAG_DISABLED])) {
			$xmlInfo = $xmlDataArray[$root][XML_SPECIFIC_TAG_DISABLED];
			unset($xmlDataArray[$root][XML_SPECIFIC_TAG_DISABLED]);
			$xmlDataArray[$root] = array_merge($xmlDataArray[$root], $xmlInfo);
		}

		$idAttrXpath = $model->getIdAttributeXpath();
		if (empty($idAttrXpath)) {
			return false;
		}

		$idText = Hash::get($xmlDataArray, $idAttrXpath);
		if (empty($idText)) {
			return false;
		}

		if (preg_match('/.+_copy(?:_([\d]+))?$/ui', $idText, $matches)) {
			if (!isset($matches[1])) {
				$idText .= '_2';
			} else {
				$numCopy = $matches[1];
				$idText = preg_replace('/(.+_copy_)[\d]+$/ui', '${1}' . (++$numCopy), $idText);
				if (empty($idText)) {
					return false;
				}
			}
		} else {
			$idText .= '_copy';
		}

		$xmlDataArray = Hash::insert($xmlDataArray, $idAttrXpath, $idText);
		$xmlString = RenderXmlData::renderXml($xmlDataArray, true);
		$modelImport = ClassRegistry::init('Import');

		return $modelImport->importXml($xmlString);
	}
}
