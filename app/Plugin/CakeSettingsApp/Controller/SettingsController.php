<?php
/**
 * This file is the controller file of the plugin.
 * Management settings of application.
 *
 * CakeSettingsApp: Manage settings of application.
 * @copyright Copyright 2016-2019, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.Controller
 */

App::uses('CakeSettingsAppAppController', 'CakeSettingsApp.Controller');
App::uses('Hash', 'Utility');

/**
 * The controller is used for management settings of application.
 *
 * @package plugin.Controller
 */
class SettingsController extends CakeSettingsAppAppController {

/**
 * The name of this controller. Controller names are plural, named after the model they manipulate.
 *
 * @var string
 * @link http://book.cakephp.org/2.0/en/controllers.html#controller-attributes
 */
	public $name = 'Settings';

/**
 * An array containing the class names of models this controller uses.
 *
 * @var mixed
 * @link http://book.cakephp.org/2.0/en/controllers.html#components-helpers-and-uses
 */
	public $uses = [
		'CakeSettingsApp.ConfigSettingsApp',
		'CakeSettingsApp.Ldap'
	];

/**
 * Array containing the names of components this controller uses. Component names
 * should not contain the "Component" portion of the class name.
 *
 * @var array
 * @link http://book.cakephp.org/2.0/en/controllers/components.html
 */
	public $components = [
		'Flash',
		'Paginator',
		'Session',
		'CakeTheme.Filter',
		'CakeTheme.ViewExtension',
	];

/**
 * An array containing the names of helpers this controller uses. The array elements should
 * not contain the "Helper" part of the class name.
 *
 * @var mixed
 * @link http://book.cakephp.org/2.0/en/controllers.html#components-helpers-and-uses
 */
	public $helpers = [
		'CakeTheme.Filter',
		'CakeTheme.ViewExtension',
		'AssetCompress.AssetCompress'
	];

/**
 * Constructor.
 *
 * @param CakeRequest $request Request object for this controller. Can be null for testing,
 *  but expect that features that use the request parameters will not work.
 * @param CakeResponse $response Response object for this controller.
 */
	public function __construct($request = null, $response = null) {
		if (ClassRegistry::init('Setting', true) !== false) {
			$this->uses[] = 'Setting';
		} else {
			$this->uses[] = 'CakeSettingsApp.Setting';
		}

		parent::__construct($request, $response);
	}

/**
 * Action `index`. Used to view and change settings of application
 *
 * POST Data:
 *  - `Setting` array data settings of application
 *
 * @return void
 */
	public function index() {
		$this->disableCache();
		$defaultConfig = $this->Setting->getDefaultConfig();
		$currentConfig = (array)$this->Setting->getConfig();
		$configUIlangs = $this->ConfigSettingsApp->getFlagConfigUiLangs();
		$configSMTP = $this->ConfigSettingsApp->getFlagConfigSmtp();
		$configAcLimit = $this->ConfigSettingsApp->getFlagConfigAcLimit();
		$configADsearch = $this->ConfigSettingsApp->getFlagConfigADsearch();
		$configExtAuth = $this->ConfigSettingsApp->getFlagConfigExtAuth();
		$authGroups = $this->ConfigSettingsApp->getAuthGroups();
		$groupList = $this->Ldap->getGroupList();
		$containerList = $this->Ldap->getTopLevelContainerList();
		$currLanguage = Hash::get($currentConfig, 'Setting.Language');
		$languages = $this->Setting->getUiLangsList();
		$schema = $this->Setting->getFullSchema();
		$passFields = [];
		foreach ($schema as $field => $metadata) {
			if (mb_stripos($field, 'password') !== false) {
				$passFields[] = $field;
			}
		}
		if ($this->request->is(['post', 'put'])) {
			foreach ($passFields as $passField) {
				if (mb_stripos($field, '_confirm') !== false) {
					continue;
				}

				$currPass = $this->request->data('Setting.' . $passField);
				$prevPpass = (string)Hash::get($currentConfig, 'Setting.' . $passField);
				if (empty($currPass) && !empty($prevPpass)) {
					$this->request->data('Setting.' . $passField, $prevPpass);
					$this->request->data('Setting.' . $passField . '_confirm', $prevPpass);
				}
			}
			$country = $this->request->data('Setting.Language');
			$language = $this->Setting->getLanguageByCountryCode($country);
			$this->request->data('Setting.Language', $language);
			if ($this->Setting->save($this->request->data)) {
				$this->Flash->success(__d('cake_settings_app', 'Application settings has been saved.'));
				if ($currLanguage !== $language) {
					$this->Flash->information(__d('cake_settings_app', 'Reload the page to apply the settings.'));
				}
				if (!$this->Session->read('Settings.FirstLogon')) {
					$this->ViewExtension->setProgressSseTask('ClearCache');
				}

				return $this->redirect(['action' => 'index']);
			} else {
				$this->Flash->error(__d('cake_settings_app', 'Unable to save application settings.'));
			}
		} else {
			$appSetting = Hash::merge(
				$defaultConfig,
				$currentConfig
			);
			$this->request->data('Setting', $appSetting['Setting']);
			$this->Setting->createValidationRules();
		}
		foreach ($passFields as $passField) {
			$this->request->data('Setting.' . $passField, '');
			$this->request->data('Setting.' . $passField . '_confirm', '');
		}

		$country = $this->Setting->getCountryCodeByLanguage($currLanguage);
		$this->request->data('Setting.Language', $country);
		$varsExt = $this->Setting->getVars();

		$pageHeader = __d('cake_settings_app', 'Application settings');
		$breadCrumbs = $this->Setting->getBreadcrumbInfo();
		$breadCrumbs[] = __d('cake_settings_app', 'Settings');

		$this->set(compact(
			'groupList',
			'containerList',
			'configUIlangs',
			'configSMTP',
			'configAcLimit',
			'configADsearch',
			'configExtAuth',
			'authGroups',
			'languages',
			'varsExt',
			'pageHeader',
			'breadCrumbs'
		));
	}
}
