<?php

class MusicPlayer {


private $playMode;
private $userid;
private $history;


function __construct($playMode, $userid) {

	$this->playMode = $playMode;
	$this->userid = $userid;
	return;

}

// returns an array of songs listened to by the user, descending
function showHistory($type) {
        include_once('/home/devsquid/public_html/hiphopgoblin.com/classes/DBHandler.class.php');

	$dbh = new DBHandler();

	if($type == 'all') {
	        $dbh->query(sprintf("select songs.id from vogoo_ratings,songs where member_id=%d and vogoo_ratings.product_id=songs.id order by ts desc", $this->userid));
		$this->history = array();
		foreach($dbh->result as $lineitem) {
			array_push($this->history, $lineitem['id']);
		}
	}
	elseif($type == 'love') { // songs thumbs-upped
	        $dbh->query(sprintf("select songs.title,songs.artist,songs.id from vogoo_ratings,songs where member_id=%d and rating=10 and vogoo_ratings.product_id=songs.id order by ts desc", $this->userid));
	}
	elseif($type == 'good') { // songs thumbs-upped & listened to all the way through
	        $dbh->query(sprintf("select songs.title,songs.artist,songs.id from vogoo_ratings,songs where member_id=%d and rating>0 and vogoo_ratings.product_id=songs.id order by ts desc", $this->userid));
	}
	return $dbh->result;
}

function rateSong($songid, $feeling) {

	//require_once(vogoo)
	if($feeling == 'love') {
		$vogoo->set_rating($this->userid, $songid, 1);
	}
	elseif($feeling == 'hate') {
		$vogoo->set_rating($this->userid, $songid, 0);
	}
	elseif($feeling == 'like') {
		$vogoo->set_rating($this->userid, $songid, .8);
	}
	elseif($feeling == 'skip') {
		$vogoo->set_rating($this->userid, $songid, .4);
	}
}



function cueSong($cue, $songid = '') {
	

mysql_connect('localhost', 'devsquid_hhg', 'SAVAGERY99');
mysql_select_db('devsquid_hhg');

	

	include('vogoo/vogoo.php');
	include('vogoo/items.php');

	$this->showHistory('all');
	
	if($this->playMode == 'autoplay') {
		$rec = $vogoo_items->member_get_recommended_items($this->userid);
		foreach($rec as $recommended_item) {
			echo "ITEM: $recommended_item \n";
		} 	
				
	}
	
	exit;
//	$db = new DBHandler();
	if($cue == 'load') {
                $song = $db->query(sprintf("select id as songid,artist,title,album,filename as url from songs where id=%d", $id));
	}
	elseif($cue == 'love') {
			
	}
	//return $info;

}

}
?>
