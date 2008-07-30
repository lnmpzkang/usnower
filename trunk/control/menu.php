<?php
include "../common.inc.php";

/*$sFile = PATH_ROOT_ABS."/".GConfig::DIR_TPL_CACHED."/admin/menu.tpl";
if(false === ($tpl = GTpl::loadTpl($sFile))){
	$tpl = new GTpl( PATH_ROOT_ABS."/".GConfig::DIR_TPL, PATH_ROOT_ABS."/".GConfig::FILE_TPL_LOG);
	$tpl->load(array(
		"menu"	=>	"admin/menu.html"
	));
	$tpl->saveToFile($sFile);
}

$tpl->parse("menu");*/

include '../lib/smarty/Smarty.class.php';
$gmt = GSmarty::getInstance();
$gmt->caching = true;
$gmt->display("admin/menu.html");
?>