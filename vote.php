<?php

include('vogoo/vogoo.php');
include('vogoo/items.php');

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

function fetch_new_song() {

	$songres = mysql_query("select id as songid,artist,title,album,filename as url from songs where filename != 'dead' and title != '' order by listen_count asc limit 1");
        $song_array = mysql_fetch_assoc($songres);
        $outstring = '';
        foreach($song_array as $key => $value) {
                $outstring .= $key . '=' . urlencode($value) . '&';
		if($key == 'songid') {
			mysql_query("update songs set listen_count=listen_count+1 where id=" . $value);
		}
        }
	return $outstring;

}


$direction = $_GET['direction'];

$userid_encrypted = $_GET['userid'];
$userid = decrypt($userid_encrypted, 'shane88botwin');
$userid = substr($userid, 0, -10); // eliminating 'backgammon'
if(!ctype_digit($userid)) { // the userid should always be a number
	exit;
}


// this code checks if a specific track is passed in
if(isset($_GET['track'])) {
	$trackid = $_GET['track'];
	$direction = 'sent';
	if(!ctype_digit($trackid)) { // the trackid should always be a number
		exit;
	}
}

// let's add some checking in here at a later date to see whether one ip address has been hammering a bunch of different userids. friggin hax0rs.

mysql_connect('localhost', 'devsquid_hhg', 'SAVAGERY99');
mysql_select_db('devsquid_hhg');


$songid = $_GET['songid'];

switch($direction) {
	case 'load':
		$rec = $vogoo_items->member_get_recommended_items($userid);
		break;
	case 'love':
		$vogoo->set_rating($userid, $songid, 1);
		break;
	case 'hate':
		$vogoo->set_rating($userid, $songid, 0);
		break;
	case 'next':
		$vogoo->set_rating($userid, $songid, .8);
                $rec = $vogoo_items->member_get_recommended_items($userid);
		break;
	case 'sent':
		$rec = array();
		$rec[] = $trackid;
		break;
	/*
	case 'skipped':
                $rec = $vogoo_items->member_get_recommended_items($userid);
		break;
	*/
}

if($rec[0] == $songid) {
                $vogoo->set_rating($userid, $songid, .5);
                $rec = $vogoo_items->member_get_recommended_items($userid);
}   

if(empty($rec)) {
	$outstring = fetch_new_song();
}
else {
	$songres = mysql_query(sprintf("select id as songid,artist,title,album,filename as url from songs where id=%d", $rec[0])); 
	$song_array = mysql_fetch_assoc($songres);
        $outstring = '';
        foreach($song_array as $key => $value) {
                $outstring .= $key . '=' . urlencode($value) . '&';
		if($key == 'songid') {
                        mysql_query("update songs set listen_count=listen_count+1 where id=" . $value);
                }
        }

}

$outstring = substr($outstring, 0, -1); // chopping off the surplus &
echo $outstring;

?>
