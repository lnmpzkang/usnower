<?php
include '../common.inc.php';
include '../lib/smarty/Smarty.class.php';

$token = $_POST['token'];
if(GToken::isToken($token,'editCmt',true)){
	//var_dump($_POST);
	
	$pass = $_POST['pass'];
	$delete = $_POST['delete'];
	$unpass = $_POST['unpass'];
	$update = $_POST['update'];
	
	//$update = 'A:1,1,2,2,3|B:4,5,6';
	
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
	
	$update = explode('|',$update);
	foreach ($update as $upd){
		
		$tmp = explode(':',$upd);
		$tag = $tmp[0];
		$forIds = explode(',',$tmp[1]);
		$forIds = array_unique($forIds);

		foreach ($forIds as $forId){
			MO_Comment::refreshComment($tag,$forId);
		}
	}
	
	header("location:".$_POST['url']);
}

$action = $_GET["action"];
$gpage = new GPagination();
$gmt = GSmarty::getInstance("admin");
$gmt->register_object("page",$gpage,array("exportPageLabel","recordNum","totalPage","currPage","pageSize","rangeS","rangeE"));

$cmtList = array();
if(GToken::isToken($action,'notPassed')){
	$cmtList = MO_Comment::getList($gpage,'A.SHOW_ABLE = FALSE');
	$gmt->assign('action','notPassed');
}elseif (GToken::isToken($action,'passed')){
	$cmtList = MO_Comment::getList($gpage,'A.SHOW_ABLE = TRUE');
	$gmt->assign('action','passed');
}

$gmt->assign_by_ref("cmtList",$cmtList);

$gmt->display("admin/cmt.html");
?>