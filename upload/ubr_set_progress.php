<?php
//******************************************************************************************************
//	Name: ubr_set_progress.php
//	Revision: 3.0
//	Date: 9:17 PM April 24, 2009
//	Link: http://uber-uploader.sourceforge.net
//	Developer: Peter Schmandra
//	Description: Initialize the progress bar
//
//	BEGIN LICENSE BLOCK
//	The contents of this file are subject to the Mozilla Public License
//	Version 1.1 (the "License"); you may not use this file except in
//	compliance with the License. You may obtain a copy of the License
//	at http://www.mozilla.org/MPL/
//
//	Software distributed under the License is distributed on an "AS IS"
//	basis, WITHOUT WARRANTY OF ANY KIND, either express or implied. See
//	the License for the specific language governing rights and
//	limitations under the License.
//
//	Alternatively, the contents of this file may be used under the
//	terms of either the GNU General Public License Version 2 or later
//	(the "GPL"), or the GNU Lesser General Public License Version 2.1
//	or later (the "LGPL"), in which case the provisions of the GPL or
//	the LGPL are applicable instead of those above. If you wish to
//	allow use of your version of this file only under the terms of
//	either the GPL or the LGPL, and not to allow others to use your
//	version of this file under the terms of the MPL, indicate your
//	decision by deleting the provisions above and replace them with the
//	notice and other provisions required by the GPL or the LGPL. If you
//	do not delete the provisions above, a recipient may use your
//	version of this file under the terms of any one of the MPL, the GPL
//	or the LGPL.
//	END LICENSE BLOCK
//***************************************************************************************************************

//***************************************************************************************************************
//	ATTENTION
//
//	If you need to debug this file, set the $_INI['debug_ajax'] = 1 in ubr_ini.php and use the showDebugMessage function.
//	eg. showDebugMessage("Hi There");
//***************************************************************************************************************

//***************************************************************************************************************
//	The following possible query string formats are assumed
//
//	1. ?upload_id=32_character_alpha_numeric_string
//	2. ?about
//***************************************************************************************************************

$THIS_VERSION    = '3.0';        // Version of this file
$UPLOAD_ID = '';                 // Initialize upload id

require_once 'ubr_ini.php';
require_once 'ubr_lib.php';

if($_INI['php_error_reporting']){ error_reporting(E_ALL); }

if(isset($_GET['upload_id']) && preg_match("/^[a-zA-Z0-9]{32}$/", $_GET['upload_id'])){ $UPLOAD_ID = $_GET['upload_id']; }
elseif(isset($_GET['about'])){ kak("<u><b>UBER UPLOADER SET PROGRESS</b></u><br>UBER UPLOADER VERSION =  <b>" . $_INI['uber_version'] . "</b><br>UBR_SET_PROGRESS = <b>" . $THIS_VERSION . "<b><br>\n", 1, __LINE__, $_INI['path_to_css_file']); }
else{ kak("<span class='ubrError'>ERROR</span>: Invalid parameters passed<br>", 1, __LINE__, $_INI['path_to_css_file']); }

$flength_file = $TEMP_DIR . $UPLOAD_ID . '.dir/' . $UPLOAD_ID . '.flength';
$hook_file = $TEMP_DIR . $UPLOAD_ID . '.dir/' . $UPLOAD_ID . '.hook';
$found_flength_file = false;
$found_hook_file = false;

// Keep trying to read the flength file until timeout
for($i = 0; $i < $_INI['flength_timeout_limit']; $i++){
	if($total_upload_size = readUbrFile($flength_file, $_INI['debug_ajax'])){
		$found_flength_file = true;
		$start_time = time();
		break;
	}

	clearstatcache();
	sleep(1);
}

// Failed to find the flength file in the alloted time
if(!$found_flength_file){
	if($_INI['debug_ajax']){ showDebugMessage("Failed to find flength file $flength_file"); }
	showAlertMessage("<span class='ubrError'>ERROR</span>: Failed to find <a href='http://uber-uploader.sourceforge.net/?section=flength' target='_new'>flength file</a>", 1);
}
elseif(strstr($total_upload_size, "ERROR")){
	// Found the flength file but it contains an error
	list($error, $error_num, $error_msg) = explode($DATA_DELIMITER, $total_upload_size);

	if($_INI['debug_ajax']){ showDebugMessage($error_msg); }

	if(!deleteDir($TEMP_DIR . $UPLOAD_ID . '.dir')){
		if($_INI['debug_ajax']){ showDebugMessage("Failed to delete " . $TEMP_DIR . $UPLOAD_ID . ".dir"); }
	}

	stopUpload();

	if($error_num == 1){ $formatted_error_msg = "<span class='ubrError'>ERROR</span>: Failed to open link file " . $UPLOAD_ID . ".link"; }
	elseif($error_num == 2 || $error_num == 3){ $formatted_error_msg = "<span class='ubrError'>ERROR</span>: " . $error_msg; }

	showAlertMessage($formatted_error_msg, 1);
}
else{
	// Keep trying to read the hook file until timeout
	if($_INI['cgi_upload_hook']){
		for($i = 0; $i < $_INI['hook_timeout_limit']; $i++){
			if($hook_data = readUbrFile($hook_file, $_INI['debug_ajax'])){
				$found_hook_file = true;
				break;
			}

			clearstatcache();
			sleep(1);
		}
	}

	// Failed to find the hook file in the alloted time
	if($_INI['cgi_upload_hook'] && !$found_hook_file){
		if($_INI['debug_ajax']){ showDebugMessage("Failed to find hook file $hook_file"); }
		showAlertMessage("<span class='ubrError'>ERROR</span>: Failed to find hook file", 1);
	}

	if($_INI['debug_ajax']){
		showDebugMessage("Found flength file $flength_file");
		if($_INI['cgi_upload_hook']){ showDebugMessage("Found hook file $hook_file"); }
	}

	startProgressBar($UPLOAD_ID, $total_upload_size, $start_time);
}

?>