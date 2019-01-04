<?php
/**
 * This file is the view file of the application. Used to render
 *  WPI configuration file.
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

if (!isset($jsDataArray)) {
	$jsDataArray = [];
}

if (empty($jsDataArray)) {
	echo '// ' . $this->ViewExtension->showEmpty($jsDataArray);
	return;
}
?>
// WPI Config 8.7.0
//
// User defined options
//


// Configurations tab
CheckOnLoad='default';
Configurations=[<?php echo $this->WpiJs->toList($jsDataArray['Configurations']); ?>];
ShowMultiDefault=true;
// ---
SortOrder=[<?php echo $this->WpiJs->toList($jsDataArray['SortOrder']); ?>];
// ---
ConfigSortBy=2;
ConfigSortAscDes='asc';

//---------------------------------------------------------------------------------------------
// Your programs here...
//---------------------------------------------------------------------------------------------
pn=1;
<?php
foreach ($jsDataArray['Programs'] as $program):
	if (!$program['enabled']) {
		echo "/*\n";
	}
?>
prog[pn]=[<?php echo $this->WpiJs->toString($program['prog']); ?>];
uid[pn]=[<?php echo $this->WpiJs->toString($program['uid']); ?>];
ordr[pn]=[<?php echo $this->WpiJs->toInt($program['ordr']); ?>];
dflt[pn]=[<?php echo $this->WpiJs->toBool($program['dflt']); ?>];
forc[pn]=[<?php echo $this->WpiJs->toBool($program['forc']); ?>];
cat[pn]=[<?php echo $this->WpiJs->toString($program['cat']); ?>];
pfro[pn]=['no'];
cmds[pn]=[<?php echo $this->WpiJs->toString($program['cmds']); ?>];
cond[pn]=[''];
gcond[pn]=[<?php echo $this->WpiJs->toString('checkWPKGpkg("' . $program['uid'] . '")'); ?>];
deps[pn]=[''];
desc[pn]=[<?php echo $this->WpiJs->toString($program['desc']); ?>];
pn++;
<?php
	if (!$program['enabled']) {
		echo "*/\n";
	}
	echo "\n";
endforeach;
?>
//---------------------------------------------------------------------------------------------
// End of program definitions ...
//---------------------------------------------------------------------------------------------
