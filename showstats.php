<?php

mysql_connect('localhost', 'root', 'root');
mysql_select_db('hiphopgoblin');

/*

Top rated songs

select songs.artist,songs.title,avg(vogoo_ratings.rating) from vogoo_ratings left join songs on songs.id=vogoo_ratings.product_id group by songs.id order by avg(rating) desc limit 20;

*/

$res = mysql_query("select distinct artist from songs ORDER BY artist");
echo '<table border =1>';
while($row = mysql_fetch_assoc($res)) {
	echo '<tr><td>' . $row['artist'] . '</td></tr>';
}
echo '</table>';
?>