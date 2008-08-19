<?php
include '../common.inc.php';
include '../lib/smarty/Smarty.class.php';

$token = $_POST['token'];
if(GToken::isToken($token,'editCmt',true)){
	//var_dump($_POST);
	$pass = $_POST['pass'];
	$delete = $_POST['delete'];
	$unpass = $_POST['unpass'];
	
	if(is_array($pass)){
		foreach ($pass as $item){
			MO_Comment::pass($item);
		}
	}
	
	if(is_array($delete)){
		foreach ($delete as $item){
			MO_Comment::delete($item);
		}
	}
	
	if(is_array($unpass)){
		foreach ($unpass as $item){
			MO_Comment::unpass($item);
		}
	}
	
	header("location:".$_POST['url']);
}

$action = $_GET["action"];
$gpage = new GPagination();
$gmt = GSmarty::getInstance("admin");
$gmt->register_object("page",$gpage,array("exportPageLabel","recordNum","totalPage","currPage","pageSize","rangeS","rangeE"));

if(GToken::isToken($action,'notPassed')){
	$cmtList = MO_Comment::getList($gpage);

	$gmt->assign_by_ref("cmtList",$cmtList);
	$gmt->assign('action','notPassed');
}

$gmt->display("admin/cmt.html");
?>