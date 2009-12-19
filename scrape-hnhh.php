<?php

$target_url = "http://www.hotnewhiphop.com/";
$userAgent = 'Googlebot/2.1 (http://www.googlebot.com/bot.html)';


function store_link($url) {

	mysql_connect('localhost', 'devsquid_hhg', 'SAVAGERY99');
	mysql_select_db('devsquid_hhg');

	$query = sprintf("INSERT INTO songs (referral,url,date_added) VALUES ('hotnewhiphop', '%s', now())", mysql_real_escape_string($url));
	echo $query, "\n";
	//mysql_query($query);

}



// make the cURL request to $target_url
$ch = curl_init();
curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
curl_setopt($ch, CURLOPT_URL,$target_url);
curl_setopt($ch, CURLOPT_FAILONERROR, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_AUTOREFERER, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
$html= curl_exec($ch);
if (!$html) {
	echo "<br />cURL error number:" .curl_errno($ch);
	echo "<br />cURL error:" . curl_error($ch);
	exit;
}

// parse the html into a DOMDocument
$dom = new DOMDocument();
@$dom->loadHTML($html);

// grab all the on the page
$xpath = new DOMXPath($dom);
$hrefs = $xpath->evaluate("/html/body//a");

for ($i = 0; $i < $hrefs->length; $i++) {
	$href = $hrefs->item($i);
	$url = $href->getAttribute('href');
	if(strpos($url,'limelinx')) {
		echo "\nLink stored: $url";
		store_link($url);
	}
}
?>
