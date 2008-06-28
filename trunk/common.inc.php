<?php
//define("PATH_DOC_ROOT",$_SERVER['DOCUMENT_ROOT']);
//define("PATH_FILE_DIR",dirname(__FILE__));
define("PATH_DOC_ROOT",dirname(__FILE__));
define("PATH_CONTENT_ROOT",str_ireplace(PATH_DOC_ROOT,"/",$_SERVER['DOCUMENT_ROOT']));

define("SYMBOL_NEWLINE","\r\n");

define("REMOTE_ADDR",$_SERVER["REMOTE_ADDR"]);


$includePath = array(
	PATH_DOC_ROOT."/lib",
	PATH_DOC_ROOT."/lib/api",
	PATH_DOC_ROOT."/lib/vo",
	PATH_DOC_ROOT."/lib/mo"
);

set_include_path(join(DIRECTORY_SEPARATOR == "/" ? ":" : ";",$includePath));

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
	}
	
	if(headers_sent()){
		echo $e->getMessage();
	}else{
		$msg = GEncrypt::encrypt( $msg != "" ? $msg : $e->getMessage(),GConfig::ENCRYPT_KEY);
		header("location:msg.php?msg=$msg");
	}
}

set_exception_handler("__showMsg");

session_start();
?>