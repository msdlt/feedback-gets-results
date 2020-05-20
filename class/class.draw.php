<?php

/* class.draw.php
The draw class contains all methods required for rendering the site */

class draw {
	
	private $_html;
	
	function __construct(){
		$this->_html = '';
	}
	
	function addToPage($content){
		$this->_html .= $content;
	}
	
	function buildAddRes($addres = null){
		
		switch($addres){
			case "1":
				//If there is a spreadsheet
				$file = new excel();	
				
				$_SESSION['resultset'] = $file->upload();
				
				$_SESSION['resultset']['name'] = $_POST['name'];
				$_SESSION['resultset']['excel'] = $_FILES['excel']['name'];
								
				if (! $template = fopen(site_root."templates/addres2.html", "r")){
					$element = '<p>Template cannot be found</p>'; //Replace with better error messages
				} else {
					$selected = ' selected';
										
					$surveys = new remote();
					$surveyinstances = $surveys->surveyInstances();					
					
					if ($surveyinstances == false){
						$surveyinstances = '<p><em>No Survey Instances are available. This will be either 
						because there are no Surveys or Survey Instances in the Feedback System, or because 
						all available Survey Instances are in use by Resultsets in Feedback gets Results.</em></p>
						<p><strong>You cannot add a Resultset without linking to a Survey Instance, please create a Survey Instance before trying again.</strong></p>';
						$submit = '';
					} else {
						$submit = '<input type="submit" name="Submit" value="Next" />
	    <input type="hidden" name="addres" value="2" />';
					}
					
					$element = fread($template, filesize(site_root."templates/addres2.html"));
					$element = preg_replace("/{{survey}}/i", $surveyinstances, $element);
					$element = preg_replace("/{{submit}}/i", $submit, $element);
				}
			break;
			case "2":
			$_SESSION['resultset']['surveyInstance'] = $_POST['select'];
			$_SESSION['resultset']['compulsory'] = 0;
			
				if (! $template = fopen(site_root."templates/addres3-1.html", "r")){
						$element = '<p>Template cannot be found</p>'; //Replace with better error messages
					} else {
						$element = fread($template, filesize(site_root."templates/addres3-1.html"));
						$element = preg_replace("/{{survey}}/i", $surveyinstances, $element);
					}
			break;
			case "3-1":
			//print_r($_SESSION['resultset']['reminders']);
				$_SESSION['resultset']['compulsory'] = 1;
				if (! $template = fopen(site_root."templates/addres3-2.html", "r")){
					$element = '<p>Template cannot be found</p>'; //Replace with better error messages
				} else {
					$reminder = new reminder();
					$remdata = $reminder->availReminders($_SESSION['resultset']['surveyInstance'], null);
					$reminders = $reminder->buildReminders($_SESSION['resultset']['reminders'], "list");
					
					$test = new remote;
					$todate = $test->unixRemaining($_SESSION['resultset']['surveyInstance']);
					if ($todate != 0){
						$todatestring = date('jS M Y', $todate);
					} else {
						$todatestring = "unknown";
					}
					$remstats = 'Resultset active until '.$todatestring.', '.$test->daysRemaining($_SESSION['resultset']['surveyInstance'])." days (".floor($test->daysRemaining($_SESSION['resultset']['surveyInstance']) / 7)." weeks) remaining."; //add to
				
					$element = fread($template, filesize(site_root."templates/addres3-2.html"));
					$element = preg_replace("/{{survey}}/i", $surveyinstances, $element);
					$element = preg_replace("/{{reminders}}/i", $reminders, $element);
					$element = preg_replace("/{{remstats}}/i", $remstats, $element);
					$element = preg_replace("/{{remdata}}/i", $remdata, $element);
				}
			break;
			case "3-2":
				if (! $template = fopen(site_root."templates/addres4.html", "r")){
					$element = '<p>Template cannot be found</p>'; //Replace with better error messages
				} else {
				//print_r($_SESSION['resultset']);
					$element = fread($template, filesize(site_root."templates/addres4.html"));
					$element = preg_replace("/{{resultset}}/i", $_SESSION['resultset']['excel'], $element);
					
					$survey = new remote();
					$element = preg_replace("/{{surveyinstance}}/i", $survey->surveyName($_SESSION['resultset']['surveyInstance']), $element);
					
					if ($_SESSION['resultset']['compulsory'] == 0){
						$element = preg_replace("/{{reminders}}/i", "", $element);
						$element = preg_replace("/{{compulsory}}/i", "Feedback is optional.", $element);
					} else {
						$reminder = new reminder();
						$reminders = '<strong>Reminders</strong></p><p>';
						$reminders .= $reminder->buildReminders($_SESSION['resultset']['reminders']);
						$element = preg_replace("/{{reminders}}/i", $reminders, $element);
						$element = preg_replace("/{{compulsory}}/i", "Feedback is compulsory.", $element);					
					}
				}
			break;
			case "4":
				if (! $template = fopen(site_root."templates/addres5.html", "r")){
					$element = '<p>Template cannot be found</p>'; //Replace with better error messages
				} else {
				
					$resultset = new local();
					$resultset->addResultset($_SESSION['resultset']);
										
					$update = $this->showUpdateResultset($_SESSION['resultset']['progress']);
					$_SESSION['resultset']['progress']++;
					
					$element = fread($template, filesize(site_root."templates/addres5.html"));
					$element = preg_replace("/{{update}}/i", $update, $element);
				}
			break;
			default:
				if (! $template = fopen(site_root."templates/addres1.html", "r")){
					$element = '<p>Template cannot be found</p>'; //Replace with better error messages
				} else {
					$element = fread($template, filesize(site_root."templates/addres1.html"));
					/*$element = preg_replace("/{{hello}}/i", $hello, $element);
					$element = preg_replace("/{{date}}/i", $date, $element);
					$element = preg_replace("/{{content}}/i", $content, $element);*/
				}
			break;		
		}
		//print "is buildaddres still running?";
		return $element;
	}
	
