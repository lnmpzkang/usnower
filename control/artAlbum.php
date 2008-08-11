<?php
include '../common.inc.php';

MO_Admin::checkRight();

include '../lib/smarty/Smarty.class.php';

$gmt = GSmarty::getInstance("admin");

$token =$_POST["token"];
if(GToken::isToken($token,"artAlbum",TRUE)){
	$msg = "";
	$deletes = $_POST["delete"];
	
	$vo = new VO_Album();
	if(is_array($deletes)){
		foreach ($deletes as $delete){
			try{
				$vo->setId($delete);
				MO_Album::delete($vo);
			}catch (GSQLException $e1){
				$msg .= $e1->getMessage()."<br />";
			}
		}
	}
	
	
	$ids = $_POST["id"];
	
	$names = $_POST["name"];
	$_names = $_POST["_name"];
	
	$descriptions = $_POST["description"];
	$_descriptions = $_POST["_description"];
	
	for($i = 0;$i< sizeof($names);$i++){
		try{
			if(isset($ids[$i]) && ($names[$i] != $_names[$i] || $descriptions[$i] != $_descriptions[$i])){//如果存在 $ids[$i],说明是原来就存在的，执行修改（删除动作在上面以执行过）
				$vo = new VO_Album();
				$vo->setId($ids[$i]);
				$vo->setName($names[$i]);
				$vo->setDescription($descriptions[$i]);
				MO_Album::edit($vo);
			}elseif(!isset($ids[$i])){//新增
				$vo = new VO_Album();
				$vo->setName($names[$i]);
				$vo->setDescription($descriptions[$i]);
				MO_Album::add($vo);
			}
		}catch(GSQLException $e2){
			if($e2->getErrCode() == 1062){
				$msg .= "$names[$i] was exists.<br />";
			}else{
				$msg .= $e2->getErrMsg()."<br />";
			}
		}catch (GDataException $e3){
			$msg .= $e3->getMessage()."<br />";
		}
	}
	
	$gmt->assign("msg",$msg);
}

$rst = MO_Album::getList();
$artAlbum = array();

while(false != ($arr = GMysql::fetchArray($rst))){
	array_push($artAlbum,$arr);
}

$gmt->assign_by_ref("artAlbum",$artAlbum);
$gmt->display("admin/artAlbum.html");
?>