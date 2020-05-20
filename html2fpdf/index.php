<?php

require_once('html2fpdf.php');

$html = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Feedback gets Results</title>
<link href="style.css" rel="stylesheet" type="text/css" />
<style>
/* Style sheet created by Andrew Kirkpatrick, ACDT, LTG, OUCS (Oxford University) */

/* Standard HTML elements */

body {
	background:#F4F4F4;
	
}

ul {
	list-style-type:square;
}

table {
	width:85%;
}

table.nostyle {
	width:auto;
}

.mini {
	width:auto;
	padding:0px;
}

input, textarea, select {
	border:thin #000048 solid;
}

input:hover, input:focus, textarea:hover, textarea:focus, select:hover, select:focus {
	border:thin #CC6633 solid;
}

input.noborder {
	border:none;

}

option.highlight {
	background:#000048;
	color:#F4F4F4;
}

th {
	background:#000048;
}

td {
	background:#F4F4F4;
	padding:1px;
}

td.nostyle {
	background:none;
	padding:0px;	
}

label {
	font-style:italic;
}

p.system {
	font-family:"Courier New", Courier, monospace;
}

h1, h2, h3, p, ul, th, td, label, input, textarea {
	font-family:Arial, Helvetica, sans-serif;
	font-size:0.8em;
	color:#000048;
}

h1 {
	padding:1px;
	color:#F4F4F4;
	background:#000048;
}

p.milk {
	padding:1px;
	background:#9F9FFF;
}

h3 {
	padding:1px;
	background:#DDDDFF;
}

p.data {
	background:#F4F4F4;
	padding:1px;
}

a:active, a:visited, a {
	color:#000048;
}

a:hover {
	color:#000048;
	text-decoration:none;
}

img {
	border:none;
}

/* Custom */

div.main {
	background:white;
	background-image:url("./images/main.jpg");
	background-position:bottom right;
	background-repeat:no-repeat;
	padding-top:2%;
	padding-left:2%;
	padding-right:2%;
	padding-bottom:2%;
	margin-top:2%;
	margin-left:10%;
	margin-right:10%;
	margin-bottom:2%;
	border-color:#000048;
	border-style:solid;
	border-width:thick;
}

div.title {
	background-image:url("./images/title.gif");
	height:50px;
	background-repeat:no-repeat;
}

div.notice {
	color:#FF0000;
}

div.th {
	color:#E1E1F0;
	text-align:left;
}

div.disclaimer {
	text-align:center;
}

table.superarchive {
	width:95%;
}
</style>
</head>

