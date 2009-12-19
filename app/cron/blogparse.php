<?php

$include_path = '/home/devsquid/public_html/hiphopgoblin.com';
include($include_path . '/classes/' . 'DBHandler.class.php');
include($include_path . '/classes/' . 'Song.class.php');

$do = new DBHandler();
$do->query("SELECT url FROM blogs");
foreach($do->result as $result) {

	$blog = $result['url'];
                $curl = curl_init();
                curl_setopt($curl, CURLOPT_URL, $blog);
                curl_setopt($curl, CURLOPT_USERAGENT, 'Mozila/5.0 (iPod; U; CPU like Mac OS X; en) AppleWebKit/420.1 (KHTML, like Geckto) Version/3.0 Mobile/3A101a Safari/419.3');
                curl_setopt($curl, CURLOPT_REFERER, 'http://www.google.com');
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                $data = curl_exec($curl);
                curl_close($curl);


                        $dom = new DOMDocument();
                        @$dom->loadHTML($data);
                        $xpath = new DOMXPath($dom);
                        $hrefs = $xpath->evaluate("/html/body//a");
                        for ($i = 0; $i < $hrefs->length; $i++) {
                                $href = $hrefs->item($i);
                                $link = $href->getAttribute('href');
				if(strpos($link,'share') || strpos($link, 'limelinx') || strpos($link, 'mp3')) {
				        $song = new Song($link, $blog);
				}
                        }


}

?>
