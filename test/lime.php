<?php


		$url = 'http://limelinx.com/files/bce0920c22a5f195aad9b9bfc4deec17';
	limeget($url);	
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
                                                echo "\nThe link {$url}, database item #{$id}, has been marked as dead.";
                                                $this->error = 'Dead Link';
                                                return 'ERROR';
                                        }


                                if($type == 'audio/mpeg' || $type == 'audio/mpeg3') {
                                        $filename = '../temp/tracks/' . md5('PREPENDnonsense' . $url) . '.mp3';
                                        file_put_contents($filename, $result, FILE_BINARY);
					echo "Saved: $filename";
					exit;
                                } else {
			

				if(strpos($result, 'Please wait while your download initializes')) {
					echo "\nlink on this page:";
					preg_match_all('#(http://[^\'"]+dlkey=.+)\' \+ \'#',$result,$regexp,PREG_SET_ORDER);
					echo $regexp[0][1];
					sleep(16);
					limeget($regexp[0][1], $url);
				}


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
				limeget($link);
				}
}


?>
