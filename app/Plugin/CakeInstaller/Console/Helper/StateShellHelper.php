<?php
/**
 * This file is the shell helper file of the plugin.
 * State message Shell Helper.
 * Concatination strings message with state, and align state
 *  by right side.
 *
 * CakeInstaller: Installer of CakePHP web application.
 * @copyright Copyright 2016-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.Console.Helper
 */

App::uses('BaseShellHelper', 'Console/Helper');

/**
 * Concatination strings message with state.
 *
 * @package plugin.Console.Helper
 */
class StateShellHelper extends BaseShellHelper {

/**
 * Formatting messages add spaces to align the state on the right side
 *
 * @param string $message Message for formatting.
 * @param string $state State for concatenation with message.
 * @param int $maxWidth The maximum length of a formatted message.
 *
 * @return string Formatted string
 */
	protected function _getState($message = null, $state = null, $maxWidth = 63) {
		$maxWidth = (int)$maxWidth;
		if (empty($message) || empty($state) || ($maxWidth <= 0)) {
			return $message;
		}

		$lengthMessage = mb_strlen(strip_tags($message));
		$lengthState = mb_strlen(strip_tags($state));
		$nRepeat = $maxWidth - $lengthMessage - $lengthState;
		if ($nRepeat <= 0) {
			$nRepeat = 1;
		}

		$space = str_repeat(' ', $nRepeat);
		$result = $message . $space . $state;

		return $result;
	}

/**
 * Formatting messages add spaces to align the state on the right side
 *
 * @param string $message Message for formatting.
 * @param string $state State for concatenation with message.
 * @param int $maxWidth The maximum length of a formatted message.
 * @return string Formatted string
 */
	public function getState($message = null, $state = null, $maxWidth = 63) {
		return $this->_getState($message, $state, $maxWidth);
	}

/**
 * This method should output formatted message.
 *
 * @param array $args The arguments for the `StateShellHelper::_getState()`.
 * @return void
 */
	public function output($args) {
		$args += ['', '', 63];
		$this->_consoleOutput->write($this->_getState($args[0], $args[1], $args[2]));
	}
}
