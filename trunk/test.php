<?php
include("common.inc.php");
//GLoger::reportToAdmin("sadfasfd");
//GLoger::logToFile("custom.log","aaaa");

//GDir::mkpath("/aa/bb/CC/DD");
//echo $_SERVER['REMOTE_ADDR'];
//var_dump(GDir::rmpath(PATH_DOC_ROOT.DIRECTORY_SEPARATOR."aa"));

//var_dump(GDir::getFileList(PATH_DOC_ROOT.DIRECTORY_SEPARATOR."aa"));

echo GMysql::$conn;
?>