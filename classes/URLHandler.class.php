<?php

class URLHandler {
	
	private $url;
	
	function __construct($url) {
		$options = array(
	        CURLOPT_RETURNTRANSFER => true,     // return web page
	        CURLOPT_HEADER         => false,    // return headers
	        CURLOPT_FOLLOWLOCATION => true,     // follow redirects
	        CURLOPT_ENCODING       => "",       // handle all encodings
	        CURLOPT_USERAGENT      => "Googlebot/2.1 (http://www.googlebot.com/bot.html)", // who am i
	        CURLOPT_AUTOREFERER    => true,     // set referer on redirect
	        CURLOPT_NOBODY         => true,     // don't waste time viewing the body
	        CURLOPT_CONNECTTIMEOUT => 5,      // timeout on connect
	        CURLOPT_TIMEOUT        => 120,      // timeout on response
	        CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
	    );

	    $ch      = curl_init( $url );
	    curl_setopt_array( $ch, $options );
	    $content = curl_exec( $ch );
	    $err     = curl_errno( $ch );
	    $errmsg  = curl_error( $ch );
	    $header  = curl_getinfo( $ch );
	    curl_close( $ch );

	    $this->url = $header["url"];
	    return;
	}

	function __get($var) {
		if($var == 'url') {
			return $this->url;
		}
	}

}

?>
