<?php

$aws_secret = 'py8RYZrvMt9EIMrHzBs2oBoYmMiEG9zlVorL8jHE';
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
$str = "PUT\n\naudio/mpeg\nThu, 03 Sep 2009 02:20:31 +0000\nx-amz-acl:public-read\n/hiphopgoblin/3aa04fdf241068d3390dec896f106e5b.mp3";
echo amazon_hmac($str);
?>
