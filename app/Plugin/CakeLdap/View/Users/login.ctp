<?php
/**
 * This file is the view file of the plugin. Used for login users.
 *
 * CakeLdap: Authentication of users by member group of Active Directory.
 * @copyright Copyright 2016-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.View.Elements
 */

	$this->assign('title', $pageTitle);
?>
	<div class="container container-table"> 
		<div class="row vertical-center-row">
			<div class="col-xs-12 col-sm-6 col-sm-offset-3 col-md-4 col-md-offset-4 col-lg-4 col-lg-offset-4">
				<div class="panel panel-default">
					<div class="panel-heading">
<?php
	echo __d('cake_ldap', 'Authentication') .
		$this->Html->tag('span', $this->ViewExtension->iconTag('fas fa-lock'), ['class' => 'pull-right']);
?>
					</div>
					<div class="panel-body">
<?php
	echo $this->element('CakeLdap.formUserLogin');
?>
					</div>
				</div>
			</div>
		</div>
	</div>
