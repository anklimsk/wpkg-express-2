<?php
/**
 * This file is the view file of the plugin. Used for show
 *  installation information.
 *
 * @copyright Copyright 2016-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.View.Users
 */
?>
<div class="container">
<?php
	echo $this->Html->div('page-header', $this->Html->tag('h2', __d('cake_installer', 'Checking installation of application')));

	$step = 0;
	$maxStep = 0;
	$progressData = [
		'phpVesion' => [
			'target' => ($phpVesion !== null ? [$phpVesion] : $phpVesion),
			'value' => false,
		],
		'phpModules' => [
			'target' => ($phpModules !== null ? [$phpModules] : $phpModules),
			'value' => 0,
		],
		'filesWritable' => [
			'target' => $filesWritable,
			'value' => false,
		],
		'connectDB' => [
			'target' => $connectDB,
			'value' => false,
		],
	];
	foreach ($progressData as $progressInfo) {
		if ($progressInfo['target'] !== null) {
			$maxStep++;
			if (!in_array($progressInfo['value'], $progressInfo['target'])) {
				$step++;
			}
		}
	}

	$barClass = 'danger';
	$barValue = 0;
	if ($maxStep > 0) {
		if ($step === $maxStep) {
			$barClass = 'success';
		}
		$barValue = round($step / $maxStep * 100);
	}
	$barText = $step . '&nbsp;/&nbsp;' . $maxStep;
?>
	<div class="progress">
		<div class="progress-bar progress-bar-<?php echo $barClass; ?>" role="progressbar" aria-valuenow="<?php echo $barValue; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $barValue; ?>%;">
<?php echo $barText; ?>
		</div>
	</div>
<?php
	$actions = [
		'<code><samp>sudo ./Console/cake CakeInstaller</samp></code> - ' . __d('cake_installer', 'to run the interactive installer'),
		'<code><samp>sudo ./Console/cake CakeInstaller check</samp></code> - ' . __d('cake_installer', 'to check the readiness of the start installation'),
		'<code><samp>sudo ./Console/cake CakeInstaller install</samp></code> - ' . __d('cake_installer', 'to start the installation process'),
		'<code><samp>sudo ./Console/cake CakeInstaller --help</samp></code> - ' . __d('cake_installer', 'to get help'),
	];

	if (!$isAppInstalled) {
		$classAlert = 'alert-danger';
		$msgAlert = __d('cake_installer', 'Application is not installed');
		if ($isAppReadyInstall) {
			$classAlert = 'alert-warning';
			$msgAlert = __d('cake_installer', 'Application is not installed (ready to install)');
		}
		echo $this->Html->div(
			'alert ' . $classAlert,
			$this->Html->tag('strong', $msgAlert) . '<br />' .
				$this->Html->tag('samp', __d('cake_installer', 'To install the application, go to the OS console, navigate to the directory %s application, and run the following commands:', '<em>APP</em>')) .
				$this->Html->nestedList($actions, [], [], 'ul'),
			['role' => 'alert']
		);
	} else {
		echo $this->Html->div(
			'alert alert-success',
			$this->Html->tag('strong', __d('cake_installer', 'Application is installed')),
			['role' => 'alert']
		);
	}

	if ($phpVesion !== null) :
		echo $this->Html->div('page-header', $this->Html->tag('h2', __d('cake_installer', 'Checking PHP version')));
		$phpVesionInfo = [
			[
				'classItem' => $this->CheckResult->getStateItemClass($phpVesion),
				'textItem' => __d('cake_installer', 'Current PHP version: %s', $this->Html->tag('em', PHP_VERSION)) .
					$this->Html->div('pull-right', $this->CheckResult->getStateElement($phpVesion))
			]
		];
?>
	<div class="row">  
		<div class="col-xs-10 col-xs-offset-1 col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3 col-lg-6 col-lg-offset-3">
<?php
		echo $this->CheckResult->getStateList($phpVesionInfo);
?>
		</div>
	</div>
<?php
	endif;
	if ($phpModules !== null) :
		echo $this->Html->div('page-header', $this->Html->tag('h2', __d('cake_installer', 'Checking PHP extensions')));
		$phpModulesInfo = [];
		foreach ($phpModules as $moduleName => $moduleState) {
			$phpModulesInfo[] = [
				'classItem' => $this->CheckResult->getStateItemClass($moduleState),
				'textItem' => __d('cake_installer', 'Checking module is loaded: \'%s\'', $this->Html->tag('em', $moduleName)) .
					$this->Html->div('pull-right', $this->CheckResult->getStateElement($moduleState))
			];
		}
?>
	<div class="row">  
		<div class="col-xs-10 col-xs-offset-1 col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3 col-lg-6 col-lg-offset-3">
<?php
		echo $this->CheckResult->getStateList($phpModulesInfo);
?>
		</div>
	</div>
<?php
	endif;
	echo $this->Html->div('page-header', $this->Html->tag('h2', __d('cake_installer', 'Checking possibility of writing to files')));
	$writableFilesInfo = [];
	foreach ($filesWritable as $filePath => $writableState) {
		$targetType = __d('cake_installer', 'file');
		if (is_dir($filePath)) {
			$targetType = __d('cake_installer', 'directory');
		}
		$fileName = basename($filePath);
		$writableFilesInfo[] = [
			'classItem' => $this->CheckResult->getStateItemClass($writableState),
			'textItem' => __d('cake_installer', 'Checking write to %s: \'%s\'', $targetType, $this->Html->tag('em', $fileName)) .
				$this->Html->div('pull-right', $this->CheckResult->getStateElement($writableState))
		];
	}
?>
	<div class="row">
		<div class="col-xs-10 col-xs-offset-1 col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3 col-lg-6 col-lg-offset-3">
<?php
		echo $this->CheckResult->getStateList($writableFilesInfo);
?>
		</div>
	</div>
<?php
	echo $this->Html->div('page-header', $this->Html->tag('h2', __d('cake_installer', 'Checking database configuration file')));
if ($connectDB !== null) {
	$configDbState = true;
} else {
	$configDbState = false;
}
	$connectDBstate = [
		[
			'classItem' => $this->CheckResult->getStateItemClass($configDbState),
			'textItem' => __d('cake_installer', 'Database configuration file') .
			$this->Html->div('pull-right', $this->CheckResult->getStateElement($configDbState))
		]
	];
?>
	<div class="row">
		<div class="col-xs-10 col-xs-offset-1 col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3 col-lg-6 col-lg-offset-3">
<?php
		echo $this->CheckResult->getStateList($connectDBstate);
?>
		</div>
	</div>
<?php
if ($connectDB !== null) :
	echo $this->Html->div('page-header', $this->Html->tag('h2', __d('cake_installer', 'Checking database connections')));
	$connectDBInfo = [];
	foreach ($connectDB as $connectionName => $connectionState) {
		$connectionDbState = false;
		if ($connectionState === true) {
			$connectionDbState = true;
		}

		$connectDBInfo[] = [
			'classItem' => $this->CheckResult->getStateItemClass($connectionDbState),
			'textItem' => __d('cake_installer', 'Checking database connection: \'%s\'', $this->Html->tag('em', $connectionName)) .
				$this->Html->div('pull-right', $this->CheckResult->getStateElement($connectionDbState))
		];
	}
?>
	<div class="row">
		<div class="col-xs-10 col-xs-offset-1 col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3 col-lg-6 col-lg-offset-3">
<?php
	echo $this->CheckResult->getStateList($connectDBInfo);
?>
		</div>
	</div>
<?php
endif;
?>
</div>
