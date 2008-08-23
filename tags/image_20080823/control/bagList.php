<?php
include '../common.inc.php';

MO_Admin::checkRight();

include '../lib/smarty/Smarty.class.php';

$gmt = GSmarty::getInstance("admin");

$page = intval( $_GET["PAGE"]);
if(is_null($page) || $page < 1) $page = 1;

if(!$gmt->is_cached("admin/bagList.html","bagList|page_$page")){
	$gpage = new GPagination();
	$bagList = MO_Bag::getList($gpage);
	$gmt->assign_by_ref("bagList",$bagList);
	$gmt->register_object("page",$gpage,array("exportPageLabel","recordNum","totalPage","currPage","pageSize","rangeS","rangeE"));
	$gmt->caching = true;
	$gmt->assign("action","bag.php");
}

$gmt->display("admin/bagList.html","bagList|page_$page");
?>