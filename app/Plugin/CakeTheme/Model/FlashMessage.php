<?php
/**
 * This file is the model file of the plugin.
 * Get information of Flash messages.
 * Methods for retrieve information of Flash messages.
 *
 * CakeTheme: Set theme for application.
 * @copyright Copyright 2016-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.Model
 */

App::uses('CakeThemeAppModel', 'CakeTheme.Model');
App::uses('CakeSession', 'Model/Datasource');

/**
 * Flash for CakeTheme.
 *
 * @package plugin.Model
 */
class FlashMessage extends CakeThemeAppModel {

/**
 * Use table
 *
 * @var mixed False or table name
 */
	public $useTable = false;

/**
 * Session key for store Flash message information
 *
 * @var array
 */
	protected $_sessionKey = 'Message';

/**
 * Get list of Flash messages.
 *
 * @param string $key Key of Flash messages
 *  to obtain list of messages
 * @return array List of Flash messages.
 */
	public function getMessage($key = null) {
		$result = [];
		if (empty($key)) {
			return $result;
		}

		$sessionKey = $this->_sessionKey . '.' . $key;
		if (!CakeSession::check($sessionKey)) {
			return $result;
		}

		$data = CakeSession::read($sessionKey);
		if (empty($data)) {
			return $result;
		}

		$result = (array)$data;
		if (isAssoc($result)) {
			$result = [$result];
		}

		return $result;
	}

/**
 * Delete Flash messages.
 *
 * @param string $key Key of Flash messages
 *  to delete
 * @return bool Success.
 */
	public function deleteMessage($key = null) {
		if (empty($key)) {
			return false;
		}

		$sessionKey = $this->_sessionKey . '.' . $key;
		if (!CakeSession::check($sessionKey)) {
			return false;
		}

		return CakeSession::delete($sessionKey);
	}
}
