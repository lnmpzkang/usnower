<?php
class GFile {	
	protected static function getRes($path,$model){

		$dir = dirname($path);
		if(!is_dir($dir))
			GDir::mkpath($dir);
		
		$fp = fopen($path,$model);
		return $fp;
	}
	
	public function create($path){
		$fp = self::getRes($path,"a");
		fclose($fp);
	}
	
	public function rewrite($path,$content){
		$fp = GFile::getRes($path,"w");
		if(flock($fp,LOCK_EX)){//独占打开
			fwrite($fp,$content);
			flock($fp,LOCK_UN);
			fclose($fp);
		}else{
			fclose($fp);
			throw new Exception("独占打开 $path 打失败");	
		}
	}
	
	public function append($path,$content){
		$fp = GFile::getRes($path,"a");
		fwrite($fp,$content);
		fclose($fp);
	}
	
	/**
	 * 读取文件内容，路径基于 common.inc.php 的目录
	 *
	 * @param unknown_type $path
	 * @return unknown
	 */
	public static function getFileContent($path){
		return GDir::isFile($path) ? file_get_contents(GDir::getAbsPath($path)) : false;
	}
}
?>