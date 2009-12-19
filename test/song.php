<?php

        include('../classes/Song.class.php');
	$song = new Song('http://usershare.net/ytkka3xvak5c', 'whocares');
	echo "Error: $song->error\n";
	echo "URL: $song->url\n";
	echo "ID: $song->id\n";
	

?>

