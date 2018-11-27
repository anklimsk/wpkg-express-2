<?php
/**
 * This file is the view file of the application. Used to render
 *  statistics information.
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
 * @package app.View.Elements
 */

if (!isset($statistics)) {
	$statistics = [];
}
?>
	<dl class="dl-horizontal dl-popup-modal">
		<dt><?php echo __('Statistics') . ':'; ?></dt>
		<dd>
<?php
	$listStatistic = [];
	foreach ($statistics as $statisticItem) {
		$url = $this->ViewExtension->addUserPrefixUrl(['controller' => $statisticItem['controller'], 'action' => 'index']);
		$info = $this->Html->link(h($statisticItem['label']), $url, ['target' => '_blank']);
		$info .= ': ' . $this->Number->format(
			$statisticItem['numberAll'],
			['thousands' => ' ', 'before' => '', 'places' => 0]
		);
		$info .= ' / ' . $this->Number->format(
			$statisticItem['numberDisabled'],
			['thousands' => ' ', 'before' => '', 'places' => 0]
		);
		$listStatistic[] = $info;
	}
	echo $this->ViewExtension->showEmpty($listStatistic, $this->Html->nestedList($listStatistic, [], [], 'ol'));
?>
		</dd>
	</dl>
