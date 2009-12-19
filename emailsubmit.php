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


$email = $_POST['email'];
mysql_query(sprintf("INSERT INTO emails (userid, email) VALUES (%d, '%s')", $userid, mysql_real_escape_string($email)));
echo 'Thanks for submitting your email, we will keep you updated with strictly the most important news you wanna hear.. (you can close this window)';
?>
