<?php
include '../common.inc.php';
include '../lib/smarty/Smarty.class.php';

$gmt = GSmarty::getInstance ();

$artCatXMLFile = PATH_DOC_ROOT . "/" . GConfig::DIR_TPL_CACHED . "/admin/artCat.xml";
$artCatXML = "";

if(isset($_POST["token"]))
	$token = $_POST ["token"];
elseif (isset($_GET["token"]))
	$token = $_GET["token"];
	
if(GToken::isToken( $token ,"refreshArtCat")){
	$gmt->clear_cache("admin/artCategory.html");
	unlink($artCatXMLFile);
}

if (GToken::isToken ( $token, "artCategory", true )) {
	$names = $_POST ["name"];
	$_names = $_POST ["_name"];
	
	$ids = $_POST ["id"];
	
	$faIds = $_POST ["faId"];
	$_faIds = $_POST ["_faId"];
	
	for($i = 0; $i < sizeof ( $names ); $i ++) {
		$vo = new VO_ArtCategory ( );
		$msg = "";
		try{
			$vo->setName ( $names [$i] );
			$vo->setFatherId ( $faIds [$i] );
			
			if (isset ( $ids [$i] )) { //如果存在，说明是修改或删除
				$vo->setId ( $ids [$i] );
				if ($names [$i] != $_names [$i] || $faIds [$i] != $_faIds [$i]) { //有修改
					MO_ArtCategory::edit ( $vo );
				}
			} else {
				MO_ArtCategory::add ( $vo );
			}
		}catch(GDataException $e){
			$msg .= $e->getMessage()."<br />";
		}catch(GAppException $e1){
			$msg .= $e1->getMessage();
		}
	}
	
	$dom = MO_ArtCategory::exportTree ();
	$dom->save ( $artCatXMLFile );
	$artCatXML = $dom->saveXML ();
	unset ( $dom );
	
	$gmt->assign("msg",$msg);
	$gmt->clear_cache("admin/artCategory.html");
}

if (! file_exists ( $artCatXMLFile )) {
	$dom = MO_ArtCategory::exportTree ();
	$dom->save ( $artCatXMLFile );
	$artCatXML = $dom->saveXML ();
	unset ( $dom );
} else {
	$artCatXML = $artCatXML != "" ? $artCatXML : file_get_contents ( $artCatXMLFile );
}
//echo $artCatXML;

if (!$gmt->is_cached ( "admin/artCategory.html" )) {
	$artCat = array ( );
	$vo = new VO_ArtCategory ( );
	$rst = MO_ArtCategory::getList ( $vo );
	while ( false != ($arr = GMysql::fetchArray ( $rst )) ) {
		$catPath = split ( "\\|", $arr ["cat_path"] );
		$arr ["namepath"] = $catPath [0];
		$arr ["idpath"] = $catPath [1];
		array_push ( $artCat, $arr );
	}
	$gmt->caching = true;
	$gmt->assign_by_ref ( "artCat", $artCat );
	$gmt->assign("action",$_SERVER['PHP_SELF']);
	$gmt->assign(array(
		"action"	=>	$_SERVER['PHP_SELF'],
		"artCatXML"	=>	$artCatXML
	));
	$gmt->cache_lifetime = 86400;// 60 * 60 * 24
}
$gmt->assign("token",GToken::newToken("artCategory"));
$gmt->display ( "admin/artCategory.html" );
?>