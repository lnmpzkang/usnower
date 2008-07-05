<?php
include "../common.inc.php";

$sFile = PATH_DOC_ROOT."/".GConfig::DIR_TPL_CACHED."/admin/menu.tpl";
if(false === ($tpl = GTpl::loadTpl($sFile))){
	$tpl = new GTpl( PATH_DOC_ROOT."/".GConfig::DIR_TPL, PATH_DOC_ROOT."/".GConfig::FILE_TPL_LOG);
	$tpl->load(array(
		"menu"	=>	"admin/menu.html"
	));
	$tpl->saveToFile($sFile);
}

$tpl->parse("menu");
?>