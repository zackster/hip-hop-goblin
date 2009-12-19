<?php

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

$songs = array();

foreach($_POST as $key=>$val) {
	if(substr($key,0,6) == 'artist') {
		$artist = substr($key,6);
		$songs[$artist]['artist'] = $val;
	}
	elseif(substr($key,0,5) == 'title') {
		$title = substr($key,5);
		$songs[$title]['title'] = $val;
	}

}
echo '<head><style type="text/css">.linkbox { border: 1px solid #C0C0C0; margin: 0px auto;text-align: center;width: 800px;padding: 5px;</style></head>';

echo '<div class="linkbox">';
echo '<h3>Here are links to your songs, you should link them to people you know</h3>';

foreach($songs as $sid => $array) {
	$querystring = sprintf("SELECT id,url,filename,referral FROM songs WHERE id=%d", $sid);
	$queryresult = mysql_query($querystring);
	$row = mysql_fetch_assoc($queryresult);
	if($row['referral'] == ('direct_upload:'.$userid)) {
		mysql_query(sprintf("UPDATE songs SET artist='%s',title='%s' WHERE id=%d", mysql_real_escape_string($array['artist']), mysql_real_escape_string($array['title']), $sid));
		echo mysql_error();
		$link = 'http://www.hiphopgoblin.com/?sid=' . $sid;
		echo $array['artist'] . ' - ' . $array['title'] . ' <input type="text" value="' . $link . '" onfocus="javascript:this.focus();this.select();" size=34>';
		echo '<br />'; 
	}
	else {
		
		echo "An error occurred! Enable cookies, and then start the process over. http://www.hiphopgoblin.com"; 
	/*

		echo $userid;
		print_r($array);
		print_r($row);
		// this should not happen ... 
	
	*/

	}
}
echo '</div>';
?>
