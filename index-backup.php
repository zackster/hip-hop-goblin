<?php

function encrypt($string, $key) {
	$result = '';
	for($i=0; $i<strlen($string); $i++) {
		$char = substr($string, $i, 1);
		$keychar = substr($key, ($i % strlen($key))-1, 1);
		$char = chr(ord($char)+ord($keychar));
		$result.=$char;
	}

	return base64_encode($result);
}

function decrypt($string, $key) {
	$result = '';
	$string = base64_decode($string);

	for($i=0; $i<strlen($string); $i++) {
		$char = substr($string, $i, 1);
		$keychar = substr($key, ($i % strlen($key))-1, 1);
		$char = chr(ord($char)-ord($keychar));
		$result.=$char;
	}

	return $result;
}

mysql_connect('localhost', 'devsquid_hhg', 'SAVAGERY99');
mysql_select_db('devsquid_hhg');

$userid_encrpyted = $_COOKIE['userid'];
if(isset($_COOKIE['userid'])) {
	$userid_encrypted = $_COOKIE['userid'];
	$userid = decrypt($userid_encrypted, 'shane88botwin');
	$userid = substr($userid, 0, -10); // eliminating 'backgammon'
	$user_result = mysql_query(sprintf("select id from users where id=%d", mysql_real_escape_string($userid)));
	if(mysql_num_rows($user_result) == 1) {
			// we are good to go!
	}
	else {
		// something's fucked up; fuck that. paece.

        
	        $ip = $_SERVER['REMOTE_ADDR'];
        	mysql_query(sprintf("INSERT INTO users (remote_addr,date_added) VALUES ('%s',now())",mysql_real_escape_string($ip)));
        	$userid = mysql_insert_id();
        	$userid_encrypted = encrypt($userid . 'backgammon', 'shane88botwin');
        	setcookie('userid', $userid_encrypted, time()+60*60*24*3365);
 
	

	}
}
else {
	
	$ip = $_SERVER['REMOTE_ADDR'];
	mysql_query(sprintf("INSERT INTO users (remote_addr,date_added) VALUES ('%s',now())",mysql_real_escape_string($ip)));
	$userid = mysql_insert_id();
	$userid_encrypted = encrypt($userid . 'backgammon', 'shane88botwin');
	setcookie('userid', $userid_encrypted, time()+60*60*24*3365);
	
}

if(isset($_GET['sid'])) {
	$song = $_GET['sid'];
}

?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<style type="text/css">
.footer-right {
	font-family:arial;
	color:#A9A9A9;
	position:relative;
	text-align:right;
}
.footnotes {
        font-family:arial;
        color:#000;
        position:relative;
        text-align:right;
}

a.footer-right {
	color:#A9A9A9;
	text-decoration:none;
}
.footer-left {
        font-family:arial;
        color:#000;
        position:relative;
        text-align:left;
}
html .fb_share_link { 
	padding:2px 0 0 20px; 
	height:16px; 
	background:url(http://b.static.ak.fbcdn.net/rsrc.php/z39E0/hash/ya8q506x.gif) no-repeat top left; 
}
</style>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>HIPHOPGOBLIN.COM DISCOVER NEW HIP HOP MIXTAPES</title>
<script language="javascript">AC_FL_RunContent = 0;</script>
<script src="AC_RunActiveContent.js" language="javascript"></script>
<script type="text/javascript">
var artist;
var title;
var songid = '';

function fbs_click() {
	u= 'http://www.hiphopgoblin.com/?sid=' + songid;
	t = artist + ' - ' + title + ' | HIPHOPGOBLIN.COM DISCOVER NEW HIP HOP MIXTAPES';
	window.open('http://www.facebook.com/sharer.php?u='+encodeURIComponent(u)+'&t='+encodeURIComponent(t),'sharer','toolbar=0,status=0,width=626,height=436');
	return false;
}

function getTextFromFlash(artistp,titlep,songidp) {
	artist = artistp;
	title = titlep;
	songid = songidp;
}

</script>
</head>
<body bgcolor="#000000">
<script language="javascript">
	if (AC_FL_RunContent == 0) {
		alert("This page requires AC_RunActiveContent.js.");
	} else {
		AC_FL_RunContent(
			'FlashVars', '<?php if(isset($song)) { echo 'sid=' . $song . '&';} ?>userid=<?php echo $userid_encrypted;?>',
			'codebase', 'http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0',
			'width', '100%',
			'height', '50%',
			'src', 'songplayer',
			'quality', 'high',
			'pluginspage', 'http://www.macromedia.com/go/getflashplayer',
			'align', 'middle',
			'play', 'true',
			'loop', 'true',
			'scale', 'showall',
			'wmode', 'window',
			'devicefont', 'false',
			'id', 'songplayer',
			'bgcolor', '#000000',
			'name', 'songplayer',
			'menu', 'true',
			'allowFullScreen', 'false',
			'allowScriptAccess','sameDomain',
			'movie', 'songplayer',
			'salign', ''
			); //end AC code
	}
</script>
<noscript>
	<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0" width="100%" height="100%" id="songplayer" align="middle">
	<param name="allowScriptAccess" value="sameDomain" />
	<param name="allowFullScreen" value="false" />
	<param name="movie" value="songplayer.swf" />
	<param name="quality" value="high" />
	<param name="bgcolor" value="#cccccc" />	
	<param name="FlashVars" value="userid=<?php echo $userid_encrypted;?>" />
	<embed src="songplayer.swf" FlashVars="userid=<?php echo $userid_encrypted;?>" quality="high" bgcolor="#cccccc" width="100%" height="100%" name="songplayer" align="middle" allowScriptAccess="sameDomain" allowFullScreen="false" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />
	</object>
</noscript>

<div class="footer-left" id="footer-left">
</div>

<div class="footnotes" id="footnotes">
<u>Don't worry! Clicking any of these links will not stop the current song</u><br /><br />
<a href="http://www.facebook.com/share.php?u=<url>" onclick="return fbs_click()" target="_blank" class="fb_share_link">Share this Song on Facebook</a><br />
	<a href="top.php" target="_blank">hottest songs</a><br />
	<a href="history.php" target="_blank">songs you have been listening to</a><br />
	<a href="blog/" target="_blank">blog</a><br />
	<a href="static/zup.php" target="_blank">artists - upload tracks</a><br />
	<a href="static/copyright.php" target="_blank">copyrighted content?</a>
</div>

<div class="footer-right" id="footer-right">
	info<b>@</b>hiphopgoblin.com<br />
	&copy; 2009 <a href="about.html" class="footer-right">HIPHOPGOBLIN.COM</a>
</div>
<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
try {
var pageTracker = _gat._getTracker("UA-10356912-1");
pageTracker._trackPageview();
} catch(err) {}</script>
</body>
</html>
