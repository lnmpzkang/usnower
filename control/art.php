<?php
include '../common.inc.php';
include '../lib/smarty/Smarty.class.php';
include '../fckeditor/fckeditor.php';


$gmt = GSmarty::getInstance();

$gfck = new GFCKEditor();
$gmt->register_object("FCKEditor",$gfck,array("createForSmarty"));

$artCatXMLFile = PATH_DOC_ROOT . "/" . GConfig::DIR_TPL_CACHED . "/admin/artCat.xml";
$artCatXML = "";
if (! file_exists ( $artCatXMLFile )) {
	$dom = MO_ArtCategory::exportTree ();
	$dom->save ( $artCatXMLFile );
	$artCatXML = $dom->saveXML ();
	unset ( $dom );
} else {
	$artCatXML = file_get_contents ( $artCatXMLFile );
}
$gmt->assign_by_ref("artCatXML",$artCatXML);


$rst = MO_ArtAlbum::getList();
$album = array();
while (false != ($arr = GMysql::fetchArray($rst))){
	$album[$arr["id"]] = $arr["name"];
}
$gmt->assign_by_ref("artAlbum",$album);


$gmt->display("admin/art.html");
?>