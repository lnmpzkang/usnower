<?php
class GSmarty{

	/**
	 * 取得Smarty 实例
	 *
	 * @return Smarty
	 */
	public static function getInstance() {
		$smt = new Smarty();
		$smt->register_function("const","__const__");
		$smt->register_function("token","__token__",false);
		$smt->register_block("design","__design__");
		$smt->register_block("artList","__artList__");
		
		$smt->template_dir = PATH_ROOT_ABS."/"."/".GConfig::DIR_TPL;
		$smt->cache_dir = PATH_ROOT_ABS."/"."/".GConfig::DIR_TPL_CACHED;
		$smt->compile_dir = PATH_ROOT_ABS."/"."/".GConfig::DIR_TPL_COMPILE;
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

function __artList__($params, $content,&$smarty, &$repeat){
	if(!$repeat){
		$str = "";
		$cat = null;
		$num = null;
		extract($params);
		$rst = MO_Article::getListForBlock($cat,$num);
		while(false != ($arr = GMysql::fetchArray($rst))){
			$str .= preg_replace(array('/\$title/s','/\$intime/s','/\$id/s'),
							 array($arr["title"],$arr["in_time"],$arr["id"]),
							 $content);
		}
		mysql_free_result($rst);
		return $str;		
	}	
}

?>