	function buildDefault($pri){
		
		$notice = new notice($pri);
		$notices = $notice->display();
		
		switch($pri){
			//Super Administrator
			case 3:
				if (! $template = fopen(site_root."templates/default_superadmin.html", "r")){
					$element = '<p>Template cannot be found</p>'; //Replace with better error messages
				} else {
					$element = fread($template, filesize(site_root."templates/default_superadmin.html"));
					$element = preg_replace("/{{resultsets}}/i", $notices.$this->listResultsetsAll(), $element);
				}
			break;
			//Administrator
			case 2:
				if (! $template = fopen(site_root."templates/default_admin.html", "r")){
					$element = '<p>Template cannot be found</p>'; //Replace with better error messages
				} else {
					$element = fread($template, filesize(site_root."templates/default_admin.html"));
					$element = preg_replace("/{{resultsets}}/i", $notices.$this->listResultsets($_SESSION['user']), $element);
				}				
			break;
			//Student
			default:
				if (! $template = fopen(site_root."templates/default_student.html", "r")){
					$element = '<p>Template cannot be found</p>'; //Replace with better error messages
				} else {
					$element = fread($template, filesize(site_root."templates/default_student.html"));
					$element = preg_replace("/{{resultsets}}/i", $notices.$this->listResultsetsStudentAll($_SESSION['user']), $element);
				}				
			break;
		}
		
		return $element;	
	
	}
	
	function buildArchive($pri){
	if (isset($_GET['arch'])){
		//If archive trigger is set but no value, assign default of 1
		if ($_GET['arch'] == "" || $_GET['arch'] == null){
			$arch = 1;
		//Fetch archive value from GET
		} else {
			$arch = $_GET['arch'];
		}
	//If archive trigger is not set, set to null
	} else {
		$arch = null;
	}
	switch($pri){
		//Super Administrator
		case 3:
			if (! $template = fopen(site_root."templates/default_superadmin.html", "r")){
				$element = '<p>Template cannot be found</p>'; //Replace with better error messages
			} else {
				$element = fread($template, filesize(site_root."templates/default_superadmin.html"));
				//$element = preg_replace("/{{resultsets}}/i", $this->dateRangeResultsets($arch, $_GET['year'], $_GET['month']).$this->listResultsetsAll($arch), $element);
				$element = preg_replace("/{{resultsets}}/i", $this->drawResultsets($pri, $arch, $_SESSION['user'], $_GET['year'], $_GET['month']), $element);
			}
		break;
		//Administrator
		case 2:
			if (! $template = fopen(site_root."templates/default_admin.html", "r")){
				$element = '<p>Template cannot be found</p>'; //Replace with better error messages
			} else {
				$element = fread($template, filesize(site_root."templates/default_admin.html"));
				//$element = preg_replace("/{{resultsets}}/i", $this->dateRangeResultsets(null, $_GET['year'], $_GET['month']).$this->listResultsets($_SESSION['user'], $arch), $element);
				$element = preg_replace("/{{resultsets}}/i", $this->drawResultsets($pri, null, $_SESSION['user'], $_GET['year'], $_GET['month']), $element);
			}				
		break;
		//Student
		default:
			//No go area
		break;
	}
	
		return $element;
	}
	
	//drawResultsets($pri, $arch, $_SESSION['user'], $_GET['year'], $_GET['month']);
	
	function drawResultsets($pri, $arch = null, $user = null, $dateyear = null, $datemonth = null){
	
		switch($pri){
		//Super Administrator
		case 3:
			$html = $this->dateRangeResultsets($arch, $dateyear, $datemonth);
			if ($dateyear != null && $datemonth != null){
				$html .= $this->listResultsetsAll($dateyear, $datemonth);
			}
		break;
		//Administrator
		case 2:
			$html = $this->dateRangeResultsets($arch, $dateyear, $datemonth);
			if ($dateyear != null && $datemonth != null){
				$html .= $this->listResultsets($user, $dateyear, $datemonth);
			}
		break;
		//Student
		default:
			//No go area
		break;
		
		}
		
		return $html;
	
	}
	
