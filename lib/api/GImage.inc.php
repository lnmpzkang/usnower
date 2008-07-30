<?php
class GImage {	
	protected $saveDir = "";
	
	public function setSaveDir($path){
		if (!is_dir($path)) {
			GDir::mkpath($path);
		}
		
		$this->saveDir = $path;
	}
	
/*	protected function checkSaveDir(){
		if (!is_dir($this->saveDir)) {
			GDir::mkpath($path);
		}
	}*/
	
	/**
	 * 是否支持GD库
	 *
	 * @return bool
	 */
	public static function isSupport(){
		return function_exists("gd_info");
	}
	
	
	protected function createImFromFile($path){
		$info = getimagesize($path);
		
		switch ($info[2]){
			case 1:
				//gif
				$im = imagecreatefromgif($path);
				break;
			case 2:
				//jpg
				$im = imagecreatefromjpeg($path);
				break;
			case 3:
				//png
				$im = imagecreatefrompng($path);
				break;
			default:
				throw new GDataException("Not support file type.File:$path");
		}
		
		return $im;
	}
	
	protected function getImgSize($path){
		if(!is_file($path))
			throw new GDataException("File not found! File:$path");
		$size = new ImgSize();
		
		list($size->width,$size->height) = getimagesize($path);
		return $size;
	}
}

class ImgSize{
	public $width = 0;
	public $height = 0;
}
?>