<?php

//******************************************************************************************************
//	Name: ubr_finished.php
//	Revision: 3.7
//	Date: 5:58 PM August 22, 2009
//	Link: http://uber-uploader.sourceforge.net
//	Developer: Peter Schmandra
//	Description: Show successful file uploads.
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
// The following possible query string formats are assumed
//
// 1. ?upload_id=32_character_alpha_numeric_string
// 2. ?about
//****************************************************************************************************************

$THIS_VERSION = "3.7";                                // Version of this file
$UPLOAD_ID = '';                                      // Initialize upload id

require_once 'ubr_ini.php';
require_once 'ubr_lib.php';
require_once 'ubr_finished_lib.php';

if($_INI['php_error_reporting']){ error_reporting(E_ALL); }

header('Content-type: text/html; charset=UTF-8');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Last-Modified: '.date('r'));
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', FALSE);
header('Pragma: no-cache');

if(isset($_GET['upload_id']) && preg_match("/^[a-zA-Z0-9]{32}$/", $_GET['upload_id'])){ $UPLOAD_ID = $_GET['upload_id']; }
elseif(isset($_GET['about'])){ kak("<u><b>UBER UPLOADER FINISHED PAGE</b></u><br>UBER UPLOADER VERSION =  <b>" . $_INI['uber_version'] . "</b><br>UBR_FINISHED = <b>" . $THIS_VERSION . "<b><br>\n", 1 , __LINE__, $_INI['path_to_css_file']); }
else{ kak("<span class='ubrError'>ERROR</span>: Invalid parameters passed<br>", 1, __LINE__, $_INI['path_to_css_file']); }

//Declare local values
$_XML_DATA = array();                                          // Array of xml data read from the upload_id.redirect file
$_CONFIG_DATA = array();                                       // Array of config data read from the $_XML_DATA array
$_POST_DATA = array();                                         // Array of posted data read from the $_XML_DATA array
$_FILE_DATA = array();                                         // Array of 'FileInfo' objects read from the $_XML_DATA array
$_FILE_DATA_TABLE = '';                                        // String used to store file info results nested between <tr> tags
$_FILE_DATA_EMAIL = '';                                        // String used to store file info results

$xml_parser = new XML_Parser;                                  // XML parser
$xml_parser->setXMLFile($TEMP_DIR, $_GET['upload_id']);        // Set upload_id.redirect file
$xml_parser->setXMLFileDelete($_INI['delete_redirect_file']);  // Delete upload_id.redirect file when finished parsing
$xml_parser->parseFeed();                                      // Parse upload_id.redirect file

// Display message if the XML parser encountered an error
if($xml_parser->getError()){ kak($xml_parser->getErrorMsg(), 1, __LINE__, $_INI['path_to_css_file']); }

$_XML_DATA = $xml_parser->getXMLData();                        // Get xml data from the xml parser
$_CONFIG_DATA = getConfigData($_XML_DATA);                     // Get config data from the xml data
$_POST_DATA  = getPostData($_XML_DATA);                        // Get post data from the xml data
$_FILE_DATA = getFileData($_XML_DATA);                         // Get file data from the xml data

// Output XML DATA, CONFIG DATA, POST DATA, FILE DATA to screen and exit if DEBUG_ENABLED.
if($_INI['debug_finished']){
	debug("<br><u>XML DATA</u>", $_XML_DATA);
	debug("<u>CONFIG DATA</u>", $_CONFIG_DATA);
	debug("<u>POST DATA</u>", $_POST_DATA);
	debug("<u>FILE DATA</u>", $_FILE_DATA);

	exit();
}

