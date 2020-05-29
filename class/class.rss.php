<?php

class rss {

private $_items;

function generateItems(){

$local = new local();
$remote = new remote();

//Select latest 20 Resultsets
$sql = $local->selectQuery('res_name, res_timestamp, surveyinstanceid, usr_name, usr_dept', 
'resultsets AS r INNER JOIN users AS u ON r.heraldid = u.heraldid ORDER BY res_timestamp DESC LIMIT 20');

while ($data = mysqli_fetch_array($sql)){

	$finishDate = $remote->finishDate($data['surveyinstanceid']);
	
	//echo '<p>finish = '.strtotime($finishDate).', time = '.time().'</p>';
	
	if (strtotime($finishDate) > time()) {
	
		$this->_items .= '<item>
		<title>'.htmlspecialchars($data['res_name']).'</title>
		<link>https://fgr.medsci.ox.ac.uk</link>
		<description>'.htmlspecialchars($data['res_name']).', uploaded by '.htmlspecialchars($data['usr_name']).' ('.htmlspecialchars($data['usr_dept']).') is now available ('.date("jS M Y", strtotime($data['res_timestamp'])).')</description>
		</item>';
	
	}

}

if (! $template = fopen("/srv/www/fgr/templates/rss.xml", "r")){
	$element = '<p>Template cannot be found</p>'; //Replace with better error messages
} else {
	if (mysqli_num_rows($sql) > 0){
		$element = fread($template, filesize("/srv/www/fgr/templates/rss.xml"));
		$element = preg_replace("/{{rss}}/i", $this->_items, $element);
	} else {
		$element = false;
	}
}

return $element;

}

}

?>