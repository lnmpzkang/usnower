<?php
include '../common.inc.php';
include '../lib/smarty/Smarty.class.php';
include '../fckeditor/fckeditor.php';

$gmt = GSmarty::getInstance();
$msg = "";

$token = $_GET["token"];
if(GToken::isToken($token,"getBag",true)){
	$id = $_GET["id"];
	$vo = new VO_Bag();
	$vo->setId($id);
	$arr = MO_Bag::get($vo);
	
	$gmt->assign_by_ref("bag",$arr);
}

$token = $_POST["token"];
if(GToken::isToken($token,"addBag",true)){
	try{
		$vo = new VO_Bag();
		$vo->setName($_POST["name"]);
		$vo->setNo($_POST["no"]);
		$vo->setSizeH($_POST["sizeH"]);
		$vo->setSizeW($_POST["sizeW"]);
		$vo->setSizeL($_POST["sizeL"]);
		$vo->setUnit($_POST["unit"]);
		$vo->setFabric($_POST["fabric"]);
		$vo->setDescription($_POST["description"]);
		$vo->setCat($_POST["cat"]);
		
		$id = MO_Bag::add($vo);
		
		MO_BagPic::upload($id,$_FILES,$_POST["color"],$_POST["picDesc"]);
	}catch(GDataException $e1){
		$msg = $e1->getMessage();
	}catch(GSQLException $e2){
		if($e2->getErrCode() == 1062)
			$msg = "Style NO:".$vo->getNo()." has been exists!";
		else 
			throw $e2;
	}
	$gmt->assign("msg",$msg);
	$gmt->assign_by_ref("bag",$_POST);
	$gmt->clear_cache(null,"bagList");
}elseif(GToken::isToken($token,"deleteBagInIds",true)){
	$deletes = $_POST["delete"];
	if(is_array($deletes)){
		$vo = new VO_Bag();			
		foreach ($deletes as $delete){
			$vo->setId($delete);
			MO_Bag::delete($vo);
		}
		$gmt->clear_cache(null,"bagList");
		header("location:".$_POST["url"]);
	}
}

$gfck = new GFCKEditor();
$gmt->register_object("FCKEditor",$gfck,array("createForSmarty"));

$bagCatXMLFile = PATH_ROOT_ABS . "/" . GConfig::DIR_TPL_CACHED . "/bagCat.xml";
$bagCatXML = "";
if (! file_exists ( $bagCatXMLFile )) {
	$dom = MO_BagCategory::exportTree ();
	$dom->save ( $bagCatXMLFile );
	$bagCatXML = $dom->saveXML ();
	unset ( $dom );
} else {
	$bagCatXML = file_get_contents ( $bagCatXMLFile );
}
$gmt->assign_by_ref("bagCatXML",$bagCatXML);

$gmt->display("admin/bag.html");
?>