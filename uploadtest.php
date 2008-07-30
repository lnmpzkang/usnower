<?php
include 'common.inc.php';
$token = $_POST["token"];
if(GToken::isToken($token,"upload")){
	$up = new GUpload();
	$up->setSaveDir(PATH_ROOT_ABS."/data/upload");
	$up->setAutoname(TRUE);
	$up->setMaxSize(1024);
	$up->setAccept(array("jpg","gif","png"));
	$files = $up->uploadAll();
	$mi = new GMiniature();
	foreach ($files as $file){
		$mi->setSaveDir(PATH_ROOT_ABS."/data/upload/mini");
		$mi->zoomByFixSize($file,200,200);
		$mi->saveAsPng(basename($file));
		$mi->setSaveDir(PATH_ROOT_ABS."/data/upload/mini2");
		$mi->zoomBySize($file,200,200);
		$mi->saveAsPng(basename($file));
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
</head>

<body>
<form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post" enctype="multipart/form-data">
<input type="hidden" name="token" value="<?php echo GTOken::newToken("upload") ?>" />
<input type="file" name="pic" />
<input name="myPic" type="file" />
<input type="submit" value="Submit" />
</form>
</body>
</html>
