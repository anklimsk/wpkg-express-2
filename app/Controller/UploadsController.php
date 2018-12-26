<?php
/**
 * This file is the controller file of the application. Used for
 *  upload XML files.
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
 * @package app.Controller
 */

App::uses('AppController', 'Controller');

/**
 * The controller is used for upload XML files.
 *
 * This controller allows to perform the following operations:
 *  - upload XML files.
 *
 * @package app.Controller
 */
class UploadsController extends AppController {

/**
 * The name of this controller. Controller names are plural, named after the model they manipulate.
 *
 * @var string
 * @link http://book.cakephp.org/2.0/en/controllers.html#controller-ActionTypes
 */
	public $name = 'Uploads';

/**
 * Array containing the names of components this controller uses. Component names
 * should not contain the "Component" portion of the class name.
 *
 * @var array
 * @link http://book.cakephp.org/2.0/en/controllers/components.html
 */
	public $components = [
		'CakeTheme.Upload' => ['uploadDir' => UPLOAD_DIR],
	];

/**
 * An array containing the class names of models this controller uses.
 *
 * @var mixed
 * @link http://book.cakephp.org/2.0/en/controllers.html#components-helpers-and-uses
 */
	public $uses = [
		'Import',
	];

/**
 * Called before the controller action.
 *
 * Actions:
 *  - Configure components.
 *
 * @return void
 * @link http://book.cakephp.org/2.0/en/controllers.html#request-life-cycle-callbacks
 */
	public function beforeFilter() {
		$this->Security->unlockedActions = [
			'admin_upload'
		];

		parent::beforeFilter();
	}

/**
 * Base of action `index`. Used to start upload files.
 *
 * @return void
 */
	protected function _index() {
		$this->view = 'index';
		$maxfilesize = $this->Import->getLimitFileSize();
		$acceptfiletypes = $this->Import->getAcceptFileTypes(false);
		$validxmltypes = $this->Import->getNameValidXmlTypes();
		$breadCrumbs = $this->Import->getBreadcrumbInfo();
		$breadCrumbs[] = __('File selection');
		$pageHeader = __('Uploading XML files');

		$this->set(compact('maxfilesize', 'acceptfiletypes', 'validxmltypes',
			'breadCrumbs', 'pageHeader'));
	}

/**
 * Action `index`. Used to start upload files.
 *  User role - administrator.
 *
 * @return void
 */
	public function admin_index() {
		$this->_index();
	}

/**
 * Base of action `upload`. Used to upload XML file.
 *
 * @throws BadRequestException if request is not `AJAX` or not `JSON`
 * @return void
 */
	protected function _upload() {
		Configure::write('debug', 0);
		if (!$this->request->is('ajax') || !$this->RequestHandler->prefers('json')) {
			throw new BadRequestException(__('Invalid request'));
		}

		$this->request->allowMethod('post');
		$uploadDir = $this->Upload->getUploadDir();
		$maxFileSize = $this->Import->getLimitFileSize();
		$acceptfiletypes = $this->Import->getAcceptFileTypes(true);
		$data = $this->Upload->upload($maxFileSize, $acceptfiletypes);
		if (!isset($data['files'][0])) {
			$this->set(compact('data'));
			$this->set('_serialize', 'data');

			return;
		}

		$oFile = $data['files'][0];
		$fileName = $uploadDir . $oFile->name;
		if (!file_exists($fileName)) {
			$this->set(compact('data'));
			$this->set('_serialize', 'data');

			return;
		}

		$this->loadModel('CakeTheme.ExtendQueuedTask');
		$taskParam = compact('fileName');
		if ($this->ExtendQueuedTask->createJob('ImportXml', $taskParam, null, 'import')) {
			$this->Flash->success(nl2br(__("XML file uploaded successfully.\nInformation will be processed by queue.")));
			$this->ViewExtension->setProgressSseTask('ImportXml');
		} else {
			$oFile->error = __('Unable to create queue task.');
		}

		$data['files'][0] = $oFile;
		$this->set(compact('data'));
		$this->set('_serialize', 'data');
	}

/**
 * Action `upload`. Used to upload XML file.
 *  User role - administrator.
 *
 * @return void
 */
	public function admin_upload() {
		$this->_upload();
	}

}
