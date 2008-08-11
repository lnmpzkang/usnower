<?php
include '../common.inc.php';

MO_Admin::checkRight();

include '../lib/smarty/Smarty.class.php';

$gmt = GSmarty::getInstance ("admin");

$bagCatXMLFile = PATH_ROOT_ABS . "/" . GConfig::DIR_XML_STORE. "/bagCat.xml";
$bagCatXML = "";

$fatherid = $_GET["fatherid"];
if($fatherid == null) $fatherid = $_POST["fatherid"];
if($fatherid == null) $fatherid = 0;

if(isset($_POST["token"]))
	$token = $_POST ["token"];
elseif (isset($_GET["token"]))
	$token = $_GET["token"];

	
if(GToken::isToken( $token ,"refreshArtCat")){
	$gmt->clear_cache("admin/bagCategory.html");
	unlink($bagCatXMLFile);
}

if (GToken::isToken ( $token, "bagCategory", true )) {
	
	$deletes = $_POST["delete"];
	if(is_array($deletes)){
		foreach ($deletes as $del){
			$vo = new VO_ArtCategory();
			$vo->setId($del);
			MO_BagCategory::delete($vo);
		}
	}
	
	$names = $_POST ["name"];
	$_names = $_POST ["_name"];
	
	$ids = $_POST ["id"];
	
	$faIds = $_POST ["faId"];
	$_faIds = $_POST ["_faId"];
	
	$msg = "";
	for($i = 0; $i < sizeof ( $names ); $i ++) {
		$vo = new VO_BagCategory ( );
		try{
			$vo->setName ( $names [$i] );
			$vo->setFatherId ( $faIds [$i] );
			
			if (isset ( $ids[$i] )) { //如果存在，说明是修改或删除
				$vo->setId ( $ids [$i] );
				if ($names [$i] != $_names [$i] || $faIds [$i] != $_faIds [$i]) { //有修改
					MO_BagCategory::edit( $vo );
				}
			} else {
				MO_BagCategory::add ( $vo );
			}
		}catch(GDataException $e){
			$msg .= $e->getMessage()."<br />";
		}catch(GSQLException $e1){
			$errCode = $e1->getErrCode();
			if($errCode == 1062){
				$msg .= "$names[$i] was exists.<br />";
			}else{
				$msg .= $e1->getErrMsg();
			}
		}
	}
	
	$dom = MO_BagCategory::exportTree ();
	$dom->save ( $bagCatXMLFile );
	$bagCatXML = $dom->saveXML ();
	unset ( $dom );
	
	$gmt->assign("msg",$msg);
	$gmt->clear_cache("admin/bagCategory.html");
}

if (! file_exists ( $bagCatXMLFile )) {
	$dom = MO_BagCategory::exportTree ();
	$dom->save ( $bagCatXMLFile );
	$bagCatXML = $dom->saveXML ();
	unset ( $dom );
} else {
	$bagCatXML = $bagCatXML != "" ? $bagCatXML : file_get_contents ( $bagCatXMLFile );
}

	$bagCat = array ( );
	$vo = new VO_BagCategory ( );
	if($fatherid != 0)
		$vo->setFatherId($fatherid);
	$rst = MO_BagCategory::getList ( $vo );
	while ( false != ($arr = GMysql::fetchArray ( $rst )) ) {
		$catPath = split ( "\\|", $arr ["cat_path"] );
		$arr ["namepath"] = $catPath [0];
		$arr ["idpath"] = $catPath [1];
		array_push ( $bagCat, $arr );
	}
	
	$gmt->assign_by_ref ( "bagCat", $bagCat );
	$gmt->assign(array(
		//"action"	=>	$_SERVER['PHP_SELF'],
		"bagCatXML"	=>	$bagCatXML
	));

$gmt->assign(array(
	"fatherid"	=>	$fatherid,
));

$gmt->display ( "admin/bagCategory.html" );
?>