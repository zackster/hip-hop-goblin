<?php

mysql_connect('localhost','devsquid_hhg','SAVAGERY99');
mysql_select_db('devsquid_hhg');

$topquery = 'select songs.artist, songs.title, songs.id, songs.date_added, songs.referral from songs where songs.filename !=\'DEAD\' and songs.filename != \'\' and songs.artist != \'\' and songs.title != \'\''; 


if($_GET['hnhh'] == 'false') {
	$topquery .= ' and songs.referral not like \'hotnewhiphop\'';
}
if($_GET['blogs'] == 'false') {
	$topquery .= ' and songs.referral not like \'http://%\'';
}
if($_GET['twitter'] == 'false') {
	$topquery .= ' and songs.referral not like \'twitter%\'';
}


$part2 = ' order by date_added desc limit 100';
$topresult = mysql_query($topquery . $part2);
echo mysql_error();

?>
<script type="text/javascript">
var getvars = new Array();
getvars['hnhh'] = <?php echo ($_GET['hnhh']==='false'?'false':'true'); ?>;
getvars['blogs'] = <?php echo ($_GET['blogs']==='false'?'false':'true'); ?>;
getvars['twitter'] = <?php echo ($_GET['twitter']==='false'?'false':'true'); ?>;
$("#hnhh").click(function() {
	getvars['hnhh'] = getvars['hnhh'] ? false : true;
	updateIt();
}); 
$("#blogs").click(function() {
	getvars['blogs'] = getvars['blogs'] ? false: true;
	updateIt();
});
$("#twitter").click(function() {
	console.log(getvars['twitter']);
	getvars['twitter'] = getvars['twitter'] ? false : true;
	console.log(getvars['twitter']);
	updateIt();
});
function updateIt() {
	datastring = '';
	for(param in getvars) {
		datastring += param + '=' + getvars[param] + '&';
	}
	$.get("new.php", datastring, function(data) {
       		$("#bigbox").html(data);
	});

}
</script>
<div id="layoutcontainer">
<div id="recentlyreleased" style="float:left; margin-left:30px;margin-top:15px"><h2>Recently released songs...</h2></div>

<div id="checkboxes" style="float:right;border:2px solid #ADADAD;margin-right:20px;margin-top:15px">
show songs from..<br />
<input type="checkbox" name="hnhh" id="hnhh" <?php if($_GET['hnhh']=='false') {?><?php }else{?>checked=true<?php }?>>hotnewhiphop.com<br />
<input type="checkbox" name="blogs" id="blogs" <?php if($_GET['blogs']=='false') {?><?php }else{?>checked=true<?php }?>> 50+ best rap blogs<br />
<input type="checkbox" name="twitter" id="twitter" <?php if($_GET['twitter']=='false') {?><?php }else{?>checked=true<?php }?>> artist submitted songs from twitter 
</div>
</div>
<br /><br /><br /><br /><br /><br /><table>
<?php
$i=1;
while($row = mysql_fetch_assoc($topresult)) {
list($yr,$mon,$day) = split('-',$row['date_added']);
$display_date = date('l F j, Y', mktime(0,0,0,$mon,$day,$yr)); 
?>
<tr><td><?php echo $i;?>.</td><td><a href="#<?php echo($row['id']);?>" onclick="javascript:cueSong(<?php echo($row['id']);?>)"><?php echo($row['artist']);?> - <?php echo($row['title']);?></a><br />on <?php echo $display_date;?> from <?php echo $row['referral'];?></td></tr>
<?php
$i++;
}
?>
</table>
