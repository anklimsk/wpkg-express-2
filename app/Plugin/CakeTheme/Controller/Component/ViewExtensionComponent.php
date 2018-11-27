<?php
/**
 * This file is the componet file of the plugin.
 * Use for:
 *  - Adding PJAX request detector;
 *  - Adding popup, modal and print preview request detector;
 *  - Adding response for SSE, modal and popup request;
 *  - Set global variable;
 *  - Set locale;
 *  - Set layout for SSE, modal and popup response;
 *  - Compress output of HTML request;
 *  - Get and set redirect for request.
 *
 * CakeTheme: Set theme for application.
 * @copyright Copyright 2016-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.Controller.Component
 */

App::uses('Component', 'Controller');
App::uses('Hash', 'Utility');
App::uses('ClassRegistry', 'Utility');
App::uses('CakeSession', 'Model/Datasource');
App::uses('Language', 'CakeBasicFunctions.Utility');

/**
 * ViewExtension Component.
 *
 * Use for:
 *  - Adding PJAX request detector;
 *  - Adding popup, modal and print preview request detector;
 *  - Adding response for SSE, modal and popup request;
 *  - Set global variable;
 *  - Set locale;
 *  - Set layout for SSE, modal and popup response;
 *  - Compress output of HTML request;
 *  - Get and set redirect for request.
 * @package plugin.Controller.Component
 */
