<?php
class GSerialize {
	/**
	 * 将对象序列化，存入文件。
	 *
	 * @param mixed $obj
	 * @param string $filePath
	 */
	public static function save($obj,$filePath){
		$dir = dirname($filePath);
		if (!is_dir($dir)) {
			GDir::mkpath($dir);
		}
		$c = serialize($obj);
		$fp = fopen($filePath,"w+");
		if(flock($fp,LOCK_EX)){
			fwrite($fp,$c);
		}else
			GLoger::logToFile("锁定文件失败：$filePath");
		
		flock($fp,LOCK_UN);
		fclose($fp);
	}
	
	/**
	 * 从序列化文件中还原一个对象
	 *
	 * @param string $filePath
	 * @return mixed 如果成功，反回还原的对象，如果失败，反回false
	 */
	public static function load($filePath){
		if (!is_file($filePath)) {
			return false;
		}else {
			$c =file_get_contents($filePath);
			return unserialize($c);
		}
	}
}
?>