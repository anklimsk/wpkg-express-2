/**
 * File for checking packages status from WPKG for WPI
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
 * @copyright Copyright 2018-2019, Andrey Klimov.
 * @file File for checking packages status from WPKG for WPI
 * @version 0.1
 */

// Config constants
var sPathWpkg = "%WPIPATH%\\Tools\\wpkg\\wpkg.js";
var sSettingsDbPath = "%TEMP%\\wpkg-wpi.xml";

// List of package statuses
var oPkgsInfo = null;

/**
 * Return console output from WPKG.
 * 
 * @return Return string with console output from WPKG.
 */
function _getWpkgInfo() {
	var sWpkgInfo = "";
	var oShell = new ActiveXObject("WScript.Shell");
	var sPathWpkgExp = oShell.ExpandEnvironmentStrings(sPathWpkg);
	var sSettingsDbPathExp = oShell.ExpandEnvironmentStrings(sSettingsDbPath);
	var sSettingsDbPathDryRun = sSettingsDbPathExp + ".dryrun";
	var oFS = new ActiveXObject("Scripting.FileSystemObject");
	if (!oFS.FileExists(sPathWpkgExp)) {
		return sWpkgInfo;
	}

	if (oFS.FileExists(sSettingsDbPathDryRun)) {
		oFS.DeleteFile(sSettingsDbPathDryRun, true);
	}

	var nExitCode = oShell.Run('cscript.exe //NoLogo "' + sPathWpkgExp + '" /synchronize /dryrun:true /noremove:true /norunningstate:true /quiet:false /logLevel:0 /settings:' + sSettingsDbPathExp, 0, true);
	if (nExitCode != 0) {
		return sWpkgInfo;
	}

	var objExecQuery = oShell.Exec('cscript.exe //NoLogo "' + sPathWpkgExp + '" /query:i /queryMode:local /quiet:false /logLevel:0 /settings:' + sSettingsDbPathDryRun);
	if (objExecQuery.status == 1) {
		return sWpkgInfo;
	}

	sWpkgInfo = objExecQuery.StdOut.ReadAll();
	if (oFS.FileExists(sSettingsDbPathDryRun)) {
		oFS.DeleteFile(sSettingsDbPathDryRun, true);
	}

	return sWpkgInfo;
}

/**
 * Return value from array lines of console output from WPKG.
 * 
 * @param data Array lines of console output from WPKG.
 * @param key Key for retrieving value.
 * @return Return string value from console output from WPKG by key.
 */
function _getItemValue(data, key) {
	var sValue = "";
	if ((data == null) || (key == null) ||
		(data.length == 0) || (key == "")) {
		return sValue;
	}

	key += ":";
	var nPosKey = data.indexOf(key, 4);
	if (nPosKey == -1) {
		return sValue;
	}

	var nPosSpace = nPosKey + key.length + 1;
	while (nPosSpace < data.length) {
		if (data.charAt(nPosSpace) != " ") {
			break;
		}
		nPosSpace++;
	}

	if (nPosSpace < data.length) {
		sValue = data.slice(nPosSpace);
	}

	return sValue;
}

/**
 * Create a package status cache
 * 
 * @returns {Boolean} Success
 */
function _createCachePkgState() {
	if (oPkgsInfo != null) {
		return true;
	}

	var sWpkgInfo = _getWpkgInfo();
	if (sWpkgInfo == "") {
		return false;
	}

	oPkgsInfo = new ActiveXObject("Scripting.Dictionary");
	var aWpkgInfo = sWpkgInfo.split("\r\n");
	var sDataLine = "";
	var sPkgId = "";
	var sPkgStatus = "";
	var bPkgStatus = false;
	for (var nLine = 0; nLine < aWpkgInfo.length; nLine++ ) {
		sPkgId = _getItemValue(aWpkgInfo[nLine], "ID");
		if (sPkgId == "") {
			continue;
		}

		nLine += 5;
		if (nLine >= aWpkgInfo.length) {
			continue;
		}

		sPkgStatus = _getItemValue(aWpkgInfo[nLine], "Status");
		if (sPkgStatus == "") {
			continue;
		}

		bPkgStatus = false;
		if (sPkgStatus == "Installed") {
			bPkgStatus = true;
		}

		oPkgsInfo.Add(sPkgId, bPkgStatus);
	}

	return true;
}

/**
 * Checking package status by ID.
 * 
 * @param pkgId Package ID to check.
 * @returns {Boolean} true if the package is installed,
 *                    otherwise returns false.
 */
function checkWPKGpkg(pkgId) {
	if ((pkgId == null) || (pkgId == "")) {
		return false;
	}

	if (!_createCachePkgState()) {
		return false;
	}

	if ((oPkgsInfo == null) || (oPkgsInfo.Count == 0) ||
		!oPkgsInfo.Exists(pkgId)) {
		return false;
	}

	return oPkgsInfo.Item(pkgId);
}
