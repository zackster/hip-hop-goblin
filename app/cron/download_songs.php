<?php

$include_path = '/home/devsquid/public_html/hiphopgoblin.com';
include($include_path . '/config/'  . 'passwords.php');
include($include_path . '/classes/' . 'DBHandler.class.php');
include($include_path . '/classes/' . 'Song.class.php');
include($include_path . '/classes/' . 'URLHandler.class.php');
include($include_path . '/classes/' . 'DownloadManager.class.php');
include($include_path . '/lib/' . 'S3.lib.php');
include($include_path . '/lib/' . 'Twitter.lib.php');

$twitter = new Twitter('hiphopgoblin', $hhg_password_twitter);


// Grab all the blank songs 
$db = new DBHandler(); // refactor the DBHandler class to use a singleton
$querystring = sprintf("SELECT * FROM songs WHERE filename=''");
$db->query($querystring);
foreach($db->result as $aresult) {
	// Make the result into a song object
	$song = new Song($aresult['url'], $aresult['referral']);
	echo "Working with new song object {{$aresult['url']}}, {{$aresult['id']}}\n";
	// Create a download object with the song's URL
	$do = new DownloadManager($song->url);
	$download_response = $do->download();
	if($download_response == 'OK'){ // If the download went through
		echo "\nThe download went through. 1/7";
		$song->setFilename($do->filename); // Update the song's filename with the local file name
		echo "\nWe set the filename to the local file. 2/7";
		$song->ID3(); // Put the song's ID3 tags in the database. TODO: add in code in the Song class to mark dead if invalid ID3s, and return accordingly
		echo "\nWe update the MySQL DB to reflect the ID3 tags. 3/7";
		$s3_result = uploadFile($song->filename, baseName($song->filename), 'hiphopgoblin', true);
                if($s3_result['response_code'] == 200) { // Upload to S3. params:(local_name, filename_for_s3, bucket_name, debugmode)
			echo "\nThe upload went through. 4/7";
			$s3_url = $s3_result['url']; // 'http://hiphopgoblin.s3.amazonaws.com/' . baseName($song->filename);
                        unlink($song->filename); // Delete the file locally
                        echo "\nWe deleted the file locally. 5/7";
			$song->setFilename($s3_url); // Update the song's filename with its Amazon S3 url
			echo "\nWe again set the song's filename to reflect the S3 URL. 6/7";
			if(substr($song->referral,0,7) == 'twitter') { // if someone linked the song to us on twitter 
				$whomto = '@' . substr($song->referral,8); // get their username 
				$status = $whomto . ' hot new track by ' . $song->tag['artist'] . ' on HHG! http://www.hiphopgoblin.com/?sid=' . $song->id . ' <-- Please RT!';
				$twitter->updateStatus($status); // write them a tweet announcing the upload of the song
				echo "\nWe updated Twitter. 7/7\n\n";
			}
		}
		else {
                        $song->markDead();
		}	
	}
	elseif($download_response == 'SKIP') {
			// do nothing
		echo "\nWe are unequipped to handle this and are doing nothing.\n";
	}
	else {
		echo "The download failed: " . $do->error, "\n";
			$song->markDead();
                        if(substr($song->referral,0,7) == 'twitter') { // if someone linked the song to us on twitter
                                $whomto = '@' . substr($song->referral,8); // get their username
                                $status = $whomto . ' the file you linked me to was invalid: more than 10 megabytes, or not an mp3. Use limelinx/zshare/a direct link, please!';
                                //$twitter->updateStatus($status); // write them a tweet announcing the upload of the song
                                echo "\nWe updated Twitter. 7/7\n\n";
                        }
	}
	

}

?>