/////////////////////////////////////////////////////////////////////////////////////////////////////
//
//           *** ATTENTION: ENTER YOUR CODE HERE !!! ***
//
//	This is a good place to put your post upload code. Like saving the
//	uploaded file information to your DB or doing some image
//	manipulation. etc. Everything you need is in the
//	$_XML_DATA, $_CONFIG_DATA, $_POST_DATA and $_FILE_DATA arrays.
//
/////////////////////////////////////////////////////////////////////////////////////////////////////
//	NOTE: You can now access all XML values below this comment. eg.
//
//	$_XML_DATA['upload_dir']; or $_XML_DATA['link_to_upload'] etc
/////////////////////////////////////////////////////////////////////////////////////////////////////
//	NOTE: You can now access all config values below this comment. eg.
//
//	$_CONFIG_DATA['upload_dir']; or $_CONFIG_DATA['link_to_upload'] etc
/////////////////////////////////////////////////////////////////////////////////////////////////////
//	NOTE: You can now access all post values below this comment. eg.
//
//	if(isset($_POST_DATA['client_id'])){ do something; }
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//	NOTE: You can now access all file (slot, name, size, type, status, status_desc) values below this comment. eg.
//
//	foreach($_FILE_DATA as $slot => $value){
//		$file_slot = $_FILE_DATA[$slot]->getFileInfo('slot');
//		$file_name = $_FILE_DATA[$slot]->getFileInfo('name');
//		$file_size = $_FILE_DATA[$slot]->getFileInfo('size');
//		$file_type = $_FILE_DATA[$slot]->getFileInfo('type');
//		$file_status = $_FILE_DATA[$slot]->getFileInfo('status');
//		$file_status_desc = $_FILE_DATA[$slot]->getFileInfo('status_desc');
//	}


/// we gotta make sure we have the user's userid...

mysql_connect('localhost', 'devsquid_hhg', 'SAVAGERY99');
mysql_select_db('devsquid_hhg');

function encrypt($string, $key) {
        $result = '';
        for($i=0; $i<strlen($string); $i++) {
                $char = substr($string, $i, 1);
                $keychar = substr($key, ($i % strlen($key))-1, 1);
                $char = chr(ord($char)+ord($keychar));
                $result.=$char;
        }
        return base64_encode($result);
}

function decrypt($string, $key) {
        $result = '';
        $string = base64_decode($string);
        for($i=0; $i<strlen($string); $i++) {
                $char = substr($string, $i, 1);
                $keychar = substr($key, ($i % strlen($key))-1, 1);
                $char = chr(ord($char)-ord($keychar));
                $result.=$char;
        }
        return $result;
}

if(isset($_COOKIE['userid'])) {
        $userid_encrypted = $_COOKIE['userid'];
        $userid = decrypt($userid_encrypted, 'shane88botwin');
        $userid = substr($userid, 0, -10); // eliminating 'backgammon'
        $user_result = mysql_query(sprintf("SELECT id FROM users WHERE id=%d", mysql_real_escape_string($userid)));
        if(mysql_num_rows($user_result) == 1) {
                        // we are good to go!
        }
        else {
                // something's fucked up; fuck that. paece.
                $ip = $_SERVER['REMOTE_ADDR'];
                mysql_query(sprintf("INSERT INTO users (remote_addr,date_added) VALUES ('%s',now())",mysql_real_escape_string($ip)));
                $userid = mysql_insert_id();
                $userid_encrypted = encrypt($userid . 'backgammon', 'shane88botwin');
                setcookie('userid', $userid_encrypted, time()+60*60*24*3365);
        }
}
else {

        $ip = $_SERVER['REMOTE_ADDR'];
        mysql_query(sprintf("INSERT INTO users (remote_addr,date_added) VALUES ('%s',now())",mysql_real_escape_string($ip)));
        $userid = mysql_insert_id();
        $userid_encrypted = encrypt($userid . 'backgammon', 'shane88botwin');
        setcookie('userid', $userid_encrypted, time()+60*60*24*3365);

}


$songids = array();
foreach($_FILE_DATA as $slot => $value) {
	if($_FILE_DATA[$slot]->getFileInfo('status') == 1) {
                $file_path = '/home/devsquid/public_html/hiphopgoblin.com/temp/ubr/' . $_FILE_DATA[$slot]->getFileInfo('name');
		$song_path = 'http://www.hiphopgoblin.com/temp/ubr/' . $_FILE_DATA[$slot]->getFileInfo('name');
		$query = sprintf("INSERT INTO songs (filename, url, referral, live) VALUES ('%s','%s','direct_upload:%d','false')", mysql_real_escape_string($song_path), mysql_real_escape_string($file_path), $userid);
		mysql_query($query);
		echo mysql_error();
		// we need to pass the insert_id into getFormattedUploadResults
		array_push($songids, mysql_insert_id());
		
	}
}