class ViewExtensionComponent extends Component {

/**
 * Controller for the request.
 *
 * @var Controller
 */
	protected $_controller = null;

/**
 * Object of library `Language`
 *
 * @var object
 */
	protected $_language = null;

/**
 * Constructor
 *
 * @param ComponentCollection $collection A ComponentCollection for this component
 * @param array $settings Array of settings.
 */
	public function __construct(ComponentCollection $collection, $settings = []) {
		$this->_language = new Language();

		parent::__construct($collection, $settings);
	}

/**
 * Initialize component
 *
 * Actions:
 *  - Initialize $_controller;
 *  - Adding PJAX request detector;
 *  - Adding popup, modal and print preview request detector;
 *  - Adding response for SSE, modal and popup request;
 *  - Set global variable;
 *  - Set locale.
 *
 * @param Controller $controller Instantiating controller
 * @return void
 */
	public function initialize(Controller $controller) {
		$this->_controller = $controller;
		if (!$this->_controller->Components->loaded('RequestHandler')) {
			$this->_controller->RequestHandler = $this->_controller->Components->load('RequestHandler');
			$this->_controller->RequestHandler->initialize($this->_controller);
		}
		if (!$this->_controller->Components->loaded('Flash')) {
			$this->_controller->Flash = $this->_controller->Components->load('Flash');
			$this->_controller->Flash->initialize($this->_controller);
		}
		$this->_addSpecificDetector($controller);
		$this->_addSpecificResponse($controller);
		$this->_setLocale();
	}

/**
 * Called after the Controller::beforeFilter() and before the controller action
 *
 * Actions:
 *  - Set global variables for View;
 *
 * @param Controller $controller Controller instance.
 * @return void
 */
	public function startup(Controller $controller) {
		if (!$this->isHtml()) {
			return;
		}

		$this->_setUiLangVar($controller);
	}

/**
 * Called before the Controller::beforeRender(), and before
 * the view class is loaded, and before Controller::render()
 *
 * Actions:
 *  - Set layout for SSE, modal and popup response;
 *  - Compress output of HTML request.
 *
 * @param Controller $controller Controller with components to beforeRender
 * @return void
 * @link http://book.cakephp.org/2.0/en/controllers/components.html#Component::beforeRender
 */
	public function beforeRender(Controller $controller) {
		$this->_setLayout($controller);
		$this->_compressOutput($controller);
	}

/**
 * Checking the response is HTML.
 *
 * @return bool True if response is HTML. False otherwise.
 */
	public function isHtml() {
		$ext = $this->_controller->request->param('ext');
		if (empty($ext)) {
			return true;
		}

		$prefers = $this->_controller->RequestHandler->prefers();
		if (empty($prefers)) {
			$prefers = 'html';
		}

		$responseMimeType = $this->_controller->response->getMimeType($prefers);
		if (!$responseMimeType) {
			return false;
		}

		return in_array('text/html', (array)$responseMimeType);
	}

/**
 * Adding request detector for MSIE browser, PJAX, SSE,
 *  print preview, modal and popup.
 *
 * @param Controller &$controller Instantiating controller
 * @return void
 */
	protected function _addSpecificDetector(Controller &$controller) {
		$controller->request->addDetector(
			'msie',
			['env' => 'HTTP_USER_AGENT', 'pattern' => '/imsie|trident/i']
		);
		$controller->request->addDetector(
			'pjax',
			['env' => 'HTTP_X_PJAX', 'value' => true]
		);
		$controller->request->addDetector(
			'sse',
			['param' => 'ext', 'value' => 'sse']
		);
		$controller->request->addDetector(
			'modal',
			['param' => 'ext', 'value' => 'mod']
		);
		$controller->request->addDetector(
			'popup',
			['param' => 'ext', 'value' => 'pop']
		);
		$controller->request->addDetector(
			'print',
			['param' => 'ext', 'value' => 'prt']
		);
	}

/**
 * Adding response for SSE, print preview, modal and popup request
 *
 * @param Controller &$controller Instantiating controller
 * @return void
 */
	protected function _addSpecificResponse(Controller &$controller) {
		$controller->response->type(['sse' => 'text/event-stream']);
		$controller->response->type(['mod' => 'text/html']);
		$controller->response->type(['pop' => 'text/html']);
		$controller->response->type(['prt' => 'text/html']);
	}

/**
 * Compress output of HTML request
 *
 * @param Controller &$controller Instantiating controller
 * @return void
 */
	protected function _compressOutput(Controller &$controller) {
		if ($this->isHtml() && (ob_get_level() === 0)) {
			$controller->response->compress();
		}
	}

/**
 * Set global variable of current UI language
 *
 * Set global variable:
 *  - `uiLcid2`: language code in format ISO 639-1 (two-letter codes);
 *  - `uiLcid3` - :language code in format ISO 639-2 (three-letter codes).
 *
 * @param Controller &$controller Instantiating controller
 * @return void
 */
	protected function _setUiLangVar(Controller &$controller) {
		$uiLcid2 = $this->_language->getCurrentUiLang(true);
		$uiLcid3 = $this->_language->getCurrentUiLang(false);
		$controller->set(compact('uiLcid2', 'uiLcid3'));
	}

/**
 * Set layout for SSE, modal and popup response
 *
 * @param Controller &$controller Instantiating controller
 * @return void
 */
	protected function _setLayout(Controller &$controller) {
		$isModal = $controller->request->is('modal');
		$isPopup = $controller->request->is('popup');
		$isSSE = $controller->request->is('sse');
		$isPrint = $controller->request->is('print');
		if ($isModal || $isPopup || $isSSE) {
			$controller->layout = 'CakeTheme.default';
		} elseif ($isPrint) {
			$controller->viewPath = mb_ereg_replace(DS . 'prt', '', $controller->viewPath);
			$controller->response->type('html');
		}
	}

/**
 * Return MD5 cache key from key string
 *
 * @param string $key Key for cache
 * @return string MD5 cache key
 */
	protected function _getSessionKeyRedirect($key = null) {
		if (empty($key)) {
			$key = $this->_controller->request->here();
		}
		$cacheKey = md5((string)$key);

		return $cacheKey;
	}

/**
 * Set redirect URL to cache
 *
 * @param string|array|bool $redirect Redirect URL. If empty,
 *  use redirect URL. If True, use current URL.
 * @param string $key Key for cache
 * @return bool Success
 */
	public function setRedirectUrl($redirect = null, $key = null) {
		if (empty($redirect)) {
			$redirect = $this->_controller->request->referer(true);
		} elseif ($redirect === true) {
			$redirect = $this->_controller->request->here(true);
		}
		if (empty($redirect) || $this->_controller->request->is('popup') ||
			$this->_controller->request->is('print')) {
			return false;
		}
		$cacheKey = $this->_getSessionKeyRedirect($key);
		$data = CakeSession::read($cacheKey);
		if (!empty($data) && (md5((string)$data) === md5((string)$redirect))) {
			return false;
		}

		return CakeSession::write($cacheKey, $redirect);
	}

/**
 * Get redirect URL from cache
 *
 * @param string $key Key for cache
 * @return mixed Return redirect URL, or Null on failure
 */
	protected function _getRedirectCache($key = null) {
		$cacheKey = $this->_getSessionKeyRedirect($key);
		$redirect = CakeSession::consume($cacheKey);

		return $redirect;
	}

/**
 * Get redirect URL
 *
 * @param string|array|bool $defaultRedirect Default redirect URL.
 *  If True, use current URL. Use if redirect URL is not found in cache.
 * @param string $key Key for cache
 * @return mixed Return redirect URL
 */
	public function getRedirectUrl($defaultRedirect = null, $key = null) {
		$redirect = $this->_getRedirectCache($key);
		if (empty($redirect)) {
			if (!empty($defaultRedirect)) {
				if ($defaultRedirect === true) {
					$redirect = $this->_controller->request->here();
				} else {
					$redirect = $defaultRedirect;
				}
			} else {
				$redirect = ['action' => 'index'];
			}
		}

		return $redirect;
	}

/**
 * Redirect by URL
 *
 * @param string|array $defaultRedirect Default redirect URL.
 *  Use if redirect URL is not found in cache.
 * @param string $key Key for cache
 * @return CakeResponse|null
 * @see ViewExtensionComponent::getRedirectUrl()
 */
	public function redirectByUrl($defaultRedirect = null, $key = null) {
		$redirectUrl = $this->getRedirectUrl($defaultRedirect, $key);

		return $this->_controller->redirect($redirectUrl);
	}

/**
 * Set locale for current UI language.
 *
 * @return bool Success
 */
	protected function _setLocale() {
		$language = $this->_language->getCurrentUiLang(false);

		return (bool)setlocale(LC_ALL, $language);
	}

/**
 * Set task to display the progress of execution from task queue
 *
 * @param string $taskName Name of task
 * @return bool Success
 */
	public function setProgressSseTask($taskName = null) {
		if (empty($taskName)) {
			return false;
		}

		$tasks = (array)CakeSession::read('SSE.progress');
		if (in_array($taskName, $tasks)) {
			return true;
		}

		$tasks[] = $taskName;

		return CakeSession::write('SSE.progress', $tasks);
	}

/**
 * Set Flash message of exception
 *
 * @param string $message Message to be flashed. If an instance
 *  of Exception the exception message will be used and code will be set
 *  in params.
 * @param string|array $defaultRedirect Default redirect URL.
 *  Use if redirect URL is not found in cache.
 * @param string $key Key for cache
 * @return CakeResponse|null
 * @see FlashComponent::set()
 * @see ViewExtensionComponent::getRedirectUrl()
 */
	public function setExceptionMessage($message = '', $defaultRedirect = null, $key = null) {
		$statusCode = null;
		$redirectUrl = null;
		if ($message instanceof Exception) {
			if ($this->_controller->request->is('ajax')) {
				$statusCode = $message->getCode();
			}
		}
		if (empty($statusCode)) {
			$this->_controller->Flash->error($message);
			$redirectUrl = $this->getRedirectUrl($defaultRedirect, $key);
		}

		return $this->_controller->redirect($redirectUrl, $statusCode);
	}
}
