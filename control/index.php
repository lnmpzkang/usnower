<?php
include '../common.inc.php';
include '../lib/smarty/Smarty.class.php';

$token = $_POST["token"];
if(GToken::isToken($token,"adminLogin",true)){
	$vo = new VO_Admin();
	$vo->setAdmin($_POST["admin"]);
	$vo->setPwd($_POST["pwd"]);
	try{
		MO_Admin::login($vo);
	}catch(GDataException $e){
		$msg = $e->getMessage(); 
	}
}

$gmt = GSmarty::getInstance();
if(MO_Admin::isLogined()){
	$gmt->caching = true;
	$gmt->display("admin/main.html");
}else{
	$gmt->assign("msg",$msg);
	$gmt->display("admin/index.html");
}

/*if(MO_Admin::isLogined()){
	$sFile = PATH_ROOT_ABS."/".GConfig::DIR_TPL_CACHED."/admin/main.tpl";
	if(false === ($tpl = GTpl::loadTpl($sFile))){
		$tpl = new GTpl(PATH_ROOT_ABS."/".GConfig::DIR_TPL,PATH_ROOT_ABS."/".GConfig::FILE_TPL_LOG);
		$tpl->load(array(
			"main"	=>	"admin/main.html"
		));
		$tpl->saveToFile($sFile);
	}
	$tpl->parse("main");
}else{
	
	$sFile = PATH_ROOT_ABS."/".GConfig::DIR_TPL_CACHED."/admin/index.tpl";
	if(false === ($tpl = GTpl::loadTpl($sFile))){
		$tpl = new GTpl( PATH_ROOT_ABS."/".GConfig::DIR_TPL, PATH_ROOT_ABS."/".GConfig::FILE_TPL_LOG);
		$tpl->load(array(
			"index"		=>	"admin/index.html"
		));
		$tpl->saveToFile($sFile);
	}	
	$tpl->assign(array(
		"msg"	=> $msg
	));
	$tpl->parseBlock("blk_adminLoginForm","cond_noLogin");
	$tpl->parse("index");
}*/
?>