/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//	Create Thumnail Example
//
//	createThumbFile(source_file_path, source_file_name, thumb_file_path, thumb_file_name, thumb_file_width, thumb_file_height)
//
//	EXAMPLE
//	$file_extension = getFileExtension($_FILE_DATA['upfile_1241018436628']->name);
//
//	if($file_extension == 'jpg' || $file_extension == 'jpeg' || $file_extension == 'png'){ $success = createThumbFile($_CONFIG_DATA['upload_dir'], $_FILE_DATA['upfile_1241018436628']->name, $_CONFIG_DATA['upload_dir'], 'thumb_' . $_FILE_DATA['upfile_1241018436628']->name, 120, 100); }
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

// Format upload results
$_FORMATTED_UPLOAD_RESULTS = getFormattedUploadResults($_FILE_DATA, $_CONFIG_DATA, $_POST_DATA);

// Spawn the process to upload to S3
$include_path = '/home/devsquid/public_html/hiphopgoblin.com';
include($include_path . '/lib/' . 'S3.lib.php');

mysql_connect('localhost','devsquid_hhg','SAVAGERY99');
mysql_select_db('devsquid_hhg');

$song_result = mysql_query("SELECT filename FROM songs WHERE filename like 'http://www.hiphopgoblin.com%'");
$songs = array();
while($row = mysql_fetch_assoc($song_result)) {
        $temp = array();
        $temp['old_db_filename'] = $row['filename'];
        /*  http://www.hiphopgoblin.com/temp/ubr/TEST.mp3 ===>  temp/ubr/TEST.mp3 */
        $truncated_path = substr($row['filename'], 28);
        $path = '/home/devsquid/public_html/hiphopgoblin.com/' . $truncated_path;
        $temp['old_path'] = $path;
        array_push($songs, $temp);
}

foreach($songs as $song_to_upload) {


        $s3_result = uploadFile($song_to_upload['old_path'], baseName($song_to_upload['old_path']), 'hiphopgoblin', true);
        if($s3_result['response_code'] == 200) { // Upload to S3. params:(local_name, filename_for_s3, bucket_name, debugmode)
                $s3_url = $s3_result['url']; // 'http://hiphopgoblin.s3.amazonaws.com/' . baseName($song->filename);
                mysql_query(sprintf("UPDATE songs SET filename='%s' WHERE filename='%s'", mysql_real_escape_string($s3_url), mysql_real_escape_string($song_to_upload['old_db_filename'])));
                unlink($song_to_upload['old_path']);
        }
        else {
		echo 'There was a problem and the upload did not go through. Please contact info@hiphopgoblin.com ... thank you!';
        }
}




// Create and send email
// if($_CONFIG_DATA['send_email_on_upload']){ emailUploadResults($_FILE_DATA, $_CONFIG_DATA, $_POST_DATA); }

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<title>HipHopGoblin Upload: Step 2 of 2</title>
		<meta http-equiv="content-type" content="text/html; charset=utf-8">
		<meta http-equiv="pragma" content="no-cache">
		<meta http-equiv="cache-control" content="no-cache">
		<meta http-equiv="expires" content="-1">
		<meta name="robots" content="none">
		<link rel="stylesheet" type="text/css" href="<?php print $_INI['path_to_css_file']; ?>">
		<script type="text/javascript" src="jquery-1.3.2.min.js"></script>
		<script type="text/javascript" src="jquery.validate.min.js"></script>
		<script type="text/javascript">
		$(document).ready(function() {
			$("#setID3").validate();	
		});
		</script>
	</head>
	<body id="body_container">
                <div id="main_container">

                        <br clear="all"/>
		                       <h2>Easy 2 Step Upload</h2>
                        <ul><li>Select up to 10 songs to upload</li><li><b>Confirm the artist and song titles</b></li></ul>

			<div id="upload_results_container">
				<form id="setID3" name="setID3" method="POST" action="id3.php"> 
				<?php print $_FORMATTED_UPLOAD_RESULTS; ?>
				<br /><br />
				<input type="submit" name="submit" id="submit" value="Submit">
				</form>
			</div>
			<br clear="all"/>
			<?php if(!$_INI['embedded_upload_results']){ ?><br><input type="button" value="Go Back" onClick="history.go(-1)"><?php } ?>
		</div>
		<br clear="all"/>
	</body>
</html>
