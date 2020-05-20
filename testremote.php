<?php

define("remote_host", "pinnacle.imsu.ox.ac.uk");
define("remote_user", "sel_mstcfeedback");
define("remote_password", "selmstcfb");
define("remote_database", "mstcfeedback");


$conn = mysql_connect(remote_host, remote_user, remote_password);
		
	mysql_select_db(remote_database);
					
		mysql_query("SET NAMES 'utf8'");
		
		$sql = mysql_query("SELECT Firstname, Lastname, Username, Email, Dept FROM cards");
		
		while ($data = mysql_fetch_array($sql)){
			
			print_r($data);
						
		}
			
?>