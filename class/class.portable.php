<?php

class portable {

private $_local;

function __construct(){

//$blah = new local();
	require_once(site_root."html2fpdf/html2fpdf.php");
}


function batchPDF($resultset){

$margh = new local();

$sql = $margh->selectQuery("heraldid", "resultsetstudents", "res_id = ".$resultset);

while ($data = mysqli_fetch_array($sql)){
	$this->buildPDF($resultset, $data['heraldid']);
}

}

function buildPDF($resultset, $user){

	$blah = new local();

	if (! $blah->selectQuery("*", "users")){
		//print "<h1>SCREWED</h1>";
	}
	
	
	$sql = $blah->selectQuery("res_name", "resultsets", "res_id = ".$resultset);
	while($data = mysqli_fetch_array($sql)){
		$res_name = $data['res_name'];
	}	
	
	$local = new local();
	$array = $local->returnUser($user);
	$usr_name = $array[0];
	
	//Draw header
	$html = '<p><b>Results for '.$usr_name.' (<i>'.$user.'</i>)</b><br />'.$res_name.'</p>';
	
	//Get values for User for Resultset
	$offset = 1;	
	$sql = $blah->selectQuery("rsd_value", "resultsetfieldsdata
	INNER JOIN resultsetstudents ON resultsetfieldsdata.rss_id = resultsetstudents.rss_id", "heraldid = '".$user."'
	AND resultsetfieldsdata.res_id = ".$resultset."
	AND resultsetstudents.res_id = ".$resultset."
	ORDER BY rsf_offset");
	
	while ($data = mysqli_fetch_array($sql)){
		
		$offset++;
		$sql2 = $blah->selectQuery("rsf_title, rsf_heading", "resultsetfields", "res_id =".$resultset."
		AND rsf_offset =".$offset."
		ORDER BY rsf_id");
		$numheaders = mysqli_num_rows($sql2);
		$headcount = 0;		
		while ($data2 = mysqli_fetch_array($sql2)){
			
			//$html .= '<p class="h'.$data2['rsf_heading'].'">'.$data2['rsf_title']."</p>";
			//$html .= '<h'.$h.'>'.$data2['rsf_title'].'</h'.$h.'>';
			$html .= '<p class="h'.$data2['rsf_heading'].'">'.$data2['rsf_title'];
			$headcount++;
			if ($headcount < $numheaders){
				$html .= '</p>';
			} else {
				$html .= ': '.$data['rsd_value'].'</p>';
			}
			//$h++;
			
		}
		
		//unset($h);		
		//$html .= '<p class="data">'.$data['rsd_value'].'</p>';
	}
	
	if (! $template = fopen(site_root."templates/pdf.html", "r")){
		$element = '<p>Template cannot be found</p>'; //Replace with better error messages
	} else {
		$element = fread($template, filesize(site_root."templates/pdf.html"));
		$element = preg_replace("/{{pdf}}/i", $html, $element);
	}
	
	$filename = $resultset."_".$user.".pdf";
	
	$pdf = new HTML2FPDF();
	$pdf->AddPage();
	$pdf->WriteHTML($element);
	$pdf->output("./pdf/".$filename, "f");
	
	$log = new log(1);
	$log->writeLog("Created Portable Document ".$filename);
		
	return $filename;

}

function __destruct(){

}

}

?>