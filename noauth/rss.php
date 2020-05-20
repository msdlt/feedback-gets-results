<?php

//require_once("inc/header.inc");

error_reporting(0);
//error_reporting(E_ALL);

//Require files needed (not all to bypass authentication)
require_once("inc/config.inc");
require_once("class/interface.database.php");
require_once("class/class.local.php");
require_once("class/class.remote.php");
require_once("class/class.rss.php");

$rss = new rss();

$content = $rss->generateItems();

if ($content){
	header("Content-type: text/xml");
	print $content;
} else {
	exit();
}

?>