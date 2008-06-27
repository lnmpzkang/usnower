<?php
include("common.inc.php");
//GLoger::reportToAdmin("sadfasfd");
//GLoger::logToFile("custom.log","aaaa");

//GDir::mkpath("/aa/bb/CC/DD");
//echo $_SERVER['REMOTE_ADDR'];
//var_dump(GDir::rmpath(PATH_DOC_ROOT.DIRECTORY_SEPARATOR."aa"));

//var_dump(GDir::getFileList(PATH_DOC_ROOT.DIRECTORY_SEPARATOR."aa"));

/*$vo = new VO_Admin();
$vo->setAdmin("xlingfairy");
$vo->setPwd("fp51gfa");
MO_Admin::login($vo);
MO_Admin::checkLoginStatus();*/

/*var_dump($_SERVER);*/
//var_dump(headers_list());
//var_dump($_REQUEST);
/*$name = "xling";
$b = file_get_contents("tpl/default/admin/loginForm.html");
echo eval("?>$b<?php ");*/

/*$key = "name";
$str = "You Name is:{:name}";
$reg = "/{:$key}/iUs";

echo preg_replace($reg,"xling",$str);*/

/*$str = "Token:{@GToken::newToken('test')}{@aa()}";
$reg = "/{@([^{}]*)}/iUs";
//preg_match($reg,$str,$match);
//var_dump($match);
echo eval("?>".preg_replace($reg,"<?php echo $1 ?>",$str)."<?php ");

function callback($match){
	$fun = substr($match[1],0,strpos($match[1],"("));
	if(function_exists($fun) || is_callable($fun)){
		return "<?php echo $match[1] ?>";
	}else{
		return $match[0];
	}
}

function aa1(){
	return "here is aa!";
}

$str = eval(" ?>".preg_replace_callback($reg,"callback",$str)."<?php ");
echo $str;*/
/*$begin = microtime(TRUE);*/
$tpl = new GTpl(PATH_DOC_ROOT."/tpl/default",PATH_DOC_ROOT."/logs/tpl.log");
$tpl->load("adminLogin","admin/loginForm.html");
$tpl->assign(array(
	"formAction"	=>	$_SERVER['PHP_SELF'],
	"admin"			=>	$_POST["admin"]
));

$tpl->parseBlock("blk_adminLoginForm","cond_noLogin");
$tpl->parse("adminLogin");
/*echo (microtime(TRUE) - $begin);*/
?>