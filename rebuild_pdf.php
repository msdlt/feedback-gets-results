<?php

session_start();
require_once("./inc/header.inc");

$portable = new portable();

$portable->batchPDF(7);

echo "Done";

?>