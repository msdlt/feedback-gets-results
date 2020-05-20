<?php

class reminder {

	function writeSession($remdate){
	
		if (! isset($_SESSION['resultset']['reminders'])){
			$_SESSION['resultset']['reminders'][0]['remdate'] = $remdate;
			$_SESSION['resultset']['reminders'][0]['msg'] = null;
		} else {
			$i = sizeof($_SESSION['resultset']['reminders']);
			$_SESSION['resultset']['reminders'][$i]['remdate'] = $remdate;
			$_SESSION['resultset']['reminders'][$i]['msg'] = null;
		}
				
	}
	
	function buildReminders($reminderdata, $type = null){
		
		if (! isset($reminderdata) || $reminderdata == null){
			$html = 'No Reminders currently set.';
		} else {
						
			//print_r($_SESSION['resultset']['reminders']);
			
			$html = '<table><tr>
				<th><div class="th">Date</div></th>
				<th><div class="th">Message</div></th></tr>';
				
			$i = 0;
			
			foreach($reminderdata as $reminder){
				
				if (isset($type)){
					$html .= '<tr><td><a href="./?res&add&rem='.$i.'">'.date('jS M y', $reminder['remdate']).'</a></td>';
				} else {
					$html .= '<tr><td>'.date('jS M y', $reminder['remdate']).'</td>';
				}
													
					if ($reminder['msg'] == null){
						$msg = '<em>Default</em>';
					} else {
						$msg = '<strong>Custom</strong>';
					}
					
				$html .= '<td>'.$msg.'</td>';				
				
				
				
				$i++;
				
				$html .= '</tr>';
				
			}
			
			$html .= '</table><br />';
			
		}

	return $html;
		
	}
	
	function availReminders($surveyinstance, $date){
	
		//$html .= '<option value="'.$date.'">Date sent is '.date("jS M Y", $date).'</option>';
		
		//Fetch remaining duration of Survey Instance
		$resultset = new remote();
		$remaining = $resultset->daysRemaining($surveyinstance);
		
		//For each day from current to remaining
		for ($i=1; $i<=$remaining; $i++){
			
			$highlight = '';
			if (($i / 7) == 1){
				$duration = ($i / 7)." week";
				$highlight = ' class="highlight"';
			} elseif (($i % 7) == 0){
				$duration = ($i / 7)." weeks";
				$highlight = ' class="highlight"';
			} elseif ($i != 1){
				$duration = $i." days";
			} else {
				$duration = $i." day";
			}
			
			$vardate = time() + ($i * (60 * 60 * 24));
			if ($date != null){
				if (! strcmp(date("jS M Y", $vardate), date("jS M Y", $date))){
					$highlight .= ' selected="selected"';
				}
			}
			
			$html .= '<option'.$highlight.' value="'.$vardate.'">'.date('jS M y', (time() + ($i * (60 * 60 * 24)))).' ('.$duration.')</option>';		
			
		}
		
		return $html;
	
	}
	
	function selectReminders($res, $surveyinstance){
	
		$html = '<form name="select_res" method="post" action="./?rem=null&add">
		<select name="date">';
		
		$html .= $this->availReminders($surveyinstance);
		
		$html .= '</select>
			<input type="hidden" name="res" value="'.$res.'" />
			<input type="submit" name="Submit" value="Select" />
		</form>';
		
		return $html;
	
	}
	
	function userRangeReminders($heraldid = null){
	
		$local = new local();
		$sql = $local->selectQuery("heraldid, usr_name", "users", "prv_id > 1");
		$html = '<p><form action="./" method="get" name="showrem">
		<input name="rem" type="hidden" value="null" />
		<select name="user" id="user">';
		
		while ($data = mysql_fetch_array($sql)){
			//If the user is not set, assume the first user
			if ($heraldid == null){
				$heraldid = $data['heraldid'];
			}
			if ($data['heraldid'] == $heraldid){
				$checked = ' selected="selected"';
				$view = $data['usr_name'];
			} else {
				$checked = '';
			}
			
			$html .= '<option value="'.$data['heraldid'].'"'.$checked.'>'.$data['usr_name'].'</option>';		
		}
		
		$html .= '</select>
		  <input type="submit" value="View" />
		</form></p><p><i>Viewing </i>'.$view.'</p>';
		return $html;
	
	}	
	
