<?php
/**
 * This file is the componet file of the application.
 *  The base actions of the controller, used to creating data
 *  from a template
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
 * @package app.Controller.Component
 */

App::uses('BaseDataComponent', 'Controller/Component');

/**
 * TemplateData Component.
 *
 * The base actions of the controller, used to creating data
 *  from a template
 * @package app.Controller.Component
 */
class TemplateDataComponent extends BaseDataComponent {

/**
 * Constructor
 *
 * @param ComponentCollection $collection A ComponentCollection for this component
 * @param array $settings Array of settings.
 * @throws InternalErrorException if TemplateData behavior is not loaded on target model
 * @return void
 */
	public function __construct(ComponentCollection $collection, $settings = []) {
		parent::__construct($collection, $settings);

		if (!$this->_modelTarget->Behaviors->loaded('TemplateData')) {
			throw new InternalErrorException(__("Behavior '%s' is not loaded in target model", 'TemplateData'));
		}
	}

/**
 * Action `create`. Used to creating data from a template.
 *
 * @param int|string $id ID of record template.
 * @return void
 */
	public function actionCreate($id = null) {
		$targetName = $this->_getTargetName();
		$targetNameI18n = $this->_getTargetName(true);
		if (method_exists($this->_modelTarget, 'getTargetName')) {
			$targetNameI18n = mb_strtolower($this->_modelTarget->getTargetName());
		}
		if (!empty($id) && !$this->_modelTarget->exists($id)) {
			return $this->_controller->ViewExtension->setExceptionMessage(new NotFoundException(__('Invalid ID for %s', $targetNameI18n)));
		}

		$listTemplates = $this->_modelTarget->getListTemplates();
		if (empty($listTemplates)) {
			return $this->_controller->ViewExtension->setExceptionMessage(new InternalErrorException(__('Template list is empty')));
		}

		$this->_controller->view = 'create';
		$modelName = $this->_getTargetName(false);
		$labelAdditAttrib = $this->_modelTarget->getLabelAdditAttrib();
		if ($this->_controller->request->is('post')) {
			$templateId = $this->_controller->request->data('Template.template_id');
			$idText = $this->_controller->request->data($modelName . '.id_text');
			$additAttrib = $this->_controller->request->data($modelName . '.addit_attrib');
			$fieldList = ['id_text'];
			$this->_modelTarget->set($this->_controller->request->data($modelName));
			if ($this->_modelTarget->validates(compact('fieldList')) &&
				$this->_modelTarget->createFromTemplate($templateId, $idText, $additAttrib)) {
				$this->_controller->Flash->success(__('The %s has been created.', mb_ucfirst($targetNameI18n)));

				return $this->_controller->ViewExtension->redirectByUrl(null, $targetName);
			} else {
				$this->_controller->Flash->error(__('The %s could not be created. Please, try again.', $targetNameI18n));
			}
		} else {
			$template = [
				'Template' => [
					'template_id' => $id,
				],
				$modelName => [
					'id_text' => '',
					'addit_attrib' => '',
				]
			];
			$this->_controller->request->data = $template;
			$this->_controller->ViewExtension->setRedirectUrl(null, $targetName);
		}
		$breadCrumbs = [];
		if ($this->_modelTarget->Behaviors->loaded('BreadCrumbExt')) {
			$breadCrumbs = $this->_modelTarget->getBreadcrumbInfo($id);
			$breadCrumbs[] = __('Creating');
		}
		$pageHeader = __('Creating a new %s based on the template', $targetNameI18n);

		$this->_controller->set(compact('breadCrumbs', 'pageHeader', 'listTemplates', 'labelAdditAttrib', 'modelName'));
	}
}
