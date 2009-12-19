<?php

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

mysql_connect('localhost', 'devsquid_hhg', 'SAVAGERY99');
mysql_select_db('devsquid_hhg');

$userid_encrpyted = $_COOKIE['userid'];
if(isset($_COOKIE['userid'])) {
	$userid_encrypted = $_COOKIE['userid'];
	$userid = decrypt($userid_encrypted, 'shane88botwin');
	$userid = substr($userid, 0, -10); // eliminating 'backgammon'
	$user_result = mysql_query(sprintf("select id from users where id=%d", mysql_real_escape_string($userid)));
	if(mysql_num_rows($user_result) == 1) {
			// we are good to go!
	}
	else {
		// something's fucked up; fuck that. paece.
		exit;
	}
}
?>
<h3>You have recently enjoyed these songs:</h3><br /><table>
<?php
$i=1;
$history_result = mysql_query(sprintf("select songs.title,songs.artist,songs.id from vogoo_ratings,songs where member_id=%d and rating>0 and vogoo_ratings.product_id=songs.id order by ts desc", $userid));
while($row = mysql_fetch_row($history_result)) {
?>
<tr><td><?=$i;?>.</td><td><a href="#" onclick="javascript:cueSong(<?=$row[2];?>)"><?=$row[1];?> - <?=$row[0];?></a></td></tr>
<?php
$i++;
}
?>
</table>
</div>
