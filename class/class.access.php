<?php

/* class.access.php
The access class contains all methods required for preventing users from activating certain methods based on the access array */

class access {

	private $_access;
	private $_keycard;
	
	//Reads the required access array into the class
	function __construct($keycard){
		$this->_access = $_SESSION['access'];	
		$this->_keycard = $keycard;
	}
	
	//Grants or denies access to a given switch based on the access array
	function keycard(){
		
			foreach(array_keys($this->_keycard) as $switch){
			//Deny
			
			if(! in_array($switch, array_keys($this->_access))){
				$log = new log(1);
				$log->writeLog("System intrusion attempt");
				return '<p>System intrusion attempt detected, stopping application...<br />'.
				$_SESSION['user'].' via '.$_SERVER['REMOTE_ADDR'].'</p>';
				
				//implement log, and more stats about machine
				exit;
			}
			
			return null;
			
		}
	}
	
}

?>