	function dateRangeResultsets($arch = null, $dateyear = null, $datemonth = null){
	
	$local = new local();
	//$remote = new remote();
	/*$sql3 = $remote->selectQuery("surveyInstanceID", "SurveyInstances", "finishDate > NOW() OR finishDate IS NULL");
	while ($data3 = mysql_fetch_array($sql3)){
		$currentsurveys[] = $data3['surveyInstanceID'];
	}*/
	
	/*if ($_SESSION['pri'] < 3){
		$sql3 = $local->selectQuery("res_id", "resultsets", "heraldid = ".$_SESSION['user']);
	} else {
		$sql3 = $local->selectQuery("res_id", "resultsets");
	}
	
	while ($data3 = mysql_fetch_array($sql3)){
		$currentres[] = $data3['res_id'];
	}*/
	
	
	if (isset($datemonth)){
		$reset = ' <em><a href="./?arch">Reset</a></em>';
		$showdate = '<strong>'.date("M", mktime(0, 0, 0, $datemonth)).' '.$dateyear.'</strong>';
	} elseif (isset($dateyear)){
		$reset = ' <em><a href="./?arch">Reset</a></em>';
		$showdate = '<strong>'.$dateyear.'</strong>';
	}
		
	$html = '<form id="form1" name="form1" method="get" action="./">';
	$html .= '<p>Please select the date: <strong>'.$showdate.'</strong>'.$reset.'</p>';
	
	if ($dateyear == null){
	
	if ($_SESSION['pri'] < 3){
		$sql_alldates = $local->selectQuery("DISTINCT year(res_timestamp)", "resultsets ORDER BY res_timestamp DESC", "heraldid = ".$_SESSION['user']);
	} else {
		$sql_alldates = $local->selectQuery("DISTINCT year(res_timestamp)", "resultsets ORDER BY res_timestamp DESC");
	}
	
	while ($alldates = mysql_fetch_array($sql_alldates)){
		//if (! array_search($alldates['res_id'], $currentres)){
			$years[] = 	$alldates['year(res_timestamp)'];
		//}
	}
	
	//print_r($years);
	
	$min = min($year);
	$html .= '<select name="year" id="year">';
	
	foreach ($years as $year){
		//print "if ".$year." = ".max($years).": ";
		if ($year == max($years)){
			$option = 'Now - '.$year;
		} else {
			$option = ($year + 1).' - '.$year;
		}
		$html .= '<option value="'.$year.'">'.$option.'</option>';
	}
		
		$html .= '</select>
		   <input type="submit" value="Year" />
		   <input type="hidden" name="arch" value="null" />
		  </form>';
		  
		if (mysql_num_rows($sql_alldates) == 0){
			$html = 'No Resultsets found.';
		}
		
	} else {
	if ($_SESSION['pri'] < 3){
		$sql_alldates = $local->selectQuery("DISTINCT month(res_timestamp)", "resultsets", "year( res_timestamp ) = ".$dateyear." ORDER BY res_timestamp ASC", "heraldid = ".$_SESSION['user']);
	} else {
		$sql_alldates = $local->selectQuery("DISTINCT month(res_timestamp)", "resultsets", "year( res_timestamp ) = ".$dateyear." ORDER BY res_timestamp ASC");
	}
	while ($alldates = mysql_fetch_array($sql_alldates)){
		//if (! array_search($allmonths['res_id'], $currentres)){
			$months[] = $alldates['month(res_timestamp)'];
		//}
	}
	
	$min = min($months);
	$html .= '<select name="month" id="month">';
	
	foreach($months as $month){
		if($datemonth == $month){
			$selected = ' selected="selected"';
		} else {
			$selected = '';
		}
	
		$html .= '<option value="'.$month.'"'.$selected.'>'.date("M", mktime(0, 0, 0, $month)).'</option>';
	}	
	
	$html .= '</select>
			   <input type="hidden" name="year" value="'.$dateyear.'" />
			   <input type="hidden" name="arch" value="null" />
			   <input type="submit" value="Month" />';
			   		   			   
			  $html .= '</form>';
	
	if (mysql_num_rows($sql_alldates) == 0){
			$html = 'No Resultsets found.';
		}
	
	}
	
	
	/*$html = '<form id="form1" name="form1" method="get" action="./">
		
		  <label>
		  <select name="arch" id="arch">';
		
		$remote = new remote();
		$sql5 = $remote->selectQuery("min(finishDate)", "SurveyInstances");
		$data = mysql_fetch_array($sql5);
		$min = strtotime($data['min(finishDate)']);
		$max = time();
		$year_max = ceil( ($max - $min) / 60 / 60 / 24 / 365 );
		$unixyear = 31536000;
		
		for ($year=1;$year<=$year_max;$year++){
			if ($arch != null && $arch == $year){
				$checked = ' selected="selected"';
			}else{
				$checked = '';
			}
			if ($year == 1){
				$msg = 'Present - '.date('jS M Y', $max - ($year * $unixyear));
				$html .= '<option value="1"'.$checked.'>'.$msg.'</option>';
			} elseif ($year == $year_max){
				$msg = date('jS M Y', $max - ( ($year-1) * $unixyear)).' - '.date('jS M Y', $min);
				$html .= '<option value="'.$year.'"'.$checked.'>'.$msg.'</option>';
			} else {
				$msg = date('jS M Y', $max - ( ($year-1) * $unixyear)).' - '.date('jS M Y', $max - ($year * $unixyear));
				$html .= '<option value="'.$year.'"'.$checked.'>'.$msg.'</option>';
			}
			if ($arch != null && $arch == $year){
				$view = $msg;
			}
		}		
		
		$html .= '</select>
		  </label>
		  <label>
		  <input type="submit" value="View" />
		  </label>
		</form><p><i>Viewing </i>'.$view.'</p>';*/
		
		return $html;
		
	}
	
	function selectResultsets($user){
	
		$local = new local();
	
		if ($_SESSION['pri'] < 3){
			$sql = $local->selectQuery("res_id, res_name", "resultsets", "heraldid = '".$user."'");
		} else {
			$sql = $local->selectQuery("res_id, res_name", "resultsets");
		}
	
		$html = '<form name="select_res" method="post" action="./?rem=null&add">
		<select name="res">';
		
		while ($data = mysql_fetch_array($sql)){
			$html .= '<option value="'.$data['res_id'].'">'.$data['res_name'].'</option>';
		}
		
		$html .= '</select>
			<input type="submit" name="Submit" value="Select" />
		</form>';
		
		if (mysql_num_rows($sql) == 0){
			return "<p>No Resultsets Found</p>";
		} else {
			return $html;
		}
	
	}	
	
