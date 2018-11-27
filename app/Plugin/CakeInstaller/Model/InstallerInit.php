<?php
/**
 * This file is the model file of the plugin.
 * Initialization database tables model.
 * Methods to initialization of database tables the initial values
 *
 * CakeInstaller: Installer of CakePHP web application.
 * @copyright Copyright 2016-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.Model
 */

App::uses('CakeInstallerAppModel', 'CakeInstaller.Model');
App::uses('ClassRegistry', 'Utility');
App::uses('Inflector', 'Utility');

/**
 * InstallerInit for installer.
 *
 * @package plugin.Model
 */
class InstallerInit extends CakeInstallerAppModel {

/**
 * Name of the model.
 *
 * @var string
 * @link http://book.cakephp.org/2.0/en/models/model-attributes.html#name
 */
	public $name = 'InstallerInit';

/**
 * Custom database table name, or null/false if no table association is desired.
 *
 * @var string
 * @link http://book.cakephp.org/2.0/en/models/model-attributes.html#usetable
 */
	public $useTable = false;

/**
 * Initialization of database table the initial values
 *
 * @param string $table Database table name for initialization.
 *  Seeking method `initDbTable()` in the appropriate model and call it.
 * @return bool Result of coll method `initDbTable()`
 */
	public function initDbTable($table = null) {
		if (empty($table)) {
			return false;
		}

		$initModel = ClassRegistry::init(Inflector::classify($table), true);
		if ($initModel === false) {
			return false;
		}

		if (!method_exists($initModel, 'initDbTable')) {
			return false;
		}

		$ds = $initModel->getDataSource();
		if (!$ds->truncate($initModel)) {
			return false;
		}

		return $initModel->initDbTable();
	}
}
