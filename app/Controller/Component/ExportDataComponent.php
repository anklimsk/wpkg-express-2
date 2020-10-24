<?php
/**
 * This file is the componet file of the application.
 *  The base actions of the controller, used to preview,
 *  export and download data.
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
 * @copyright Copyright 2018-2019, Andrey Klimov.
 * @package app.Controller.Component
 */

App::uses('BaseDataComponent', 'Controller/Component');
App::uses('RenderXmlData', 'Utility');

/**
 * ExportData Component.
 *
 * The base actions of the controller, used to preview,
 *  export and download data.
 * @package app.Controller.Component
 */
class ExportDataComponent extends BaseDataComponent {

/**
 * Constructor
 *
 * @param ComponentCollection $collection A ComponentCollection for this component
 * @param array $settings Array of settings.
 * @throws InternalErrorException if Restore behavior is not loaded on
 *  target model and method 'getXMLdata' is not found
 * @return void
 */
	public function __construct(ComponentCollection $collection, $settings = []) {
		parent::__construct($collection, $settings);

		if (!$this->_modelTarget->Behaviors->loaded('Restore') &&
			!method_exists($this->_modelTarget, 'getXMLdata')) {
			throw new InternalErrorException(__("Method '%s' is not found in target model", 'getXMLdata'));
		}
	}

/**
 * Action `preview`. Used to preview XML data.
 *
 * @param int|string $id ID of record for previewing
 * @return void
 */
	public function preview($id = null) {
		$resultValidate = $this->_validateId($id);
		if ($resultValidate !== true) {
			return $resultValidate;
		}

		$formatXml = true;
		$exportDisable = false;
		$exportNotes = true;
		$exportDisabled = true;
		$xmlDataArray = $this->_modelTarget->getXMLdata($id, $exportDisable, $exportNotes, $exportDisabled);
		$fullName = null;
		$targetName = $this->_getTargetName();
		$targetNamePlural = $this->_getTargetNamePlural();
		$controllerName = $targetNamePlural;
		$targetNameI18n = $this->_getTargetName(true);
		if (method_exists($this->_modelTarget, 'getTargetName')) {
			$targetNameI18n = mb_strtolower($this->_modelTarget->getTargetName());
		}
		$useBreadCrumbExt = false;
		if ($this->_modelTarget->Behaviors->loaded('BreadCrumbExt')) {
			$fullName = $this->_modelTarget->getFullName($id);
			$controllerName = $this->_modelTarget->getControllerName();
			$useBreadCrumbExt = true;
		}
		$downloadUrl = [
			'controller' => $controllerName,
			'action' => 'download',
			$id,
			'ext' => 'xml'
		];

		$selLine = [];
		$outXML = RenderXmlData::renderXml($xmlDataArray, $formatXml);

		$modelImport = ClassRegistry::init('Import');
		$xsdPath = $modelImport->getXsdForType($targetName);
		$result = true;
		if (!empty($xsdPath)) {
			$result = $modelImport->validateXML($outXML, $xsdPath);
		}
		$errorMsg = null;
		if ($result !== true) {
			$errorMsg = RenderXmlData::renderValidateMessages($result);
			$selLine = $modelImport->getListErrorLines($result);
		}
		$breadCrumbs = [];
		if ($useBreadCrumbExt) {
			$breadCrumbs = $this->_modelTarget->getBreadcrumbInfo($id);
			$breadCrumbs[] = __('Previewing');
		}
		$pageHeader = __('Preview XML');
		$headerMenuActions = [
			[
				'fas fa-file-download',
				__('Download XML file'),
				$downloadUrl,
				['title' => __('Download XML file'), 'skip-modal' => true]
			],
		];

		if (empty($errorMsg)) {
			$this->_controller->Flash->success(__('XSD schema validation passed.'));
		} else {
			$this->_controller->Flash->error(__('XML failed to pass XSD schema validation'), 'flash_error');
		}

		$this->_controller->set(compact('fullName', 'selLine', 'errorMsg',
			'outXML', 'breadCrumbs', 'pageHeader', 'headerMenuActions'));
	}

/**
 * Action `download`. Used to download XML data.
 *
 * @param int|string $id ID of record or type name for downloading data
 * @param bool $checkIsExists If True, check ID of record for
 *  downloading data is exists
 * @throws InternalErrorException if Restore behavior is not loaded on
 *  target model and method 'getDownloadName' is not found
 * @throws BadRequestException if request is not XML
 * @return CakeResponse
 */
	public function download($id = null, $checkIsExists = true) {
		if (!$this->_modelTarget->Behaviors->loaded('Restore') &&
			!method_exists($this->_modelTarget, 'getDownloadName')) {
			throw new InternalErrorException(__("Method '%s' is not found in target model", 'getDownloadName'));
		}
		if (!$this->_controller->RequestHandler->isXml()) {
			throw new BadRequestException(__('Invalid request'));
		}
		if ($checkIsExists && !empty($id) && ctype_digit((string)$id)) {
			$resultValidate = $this->_validateId($id);
			if ($resultValidate !== true) {
				return $resultValidate;
			}
		}

		$this->_controller->autoRender = false;
		$formatXml = true;
		$exportDisable = false;
		$exportNotes = true;
		$exportDisabled = true;
		$xmlDataArray = $this->_modelTarget->getXMLdata($id, $exportDisable, $exportNotes, $exportDisabled);
		$outXML = RenderXmlData::renderXml($xmlDataArray, $formatXml);

		$isFullData = empty($id);
		if (!is_array($xmlDataArray)) {
			$xmlDataArray = $id;
		}
		$name = $this->_modelTarget->getDownloadName($xmlDataArray, $isFullData);
		if ($this->_controller->request->is('msie')) {
			$name = rawurlencode($name);
		}
		$this->_controller->response->type('xml');
		$this->_controller->response->body($outXML);
		$this->_controller->response->download($name);

		return $this->_controller->response;
	}

/**
 * Action `export`. Used to export XML data.
 *
 * @param int|string $id ID of record for downloading data
 * @throws BadRequestException if request is not XML
 * @return void
 */
	public function export($id = null) {
		if (!$this->_controller->RequestHandler->isXml()) {
			throw new BadRequestException(__('Invalid request'));
		}
		$resultValidate = $this->_validateId($id);
		if ($resultValidate !== true) {
			return $resultValidate;
		}

		$this->_controller->response->disableCache();
		$this->_controller->cacheAction = [
			$this->_controller->view => [
				'callbacks' => true,
				'duration' => WEEK
			]
		];
		$formatXml = $this->_modelSetting->getConfig('FormatXml');
		$exportDisable = $this->_modelSetting->getConfig('ExportDisable');
		$exportNotes = $this->_modelSetting->getConfig('ExportNotes');
		$exportDisabled = $this->_modelSetting->getConfig('ExportDisabled');
		$xmlDataArray = $this->_modelTarget->getXMLdata($id, $exportDisable, $exportNotes, $exportDisabled);
		$outXML = RenderXmlData::renderXml($xmlDataArray, $formatXml);

		$this->_controller->set(compact('outXML'));
	}
}
