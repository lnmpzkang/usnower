<?php
include ("common.inc.php");
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
/*$sFile = PATH_DOC_ROOT."/cached/tpl/default/admin/loginForm";
if(false === ($tpl = GTpl::loadTpl($sFile))){
	$tpl = new GTpl(PATH_DOC_ROOT."/tpl/default",PATH_DOC_ROOT."/logs/tpl.log");
	$tpl->load("adminLogin","admin/loginForm.html");
	$tpl->assign(array(
		"formAction"	=>	$_SERVER['PHP_SELF'],
		"admin"			=>	$_POST["admin"]
	));
	$tpl->saveToFile($sFile);
}

$tpl->parseBlock("blk_adminLoginForm","cond_noLogin");
$tpl->parse("adminLogin");*/
/*echo (microtime(TRUE) - $begin);*/
/*echo PATH_DOC_ROOT;
echo $_SERVER['DOCUMENT_ROOT']*/

class TestClass {
	var $thisVar = 0;
	
	function TestClass($value) {
		$this->thisVar = $value;
	}
	
	function &getTestClass($value) {
		static $classes;
		
		if (! isset ( $classes [$value] )) {
			$classes [$value] = new TestClass ( $value );
		}
		
		return $classes [$value];
	}
}

echo "<pre>";

echo "Getting class1 with a value of 432\n";
$class1 = & TestClass::getTestClass ( 432 );
echo "Value is: " . $class1->thisVar . "\n";

echo "Getting class2 with a value of 342\n";
$class2 = & TestClass::getTestClass ( 342 );
echo "Value is: " . $class2->thisVar . "\n";

echo "Getting class3 with the same value of 432\n";
$class3 = & TestClass::getTestClass ( 432 );
echo "Value is: " . $class3->thisVar . "\n";

echo "Changing the value of class1 to 3425, which should also change class3\n";
$class1->thisVar = 3425;

echo "Now checking value of class3: " . $class3->thisVar . "\n";

?>