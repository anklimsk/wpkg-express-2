<?php
/**
 * This file is the model file of the application.
 * Method call after the installation process is completed.
 *
 * CakeInstaller: Installer of CakePHP web application.
 * @copyright Copyright 2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package app.Model
 */

App::uses('CakeInstallerAppModel', 'CakeInstaller.Model');
App::uses('Folder', 'Utility');
App::uses('ClassRegistry', 'Utility');

/**
 * InstallerCompleted for installer.
 *
 * @package app.Model
 */
class InstallerCompleted extends CakeInstallerAppModel {

/**
 * Name of the model.
 *
 * @var string
 * @link http://book.cakephp.org/2.0/en/models/model-attributes.html#name
 */
	public $name = 'InstallerCompleted';

/**
 * Custom database table name, or null/false if no table association is desired.
 *
 * @var string
 * @link http://book.cakephp.org/2.0/en/models/model-attributes.html#usetable
 */
	public $useTable = false;

/**
 * This method is called after the installation process is complete.
 *  Used to import XML template files.
 *
 * @return bool Success
 */
	public function intsallCompleted() {
		$wwwRoot = Configure::read('App.www_root');
		if (empty($wwwRoot)) {
			return false;
		}

		$xmlDirPath = $wwwRoot . 'files' . DS . 'XML' . DS;
		if (!file_exists($xmlDirPath)) {
			return false;
		}

		$orderList = [
			'CONFIG',
			'PACKAGE',
			'PROFILE',
			'HOST'
		];
		$oXmlDir = new Folder($xmlDirPath, false, false);
		list(, $xmlFiles) = $oXmlDir->read(true, false, true);
		if (empty($xmlFiles)) {
			return true;
		}

		$result = true;
		$modelImport = ClassRegistry::init('Import');
		foreach ($orderList as $orderItem) {
			$xmlFilesType = preg_grep('/' . $orderItem . '.*_TEMPLATE\.xml$/', $xmlFiles);
			if (empty($xmlFilesType)) {
				continue;
			}

			foreach ($xmlFilesType as $xmlFile) {
				$xmlFilePath = $xmlFile;
				if (!$modelImport->importXml($xmlFilePath)) {
					$result = false;
				}
			}
		}

		return $result;
	}
}
