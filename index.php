<?php
session_start();
require_once("./inc/header.inc");

//error_reporting(0); //Error settings for LIVE
error_reporting(E_ALL); //Error settings for DEBUG

/*print '<p>Start session dump:</p>';
print_r($_SESSION);
print '<p>-- FINISH --</p>';*/

//Perform security checks on provided switches
$card = new access($_GET);
swipe($card);

//Create new draw object
$page = new draw();

//Create new menu object
$menu = new menu();

//REMINDERS
if(isset($_GET['rem']) && ! isset($_GET['res']) && ! isset($_GET['add']) && ! isset($_GET['edit']) && ! isset($_GET['del'])){
	//If specifying a reminder
	if ($_GET['rem'] != 'null'){
		$rem = new reminder();
		$page->addToPage($menu->buildMenu("Reminders", "Manipulate Reminder"));
		$page->addToPage($rem->formReminder($_GET['rem'], null));
	//If adding Reminders
	}else{
		$rem = new reminder();
		if (! isset($_GET['user'])){
			$user = $_SESSION['user'];
		} else {
			$user = $_GET['user'];			
		}
		$page->addToPage($menu->buildMenu("Reminders", "Reminders"));
		if ($_SESSION['pri'] < 3){
			$page->addToPage($rem->readReminders($user));
		} else {
			$page->addToPage($rem->readAllReminders($user));
		}
	}
}elseif(isset($_GET['rem']) && isset($_GET['add']) && ! isset($_GET['res'])){
	$page->addToPage($menu->buildMenu("Reminders", "Manipulate Reminder"));
	if (isset($_POST['date'])) {
		$local = new local();
		if ($local->addReminder($_POST['res'], $_POST['date'], 'NULL')){
			$page->addToPage("<p>Reminder has been added.</p>");
		} else {
			$page->addToPage("<p>Reminder cannot be added.</p>");
		}
	} elseif ($_POST['res'] != null){ //PIZZA
		$page->addToPage("<p>Please select the date the Reminder is to be scheduled for:</p>");
		$local = new local();
		$sql = $local->selectQuery("surveyinstanceid", "resultsets", "res_id = ".$_POST['res']);
		$reminders = new reminder();
		while ($data = mysqli_fetch_array($sql)){
			$page->addToPage($reminders->selectReminders($_POST['res'], $data['surveyinstanceid']));
		}
	
	} else {
		$page->addToPage('<p>Please select the Resultset you wish to add a Reminder to:</p>');
		$page->addToPage($page->selectResultsets($_SESSION['user']));
	}
		//BONOBO
		
		
	//If editing Reminders
	}elseif(isset($_GET['rem']) && isset($_GET['edit']) && ! isset($_GET['res'])){
		$local = new local();
		$page->addToPage($menu->buildMenu("Reminders", "Manipulate Reminder"));
		$page->addToPage($local->editReminder($_GET['rem'], $_POST['date'], $_POST['msg'], null));
	//If deleting Reminders
	}elseif(isset($_GET['rem']) && isset($_GET['del']) && ! isset($_GET['res'])){
		$local = new local();
		$page->addToPage($menu->buildMenu("Reminders", "Manipulate Reminder"));
		$page->addToPage($local->deleteReminder($_GET['rem']));
	//If viewing Reminders
	}else{
	
	}

