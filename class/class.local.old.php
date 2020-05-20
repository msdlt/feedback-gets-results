<?php

class local implements database {

	private $_local;
	
	function __construct() {
	
		//set_magic_quote_runtime(1);
		//print "<p>Attempting to open MySQL connection...</p>";
		$this->_local = mysql_connect(local_host, local_user, local_password);
		
		if (! $this->_local) {
			return "Unable to connect to database.";
		}
		
		if (! mysql_select_db(local_database, $this->_local)) {
			return "Unable to select Feedback gets Results database.";
		}
		
		mysql_query("SET NAMES 'utf8'");
		
		
		//print("<h1>Local Connection Created</h1>");
		//return "Connection successful";
		
	}
	
	function selectQuery($fields, $from, $where = null) {
		if ($where == null){
			//echo "<p>SELECT ".$fields." FROM ".$from."</p>";
			return mysql_query("SELECT ".$fields." FROM ".$from, $this->_local);
		} else {
			//print "<p>SELECT ".$fields." FROM ".$from." WHERE ".$where."</p>";
			return mysql_query("SELECT ".$fields." FROM ".$from." WHERE ".$where, $this->_local);
		}
		//return mysql_fetch_array($sql);
	}
	
	function countQuery($field, $from, $where = null){
	//Counts the number of records in a table, optionally based on criteria; Returns integer
		if ($where == null){
			$sql = mysql_query("SELECT COUNT(".$field.") FROM ".$from, $this->_local);
			$data = mysql_fetch_row($sql);
			if (! $sql){
				return 0;
			} else {
				return $data[0];
			}
		} else {
			$sql = mysql_query("SELECT COUNT(".$field.") FROM ".$from." WHERE ".$where, $this->_local);
			$data = mysql_fetch_row($sql);
			if (! $sql){
				return 0;
			} else {
				return $data[0];
			}
		}
	}
	
