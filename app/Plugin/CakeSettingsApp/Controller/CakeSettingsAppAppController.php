<?php
/**
 * Plugin level Controller
 *
 * CakeSettingsApp: Manage settings of application.
 * @copyright Copyright 2016-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.Controller
 */

App::uses('AppController', 'Controller');

/**
 * Plugin level Controller
 *
 * @package plugin.Controller
 */
class CakeSettingsAppAppController extends AppController {

/**
 * An array containing the class names of models this controller uses.
 *
 * @var mixed
 * @link http://book.cakephp.org/2.0/en/controllers.html#components-helpers-and-uses
 */
	public $uses = ['CakeSettingsApp.Setting'];

/**
 * Array containing the names of components this controller uses. Component names
 * should not contain the "Component" portion of the class name.
 *
 * @var array
 * @link http://book.cakephp.org/2.0/en/controllers/components.html
 */
	public $components = [
		'Auth',
		'Security',
		'Flash',
		'RequestHandler'
	];

/**
 * An array containing the names of helpers this controller uses. The array elements should
 * not contain the "Helper" part of the class name.
 *
 * @var mixed
 * @link http://book.cakephp.org/2.0/en/controllers.html#components-helpers-and-uses
 */
	public $helpers = [
		'Html',
		'Form' => [
			'className' => 'CakeTheme.ExtBs3Form'
		],
	];
}