//VIEW
if(isset($_GET['res']) && isset($_GET['view']) && ! isset($_GET['xls'])){

	if ($_SESSION['pri'] > 1){
		$heraldid = $_GET['user']; //If admin or super admin, use user specified
	} else {
		$heraldid = $_SESSION['user']; //... otherwise use the current user
	}
			
	$access = array('res', 'view');
	if(! isset($_GET['user'])){
		$user = $_SESSION['user'];
	} else {
		$user = $_GET['user'];
	}
	
	$user = new local();
		if ($_SESSION['pri'] == 2){
			//Only required for administrators since super admins can view all, and students are restricted to themselves (bit of a last minute panic hack...)
			$check = $user->checkPerms($_GET['res']);
			if ($check != false){
				$page->addToPage($check); //Checks permissions to view resultset, exits with error and log it fails
			}
		} else {
			$check = false;
		}
		
		if (! $check){
		
			if (isset($_GET['pdf'])){
						
			$pdf = $_GET['res'].'_'.$heraldid.'.pdf';
	
			header('Cache-Control:');
			header('Pragma:');
			header('Content-type: application/pdf');
			header('Content-Disposition: inline; filename="/srv/www/fgr/pdf/'.$pdf.'"');
			header('Content-Length: ' . filesize('/srv/www/fgr/pdf/'.$pdf));
			readfile("pdf/".$pdf);
			
			exit;
			
			} elseif (isset($_GET['email'])){
				$email = new email();
								
				$sql_gnarr = $user->selectQuery("res_name, usr_name, usr_email", 
				"resultsets INNER JOIN users ON resultsets.heraldid = users.heraldid",
				"res_id = ".$_GET['res']);
				
				while ($data_gnarr = mysqli_fetch_array($sql_gnarr)){
					$res_name = $data_gnarr['res_name'];
					$reply = $data_gnarr['usr_name'].' <'.$data_gnarr['usr_email'].'>';
				}
								
				$subject = 'Your Results for '.$res_name;
				$to = $_SESSION['name']." (".$_SESSION['user'].") <".$_SESSION['email'].">";				
				
				$sent = $email->sendPDF($to, $reply, $subject, $_GET['res'], $heraldid);
						
				//Doesn't work for some reason, always returns false even if accepted for delivery
				//if (! $send){
				//	$page->addToPage('<p>Your Results cannot be sent to '.$_SESSION['email'].'</p>');
				//} else {
					$page->addToPage('<p>Your Results have been successfully emailed to '.$_SESSION['email'].'</p>');
				//}
			
			}			
			
			$view = new local();
			
			if ((! isset($_GET['user'])) && ($_SESSION['pri'] > 1)){
			
				$page->addToPage($menu->buildMenu("Results"));
				$page->addToPage($view->overviewResultset($_GET['res']));
			
			} else {
			
				//If a user is specified then view their Resultset
				$page->addToPage($menu->buildMenu("Results", "Results", "./?res=".$_GET['res']."&view"));
				$array = $user->returnUser($heraldid);
				$page->addToPage('<p><strong>Viewing '.$array[0].' <em>('.$heraldid.')</em>');	
				
				$resultset = new local();
				$page->addToPage(' in '.$resultset->titleResultset($_GET['res']).'</strong></p>');
				
				$page->addToPage($view->buildResultset($_GET['res'], $heraldid));
				
			}			
		
		}
	
	switch($_GET['view']){
		//Online
		case "o":
			$page->addToPage('<p>View Resultset online as '.$heraldid.'</p>'); //REDUNDANT
		break;
		//Adobe Acrobat
		case "p":
			$page->addToPage('<p>View Resultset pdf as '.$heraldid.'</p>'); //REDUNDANT
		break;
		//Email
		case "e":
			$page->addToPage('<p>Get Resultset email as '.$heraldid.'</p>'); //REDUNDANT
		break;
		default:
		//Then it's clearly broken
		break;
	}
//If no view is specified use overview
}elseif(isset($_GET['res']) && ! isset($_GET['view']) && ! isset($_GET['xls'])){

	//print_r($_GET);
	//print_r($_POST);
	
	if(isset($_GET['rem'])){
						if (isset($_GET['del'])){
							$local = new local();
							$local->deleteReminder($_GET['rem'], 1);
							$page->addToPage($page->buildAddRes($addres = '3-1'));
						} elseif(isset($_GET['edit'])) {
							$local = new local();
							$local->editReminder($_GET['rem'], $_POST['date'], $_POST['msg'], 1);
							$page->addToPage($page->buildAddRes($addres = '3-1'));
						} else {
							$rem = new reminder();
							$page->addToPage($rem->formReminder($_GET['rem'], 1));
						}
	//If adding Resultsets
	} elseif(isset($_GET['add'])){
		$page->addToPage($menu->buildMenu("Add Resultset"));
		if (empty($_POST)){
			$_POST['addres'] = null;
		}
		
		if (isset($_GET['commit'])){
			$page->addToPage($page->buildAddRes($addres = '4'));
		} else {
			//print_r($_SESSION);
			switch($_POST['addres']){
				default:
					//Load addres1.html
					$page->addToPage($page->buildAddRes($addres = null));
				break;
				case "1":
				if ($_POST['name'] == '' || $_FILES['excel']['name'] == ''){
				$page->addToPage('<p><em>You must provide a Resultset Name and an Excel spreadsheet</em></p>');
				$page->addToPage($page->buildAddRes($addres = null));
				} else {			
					//Load addres2.html
					$page->addToPage($page->buildAddRes($addres = '1'));
					}
				break;
				case "2":
					//Load addres3-1.html
					$page->addToPage($page->buildAddRes($addres = '2'));
				break;
				case "3":
					//Load addres3-2.html
					if ($_POST['compulsory'] == 1){
					
								
						
						/*if (isset($_SESSION['resultset']['reminders'])){
							print_r("<p>Before sort");
							print_r($_SESSION['resultset']['reminders']);
							print_r("</p><p>After reminders");
							sort($_SESSION['resultset']['reminders']);
							print_r($_SESSION['resultset']['reminders']);
							print_r("</p>");							
						}*/
					
						if (isset($_POST['addrem'])){
							//If a reminder needs adding
							$reminder = new reminder();
							$reminder->writeSession($_POST['remdate']);
							
							$page->addToPage($page->buildAddRes($addres = '3-1'));
							
					//If editing Resultsets
					}else{
						
							//Display addres3-2
							$page->addToPage($page->buildAddRes($addres = '3-1'));
						}
					//Load addres4.html
					}else{
						$page->addToPage($page->buildAddRes($addres = '3-2'));
					}
				break;
				case "4":
					//Load addres5.html
					$_SESSION['resultset']['progress'] = 1;
					$page->addToPage($page->buildAddRes($addres = '4'));
				break;
			}
			
		}
		
	//Handler
	}elseif(isset($_GET['edit'])){
		$page->addToPage('<p>Edit Resultset</p>'); //REDUNDANT
	//If deleting Resultsets
	}elseif(isset($_GET['del'])){
		$page->addToPage('<p>Delete Resultset</p>'); //REDUNDANT
	//If overviewing Resultsets
	}else{
		$page->addToPage('<p>Resultset overview</p>'); //REDUNDANT
	}
//If fetching Excel document
}elseif(isset($_GET['res']) && ! isset($_GET['view']) && isset($_GET['xls'])){
	$page->addToPage('<p>Get Resultset XLS</p>');
}

