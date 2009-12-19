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
<html>
<head>
	<title>HipHopGoblin.com - Hear Songs You'll Love, Before Your Friends Hear Them</title>
	<style type="text/css">
	hr {
		width: 80%;
		color: #999999;
		height: 2px;
	}
	body {
		font-family: Arial Narrow,sans-serif;
	}
	h5 {
		color: #FF0000;
	}
	#options {
		color: #ddd;
	//	border-top: 1px solid #000;
		width: 600px;
	}
	#options h1 {
		text-align: center;
		font-size: 10px;
		left: 233px;
		top: 313px;
		text-decoration: none;
		color: #999;
		position: absolute;
	}
	#options .box {
		cursor: pointer;
		width: 100px;
		height: 20px;
		color: #000;
		position: relative;
		text-decoration: none;
		left: 0px;
		background: #fff;
		border: 1px solid #000;
		padding-top: 4px;
		text-align: center;
		margin: 0 auto;
	}
	#options .selectedbox {
		width: 100px;
		height: 20px;
		color: #999;
		border: 2px solid #2a3597;
		position: relative;
		text-decoration: none;
		background: #2a3597;
		left: 0px;
		padding-top: 4px;
		text-align: center;
		margin: 0 auto;
	}
	#bigbox {
		background: #f5f5f5;
		overflow: scroll;
		width: 600px;
		border: 1px solid #000;
		height: 50%;
		color: #000;
		text-align: center;
		margin: 0 auto;
		padding: 5px;
	}
	#container {
		width: 600px;
		background: #FFF;
		margin: 0 auto;
	}
	#banner {
		font-size: 68px;
		font-family: Arial Narrow;	
		font-weight: bold;
		color:#2A3597;
		text-align: center;
		padding-top: 10px;
	}
	#songplayer {
		top: 100px;
		left: 200px;
		text-align: center;
		width: 552px;
		height: 70px;
		margin: 0px auto;
	}

	#checkboxes {
		float: right;

	}

html .fb_share_link {
        padding:2px 0 0 20px;
        height:16px;
        background:url(http://b.static.ak.fbcdn.net/rsrc.php/z39E0/hash/ya8q506x.gif) no-repeat top left;
}

	</style>
        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>
	<script src="jquery.swfobject.1-0-7.js" type="text/javascript" charset="utf-8"></script>
	<script src="AC_RunActiveContent.js" language="javascript"></script>
	<script type="text/javascript">
	var artist;
	var title;
	var songid='';


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


	function loadMovie() {
		
		AC_FL_RunContent(
			'codebase', 'http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,0,0',
			'flashvars',  '<?php if(isset($song)) { echo 'sid=' . $song . '&';} ?>userid=<?php echo $userid_encrypted;?>',
			'width', '100%',
			'height', '100%',
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
			'bgcolor', '#FFFFFF',
			'name', 'songplayer',
			'menu', 'true',
			'allowFullScreen', 'false',
			'allowScriptAccess','always',
			'movie', 'songplayer',
			'salign', ''
			); //end AC code

	}
	function thisMovie(movieName) {
	    if (navigator.appName.indexOf("Microsoft") != -1) {
	        return window[movieName]
	    }
	    else {
	        return document[movieName]
	    }
	}



function newest() {

                                $.get("new.php", function(data) {
                                        $("#bigbox").html(data);
                                });


}



	  $(document).ready(function() {


			/*************************
				NO.

 			$('#songplayer').flash(   {   
				swf: 'songplayer.swf',   
				params: {   play: false, AllowScriptAccess: 'always' },   
				flashvars: {   <?php if(isset($song)) { echo 'sid:' . $song . ',';} ?> userid:'<?php echo $userid_encrypted;?>'},
				height: '100%',   
				width: '100%' 
			}   );


			*************************/


			$(".box").hover(function () {
				$(this).css("background", "#999");
			}, function() {
				$(this).css("background", "#ffffff");
			});


                        $("#top50").click(function() {
                                $.get("top.php", function(data) {
                                        $("#bigbox").html(data);
                                });
                        });
		
	
			$("#history").click(function() {
				$.get("history.php", function(data) {
					$("#bigbox").html(data);
				});
			});
			
			
			$("#newreleases").click(function() {
				$.get("new.php", function(data) {
					$("#bigbox").html(data);
				});
			});
	});

        function cueSong(songid) {
		thisMovie("songplayer").callCueSong(songid);
        }       


	</script>

	</head>
	<body bgcolor="#FFFFFF" onload="loadMovie()">

		<h5>new songs added every day!! click on "New Releases" <br /><a href="#" onclick="javascript:newest();">most recent update</a> <?php echo date("F j Y");?></h5>
		<div id="banner" class="banner">HIPHOPGOBLIN.COM</div>
		<div id="songplayer" class="songplayer"></div>
		<div id="container" class="container">
			<div id="options">
				<table>
					<tr>
						<td><div id="newreleases" class="box">New Releases</div></td>
						<td><div id="autoplay" class="selectedbox">AutoPlay</div></td>
						<td><div id="top50" class="box">Top 50</div></td>
						<td><div id="history" class="box">Recently Played</div></td>
						<!-- <td><div id="moreinfo" class="box">More Info</div></td> -->
						<td><div id="share" class="box"><a href="http://www.facebook.com/share.php?u=<url>" onclick="return fbs_click()" target="_blank" class="fb_share_link">Share</a></div></td> 
					</tr>
				</table>
			</div>
			<div id="bigbox">
				HipHopGoblin.com is undergoing a site design with new features. We will provide HOSTING FOR RAPPERS, A LIST & PLAY of all the latest songs released on the streets&scene.

<br /><br />
				Comments? Want to help out with the site? please send an email to info@hiphopgoblin.com!	
			</div>
			<div id="footer">
				<!-- <a href="http://www.hiphopgoblin.com/blog/" target="_blank">Blog</a> ->
				<a href="http://www.hiphopgoblin.com" target="_blank">Copyright Info</a>
				<a href="http://www.twitter.com/hiphopgoblin" target="_blank">You should follow us on Twitter</a>
				<br /><br />
				<form action="emailsubmit.php" method="POST" target="_blank">
				Give us your email address to be updated on strictly the most important news you wanna hear..
				<br /><input name="email" id="email" type="text">  <input name="submit" id="submit" type="submit" value="click to give us your email"></form> 
			</div>
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
