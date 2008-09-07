<?php
$saveDir = 'data/';

$acceptDomainReg = '/(blueidea.com|dajiaozi.com){1}/i';

//var_dump(preg_match($acceptDomainReg,"<P><IMG src='http://www.blueidea.com/img/common/logo.gif'><BR></P>"));

$ctx = <<<ctx
<P>希望對有相同情況同志有個幫助。</P>
<P><BR><IMG src="http://blogbeta.blueidea.com/UploadFiles/2006-4/415160119.jpg"></P>
<P><IMG src="http://blogbeta.blueidea.com/UploadFiles/2006-4/415122216.jpg"><BR></P>
<P><IMG src='http://blogbeta.blueidea.com/UploadFiles/2006-4/415122217.jpg'><BR></P>
<P><IMG src='http://blogbeta.blueidea.com/UploadFiles/2006-4/415122218.jpg'   ><BR></P>
<P><IMG src=   'http://blogbeta.blueidea.com/UploadFiles/2006-4/415122219.jpg'><BR></P>
<P><IMG src=   'http://blogbeta.blueidea.com/UploadFiles/2006-4/415122219.jpg'><BR></P>
<P><IMG src=   'http://blogbeta.blueidea.com/UploadFiles/2006-4/415122219.jpg'><BR></P>
<P><IMG src='http://www.blueidea.com/img/common/logo.gif'><BR></P>
<P><IMG src= http://blogbeta.blueidea.com/UploadFiles/2006-4/415122220.jpg ><BR></P>
ctx;

$reg = '/<img.*src\s*=\s*(\'|")?(?<url>.*)\1.*>/iUs';
$ma = array();
if(preg_match_all($reg,$ctx,$ma)){
	$urls = array_unique($ma['url']);/////////用了 unique ，下标打乱了。
	$urls_ = array();
	$local = array();
	
	foreach($urls as $url){
		if(!preg_match($acceptDomainReg,$url)){	
			continue;
		}
		
		$name = basename($url);
		
		if(false != ($resCtx = @file_get_contents($url))){
			$fl = @fopen($saveDir.$name,'w+');
			fwrite($fl,$resCtx);
			array_push($urls_,$url);
			array_push($local,$saveDir.$name);
			fclose($fl);
		}else{
			//
		}
	}
	//var_dump($urls_);
	//var_dump($local);
	
	echo str_ireplace($urls_,$local,$ctx);
}
?>