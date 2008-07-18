<?php
include '../common.inc.php';
include '../lib/smarty/Smarty.class.php';

$page = $_GET["page"];
$gmt = GSmarty::getInstance();

if(!is_int($page)) $page = 0;

if(!$gmt->is_cached("admin/artList.html")){

	$rst = MO_Article::getList();
	$artList = array();
	while(false != ($arr = GMysql::fetchArray($rst))){
		$artPath = explode("|",$arr["cat_path"]);
		$arr["catPathName"] = $artPath[0];
		
		$albums = explode("|",$arr["albums"]);
		$arr["albumNames"] = $albums[0];
		
		$keywords = explode("|",$arr["keywords"]);
		$arr["keywords"] = $keywords[0];
		
		array_push($artList,$arr);
	}
	$gmt->caching = true;
	$gmt->assign_by_ref("artList",$artList);
}

$gmt->display("admin/artList.html");
?>