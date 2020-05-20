<?php

class menu {

	private $_html;
	
	function __construct(){
	
	$this->_html = '<table>';
  	
	}
	
	function buildMenu($title, $switch = null, $link = null){
	
		//If default and user is not a student
		if ($switch == "Start" && $_SESSION['pri'] != 1){
			switch($_SESSION['pri']){
				case 2:
				$title = "Administration";
				break;
				case 3:
				$title = "Super Administration";
				break;
			}
		}
		//If any page other than student default
		if (! ($switch == 1 && $_SESSION['pri'] == 1)){
			$this->_html = '<p><strong>'.$title.'</strong></p>'.$this->_html;
		}
							
		switch($_SESSION['pri']){
			case 1:
				//Default
				switch($switch){
				case "Start":
				return '<p><strong>'.$title.'</strong></p>';
				break;
				case "Results":
				$this->_html .= '<tr><td class="nostyle" width="32px"><a href="./"><img src="./images/return_start.png" width="32" height="32" alt="Return to Start" /></a></td>
				<td class="nostyle" ><a href="./">Return to Start</a></td>
				</tr><tr>
				<td class="nostyle" ><a href="'.$link.'&user='.$_SESSION['user'].'&pdf"><img src="./images/view_pdf.png" width="32" height="32" alt="Back to Resultsets" /></a></td>
				<td class="nostyle" ><a href="'.$link.'&user='.$_SESSION['user'].'&pdf">View as Portable Document (<em>*.pdf</em>)</a></td>
				</tr></tr><tr>
				<td class="nostyle" ><a href="'.$link.'&user='.$_SESSION['user'].'&email"><img src="./images/send_email.png" width="32" height="32" alt="Back to Resultsets" /></a></td>
				<td class="nostyle" ><a href="'.$link.'&user='.$_SESSION['user'].'&email">Send to my Email</a></td>
				</tr>';
				break;
				default:
				$this->_html .= '<tr><td class="nostyle" width="32px"><a href="./"><img src="./images/return_start.png" width="32" height="32" alt="Return to Start" /></a></td>
				<td class="nostyle" ><a href="./">Return to Start</a></td>
				</tr>';
				break;
				}
			break;
			case 2:
			switch($switch){
				//Default
				case "Start":
					$this->_html .= '<tr><td class="nostyle" width="32px"><a href="./?add&res"><img src="./images/add.png" alt="Add Resultset" width="32" height="32" /></a></td>
					<td class="nostyle"><a href="./?add&res">Add Resultset</a></td>
				  </tr>
				  <tr>
					<td class="nostyle"><a href="./?arch"><img src="./images/view_archive.png" alt="View Archive" width="32" height="32" /></a></td>
					<td class="nostyle"><a href="./?arch">View Archive</a></td>
				  </tr>
				  <tr>
					<td class="nostyle"><a href="./?rem=null"><img src="./images/view_reminders.png" alt="View Reminders" width="32" height="32" /></a></td>
					<td class="nostyle"><a href="./?rem=null">View Reminders</a></td>
				  </tr>';				
				break;
				case "Results":
				$this->_html .= '<tr><td class="nostyle" width="32px"><a href="./"><img src="./images/return_start.png" width="32" height="32" alt="Return to Start" /></a></td>
				<td class="nostyle" ><a href="./">Return to Start</a></td>
				</tr><tr>
				<td class="nostyle" ><a href="'.$link.'"><img src="./images/back.png" width="32" height="32" alt="Back to Resultsets" /></a></td>
				<td class="nostyle" ><a href="'.$link.'">Back to Resultset</a></td>
				</tr><tr>
				<td class="nostyle" ><a href="'.$link.'&user='.$_GET['user'].'&pdf"><img src="./images/view_pdf.png" width="32" height="32" alt="Back to Resultsets" /></a></td>
				<td class="nostyle" ><a href="'.$link.'&user='.$_GET['user'].'&pdf">View as Portable Document (<em>*.pdf</em>)</a></td>
				</tr></tr><tr>
				<td class="nostyle" ><a href="'.$link.'&user='.$_GET['user'].'&email"><img src="./images/send_email.png" width="32" height="32" alt="Back to Resultsets" /></a></td>
				<td class="nostyle" ><a href="'.$link.'&user='.$_GET['user'].'&email">Send to my Email</a></td>
				</tr>';
				break;
				case "Reminders":
				$this->_html .= '<tr><td class="nostyle" width="32px"><a href="./"><img src="./images/return_start.png" width="32" height="32" alt="Return to Start" /></a></td>
				<td class="nostyle" ><a href="./">Return to Start</a></td>
				</tr>
				<tr>
				<td class="nostyle" width="32px"><a href="./?rem='.$_GET['rem'].'&add"><img src="./images/add_reminder.png" width="32" height="32" alt="Add Reminder" /></a></td>
				<td class="nostyle" ><a href="./?rem='.$_GET['rem'].'&add">Add Reminder</a></td>
				</tr>';
				break;
				case "Manipulate Reminder":
				$this->_html .= '<tr><td class="nostyle" width="32px"><a href="./"><img src="./images/return_start.png" width="32" height="32" alt="Return to Start" /></a></td>
				<td class="nostyle" ><a href="./">Return to Start</a></td>
				</tr><tr>
				<td class="nostyle" ><a href="./?rem=null"><img src="./images/back.png" width="32" height="32" alt="Back to Users" /></a></td>
				<td class="nostyle" ><a href="./?rem=null">Back to Reminders</a></td>
				</tr>';
				break;
				default:
				$this->_html .= '<tr><td class="nostyle" width="32px"><a href="./"><img src="./images/return_start.png" width="32" height="32" alt="Return to Start" /></a></td>
				<td class="nostyle" ><a href="./">Return to Start</a></td>
				</tr>';
				break;
			}
			break;
			case 3:
			switch($switch){
				//Default
				case "Start":
					$this->_html .= '<tr><td class="nostyle" width="32px"><a href="./?add&res"><img src="./images/add.png" alt="Add Resultset" width="32" height="32" /></a></td>
					<td class="nostyle"><a href="./?add&res">Add Resultset</a></td>
				  </tr>					
					<tr><td class="nostyle" width="32px"><a href="./?stat"><img src="./images/stats.png" alt="View Statistics" width="32" height="32" /></a></td>
					<td class="nostyle"><a href="./?stat">View Statistics</a></td>
				  </tr>
				  <tr>
					<td class="nostyle"><a href="./?log"><img src="./images/view_log.png" alt="View Log" width="32" height="32" /></a></td>
					<td class="nostyle"><a href="./?log">View Log</a></td>
				  </tr>
				  <tr>
					<td class="nostyle"><a href="./?arch"><img src="./images/view_archive.png" alt="View Archive" width="32" height="32" /></a></td>
					<td class="nostyle"><a href="./?arch">View Archive</a></td>
				  </tr>
				  <tr>
					<td class="nostyle"><a href="./?rem=null"><img src="./images/view_reminders.png" alt="View Reminders" width="32" height="32" /></a></td>
					<td class="nostyle"><a href="./?rem=null">View Reminders</a></td>
				  </tr>
				  <tr>
					<td class="nostyle"><a href="./?not"><img src="./images/view_notices.png" alt="View Users" width="32" height="32" /></a></td>
					<td class="nostyle"><a href="./?not">View Notices</a></td>
					</tr>
				  <tr>
					<td class="nostyle"><a href="./?acc"><img src="./images/view_users.png" alt="View Users" width="32" height="32" /></a></td>
					<td class="nostyle"><a href="./?acc">View Users</a></td>
					</tr>';				
				break;
				//Sommat
				case "Results":
				$this->_html .= '<tr><td class="nostyle" width="32px"><a href="./"><img src="./images/return_start.png" width="32" height="32" alt="Return to Start" /></a></td>
				<td class="nostyle" ><a href="./">Return to Start</a></td>
				</tr><tr>
				<td class="nostyle" ><a href="'.$link.'"><img src="./images/back.png" width="32" height="32" alt="Back to Resultsets" /></a></td>
				<td class="nostyle" ><a href="'.$link.'">Back to Resultset</a></td>
				</tr></tr><tr>
				<td class="nostyle" ><a href="'.$link.'&user='.$_GET['user'].'&pdf"><img src="./images/view_pdf.png" width="32" height="32" alt="Back to Resultsets" /></a></td>
				<td class="nostyle" ><a href="'.$link.'&user='.$_GET['user'].'&pdf">View as Portable Document (<em>*.pdf</em>)</a></td>
				</tr></tr><tr>
				<td class="nostyle" ><a href="'.$link.'&user='.$_GET['user'].'&email"><img src="./images/send_email.png" width="32" height="32" alt="Back to Resultsets" /></a></td>
				<td class="nostyle" ><a href="'.$link.'&user='.$_GET['user'].'&email">Send to my Email</a></td>
				</tr>';
				break;
				case "Reminders":
				$this->_html .= '<tr><td class="nostyle" width="32px"><a href="./"><img src="./images/return_start.png" width="32" height="32" alt="Return to Start" /></a></td>
				<td class="nostyle" ><a href="./">Return to Start</a></td>
				</tr>
				<tr>
				<td class="nostyle" width="32px"><a href="./?rem='.$_GET['rem'].'&add"><img src="./images/add_reminder.png" width="32" height="32" alt="Add Reminder" /></a></td>
				<td class="nostyle" ><a href="./?rem='.$_GET['rem'].'&add">Add Reminder</a></td>
				</tr>';
				break;
				case "Admins":
				$this->_html .= '<tr><td class="nostyle" width="32px"><a href="./"><img src="./images/return_start.png" width="32" height="32" alt="Return to Start" /></a></td>
				<td class="nostyle" ><a href="./">Return to Start</a></td>
				</tr><tr>
				<td class="nostyle" ><a href="./"><img src="./images/back.png" width="32" height="32" alt="Back to Users" /></a></td>
				<td class="nostyle" ><a href="./?acc">Back to Users</a></td>
				</tr><tr>
				<td class="nostyle" ><a href="./?acc&add"><img src="./images/add_user.png" width="32" height="32" alt="Add User" /></a></td>
				<td class="nostyle" ><a href="./?acc&add">Add User</a></td>
				</tr>';
				break;
				case "Users":
				$this->_html .= '<tr><td class="nostyle" width="32px"><a href="./"><img src="./images/return_start.png" width="32" height="32" alt="Return to Start" /></a></td>
				<td class="nostyle" ><a href="./">Return to Start</a></td>
				</tr><tr>
				<td class="nostyle" ><a href="./?acc&add"><img src="./images/add_user.png" width="32" height="32" alt="Add User" /></a></td>
				<td class="nostyle" ><a href="./?acc&add">Add User</a></td>
				</tr>';
				break;
				case "Manipulate User":
				$this->_html .= '<tr><td class="nostyle" width="32px"><a href="./"><img src="./images/return_start.png" width="32" height="32" alt="Return to Start" /></a></td>
				<td class="nostyle" ><a href="./">Return to Start</a></td>
				</tr><tr>
				<td class="nostyle" ><a href="./?acc"><img src="./images/back.png" width="32" height="32" alt="Back to Users" /></a></td>
				<td class="nostyle" ><a href="./?acc">Back to Users</a></td>
				</tr>';
				break;
				case "Manipulate Reminder":
				$this->_html .= '<tr><td class="nostyle" width="32px"><a href="./"><img src="./images/return_start.png" width="32" height="32" alt="Return to Start" /></a></td>
				<td class="nostyle" ><a href="./">Return to Start</a></td>
				</tr><tr>
				<td class="nostyle" ><a href="./?rem=null"><img src="./images/back.png" width="32" height="32" alt="Back to Users" /></a></td>
				<td class="nostyle" ><a href="./?rem=null">Back to Reminders</a></td>
				</tr>';
				break;
				case "Notices":
				$this->_html .= '<tr><td class="nostyle" width="32px"><a href="./"><img src="./images/return_start.png" width="32" height="32" alt="Return to Start" /></a></td>
				<td class="nostyle" ><a href="./">Return to Start</a></td>
				</tr><tr>
				<td class="nostyle" ><a href="./?not&add"><img src="./images/add_notice.png" width="32" height="32" alt="Back to Users" /></a></td>
				<td class="nostyle" ><a href="./?not&add">Add Notice</a></td>
				</tr>';
				break;
				case "Manipulate Notice":
				$this->_html .= '<tr><td class="nostyle" width="32px"><a href="./"><img src="./images/return_start.png" width="32" height="32" alt="Return to Start" /></a></td>
				<td class="nostyle" ><a href="./">Return to Start</a></td>
				</tr><tr>
				<td class="nostyle" ><a href="./?not"><img src="./images/back.png" width="32" height="32" alt="Back to Users" /></a></td>
				<td class="nostyle" ><a href="./?not">Back to Notices</a></td>
				</tr>';
				break;
				default:
				$this->_html .= '<tr><td class="nostyle" width="32px"><a href="./"><img src="./images/return_start.png" width="32" height="32" alt="Return to Start" /></a></td>
				<td class="nostyle" ><a href="./">Return to Start</a></td>
				</tr>';
				break;
				}
			break;				
		}
		
	$this->_html .= '</table>';	
	return $this->_html;
	
	}

}

?>