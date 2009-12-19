<?php

mysql_connect('localhost','devsquid_hhg','SAVAGERY99');
mysql_select_db('devsquid_hhg');

$topquery = 'select songs.artist, songs.title, id, avg(rating) from vogoo_ratings,songs where songs.id=vogoo_ratings.product_id group by product_id order by avg(rating) desc, listen_count desc limit 50';
$topresult = mysql_query($topquery);

?>
<h3>Top 50 songs on the Goblins deck:</h3><br /><table>
<?php
$i=1;
while($row = mysql_fetch_row($topresult)) {
?>
<tr><td><?php echo $i;?>.</td><td><a href="#" onclick="javascript:cueSong(<?php echo($row[2]);?>);"><?php echo($row[0]);?> - <?php echo($row[1]);?></a></td></tr>
<?php
$i++;
}
?>
</table>
