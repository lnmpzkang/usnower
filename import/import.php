<?php

set_time_limit(0);//设置超时，0为不超时


$resSaveDir = 'data/';//必须存在,以  /  结尾
$acceptDomainReg = '/(blueidea.com|dajiaozi.com){1}/i';//如果用的图片包括这些域名，就下载，否则不下载

/**
 * 获取指定域名下的图片资源，并对图片地址进行转换
 *
 * @param unknown_type $ctx
 */
function getRes(&$ctx){
	global $resSaveDir,$acceptDomainReg;
	
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
				$fl = @fopen($resSaveDir.$name,'w+');
				fwrite($fl,$resCtx);
				array_push($urls_,$url);
				array_push($local,$resSaveDir.$name);
				fclose($fl);
			}else{
				//
			}
		}
	}
	
	$ctx = str_ireplace($urls_,$local,$ctx);
}


$fl = fopen('backup.htm','r');

//自动分类，比如您的 oracle 分类ID为 2,java分类ID为 3
//因为导出的htm文件里没有日志分类，所以，这里用关键字是否出现做为分类判断，并不准确，如果想提高准确，请多写些关键字
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

$catMap = array_change_key_case($catMap,CASE_LOWER);

function autoCategory($ma){
	global $cat;
	$cat = $ma[1];
	return $ma[0];
} 

$cnt = '';


$dom = new DOMDocument('1.0','utf-8');
$root = $dom->createElement('root');
$dom->appendChild($root);

$title = null;
$time= null;
$cat = null;

while( !feof($fl) ){		
	$ctx = iconv('gb18030','utf8', fgets($fl));
	
	if( strpos( trim( $ctx ),'<td>日志标题：') === 0 ){
		
		
		if($title != null){
			echo "正在解析日志： $title 请稍等。。。不要关闭浏览器！<br />";
			$log = $dom->createElement('log');
			$root->appendChild($log);
			$log->setAttribute('title',$title);
			$log->setAttribute('inTime',$time);
			$log->setAttribute('cat',$catMap[ strtolower( $cat ) ]);
			
			echo "正在保存 $title 用到的图片资源。。。。<br />";
			getRes($cnt);
			
			$log->appendChild( $dom->createCDATASection($cnt) );
			echo "日志 $title 保存完毕。<br /><br /><br />";
		}
		
		$title = preg_replace(array('/<td>日志标题：/','/\r/','/\n/','/\&nbsp;/','/<br>/'),array('','','',' ',''),trim($ctx));

		preg_replace_callback('/(oracle|java|javascript|js|computer|php|mysql|ubuntu|linux|music)/i','autoCategory',$title);
		$cat == null && ($cat = 'job');
		
		$cnt = '';
	}elseif (strpos(trim($ctx),'发表时间：') === 0){
		
		$time = preg_replace(array('/发表时间：/','/\r/','/\n/','/<br>/'),'',$ctx);
	}else{
		$cnt .= $ctx;
	}
}
fclose($fl);
$dom->save('test.xml');

echo '所有日志保存完毕！';
?>