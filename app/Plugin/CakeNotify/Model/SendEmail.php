<?php
/**
 * This file is the model file of the plugin.
 * Sending Email now or put it in queue.
 * Methods to sending email
 *
 * CakeNotify: Sending email from CakePHP using task queues
 * @copyright Copyright 2016-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.Model
 */

App::uses('CakeNotifyAppModel', 'CakeNotify.Model');
App::uses('QueuedTask', 'Queue.Model');
App::uses('ClassRegistry', 'Utility');
App::uses('EmailLib', 'Tools.Lib');
App::uses('Router', 'Routing');

/**
 * SendEmail Model
 *
 * @package plugin.Model
 */
class SendEmail extends CakeNotifyAppModel {

/**
 * Custom database table name, or null/false if no table association is desired.
 *
 * @var string
 * @link http://book.cakephp.org/2.0/en/models/model-attributes.html#usetable
 */
	public $useTable = false;

/**
 * Creates a configuration for sending email
 *
 * @param array $data Data for creating a configuration.
 *  List of key for data array:
 *   - `config`;
 *   - `from`;
 *   - `to`;
 *   - `subject`;
 *   - `template`;
 *   - `vars`;
 *   - `helpers`;
 *   - `emailFormat`.
 * @return bool|array Return array of configuration, or False on failure.
 */
	protected function _configSender($data = []) {
		$defaultData = [
			'config' => null,
			'from' => null,
			'to' => null,
			'subject' => null,
			'template' => null,
			'vars' => null,
			'helpers' => null,
			'emailFormat' => null];
		$data = (array)$data + $defaultData;
		extract($data, EXTR_OVERWRITE);

		if (empty($to) || empty($template)) {
			return false;
		}

		if (empty($config)) {
			$config = 'default';
		}
		if (empty($from)) {
			$domain = parse_url(Configure::read('App.fullBaseUrl'), PHP_URL_HOST);
			if (empty($domain)) {
				$domain = 'localhost';
			}
			$from = 'report@' . $domain;
		}
		if (empty($subject)) {
			$subject = $from;
		}
		if (empty($helpers)) {
			$helpers = [];
		}

		if (!is_array($template)) {
			$template = [$template];
		}
		if (!is_array($helpers)) {
			$helpers = [$helpers];
		}

		$default = [
			'template' => [
				false,
				'CakeNotify.default'
			],
			'helpers' => [
				'Html',
				'Text',
				'CakeNotify.Style'
			],
		];

		$template = $template + $default['template'];
		$helpers = array_values(array_unique(array_merge($default['helpers'], $helpers)));
		if (!empty($helpers)) {
			$helpers = [$helpers];
		}

		if (empty($emailFormat)) {
			$emailFormat = 'both';
		}
		$mailData = [
			'settings' => compact('config', 'from', 'to', 'subject', 'template', 'helpers', 'emailFormat'),
			'vars' => $vars
		];

		return $mailData;
	}

/**
 * Retrun current domain name
 *
 * @return string Return current domain name.
 */
	public function getDomain() {
		$fullBaseUrl = Router::fullBaseUrl();
		if (!empty($fullBaseUrl)) {
			$domain = parse_url($fullBaseUrl, PHP_URL_HOST);
		} else {
			$domain = 'localhost';
		}

		return $domain;
	}

/**
 * Put sending email in queue
 *
 * @param array $data Data for sending email.
 *  List of key for data array:
 *   - `config` - name of email configuration. Default - `smtp`;
 *   - `from` - email from;
 *   - `to` - email to;
 *   - `subject` - subject of email;
 *   - `template` - template of email. e.g. 'template' of array('template', 'layout');
 *   - `vars` - variables of View. Used in template;
 *   - `helpers` - list of View helpers. Used in template.
 * @return bool Success
 */
	public function putQueueEmail($data = []) {
		$mailData = $this->_configSender($data);
		if ($mailData === false) {
			return false;
		}

		$modelQueue = ClassRegistry::init('Queue.QueuedTask');
		$result = (bool)$modelQueue->createJob('Email', $mailData, null, 'Email');

		return $result;
	}

/**
 * Put sending email in queue
 *
 * @param array $data Data for sending email.
 *  List of key for data array:
 *   - `config` - name of email configuration. Default - `smtp`;
 *   - `from` - email from;
 *   - `to` - email to;
 *   - `subject` - subject of email;
 *   - `template` - template of email. e.g. 'template' of array('template', 'layout');
 *   - `vars` - variables of View. Used in template;
 *   - `helpers` - list of View helpers. Used in template.
 *   - `resultType` - type of result.
 *
 *  List of values for key `resultType`:
 *   - `headers`: return headers of email;
 *   - `message`: return body of email.
 * @return bool|string If key `resultType` is not set return bool.
 *  If key `resultType` equlas `headers` or `message` return string.
 *  Return Flase on failure.
 */
	public function sendEmailNow($data = []) {
		$mailData = $this->_configSender($data);
		if ($mailData === false) {
			return false;
		}

		$resultType = (string)Hash::get((array)$data, 'resultType');
		$defaults = [
			'to' => null,
			'from' => null,
		];
		$Email = new EmailLib();
		$settings = array_merge($defaults, $mailData['settings']);
		foreach ($settings as $method => $setting) {
			call_user_func_array([$Email, $method], (array)$setting);
		}
		$message = null;
		if (!empty($mailData['vars'])) {
			if (isset($mailData['vars']['content'])) {
				$message = $mailData['vars']['content'];
			}
			$Email->viewVars($mailData['vars']);
		}
		$result = $Email->send($message);
		if (!$result || empty($resultType)) {
			return $result;
		}

		$debugInfo = $Email->getDebug();
		if (isset($debugInfo[$resultType])) {
			$result = $debugInfo[$resultType];
		}

		return $result;
	}
}
