<?php

require_once 'Crypt/HMAC.php';
require_once 'HTTP/Request.php';


function uploadFile($path_to_file, $store_file_as, $bucket, $debugmode = false) {
	


	if($debugmode) {
		ob_start();
	}

	$S3_URL = "http://s3.amazonaws.com/";


	$filePath = $path_to_file;
	$contentType = 'audio/mpeg';
	$keyId = 'AKIAJ2KRB5UNOCE64OUA';
	$secretKey = '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';
	$key = $store_file_as;
	$resource = $bucket . "/" . $key;
	$acl = "public-read";
	$verb = "PUT";
	

    $httpDate = gmdate("D, d M Y H:i:s T");
    $stringToSign = "$verb\n\n$contentType\n$httpDate\nx-amz-acl:$acl\n/$resource";
    $hasher =& new Crypt_HMAC($secretKey, "sha1");
    $signature = hex2b64($hasher->hash($stringToSign));

    $req =& new HTTP_Request($S3_URL . $resource);
    $req->setMethod($verb);
    $req->addHeader("content-type", $contentType);
    $req->addHeader("Date", $httpDate);
    $req->addHeader("x-amz-acl", $acl);
    $req->addHeader("Authorization", "AWS " . $keyId . ":" . $signature);
    $req->setBody(file_get_contents($filePath));   
    $req->sendRequest();

    $ct = $req->getResponseHeader("content-type");
   
	if($debugmode) {
		
	
		if ($ct == "application/xml") $ct = "text/xml";
		header("content-type: $ct");
		ob_end_flush();

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

?>
