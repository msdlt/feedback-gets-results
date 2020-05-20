<?php

class stats{

private $_local;
private $_remote;

function __construct(){
	$this->_local = new local();
	$this->_remote = new remote();
}

function getStats(){

	$survey .= '<strong>Active Survey Instances: '.$this->statSurveyInstanceActive().
	'<br />Total Survey Instances: '.$this->statSurveyInstanceCount().'</strong>';
	
	$resultsets = '<strong>Active Resultsets: '.$this->statResultsetActive().
	'<br />Total Resultsets: '.$this->statResultsetCount().'</strong>';
	
	$reminders = '<strong>Reminders: '.$this->statReminderCount().'</strong>';
	
	$superadmins = '<strong>Super Administrators: '.$this->statUserCount(3).'</strong>';
	$admins = '<strong>Administrators: '.$this->statUserCount(2).'</strong>';
	$students = '<strong>Students: '.$this->statUserCount(1).'</strong>';
	
	if (! $template = fopen(site_root."templates/stats.html", "r")){
		$element = '<p>Template cannot be found</p>'; //Replace with better error messages
	} else {
		$element = fread($template, filesize(site_root."templates/stats.html"));
		$element = preg_replace("/{{surveys}}/i", $survey, $element);
		$element = preg_replace("/{{resultsets}}/i", $resultsets, $element);
		$element = preg_replace("/{{reminders}}/i", $reminders, $element);
		$element = preg_replace("/{{superadmins}}/i", $superadmins, $element);
		$element = preg_replace("/{{admins}}/i", $admins, $element);
		$element = preg_replace("/{{students}}/i", $students, $element);
	}
	
	return $element;
	
}

function statResultsetCount(){
//Returns number of Resultsets in Feedback gets Results

	$sql = $this->_local->selectQuery("count(res_id)", "resultsets");
	$data = mysql_fetch_row($sql);
	
	return $data[0];

}

function statResultsetActive(){
//Returns number of Resultsets in Feedback gets Results that have an active Survey Instance in MSD Feedback

$sql = $this->_local->selectQuery("surveyinstanceid", "resultsets");
$sql2 = $this->_remote->selectQuery("surveyInstanceID", "SurveyInstances", "finishDate > NOW() OR finishDate IS NULL");
while ($remotedata = mysql_fetch_array($sql2)){
	$remaining[] = $remotedata['surveyInstanceID'];
}
while ($localdata = mysql_fetch_array($sql)){
	if (in_array($localdata['surveyinstanceid'], $remaining)){
		$i++;
	}
}

return $i;

}

function statSurveyInstanceCount(){
//Returns number of Survey Instances in MSD Feedback. Result should be less than or equal to statResultsetCount

	$sql = $this->_remote->selectQuery("count(surveyInstanceID)", "SurveyInstances");
	$data = mysql_fetch_row($sql);
	
	return $data[0];

}

function statSurveyInstanceActive(){
//Returns number of active Survey Instances in MSD Feedback. Result should be less than or equal to statResultsetCount

	$sql = $this->_remote->selectQuery("count(surveyInstanceID)", "SurveyInstances", "finishDate > NOW() OR finishDate IS NULL");
	$data = mysql_fetch_row($sql);
	
	return $data[0];

}

function statReminderCount(){

	$sql = $this->_local->selectQuery("count(rem_id)", "reminders");
	$data = mysql_fetch_row($sql);
	
	return $data[0];

}

function statUserCount($pri){

	$sql = $this->_local->selectQuery("count(heraldid)", "users", "prv_id = ".$pri);
	$data = mysql_fetch_row($sql);
	
	return $data[0];

}

}

?>