<?php
class GSmarty extends Smarty {
	
	public function __construct() {
		
	}
	
	/**
	 * 取得GSmarty 实例
	 *
	 * @return GSmarty
	 */
	public static function getInstance() {
		$smt = new GSmarty();
		$smt->register_function("const","__const__");
		$smt->register_block("design","__design__");
		
		$token = new GToken();
		$smt->register_object("token",$token,array("newTokenForSmarty"));
		
		$smt->template_dir = PATH_DOC_ROOT."/"."/".GConfig::DIR_TPL;
		$smt->cache_dir = PATH_DOC_ROOT."/"."/".GConfig::DIR_TPL_CACHED;
		$smt->compile_dir = PATH_DOC_ROOT."/"."/".GConfig::DIR_TPL_COMPILE;
		return $smt;
	}
}

function __const__($params) {
	$scope = null;
	$key = null;
	extract ( $params );
	if ($scope == null)
		echo constant ( sprintf ( "%s", $key ) ); else
		echo constant ( sprintf ( "%s::%s", $scope, $key ) );
}

function __design__($params, $content, &$smarty){
	return "";
}

?>
