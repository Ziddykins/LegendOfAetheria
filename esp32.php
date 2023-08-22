<?php
    $db = new mysqli('localhost',
					 'user_gamething',
					 'G4m3th1n6!',
					 'db_gamething',
					 3306);

    $result = $db->query('SELECT * FROM tbl_accounts WHERE esp = 1;');
	$row = $result->fetch_assoc();
    // 11 + charlen(result)
    // 24 max
    print_r($row);
?>
    