<?php
/**
 * This file is the view file of the application. Used to viewing
 *  statistical information.
 *
 * This file is part of wpkgExpress II.
 *
 * wpkgExpress II is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * wpkgExpress II is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with wpkgExpress II. If not, see <https://www.gnu.org/licenses/>.
 *
 * wpkgExpress II: A web-based frontend to WPKG.
 *  Based on wpkgExpress by Brian White.
 * @copyright Copyright 2009, Brian White.
 * @copyright Copyright 2018, Andrey Klimov.
 * @package app.View.Home
 */

	$this->assign('title', $pageHeader);
	$linkOptions = [
		'data-modal-size' => 'lg',
		'data-popover-size' => 'lg'
	];
?>
<div class="container" data-toggle="repeat" data-repeat-time="300">
<?php
	echo $this->ViewExtension->headerPage($pageHeader);
	if (!empty($stateReportData)):
?>
		<div class="row">
			<div class="col-12">
<?php
		echo $this->Html->tag('h3', __('Installation state of packages'), ['class' => 'text-center']);
		echo $this->ViewExtension->barState($stateReportData);
?>
			</div>
		</div>
<?php
	endif;
	if (!empty($stateLogData)):
?>
		<div class="row">
			<div class="col-12">
<?php
		echo $this->Html->tag('h3', __('State of logs'), ['class' => 'text-center']);
		echo $this->ViewExtension->barState($stateLogData);
?>
			</div>
		</div>
<?php 
	endif;
	if (!empty($lastPackages) || !empty($lastProfiles) || !empty($lastHosts) || !empty($statistics)):
?>
		<div class="row">
			<div class="col-12">
<?php
	echo $this->Html->tag('h3', __('Last modified informations'), ['class' =>'text-center']);
?>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-8 col-xs-offset-2 col-sm-8 col-sm-offset-2 col-md-5 col-md-offset-1 col-lg-5 col-lg-offset-1">
<?php
	echo $this->ViewExtension->listLastInfo($lastPackages, __('Packages'), 'packages', null, $linkOptions);
?>
			</div>
			<div class="col-xs-8 col-xs-offset-2 col-sm-8 col-sm-offset-2 col-md-5 col-md-offset-0 col-lg-5 col-lg-offset-0">
<?php
	echo $this->ViewExtension->listLastInfo($lastProfiles, __('Profiles'), 'profiles', null, $linkOptions);
?>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-8 col-xs-offset-2 col-sm-8 col-sm-offset-2 col-md-5 col-md-offset-1 col-lg-5 col-lg-offset-1">
<?php
	echo $this->ViewExtension->listLastInfo($lastHosts, __('Hosts'), 'hosts', null, $linkOptions);
?>
			</div>
			<div class="col-xs-8 col-xs-offset-2 col-sm-8 col-sm-offset-2 col-md-5 col-md-offset-0 col-lg-5 col-lg-offset-0">
<?php
	echo $this->element('infoStatistics', compact('statistics'));
?>
			</div>
		</div>
<?php 
	endif;
	if (!empty($listExport)):
?>
		<div class="row">
			<div class="col-12">
<?php
	echo $this->Html->tag('h3', __('Export XML files'), ['class' => 'text-center']);
?>
			</div>
		</div>
		<div class="row">
			<div class="col-12 text-center">
<?php
	echo $this->element('listExports', compact('listExport'));
?>
			</div>
		</div>
<?php endif; ?>
</div>
