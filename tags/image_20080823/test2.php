<?php
include 'common.inc.php';

echo __FILE__."<BR />";
echo dirname(__FILE__)."<br />";
echo $_SERVER['DOCUMENT_ROOT']."<BR />";
echo realpath($_SERVER['DOCUMENT_ROOT'])."<br /><br/>";

echo PATH_ROOT_ABS."<BR />";
echo PATH_ROOT_RELATIVE."<br />";
?>