<?php
include '../common.inc.php';

set_time_limit(0);

$fl = fopen('backup.txt','r');

$catMap = array(
	'oracle'			=>	2,
	'java'			=>	3,
	'javascript'	=>	4,
	'js'				=>	4,
	'computer'		=> 5,
	'php'				=>	12,
	'mysql'			=>	12,
	'ubuntu'			=> 11,
	'linux'			=>	11,
	'music'			=>	10,
	'job'				=>	13	
);

function autoCategory($ma){
	return $ma[1];
} 

$cnt = '';


$dom = new DOMDocument('1.0','utf-8');
$root = $dom->createElement('root');
$dom->appendChild($root);


while( !feof($fl) ){		
	$ctx = iconv('gb18030','utf8', fgets($fl));
	//var_dump( iconv('gb2312','utf8',$ctx));
	//var_dump( strpos($ctx,'日志标题：') );
	//echo '<br />';
	
	if( strpos($ctx,'日志标题：') === 0 ){
		
		
		if($title != null){
			$log = $dom->createElement('log');
			$root->appendChild($log);
			$log->setAttribute('title',$title);
			$log->setAttribute('inTime',$time);
			$log->appendChild( $dom->createCDATASection($cnt) );
		}
		
		
		//$title = str_replace('日志标题：','',$ctx);
		//$title = str_replace('&nbsp;',' ',$title);
		//$title = str_replace('&#13;&#10;','',$title);
		
		$title = preg_replace(array('/日志标题：/','/\r/','/\n/','/\&nbsp;/'),array('','','',' '),$ctx);
		//echo $title.'<br />';
		$cat = preg_replace_callback('/(oracle|java|javascript|js|computer|php|mysql|ubuntu|linux|music)/i','autoCategory',$title);
		$cat == null && ($cat = 'job');
		
		$cnt = '';
	}elseif (strpos($ctx,'发表时间：') === 0){
		/*$time = str_replace('发表时间：','',$ctx);
		$time = str_replace('&#13;&#10;','',$time);*/
		
		$time = preg_replace(array('/发表时间：/','/\r/','/\n/'),'',$ctx);
	}elseif (strpos($ctx,'日志内容：') === 0){
		$cnt = str_replace('日志内容：','',$ctx);
	}else{
		$cnt .= $ctx;
	}
}
fclose($fl);
$dom->save('test.xml');
?>