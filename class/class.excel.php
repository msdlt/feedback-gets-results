<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;

class excel {

function upload(){

	 $log = new log(1);

	if ($_FILES['excel']['name'] != ""){
										
		//If there is an image of the same name already, rename
		$append = 2;
		$temp_source_file = $_FILES['excel']['name'];
		while (file_exists("./uploads/".$_FILES['excel']['name'])) {
			//list($f_name, $f_ext) = split('[.]', $temp_source_file);
			list($f_name, $f_ext) = explode('[.]', $temp_source_file);
			$_FILES['excel']['name'] = $f_name."_".$append.".".$f_ext;
			$append++;
		}
		
		//Upload temporary image file
		if (is_uploaded_file($_FILES['excel']['tmp_name'])) {
		   //echo "<p>Uploading ". $_FILES['excel']['name'] ."...<br />";
		   } else {
		   //echo "Possible file upload attack: ";
		  echo "filename '". $_FILES['excel']['tmp_name'] . "'.";
		}
		
		/*//Transfer image to uploads directory		
		$uploaddir = './uploads/';
		$uploadfile = $uploaddir . basename($_FILES['excel']['name']);
		$filetype = strtolower($_FILES['excel']['type']);
		if (move_uploaded_file($_FILES['excel']['tmp_name'], $uploadfile)) {
			rename($uploaddir . $_FILES['excel']['name'], $uploaddir . strtolower($_FILES['excel']['name']));
			echo "Upload success";
		}*/
	
	}

	
	//require_once './phpxls/reader.php';

	require_once("vendor/autoload.php"); 
	
		
	//$resultset = new Spreadsheet_Excel_Reader();
	//$resultset->setOutputEncoding('CP1251');
	//$resultset->setUTFEncoder('mb');
	if (! is_uploaded_file($_FILES['excel']['tmp_name'])) {
		//print "Image not transferred to temporary directory";
	}
	
	$uploaddir = './uploads/';
	$uploadfile = $uploaddir . basename($_FILES['excel']['name']);
	
	if (move_uploaded_file($_FILES['excel']['tmp_name'], $uploadfile)) {
		$log->writeLog("Uploaded ".basename($_FILES['excel']['name']));
		//print '<img src="./log/'.$_FILES['excel']['name'].'" alt="Worked">';
	} else {
		$log->writeLog("Could not Upload ".basename($_FILES['excel']['name']));
		//print "Image cannot be moved to permanent directory";
	}
	//$resultset->read($uploaddir . basename($_FILES['excel']['name']));
	//$resultset->read($_FILES['excel']['tmp_name']);

	$resultset = \PhpOffice\PhpSpreadsheet\IOFactory::load($uploaddir . basename($_FILES['excel']['name']));
		
	//return $resultset->sheets[0];
	return $resultset->setActiveSheetIndex(0);

	//return "bonobo";
	
}

}

?>