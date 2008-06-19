<?php
class GDir{
	/**
	* make dirs.
	* 
	* @param string $path 要建立的文件夹全路径。
	* @param int $r 权限
	* @return boolean
	*/

	public static function mkpath($path,$r = 0777){
		$dirs=array();
		$dirs=explode("/",$path);
		$path="";
		foreach ($dirs as $element){
			$path.=$element."/";
			if(!is_dir($path)){
				if(!mkdir($path,$r)){ 
					return false; 
				}
			}//不用chmod了,用了也白用,程序执行用的和FTP用户不一样,如果FTP不给上级目录777的话,执行用户也是没有办法改到777的.
		}
		return true;
	}
	
	/**
	 * 新建路径，基于文档目录
	 *
	 * @param string $path
	 * @param int $r 权限
	 * @return boolean
	 */
	public static function mkpathBaseDocRoot($path,$r = 0777){
		return self::mkpath(PATH_DOC_ROOT.DIRECTORY_SEPARATOR.$path,$r);
	}
	
	/**
	 * 删除路径
	 *
	 * @param string $path
	 * @param boolean $removeSelf
	 * @return boolean
	 */
	public static function rmpath($path,$removeSelf = true){
		if(!is_dir($path) && !is_file($path))
			return false;
		elseif(is_file($path)){
			unlink($path);
			return true;
		}elseif(is_dir($path)){
			$list = scandir($path);
			foreach ($list as $item){
				if($item == "." || $item == "..") continue;
				$item = $path."/".$item;
				if(is_file($item)){
					unlink($item);
				}elseif(is_dir($item)){
					self::rmpath($item,true);
				}
			}
		}
		$parent = dirname($path);
		if($removeSelf)
			rmdir($parent."/".basename($path));
		
		return true;
	}
	
	/**
	 * 删除路径，基于 document root
	 *
	 * @param string $path
	 * @param boolean $removeSelf
	 * @return boolean
	 */
	public static function rmpathBaseDocRoot($path,$removeSelf = true){
		return self::rmpath($path,$removeSelf);
	}
	
	/**
	 * 统绝对路径
	 *
	 * @param string $path
	 * @param string arg1 可选。arg2,arg3,...
	 * @return string
	 */
	public static function getAbsPath($path){
		$path = preg_replace(PATH_REGULATION,'/',$path);
		
		if ( strpos($path,SITE_ABS_DIR) !== 0) {
			$path = SITE_ABS_DIR."/".$path;
		}
		
		$args = func_get_args();
		for ($i=1;$i<func_num_args();$i++){
			$path .= "/".$args[$i];
		}
		
		return preg_replace(PATH_REGULATION,'/',$path);
	}
	
	/**
	 * 得到相对路径，相对于网站所在的文件夹（网站根目录，和这个文件夹可以不是同一级目录）
	 *如目录是：www/bbs/admin/,common.inc.php位于bbs目录下,想得到admin的相对路径，请用：getRelativePath('/admin')。
	 * 
	 * @param string $path 
	 * @return string
	 */
	public static function getRelativePath($path){

		$path = self::getAbsPath($path);
		
		$args = func_get_args();
			for ($i=1;$i<func_num_args();$i++){
				$path .= "/".$args[$i];
		}
		
		return preg_replace(PATH_REGULATION,'/', str_replace(SITE_ABS_DIR,"/",$path));
	}
	
	public static function getWebRelativePath($path){
		return preg_replace(PATH_REGULATION,"/", str_replace(DOC_ROOT,"/",$path));
	}
	
	/**
	 * Enter description here...
	 *
	 * @param string $dir
	 * @param string $exts 格式如: jpg|png|gif
	 * @param boolean $nodir 如果为真，不包括文件夹在内
	 * @return array;
	 */
	public static function getFileList($dir,$exts = null,$nodir = true){
		$r = array();
		if(is_dir(GDir::getAbsPath($dir))){
			$files = scandir(GDir::getAbsPath($dir));
			foreach ($files as $file){
				if($file == "." || $file == "..") continue;
				if($nodir && !is_file(GDir::getAbsPath($dir,$file))) continue;
				//if(is_string($exts) && !preg_match("/(.+)(\\.+)(jpg|png|gif)+$/i",$file)) continue;
				if(is_string($exts) && !preg_match("/(.+)(\\.+)($exts)+$/i",$file)) continue;
				array_push($r,self::getRelativePath($dir,$file));
			}
		}
		
		return $r;
	}
}
?>