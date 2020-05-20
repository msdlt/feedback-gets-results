<?php

class email {

	function send($to, $reply, $subject, $message = null){
	
	//$to = "Andy K <andrew.kirkpatrick@oucs.ox.ac.uk>";
	//$subject = "Testing php mail() function";
	//$message = "Testing testing 123";
	$headers = 'From: Feedback gets Results <feedback_gets_results@medsci.ox.ac.uk>' . "\r\n" .
    'Reply-To: '.$reply.'	' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();
	
	if ($message == null){
	
		$filename = "/srv/www/fgr/templates/default_reminder.txt";
		$file = fopen($filename, "r");
		$default = fread($file, filesize($filename));
		$message = $default;
		
		/*$reminder = preg_split("{\n}", $default);
		
			foreach ($reminder as $line) {
				$message .= $line.'<br />';
			}*/
	
	}
	
	$pre_message = "To ".$to.",\n\n";
	//$pre_message .= "(Should have been sent to".$_SESSION['email'].")";		
	$post_message = "\n\nThanks,\n\nFeedback gets Results";
	
	//MSDLT turned this off for the mo.
	if (! mail($to, $subject, $pre_message.$message.$post_message, $headers)){
		return false;
	} else {
		return true;
	}
	
	}
	
	function sendPDF($to, $reply, $subject, $res, $user){
		
		//$to = "Andy K <andrew.kirkpatrick@oucs.ox.ac.uk>";
		$headers = 'From: Feedback gets Results <feedback_gets_results@medsci.ox.ac.uk>' . "\r\n" .
	    'Reply-To: '.$reply.'	' . "\r\n" .
	    'X-Mailer: PHP/' . phpversion();
		$pdf = '/srv/www/fgr/pdf/'.$res.'_'.$user.'.pdf';
		
		$filename = "/srv/www/fgr/templates/default_sent.txt";
		$file = fopen($filename, "r");
		$message = fread($file, filesize($filename));
		
		$pre_message = "To ".$_SESSION['name'].",\n\n";
		$post_message = "\n\nThanks,\n\nFeedback gets Results";
		
		require_once('/srv/www/fgr/inc/mail_attachment.php');
		//MSDLT removed this for the mo.
		//$send = mail_attachment($to, $subject, $pre_message.$message.$post_message, $headers, $pdf); //Returns true or false
		
		return $send;
				
	}
	
}

?>