<?php
/**
 * Plugin level Controller
 *
 * CakeLdap: Authentication of users by member group of Active Directory.
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
class CakeLdapAppController extends AppController {

/**
 * Array containing the names of components this controller uses. Component names
 * should not contain the "Component" portion of the class name.
 *
 * @var array
 * @link http://book.cakephp.org/2.0/en/controllers/components.html
 */
	public $components = [
		'Auth',
		'Session',
		'Security',
		'Flash',
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
		'CakeTheme.ViewExtension',
		'AssetCompress.AssetCompress',
	];
}
