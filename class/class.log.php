<?php

class log {

private $_file;
private $_filename;

function __construct($read = null){

	//SuperAdmins can read the file, others can simply write to it
	if (isset($read)){
		//Write access only at end of file
		$mode = "a";
	} else {
		//Read access only at the beginning of file
		$mode = "r";
	}
	
	$this->_filename = strtolower(date("Y_m_d", time())).".txt";
	
	if (! touch(site_root."log/".$this->_filename)){
		exit("Filesystem is not writable. Please contact an administrator.");
	}
	
	if (! $this->_file = fopen(site_root."log/".$this->_filename, $mode)){
		exit("Cannot open file for writing");
	}
	
}

function findLogs(){

	$array = scandir(site_root."log/", 1);
	array_pop($array);
	array_pop($array);
	
	arsort($array);
	//print_r($array);
	
	return $array;

}

function writeLog($action){

$entry = date("H:i, jS M Y", time())." - ".$_SESSION['user']." - ".$action."\n";

if (fwrite($this->_file, $entry) == false){
	//echo 'Cant write to log';
	return false;
} else {
	return true;
}
	
}

function readLog(){

$filesize = filesize(site_root."log/".$this->_filename);

if ($filesize != 0){
	$log = fread($this->_file, $filesize);
} else {
	$log = "Log file is empty";
}

return $log;

}

function readArchive($date){

$filename = strtolower(date("Y_m_d", $date)).".txt";
$filesize = filesize(site_root."log/".$filename);
$file = fopen(site_root."log/".$filename, "r");
if ($filesize != 0){
	$log = fread($file, $filesize);
} else {
	$log = "Log file is empty CLOWNS!";
}

fclose($file);

return $log;

}

function resetFile(){
	
	/*if (! chown(site_root."log/".$this->_filename, "www-run")){
		$this->writeLog("Could not change ownership of log ".$this->_filename);
	}
	
	if (! chgrp(site_root."log/".$this->_filename, "www")){
		$this->writeLog("Could not change group of log ".$this->_filename);
	}*/
	chgrp(site_root."log/".$this->_filename, "www");
	chown(site_root."log/".$this->_filename, "wwwrun");
	
}


function __destruct(){
	fclose($this->_file);
	if ($_SESSION['user'] == "imsu_fgr") { //For the cron script
		$this->resetFile();
		session_destroy();
	}
}

}

?>