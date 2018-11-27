<?php
/**
 * This file is the shell helper file of the plugin.
 * Waiting message Shell Helper.
 * Methods to show and hide message 'Please wait...' with animate
 *  for slow tasks
 *
 * CakeInstaller: Installer of CakePHP web application.
 * @copyright Copyright 2016-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.Console.Helper
 */

App::uses('BaseShellHelper', 'Console/Helper');

/**
 * Show and hide message 'Please wait...' with animate.
 *
 * @package plugin.Console.Helper
 */
class WaitingShellHelper extends BaseShellHelper {

/**
 * Array of chars for animate.
 *
 * @var array
 */
	protected $_animateChars = [
		'|',
		'/',
		'-',
		'\\',
		'|',
		'/',
		'-',
		'\\',
	];

/**
 * Number of current animate char.
 *
 * @var int
 */
	protected $_animateCharNum = 0;

/**
 * Flag of state showing message
 *
 * @var bool
 */
	protected $_showMessage = false;

/**
 * Get char for animate
 *
 * @return string
 */
	protected function _getAnimateChar() {
		if ($this->_animateCharNum >= count($this->_animateChars)) {
			$this->_animateCharNum = 0;
		}

		return $this->_animateChars[$this->_animateCharNum++];
	}

/**
 * Get text of message waiting
 *
 * @return string
 */
	protected function _getWaitMsg() {
		$msg = __d('cake_installer', 'Please wait...');

		return $msg;
	}

/**
 * Print message 'Please wait...' and animate char on console
 *
 * @return void
 */
	public function animateMessage() {
		if (!$this->_showMessage) {
			$this->_showMessage = true;
			$this->_animateCharNum = 0;
			$msg = $this->_getWaitMsg();
			$msg .= ' ' . $this->_getAnimateChar();
			$this->_consoleOutput->write($msg, 0);
		} else {
			$msg = $this->_getAnimateChar();
			$this->_consoleOutput->overwrite($msg, 0, mb_strlen($msg));
		}
	}

/**
 * Hide message 'Please wait...' on console
 *
 * @return void
 */
	public function hideMessage() {
		if (!$this->_showMessage) {
			return;
		}

		$msg = $this->_getWaitMsg();
		$msg .= ' x';
		$this->_consoleOutput->write(str_repeat("\x08", mb_strlen($msg)), 0);
		$this->_showMessage = false;
	}

/**
 * Print message 'Please wait...' on console
 *
 * @param array $args The arguments.
 * @return void
 */
	public function output($args) {
		$this->_consoleOutput->write($this->_getWaitMsg(), 0);
	}
}
