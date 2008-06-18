<?php
define("PATH_DOC_ROOT",$_SERVER['DOCUMENT_ROOT']);
define("PATH_FILE_DIR",dirname(__FILE__));

$includePath = array(
	PATH_DOC_ROOT."/lib",
	PATH_DOC_ROOT."/lib/api"
);

set_include_path(join(DIRECTORY_SEPARATOR == "/" ? ":" : ";",$includePath));

/**
 * 自动载入.
 *
 * @param string $class
 */
function __autoload($class){
	include("$class.inc.php");
}


?>