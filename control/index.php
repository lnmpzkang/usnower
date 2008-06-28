<?php
include '../common.inc.php';

$sFile = PATH_DOC_ROOT.DIRECTORY_SEPARATOR.GConfig::DIR_TPL_CACHED.DIRECTORY_SEPARATOR."admin/index.tpl";
if(false === ($tpl = GTpl::loadTpl($sFile))){
	$tpl = new GTpl( PATH_DOC_ROOT."/".GConfig::DIR_TPL, PATH_DOC_ROOT."/".GConfig::FILE_TPL_LOG);
	$tpl->load(array(
		"index"		=>	"admin/index.html"
	));
	$tpl->saveToFile($sFile);
}
$tpl->parse("index");
GDir::
?>