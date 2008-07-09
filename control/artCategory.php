<?php
include '../common.inc.php';

$token = $_POST["token"];
if(GToken::isToken($token,"artCategory",true)){
	$names = $_POST["name"];
	for($i = 0;$i < sizeof($names); $i++){
		$vo = new VO_ArtCategory();
		$vo->setName($names[$i]);
		MO_ArtCategory::add($vo);
	}
}

$sFile = $sFile = PATH_DOC_ROOT."/".GConfig::DIR_TPL_CACHED."/admin/artCategory.tpl";
if(false === ($tpl = GTpl::loadTpl($sFile))){
	$tpl = new GTpl( PATH_DOC_ROOT."/".GConfig::DIR_TPL, PATH_DOC_ROOT."/".GConfig::FILE_TPL_LOG);
	$tpl->load(array(
		"artCategory"	=>	"admin/artCategory.html"
	));
	$tpl->saveToFile($sFile);
}

$vo = new VO_ArtCategory();

$idx = 0;
$tpl->assign("idx",$idx);
$tpl->assign("action",$_SERVER['PHP_SELF']);
$rst = MO_ArtCategory::getList($vo);
while(false != ($arr = GMysql::fetchArray($rst))){
	$catPath = split("\\|",$arr["cat_path"]);
	$tpl->assign(array(
		"id"		=>	$arr["id"],
		"name"	=>	$arr["name"],
		"faId"	=>	$arr["fa_id"],
		"faName"	=>	$arr["fa_name"],
		"catNamePath"	=>	$catPath[0],
		"subNum"	=>	$arr["sub_num"],
		"idx"		=>	$idx ++
	));
	$tpl->parseBlock("blk_list");
}
$tpl->parse("artCategory");
?>