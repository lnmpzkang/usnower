<?php
include 'common.inc.php';
$msg = $_REQUEST["msg"];
if(null != $msg && "" != trim($msg)){
	$msg = GEncrypt::decrypt($msg,GConfig::ENCRYPT_KEY);
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
</head>

<body>
<?php echo $msg; ?>
</body>
</html>