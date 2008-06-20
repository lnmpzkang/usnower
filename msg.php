<?php
include 'common.inc.php';
$msg = $_REQUEST["msg"];
if(null != $msg && "" != trim($msg)){
	$msg = GEncrypt::decrypt($msg,GConfig::ENCRYPT_KEY);
	echo $msg;
}
?>