<body>
<div class="main">
  <div class="title"></div>
  <p><strong>Welcome Andrew Kirkpatrick <em>(oucs0032)</em><br />OUCS</strong> <a href="./?logout">Logout</a></p>

  <p>22nd Jan 2007</p>
  <p><strong>Results</strong></p><table><td class="nostyle" width="32px"><a href="./"><img src="./images/return_start.png" width="32" height="32" alt="Return to Start" /></a></td>
				<td class="nostyle" ><a href="./">Return to Start</a></td>
				</tr><tr>
				<td class="nostyle" ><a href="./"><img src="./images/back.png" width="32" height="32" alt="Back to Resultsets" /></a></td>
				<td class="nostyle" ><a href="./?res=54&view">Back to Resultset</a></td>
				</tr></table><img width="500" src="title_pdf.png" alt="blah"><p><strong>Viewing Leonid Nikitenko <em>(lina0942)</em> in test pdf</strong></p><h1>Written</h1><p class="milk">Mark (%)</p><p class="data">64</p><p class="milk">mega</p><h3>Rank out of 26</h3><p class="data">17</p><h1>Clinical</h1><p class="milk">Overall mark (%)</p><p class="data">76</p><h3>Rank out of 26</h3><p class="data">3</p><p class="milk">AN patient 27</p><h3>Mark out of 20</h3><p style="color:#e22;font-weight:bold;">15</p><h3>Highest</h3><p class="data">18</p><h3>Mean</h3><p class="data">16</p><h3>Lowest</h3><p class="data">13</p><h3>Introduction</h3><p class="data">Very good introduction</p><h3>Eye contact</h3><p class="data">Very good eye contact</p><h3>Good language</h3><p class="data">Very good. Very easy to understand</p><h3>Questions & answers</h3><p class="data">Answered my questions well</p><h3>Attend as GP</h3><p class="data">Yes, willingly</p><p class="milk">CS consent 26</p><h3>Mark out of 20</h3><p class="data">14</p><h3>Highest</h3><p class="data">19</p><h3>Mean</h3><p class="data">15</p><h3>Lowest</h3><p class="data">13</p><h3>Introduction</h3><p class="data">Excellent introduction</p><h3>Eye contact</h3><p class="data">Very good eye contact</p><h3>Good language</h3><p class="data">Very good. Very easy to understand</p><h3>Questions & answers</h3><p class="data">Excellent and easy to understand</p><h3>Attend as GP</h3><p class="data">Definitely. </p><p class="milk">Minipill 24</p><h3>Mark out of 20</h3><p class="data">18</p><h3>Highest</h3><p class="data">20</p><h3>Mean</h3><p class="data">16</p><h3>Lowest</h3><p class="data">8</p><h3>Introduction</h3><p class="data">Excellent introduction</p><h3>Eye contact</h3><p class="data">Very good eye contact</p><h3>Good language</h3><p class="data">Very good. Very easy to understand</p><h3>Questions & answers</h3><p class="data">Excellent and easy to understand</p><h3>Attend as GP</h3><p class="data">Definitely. </p><p class="milk">Menorrhagia 15</p><h3>Mark out of 20</h3><p class="data">13</p><h3>Highest</h3><p class="data">18</p><h3>Mean</h3><p class="data">14</p><h3>Lowest</h3><p class="data">10</p><h3>Introduction</h3><p class="data">Excellent introduction</p><h3>Eye contact</h3><p class="data">Very good eye contact</p><h3>Good language</h3><p class="data">Good. Fairly easy to understand</p><h3>Questions & answers</h3><p class="data">Excellent and easy to understand</p><h3>Attend as GP</h3><p class="data">Yes, willingly</p><p class="milk">GUM2 31</p><h3>Mark out of 20</h3><p class="data">16</p><h3>Highest</h3><p class="data">19</p><h3>Mean</h3><p class="data">15</p><h3>Lowest</h3><p class="data">11</p><h3>Introduction</h3><p class="data">Very good introduction</p><h3>Eye contact</h3><p class="data">Good eye contact</p><h3>Good language</h3><p class="data">Good. Fairly easy to understand</p><h3>Questions & answers</h3><p class="data">Answered my questions well</p><h3>Attend as GP</h3><p class="data">Yes, willingly</p><p class="milk">Gynman 2 smear</p><h3>Mark out of 25</h3><p class="data">21</p><h3>Highest</h3><p class="data">25</p><h3>Mean</h3><p class="data">20</p><h3>Lowest</h3><p class="data">16</p><p class="milk">Obsman 1 Delay</p><h3>Mark out of 25</h3><p class="data">17</p><h3>Highest</h3><p class="data">20</p><h3>Mean</h3><p class="data">17</p><h3>Lowest</h3><p class="data">11</p>

  </div>
<div class="disclaimer">
  <p>  Developed by the <a href="http://www.oucs.ox.ac.uk/acdt/">Academic Computing Development Team</a> in conjuction with Medical Sciences Division Learning Technologies<br />
  &copy; Oxford University 2007</p>
</div>
</body>
</html>

';

$pdf = new HTML2FPDF();
$pdf->DisplayPreferences('HideWindowUI');
$pdf->AddPage();
$pdf->WriteHTML($html);
$pdf->Output('doc.pdf', 'F');

?>