	function listResultsets($user, $dateyear = null, $datemonth = null){
		$local = new local();
		$remote = new remote();
		
		if ($dateyear != null || $datemonth != null){
			$sql = $local->selectQuery("res_id, res_name, res_timestamp, surveyinstanceid", "resultsets", "heraldid = '".$user."' AND YEAR(res_timestamp) = ".$dateyear." AND MONTH(res_timestamp) = ".$datemonth);
			//print "res_id, res_name, res_timestamp, surveyinstanceid resultsets heraldid = '".$user."' AND YEAR(res_timestamp) = ".$dateyear." AND MONTH(res_timestamp) = ".$datemonth;
		} else {
			$sql = $local->selectQuery("res_id, res_name, res_timestamp, surveyinstanceid", "resultsets", "heraldid = '".$user."'");
		}
				
		$html = '<table>
	    <tr>
	      <th><div class="th">Course name</div></th>
		  <th><div class="th">Feedback</div></th>
		  <th width="20%"><div class="th">Released</div></th>
	      <th width="20%"><div class="th">Available until</div></th>
	    </tr>';
		
		//$sql = mysql_query("SELECT res_name, res_timestamp FROM resultsets WHERE heraldid = '".$user."'");
		$flag = 0;
		while ($data = mysql_fetch_array($sql)){
			
			$sql6 = $local->selectQuery("users.heraldid, usr_name, usr_dept, usr_email, prv_id", 
			"users inner join resultsetstudents on users.heraldid = resultsetstudents.heraldid", "res_id = ".$data['res_id']);
			
			$sql7 = $remote->selectQuery("surveyInstanceID", "SurveyInstances", "surveyInstanceID = ".$data['surveyinstanceid']); //Check if can find in Feedback System
			if (mysql_num_rows($sql7) == 0){
				$data5 = array();
				$x = "?";
				$finishDate = "Unknown";
				$finishDateRaw = null;
			} else {
				$sql5 = $remote->selectQuery("heraldID", "SurveyInstanceParticipants", "surveyInstanceID = ".$data['surveyinstanceid']." AND status = 2"); //RABBIT
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
				
				$sql3 = $remote->selectQuery("finishDate", "SurveyInstances", "surveyInstanceID = ".$data['surveyinstanceid']);
				while ($data3 = mysql_fetch_array($sql3)){
					if ($data3['finishDate'] == ''){
						$finishDate = "Unknown";
						$finishDateRaw = null;
					} else {
						$finishDate = date("jS M Y", strtotime($data3['finishDate']));
						$finishDateRaw = $data3['finishDate'];
					}
					
				}
				
			}
			
			$y = $local->countQuery("heraldid", "resultsetstudents", "res_id = ".$data['res_id']);
			if ($y == 0){
				$y = "?";
				$percent = "?";
			} else {
				$percent = round((($x/$y) * 100), 2);
			}			
				//echo "<h1>HAVE I RUN?</h1>";		
			if ($dateyear != null || $datemonth != null){
				
				//If the finish date has passed, or it cannot be found
				//if (strtotime($finishDateRaw) < time()){
				$html .= '<tr>
				      <td><a href="./?res='.$data['res_id'].'&view">'.$data['res_name'].'</a></td>
					  <td>'.$x.' <em>(of '.$y.')</em> '.$percent.'%</td>
				      <td>'.date("jS M Y", strtotime($data['res_timestamp'])).'</td>
					  <td>'.$finishDate.'</td>
				    </tr>';
					$flag++;
					//if (mysql_num_rows($sql7) == 0){ //If surveyinstance cannot be found
					/*$html .= '<tr>
				      <td><a href="./?res='.$data['res_id'].'&view">'.$data['res_name'].'</a></td>
					  <td>'.$x.' <em>(of '.$y.')</em> %</td>
				      <td>'.date("jS M Y", strtotime($data['res_timestamp'])).'</td>
					  <td>'.$finishDate.'</td>
				    </tr>';*/
					//} else {
					/*$html .= '<tr>
				      <td><a href="./?res='.$data['res_id'].'&view">'.$data['res_name'].'</a></td>
					  <td>'.$x.' <em>(of '.$y.')</em> '.round((($x/$y) * 100), 2).'%</td>
				      <td>'.date("jS M Y", strtotime($data['res_timestamp'])).'</td>
					  <td>'.date("jS M Y", strtotime($finishDate['finishDate'])).'</td>
				    </tr>';
					//}*/
					//$flag++;
				//}
			} else {
				//If not archive, display all unfinished SurveyInstances
				if (strtotime($finishDateRaw) > time() || $finishDateRaw == null){
					$html .= '<tr>
				      <td><a href="./?res='.$data['res_id'].'&view">'.$data['res_name'].'</a></td>
					  <td>'.$x.' <em>(of '.$y.')</em> '.$percent.'%</td>
				      <td>'.date("jS M Y", strtotime($data['res_timestamp'])).'</td>
					  <td>'.$finishDate.'</td>
				    </tr>';
					$flag++;
				}
			}
			
		}
		$html .= '</table>';
		
		if ($flag == 0){
			return '<p>No Courses found</p>';
		} else {		
			return $html;
		}
	
	}
	
	function listResultsetsAll($dateyear = null, $datemonth = null){
	
		$local = new local();
		$sql = $local->selectQuery("heraldid, usr_name, usr_dept", "users", "prv_id > 1");
		
		while ($data = mysql_fetch_array($sql)){
			$html .= '<p><strong>'.$data['usr_name'].', '.$data['usr_dept'].'</strong></p>';
			if ($dateyear != null || $datemonth != null){
				$html .= $this->listResultsets($data['heraldid'], $dateyear, $datemonth);
			} else {
				$html .= $this->listResultsets($data['heraldid']);
			}
		}

		return $html;
	
	}
	
