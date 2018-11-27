<?php
/**
 * This file is the behavior file of the application. Is used to
 *  create new object based on template.
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
 * The behavior is used to create new object based on template.
 *
 * @package app.Model.Behavior
 */
class TemplateDataBehavior extends ModelBehavior {

/**
 * Setup this behavior with the specified configuration settings.
 *
 * @param Model $model Model using this behavior
 * @param array $config Configuration settings for $model
 * @throws InternalErrorException if field "template" is not found in model
 * @throws InternalErrorException if methods "getXMLdata", "getIdAttributeXpath"
 *  or "getTemplateElementXpath" is not found in model
 * @throws InternalErrorException if Behavior GetList not loaded in model
 * @return void
 */
	public function setup(Model $model, $config = array()) {
		if (!$model->schema('template')) {
			throw new InternalErrorException(__("Field '%s' is not found in model %s", 'template', $model->name));
		}
		$requiredMethods = [
			'getXMLdata',
			'getIdAttributeXpath',
			'getTemplateElementXpath',
		];
		foreach ($requiredMethods as $requiredMethod) {
			if (!method_exists($model, $requiredMethod)) {
				throw new InternalErrorException(__("Method '%s' is not found in model %s", $requiredMethod, $model->name));
			}
		}
		if (!$model->Behaviors->loaded('GetList')) {
			throw new InternalErrorException(__('Behavior %s not loaded in %s', 'GetList', $model->name));
		}
	}

/**
 * Checking the record that it is a template
 *
 * @param Model $model Model using this behavior
 * @param int|string $id Record ID to check
 * @return bool Return True, if template
 */
	public function checkIsTemplate(Model $model, $id = null) {
		$model->id = $id;

		return $model->field('template');
	}

/**
 * Return label of additional attribute for input in form.
 *  Can be an overload in the model.
 *
 * @param Model $model Model using this behavior
 * @return string Return label of additional attribute.
 */
	public function getLabelAdditAttrib(Model $model) {
		return false;
	}

/**
 * Return the path to extract value of additional attribute
 *  from XML data array. Can be an overload in the model.
 *
 * @param Model $model Model using this behavior
 * @return string Return label of additional attribute.
 */
	public function getAdditionalAttributeXpath(Model $model) {
		return false;
	}

/**
 * Return list of templates
 *
 * @param Model $model Model using this behavior
 * @return array Return list of templates
 */
	public function getListTemplates(Model $model) {
		$conditions = [$model->alias . '.template' => true];

		return $model->getList($conditions);
	}

/**
 * Create new object based on template
 *
 * @param Model $model Model using this behavior
 * @param int|string $id Record ID of template
 * @param string $idText Value of attribute `id_text`
 * @param string $additAttrib Value of additional attribute
 * @param bool $useTaskQueue Flag of using task queue
 * @return bool Success
 */
	public function createFromTemplate(Model $model, $id = null, $idText = null, $additAttrib = null, $useTaskQueue = false) {
		if (empty($id) || empty($idText) || !$model->exists($id)) {
			return false;
		}

		if (!$this->checkIsTemplate($model, $id)) {
			return false;
		}

		$xmlDataArray = $model->getXMLdata($id, false, true, false);
		if (empty($xmlDataArray)) {
			return false;
		}

		$idAttrXpath = $model->getIdAttributeXpath();
		if (empty($idAttrXpath)) {
			return false;
		}

		if (!Hash::check($xmlDataArray, $idAttrXpath)) {
			return false;
		}
		$xmlDataArray = Hash::insert($xmlDataArray, $idAttrXpath, $idText);

		$additAttrXpath = $model->getAdditionalAttributeXpath();
		if (!empty($additAttrXpath) && !empty($additAttrib)) {
			if (!Hash::check($xmlDataArray, $additAttrXpath)) {
				return false;
			}
			$xmlDataArray = Hash::insert($xmlDataArray, $additAttrXpath, $additAttrib);
		}

		$templateElemXpath = $model->getTemplateElementXpath();
		if (!empty($templateElemXpath) || !Hash::check($xmlDataArray, $templateElemXpath)) {
			$xmlDataArray = Hash::remove($xmlDataArray, $templateElemXpath);
		}
		$xmlString = RenderXmlData::renderXml($xmlDataArray, true);

		if ($useTaskQueue) {
			$modelExtendQueuedTask = ClassRegistry::init('CakeTheme.ExtendQueuedTask');
			$taskParam = ['fileName' => $xmlString];
			$result = (bool)$modelExtendQueuedTask->createJob('ImportXml', $taskParam, null, 'import');
		} else {
			$modelImport = ClassRegistry::init('Import');
			$result = $modelImport->importXml($xmlString);
		}

		return $result;
	}
}
