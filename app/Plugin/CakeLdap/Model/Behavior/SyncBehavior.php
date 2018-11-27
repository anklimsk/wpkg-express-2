<?php
/**
 * This file is the behavior file of the plugin. Is used for processing
 *  synchronization informations data.
 *
 * CakeLdap: Authentication of users by member group of Active Directory.
 * @copyright Copyright 2017-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.Model.Behavior
 */

App::uses('ModelBehavior', 'Model');
App::uses('Hash', 'Utility');

/**
 * The behavior is used for processing synchronization informations data.
 *
 * @package plugin.Model.Behavior
 */
class SyncBehavior extends ModelBehavior {

/**
 * Creates cache as associative array using `$key` as the path to build its keys, and optionally
 * `$value` as path to get the values.
 *
 * @param Model $model Model using this behavior.
 * @param array $data Array from where to extract keys and values
 * @param string $key Key name string.
 * @param string $value Value name.
 * @return array Combined array
 */
	public function createCache(Model $model, $data = null, $key = null, $value = null) {
		$result = [];
		if (empty($data) || !is_array($data) || empty($key)) {
			return $result;
		}

		$pathKey = '{n}.' . $model->alias . '.' . $key;
		$pathValue = '{n}';
		if (!empty($value)) {
			$pathValue .= '.' . $model->alias . '.' . $value;
		}
		$result = Hash::combine($data, $pathKey, $pathValue);
		ksort($result);

		return $result;
	}

/**
 * Return message of result for synchronization process.
 *
 * @param Model $model Model using this behavior.
 * @param array $info Array data for creation message informat:
 *  - key `data`, value - data for processing;
 *  - key `deep`, value - if True, use amount of second level items in data;
 *   otherwise, use amount of first level;
 *  - key `label`, value - label of result string.
 * @param string $type Type of synchronization.
 * @return string Return message of result
 */
	public function getResultMessage(Model $model, $info = null, $type = null) {
		if (empty($info) || !is_array($info)) {
			return false;
		}

		$type = (string)$type;
		if (!empty($type)) {
			$type = ' ' . $type;
		}

		$result = '';
		$messages = [];
		foreach ($info as $infoItem) {
			if (!isset($infoItem['data']) || empty($infoItem['data']) ||
				!is_array($infoItem['data'])) {
				continue;
			}
			if (isset($infoItem['deep']) && $infoItem['deep']) {
				$numRecords = count($infoItem['data'], COUNT_RECURSIVE) - count($infoItem['data'], COUNT_NORMAL);
			} else {
				$numRecords = count($infoItem['data']);
			}

			if ($numRecords == 0) {
				continue;
			}

			$numRecordsText = __dn('cake_ldap', 'record', 'records', $numRecords);
			$label = '';
			if (isset($infoItem['label'])) {
				$label = $infoItem['label'];
			}
			if (!empty($label)) {
				$label .= ': ';
			}
			$messages[] = ' * ' . $label . $numRecords . ' ' . $numRecordsText;
		}
		if (!empty($messages)) {
			$result = __d('cake_ldap', 'Result of synchronization') . $type . "\n" . implode("\n", $messages);
		}

		return $result;
	}
}
