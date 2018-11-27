<?php
/**
 * This file is the controller file of the plugin.
 * Show state of installation for application
 *
 * CakeInstaller: Installer of CakePHP web application.
 * @copyright Copyright 2016-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.Controller
 */

App::uses('CakeInstallerAppController', 'CakeInstaller.Controller');

/**
 * The controller is used for show state of installation for application
 *
 * @package plugin.Controller
 */
class CheckController extends CakeInstallerAppController {

/**
 * The name of this controller. Controller names are plural, named after the model they manipulate.
 *
 * @var string
 * @link http://book.cakephp.org/2.0/en/controllers.html#controller-attributes
 */
	public $name = 'Check';

/**
 * An array containing the class names of models this controller uses.
 *
 * Example: `public $uses = array('Product', 'Post', 'Comment');`
 *
 * Can be set to several values to express different options:
 *
 * - `true` Use the default inflected model name.
 * - `array()` Use only models defined in the parent class.
 * - `false` Use no models at all, do not merge with parent class either.
 * - `array('Post', 'Comment')` Use only the Post and Comment models. Models
 *   Will also be merged with the parent class.
 *
 * The default value is `true`.
 *
 * @var mixed
 * @link http://book.cakephp.org/2.0/en/controllers.html#components-helpers-and-uses
 */
	public $uses = ['CakeInstaller.InstallerCheck'];

/**
 * Array containing the names of components this controller uses. Component names
 * should not contain the "Component" portion of the class name.
 *
 * Example: `public $components = array('Session', 'RequestHandler', 'Acl');`
 *
 * @var array
 * @link http://book.cakephp.org/2.0/en/controllers/components.html
 */
	public $components = [
		'CakeInstaller.Installer'
	];

/**
 * An array containing the names of helpers this controller uses. The array elements should
 * not contain the "Helper" part of the class name.
 *
 * Example: `public $helpers = array('Html', 'Js', 'Time', 'Ajax');`
 *
 * @var mixed
 * @link http://book.cakephp.org/2.0/en/controllers.html#components-helpers-and-uses
 */
	public $helpers = [
		'CakeInstaller.CheckResult'
	];

/**
 * Called before the controller action. You can use this method to configure and customize components
 * or perform logic that needs to happen before each controller action.
 *
 * Actions:
 *  - Redirect to homepage if application is installed;
 *
 * @return void
 * @link http://book.cakephp.org/2.0/en/controllers.html#request-life-cycle-callbacks
 */
	public function beforeFilter() {
		if ($this->action === 'index') {
			$isAppInstalled = $this->Installer->isAppInstalled();
			if ($isAppInstalled) {
				$this->redirect('/');
			}
		}
		$this->Auth->allow('index');

		parent::beforeFilter();
	}

/**
 * Action `index`. Used to view state of installation for application.
 *
 * @return void
 */
	public function index() {
		$isAppInstalled = $this->Installer->isAppInstalled();
		$isAppReadyInstall = $this->InstallerCheck->isAppReadyToInstall();
		$phpVesion = $this->InstallerCheck->checkPhpVersion();
		$phpModules = $this->InstallerCheck->checkPhpExtensions();
		$filesWritable = $this->InstallerCheck->checkFilesWritable();
		$connectDB = $this->InstallerCheck->checkConnectDb();

		$this->set(compact(
			'isAppInstalled',
			'isAppReadyInstall',
			'phpVesion',
			'phpModules',
			'filesWritable',
			'connectDB'
		));
	}
}
