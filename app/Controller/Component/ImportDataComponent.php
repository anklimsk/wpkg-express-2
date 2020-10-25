<?php
/**
 * This file is the componet file of the application.
 *  Used to save and restore table settings such as sorting, number of records
 *  per page and filter parameters.
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
 * @copyright Copyright 2018-2020, Andrey Klimov.
 * @package app.Controller.Component
 */

App::uses('BaseDataComponent', 'Controller/Component');
App::uses('CakeNumber', 'Utility');
App::uses('RenderXmlData', 'Utility');

/**
 * BookmarkTable Component.
 *
 * Used to save and restore table settings such as sorting, number of records
 *  per page and filter parameters.
 * @package app.Controller.Component
 */
class ImportDataComponent extends BaseDataComponent {

/**
 * Object of model `Import`
 *
 * @var object
 */
	protected $_modelImport = null;

/**
 * Object of model `ExtendQueuedTask`
 *
 * @var object
 */
	protected $_modelExtendQueuedTask = null;

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

		if (!method_exists($this->_modelTarget, 'getXMLdata')) {
			throw new InternalErrorException(__("Method '%s' is not found in target model", 'getXMLdata'));
		}

		$this->_modelImport = ClassRegistry::init('Import');
		$this->_modelExtendQueuedTask = ClassRegistry::init('ExtendQueuedTask');
	}

/**
 * Action `import`. Used to import XML data.
 *
 * @param int|string $id ID of record for downloading data
 * @throws BadRequestException if request is not XML
 * @return void
 */
	public function import($id = null) {
		$resultValidate = $this->_validateId($id);
		if ($resultValidate !== true) {
			return $resultValidate;
		}

		$selLine = [];
		$warningMsg = null;
		$errorMsg = null;
		$maxFileSize = $this->_modelImport->getLimitFileSize();
		$targetName = $this->_getTargetName();
		if ($this->_controller->request->is('post')) {
			$xmlString = trim((string)$this->_controller->request->data('Create.xml'));
			$sizeXmlString = strlen(utf8_decode($xmlString));
			if (($sizeXmlString > 0) && ($sizeXmlString < $maxFileSize)) {
				$validateResult = $this->_modelImport->validateXMLstring($xmlString);
				if ($validateResult !== true) {
					$errorMsg = RenderXmlData::renderValidateMessages($validateResult);
					$selLine = $this->_modelImport->getListErrorLines($validateResult);
					$this->_controller->Flash->error(__('XML failed to pass XSD schema validation'), 'flash_error');
				} else {
					$taskParam = ['fileName' => $xmlString];
					if ($this->_modelExtendQueuedTask->createJob('ImportXml', $taskParam, null, 'import')) {
						$this->_controller->Flash->success(nl2br(__("XML updated successfully.\nInformation will be processed by queue.\nAfter processing the information, refresh the page.")));
						$this->_controller->ViewExtension->setProgressSseTask('ImportXml');

						return $this->_controller->ViewExtension->redirectByUrl(null, $targetName);
					} else {
						$this->_controller->Flash->error(__('Unable to create queue task.'));
					}
				}
			} elseif (empty($xmlString) || ($sizeXmlString == 0)) {
				$this->_controller->Flash->error(__('XML is empty'));
			} else {
				$this->_controller->Flash->error(__('XML size exceeded. Maximum size %s.', CakeNumber::toReadableSize($maxFileSize)));
			}
		} else {
			$this->_controller->ViewExtension->setRedirectUrl(null, $targetName);

			if ($this->_modelTarget->name === 'Package') {
				$warningMsg = __('When updating a Package revision, the previous configuration is not archived!');
			}

			$formatXml = $this->_modelSetting->getConfig('FormatXml');
			$exportDisable = $this->_modelSetting->getConfig('ExportDisable');
			$exportNotes = $this->_modelSetting->getConfig('ExportNotes');
			$exportDisabled = $this->_modelSetting->getConfig('ExportDisabled');
			$xmlDataArray = $this->_modelTarget->getXMLdata($id, $exportDisable, $exportNotes, $exportDisabled);
			$outXML = RenderXmlData::renderXml($xmlDataArray, $formatXml);
			$this->_controller->request->data('Create.xml', $outXML);
			$this->_controller->request->data('Create.type', $targetName);
		}

		$fullName = null;
		$breadCrumbs = [];
		$pageHeader = __('Editing XML');
		if ($this->_modelTarget->Behaviors->loaded('BreadCrumbExt')) {
			$breadCrumbs = $this->_modelTarget->getBreadcrumbInfo($id);
			$breadCrumbs[] = __('Editing XML');
			$fullName = $this->_modelTarget->getFullName($id);
		}

		$this->_controller->set(compact('breadCrumbs', 'pageHeader', 'fullName',
			'selLine', 'warningMsg', 'errorMsg'));
	}
}
