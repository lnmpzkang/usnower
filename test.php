<?php
//include ("common.inc.php");
//GLoger::reportToAdmin("sadfasfd");
//GLoger::logToFile("custom.log","aaaa");


//GDir::mkpath("/aa/bb/CC/DD");
//echo $_SERVER['REMOTE_ADDR'];
//var_dump(GDir::rmpath(PATH_ROOT_ABS.DIRECTORY_SEPARATOR."aa"));


//var_dump(GDir::getFileList(PATH_ROOT_ABS.DIRECTORY_SEPARATOR."aa"));


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
/*$sFile = PATH_ROOT_ABS."/cached/tpl/default/admin/loginForm";
if(false === ($tpl = GTpl::loadTpl($sFile))){
	$tpl = new GTpl(PATH_ROOT_ABS."/tpl/default",PATH_ROOT_ABS."/logs/tpl.log");
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
/*echo PATH_ROOT_ABS;
echo $_SERVER['DOCUMENT_ROOT']*/

/*class TestClass {
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

echo "Now checking value of class3: " . $class3->thisVar . "\n";*/

/*echo MO_ArtCategory::exportTree();

$imgs = $_FILES["img"];
$dir = $_SERVER['DOCUMENT_ROOT']."/"."uploads";
for($i = 0;$i < 5 ;$i++){
	if(empty($imgs[$i])) continue;
	$img = $imgs[$i];
	
	if ( $img["size"] > 100 * 1024 ){
		continue;
	}
	$file = $dir."/".$img["name"];
	if(file_exists($file)){
		continue;
	}
	
	move_uploaded_file($img["tmpname"],$dir);
}
*/

/*$content = "aa";
$reg = "~('?)(.*)('?)~";
$ma = array();
preg_match_all($reg,$content,$ma);
var_dump($ma);*/

/*$content = 'asadf<img width="100" src    =   http://www.google.com/q.gif alt= aa />dfa<img src=  "http://www.baidu.com/logo.gif" alt= "bbb" />';
$reg = "~(<img.*src\\s*=\\s*[\"'])(.*)([\"'].*>)~iUs";
//$reg = "~(<img.*alt\\s*=\\s*[\"']?)(?<alt>[^\"']*)([\"']?\\b.*>)~iUs";
$reg = "~(<img.*alt\\s*=\\s*[\"']?)(?<alt>[^\"']*)([\"']?\\b.*>)~iUs";

$reg="~<img.*src\\s*=\\s*(?<mark>[\"'])?\\s*(?<imgUrl>[^\\s\"']*)\\s*(\\k<mark>)?\\b.?\\s*>~iUs";


$ma = array();
preg_match_all($reg,$content,$ma);
var_dump($ma)*/

//var_dump(GValidate::checkString(null,array("required"=>false,"min"=>3,"max"=>10)));
//var_dump(GValidate::checkNumRange(2,array("required"=>false,"min"=>-1)));
//var_dump($_GET);


//var_dump(GValidate::checkString ("xling",array("required"=>true,"max"=>30)));

//var_dump( explode("|",null) );
//echo sprintf("aa%d",true);

/*function test(){
		function removeBlank($var){
			if($var == null || trim($var) == "") return false;
			else return true;
		}
	$arr = explode("|","a|b|c|");
	//var_dump( array_filter($arr,"removeBlank"));	
	var_dump(implode(",",$arr));	
}
test();*/
//var_dump($arr);


/*if (preg_match('/^[\\x{4e00}-\\x{9fa5}]+$/u', '奥运')){
  echo '全是汉字';
}else{
  echo '不全是汉字';
}*/

//echo preg_replace("/^[\\x{4e00}-\\x{9fa5}]+$/s","","aa'|bb_|大小，");
//echo preg_replace('/[\\x{4e00}-\\x{9fa5}]+/s',"","abc一二三cde");

//echo preg_replace('/[^a-z0-9\x{4e00}-\x{9fa5}\|]/iu',"","aa'|bb_|大小，");

/*function addQuote($item){
	return "'".$item."'";
}
$str = preg_replace('/[^a-z0-9\x{4e00}-\x{9fa5}\|]/isu',"","aa'|bb_|大小，");
$arr = explode("|",$str);
//array_walk($arr,"addQuote");
$arr = array_map("addQuote",$arr);
var_dump($arr);*/

/*function test(){
		$arr = array("aa","bb","cc");
		$keywordsString = implode(",",$arr);
		echo $keywordsString;
}
test();*/

/*echo PATH_ROOT_RELATIVE.GConfig::DIR_UPLOAD ."<br />";
echo PATH_ROOT_ABS.PATH_ROOT_RELATIVE.GConfig::DIR_UPLOAD ;*/
/*
include 'lib/smarty/Smarty.class.php';
$gmt = GSmarty::getInstance();
//$gmt->caching = true;
$gmt->display("test.html");

//echo MO_Article::getListForBlock();
*/

/*var_dump(intval("-1a13"));*/

/*var_dump(GValidate::checkNumber(" a",array("required"=>false,"min"=>0,"max"=>2)));*/

//var_dump(MO_Bag::getList1());

//var_dump(MO_Bag::getTopList(0,10));

//MO_Admin::checkRight();

/*echo intval(1/100)."<br/>";
echo intval(2/100)."<br/>";
echo intval(101/100)."<br/>";
echo intval(305/100)."<br/>";*/

