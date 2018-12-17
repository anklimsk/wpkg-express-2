<?php
/**
 * This file is the Exception Renderer file of the plugin.
 * Set layout layout to `CakeTheme.error`.
 *
 * CakeTheme: Set theme for application.
 * @copyright Copyright 2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.Lib.Error
 */

App::uses('ExceptionRenderer', 'Error');

/**
 * Exception Renderer.
 *
 * Set layout layout to `CakeTheme.error`.
 * @package plugin.Lib.Error
 */
class ExceptionRendererCakeTheme extends ExceptionRenderer {

/**
 * Generate the response using the controller object.
 *
 * @param string $template The template to render.
 * @return void
 */
	protected function _outputMessage($template) {
		if (!empty($template)) {
			$template = 'CakeTheme.' . $template;
		}
		$this->controller->layout = 'CakeTheme.error';
		try {
			$this->controller->render($template);
			$this->_shutdown();
			$this->controller->response->send();
		} catch (MissingViewException $e) {
			$attributes = $e->getAttributes();
			if (isset($attributes['file']) && strpos($attributes['file'], 'error500') !== false) {
				$this->_outputMessageSafe('error500');
			} else {
				$this->_outputMessage('error500');
			}
		} catch (MissingPluginException $e) {
			$attributes = $e->getAttributes();
			if (isset($attributes['plugin']) && $attributes['plugin'] === $this->controller->plugin) {
				$this->controller->plugin = null;
			}
			$this->_outputMessageSafe('error500');
		} catch (Exception $e) {
			$this->_outputMessageSafe('error500');
		}
	}

/**
 * A safer way to render error messages, replaces all helpers, with basics
 * and doesn't call component methods. Set layout to `CakeTheme.error`.
 *
 * @param string $template The template to render
 * @return void
 */
	protected function _outputMessageSafe($template) {
		$this->controller->layoutPath = null;
		$this->controller->subDir = null;
		$this->controller->viewPath = 'Errors';
		$this->controller->layout = 'CakeTheme.error';
		$this->controller->helpers = array('Form', 'Html', 'Session');

		if (!empty($template)) {
			$template = 'CakeTheme.' . $template;
		}
		$view = new View($this->controller);
		$this->controller->response->body($view->render($template, 'CakeTheme.error'));
		$this->controller->response->type('html');
		$this->controller->response->send();
	}

}
