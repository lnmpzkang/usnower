<?php
//define("PATH_ROOT_ABS",$_SERVER['DOCUMENT_ROOT']);
//define("PATH_FILE_DIR",dirname(__FILE__));
define("PATH_ROOT_ABS",dirname(__FILE__));
define("PATH_ROOT_RELATIVE",str_ireplace(PATH_ROOT_ABS,"/",$_SERVER['DOCUMENT_ROOT']));

define("SYMBOL_NEWLINE","\r\n");

define("REMOTE_ADDR",$_SERVER["REMOTE_ADDR"]);

define("MUTI_CHAR_LEN",strlen("－"));


$includePath = array(
	PATH_ROOT_ABS."/lib",
	PATH_ROOT_ABS."/lib/api",
	PATH_ROOT_ABS."/lib/vo",
	PATH_ROOT_ABS."/lib/mo"
);

set_include_path(join(DIRECTORY_SEPARATOR == "/" ? ":" : ";",$includePath));

date_default_timezone_set(GConfig::TIMEZONE);

/**
 * 自动载入.
 *
 * @param string $class
 */
function __autoload($class){
	require("$class.inc.php");
}

/**
 * 最外层异常处理
 *
 * @param Exception $e
 */
function __showMsg($e){
	$msg = "";
	if(get_class($e) == "GAppException"){
		$msg = "Application Error!";
		GLoger::logToFile($e->__toString());
	}elseif(get_class($e) == "GSQLException"){
		$msg = "Application SQL Error!";
		GLoger::logToFile($e->getErrMsg().SYMBOL_NEWLINE.$e->getErrSql().SYMBOL_NEWLINE.$e->__toString());
	}
	
	if(headers_sent()){
		echo $e->getMessage();
	}else{
		$msg = GEncrypt::encrypt( $msg != "" ? $msg : $e->getMessage(),GConfig::ENCRYPT_KEY);
		header("location:".PATH_ROOT_RELATIVE."msg.php?msg=$msg");
	}
}

set_exception_handler("__showMsg");

session_start();
?>