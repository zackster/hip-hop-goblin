<?php

//******************************************************************************************************
//	Name: ubr_file_upload.php
//	Revision: 3.2
//	Date: 5:59 PM August 22, 2009
//	Link: http://uber-uploader.sourceforge.net
//	Developer: Peter Schmandra
//	Description: Select and submit upload files.
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

$THIS_VERSION = '3.2';        // Version of this file

require_once 'ubr_ini.php';
require_once 'ubr_lib.php';

if($_INI['php_error_reporting']){ error_reporting(E_ALL); }

//Set config file
if($_INI['multi_configs_enabled']){
	//////////////////////////////////////////////////////////////////////////////
	//	ATTENTION
	//
	//	Put your multi config file code here. eg
	//
	//	if($_SESSION['user_name'] == 'TOM'){ $config_file = 'tom_config.php'; }
	//	if($_COOKIE['user_name'] == 'TOM'){ $config_file = 'tom_config.php'; }
	//////////////////////////////////////////////////////////////////////////////
}
else{ $config_file = $_INI['default_config']; }

// Load config file
require_once $config_file;

//***************************************************************************************************************
//	The following possible query string formats are assumed
//
//	1. No query string
//	2. ?about
//***************************************************************************************************************

/*

if($_INI['debug_php']){ phpinfo(); exit(); }
elseif($_INI['debug_config']){ debug($_CONFIG['config_file_name'], $_CONFIG); exit(); }
elseif(isset($_GET['about'])){
	kak("<u><b>UBER UPLOADER FILE UPLOAD</b></u><br>UBER UPLOADER VERSION =  <b>" . $_INI['uber_version'] . "</b><br>UBR_FILE_UPLOAD = <b>" . $THIS_VERSION . "</b><br>\n", 1, __LINE__, $_INI['path_to_css_file']);
}

*/

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<title>HipHopGoblin Upload: Step 1 of 2</title>
		<meta http-equiv="content-type" content="text/html; charset=utf-8">
		<meta http-equiv="expires" content="-1">
		<meta name="robots" content="index,nofollow">
		<!-- Please do not remove this tag: Uber-Uploader Ver 6.8 http://uber-uploader.sourceforge.net -->
		<link rel="stylesheet" type="text/css" href="<?php print $_INI['path_to_css_file']; ?>">
		<script language="JavaScript" type="text/JavaScript" src="<?php print $_INI['path_to_jquery']; ?>"></script>
		<?php if($_INI['block_ui_enabled']){ ?><script language="JavaScript" type="text/JavaScript" src="<?php print $_INI['path_to_block_ui']; ?>"></script><?php } ?>
		<script language="javascript" type="text/javascript" src="<?php print $_INI['path_to_js_script']; ?>"></script>
		<script language="javascript" type="text/javascript">
			UberUpload.path_to_link_script = "<?php print $_INI['path_to_link_script']; ?>";
			UberUpload.path_to_set_progress_script = "<?php print $_INI['path_to_set_progress_script']; ?>";
			UberUpload.path_to_get_progress_script = "<?php print $_INI['path_to_get_progress_script']; ?>";
			UberUpload.path_to_upload_script = "<?php print $_INI['path_to_upload_script']; ?>";
			UberUpload.check_allow_extensions_on_client = <?php print $_CONFIG['check_allow_extensions_on_client']; ?>;
			UberUpload.check_disallow_extensions_on_client = <?php print $_CONFIG['check_disallow_extensions_on_client']; ?>;
			<?php if($_CONFIG['check_allow_extensions_on_client']){ print "UberUpload.allow_extensions = /" . $_CONFIG['allow_extensions'] . "$/i;\n"; } ?>
			<?php if($_CONFIG['check_disallow_extensions_on_client']){ print "UberUpload.disallow_extensions = /" . $_CONFIG['disallow_extensions'] . "$/i;\n"; } ?>
			UberUpload.check_file_name_format = <?php print $_CONFIG['check_file_name_format']; ?>;
			<?php if($_CONFIG['check_file_name_format']){ print "UberUpload.check_file_name_regex = /" . $_CONFIG['check_file_name_regex'] . "/;\n"; } ?>
			<?php if($_CONFIG['check_file_name_format']){ print "UberUpload.check_file_name_error_message = '" . $_CONFIG['check_file_name_error_message'] . "';\n"; } ?>
			<?php if($_CONFIG['check_file_name_format']){ print "UberUpload.max_file_name_chars = " . $_CONFIG['max_file_name_chars'] . ";\n"; } ?>
			<?php if($_CONFIG['check_file_name_format']){ print "UberUpload.min_file_name_chars = " . $_CONFIG['min_file_name_chars'] . ";\n"; } ?>
			UberUpload.check_null_file_count = <?php print $_CONFIG['check_null_file_count']; ?>;
			UberUpload.check_duplicate_file_count = <?php print $_CONFIG['check_duplicate_file_count']; ?>;
			UberUpload.max_upload_slots = <?php print $_CONFIG['max_upload_slots']; ?>;
			UberUpload.cedric_progress_bar = <?php print $_CONFIG['cedric_progress_bar']; ?>;
			UberUpload.cedric_hold_to_sync = <?php print $_CONFIG['cedric_hold_to_sync']; ?>;
			UberUpload.bucket_progress_bar = <?php print $_CONFIG['bucket_progress_bar']; ?>;
			UberUpload.progress_bar_width = <?php print $_INI['progress_bar_width']; ?>;
			UberUpload.progress_bar_width = <?php print $_INI['progress_bar_width']; ?>;
			UberUpload.show_percent_complete = <?php print $_CONFIG['show_percent_complete']; ?>;
			UberUpload.block_ui_enabled = <?php print $_INI['block_ui_enabled']; ?>;
			UberUpload.show_files_uploaded = <?php print $_CONFIG['show_files_uploaded']; ?>;
			UberUpload.show_current_position = <?php print $_CONFIG['show_current_position']; ?>;
			UberUpload.show_current_file = <?php if($_INI['cgi_upload_hook'] && $_CONFIG['show_current_file']){ print "1"; }else{ print "0"; } ?>;
			UberUpload.show_elapsed_time = <?php print $_CONFIG['show_elapsed_time']; ?>;
			UberUpload.show_est_time_left = <?php print $_CONFIG['show_est_time_left']; ?>;
			UberUpload.show_est_speed = <?php print $_CONFIG['show_est_speed']; ?>;
			var JQ = jQuery.noConflict();

			JQ(document).ready(function(){
				UberUpload.resetFileUploadPage();
				JQ("#upload_button").bind("click", function(e){ UberUpload.linkUpload(); });
				JQ("#reset_button").bind("click", function(e){ UberUpload.resetFileUploadPage(); });
				JQ("#progress_bar_background").css("width", UberUpload.progress_bar_width);

				if(UberUpload.show_files_uploaded || UberUpload.show_current_position || UberUpload.show_elapsed_time || UberUpload.show_est_time_left || UberUpload.show_est_speed){
					JQ("#upload_stats_toggle").bind("click", function(e){ UberUpload.toggleUploadStats(); });
					JQ("#upload_stats_toggle").html("[+]");
					JQ("#upload_stats_toggle").attr("title", "Toggle Upload Statistics");
				}
			});
		</script>
	</head>
	<body id="body_container">
		<div id="main_container">
			<?php if($_INI['debug_ajax']){ ?><div id='ubr_debug'></div><?php } ?>
			<div id="ubr_alert"></div>

			<!-- Progress Bar -->
			<div id="progress_bar_container">
				<div id="upload_stats_toggle">&nbsp;</div>
				<div id="progress_bar_background">
					<div id="progress_bar"></div>
				</div>
				<div id="percent_complete">&nbsp;</div>
			</div>

			<br clear="all">

			<!-- Upload Stats -->
			<?php if($_CONFIG['show_files_uploaded'] || $_CONFIG['show_current_position'] || $_CONFIG['show_elapsed_time'] || $_CONFIG['show_est_time_left'] || $_CONFIG['show_est_speed']){ ?>
				<div id="upload_stats_container">
					<?php if($_CONFIG['show_files_uploaded']){ ?>
					<div class='upload_stats_label'>&nbsp;Files Uploaded:</div>
					<div class='upload_stats_data'><span id="files_uploaded">0</span> of <span id="total_uploads">0</span></div>
					<?php }if($_CONFIG['show_current_position']){ ?>
					<div class='upload_stats_label'>&nbsp;Current Position:</div>
					<div class='upload_stats_data'><span id="current_position">0</span> / <span id="total_kbytes">0</span> KBytes</div>
					<?php }if($_INI['cgi_upload_hook'] && $_CONFIG['show_current_file']){ ?>
					<div class='upload_stats_label'>&nbsp;Current File Uploading:</div>
					<div class='upload_stats_data'><span id="current_file"></span></div>
					<?php }if($_CONFIG['show_elapsed_time']){ ?>
					<div class='upload_stats_label'>&nbsp;Elapsed Time:</div>
					<div class='upload_stats_data'><span id="elapsed_time">0</span></div>
					<?php }if($_CONFIG['show_est_time_left']){ ?>
					<div class='upload_stats_label'>&nbsp;Est Time Left:</div>
					<div class='upload_stats_data'><span id="est_time_left">0</span></div>
					<?php }if($_CONFIG['show_est_speed']){ ?>
					<div class='upload_stats_label'>&nbsp;Est Speed:</div>
					<div class='upload_stats_data'><span id="est_speed">0</span> KB/s.</div>
					<?php } ?>
				</div>
				<br clear="all">
			<?php } ?>

			<!-- Container for upload iframe -->
			<div id="upload_container"></div>
	

			<!-- Start Upload Form -->
			<h2>Easy 2 Step Upload</h2>
			<ul><li><b>Select up to 10 songs to upload</b></li><li>Confirm the artist and song titles</li></ul>
			<form id="uu_upload" name="uu_upload" method="post" enctype="multipart/form-data" action="#" style="margin:0px; padding:0px">
				<noscript><span class="ubrError">ERROR</span>: Javascript must be enabled to use Uber-Uploader.<br><br></noscript>
				<div id="file_picker_container"></div>
				<div id="upload_slots_container"></div>
				<!-- Add Your Form Values Here -->
				<div id="upload_buttons_container"><input type="button" id="reset_button" name="reset_button" value="Reset">&nbsp;&nbsp;&nbsp;<input type="button" id="upload_button" name="upload_button" value="Upload"></div>
			</form>
		</div>
		<br clear="all">
	</body>
</html>
