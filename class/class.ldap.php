<?php

class ldap {

	private $_remote;
	
	function __construct(){
	
	$this->_remote = mysqli_connect(carddata_host, carddata_user, carddata_password, carddata_database);
	//$this->_remote = mysql_connect("netman3.imsu.ox.ac.uk", "mstcunivcard", "univcardmstc");
		
		if (! $this->_remote) {
			return "Unable to connect to database.";
		}
		
		//if (! mysql_select_db(carddata_database)) {
		//	return "Unable to select Feedback gets Results database.";
		//}
		
		//mysql_query("SET NAMES 'utf8'");
		mysqli_set_charset($this->_remote, 'utf8mb4');
		
	}
	
	function search($criteria){
		
	}
	
/*	function returnName($heraldid){
	
		$name = $this->search($heraldid);
		return $name[0]["fullname"];
		
	}
	
	function returnEmail($heraldid){
	
		$email = $this->search($heraldid);
		return $email[0]["oxfordemail"];
		
	} */
	
	function returnUser($heraldid){
	
		$sql = mysqli_query($this->_remote, "select Firstname, Lastname, Username, Email, Dept from cards where Username = '".$heraldid."'");
		//echo "select Firstname, Lastname, Username, Email, Dept from cards where Username = '".$heraldid."'";
		
		//echo "i found ".mysql_num_rows($sql)." rows ";
		
		if (mysqli_num_rows($sql) <= 0){
		//if (1 == 0){
			$array[] = $heraldid;
			$array[] = "Unknown";
			$array[] = "Unknown";
			$array[] = 1;
			//MSDLT
			//echo "not found";
			//print_r($array);
			//exit();
				
		} else {
		//Else assume local user, return as above plus privlige id
			//print "i found user in card database! ";
			
			$user = new local();
			
			while ($data = mysqli_fetch_array($sql)){
			
				$username = $data['Firstname']." ".$data['Lastname'];
				$dept = $data['Dept'];
				$email = $data['Email'];
							
			}
			
			//print $username." ".$dept." ".$email." ";
			
			$sql2 = $user->selectQuery("usr_name, usr_dept, usr_email, prv_id", "users", "heraldid = '".$heraldid."'");
			if (mysqli_num_rows($sql2) <= 0){
				$array[] = $username;
				$array[] = $dept;
				$array[] = $email;
				$array[] = 1;	
				//MSDLT
				//echo "not a user";
			//print_r($array);	
			//exit();		
			} else {
				$data2 = mysqli_fetch_array($sql2);
				$array[] = $data2['usr_name'];
				$array[] = $data2['usr_dept'];
				$array[] = $data2['usr_email'];
				$array[] = $data2['prv_id'];	
			//MSDLT
			//echo "already a user";
			//print_r($array);	
			//exit();
			}
			
			//If user is not found via LDAP or local
		}
		
		//print_r($array);
		return $array;
	
	}
	
	function __destruct(){
		//print "<p>Attempting to close MySQL connection...</p>";
		if (! mysqli_close($this->_remote)) {
			return "Unable to disconnect from database.";
		}
	}
	
}

?>