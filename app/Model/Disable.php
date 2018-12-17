<?php
/**
 * This file is the model file of the application. Used to
 *  disable unused hosts and profiles.
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
 * @copyright Copyright 2018, Andrey Klimov.
 * @package app.Model
 */

App::uses('AppModel', 'Model');
App::uses('ClassRegistry', 'Utility');
App::uses('Hash', 'Utility');

/**
 * The model is used to disable unused hosts and profiles.
 *
 * @package app.Model
 */
class Disable extends AppModel {

/**
 * Custom database table name, or null/false if no table association is desired.
 *
 * @var string
 * @link https://book.cakephp.org/2.0/en/models/model-attributes.html#usetable
 */
	public $useTable = false;

/**
 * Object of model `ExtendQueuedTask`
 *
 * @var object
 */
	protected $_modelExtendQueuedTask = null;

/**
 * Constructor. Binds the model's database table to the object.
 *
 * @param bool|int|string|array $id Set this ID for this model on startup,
 * can also be an array of options, see above.
 * @param string $table Name of database table to use.
 * @param string $ds DataSource connection name.
 */
	public function __construct($id = false, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);

		$this->_modelExtendQueuedTask = ClassRegistry::init('CakeTheme.ExtendQueuedTask');
	}

/**
 * Disable unused hosts.
 *
 * @param int $idTask The ID of the QueuedTask
 * @return array|bool Return array list of processed data, or False on failure.
 */
	protected function _disableUnusedHosts($idTask = null) {
		$modelLdapComputer = ClassRegistry::init('LdapComputer');
		$listComputers = $modelLdapComputer->getListComputers();
		if (empty($listComputers)) {
			$this->_modelExtendQueuedTask->updateTaskErrorMessage($idTask, __('The list of computers for processing is empty'));
			return false;
		}

		$modelHost = ClassRegistry::init('Host');
		$listHosts = $modelHost->getListHosts();
		if (empty($listHosts)) {
			$this->_modelExtendQueuedTask->updateTaskErrorMessage($idTask, __('The list of hosts for processing is empty'));
			return false;
		}

		$dataToDisable = [];
		foreach ($listHosts as $hostId => $hostName) {
			if (!preg_grep('/' . $hostName . '/i', $listComputers)) {
				$dataToDisable[] = $hostId;
			}
		}
		if (empty($dataToDisable)) {
			return true;
		}

		$conditions = [$modelHost->alias . '.id' => $dataToDisable];
		return $modelHost->changeStateGroupRecords(false, $conditions, $idTask);
	}

/**
 * Disable unused profiles.
 *
 * @param int $idTask The ID of the QueuedTask
 * @return array|bool Return array list of processed data, or False on failure.
 */
	protected function _disableUnusedProfiles($idTask = null) {
		$modelProfile = ClassRegistry::init('Profile');
		$listProfiles = $modelProfile->getListProfilesToDisable();
		if (empty($listProfiles)) {
			return true;
		}

		$conditions = [$modelProfile->alias . '.id' => $listProfiles];
		return $modelProfile->changeStateGroupRecords(false, $conditions, $idTask);
	}

/**
 * Disable unused hosts and profiles.
 *
 * @param int $idTask The ID of the QueuedTask
 * @return bool Success.
 */
	public function disableUnusedData($idTask = null) {
		$step = 0;
		$maxStep = 3;
		$messages = [];
		$dataToMail = [];
		$result = true;
		set_time_limit(DISABLE_UNUSED_TIME_LIMIT);

		$this->_modelExtendQueuedTask->updateProgress($idTask, 0);
		$listDisableTypes = [
			'Hosts',
			'Profiles',
		];
		foreach ($listDisableTypes as $disableType) {
			$methodName = '_disableUnused' . $disableType;
			$resultDisable = $this->$methodName();
			$this->_modelExtendQueuedTask->updateTaskProgress($idTask, $step, $maxStep);
			if (is_array($resultDisable)) {
				$dataToMail += $resultDisable;
			} elseif ($resultDisable === false) {
				$result = false;
			}
		}

		if (!empty($dataToMail) && !$this->_sendEmail($dataToMail, $idTask)) {
			$result = false;
		}

		$step = $maxStep - 1;
		$this->_modelExtendQueuedTask->updateTaskProgress($idTask, $step, $maxStep);

		return $result;
	}

/**
 * Sending E-mail report.
 *
 * @param array $data Array of data for email.
 * @param int $idTask The ID of the QueuedTask
 * @return bool Success.
 */
	protected function _sendEmail($data = [], $idTask = null) {
		if (empty($data)) {
			return false;
		}

		$result = true;
		$modelSetting = ClassRegistry::init('Setting');
		$modelLdap = ClassRegistry::init('CakeSettingsApp.Ldap');
		$listEmails = $modelLdap->getListGroupEmail('AdminGroupMember');
		if (empty($listEmails)) {
			$emailContact = $modelSetting->getConfig('EmailContact');
			if (!empty($emailContact)) {
				$listEmails = [$emailContact => __('Administrator of WPKG')];
			}
		}

		$modelSendEmail = ClassRegistry::init('CakeNotify.SendEmail');
		$projectName = __dx('project', 'mail', PROJECT_NAME);
		$config = 'smtp';
		$domain = $modelSendEmail->getDomain();
		$from = ['noreply@' . $domain, __d('project', PROJECT_NAME)];
		$subject = __('Unused hosts and profiles is disabled');
		$template = 'disable_unused_report';
		$helpers = [
			'Time',
		];
		$created = date('Y-m-d H:i:s');
		$vars = compact(
			'data',
			'created',
			'projectName'
		);
		foreach ($listEmails as $email => $name) {
			$to = [$email, $name];
			if (!$modelSendEmail->putQueueEmail(compact('config', 'from', 'to', 'subject', 'template', 'vars', 'helpers'))) {
				$this->_modelExtendQueuedTask->updateTaskErrorMessage($idTask, __('Error on putting sending e-mail for "%s" in queue...', $to));
				$result = false;
			}
		}

		return $result;
	}
}