	function addResultset($resultset){
	
		switch($resultset['progress']){
	
		case 1:
		//Adding Resultset
			
				$sql = mysql_query("LOCK TABLE resultsets WRITE", $this->_local);
				if (! $sql){
					return "Unable to lock tables";
				}
				
				$sql2 = mysql_query("INSERT INTO `resultsets` ( `res_id` , `heraldid` , `surveyinstanceid` , `res_name` , `res_timestamp` )
				VALUES (
				NULL , '".$_SESSION['user']."', '".$resultset['surveyInstance']."', '".mysql_real_escape_string($resultset['name'])."', NOW( )
				)", $this->_local);
				if (! $sql2){
					return "Unable to insert values";
				}
				
				$sql3 = mysql_query("SELECT LAST_INSERT_ID()", $this->_local);
				if (! $sql3){
					return "Unable to select last auto increment";
				}
				while ($data = mysql_fetch_row($sql3)){
					$_SESSION['res_id'] = $data[0];
				}
				
				$sql4 = mysql_query("UNLOCK TABLES", $this->_local);
				if (! $sql4){
					return "Unable to unlock tables";
				}
				
				$log = new log(1);
				$log->writeLog("Created Resultset ".$resultset['name']);
				
		break;
		
		case 2:
		//Adding headings
				
				//return "Resultset added successfully";
				
				$sql5 = mysql_query("LOCK TABLE resultsetfields WRITE", $this->_local);
				if (! $sql5){
					return "Unable to lock tables";
				}
				
				//print_r($resultset);
				
				//Set no parent for the first header
				$parent1 = "NULL";
				$parent2 = "NULL";
				
				//For each column (except Herald ID)
				for ($j = 2; $j <= $resultset['numCols']; $j++) {
				
				//For each heading from H1 to H3
				for ($i = 1; $i <= 3; $i++) { //CLOWNS
						if ($resultset['cells'][$i][$j] != null){
														
							$sql6 = mysql_query("INSERT INTO `resultsetfields` ( `rsf_id` , `res_id` , `rsf_title` , `rsf_heading` , `rsf_offset` )
							VALUES (
							NULL , ".$_SESSION['res_id'].", '".mysql_real_escape_string($resultset['cells'][$i][$j])."', ".$i.", ".$j.")", $this->_local);
							if (! $sql6){
								return "Unable to insert values";
							}
								
							//If either H1 or H2 (H3 cannot be a parent header because it is the lowest level)
							if ($i != 3){
								//Select the rsf_id of that header, to set as the parent header of the 
								$sql7 = mysql_query("SELECT LAST_INSERT_ID()", $this->_local);
								if (! $sql7){
									return "Unable to select last auto increment";
								}
								while ($data = mysql_fetch_row($sql7)){
									switch($i){
									case 1:					
										$parent1 = $data[0];
										$parent2 = "NULL";
										break;
									case 2:
										$parent2 = $data[0];
										 break;
								}
								
								}
							}
						}				
					}
				}
				
				$sql8 = mysql_query("UNLOCK TABLES", $this->_local);
				if (! $sql8){
					return "Unable to unlock tables";
				}
				
		break;
		
		case 3:
		//Adding students
				
				/*$sqlmega = mysql_query("LOCK TABLE resultsetstudents WRITE");
				if (! $sqlmega){
					return "Unable to lock tables";
				}*/
				$ldap = new ldap();
				//For each student
				for ($i = 4; $i <= $resultset['numRows']; $i++) {
					if ($resultset['cells'][$i][1] != "" || $resultset['cells'][$i][1] != null){
					//print "<p><b><i>dollar i = ".$i."</i></b></p>";
					//print "<p>".$resultset['cells'][$i][1]."</p>";
					$sql9 = mysql_query("INSERT INTO `resultsetstudents` ( `rss_id` , `res_id` , `heraldid` )
					VALUES (
					NULL , '".$_SESSION['res_id']."', '".$resultset['cells'][$i][1]."'
					)", $this->_local);
					if (! $sql9){
						//return "Unable to insert fields";
					}
					
					$sql10 = mysql_query("SELECT LAST_INSERT_ID()", $this->_local);
					if (! $sql10){
						return "Unable to select last auto increment";
					}
					while ($data = mysql_fetch_row($sql10)){
						$student = $data[0];
					}		
								
					for ($j = 2; $j <= $resultset['numCols']; $j++) {
						$sql11 = mysql_query("INSERT INTO `resultsetfieldsdata` ( `rsd_id` , `res_id` , `rsf_offset` , `rss_id` , `rsd_value` )
						VALUES (
						NULL , '".$_SESSION['res_id']."', '".$j."', '".$student."', '".mysql_real_escape_string($resultset['cells'][$i][$j])."'
						)", $this->_local);
						if (! $sql11){
						return "Unable to insert fields";
						}
					}
					
					//Test returnUser bug
					//$resultset['cells'][$i][1] = "imsu0005";
					//$blah = 'imsu0005';
					
					//Check if the user in the system is an administrator
					$f = $this->countQuery("heraldid", "users", "heraldid = '".$resultset['cells'][$i][1]."' and prv_id != 1");
					//print '<h3>An admin?</h3>';
					//If they are not an admin
					if ($f == 0){
					//print '<h2>No</h2>';
						//Import information from LDAP
						$e = $this->countQuery("heraldid", "users", "heraldid = '".$resultset['cells'][$i][1]."'");
						//If user already exists in the system , delete them first
						if ($e > 0){
							$sql_del = mysql_query("delete from users where heraldid = '".$resultset['cells'][$i][1]."'", $this->_local);
							if (! $sql_del){
								return "Could not delete user from local database";
							} else {
								//print "<h1>"."delete from users where heraldid = '".$resultset['cells'][$i][1]."'"."</h1>";
							}
						}
							
												
							$student = $ldap->returnUser($resultset['cells'][$i][1]);
							if ($student[1] == "Unknown"){
								$_SESSION['resultset']['unknown'][] = $student;
							}
							//print "<h2>i = ".$i.", Looking up ".$resultset['cells'][$i][1];
							//print_r($student);
							//print "</h2>";
							$sql_sequel = mysql_query("INSERT INTO `users` ( `heraldid` , `prv_id` , `usr_name` , `usr_dept` , `usr_email` )
								VALUES (
								'".$resultset['cells'][$i][1]."', '".$student[3]."', '".mysql_real_escape_string($student[0])."', '".mysql_real_escape_string($student[1])."', '".mysql_real_escape_string($student[2])."'
								)", $this->_local);
							if (! $sql_sequel){
								/*return "Do you wanna end up in the hospital for five weeks this time! INSERT INTO `users` ( `heraldid` , `prv_id` , `usr_name` , `usr_dept` , `usr_email` )
								VALUES (
								'".$resultset['cells'][$i][1]."', '".$student[3]."', '".$student[0]."', '".$student[1]."', '".$student[2]."'
								)";*/
							} else {
								$log = new log(1);
								if ($e > 0){
									$log->writeLog("Updated Student ".$student[0]." (".$resultset['cells'][$i][1].")");
								} else {
									$log->writeLog("Added Student ".$student[0]." (".$resultset['cells'][$i][1].")");
								}
							}
							
							//Send student an email letting them know the results are available
							$email = new email();
							$to = $student[0]." (".$resultset['cells'][$i][1].") <".$student[2].">";
							$reply = $_SESSION['name']." <".$_SESSION['email'].">";
							$subject = "[fgr] Your Results are now available for ".$resultset['name'];
							
							$filename = "/srv/www/fgr/templates/default_released.txt";
							$file = fopen($filename, "r");
							$default = fread($file, filesize($filename));
							$message = $default;
							
							//$message = "Test!\n\nShould have been sent to: ".$to;
							
							if (! $email->send($to, $reply, $subject, $message)){
								$log->writeLog("Cannot send Email to ".$student[0]." (".$resultset['cells'][$i][1].") for ".$resultset['name']);
							} else {
								$log->writeLog("Sent Email to ".$student[0]." (".$resultset['cells'][$i][1].") for ".$resultset['name']);
							}
						
					}
						
					
					
					}
				}
				
				/*$sqlultra = mysql_query("UNLOCK TABLES");
				if (! $sqlultra){
					return "Unable to unlock tables";
				}*/
				
		break;
		
		case 4:
				
				//If feedback is compulsory, and there are Reminders set
				if ($resultset['compulsory'] != 0 && isset($resultset['reminders'])){
					foreach ($resultset['reminders'] as $reminders){
					
						if ($reminders['msg'] == ""){
							$msg = "NULL";
						} else {
							$msg = $reminders['msg'];
						}
					
						$this->addReminder($_SESSION['res_id'], $reminders['remdate'], $msg);
					
					}
				}
				
		break;
		
		case 5:
				
				//Generate PDFs
				$portable = new portable();
				$portable->batchPDF($_SESSION['res_id']);
				
				if (isset($_SESSION['resultset']['unknown'])){
					//print_r($_SESSION['resultset']['unknown']);
				} else {
					//print "<h2>TYPHOON!</h2>";
				}
						
		break;
		
		}
			
	}
	
	function checkPerms($resultset){
	
		$sql_verify = $this->selectQuery("res_id", "resultsets", "heraldid = '".$_SESSION['user']."'");
		while ($data_res = mysql_fetch_array($sql_verify)){ //Find resultsets that the user owns
			$resultsets_owned[] = $data_res['res_id'];
		}
		/*foreach($resultsets_owned as $thing){
			print $thing['res_id'].'<br />';
		}*/
		
		if (array_search($resultset, $resultsets_owned) === false){ //Check if they own the resultset they are trying to view (=== as may return array[0])
			$log = new log(1);
			$log->writeLog("System intrusion attempt");
			return '<p>System intrusion attempt detected, stopping application...<br />'.
			$_SESSION['user'].' via '.$_SERVER['REMOTE_ADDR'].'</p>';
			
			//implement log, and more stats about machine
			exit;
		} else {
			return false;
		}
		
	}
	
	function addReminder($res, $date, $msg){
		
		$sql = mysql_query("INSERT INTO `reminders` ( `rem_id` , `res_id` , `rem_date` , `rem_message` )
		VALUES (
		NULL , '".$res."', FROM_UNIXTIME( '".$date."' ) , '".mysql_real_escape_string($msg)."'
		)", $this->_local);

		if (! $sql){
			return false;
		} else {
			$log = new log(1);
			$log->writeLog("Added Reminder for ".$data['res_name']." (".date("jS M Y", $date).")");
			return true;
		}
	
	}
	
	function buildResultset($resultset, $user){
	
	$offset = 1;	
	$sql = mysql_query("SELECT rsd_value
	FROM resultsetfieldsdata
	INNER JOIN resultsetstudents ON resultsetfieldsdata.rss_id = resultsetstudents.rss_id
	WHERE heraldid = '".$user."'
	AND resultsetfieldsdata.res_id = ".$resultset."
	AND resultsetstudents.res_id = ".$resultset."
	ORDER BY rsf_offset");
	
	while ($data = mysql_fetch_array($sql)){
	
		$offset++;
		$sql2 = mysql_query("SELECT rsf_title, rsf_heading
		FROM resultsetfields
		WHERE res_id =".$resultset."
		AND rsf_offset =".$offset."
		ORDER BY rsf_id");
		$numheaders = mysql_num_rows($sql2);
		$headcount = 0;
		while ($data2 = mysql_fetch_array($sql2)){
			
			//$html .= '<h'.$h.'>'.$data2['rsf_title'].'</h'.$h.'>';
			$html .= '<h'.$data2['rsf_heading'].'>'.$data2['rsf_title'];
			$headcount++;
			if ($headcount < $numheaders){
				$html .= '</h'.$data2['rsf_heading'].'>';
			} else {
				$html .= ': '.$data['rsd_value'].'</h'.$data2['rsf_heading'].'>';
			}
		}
		
		//$html .= '<p class="data">'.$data['rsd_value'].'</p>';
		
	
	}
	
	return $html;
	
	}
	
	function titleResultset($resultset){
	
		$sql = $this->selectQuery("res_name", "resultsets", "res_id = ".$resultset);
		$array = mysql_fetch_array($sql);
		return $array[0];	
	
	}
	
	function overviewResultset($resultset){
	
		//$local = new local();
		$remote = new remote();
				
		$sql = $this->selectQuery("*", "resultsets", "res_id = ".$resultset);
		while ($data = mysql_fetch_array($sql)){
			$html = '<p><strong>'.$data['res_name'].'</strong>';
			if ($data['heraldid'] != $_SESSION['user']){
				$html .= '<br />Created by ';
				$user = $this->returnUser($data['heraldid']);
				$html .= $user[0].', '.$user[1];
			}
			$html .= '</p>';
			
			$html .= '<p>Linked to '.$remote->surveyName($data['surveyinstanceid']).
			'<br /><em>'.$remote->surveyInstanceName($data['surveyinstanceid']).'</em>
			<p>Available until '.$remote->surveyInstanceFinish($data['surveyinstanceid']).'</p>';
			
			$res_id = $data['res_id'];
			$surveyinstanceid = $data['surveyinstanceid'];
			
			}
			
			$sql3 = $this->selectQuery("users.heraldid, usr_name, usr_dept, usr_email, prv_id", 
			"users inner join resultsetstudents on users.heraldid = resultsetstudents.heraldid", "res_id = ".$res_id);
			
			$sql6 = $this->selectQuery("users.heraldid, usr_name, usr_dept, usr_email, prv_id", 
			"users inner join resultsetstudents on users.heraldid = resultsetstudents.heraldid", "res_id = ".$res_id);
			
			$sql5 = $remote->selectQuery("heraldID", "SurveyInstanceParticipants", "surveyInstanceID = ".$surveyinstanceid." AND status = 2"); //RABBIT
						
			$thingies = array();
				while ($data5 = mysql_fetch_array($sql5)){
					$thingies[] = $data5['heraldID'];
				}
			
			$x = 0;
			while ($data4 = mysql_fetch_array($sql6)){
				if (in_array($data4['heraldid'], $thingies)){
					$x++;
				}
			}
			
			$y = mysql_num_rows($sql3);
			
			$html .= '<p>Feedback is '.$x.' out of '.$y.' ('.round((($x/$y) * 100), 2).'%)</p>';
			
			$html .= '<table>
		    <tr>
		      <th><div class="th">Student</div></th>
		      <th><div class="th">Department</div></th>
			  <th width="25%"><div class="th">ID</div></th>
			</tr>';
			
			while ($data2 = mysql_fetch_array($sql3)){
				$sql4 = $remote->selectQuery("status", "SurveyInstanceParticipants", "heraldID = '".$data2['heraldid']."'
				AND surveyInstanceID =".$surveyinstanceid);
				$data3 = mysql_fetch_array($sql4);
				switch($data3['status']){
					case "1":
					$highlight = ' class = "amber"';
					break;
					case "2":
					$highlight = ' class = "green"';
					break;
					default:
					$highlight = ' class = "red"';
					break;
				}				
				$html .= '<tr>
			      <td'.$highlight.'><a href="./?res='.$resultset.'&view&user='.$data2['heraldid'].'">'.$data2['usr_name'].'</a></td>
			      <td'.$highlight.'>'.$data2['usr_dept'].'</td>
				  <td'.$highlight.'>'.$data2['heraldid'].'</td>				  
				</tr>';
			}
			
			$html .= '</table>';
			
			//Legend
			$html .= '<br /><table>
			<tr><td class="green"></td><td class="nostyle">Submitted</td></tr>
			<tr><td class="amber"></td><td class="nostyle">Incomplete</td></tr>
			<tr><td class="red"></td><td class="nostyle">Not Submitted</td></tr>
			</table><br />';
			
			if ($_SESSION['pri'] > 2){
				$html .= '<form id="form1" name="deleteRes" method="get" action="">
				<input type="hidden" name="resdel" value="'.$resultset.'" />
				<input type="submit" value="Delete Resultset" />
				</form>';
			}
			
			
	return $html;

	}
	
	function addUser($heraldid, $name, $dept, $email, $pri){
		
		$sql = mysql_query("INSERT INTO `users` ( `heraldid` , `prv_id` , `usr_name` , `usr_dept` , `usr_email` )
		VALUES (
		'".$heraldid."', '".$pri."', '".mysql_real_escape_string($name)."', '".mysql_real_escape_string($dept)."', '".mysql_real_escape_string($email)."'
		)", $this->_local);
		
		if (! $sql){
			return false;
		} else {
			$log = new log(1);
			$log->writeLog("Added User ".$name." (".$heraldid.")");
			return true;
		}
		
		return $html;
		
	}
	
	function deleteUser($heraldid){
		
		$sql = mysql_query("DELETE FROM users WHERE heraldid = '".$heraldid."'", $this->_local);
		
		if (! $sql){
			return false;
		} else {
			$sql2 = mysql_query("select usr_name from users where heraldid = '".$heraldid."'");
			$data = mysql_fetch_array($sql2);
			$log = new log(1);
			$log->writeLog("Deleted User ".$data['usr_name']." (".$heraldid.")");
			return true;
		}
		
	}
	
	function editUser($heraldid, $name, $dept, $email, $pri){
	
	$sql = mysql_query("UPDATE `users` SET `prv_id` = '".$pri."', `usr_name` = '".$name."',
	`usr_dept` = '".$dept."',
	`usr_email` = '".$email."' WHERE `heraldid` = '".$heraldid."'");
	
	if (! $sql){
			return false;
		} else {
			$log = new log(1);
			$log->writeLog("Edited User ".$name." (".$heraldid.")");
			return true;
		}
		
	}
	
	function editStudent($heraldid, $name, $dept, $email){
	
	$sql = mysql_query("UPDATE `users` SET `usr_name` = '".mysql_real_escape_string($name)."',
`usr_dept` = '".mysql_real_escape_string($dept)."',
`usr_email` = '".mysql_real_escape_string($email)."' WHERE `heraldid` = '".$heraldid."'");
	
	if (! $sql){
			return false;
		} else {
			$log = new log(1);
			$log->writeLog("Edited User ".$name." (".$heraldid.")");
			return true;
		}
	
	}

	function editReminder($rem, $date, $msg, $virtual){
	
	$filename = "/srv/www/fgr/templates/default_reminder.txt";
		$file = fopen($filename, "r");
		$default = fread($file, filesize($filename));
		
		//Convert the strings to all printable characters
		$msg2 = urlencode($msg);
		$default2 = urlencode($default);
		
		//Corrects the line feed/carriage return problem
		$msg2 = preg_replace("/%0D%0A/i", "%0A", $msg2);
		
		//print '<h2>Is msg a string? '.is_string($msg2).', len '.strlen($msg2).'</h2>';
		//print '<h2>Is the default a string? '.is_string($default2).', len '.strlen($default2).'</h2>';
				
		$test = strcmp($msg2, $default2);
		
		//print '<h1>'.$msg2.'</h1>';
		//print '<h1>'.$default2.'</h1>';
		
		//print '<h1>'.$test.'</h1>';
	
	//If there is nothing in the textarea, or it's identical to the default
	if (empty($msg) || (! $test)){
		$msg = "NULL";
	}
	
	if ($virtual == null){
	
		//print '<h1>msg = '.$msg.'</h1>';
		
		if (! mysql_query("UPDATE `reminders` SET `rem_date` = FROM_UNIXTIME( '".$date."' ) ,
		`rem_message` = '".mysql_real_escape_string($msg)."' WHERE `reminders`.`rem_id` = ".$rem)){
			return '<p>Cannot edit Reminder.</p>';
		} else {
			$sql = mysql_query("select res_name, rem_date from reminders inner join resultsets on reminders.res_id = resultsets.res_id where rem_id = ".$rem);
			$data = mysql_fetch_array($sql);
			
			$log = new log(1);
			$log->writeLog("Edited Reminder for ".$data['res_name']." (".date("jS M Y", $data['rem_date']).")");
			return '<p>Edited Reminder for '.$data['res_name'].'</p>';
		}
	} else {
		$_SESSION['resultset']['reminders'][$rem]['msg'] = $msg;	
		$_SESSION['resultset']['reminders'][$rem]['remdate'] = $date;
		
		return "<p>Edited Reminder for ".$_SESSION['resultset']['name']."</p>";
	}
	
	/*INSERT INTO `reminders` ( `rem_id` , `res_id` , `rem_date` , `rem_message` )
VALUES (
NULL , '1', NOW( ) , 'hkjlhljkyklhklhklh'
);*/
		
	}
	
	function deleteReminder($rem, $virtual = null){
	
	//echo "select res_name, rem_date from reminders inner join resultsets on reminders.res_id = resultsets.res_id where rem_id = ".$rem;
	
	if ($virtual == null){
		$sql = mysql_query("select res_name, rem_date from reminders inner join resultsets on reminders.res_id = resultsets.res_id where rem_id = ".$rem, $this->_local);
		$data = mysql_fetch_array($sql);
		$log_entry = "Reminder for ".$data['res_name']." (".date("jS M Y", strtotime($data['rem_date'])).")";
	
		$log = new log(1);
		if (! mysql_query("DELETE FROM reminders WHERE rem_id = ".$rem, $this->_local)){
			$log->writeLog("Cannot delete ".$log_entry);
			return '<p>Reminder cannot be deleted.</p>';
		} else {
			$log->writeLog("Deleted ".$log_entry);
			return '<p>Reminder has been deleted.</p>';
		}
	} else {
		unset($_SESSION['resultset']['reminders'][$rem]);
		return '<p>Reminder has been deleted.</p>';
	}

}

function returnUser($heraldid){

$sql = $this->selectQuery("usr_name, usr_dept, usr_email, prv_id", "users", "heraldid = '".$heraldid."'");

if (! $sql){
			return false;
		} else {
				$data = mysql_fetch_array($sql);
				$array[] = $data['usr_name'];
				$array[] = $data['usr_dept'];
				$array[] = $data['usr_email'];
				$array[] = $data['prv_id'];
				return $array;
			
			
			}





}

function insertNotice($title, $detail, $prv){

	$arr_detail = str_split($detail, 3);
	if ($detail[0] != '<p>'){
		$detail = '<p>'.$_POST['detail'].'</p>';
	}
		
$sql = mysql_query("INSERT INTO `notices` ( `not_id` , `not_title` , `not_detail` , `not_date` , `not_prv` )
VALUES (
NULL , '".$title."', '".mysql_real_escape_string($detail)."', NOW( ) , '".$prv."'
)", $this->_local);

if (! $sql){
	$log = new log(1);
	$log->writeLog("Cannot add Notice ".$title);
	return false;
} else {
	$log = new log(1);
	$log->writeLog("Added Notice ".$title);
	return true;
}

}

function updateNotice($id, $title, $detail, $prv){

	$arr_detail = str_split($detail, 3);
	if ($detail[0] != '<p>'){
		$detail = '<p>'.$_POST['detail'].'</p>';
	}
	
$sql = mysql_query("UPDATE `notices` SET `not_title` = '".$title."',
`not_detail` = '".mysql_real_escape_string($detail)."',
`not_date` = NOW( ) ,
`not_prv` = '".$prv."' WHERE `notices`.`not_id` =".$id." LIMIT 1", $this->_local);

if (! $sql){
	$log = new log(1);
	$log->writeLog("Cannot edit Notice ".$title);
	return false;
} else {
	$log = new log(1);
	$log->writeLog("Edited Notice ".$title);
	return true;
}

}

	function __destruct() {
	//	print "<p>Attempting to close MySQL connection...</p>";
		if (! mysql_close()) {
			return "Unable to disconnect from database.";
		}
	}
	
	function repairTable($table){
		if (! mysql_query("REPAIR TABLE ".$table, $this->_local)){
			return false;
		} else {
			return true;
		}
	}
	
	function optimiseTable($table){
		if (! mysql_query("OPTIMIZE TABLE ".$table, $this->_local)){
			return false;
		} else {
			return true;
		}
	}
	
	function deleteResultset($res){
	
	if (! isset($_POST['delete'])){
	
	$html = '<p>Are you sure you wish to delete this Resultset?</p>
	<p>Existing results will be withdrawn, and students will be notified.</p>	
		<form name="select_res" method="post" action="./?resdel='.$res.'">
			<input type="submit" name="delete" value="Delete Resultset" />
		</form>';
	
	} else {
	
		$log = new log(1);
		
		$sql2 = $this->selectQuery("res_name, heraldid, usr_name, usr_email", "resultsets
INNER JOIN users ON resultsets.heraldid = users.heraldid", "res_id = ".$res);

		while ($data2 = mysql_fetch_array($sql2)){
			$reply = $data2['usr_name'].' <'.$data2['usr_email'].'>';
			$title = $data2['res_name'];
		}
		
		if (! mysql_query("DELETE FROM resultsets WHERE res_id = ".$res, $this->_local)){
			$log->writeLog("Cannot delete ".$title);
			return $fail;
		}
		
		$log->writeLog("Deleted ".$title);
		
		if (! mysql_query("DELETE FROM resultsetfieldsdata WHERE res_id = ".$res, $this->_local)){
			$log->writeLog("Cannot delete data for ".$title);
			return $fail;
		}
		
		$log->writeLog("Deleted data for ".$title);
		
		if (! mysql_query("DELETE FROM resultsetfields WHERE res_id = ".$res, $this->_local)){
			$log->writeLog("Cannot delete fields for ".$title);
			return $fail;
		}
		
		$log->writeLog("Deleted fields for ".$title);
				
		if (! mysql_query("DELETE FROM reminders WHERE res_id = ".$res, $this->_local)){
			$log->writeLog("Cannot delete Reminders for ".$title);
			return $fail;
		}
		
		$log->writeLog("Deleted Reminders for ".$title);
		
		$log->writeLog("Cannot send email to ".$data['usr_name']." (".$data['heraldid'].") for ".$title);
		
		$sql = $this->selectQuery("resultsetstudents.heraldid, usr_name, usr_email", "users INNER JOIN resultsetstudents ON resultsetstudents.heraldid = users.heraldid", "res_id = ".$res);
		$email = new email();
		$subject = "Your Results have been withdrawn";
		$filename = "/srv/www/fgr/templates/default_withdrawn.txt";
		$file = fopen($filename, "r");
		$message = fread($file, filesize($filename));
				
		while ($data = mysql_fetch_array($sql)){
			$to = $data['usr_name']." (".$data['heraldid'].") <".$data['usr_email'].">";
			if (!$email->send($to, $reply, $subject, $message)){
					$log->writeLog("Cannot send email to ".$data['usr_name']." (".$data['heraldid'].") for ".$title);
				return $fail;
			}
		}
		
		if (! mysql_query("DELETE FROM resultsetstudents WHERE res_id = ".$res, $this->_local)){
			$log->writeLog("Cannot unlink Students for ".$title);
			return $fail;
		}
		
		$log->writeLog("Unlinked Students for ".$title);
	
	$html = '<p>Resultset successfully deleted.</p>';
	
	}
	
		return $html;
	
	}	
	
	}



?>