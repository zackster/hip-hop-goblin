<?php

class DownloadManager {

	private $url;
	private $error;
	private $filename;
	private $limereturn;
	private $skippable;

	function __construct($url) {
		$this->url = $url;
		return;
	}

	function __get($var) {
		if($var == 'error') {
			return $this->error;
		}
		elseif($var == 'filename') {
			return $this->filename;
		}
		return;
	}

	function zshareURL() {
		$this->url= str_replace("audio", "download", $this->url);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'http://www.zshare.net/myzshare/process.php?loc=http://www.zshare.net/myzshare/login.php');
		curl_setopt ($ch, CURLOPT_POST, 1);
		curl_setopt ($ch, CURLOPT_HEADER, 1);
		curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 5);
		curl_setopt ($ch, CURLOPT_POSTFIELDS, 'username=HHG3&password=PASSPASS&submit=Login');
		curl_setopt ($ch, CURLOPT_USERAGENT, 'Mozila/5.0 (iPod; U; CPU like Mac OS X; en) AppleWebKit/420.1 (KHTML, like Geckto) Version/3.0 Mobile/3A101a Safari/419.3');
		curl_setopt ($ch, CURLOPT_COOKIEJAR, '/home/devsquid/public_html/hiphopgoblin.com/temp/zsharecookies.txt');
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
		$store = curl_exec ($ch);
		curl_setopt($ch, CURLOPT_URL, $this->url);
		$content = curl_exec ($ch);
		$webpage = split("\n", $content);
		curl_close ($ch);

		foreach($webpage as $line) {
			if($position = strpos($line, 'link_enc')) {
				$file = explode(",", $line);
				$file[0] = 'h';
				$last_char = $file[count($file)-1];
				$file[count($file)-1] = $last_char[1];
				$mp3 = implode("", $file);
				$mp3 = str_replace("'", "", $mp3);
				return $mp3; 
			}       
		}
		return 'FAIL';
	}


	function remote_filesize ($url,$referer){ 

		echo "\nurl:$url\n";	
		$cookie_file = '/home/devsquid/public_html/hiphopgoblin.com/temp/limelinxcookies.txt';
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_NOBODY, true);
		curl_setopt($curl, CURLOPT_COOKIEJAR, $cookie_file);
		curl_setopt($curl, CURLOPT_USERAGENT, 'Mozila/5.0 (iPod; U; CPU like Mac OS X; en) AppleWebKit/420.1 (KHTML, like Geckto) Version/3.0 Mobile/3A101a Safari/419.3');
		curl_setopt($curl, CURLOPT_REFERER, $referer);
		curl_setopt($curl, CURLOPT_COOKIEFILE, $cookie_file);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$header = curl_exec($curl);
		$info = curl_getinfo($curl);
		curl_close($curl);

		return $info;
	}

	function noquery($url) {

		$arr = parse_url($url);
		$ret = $arr['scheme'] . '://' . $arr['host'] . $arr['path'];
		return $ret;

	}        

	function limeget($url, $referer = '') { 
		$cookie_file = '/home/devsquid/public_html/hiphopgoblin.com/temp/limelinxcookies.txt';
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt ($ch, CURLOPT_USERAGENT, 'Mozila/5.0 (iPod; U; CPU like Mac OS X; en) AppleWebKit/420.1 (KHTML, like Geckto) Version/3.0 Mobile/3A101a Safari/419.3');
		curl_setopt ($ch, CURLOPT_REFERER, $referer);
		curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
		curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		$result = curl_exec($ch);
		$type = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
		curl_close($ch);
		$ch = null;
		if(strpos($result, 'The file you were looking for could not be found')) {
			$db = new DBHandler();
			$querystring = sprintf("UPDATE songs SET filename='DEAD' WHERE url='%s'",mysql_real_escape_string($this->url));
			$db->query($querystring);
			echo "\nThe link {$url}, database item #{$this->id}, has been marked as dead.";
			$this->error = 'Dead Link';
			$this->limereturn = 'ERROR';
			return 'ERROR'; 
		}
		if($type == 'audio/mpeg' || $type == 'audio/mpeg3') {
			$filename = '/home/devsquid/public_html/hiphopgoblin.com/temp/tracks/' . md5('PREPENDnonsense' . $url) . '.mp3';
			file_put_contents($filename, $result, FILE_BINARY);
			echo "Saved: $filename";
			$this->filename = $filename;
			$this->limereturn = 'OK';
			return 'OK';
		} 
		else {
			if(strpos($result, 'Please wait while your download initializes')) {
				echo "\nWe found a direct download link, and are now getting headers information:";
				preg_match_all('#(http://[^\'"]+dlkey=.+)\' \+ \'#',$result,$regexp,PREG_SET_ORDER);
				echo $regexp[0][1];
				if(!isset($this->skippable[$this->noquery($regexp[0][1])])) { // if we have not figured out headers yet
					sleep(16);	
					$headers = $this->remote_filesize($regexp[0][1], $url);
					$this->skippable[$this->noquery($regexp[0][1])] = true;
					if(!($headers['content_type'] == 'audio/mpeg' || $headers['content_type'] == 'audio/mpeg3')) {
						print_r($headers);
						$this->limereturn = 'ERROR';
						$this->error = 'NOTMP3';
						return 'ERROR';
					}
					if((($headers['download_content_length'] / 1024) / 1024) > 10) {
						$this->error = 'TOOBIG';
						$this->limereturn = 'ERROR';
						return 'ERROR';
					}
				}
				else {
					sleep(16);
				}
				echo "\n**We are now starting the cycle over from the base URL";
				$this->limeget($this->noquery($regexp[0][1]), $url);
				if($this->limereturn != 'ERROR') { $this->limereturn = 'OK'; }
				return 'OK'; 
			}
		
			echo "\n**We are parsing the document for the download link.";
			$dom = new DOMDocument();
			@$dom->loadHTML($result);
			$xpath = new DOMXPath($dom);
			$hrefs = $xpath->evaluate("/html/body//a");
			for ($i = 0; $i < $hrefs->length; $i++) {
				$href = $hrefs->item($i);
				$link = $href->getAttribute('href');
				if(strpos($link,'dlkey=')) {
					break;
				}
			}
			echo "\n Running again: $link";
			$this->limeget($link, $url);
		}
	}



	// returns 'OK' on success & sets $filename accordingly
	// returns 'ERROR' on failure & sets $error accordingly



	function download() {


		if(strpos($this->url, 'limelinx')) {

			echo "\nBeginning a LimeLinx download process.\n";
			$result = $this->limeget($this->url);
			echo "\nRESULT: " . $this->limereturn;
			return $this->limereturn;
		}

		elseif(strpos($this->url, 'zshare')) {
			$this->url = $this->zshareURL();
			if($this->url == 'FAIL') {
				$this->error = 'BADURL';
				return 'ERROR';
			}
			$headers = get_headers($this->url);
			$octets = substr($headers[7], 16);
			$type = substr($headers[2], 14);
			if(ctype_digit($octets)) {
				$megabytes = ($octets / 1024) / 1024;
				if($megabytes > 10) {
					$this->error = 'TOOBIG';
					return 'ERROR';
				}
				else {
					echo "\nFile is $megabytes MB in size. Download of {$this->url} beginning.\n";
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, $this->url); 
					curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 5);
					curl_setopt ($ch, CURLOPT_USERAGENT, 'Mozila/5.0 (iPod; U; CPU like Mac OS X; en) AppleWebKit/420.1 (KHTML, like Geckto) Version/3.0 Mobile/3A101a Safari/419.3');
					curl_setopt ($ch, CURLOPT_COOKIEFILE, '/home/devsquid/public_html/hiphopgoblin.com/temp/zsharecookies.txt');
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
					$result = curl_exec($ch);
					curl_close($ch);
					$ch = null;
					if($type == 'audio/mpeg' || $type == 'audio/mpeg3' || $type == 'application/octet-stream') {
						$filename = '/home/devsquid/public_html/hiphopgoblin.com/temp/tracks/' . md5('PREPENDnonsense' . $this->url) . '.mp3';
						file_put_contents($filename, $result, FILE_BINARY);
						$result = null;
						$this->filename = $filename;
						return 'OK';
					}
					else {
						$this->error = 'NOTMP3';	
						echo "\n$type";
						return 'ERROR';
					}
				}
			}
			else {
				$this->error = 'Unable to determine file size: not downlading';
				print_r($headers);
				return 'ERROR';
			}

		}
		elseif(substr($this->url, -4, 4) == '.mp3') {
				
			$headers = $this->remote_filesize($this->url, '');
			if((($headers['download_content_length'] / 1024) / 1024) > 10) {
				$this->error = 'TOOBIG';
				return 'ERROR';
			}
			else {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $this->url);
	curl_setopt ($ch, CURLOPT_USERAGENT, 'Mozila/5.0 (iPod; U; CPU like Mac OS X; en) AppleWebKit/420.1 (KHTML, like Geckto) Version/3.0 Mobile/3A101a Safari/419.3');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	$result = curl_exec($ch); 
	$type = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
	curl_close($ch);
	$ch = null;
	if($type == 'audio/mpeg' || $type == 'audio/mpeg3') {
		$filename = '/home/devsquid/public_html/hiphopgoblin.com/temp/tracks/' . md5('PREPENDnonsense' . $url) . '.mp3';
		file_put_contents($filename, $result, FILE_BINARY);
		echo "Saved: $filename";
		$this->filename = $filename;
		return 'OK';
	} 
	else {
		return 'ERROR';
	}
}

			
		}
		else {
			echo "\n$this->url\nWe tried to download a file that is neither zshare nor limelinx.\n";
			return 'SKIP';
		}


	}



}

?>