	function readAllReminders($heraldid = null){
	
	$local = new local();
	$sql = $local->selectQuery("rem_id, res_name, rem_date, rem_message", "reminders
	INNER JOIN resultsets ON reminders.res_id = resultsets.res_id", "heraldid='".$heraldid."'");
	//rem_date > 0 and rem_date < now();
	
	$html = $this->userRangeReminders($heraldid);
	
	if (mysql_num_rows($sql) == 0){
	
	$html .= '<p>No Reminders to display.</p>';
	
	} else {
	
	$html .= '<table><tr><th><div class="th">Resultset</div></th>
	<th><div class="th">Date</div></th>
	<th><div class="th">Message</div></th></tr>';
	
	while ($data = mysql_fetch_array($sql)){
	
		if ($data['rem_message'] == "NULL"){
			$msg = '<em>Default</em>';
		} else {
			$msg = '<strong>Custom</strong>';
		}
		
		$html .= '<tr><td>'.$data['res_name'].'</td>
		<td><a href="./?rem='.$data['rem_id'].'">'.date("jS M Y", strtotime($data['rem_date'])).'</a></td>
		<td>'.$msg.'</td>
		</tr>';
	
	}

	$html .= '</table>';
	
	}
	return $html;
	
	}
	
	function readReminders($heraldid){
	
	$local = new local();
	$sql = $local->selectQuery("res_id, res_name", "resultsets", "heraldid = '".$heraldid."'");
	while ($data = mysql_fetch_array($sql)){
		$html .= '<p><strong>'.$data['res_name'].'</strong></p>';
		$sql2 = $local->selectQuery("rem_id, rem_date, rem_message", "reminders", "res_id = ".$data['res_id']);
		if (mysql_num_rows($sql2) == 0){
			$html .= '<p>No reminders for this resultset</p>';
		} else{
		$html .= '<table><tr><th><div class="th">Resultset</div></th>
	<th><div class="th">Date</div></th>
	<th><div class="th">Message</div></th></tr>';
			while ($data2 = mysql_fetch_array($sql2)){
				if ($data2['rem_message'] == "NULL"){
					$msg = '<em>Default</em>';
				} else {
					$msg = '<strong>Custom</strong>';
				}
		
				$html .= '<tr><td>'.$data['res_name'].'</td>
		<td><a href="./?rem='.$data2['rem_id'].'">'.date("jS M Y", strtotime($data2['rem_date'])).'</a></td>
		<td>'.$msg.'</td>
		</tr>';
			}
		$html .= '</table>';			
		}
	}

return $html;	
	
	}
	
	function formReminder($rem, $virtual){
	
	if ($virtual == null){
		$action = "./?rem=".$rem."&edit";
		$local = new local();
		$sql = $local->selectQuery("rem_date, rem_message, surveyinstanceid", "reminders inner join resultsets on reminders.res_id = resultsets.res_id", "rem_id = ".$rem);
		while ($data = mysql_fetch_array($sql)){
			//$date = $data['rem_date'];
			$msg = $data['rem_message'];
			$datelist = $this->availReminders($data['surveyinstanceid'], strtotime($data['rem_date']));
			$res = '</form><br /><form id="form2" name="editRem2" method="post" action="./?rem='.$rem.'&del">
			<input type="submit" name="Submit" value="Delete Reminder"></form>';
		}
	} else {
		$action = "./?add&res&rem=".$rem."&edit";
		$msg = $_SESSION['resultset']['reminders'][$rem]['msg'];
		$datelist = $this->availReminders($_SESSION['resultset']['surveyInstance'], $_SESSION['resultset']['reminders'][$rem]['remdate']);
		$res = '<input type="hidden" name="addres" value="3"><input name="compulsory" type="hidden" value="1" /></form><br /><form action="./?res&add&rem='.$rem.'&del" method="post" name="delrem">
						<input type="submit" name="Submit" value="Delete Reminder"><input name="compulsory" type="hidden" value="1" />
<input name="addres" type="hidden" value="3" />
						</form>';
	}
	
	if ($msg == 'NULL' || $msg == ''){
		$filename = "/srv/www/fgr/templates/default_reminder.txt";
		$file = fopen($filename, "r");
		$default = fread($file, filesize($filename));
		$msg = $default;
		}
	
	if (! $template = fopen(site_root."templates/rem_edit.html", "r")){
					$page = '<p>Template cannot be found</p>'; //Replace with better error messages
				} else {
	$page = fread($template, filesize(site_root."templates/rem_edit.html"));
	$page = preg_replace("/{{date}}/i", $datelist, $page);
	$page = preg_replace("/{{msg}}/i", $msg, $page);
	$page = preg_replace("/{{res}}/i", $res, $page);
	$page = preg_replace("/{{action}}/i", $action, $page);
	}
	
	return $page;
		
	}
	
	function unixDateReminder($rem, $virtual = null){
	//Converts a rem_id to unix timestamp
	
	if ($virtual == null){
		$local = new local();
		$sql = $local->selectQuery("rem_date", "reminders", "rem_id = ".$rem);
		$data = mysql_fetch_array($sql);
		return strtotime($data['rem_date']);
	} else {
		return $_SESSION['resultset']['reminders'][$rem]['remdate'];
	}
		
	}
	
	
	
}