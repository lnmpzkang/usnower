<?php
include "../common.inc.php";

MO_Admin::checkRight();

include '../lib/smarty/Smarty.class.php';
$gmt = GSmarty::getInstance("admin");
$gmt->caching = true;
$gmt->display("admin/menu.html");
?>