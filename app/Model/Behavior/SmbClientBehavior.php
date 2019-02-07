<?php
/**
 * This file is the behavior file of the application. Is used to
 *  manage SMB client library.
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
 * @copyright Copyright 2018-2019, Andrey Klimov.
 * @package app.Model.Behavior
 */

App::uses('ModelBehavior', 'Model');
App::uses('ClassRegistry', 'Utility');
App::uses('Hash', 'Utility');
App::import(
	'Vendor',
	'SMB',
	['file' => 'SMB' . DS . 'vendor' . DS . 'autoload.php']
);

/**
 * The behavior is used to manage SMB client library
 *
 * @package app.Model.Behavior
 */
class SmbClientBehavior extends ModelBehavior {

/**
 * Object of model `Setting`
 *
 * @var object
 */
	protected $_modelSetting = null;

/**
 * Setup this behavior with the specified configuration settings
 *
 * @param Model $model Model using this behavior
 * @param array $config Configuration settings for $model
 * @return void
 */
	public function setup(Model $model, $config = array()) {
		$this->_modelSetting = ClassRegistry::init('Setting');
	}

/**
 * Return object of IShare
 *
 * @param Model $model Model using this behavior
 * @param string $configKeyShare The name of the parameter with share path
 * @return object|bool Return object of IShare, or False on failure.
 */
	public function getShareObj(Model $model, $configKeyShare = null) {
		if (empty($configKeyShare)) {
			return false;
		}

		$user = $this->_modelSetting->getConfig('SmbAuthUser');
		$pswd = $this->_modelSetting->getConfig('SmbAuthPassword');
		$workgroup = $this->_modelSetting->getConfig('SmbWorkgroup');
		$host = $this->_modelSetting->getConfig('SmbServer');
		$shareInfo = $this->getShareInfo($model, $configKeyShare);
		if (!$shareInfo) {
			return false;
		}
		extract($shareInfo);

		if (empty($user) || empty($pswd) ||
			empty($host) || empty($shareName)) {
			return false;
		}

		$auth = new \Icewind\SMB\BasicAuth($user, $workgroup, $pswd);
		$serverFactory = new \Icewind\SMB\ServerFactory();
		try {
			$server = $serverFactory->createServer($host, $auth);
		} catch (Exception $e) {
			return false;
		}

		return $server->getShare($shareName);
	}

/**
 * Return information of share path
 *
 * @param Model $model Model using this behavior
 * @param string $configKeyShare The name of the parameter with share path
 * @return array|bool Return array information of share path, or False on failure.
 */
	public function getShareInfo(Model $model, $configKeyShare = null) {
		if (empty($configKeyShare)) {
			return false;
		}

		$share = $this->_modelSetting->getConfig($configKeyShare);
		if (empty($share)) {
			return false;
		}

		$shareInfo = explode('/', $share, 2);
		if (empty($shareInfo)) {
			return false;
		}

		$shareName = (string)Hash::get($shareInfo, 0);
		$sharePath = (string)Hash::get($shareInfo, 1);
		$result = compact('shareName', 'sharePath');

		return $result;
	}
}
