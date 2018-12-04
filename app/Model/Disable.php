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
 * List of behaviors to load when the model object is initialized.
 *
 * @var array
 * @link https://book.cakephp.org/2.0/en/models/behaviors.html#using-behaviors
 */
	public $actsAs = [
		'ParseData',
	];

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
 * @param array &$errorMessages Array of error messages.
 * @return bool|array Return False on failure. If successful,
 *  return True or an array of the list of disabled hosts.
 */
	protected function _disableUnusedHosts(array &$errorMessages) {
		$modelLdapComputer = ClassRegistry::init('LdapComputer');
		$listComputers = $modelLdapComputer->getListComputers();
		if (empty($listComputers)) {
			$errorMessages[__('Errors')][] = __('The list of computers for processing is empty');
			return false;
		}

		$modelHost = ClassRegistry::init('Host');
		$listHosts = $modelHost->getListHosts();
		if (empty($listHosts)) {
			$errorMessages[__('Errors')][] = __('The list of hosts for processing is empty');
			return false;
		}

		$dataToDisable = [];
		foreach ($listHosts as $hostId => $hostName) {
			if (!preg_grep('/' . $hostName . '/i', $listComputers)) {
				$dataToDisable['id'][] = $hostId;
				$dataToDisable['name'][] = $hostName;
			}
		}
		if (empty($dataToDisable)) {
			return true;
		}

		$numbRecords = count($dataToDisable['id']);
		$result = [
			'Host' => [
				__('Number of hosts') => $numbRecords,
				__('List') => $dataToDisable['name']
			]
		];
		$conditions = [$modelHost->alias . '.id' => $dataToDisable['id']];
		if ($modelHost->changeStateAll(false, $conditions)) {
			$errorMessages[__('Information')][] = __(
				'Disabled %d %s.',
				$numbRecords,
				__dxn('plural', 'Disable type', 'host', 'hosts', $numbRecords)
			);
		} else {
			$errorMessages[__('Errors')][__('Error on disabling hosts')] = $modelHost->getMessageCheckDisable();
			return false;
		}

		return $result;
	}

/**
 * Disable unused profiles.
 *
 * @param array &$errorMessages Array of error messages.
 * @return bool|array Return False on failure. If successful,
 *  return True or an array of the list of disabled profiles.
 */
	protected function _disableUnusedProfiles(array &$errorMessages) {
		$modelProfile = ClassRegistry::init('Profile');
		$listProfiles = $modelProfile->getListProfilesToDisable();
		if (empty($listProfiles)) {
			return true;
		}

		$dataToDisable['id'] = Hash::extract($listProfiles, '{n}.Profile.id');
		$dataToDisable['name'] = Hash::extract($listProfiles, '{n}.Profile.id_text');

		$numbRecords = count($dataToDisable['id']);
		$result = [
			'Profile' => [
				__('Number of profiles') => $numbRecords,
				__('List') => $dataToDisable['name']
			]
		];
		$conditions = [$modelProfile->alias . '.id' => $dataToDisable['id']];
		if ($modelProfile->changeStateAll(false, $conditions)) {
			$errorMessages[__('Information')][] = __(
				'Disabled %d %s.',
				$numbRecords,
				__dxn('plural', 'Disable type', 'profile', 'profiles', $numbRecords)
			);
		} else {
			$errorMessages[__('Errors')][__('Error on disabling profiles')] = $modelProfile->getMessageCheckDisable();
			return false;
		}

		return $result;
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
		$errorMessages = [];
		$result = true;
		set_time_limit(DISABLE_UNUSED_TIME_LIMIT);

		$this->_modelExtendQueuedTask->updateProgress($idTask, 0);
		$dataToMail = [];
		$listDisableTypes = [
			'Hosts',
			'Profiles',
		];
		foreach ($listDisableTypes as $disableType) {
			$methodName = '_disableUnused' . $disableType;
			$resultDisable = $this->$methodName($errorMessages);
			$this->_modelExtendQueuedTask->updateTaskProgress($idTask, $step, $maxStep);
			if (is_array($resultDisable)) {
				$dataToMail += $resultDisable;
			} elseif (!$resultDisable) {
				$result = false;
			}
		}

		if (empty($dataToMail)) {
			return $result;
		}

		if (!$this->_sendEmail($errorMessages, $dataToMail)) {
			$result = false;
		}

		$step = $maxStep - 1;
		$this->_modelExtendQueuedTask->updateTaskProgress($idTask, $step, $maxStep);
		if (!empty($idTask) && !empty($errorMessages)) {
			$errorMessagesText = $this->renderErrorMessages($errorMessages);
			$this->_modelExtendQueuedTask->updateTaskErrorMessage($idTask, $errorMessagesText, true);
		}

		return $result;
	}

/**
 * Sending E-mail report.
 *
 * @param array &$errorMessages Array of error messages.
 * @param array $data Data of report.
 * @return bool Success.
 */
	protected function _sendEmail(array &$errorMessages, $data = []) {
		if (empty($data)) {
			return true;
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
				$errorMessages[__('Errors')][__('Error on sending e-mail')][] = __('Error on putting sending e-mail for "%s" in queue...', $to);
				$result = false;
			}
		}

		return $result;
	}
}
