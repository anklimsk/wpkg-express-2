<?php
/**
 * AppShell file
 *
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         CakePHP(tm) v 2.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

App::uses('Shell', 'Console');

/**
 * Application Shell
 *
 * Add your application-wide methods in the class below, your shells
 * will inherit them.
 *
 * @package       app.Console.Command
 */
class AppShell extends Shell {

/**
 * Initializes the Shell
 * acts as constructor for subclasses
 * allows configuration of tasks prior to shell execution
 *
 * @return void
 * @link https://book.cakephp.org/2.0/en/console-and-shells.html#Shell::initialize
 */
	public function initialize() {
		if (($this->name === 'Queue') && ($this->plugin === 'Queue')) {
			$this->uses = array_map(
				function ($v) {
					return $v === 'Queue.QueuedTask' ? 'QueuedTask' : $v;
				},
				$this->uses
			);
		}

		parent::initialize();
	}
}
