<?php

	include('../classes/DBHandler.class.php');

                $db = new DBHandler();
                $querystring = sprintf("SELECT * FROM songs LIMIT 1"); 
                $db->query($querystring);
		print_r($db->result);

?>