/*require 'lib/smarty/Smarty.class.php';

*//**
 * Enter description here...
 *
 * @param unknown_type $params
 * @param unknown_type $content
 * @param Smarty $smarty
 * @param unknown_type $repeat
 * @return unknown
 *//*
function __test__($params, $content, &$smarty, &$repeat){
	echo intval($repeat)." ".intval(isset($content))."<br />";
	if(isset($content)){
		return $content;
	}else{
		$cat = null;
		$num = null;
		$assign = null;
		extract($params);
		$list = MO_Article::getTopList($cat,$num);
		$smarty->assign_by_ref($assign,$list);		
	}
}

$gmt = GSmarty::getInstance("test");
$gmt->register_block('test','__test__',false);
$gmt->display("test.html");*/

/*
$url = "https://usnower.com";
$url2 = "aaa.com";
$url3 = "http://www.abcdefa";
var_dump(GValidate::checkURL($url,array('required'=>true)));
var_dump(GValidate::checkURL($url2,array('required'=>true)));
var_dump(GValidate::checkURL($url3,array('required'=>true)));
*/

/*function aa($dec){
	$utf = chr(224 + (($dec - ($dec % 4096)) / 4096));
	$utf .= chr(128 + ((($dec % 4096) - ($dec % 64)) / 64));
	$utf .= chr(128 + ($dec % 64));
	return $utf;
}
echo aa(20013).aa(22269);*/


/*$str = urldecode("&#20013;&#22269;&#35821;&#35328;&#25991;");
$test = preg_replace('/(\&\#)(\d{5});/mise',"iconv('UCS-2','GBK',pack('H4',dechex(\\2)) )",$str);
echo $test;*/

/*header("Cache-Control: public");
header('content-type:application/vnd.ms-excel');
header("Content-Disposition:attachment; filename=report.xls"); 

include 'lib/smarty/Smarty.class.php';
$gmt = GSmarty::getInstance('report');

$employ = array();
$employ[] = array(
	'name'	=>	'阮刚',
	'sex'		=>	'男',
	'birthday'	=>	'1983-07-14',
	'phone'		=>	'15986627851',
	'email'		=>	'xlingfairy@hotmail.com'
);

$employ[] = array(
	'name'	=>	'薛娟',
	'sex'		=>	'女',
	'birthday'	=>	'1983-07-14',
	'phone'		=>	'15986627851',
	'email'		=>	'snow@hotmail.com'
);

$employ[] = array(
	'name'	=>	'王涛',
	'sex'		=>	'男',
	'birthday'	=>	'1983-07-14',
	'phone'		=>	'15986627851',
	'email'		=>	'werewi@hotmail.com'
);

$gmt->assign_by_ref('employ',$employ);
$gmt->display('report');*/


/*function dl_file($file){

   //First, see if the file exists
   if (!is_file($file)) { die("<b>404 File not found!</b>"); }

   //Gather relevent info about file
   $len = filesize($file);
   $filename = basename($file);
   $file_extension = strtolower(substr(strrchr($filename,"."),1));

   //This will set the Content-Type to the appropriate setting for the file
   switch( $file_extension ) {
     case "pdf": $ctype="application/pdf"; break;
     case "exe": $ctype="application/octet-stream"; break;
     case "zip": $ctype="application/zip"; break;
     case "doc": $ctype="application/msword"; break;
     case "xls": $ctype="application/vnd.ms-excel"; break;
     case "ppt": $ctype="application/vnd.ms-powerpoint"; break;
     case "gif": $ctype="image/gif"; break;
     case "png": $ctype="image/png"; break;
     case "jpeg":
     case "jpg": $ctype="image/jpg"; break;
     case "mp3": $ctype="audio/mpeg"; break;
     case "wav": $ctype="audio/x-wav"; break;
     case "mpeg":
     case "mpg":
     case "mpe": $ctype="video/mpeg"; break;
     case "mov": $ctype="video/quicktime"; break;
     case "avi": $ctype="video/x-msvideo"; break;

     //The following are for extensions that shouldn't be downloaded (sensitive stuff, like php files)
     case "php":
     case "htm":
     case "html":
     case "txt": die("<b>Cannot be used for ". $file_extension ." files!</b>"); break;

     default: $ctype="application/force-download";
   }

   //Begin writing headers
   header("Pragma: public");
   header("Expires: 0");
   header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
   header("Cache-Control: public"); 
   header("Content-Description: File Transfer");
   
   //Use the switch-generated Content-Type
   header("Content-Type: $ctype");

   //Force the download
   $header="Content-Disposition: attachment; filename=".$filename.";";
   header($header );
   header("Content-Transfer-Encoding: binary");
   header("Content-Length: ".$len);
   @readfile($file);
   exit;
}

//dl_file('lan.zip');

//header("Pragma: public");
//header("Expires: 0");
//header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Cache-Control: public"); 
//header("Content-Description: File Transfer");
header("Content-Type: application/zip");
header("Content-Disposition: attachment; filename=lan.zip;");
//header("Content-Transfer-Encoding: binary");
//header("Content-Length: ".filesize("lan.zip"));
@readfile("lan.zip");*/

/*$arr = array('name'=>'a','sex'=>'m');
$str = var_export($arr,true);
if(!is_file('a')) GFile::create('a');
GFile::rewrite('a',$str);*/
$output = null;
exec('tar xzvf zlib.tar.gz -C '.dirname(__FILE__),$output);
var_dump($output);
?>