	function listResultsetsStudentAll($user){
	//Finds all resultsets for a particular student
	
		//Check remote database to see how many Survey Instances have been completed by the user
		$local = new local();
		$remote = new remote();
		$sql2 = $remote->selectQuery("i.surveyInstanceID", 
		"SurveyInstanceParticipants as p INNER JOIN SurveyInstances as i ON p.surveyInstanceID = i.surveyInstanceID", 
		"heraldID = '".$user."' AND status = 2 AND finishDate > NOW() OR heraldID = '".$user."' AND status = 2 AND finishDate IS NULL");
		/*$sql4 = $remote->selectQuery("i.surveyInstanceID", 
		"SurveyInstanceParticipants as p INNER JOIN SurveyInstances as i ON p.surveyInstanceID = i.surveyInstanceID", 
		"heraldID = '".$user."' AND status = 1 AND finishDate > NOW() OR heraldID = '".$user."' AND status = 1 AND finishDate IS NULL");*/
		/*$sqlall = $remote->selectQuery("i.surveyInstanceID", 
		"SurveyInstanceParticipants as p INNER JOIN SurveyInstances as i ON p.surveyInstanceID = i.surveyInstanceID", 
		"heraldID = '".$user."' AND finishDate > NOW() OR heraldID = '".$user."' AND finishDate IS NULL");
		while ($magnacarta = mysql_fetch_array($sqlall)){
			$allsurveys[] = $magnacarta['surveyInstanceID'];
		}*/

			//echo "<h2>SELECT i.surveyInstanceID FROM SurveyInstanceParticipants as p INNER JOIN SurveyInstances as i ON p.surveyInstanceID = i.surveyInstanceID WHERE heraldID = '".$user."' AND status = 2 AND finishDate > NOW() OR heraldID = '".$user."' AND status = 2 AND finishDate IS NULL</h2>";
		
		//If there are any do stuff
		$x = mysql_num_rows($sql2);

		if ($x > 0){
		
			
			//Create SQL string for IN comparison in WHERE clause
			while ($surveyinstances = mysql_fetch_array($sql2)){
				$i++;
					$search .= $surveyinstances['surveyInstanceID'];
				if ($i != $x){ //If not last heraldid, add a seperator
					$search .= ", ";
				}
			} 
			//var_dump($surveyinstances);
			
		
			
			//echo "<h1>x = ".$x.", i = ".$i.", search = ".$search."</h1>";
			//print '<b>x yes</b>';
			$sql = $local->selectQuery("resultsets.res_id, res_name, resultsets.surveyinstanceid", 
			"resultsets inner join resultsetstudents on resultsets.res_id = resultsetstudents.res_id", 
			"resultsetstudents.heraldid = '".$user."' and resultsets.surveyinstanceid in (".$search.")");
			
			//echo "<h2>SELECT resultsets.res_id, res_name, resultsets.surveyinstanceid FROM resultsets inner join resultsetstudents on resultsets.res_id = resultsetstudents.res_id WHERE resultsetstudents.heraldid = '".$user."' and resultsets.surveyinstanceid in (".$search.")</h2>";
			
			while($data = mysql_fetch_array($sql)){
			
				if (! isset($avail)){
					$i = 0;
				} else {
					$i = sizeof($avail);
				}
			
			//if (in_array($data['surveyinstanceid'], $allsurveys)){
				$avail[$i]['name'] = $data['res_name'];
				$avail[$i]['link'] = "./?res=".$data['res_id']."&view";
				$finishDate = $remote->finishDate($data['surveyinstanceid']);
				if (! $finishDate){
					$avail[$i]['date'] = "Unknown";
				} else {
					$avail[$i]['date'] = date("jS M Y", strtotime($finishDate));
				}
			//}	
			
			}
		
		} else {
			$search = "''";
		}
		
		$sql3 = $local->selectQuery("resultsets.res_id, res_name, resultsets.surveyinstanceid", 
			"resultsets inner join resultsetstudents on resultsets.res_id = resultsetstudents.res_id", 
			"resultsetstudents.heraldid = '".$user."' and resultsets.surveyinstanceid not in (".$search.")");
		$y = mysql_num_rows($sql3);
		
		//echo "<h2>SELECT resultsets.res_id, res_name, resultsets.surveyinstanceid FROM resultsets inner join resultsetstudents on resultsets.res_id = resultsetstudents.res_id WHERE resultsetstudents.heraldid = '".$user."' and resultsets.surveyinstanceid not in (".$search.")</h2>";

		if ($y > 0){
				
			while ($surveyincomplete = mysql_fetch_array($sql4)){
				$i++;
				$search2 .= $surveyincomplete['surveyInstanceID'];
				if ($i != $y){ //If not last heraldid, add a seperator
					$incomplete = ", ";
				}
			}
			
			//print '<b>y yes</b>';			
			//Find Resultsets that the user is on, and that match the list of completed Survey Instances
			
			while($data3 = mysql_fetch_array($sql3)){
				
				if (! isset($nonavail)){
					$i = 0;
				} else {
					$i = sizeof($nonavail);
				}
			
			//foreach($allsurveys as $survey){
				//if ($data3['surveyinstanceid'] == $survey){
					$nonavail[$i]['name'] = $data3['res_name'];
					$nonavail[$i]['link'] = "https://learntech.imsu.ox.ac.uk/feedback/showsurvey.php?surveyInstanceID=".$data3['surveyinstanceid'];
					$finishDate = $remote->finishDate($data3['surveyinstanceid']);
				if (! $finishDate){
					$nonavail[$i]['date'] = "Unknown";
				} else {
					$nonavail[$i]['date'] = date("jS M Y", strtotime($finishDate));
				}
				//}			
			}
			
			//}

		
		} else {
			$search2 = "''";
		}
		
		/*
		$sql5 = $local->selectQuery("resultsets.res_id, res_name, res_timestamp, resultsets.surveyinstanceid", 
		"resultsets inner join resultsetstudents on resultsets.res_id = resultsetstudents.res_id", 
		"resultsetstudents.heraldid = '".$user."' and resultsets.surveyinstanceid not in (".$search.", ".$search2.")");
		
		if (mysql_num_rows($sql5) > 0){
		
		while($data = mysql_fetch_array($sql5)){
		
			if (! isset($nonavail)){
				$i = 0;
			} else {
				$i = sizeof($nonavail);
			}
			
			if (in_array($data['surveyinstanceid'], $allsurveys)){
				$nonavail[$i]['name'] = $data['res_name'];
				$nonavail[$i]['link'] = "https://learntech.imsu.ox.ac.uk/feedback/showsurvey.php?surveyInstanceID=".$data['surveyinstanceid'];
				$nonavail[$i]['date'] = date("jS M Y", strtotime($remote->finishDate($data['surveyinstanceid'])));
			}			
		
		}
		
		}*/
		
		/* echo '<h1>avail</h1>';
		print_r($avail);
		echo '<h1>nonavail</h1>';
		print_r($nonavail); */
				
		if (! isset($avail) && ! isset($nonavail)){
			$html = "<p>There are no results available for you at this time.</p>";
		} elseif (isset($avail)){
			$html = "<p><strong>Results available</strong></p>";
			$html .= $this->listResultsetsStudent($avail);
		}
		
		if (isset($nonavail)){
			$html .= "<p><strong>Results withheld</strong></p>
			<p>You need to provide feedback on this course before you will be 
			able to access your results. Click on the appropriate link below 
			to be taken to the online feedback form for this course.<p>";
			$html .= $this->listResultsetsStudent($nonavail);
		}
		
		return $html;
	
	}
	
