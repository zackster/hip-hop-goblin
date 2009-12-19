<?php
//******************************************************************************************************
//	Name: ubr_get_progress.php
//	Revision: 3.1
//	Date: 11:20 PM July 7, 2009
//	Link: http://uber-uploader.sourceforge.net
//	Developer: Peter Schmandra
//	Description: Gather stats on an existing upload
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
//******************************************************************************************************************************

//******************************************************************************************************************************
//	ATTENTION
//
//	If you need to debug this file, set the $_INI['debug_ajax'] = 1 in ubr_ini.php and use the showDebugMessage function.
//	eg. showDebugMessage("Hi There");
//******************************************************************************************************************************

//******************************************************************************************************************************
//	The following possible query string formats are assumed
//
//	1. ?upload_id=32_character_alpha_numeric_string&start_time=unix_time&total_upload_size=total_upload_size_in_bytes
//	2. ?about
//******************************************************************************************************************************

$THIS_VERSION = "3.1";     // Version of this file
$UPLOAD_ID = '';           // Initialize upload id

require_once 'ubr_ini.php';
require_once 'ubr_lib.php';

if($_INI['php_error_reporting']){ error_reporting(E_ALL); }

if(isset($_GET['upload_id']) && preg_match("/^[a-zA-Z0-9]{32}$/", $_GET['upload_id']) && isset($_GET['start_time']) && isset($_GET['total_upload_size'])){ $UPLOAD_ID = $_GET['upload_id']; }
elseif(isset($_GET['about'])){ kak("<u><b>UBER UPLOADER GET PROGRESS</b></u><br>UBER UPLOADER VERSION =  <b>" . $_INI['uber_version'] . "</b><br>UBR_GET_PROGRESS = <b>" . $THIS_VERSION . "<b>", 1, __LINE__, $_INI['path_to_css_file']); }
else{ kak("<span class='ubrError'>ERROR</span>: Invalid parameters passed<br>", 1, __LINE__, $_INI['path_to_css_file']); }

$total_bytes_read = 0;
$files_uploaded = 0;
$current_filename = '';
$bytes_read = 0;
$upload_active = 0;
$flength_file = $UPLOAD_ID . '.flength';
$path_to_flength_file = $TEMP_DIR . $UPLOAD_ID . '.dir/' . $flength_file;
$temp_upload_dir = $TEMP_DIR . $UPLOAD_ID . '.dir';

// If the "/temp_dir/upload_id.dir/upload_id.flength" file exist, the upload is active
if(is_readable($path_to_flength_file)){
	$upload_active = 1;

	if($_INI['cgi_upload_hook']){
		// Get upload status by reading the "/temp_dir/upload_id.dir/upload_id.hook" file
		$hook_file = $TEMP_DIR . $UPLOAD_ID . '.dir/' . $UPLOAD_ID . '.hook';

		if($upload_status = readUbrFile($hook_file, $_INI['debug_ajax'])){ list($total_bytes_read, $files_uploaded, $current_filename, $bytes_read) = explode($DATA_DELIMITER, $upload_status); }
		else{ $upload_active = 0; }
	}
	else{
		// Get upload status by reading the "/temp_dir/upload_id.dir" directory
		if($handle = @opendir($temp_upload_dir)){
			while(($file_name = @readdir($handle)) !== false){
				if(($file_name !== '.') && ($file_name !== '..') && ($file_name !== $flength_file)){
					$total_bytes_read += sprintf("%u", @filesize($temp_upload_dir . '/' . $file_name));
					$files_uploaded++;
				}
			}
			@closedir($handle);

			if($files_uploaded > 0){ $files_uploaded -= 1; }
		}
		else{ $upload_active = 0; }
	}
}

if($upload_active && $total_bytes_read < $_GET['total_upload_size']){
	$lapsed_time = time() - $_GET['start_time'];

	if($_INI['debug_ajax']){
		if($_INI['cgi_upload_hook']){ showDebugMessage("Set progress: bytes uploaded=" . $total_bytes_read . " files uploaded=" . $files_uploaded . " current file=" . $current_filename . " bytes read=" . $bytes_read . " lapsed time=" . $lapsed_time); }
		else{ showDebugMessage("Set progress: bytes uploaded=" . $total_bytes_read . " files uploaded=" . $files_uploaded . " lapsed time=" . $lapsed_time); }
	}

	setProgressStatus($total_bytes_read, $files_uploaded, $current_filename, $bytes_read, $lapsed_time);
	getProgressStatus($_INI['get_progress_speed']);
}
else{
	stopDataLoop();

	if($_INI['debug_ajax']){ showDebugMessage("<span class='ubrWarning'>WARNING</span>: No active upload detected $path_to_flength_file"); }
}

?>