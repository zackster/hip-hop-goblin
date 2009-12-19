<?php
//******************************************************************************************************
//	Name: ubr_ini_progress.php
//	Revision: 3.1
//	Date: 5:59 PM August 22, 2009
//	Link: http://uber-uploader.sourceforge.net
//	Developer: Peter Schmandra
//	Description: Initializes Uber-Uploader
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

$TEMP_DIR                            = '/home/devsquid/ubr_temp/';             // *ATTENTION * : The $TEMP_DIR value MUST be duplicated in the "ubr_upload.pl" file
$DATA_DELIMITER                      = '<=>';                        // *ATTENTION * : The $DATA_DELIMITER value MUST be duplicated in the "ubr_upload.pl" file

$_INI['uber_version']                = '6.8';                        // This version of Uber-Uploader
$_INI['path_to_upload_script']       = '/cgi-bin/ubr_upload.pl';     // Path info
$_INI['path_to_link_script']         = '/upload/ubr_link_upload.php';        // Path info
$_INI['path_to_set_progress_script'] = '/upload/ubr_set_progress.php';       // Path info
$_INI['path_to_get_progress_script'] = '/upload/ubr_get_progress.php';       // Path info
$_INI['path_to_js_script']           = '/upload/ubr_file_upload.js';         // Path info
$_INI['path_to_jquery']              = '/upload/jquery-1.3.2.min.js';        // Path Info
$_INI['path_to_block_ui']            = '/upload/jquery.blockUI.js';          // Path Info
$_INI['path_to_css_file']            = '/upload/ubr.css';                    // Path info
$_INI['default_config']              = '/home/devsquid/public_html/hiphopgoblin.com/upload/ubr_default_config.php';     // Path info
$_INI['redirect_after_upload']       = 1;                            // Enable/Disable redirect after upload
$_INI['embedded_upload_results']     = 0;                            // Display the upload results on the file upload page
$_INI['block_ui_enabled']            = 1;                            // Enable/Disable block UI
$_INI['multi_configs_enabled']       = 0;                            // Enable/Disable multi config files
$_INI['cgi_upload_hook']             = 0;                            // Use the CGI hook file to get upload status. Requires CGI.pm >= 3.15
$_INI['get_progress_speed']          = 1000;                         // CAUTION ! How frequent the web server is poled for upload status. 5000=5 seconds, 1000=1 second, 500=0.5 seconds, 250=0.25 seconds. etc.
$_INI['progress_bar_width']          = 400;                          // Width of the progress bar in pixels (This value is also used in calculations)
$_INI['delete_link_file']            = 1;                            // Enable/Disable delete link file
$_INI['delete_redirect_file']        = 1;                            // Enable/Disable delete redirect file
$_INI['purge_link_files']            = 1;                            // Enable/Disable delete old upload_id.link files
$_INI['purge_link_limit']            = 300;                          // Delete old upload_id.link files older than X seconds
$_INI['purge_redirect_files']        = 1;                            // Enable/Disable delete old upload_id.redirect files
$_INI['purge_redirect_limit']        = 300;                          // Delete old redirect files older than X seconds
$_INI['purge_temp_dirs']             = 1;                            // Enable/Disable delete old upload_id.dir directories
$_INI['purge_temp_dirs_limit']       = 43200;                        // Delete old upload_id.dir directories older than X seconds (43200=12 hrs)
$_INI['flength_timeout_limit']       = 6;                            // Max number of seconds to find the flength file
$_INI['hook_timeout_limit']          = 6;                            // Max number of seconds to find the hook file
$_INI['debug_ajax']                  = 0;                            // Enable/Disable AJAX debug mode. Add your own debug messages by calling the "showDebugMessage() " function. UPLOADS POSSIBLE.
$_INI['debug_php']                   = 0;                            // Enable/Disable PHP debug mode. Dumps your PHP settings to screen and exits. UPLOADS IMPOSSIBLE.
$_INI['debug_config']                = 0;                            // Enable/Disable config debug mode. Dumps the loaded config file to screen and exits. UPLOADS IMPOSSIBLE.
$_INI['debug_upload']                = 0;                            // Enable/Disable debug mode in uploader. Dumps your CGI and loaded config settings to screen and exits. UPLOADS IMPOSSIBLE.
$_INI['debug_finished']              = 0;                            // Enable/Disable debug mode in the upload finished page. Dumps all values to screen and exits. UPLOADS POSSIBLE.
$_INI['php_error_reporting']         = 1;                            // Enable/Disable PHP error_reporting(E_ALL). UPLOADS POSSIBLE.
?>
