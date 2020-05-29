<?php

define("remote_host", "pinnacle.imsu.ox.ac.uk");
define("remote_user", "sel_mstcfeedback");
define("remote_password", "selmstcfb");
define("remote_database", "mstcfeedback");


$conn = mysqli_connect(remote_host, remote_user, remote_password, remote_database);
		
	//mysql_select_db(remote_database);
					
		//mysql_query("SET NAMES 'utf8'");
		mysqli_set_charset($conn, 'utf8mb4');
		
		$sql = mysqli_query($conn, "SELECT Firstname, Lastname, Username, Email, Dept FROM cards");
		
		while ($data = mysqli_fetch_array($sql)){
			
			print_r($data);
						
		}
			
?>