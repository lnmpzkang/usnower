<?php
class GSmarty{

	/**
	 * 取得Smarty 实例
	 *
	 * @return Smarty
	 */
	public static function getInstance($subDir = "") {
		$smt = new Smarty();
		$smt->register_function("const","__const__");
		$smt->register_function("token","__token__",false);
		$smt->register_block("design","__design__");
		$smt->register_function("artList","__artList__");
		$smt->register_function("bagList","__bagList__");
		
		$smt->template_dir = PATH_ROOT_ABS."/".GConfig::DIR_TPL;
		$smt->cache_dir = PATH_ROOT_ABS."/".GConfig::DIR_TPL_CACHED."/".$subDir;
		$smt->compile_dir = PATH_ROOT_ABS."/".GConfig::DIR_TPL_COMPILE."/".$subDir;
		
		if(!is_dir($smt->cache_dir))
			GDir::mkpath($smt->cache_dir);
		if(!is_dir($smt->compile_dir))
			GDir::mkpath($smt->compile_dir);
		
		return $smt;
	}
}

function __token__($params){
	$form = null;
	extract($params);
	if($form == null || trim($form) == "")
		Smarty::trigger_error("Attribute : form required!");
	else
		echo GToken::newToken($form);
}

function __const__($params) {
	$scope = null;
	$key = null;
	extract ( $params );
	if ($scope == null)
		echo constant ( sprintf ( "%s", $key ) ); else
		echo constant ( sprintf ( "%s::%s", $scope, $key ) );
}

function __design__(){
	return "";
}


/**
 * Enter description here...
 *
 * @param unknown_type $params
 * @param unknown_type $content
 * @param Smarty $smarty
 * @param unknown_type $repeat
 * @return unknown
 */
function __artList__($params,&$smarty){
		$cat = null;
		$num = null;
		extract($params);
		$list = MO_Article::getTopList($cat,$num);
		$smarty->assign_by_ref($params["assign"],$list);
}
/**
 * Enter description here...
 *
 * @param unknown_type $params
 * @param Smarty $smarty
 */

function __bagList__($params,&$smarty){
	$cat = null;
	$num = null;
	extract($params);
	$list = MO_Bag::getTopList($cat,$num);
	$smarty->assign_by_ref($params["assign"],$list);
}

?>
