<?php
include '../common.inc.php';
include '../lib/smarty/Smarty.class.php';

$gmt = GSmarty::getInstance();

$page = intval( $_GET["PAGE"]);
if(is_null($page) || $page < 1) $page = 1;

if(!$gmt->is_cached("admin/artList.html","artList|page_$page")){
	$gpage = new GPagination();
	//$gpage->pageSize = 2;
	$rst = MO_Article::getList($gpage);
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
	
	$gmt->register_object("page",$gpage,array("exportPageLabel","recordNum","totalPage","currPage","pageSize","rangeS","rangeE"));
	$gmt->assign("action","art.php");
}

$gmt->display("admin/artList.html","artList|page_$page");
?>