	function listResultsetsStudent($resultsets){
	
		$html = '<table>
		<tr>
	      <th><div class="th">Course name</div></th>
		  <th width="20%"><div class="th">Available until</div></th>
		</tr>';
		
		foreach ($resultsets as $resultset){
			
			$html .= '<tr>
	      <td><a href="'.$resultset['link'].'">'.$resultset['name'].'</a></td>
		  <td>'.$resultset['date'].'</td>
		</tr>';
			
		}
		
		$html .= '</table>';
		
		return $html;	
	
	}
	
	function listUsers($acc = null){
	

		$local = new local();
		
		if ($acc == 1){
			$where = " WHERE prv_id = 1";
		} else {
			$where = "";
		}
		
		$sql2 = $local->selectQuery("prv_id, prv_name", "priviliges".$where." ORDER BY prv_id DESC");
				
		while ($data2 = mysql_fetch_array($sql2)){
		
		$sql = $local->selectQuery("heraldid, usr_name, usr_dept, prv_id, usr_email", "users", "prv_id = ".$data2['prv_id']." ORDER BY usr_name, usr_dept");
		
			if ($data2['prv_id'] == 1 && $acc != 1){
			
			$num = mysql_num_rows($sql);
			if ($num != 0){
				$html .= '<p><a href="./?acc=1">View '.$num.' Users</p>';
			} else {
				$html .= '<p><em>No '.$data2['prv_name'].'s are in Feedback gets Results.</em></p>';
			}
			
			} else {
			
			if (mysql_num_rows($sql) != 0){
			
				$html .= '<p><strong>'.$data2['prv_name'].'</strong></p><table>
				<tr>
			      <th width="30%" ><div class="th">Name</div></th>
				  <th width="30%"><div class="th">Department</div></th>
				  <th><div class="th">Email</div></th>		
				</tr>';
				
				while ($data = mysql_fetch_array($sql)){
					if ($acc == 1){
						$html .= '<tr>
					      <td>'.$data['usr_name'].' (<em>'.$data['heraldid'].'</em>)</td>
						  <td>'.$data['usr_dept'].'</td>
						  <td><a href="mailto:'.$data['usr_email'].'">'.$data['usr_email'].'</a></td>
						</tr>';
					} else {
						$html .= '<tr>
					      <td><a href="./?acc&edit&user='.$data['heraldid'].'">'.$data['usr_name'].'</a></td>
						  <td>'.$data['usr_dept'].'</td>
						  <td><a href="mailto:'.$data['usr_email'].'">'.$data['usr_email'].'</a></td>
						</tr>';
					}
				}

				$html .= '</table>';
				
				} else {
				
				$html .= '<p><em>No '.$data2['prv_name'].'s are in Feedback gets Results.</em></p>';
				
				}
			}
		
		}
		
		if (! $template = fopen(site_root."templates/user_list.html", "r")){
				$element = '<p>Template cannot be found</p>'; //Replace with better error messages
			} else {
				$element = fread($template, filesize(site_root."templates/user_list.html"));
				$element = preg_replace("/{{users}}/i", $html, $element);
			}			
			
		return $element;
	
	}
	
	function formUser($heraldid = null){
	
		if ($heraldid == null){
			$link = "./?acc&add";
			$button = "Add User";
		} else {
			$link = "./?acc&edit=".$heraldid;
			$local = new local();
			$user = $local->returnUser($heraldid);
			$name = $user[0];
			$dept = $user[1];
			$email = $user[2];
			$button = "Submit Revisions";
			$delete = '</form><br /><form name="user_edit" method="post" action="./?acc&del&user='.$heraldid.'">
			<input type="submit" value="Delete User">';
			switch($user[3]){
				case 1:
				$array[] = " selected";
				$array[] = null;
				$array[] = null;
				break;
				case 2:
				$array[] = null;
				$array[] = " selected";
				$array[] = null;
				break;
				case 3:
				$array[] = null;
				$array[] = null;
				$array[] = " selected";
				break;
			}
			//print_r($array);
		}
		
		if (! $template = fopen(site_root."templates/user_edit.html", "r")){
				$element = '<p>Template cannot be found</p>'; //Replace with better error messages
			} else {
				$element = fread($template, filesize(site_root."templates/user_edit.html"));
				$element = preg_replace("/{{heraldid}}/i", $heraldid, $element);
				$element = preg_replace("/{{link}}/i", $link, $element);
				$element = preg_replace("/{{name}}/i", $name, $element);
				$element = preg_replace("/{{dept}}/i", $dept, $element);
				$element = preg_replace("/{{pri}}/i", $pri, $element);
				$element = preg_replace("/{{email}}/i", $email, $element);
				$element = preg_replace("/{{button}}/i", $button, $element);
				$element = preg_replace("/{{delete}}/i", $delete, $element);
				$element = preg_replace("/{{one}}/i", $array[0], $element);
				$element = preg_replace("/{{two}}/i", $array[1], $element);
				$element = preg_replace("/{{three}}/i", $array[2], $element);				
			}			
			
		return $element;
	
	}
	
	function removeUser($heraldid){
		
		$local = new local();
		if ($local->deleteUser($heraldid)){
			return '<p>User successfully deleted.</p>';
		}else{
			return '<p>Unable to remove user. Please hit the back button on your 
			browser and try again, and if you are still unsuccessful please contact 
			an administrator for further assistance.</p>';
		}
		
	}
	