//HELP
if(isset($_GET['hlp'])){
	//If adding Help
	if(isset($_GET['add'])){
		$page->addToPage('<p>Add Help</p>');
	//If editing Help
	}elseif(isset($_GET['edit'])){
		$page->addToPage('<p>Edit Help</p>');
	//If deleting Help
	}elseif(isset($_GET['del'])){
		$page->addToPage('<p>Delete Help</p>');
	//If viewing Help
	}else{
		$page->addToPage($menu->buildMenu("Help"));
		$page->addToPage('<p>Viewing Help</p>');
	}
}

//LOG
if(isset($_GET['log'])){
	$page->addToPage($menu->buildMenu("Log"));
	//print '<h1>'.$_GET['year'].'</h1>';
	$page->addToPage($page->buildLog($_GET['log'], $_GET['year'], $_GET['month']));
}

//STAT
if(isset($_GET['stat'])){
	$stats = new stats();
	$page->addToPage($menu->buildMenu("Statistics"));
	$page->addToPage($stats->getStats());
}

//ARCH
if(isset($_GET['arch'])){
	$page->addToPage($menu->buildMenu("Archive"));
	$page->addToPage($page->buildArchive($_SESSION['pri']));	
}

//ACC
if(isset($_GET['acc'])){
	if(isset($_GET['edit'])){
		if (isset($_POST['submit'])){
			$page->addToPage($menu->buildMenu("Users", "Manipulate User"));
			$page->addToPage($page->updateUser($_POST));			
		}else{
			$page->addToPage($menu->buildMenu("Users", "Manipulate User"));
			$page->addToPage($page->formUser($_GET['user']));			
		}		
	}elseif(isset($_GET['del'])){
		$page->addToPage($menu->buildMenu("Users", "Manipulate User"));
		$page->addToPage($page->removeUser($_GET['user']));	
	}elseif(isset($_GET['add'])){
		if (isset($_POST['submit'])){
			$page->addToPage($menu->buildMenu("Users", "Manipulate User"));
			$page->addToPage($page->createUser($_POST));	
		}else{
			$page->addToPage($menu->buildMenu("Users", "Manipulate User"));
			$page->addToPage($page->formUser());	
		}
	}else{
		if ($_GET['acc'] == 1){
			$page->addToPage($menu->buildMenu("Users", "Admins"));
		} else {
			$page->addToPage($menu->buildMenu("Users", "Users"));
		}
		$page->addToPage($page->listUsers($_GET['acc']));	
	}
}

//NOT
if(isset($_GET['not'])){
	$notice = new notice($_SESSION['pri']);
	if(isset($_GET['edit'])){
		$page->addToPage($menu->buildMenu("Notices", "Manipulate Notice"));
		$page->addToPage($notice->editNotice($_GET['not']));	
	}elseif(isset($_GET['del'])){
		$page->addToPage($menu->buildMenu("Notices"));
		$page->addToPage($notice->removeNotice($_GET['not']));	
	}elseif(isset($_GET['add'])){
			$page->addToPage($menu->buildMenu("Notices", "Manipulate Notice"));
			$page->addToPage($notice->addNotice($_POST));	
	}else{
		$page->addToPage($menu->buildMenu("Notices", "Notices"));
		$page->addToPage($notice->listNotices());	
	}
}

//LOGOUT
if(isset($_GET['logout'])){
	//session_destroy();
	//header("Location: https://webauth.ox.ac.uk/logout");
}

if(isset($_GET['resdel'])){
	$local = new local();
	$page->addToPage($menu->buildMenu("Delete Resultset"));
	$page->addToPage($local->deleteResultset($_GET['resdel']));
}

if(isset($_GET['sendemail'])){
	$local = new local();
	$page->addToPage($menu->buildMenu("Send Email"));
	$page->addToPage($local->sendResultsAvailableEmail($_GET['sendemail']));
}

//Default
if (empty($_GET)){
	//$page->addToPage('<p>Default</p>');
	$page->addToPage($menu->buildMenu("Student", "Start"));
	$page->addToPage($page->buildDefault($_SESSION['pri']));	
	
}

?>