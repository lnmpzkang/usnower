<?php
include 'common.inc.php';

$token = $_GET['token'];
if(GToken::isToken($token,'getComment',true)){
	header('content-type:text/xml');
	header('Expires:'.gmdate ("D, d M Y H:i:s", time() + 3600 * 1). " GMT");//一小时后过期
	
	$forId = $_GET['id'];
	$tag = $_GET['tag'];
	
	$path = MO_Comment::getCommentPath($forId,$tag);

	if(!is_file($path)){
		if(false != ($dom = MO_Comment::exportXML($forId,$tag))){
			echo $dom->saveXML();
		}else
			echo '<xml><error msg="No Comment" /></xml>';
	}else	
		echo file_get_contents($path);
}
?>