	function createUser($user){
	
		$local = new local();
		if ($local->addUser($user['heraldid'], $user['name'], $user['dept'], $user['email'], $user['pri'])){
			return '<p>User successfully added.</p>';
		}else{
			return '<p>Unable to add user. Please hit the back button on your 
			browser and try again, and if you are still unsuccessful please contact 
			an administrator for further assistance.</p>';
		}
		
	}
	
	function updateUser($user){
	
		$local = new local();
		if ($local->editUser($user['heraldid'], $user['name'], $user['dept'], $user['email'], $user['pri'])){
			return '<p>User successfully edited.</p>';
		}else{
			return '<p>Unable to edit user. Please hit the back button on your 
			browser and try again, and if you are still unsuccessful please contact 
			an administrator for further assistance.</p>';
		}
		
	}
	
	function buildLog($date = null, $year = null, $month = null){  //CLOWNS
		
		//print '<h1>'.$year.'</h1>';
			
		$log = new log();
		$min = strtotime("today");
		$max = strtotime("tomorrow")-1;
		
		if (strlen($date) < 9){
			
			$data = $log->findLogs();
			
			foreach($data as $file){
				$file = preg_replace("/.txt/i", "", $file);
				$dateparts = preg_split("/_/i", $file);
				$checkday = intval($dateparts[2]);
				$checkmonth = intval($dateparts[1]);
				$checkyear = intval($dateparts[0]);
				
				if ($checkmonth == $month && $checkyear == $year){
					$days[] = $checkday;
				}
			}
			
			$date = mktime(0, 0, 0, $month, max($days), $year);
			
		}
		
		/*print '<h1>Min is '.$min.'</h1>';
		print '<h1>Max is '.$max.'</h1>';
		print '<h1>Date is '.$date.'</h1>';*/
		
		//print '<h1>'.$date.'</h1>';
				
		if ($date >= $min && $date < $max){
			$file = $log->readLog();
		}else{
			$file = $log->readArchive($date);
		}		
		
		$html = $this->dateRangeLogs($date, $year, $month);
		
		if ($year != null && $month != null){
		$min = strtotime("today");
		$max = strtotime("tomorrow")-1;
		if ($date >= $min && $date < $max){
			$view = "Today";
		}else{
			$view = date("jS M Y", $date);
		}
		$html .= '<p><em>Viewing</em> '.$view.'</p>';
		}
		
		if ($year != null && $month != null){ //If a specific date is selected, view the log
			$html .= '<p class="system">';
			$entries = preg_split("{\n}", $file);
			foreach($entries as $entry){
				$html .= $entry.'<br />';
			}
			
			$html .= '</p>';		
		}
		
		return $html;
		
	}
	
	function dateRangeLogs($date = null, $dateyear = null, $datemonth = null){
	
		$log = new log();
		$data = $log->findLogs();
		
		if (isset($datemonth)){
			$reset = ' <em><a href="./?log">Reset</a></em>';
			$showdate = '<strong>'.date("M", mktime(0, 0, 0, $datemonth)).' '.$dateyear.'</strong>';
		} elseif (isset($dateyear)){
		$reset = ' <em><a href="./?log">Reset</a></em>';
			$showdate = '<strong>'.$dateyear.'</strong>';
		}
		
		$html = '<p>Please select the date: <strong>'.$showdate.'</strong>'.$reset.'</p>';
		
		if ($dateyear == null){ //If no year is set
			$html .= '<form id="form1" name="form1" method="get" action="./">	
			  <select name="year" id="year">';
			$allyears = array();
		
			foreach($data as $file){
		
				$file = preg_replace("/.txt/i", "", $file);
				$dateparts = preg_split("/_/i", $file);
				$year = intval($dateparts[0]);
				if (strcmp($file, ".htaccess") != 0){
					$allyears[] = $year;
				}
				
			}
			
			/*echo "<p>allyears = ";
			print_r($allyears);
			echo "</p>";*/
			
			$years = array_unique($allyears);
			$min = min($years); //Find the earliest log file
			
			/*echo "<p>years = ";
			print_r($years);
			echo "</p>";*/
			
			foreach ($years as $year) { //Until we drop past the earliest log file
				
				if ($year == max($years)){
					
					$option = 'Now - '.$year;
					$html .= '<option value="'.$year.'">'.$option.'</option>';	
					
				} else {
				
					$option = ($year + 1).' - '.$year;
					$html .= '<option value="'.$year.'">'.$option.'</option>';	
				
				}
				
			}
		
			$html .= '</select>
			   <input type="submit" value="Year" />
			   <input type="hidden" name="log" value="null" />
			  </form>';
		
		} elseif ($datemonth == null){ //If no month is set
		
			$html .= '<form id="form1" name="form1" method="get" action="./">	
			  <select name="month" id="month">';
			$allmonths = array();
		
			foreach($data as $file){
		
				$file = preg_replace("/.txt/i", "", $file);
				$dateparts = preg_split("/_/i", $file);
				$month = intval($dateparts[1]);
				$year = intval($dateparts[0]);
				
				if ($year == $dateyear){
					$allmonths[] = $month;
					$day = intval($dateparts[2]);
					$date = mktime(0, 0, 0, $month, $day, $year);
					$files[] = $date;
				}
				
			}
			
			//print_r($files);
			
			//print(max($files));
			
			$months = array_unique($allmonths);
			
			foreach ($months as $month) {
			
				$html .= '<option value="'.$month.'">'.date("M", mktime(0, 0, 0, $month)).'</option>';		
			
			}
			
			$html .= '</select>
			   <input type="submit" value="Month" />
			   <input type="hidden" name="year" value="'.$dateyear.'" />
			   <input type="hidden" name="log" value="null" />';
			   		   			   
			  $html .= '</form>';
		
		} else {
		
		//print_r($data);
		
		$html .= '<form id="form1" name="form1" method="get" action="./">	
		  <select name="log" id="log">';
		
		foreach($data as $file){
		
		$file = preg_replace("/.txt/i", "", $file);
		$dateparts = preg_split("/_/i", $file);
		$day = intval($dateparts[2]);
		$month = intval($dateparts[1]);
		$year = intval($dateparts[0]);
		
		if ($year == $dateyear && $month == $datemonth){
			$file = mktime(0, 0, 0, $month, $day, $year);
			//print '<h1>log = '.$log.'</h1>';
			//print '<h1>file = '.$file.'</h1>';
			if ($date == $file){
			$checked = ' selected="selected"';
		}else{
			$checked = "";
		}
		
		$option = date("jS M Y", $file);
		//print_r($option);
		$html .= '<option value="'.$file.'"'.$checked.'>'.$option.'</option>';		
		}
		//print_r($file);
		
		
		
		}	
		
		$html .= '</select>
		   <input type="submit" value="View" />
		   <input type="hidden" name="year" value="'.$dateyear.'" />
		   <input type="hidden" name="month" value="'.$datemonth.'" /></form>';
		
		}
	
	return $html;
	
	}
	
