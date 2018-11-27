<?php
/**
 * Plugin level View Helper
 *
 * CakeTheme: Set theme for application.
 * @copyright Copyright 2016-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.View.Helper
 */

App::uses('AppHelper', 'View/Helper');
App::uses('Hash', 'Utility');

/**
 * Plugin helper
 *
 * @package plugin.View.Helper
 */
class CakeThemeAppHelper extends AppHelper {

/**
 * Stores default options for helper methods.
 *
 * @var array
 */
	protected $_optionsForElem = [];

/**
 * Current language of UI.
 *
 * @var string
 */
	protected $_currUIlang;

/**
 * Constructor
 *
 * @param View $View The View this helper is being attached to.
 * @param array $settings Configuration settings for the helper.
 */
	public function __construct(View $View, $settings = []) {
		parent::__construct($View, $settings);

		$this->_currUIlang = (string)Configure::read('Config.language');
		if (empty($this->_currUIlang)) {
			$this->_currUIlang = 'eng';
		}
		$this->_addSpecificDetector();
	}

/**
 * Return default all options.
 *
 * @return mixed Return all default options.
 */
	protected function _getListOptionsForElem() {
		$result = [];

		return $result;
	}

/**
 * Return default options by path.
 *
 * @param string $path Path to retrieve options
 * @return mixed Return default options by path.
 */
	protected function _getOptionsForElem($path = null) {
		if (empty($path)) {
			return null;
		}
		$result = Hash::get($this->_optionsForElem, $path);

		return $result;
	}

/**
 * Adding request detector for MSIE browser, PJAX, SSE,
 *  print preview, modal and popup.
 *
 * @return void
 */
	protected function _addSpecificDetector() {
		$this->request->addDetector(
			'msie',
			['env' => 'HTTP_USER_AGENT', 'pattern' => '/imsie|trident/i']
		);
		$this->request->addDetector(
			'pjax',
			['env' => 'HTTP_X_PJAX', 'value' => true]
		);
		$this->request->addDetector(
			'sse',
			['param' => 'ext', 'value' => 'sse']
		);
		$this->request->addDetector(
			'modal',
			['param' => 'ext', 'value' => 'mod']
		);
		$this->request->addDetector(
			'popup',
			['param' => 'ext', 'value' => 'pop']
		);
		$this->request->addDetector(
			'print',
			['param' => 'ext', 'value' => 'prt']
		);
	}
}
