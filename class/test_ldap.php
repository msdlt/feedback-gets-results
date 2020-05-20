<?php

require_once("../inc/config.inc");
require_once("class.ldap.php");
require_once("interface.database.php");
require_once("class.local.php");

$something = new ldap();

$user = $something->returnUser("opht0006");

//print_r($user);

?>