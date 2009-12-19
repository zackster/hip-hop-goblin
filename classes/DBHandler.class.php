<?php

class DBHandler {
	
	private $link;
	private $numRows;
	private $result;
	private $resultObj;
	private $insertID;
	
	function __construct() {
		$this->link = mysql_connect('localhost', 'devsquid_hhg', 'SAVAGERY99', true);
		mysql_select_db('devsquid_hhg', $this->link);
		return;
	}

	function __get($var) {
		if($var == 'result') {
			return $this->result;
		}
		elseif($var == 'error') {
			return $this->error;
		}
		elseif($var == 'numRows') {
			return $this->numRows;
		}
		elseif($var == 'insertID') {
			return $this->insertID;
		}
		

	}	

	function query($sql) {
		$this->resultObj = mysql_query($sql,$this->link);
		$this->error = mysql_error($this->link);
		if(strlen($this->error)) {
			echo "\nError on query (" . $sql . "): " . $this->error . "\n";
		}

		if(stripos($sql, 'select') !== FALSE) {
			$this->numRows = mysql_num_rows($this->resultObj);
			if($this->numRows == 1) {
				$this->result = mysql_fetch_assoc($this->resultObj);
			}
			elseif($this->numRows > 1) {
				$this->result = array();
				while($assoc = mysql_fetch_assoc($this->resultObj)) {
					array_push($this->result, $assoc);
				}
			}
		}
		elseif(stripos($sql, 'insert') !== FALSE) {
			$this->insertID = mysql_insert_id();
		}
		else {
			// we did an UPDATE.
		}
		return;
	}
	
}

?>