	function showUpdateResultset($progress){
	
	switch ($progress){
		case 1:
		$images = array("done", "progress", "notdone", 
		"notdone", "notdone");
		break;
		case 2:
		$images = array("done", "done", "progress", 
		"notdone", "notdone");
		break;
		case 3:
		$images = array("done", "done", "done", 
		"progress", "notdone");
		break;
		case 4:
		$images = array("done", "done", "done", 
		"done", "progress");
		break;
		case 5:
		$images = array("done", "done", "done", 
		"done", "done");
		break;
		default:
		$images = array("done", "done", "done", 
		"done", "done");
		break;
	}
	
	$html = '<br /><table>
  <tr>
    <td class="nostyle"><img src="images/res_'.$images[0].'.png" /></td>
    <td class="nostyle">Creating Resultset</td>
  </tr>
  <tr>
    <td class="nostyle"><img src="images/res_'.$images[1].'.png" /></td>
    <td class="nostyle">Adding Resultset headings</td>
  </tr>
  <tr>
    <td class="nostyle"><img src="images/res_'.$images[2].'.png" /></td>
    <td class="nostyle">Adding Resultset Students and Results</td>
  </tr>
  <tr>
    <td class="nostyle"><img src="images/res_'.$images[3].'.png" /></td>
    <td class="nostyle">Adding Reminders</td>
  </tr>
  <tr>
    <td class="nostyle"><img src="images/res_'.$images[4].'.png" /></td>
    <td class="nostyle">Building portable documents (*.pdf)</td>
  </tr>
</table>';

//print_r($_SESSION['resultset']['progress']);

if ($progress == 6) {

$local = new local();

for($i = 1; $i <= $_POST['count']; $i++){

$local->editStudent($_POST['heraldid_'.$i], $_POST['name_'.$i], $_POST['dept_'.$i], $_POST['email_'.$i]);

}

$html .= '<p><em>Complete</em></p>
<p>The details you have entered for the students have now been entered into the system.</p>';

} elseif (($progress >= 5) && (isset($_SESSION['resultset']['unknown']))){
	$html .= '<p><em>Nearly Complete</em></p>
<p>Your Resultset has now been inserted into the database, and configured to work alongside the Medical Sciences Division Feedback System.</p>
<p>However, there has been a problem retrieving details for one or more of your students. Please enter the details manually, or they will not 
be displayed properly. Students will not have been emailed automatically as a result.<form id="editUnknown" name="editUnknown" method="post" action="./?res&add&commit"></p>';

foreach ($_SESSION['resultset']['unknown'] as $student){

$i++;
//$_SESSION['resultset']['progress'] = 6;

$html .= '<p>'.$student[0].'</p>
<input name="heraldid_'.$i.'" type="hidden" id="heraldid_'.$i.'" value="'.$student[0].'" />
  <input name="name_'.$i.'" type="text" id="name_'.$i.'" />
  <label>Name</label>
  <br /><br />
    <input name="dept_'.$i.'" type="text" id="dept_'.$i.'" />
  <label>Department</label> 
    <br /><br />
    <input type="text" name="email_'.$i.'" />
        <label>Email</label> ';
		
}
	
    $html .= '<br /><br /><input type="hidden" value="'.$i.'" name="count" /><input type="submit" value="Edit unknown students" /></form>';
	
	
} elseif ($progress >= 5) {
$html .= '<p><em>Complete</em></p>
<p>Your Resultset has now been inserted into the database, and configured to work alongside the Medical Sciences Division Feedback System.</p>';
} else {

	$html .= '<meta http-equiv="refresh" content="1;url=./?res&add&commit">';
	
	}

	return $html;
	
	}
	
	function __destruct() {
	/* The site function uses the site.html template to render the entire page
	Inputs: $content (contains all html generated at runtime prepared by other classes or modules
	Outputs: $page (the entire page that will be displayed) */
	
		$user = new local();
		//$array = $user->returnUser($_SESSION['user']);
		//$array = $user->returnUser("imsu0001");
		
		$hello = 'Welcome '.$_SESSION['name'].' <em>('.$_SESSION['user'].')</em><br />'.$_SESSION['info']; //Replace with $_SESSION data later
		$date = date("jS M Y");
		
		if (! $template = fopen(site_root."templates/site.html", "r")){
			$page = '<p>Template cannot be found</p>'; //Replace with better error messages
		} else {
			$page = fread($template, filesize(site_root."templates/site.html"));
			$page = preg_replace("/{{hello}}/i", $hello, $page);
			$page = preg_replace("/{{date}}/i", $date, $page);
			$page = preg_replace("/{{content}}/i", $this->_html, $page);
		}
		
		print($page);
	
	}

}

?>