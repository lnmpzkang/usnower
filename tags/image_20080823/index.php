<?php
include 'common.inc.php';
include 'lib/smarty/Smarty.class.php';

$gmt = GSmarty::getInstance();
$gmt->display("index.html");
?>