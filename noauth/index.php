<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Feedback gets Results</title>
<link href="./style.css" rel="stylesheet" type="text/css" />
<link rel="alternate" type="application/rss+xml" title="RSS" href="http://fgr.medsci.ox.ac.uk/rss.php">
</head>

<body>
<div class="main">
  <div class="title"></div>
  <p><strong>Welcome to Feedback gets Results, the Medical Science Division's results distribution system.</strong></p>
  <div>
  <p><img align="right" src="images/medsci.jpg" alt="Medical Science temp" width="400" height="400" /><a href="https://fgr.medsci.ox.ac.uk"><img src="images/enter.jpg" alt="Enter Feedback gets Results" width="110" height="35" border="0" /></a></p>
  <p>Feedback gets Results is for students of the Medical Science Division, and requires a valid University Card for logon; using WebAuth, the Oxford University Single Sign-On Service.</p>
  <p>Email notifications will be sent to you by Feedback gets Results automatically, and a list of recently-released results will be available from this page, or via <a href="http://fgr.medsci.ox.ac.uk/rss.php">RSS</a>.</p>
  <p>If you are not receiving notifications, cannot check your results, or are having any difficulties with the system please contact the<a href="mailto:feedback_gets_results@medsci.ox.ac.uk"> mailing list</a> and an administrator will get back to you. </p>
  <p>The <a href="http://www.medsci.ox.ac.uk/">Medical Sciences Division</a>, <a href="http://www.ox.ac.uk/">University of Oxford</a>, is ranked third in the world for Biomedicine. We are the largest of the University's five academic divisions,&nbsp;with departments&nbsp; located on several hospital sites around Oxford and in the University's Science Area.</p>
  </div>
<div id="rss">
<?php

	require_once('magpie/rss_fetch.inc');
	
$url = "http://fgr.medsci.ox.ac.uk/rss.php"; //Must use double quotes
$rss = fetch_rss($url);

//Quick hack as Magpie won't return false is the feed is empty or has no items
require_once("/srv/www/fgr/inc/config.inc");
require_once("/srv/www/fgr/class/interface.database.php");
require_once("/srv/www/fgr/class/class.local.php");
require_once("/srv/www/fgr/class/class.remote.php");
require_once("/srv/www/fgr/class/class.rss.php");
$testrss = new rss();

if ($testrss->generateItems() == false){

		$html = '<p><em>No results are currently available.</em></p>';
	
	} else {
	
		$html = '<table>
		<tr>
		<th><div class="th">Results now available <a href="rss.php"><img src="images/rss.gif" width="10" height="10" alt="RSS" /></a></div></th>
		</tr>';
				
		foreach ($rss->items as $item ) {
			
			$title = $item['title'];
			$link   = $item['link'];
			$description   = $item['description'];
		
			$html .= '<tr><td><a href="'.$link.'">'.$description.'</a></td></tr>';
		
		}
		
		$html .= '</table>';
	
	}
	
	echo $html;

?>
</div>
</div>
<div class="disclaimer">
  <p>  Developed by the <a href="http://www.oucs.ox.ac.uk/acdt/">Academic Computing Development Team</a> in conjuction with <a href="http://www.medsci.ox.ac.uk/">Medical Sciences Division</a> Learning Technologies<br />
  &copy;  <a href="http://www.ox.ac.uk/">University of Oxford</a> 2007</p>
</div>
</body>
</html>
