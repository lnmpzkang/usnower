<?php
include 'common.inc.php';
include 'lib/smarty/Smarty.class.php';

$token = $_POST['token'];

$msg = null;

if(GToken::isToken($token,'comment',true)){
	$vo = new VO_Comment();
	try{
		$vo->setForId($_POST['forId']);
		$vo->setName($_POST['name']);
		$vo->setEmail($_POST['email']);
		$vo->setHttp($_POST['http']);
		$vo->setContent($_POST['content']);
		
		$vo->setIp(REMOTE_ADDR);
		$vo->setTag('A');
		
		MO_Comment::add($vo);
		$msg = 'Your message has been post success.And wait ';
		
		MO_Comment::exportXML($_POST['forId'],'A');
	}catch(GDataException $e){
		$msg = $e->getMessage();
	}
	$id = $_POST['forId'];
}

if(!isset($id)){
	$id = intval($_GET["id"]);
	if($id <= 0)
		throw new GDataException("Invalid param!");
}

$subDir = intval ( $id / 100 );
$gmt = GSmarty::getInstance ( "art/$subDir" );
$gmt->assign('msg',$msg);



function __getClickNum__(){
	global $id;
	return MO_Article::updateAndGetClick($id);
}


/**
 * Enter description here...
 *
 * @param unknown_type $param
 * @param Smarty $smarty
 */
function __nextAndPre__($params,$content,&$smarty){
	if(isset($content)){
		return $content;
	}else{
		$assign = null;
		extract($params);
		global $id;
		$arr = MO_Article::getPreAndNext($id);
		$smarty->assign_by_ref($assign,$arr);
		return null;
	}	
}

$gmt->register_function("getClickNum","__getClickNum__",false);
$gmt->register_block("nextAndPre","__nextAndPre__",false);

//MO_Article::getPreAndNext($id);

if (! $gmt->is_cached ( "art.html", "art|$id" )) {
	$gmt->caching = true;
	
	$artXMLPath = MO_Article::getArtPath ( $id );
	$dom = new DOMDocument ( '1.0', 'utf-8' );
	$f = true;
	if (! is_file ( $artXMLPath )) {
		$dom = MO_Article::exportXML ( $id );
	} else {
		$f = $dom->load ( $artXMLPath );
	}
	
	if($dom == false || $f == false){
		header("HTTP/1.1 404 Not Found");
		exit();
	}
	
	$showAble = $dom->getElementsByTagName ( "showAble" )->item ( 0 )->nodeValue;
	$commentAble = $dom->getElementsByTagName ( "showAble" )->item ( 0 )->nodeValue;
	
	if(!$showAble){
		$art = array(
			'title' 			=> $dom->getElementsByTagName ( "title" )->item ( 0 )->nodeValue,
			'content' 		=> 'Not Show able!',
			'nextId' 		=> $dom->getElementsByTagName ( 'nextId' )->item ( 0 )->nodeValue,
			'nextTitle' 	=> $dom->getElementsByTagName ( 'nextTitle' )->item ( 0 )->nodeValue,
			'preId'	 		=> $dom->getElementsByTagName ( 'preId' )->item ( 0 )->nodeValue, 
			'preTitle' 		=> $dom->getElementsByTagName ( 'preTitle' )->item ( 0 )->nodeValue,		
		);
	}else{	
		$art = array (
					'id'				=> $dom->getElementsByTagName('id')->item(0)->nodeValue,
					'showAble' 		=> $showAble, 
					'commentAble' 	=> $commentAble, 
					'title' 			=> $dom->getElementsByTagName ( "title" )->item ( 0 )->nodeValue, 
					'author' 		=> $dom->getElementsByTagName ( "author" )->item ( 0 )->nodeValue, 
					'comeFrom' 		=> $dom->getElementsByTagName ( 'comeFrom' )->item ( 0 )->nodeValue, 
					'inTime' 		=> $dom->getElementsByTagName ( 'inTime' )->item ( 0 )->nodeValue, 
					'catId' 			=> $dom->getElementsByTagName ( 'catId' )->item ( 0 )->nodeValue, 
					'catName' 		=> $dom->getElementsByTagName ( 'catName' )->item ( 0 )->nodeValue, 
					'albums' 		=> $dom->getElementsByTagName ( 'albums' )->item ( 0 )->nodeValue, 
					'keywords' 		=> $dom->getElementsByTagName ( 'keywords' )->item ( 0 )->nodeValue, 
					'content' 		=> $dom->getElementsByTagName ( 'content' )->item ( 0 )->nodeValue,
					'nextId' 		=> $dom->getElementsByTagName ( 'nextId' )->item ( 0 )->nodeValue,
					'nextTitle' 	=> $dom->getElementsByTagName ( 'nextTitle' )->item ( 0 )->nodeValue,
					'preId'	 		=> $dom->getElementsByTagName ( 'preId' )->item ( 0 )->nodeValue, 
					'preTitle' 		=> $dom->getElementsByTagName ( 'preTitle' )->item ( 0 )->nodeValue
				);
	}
	$arr = explode('|',$dom->getElementsByTagName ( 'catPath' )->item ( 0 )->nodeValue);
	$names = explode(',',$arr[0]);
	$ids = explode(',',$arr[1]);
	
	$art['catPath'] = array('ids'=>$ids,'names'=>$names);
	unset ($arr);
	unset($ids);
	unset($names);
	
	$arr = explode('|',$dom->getElementsByTagName('keywords')->item(0)->nodeValue);
	$names = explode(',',$arr[0]);
	$ids = explode(',',$arr[1]);
	$art['keywords'] = array('ids'=>$ids,'names'=>$names);
	unset($arr);
	unset($names);
	unset($ids);
	
	/*$piText = "type='text/xsl' href='art.xsl'";
	$pi = $dom->createProcessingInstruction("xml-stylesheet", $piText);
	$dom->insertBefore($pi,$dom->firstChild);
	
	echo $dom->saveXML();*/
	
	unset ( $dom );
	
	$gmt->assign_by_ref ( "art", $art );
}

$gmt->display("art.html","art|$id");
?>