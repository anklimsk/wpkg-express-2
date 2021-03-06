<?php
/**
 * This file is the model file of the plugin.
 * Method call after the installation process is completed.
 *
 * CakeInstaller: Installer of CakePHP web application.
 * @copyright Copyright 2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.Model
 */

App::uses('CakeInstallerAppModel', 'CakeInstaller.Model');

/**
 * InstallerCompleted for installer.
 *
 * @package plugin.Model
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
 *
 * @return bool Success
 */
	public function intsallCompleted() {
		return true;
	}
}
