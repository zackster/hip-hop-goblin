<?php

class Song {
	
	private $id;
	private $filename;
	private $url;
	private $tag;
	private $error;	
	private $referral;

	function __construct($identifier, $referral = '') {
		
		require_once('/home/devsquid/public_html/hiphopgoblin.com/classes/DBHandler.class.php');
		$db = new DBHandler();


		if(is_numeric($identifier)) {
			$id = $identifier;
	                $querystring = sprintf("SELECT id,url,filename,referral FROM songs WHERE id=%d", ($id));
        	        $db->query($querystring);
			if($db->error) {
				$this->error = 'Invalid identifier in constructor.';
				return 'ERROR';
			}
			else {
				$this->id = $db->result['id'];
				$this->url = $db->result['url'];
				$this->filename = $db->result['filename'];
				$this->referral = $db->result['referral'];
			}


		}
		elseif(strpos($identifier, 'http') !== FALSE) {
			$url = $identifier;
                	require_once('/home/devsquid/public_html/hiphopgoblin.com/classes/URLHandler.class.php');
	                $urlObject = new URLHandler($url);
        	        $url = $urlObject->url;
			$this->url = $url;
                        $querystring = sprintf("SELECT id,url,filename,referral FROM songs WHERE url='%s'", mysql_real_escape_string($url));
                        $db->query($querystring);
                        if($db->error) {
                                $this->error = 'Invalid identifier in constructor.';
                                return 'ERROR';
                        }
                        else {
                                $this->id = $db->result['id'];
                                $this->url = $db->result['url'];
                                $this->filename = $db->result['filename'];
                                $this->referral = $db->result['referral'];

                        }


		}
		else {
			$this->error = 'Invalid identifier type in constructor, must be int/url';
			return 'ERROR';
		}

		if($db->numRows == 0) {
			if($referral == '') {
				die('We should have passed in a referral.');
			}
			else {
				echo "Creating a new entry for this song.\n";
		                $querystring = sprintf("INSERT INTO songs (referral,url) VALUES ('%s', '%s')", mysql_real_escape_string($referral), mysql_real_escape_string($url));
        	                $db->query($querystring);
                	        $this->id = $db->insertID;
				$this->url= $url;
				$this->filename = '';	
				$this->referral = $referral;
			}
		}

	}
	
	function __get($var) {

		if($var == 'error') {
			return $this->error;
		}
		elseif($var == 'filename') {
			return $this->filename;
		}
		elseif($var == 'referral') {
			return $this->referral;
		}
		elseif($var == 'tag') {
			return $this->tag;
		}
		elseif($var == 'url') {
			return $this->url;
		}
		elseif($var == 'id') {
			return $this->id;
		}
	}

	function setFilename($filename) {
		
                $db = new DBHandler();
		$this->filename = $filename;	
		$querystring = sprintf("UPDATE songs SET filename='%s' WHERE id=%d", mysql_real_escape_string($filename), $this->id);
               	$db->query($querystring);
	
	}

        function markDead() {
                        
                $db = new DBHandler();
                $querystring = sprintf("UPDATE songs SET filename='DEAD' WHERE id=%d", $this->id);
                $db->query($querystring);
		echo "\n$querystring\n";
		echo "Song marked as dead.\n";
                        
        }                       
                   
	
	
	/*
		function ID3
		
		no parameters: returns an array of ID3 tags
		parameters: sets ID3 = parameters.
		
	*/
	
	function ID3($artist = null, $title = null, $album = null) {
                $db = new DBHandler();

		if(isset($artist) && isset($title) && isset($album)) {
			// we are hand-updating the db with this info. otherwise....
			$querystring = sprintf("UPDATE songs SET artist='%s',title='%s',album='%s' WHERE id=%d", mysql_real_escape_string($artist),mysql_real_escape_string($title),mysql_real_escape_string($album),$id);
			$db->query($querystring);
			return;
		}
		else {
	                require_once('/home/devsquid/public_html/hiphopgoblin.com/lib/ID3.lib.php');
			$getid3 = new getID3; 
			$mytag = $getid3->analyze($this->filename);
			getid3_lib::CopyTagsToComments($mytag);  
			$this->tag = array();
			$this->tag['artist'] = $mytag['comments']['artist'][0];
			$this->tag['album'] = $mytag['comments']['artist'][0];
			$this->tag['title'] = $mytag['comments']['title'][0];
                        $querystring = sprintf("UPDATE songs SET artist='%s',title='%s',album='%s' WHERE id=%d", mysql_real_escape_string($this->tag['artist']),mysql_real_escape_string($this->tag['title']),mysql_real_escape_string($this->tag['album']),$this->id);
			$db->query($querystring);
			return;
		}
        
		
	}
		
}



?>
