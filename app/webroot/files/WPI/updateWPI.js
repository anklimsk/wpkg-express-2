// MsgBox Constants
var vbOKOnly = 0, vbCritical = 16, vbExclamation = 48, vbInformation = 64;

// System constants
var nTemporaryFolder = 2;
var nForReading = 1;
var nTristateTrue = -1;
var nAdTypeBinary = 1;
var nHTTPStatusCodeOk = 200;

// Internal constants
var sTargetConfigPath = '.\\UserFiles';
var sTargetConfigFile = 'config.js';

// Config constants
var sDownloadURL = 'http://fabricam.com/wpi/config.js';
var sDownloadUser = '';
var sDownloadPassword = '';

main();

/**
 * Main function. Performs most of the work on updating the configuration file WPI.
 */
function main() {
	var exitCode = update(false);
	
	WScript.Quit(exitCode);
}

/**
 * Update the WPI configuration file from WPKG Express.
 * 
 * @param bSilent 
*			Flag of silent mode.
 * @return 
 *			Exit code.
*/
function update(bSilent) {
	var sSaveName = '';
	var sSaveTo = '';
	var sTargetPath = '';
	var sTargetFile = '';	
	var vDownloadResult = null;
	var sMsg = '';
	var bNeedUpdate = false;	

	var objShell = WScript.CreateObject('WScript.Shell');
	var objFSO = new ActiveXObject('Scripting.FileSystemObject');

	// Prepare paths
	sSaveName = sTargetConfigFile;
	sSaveTo = objFSO.GetSpecialFolder(nTemporaryFolder) + '\\' + sSaveName;
	sTargetPath = objFSO.GetAbsolutePathName(sTargetConfigPath);
	sTargetFile = objFSO.BuildPath(sTargetPath, sTargetConfigFile);

	// Check target folder
	if (!objFSO.FolderExists(sTargetPath)) {
		if (!bSilent) 
			objShell.Popup('Target folder not found "' + sTargetPath + '"', 10, 'Initializing Error', vbOKOnly + vbCritical);
		return 2;
	}

	// Download config file from WPKG Exporess
	vDownloadResult = downloadFile(sDownloadURL, sDownloadUser, sDownloadPassword, sSaveTo);
	if (vDownloadResult !== true) {
		if (!bSilent) {
			if (vDownloadResult === false)
				sMsg = 'Download "' + sSaveName + '" completed unsuccessfuly.';
			else
				sMsg = 'Request "' + sDownloadURL + '" return status code: ' + vDownloadResult;		
			objShell.Popup(sMsg, 0, 'Request Error', vbOKOnly + vbCritical);
		}
		
		return 1;
	}
	
	// Compare config files
	bNeedUpdate = compareFile(sSaveTo, sTargetFile);
	
	if (bNeedUpdate) {	
		sTargetPath = objFSO.BuildPath(sTargetPath, '\\');
		// Update config file
		try {
			objFSO.CopyFile(sSaveTo, sTargetPath, true);
		} catch (e) {
			if (!bSilent) {
				objShell.Popup("Error copying file from:\n" +	
					'"' + sSaveTo + "\" to:\n\"" + sTargetPath + "\"\n" + 
					e.description, 0, 'Copying file', vbOKOnly + vbExclamation);
			}
			return 1;
		}
				
		sMsg = 'Update config file completed successfuly.';
	} else {
		sMsg = 'Update is not required.';
	}

	if (!bSilent) 		
		objShell.Popup(sMsg, 5, 'Completed',  vbOKOnly + vbInformation);
		
	return 0;
}


/**
 * Downloads the WPI configuration file from WPKG Express.
 * 
 * @param sDownloadURL 
*			URL to download config file.
 * @param sDownloadUser 
 *			Username for base authentication on WPKG Express.
 * @param sDownloadPassword 
 *			Password for base authentication on WPKG Express.
 * @param sSaveTo  
 *			Full path to store the result download.
 * @return 
*			Mixed. True if download complete successfully.
*			False or integer status code if download complete unsuccessfully.
*/
function downloadFile(sDownloadURL, sDownloadUser, sDownloadPassword, sSaveTo) {
	var objFSO = null;
	var objHTTP = null;
	var objStream = null;
	var objShell = WScript.CreateObject('WScript.Shell');
	
	objFSO = new ActiveXObject('Scripting.FileSystemObject');
	// Create an HTTP object
	try {
		objHTTP = new ActiveXObject('MSXML2.ServerXMLHTTP.6.0')
	} catch (e) {
		objShell.Popup("Error creating object \"MSXML2.ServerXMLHTTP.6.0\"\n" +	
			e.description, 0, 'Creating object', vbOKOnly + vbExclamation);
		return false;	
	}
	
	// Delete old download result
	if (objFSO.FileExists(sSaveTo))
		objFSO.DeleteFile(sSaveTo);	

	// Download the specified URL
	objHTTP.open('GET', sDownloadURL, false, sDownloadUser, sDownloadPassword);
	objHTTP.send();	
	
	// Check dowdload result
	if (objHTTP.status == nHTTPStatusCodeOk) {  
		// Save download result
		objStream = new ActiveXObject('ADODB.Stream');
		with (objStream) {
			Type = nAdTypeBinary; 
			Open();
			Write(objHTTP.responseBody);
			SaveToFile(sSaveTo);
			Close();
		}  
	} else {
		return objHTTP.status;
	}

	if (!objFSO.FileExists(sSaveTo))		
		return false;	

	return true;	
}


/**
 * Compare two text files by content.
 * 
 * @param sFile1 
 *			Full path to first text file.
 * @param sFile2
 *			Full path to second text file.
 * @return True if the contents of the files are the same, False otherwise.
 */
function compareFile(sFile1, sFile2) {
	var objShell = WScript.CreateObject('WScript.Shell');
	var cmd = '%COMSPEC% /c fc /b ' + sFile1 + ' ' + sFile2;
	
	return objShell.Run(cmd, 0, true);	
}