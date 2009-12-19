<?php

mysql_connect('localhost', 'devsquid_hhg', 'SAVAGERY99');
mysql_select_db('devsquid_hhg');

// Ranking implemented using Bayesian Average
// c.f. http://www.thebroth.com/blog/118/bayesian-rating for mathematical explanation
// $ranking = ( ($avg_num_votes * $avg_rating) + ($this_num_votes * $this_rating) ) / ($avg_num_votes + $this_num_votes);

// getting $avg_num_votes; // select count(*)/count(DISTINCT product_id) from vogoo_ratings

$anv_res = mysql_query('SELECT count(*)/count(DISTINCT product_id) from vogoo_ratings');
$anv_row = mysql_fetch_row($anv_res);
$avg_num_votes = $anv_row[0];

echo "AVG_NUM_VOTES: {$avg_num_votes}\n";

// getting $avg_rating; // select avg(rating), product_id from vogoo_ratings group by product_id
// this_num_votes;
// $this_rating


$products_arr = array();
$ar_res = mysql_query('SELECT avg(rating) AS rating, count(rating) AS votes, product_id FROM vogoo_ratings GROUP BY product_id');
while($ar_row = mysql_fetch_assoc($ar_res)) {
	$products_arr[$ar_row['product_id']]['rating'] = $ar_row['rating'];
	$products_arr[$ar_row['product_id']]['votes']  = $ar_row['votes'];
}
$count = 0;
foreach($products_arr as $product) {
	$total_rating += $product['rating'];
	$count++;
}
$avg_rating = $total_rating / $count;
echo "AVG_RATING: {$avg_rating}\n";


$ratings = array();
foreach($products_arr as $pid => $product) {
	$true_rating = ( ($avg_num_votes * $avg_rating) + ($product['votes'] * $product['rating']) ) / ($avg_num_votes + $product['votes']);
	$ratings[$pid] = $true_rating; 
}
arsort($ratings);
$top50 = array_slice($ratings, 0, 50, TRUE);
$rankings = array();
$top_products = array_keys($top50);
for($i=1;$i<=50;$i++) {
	$pid = array_shift($top_products);
	$song_res = mysql_query(sprintf("SELECT artist, title FROM songs WHERE id=%d", $pid));
	$song_row = mysql_fetch_assoc($song_res);
	mysql_query(sprintf("INSERT INTO top50 (rank,product_id,artist,title) VALUES (%d,%d,'%s','%s') ON DUPLICATE KEY UPDATE product_id=%d,artist='%s',title='%s'", $i, $pid, mysql_real_escape_string($song_row['artist']), mysql_real_escape_string($song_row['title']), $pid, mysql_real_escape_string($song_row['artist']), mysql_real_escape_string($song_row['title'])));
	echo mysql_error();
}	
?>

