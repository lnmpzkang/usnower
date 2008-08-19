<?php
include '../common.inc.php';

MO_Admin::checkRight();

include '../lib/smarty/Smarty.class.php';
include '../fckeditor/fckeditor.php';

$gmt = GSmarty::getInstance("admin");
$msg = "";

$token = $_GET["token"];
if(GToken::isToken($token,"getArt")){
	if(!isset($_GET["id"]))
		throw new GDataException("Invalidate Parameter!");
	
	$vo = new VO_Article();
	$vo->setId($_GET["id"]);
	$rst = MO_Article::get($vo);
	if(false != ($arr = GMysql::fetchArray($rst))){
		$catPath = explode("|",$arr["cat_path"]);
		$albums = explode("|",$arr["albums"]);
		$keywords = explode("|",$arr["keywords"]);
		$arr1 = array(
			"id"		=>	$arr["id"],
			"title"	=>	$arr["title"],
			"author"	=>	$arr["author"],
			"comeFrom"	=>	$arr["come_from"],
			"keywords"	=> "",
			"category"	=>	$arr["cat_id"],
			"album"		=>	"",
			"content"	=>	MO_Article::getContent($vo->getId()),
			"titleColor"=>	$arr["title_color"],
			"titleI"		=>	$arr["title_i"],
			"titleB"		=>	$arr["title_b"],
			"titleU"		=>	$arr["title_u"],
			"catPathName"	=>	$catPath[0],
			"albumNames"	=>	$albums[0],
			"keywords"		=> preg_replace("/\\,/s","|",$keywords[0])
		);
		$gmt->assign_by_ref("albumIds",split(",",$albums[1]));
		$gmt->assign_by_ref("art",$arr1);
	}
	mysql_free_result($rst);
}

// add new
$token = $_POST["token"];
if(GToken::isToken($token,"addArt",true)){
	try{
		$vo = new VO_Article();
		$vo->setTitle($_POST["title"]);
		$vo->setAuthor($_POST["author"]);
		$vo->setComeFrom($_POST["comeFrom"]);
		$vo->setKeywords($_POST["keywords"]);
		$vo->setCategory($_POST["category"]);
		$vo->setAlbums($_POST["album"]);//多选项
		$vo->setContent($_POST["content"]);
		
		$vo->setTitleColor($_POST["titleColor"]);
		isset($_POST["titleB"]) ? $vo->setTitleB( true ) : null;
		isset($_POST["titleI"]) ? $vo->setTitleI( true ) : null;
		isset($_POST["titleU"]) ? $vo->setTitleU( true ) : null;
		
		$vo->setAutoUploadOtherSItePic($_POST["autoUploadOtherSItePic"]);
		$vo->setPreventCopy($_POST["preventCopy"]);
		
		MO_Article::add($vo);
		$gmt->clear_cache(null,"artList");
	}catch(Exception $e){
		$msg .= $e->getMessage()."<br />";
		if(get_class($e) == "GSQLException"){
			throw $e;
		}
	}
	$gmt->assign_by_ref("art",$_POST);
	
}elseif(GTOken::isToken($token,"editArt")){
	try{
		$vo = new VO_Article();
		$vo->setId($_POST["id"]);
		$vo->setTitle($_POST["title"]);
		$vo->setAuthor($_POST["author"]);
		$vo->setComeFrom($_POST["comeFrom"]);
		$vo->setKeywords($_POST["keywords"]);
		$vo->setCategory($_POST["category"]);
		$vo->setAlbums($_POST["album"]);//多选项
		$vo->setContent($_POST["content"]);
		
		$vo->setTitleColor($_POST["titleColor"]);
		isset($_POST["titleB"]) ? $vo->setTitleB( true ) : null;
		isset($_POST["titleI"]) ? $vo->setTitleI( true ) : null;
		isset($_POST["titleU"]) ? $vo->setTitleU( true ) : null;
		
		$vo->setAutoUploadOtherSItePic($_POST["autoUploadOtherSItePic"]);
		$vo->setPreventCopy($_POST["preventCopy"]);
		
		MO_Article::edit($vo);
		$gmt->clear_cache(null,"artList");
		
		$gmt->clear_cache( "art.html", "art|".$vo->getId() );//删除以cache的文件
		
	}catch(GDataException $e1){
		$msg .= $e1->getMessage()."<br />";	
	}
}elseif(GToken::isToken($token,"deleteArtInIds")){
	$deletes = $_POST["delete"];
	if(is_array($deletes)){
		$vo = new VO_Article();
		MO_Article::deleteInIds(implode(",",$deletes));
		$gmt->clear_cache(null,"artList");
		header("location:".$_POST["url"]);
	}
}


$gmt->assign("msg",$msg);

$gfck = new GFCKEditor();
$gmt->register_object("FCKEditor",$gfck,array("createForSmarty"));

$artCatXMLFile = PATH_ROOT_ABS . "/" . GConfig::DIR_XML_STORE . "/artCat.xml";
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


$rst = MO_Album::getList();
$album = array();
while (false != ($arr = GMysql::fetchArray($rst))){
	$album[$arr["id"]] = $arr["name"];
}
$gmt->assign_by_ref("artAlbum",$album);
mysql_free_result($rst);

$gmt->display("admin/art.html");
?>