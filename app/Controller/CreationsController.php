<?php
/**
 * This file is the controller file of the application. Used for
 *  create XML.
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
 * @package app.Controller
 */

App::uses('AppController', 'Controller');
App::uses('CakeNumber', 'Utility');
App::uses('RenderXmlData', 'Utility');

/**
 * The controller is used for create XML.
 *
 * This controller allows to perform the following operations:
 *  - create XML.
 *
 * @package app.Controller
 */
class CreationsController extends AppController {

/**
 * The name of this controller. Controller names are plural, named after the model they manipulate.
 *
 * @var string
 * @link http://book.cakephp.org/2.0/en/controllers.html#controller-attributes
 */
	public $name = 'Creations';

/**
 * An array containing the class names of models this controller uses.
 *
 * @var mixed
 * @link http://book.cakephp.org/2.0/en/controllers.html#components-helpers-and-uses
 */
	public $uses = [
		'Import',
		'CakeTheme.ExtendQueuedTask'
	];

/**
 * Base of action `index`. Used to create XML.
 *
 * @return void
 */
	protected function _index() {
		$this->view = 'index';
		$selLine = [];
		$errorMsg = null;
		$maxFileSize = $this->Import->getLimitFileSize();
		if ($this->request->is('post')) {
			$xmlString = trim((string)$this->request->data('Create.xml'));
			$sizeXmlString = strlen(utf8_decode($xmlString));
			if (($sizeXmlString > 0) && ($sizeXmlString < $maxFileSize)) {
				$validateResult = $this->Import->validateXMLstring($xmlString);
				if ($validateResult !== true) {
					$errorMsg = RenderXmlData::renderValidateMessages($validateResult);
					$selLine = $this->Import->getListErrorLines($validateResult);
					$this->Flash->error(__('XML failed to pass XSD schema validation'), 'flash_error');
				} else {
					$taskParam = ['fileName' => $xmlString];
					if ($this->ExtendQueuedTask->createJob('ImportXml', $taskParam, null, 'import')) {
						$this->Flash->success(nl2br(__("XML created successfully.\nInformation will be processed by queue.")));
						$this->ViewExtension->setProgressSseTask('ImportXml');
					} else {
						$this->Flash->error(__('Unable to create queue task.'));
					}
				}
			} elseif (empty($xmlString) || ($sizeXmlString == 0)) {
				$this->Flash->error(__('XML is empty'));
			} else {
				$this->Flash->error(__('XML size exceeded. Maximum size %s.', CakeNumber::toReadableSize($maxFileSize)));
			}
		} else {
			$this->request->data('Create.xml', $this->Import->getDefaultXML());
		}
		$validxmltypes = $this->Import->getNameValidXmlTypes();
		$listXmlConfigUrl = XML_CREATE_LIST_XML_CONFIG_URL;
		$breadCrumbs = $this->Import->getBreadcrumbInfo();
		$breadCrumbs[] = __('Inputting XML text');
		$pageHeader = __('Creating XML');

		$this->set(compact('selLine', 'errorMsg', 'maxFileSize', 'validxmltypes',
			'listXmlConfigUrl', 'breadCrumbs', 'pageHeader'));
	}

/**
 * Action `index`. Used to create XML.
 *  User role - administrator.
 *
 * @return void
 */
	public function admin_index() {
		$this->_index();
	}

}
