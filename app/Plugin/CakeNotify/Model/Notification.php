<?php
/**
 * This file is the model file of the plugin.
 * Create, retrieve and clear expired notification.
 * Methods to manage notifications
 *
 * CakeNotify: Sending email from CakePHP using task queues
 * @copyright Copyright 2017-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.Model
 */

App::uses('CakeNotifyAppModel', 'CakeNotify.Model');

/**
 * Notification for CakeNotify.
 *
 * @package plugin.Model
 */
class Notification extends CakeNotifyAppModel {

/**
 * List of validation rules. It must be an array with the field name as key and using
 * as value one of the following possibilities
 *
 * @var array
 * @link http://book.cakephp.org/2.0/en/models/model-attributes.html#validate
 * @link http://book.cakephp.org/2.0/en/models/data-validation.html
 */
	public $validate = [
		'id' => [
			'naturalNumber' => [
				'rule' => ['naturalNumber'],
				'message' => 'Incorrect primary key',
				'allowEmpty' => false,
				'required' => true,
				'last' => true,
				'on' => 'update'
			],
		],
		'user_id' => [
			'naturalNumber' => [
				'rule' => ['naturalNumber'],
				'message' => 'Incorrect user ID',
				'allowEmpty' => true,
				'required' => true,
				'last' => true,
			],
		],
		'user_role' => [
			'naturalNumber' => [
				'rule' => ['naturalNumber'],
				'message' => 'Incorrect user role',
				'allowEmpty' => true,
				'required' => true,
				'last' => true,
			],
		],
		'title' => [
			'notBlank' => [
				'rule' => ['notBlank'],
				'message' => 'Incorrect title of notification',
				'allowEmpty' => false,
				'required' => true,
				'last' => true
			],
		],
		'body' => [
			'notBlank' => [
				'rule' => ['notBlank'],
				'message' => 'Incorrect body of notification',
				'allowEmpty' => false,
				'required' => true,
				'last' => true
			],
		],
		'expires' => [
			'notBlank' => [
				'rule' => ['datetime', 'ymd'],
				'message' => 'Incorrect expiration date or time of notification',
				'allowEmpty' => false,
				'required' => true,
				'last' => true
			],
		],
	];

/**
 * Called after each find operation. Can be used to modify any results returned by find().
 * Return value should be the (modified) results.
 *
 * Actions:
 *  - Unserialize data of notification.
 *
 * @param mixed $results The results of the find operation
 * @param bool $primary Whether this model is being queried directly (vs. being queried as an association)
 * @return mixed Result of the find operation
 * @link http://book.cakephp.org/2.0/en/models/callback-methods.html#afterfind
 */
	public function afterFind($results, $primary = false) {
		if (empty($results)) {
			return $results;
		}

		foreach ($results as &$result) {
			if (!isset($result[$this->alias]['data']) || empty($result[$this->alias]['data'])) {
				continue;
			}

			$result[$this->alias]['data'] = unserialize($result[$this->alias]['data']);
		}

		return $results;
	}

/**
 * Called before each save operation, after validation. Return a non-true result
 * to halt the save.
 *
 * Actions:
 *  - Replace empty value of fields to Null;
 *  - Serialize data of notification.
 *
 * @param array $options Options passed from Model::save().
 * @return bool True if the operation should continue, false if it should abort
 * @link http://book.cakephp.org/2.0/en/models/callback-methods.html#beforesave
 * @see Model::save()
 */
	public function beforeSave($options = []) {
		$fields = $this->_getListFieldsNullVal();
		foreach ($fields as $field) {
			if (isset($this->data[$this->alias][$field]) && empty($this->data[$this->alias][$field])) {
				$this->data[$this->alias][$field] = null;
			}
		}

		if (isset($this->data[$this->alias]['data']) && !empty($this->data[$this->alias]['data'])) {
			$this->data[$this->alias]['data'] = serialize($this->data[$this->alias]['data']);
		}

		return true;
	}

/**
 * Return list of fields with allowed null value
 *
 * @return array Return list of fields.
 */
	protected function _getListFieldsNullVal() {
		$result = [];
		$schema = $this->schema();
		$data = array_filter(
			$schema,
			function ($v, $k) {

				return isset($v['null']) && $v['null'] === true;
			},
			ARRAY_FILTER_USE_BOTH
		);
		if (!empty($data)) {
			$result = array_keys($data);
		}

		return $result;
	}

/**
 * Create notification
 *
 * @param string|null $tag The ID of the notification.
 *  Is used to replace the messages with the same tag.
 * @param string $title The title of the notification.
 * @param string $body The body string of the notification.
 * @param array $extendInfo Extended info for notification.
 *  List of key:
 *   - `data` - data associated with the notification;
 *   - `user_role` - bit mask of user roles to notify users
 *  with a certain role;
 *   - `user_id` - the ID of user to personal notify;
 *   - `expires` - date and time of expire notification;
 * @return bool Success.
 */
	public function createNotification($tag = null, $title = null, $body = null, $extendInfo = null) {
		if (empty($title) || empty($body)) {
			return false;
		}

		$expiresDefault = date('Y-m-d H:i:s', strtotime(sprintf('+%d hour', CAKE_NOTIFY_EXPIRES_HOUR)));
		$extendInfoDefault = [
			'data' => [],
			'user_role' => null,
			'user_id' => null,
			'expires' => $expiresDefault
		];
		if (empty($extendInfo) || !is_array($extendInfo)) {
			$extendInfo = [];
		}

		$extendInfo += $extendInfoDefault;
		if (!is_array($extendInfo['data'])) {
			$extendInfo['data'] = [];
		}

		$notification = [];
		$notification += compact('tag', 'title', 'body');
		$notification += $extendInfo;
		$this->create();
		$savedData = $this->save([$this->alias => $notification]);
		if (!$savedData) {
			return false;
		}

		$conditionFields = ['tag', 'user_id', 'user_role'];
		$conditionsDelete = array_intersect_key($savedData[$this->alias], array_flip($conditionFields));
		if (empty($conditionsDelete)) {
			return true;
		}

		$conditionsDelete['id <>'] = $savedData[$this->alias]['id'];

		return $this->deleteAll($conditionsDelete);
	}

/**
 * Return array of notifications
 *
 * @param int $id The ID of last processed notify.
 * @param int $userId The ID of user to personal notify.
 * @param int $userRole Bit mask of user roles to
 *  notify users with a certain role.
 * @return bool Success.
 */
	public function getNotifications($id = null, $userId = null, $userRole = null) {
		$fields = [
			$this->alias . '.id',
			$this->alias . '.user_id',
			$this->alias . '.user_role',
			$this->alias . '.tag',
			$this->alias . '.title',
			$this->alias . '.body',
			$this->alias . '.data',
		];
		$conditions = [
			'AND' => [
				$this->alias . '.expires >=' => date('Y-m-d H:i:s'),
				'OR' => [
					[
						'AND' => [
							$this->alias . '.user_id' => '',
							$this->alias . '.user_role' => '',
						]
					]
				]
			]
		];
		if (!empty($id)) {
			$conditions['AND'][][$this->alias . '.id >'] = (int)$id;
		}
		if (!empty($userId)) {
			$conditions['AND']['OR'][][$this->alias . '.user_id'] = (int)$userId;
		}
		if (!empty($userRole)) {
			$conditions['AND']['OR'][]['AND'] = [
				$this->alias . '.user_role & ' . (int)$userRole . ' > 0',
				$this->alias . '.user_id' => '',
			];
		}

		$order = [$this->alias . '.id' => 'asc'];

		return $this->find('all', compact('fields', 'conditions', 'order'));
	}

/**
 * Clear expired notifications
 *
 * @return bool Success.
 */
	public function clearNotifications() {
		$conditions = [
			$this->alias . '.expires <' => date('Y-m-d H:i:s')
		];

		return $this->deleteAll($conditions);
	}
}
