<?php

        $aws_secret = 'py8RYZrvMt9EIMrHzBs2oBoYmMiEG9zlVorL8jHE'; //'idGmSmzOUgQ7jbEgdcTAUdOKKoYfSEl8WjYbfkE1';


function uploadFile($source_file, $aws_object, $aws_bucket, $debugmode = false) {


	$aws_key = 'AKIAJXWUH5BVV74GEPGQ'; //'AKIAJ2KRB5UNOCE64OUA';
	$aws_secret = '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'; //'idGmSmzOUgQ7jbEgdcTAUdOKKoYfSEl8WjYbfkE1';



$file_type = "audio/mpeg";  // or other file type like "image/jpeg" for JPEG image, 
// or "binary/octet-stream" for binary file


if (strlen($aws_secret) != 40) die("$aws_secret should be exactly 40 bytes long");
$file_data = file_get_contents($source_file);
if ($file_data == false) die("Failed to read file ".$source_file);


// opening HTTP connection to Amazon S3
$fp = fsockopen("s3.amazonaws.com", 80, $errno, $errstr, 30);
if (!$fp) {
	die("$errstr ($errno)\n");
}


// Creating or updating bucket 

$dt = gmdate('r'); // GMT based timestamp 

// preparing String to Sign    (see AWS S3 Developer Guide)
$string2sign = "PUT\n\n{$file_type}\n{$dt}\nx-amz-acl:public-read\n/{$aws_bucket}/{$aws_object}";
$hmac_encoded = amazon_hmac($string2sign);

$string2sign2 = str_replace("\n", "\\n", $string2sign);

// preparing HTTP PUT query
$query = "PUT /{$aws_bucket} HTTP/1.1
	Host: s3.amazonaws.com
	Connection: keep-alive
	Date: $dt
	Authorization: AWS {$aws_key}:".amazon_hmac($string2sign)."\n\n";

$resp = sendREST($fp, $query);
if (strpos($resp, '<Error>') !== false)
	die($resp);


// Uploading object
$file_length = strlen($file_data); // for Content-Length HTTP field 

$dt = gmdate('r'); // GMT based timestamp
// preparing String to Sign    (see AWS S3 Developer Guide)

// preparing HTTP PUT query
$query = "PUT /{$aws_bucket}/{$aws_object} HTTP/1.1\n" . "Host: s3.amazonaws.com\n" . "x-amz-acl: public-read\n" . "Connection: keep-alive\n" . "Content-Type: {$file_type}\n" . "Content-Length: {$file_length}\n" . "Date: $dt\n" . "Authorization: AWS {$aws_key}:" . amazon_hmac($string2sign) . "\n\n";
$query .= $file_data;

$resp = sendREST($fp, $query, false);
if (strpos($resp, '<Error>') !== false) {
	$code = 'FAIL';
}	
elseif (strpos($resp, 'HTTP/1.1 200 OK') !== false) {
	$code = '200';
}
// done

fclose($fp);
return array("response_code" => $code, "url" => "http://{$aws_bucket}.s3.amazonaws.com/{$aws_object}");


}

// Sending HTTP query and receiving, with trivial keep-alive support
function sendREST($fp, $q, $debug = false)
{
	//if ($debug) echo "\nQUERY<<{$q}>>\n";

	fwrite($fp, $q);
	$r = '';
	$check_header = true;
	while (!feof($fp)) {
		$tr = fgets($fp, 256);
		if ($debug) echo "\nRESPONSE<<{$tr}>>"; 
		$r .= $tr;

		if (($check_header)&&(strpos($r, "\r\n\r\n") !== false))
		{
			// if content-length == 0, return query result
			if (strpos($r, 'Content-Length: 0') !== false)
				return $r;
		}

		// Keep-alive responses does not return EOF
		// they end with \r\n0\r\n\r\n string
		if (substr($r, -7) == "\r\n0\r\n\r\n")
			return $r;
	}
	return $r;
}

function amazon_hmac($stringToSign) 
{
	// helper function binsha1 for amazon_hmac (returns binary value of sha1 hash)
	if (!function_exists('binsha1'))
	{ 
		if (version_compare(phpversion(), "5.0.0", ">=")) { 
			function binsha1($d) { return sha1($d, true); }
		} else { 
			function binsha1($d) { return pack('H*', sha1($d)); }
		}
	}

	global $aws_secret;

	if (strlen($aws_secret) == 40)
		$aws_secret = $aws_secret.str_repeat(chr(0), 24);

	$ipad = str_repeat(chr(0x36), 64);
	$opad = str_repeat(chr(0x5c), 64);

	$hmac = binsha1(($aws_secret^$opad).binsha1(($aws_secret^$ipad).$stringToSign));
	return base64_encode($hmac);
}







/*

function uploadFile($path_to_file, $store_file_as, $bucket, $debugmode = false) {

	$S3_URL = "http://s3.amazonaws.com/";
	$filePath = $path_to_file;
	$contentType = 'audio/mpeg';
	$keyId = 'AKIAJXWUH5BVV74GEPGQ'; //'AKIAJ2KRB5UNOCE64OUA';
	$secretKey = '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'; //'idGmSmzOUgQ7jbEgdcTAUdOKKoYfSEl8WjYbfkE1';
	$key = $store_file_as;
	$resource = $bucket . "/" . $key;
	$acl = "public-read";
	$verb = "PUT";
	$httpDate = gmdate("D, d M Y H:i:s T");
	$stringToSign = "PUT\n\naudio/mpeg\n$httpDate\nx-amz-acl:$acl\n/$resource";
	$hasher =& new Crypt_HMAC($secretKey, "sha1");
	echo "\n\nSTRINGTOSIGN:" . $stringToSign . "[end]";
	$signature3 = hex2b64($hasher->hash($stringToSign));
	$signature = urlencode(hex2b64($hasher->hash($stringToSign)));
	$signature2 = urlencode(base64_encode($hasher->hash($stringToSign)));

	echo "\n\nSIGNATURE3:" . $signature3. "[end]";
	echo "\n\nSIGNATURE:" . $signature. "[end]";
	echo "\n\nSIGNATURE2:" . $signature2. "[end]";



	$req =& new HTTP_Request($S3_URL . $resource);
	$req->setMethod('PUT');
	$req->addHeader("content-type", $contentType);
	$req->addHeader("Date", $httpDate);
	$req->addHeader("x-amz-acl", $acl);
	$req->addHeader("Authorization", "AWS " . $keyId . ":" . $signature);
	$req->setBody(file_get_contents($filePath));   
	$req->sendRequest();

	$ct = $req->getResponseHeader("content-type");

	if($debugmode) {



		if ($req->getResponseCode() >= 300) {
			print $req->getResponseBody();
			return;
		}

		if ($verb != "GET") {
			print "$resource ${verb}ed successfully.";
			return;
		}

		print $req->getResponseBody();

	}

	$returncode = $req->getResponseCode();
	unset($req);	
	return $returncode;

}



function hex2b64($str) {
	$raw = '';
	for ($i=0; $i < strlen($str); $i+=2) {
		$raw .= chr(hexdec(substr($str, $i, 2)));
	}
	return base64_encode($raw);
}

*/


?>
