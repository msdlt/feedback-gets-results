<?php

class remote implements database {

	private $_remote;
	
	function __construct() {
		//print "<p>Attempting to open MySQL connection...</p>";
		$this->_remote = mysql_connect(remote_host, remote_user, remote_password);
		
		if (! $this->_remote) {
			return "Unable to connect to database.";
		}
		
		if (! mysql_select_db(remote_database)) {
			return "Unable to select Feedback gets Results database.";
		}
		
		mysql_query("SET NAMES 'utf8'");
		//print("<h1>Local Connection Created</h1>");
		//return "Connection successful";
	}
	
	function selectQuery($fields, $from, $where = null) {
		if ($where == null){
			return mysql_query("SELECT ".$fields." FROM ".$from, $this->_remote);
		} else {
			return mysql_query("SELECT ".$fields." FROM ".$from." WHERE ".$where, $this->_remote);
		}
	}
	
	function countQuery($field, $from, $where = null){
	//Counts the number of records in a table, optionally based on criteria; Returns integer
		if ($where == null){
			$sql = mysql_query("SELECT COUNT(".$field.") FROM ".$from, $this->_remote);
			$data = mysql_fetch_row($sql);
			return $data[0];
		} else {
			$sql = mysql_query("SELECT COUNT(".$field.") FROM ".$from." WHERE ".$where, $this->_remote);
			$data = mysql_fetch_row($sql);
			return $data[0];
		}
	}
	
	function surveyInstances(){
	
		
		
		$sql = mysql_query("select surveyInstanceID, title from SurveyInstances where finishDate > now() OR finishDate IS NULL", $this->_remote);
		
		$local = new local();
		$used = array();
		$sql2 = $local->selectQuery("surveyinstanceid", "resultsets");
		while ($data2 = mysql_fetch_array($sql2)){
			$used[] = $data2['surveyinstanceid'];
		}
		
		$html = '<form action="./?res&add" method="post" name="addres2">
		<select name="select">';

		$i = 0;
		while ($data = mysql_fetch_array($sql)){
				if (! in_array($data['surveyInstanceID'], $used)){
				$html .= '<option value="'.$data['surveyInstanceID'].'">'.$data['title'].'</option>';
				$i++;
				}
		}
		
		$html .= '</select> <label>Select the Survey Instance</label>';
		
		if ($i == 0){
			return false;
		} else {
			//echo "<p>I found ".$i." records!</p>";
			return $html;
		}
		
		
			
	}
	
	function surveyName($surveyinstance){
		
		$sql = mysql_query("select Surveys.title from Surveys inner join SurveyInstances on 
		Surveys.surveyID = SurveyInstances.surveyID where surveyInstanceID = ".$surveyinstance, $this->_remote);
		
		while ($data = mysql_fetch_array($sql)){
			$html .= $data['title'];
		}
		
		if (mysql_num_rows($sql) == 0){
			return "Unknown";
		} else {
			return $html;
		}
		
	
	}
	
	function surveyInstanceName($surveyinstance){
	
		$sql = mysql_query("SELECT title FROM SurveyInstances WHERE surveyInstanceID = ".$surveyinstance, $this->_remote);
		
		while ($data = mysql_fetch_array($sql)){
			$html .= $data['title'];
		}
		
		if (mysql_num_rows($sql) == 0){
			return "Unknown";
		} else {
			return $html;
		}
		
	}
	
	function surveyInstanceFinish($surveyinstance){
	
		$sql = mysql_query("SELECT finishDate FROM SurveyInstances WHERE surveyInstanceID = ".$surveyinstance, $this->_remote);
		
		while ($data = mysql_fetch_array($sql)){
			$html = $data['finishDate'];
		}
		
		if (mysql_num_rows($sql) == 0){
			return "Unknown";
		} else {
			return date("jS M Y", strtotime($html));
		}
		
	}
	
	function finishDate($surveyinstance){
		$sql = mysql_query("select finishDate from SurveyInstances where surveyInstanceID = ".$surveyinstance, $this->_remote);
		
		while ($data = mysql_fetch_array($sql)){
			$value = $data['finishDate'];
		}
		
		if (mysql_num_rows($sql) > 0){
			return $value;
		} else {
			return false;
		}
		
	}
	
	function daysRemaining($surveyinstance){
		
		$finishDate = $this->finishDate($surveyinstance);
		
		if (! $finishDate){ //If no finish date is specified, assume one year of days remaining
			return 365;
		} else {
			$finishdate = str_split($finishDate);
			$finishdatearray[] = $finishdate[0].$finishdate[1].$finishdate[2].$finishdate[3];
			$finishdatearray[] = $finishdate[5].$finishdate[6];
			$finishdatearray[] = $finishdate[8].$finishdate[9];		
			
			$finishdateunix = mktime(0, 0, 0, $finishdatearray[1], $finishdatearray[2], $finishdatearray[0]);
			$currentdateunix = time();
			
			return floor((((($finishdateunix - $currentdateunix) / 60) / 60) / 24));
		}
		
	}
	
	function unixRemaining($surveyinstance){
		
		if ($this->finishDate($surveyinstance) == 365){
		
		return 0;
		
		} else {
		
		$finishdate = str_split($this->finishDate($surveyinstance));
		$finishdatearray[] = $finishdate[0].$finishdate[1].$finishdate[2].$finishdate[3];
		$finishdatearray[] = $finishdate[5].$finishdate[6];
		$finishdatearray[] = $finishdate[8].$finishdate[9];		
		
		return mktime(0, 0, 0, $finishdatearray[1], $finishdatearray[2], $finishdatearray[0]);
		
		}
		
	}

	function __destruct() {
		//print "<p>Attempting to close MySQL connection...</p>";
		if (! mysql_close($this->_remote)) {
			return "Unable to disconnect from database.";
		}
	}

}

?>