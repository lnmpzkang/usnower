<?php
class GMiniature extends GImage  {
	
	private $dIm = null;
	private $sIm = null;
	
	public function __construct(){
		if (!self::isSupport()) {
			throw new Exception("空间不支持使用GD库");
		}
	}
	
	/**
	 * 按比例缩放，比例如果1%,scale就是1
	 *
	 * @param string $path
	 * @param float $scale
	 */
	public function zoomByScale($path,$scale){
		if ($scale <= 0) {
			throw new GDataException("Scale must more than zero!");
		}
		
		$size = $this->getImgSize($path);
		$size2 = new ImgSize();
		$size2->width = $size->width * $scale / 100;
		$size2->height = $size->height * $scale / 100;
		
		$this->sIm = $this->createImFromFile($path);
		$this->dIm = imagecreatetruecolor($size2->width,$size2->height);
	//bool imagecopyresampled ( resource dst_image, resource src_image, int dst_x, int dst_y, int src_x, int src_y, int dst_w, int dst_h, int src_w, int src_h )

		imagecopyresampled($this->dIm,$this->sIm,0,0,0,0,$size2->width,$size2->height,$size->width,$size->height);
	}
	
	public function zoomBySize($path,$width,$height){
		if ($width < 1 || $height < 1){
			throw new GDataException("Width & height must more than one!");
		}
		
		$size = $this->getImgSize($path);
		$size2 = new ImgSize();
		
		$scaleW = $width / $size->width;
		$scaleH = $height / $size->height;
		
		if($scaleW < $scaleH){
			$size2->width = $width;
			$size2->height = $size->height * $scaleW;
		}else{
			$size2->height = $height;
			$size2->width = $size->width * $scaleH;
		}
		
		$this->sIm = $this->createImFromFile($path);
		$this->dIm = imagecreatetruecolor($size2->width,$size2->height);
		imagecopyresampled($this->dIm,$this->sIm,0,0,0,0,$size2->width,$size2->height,$size->width,$size->height);
	}
	
	public function zoomByFixSize($path,$width,$height){
		if ($width < 1 || $height < 1){
			throw new GDataException("Width & height must more than 1");
		}
		
		$size = $this->getImgSize($path);

		$scaleW = $width / $size->width;
		$scaleH = $height / $size->height;
		
		if($scaleW < $scaleH){
			$scale = $scaleH ;
		}else{
			$scale = $scaleW;
		}
		
		$scale = ($scaleW < $scaleH ? $scaleH : $scaleW) * 100;
		$this->zoomByScale($path,$scale);
		
		$tIm = imagecreatetruecolor($width,$height);
		imagecopymerge($tIm,$this->dIm,0,0,0,0,$width,$height,100);
		
		imagedestroy($this->dIm);
		$this->dIm = $tIm;
	}	
	
	private function saveAs($name,$ext){		
		$filePath = $this->saveDir."/".$name;
		$filePath = preg_replace("/(.+)(\\.+)(jpg|png|gif)+$/i","$1",$filePath);
		
		//$this->checkSaveDir();
		
		switch($ext){
			case "GIF";
				imagegif($this->dIm,$filePath.".gif");
				break;
			case "JPG":
				imagejpeg($this->dIm,$filePath.".jpg");
				break;
			case "PNG":
				imagepng($this->dIm,$filePath.".png");
				break;
			default:
				throw new GDataException("Not support this file type:$ext");
		}
	}
	
	public function saveAsGif($name){
		$this->saveAs($name,"GIF");
	}
	
	public function saveAsJpg($name){
		$this->saveAs($name,"JPG");
	}
	
	public function saveAsPng($name){
		$this->saveAs($name,"PNG");
	}
	
	public function save($name){
		$pi = pathinfo($name);
		$ext = strtoupper($pi["extension"]);
		$this->saveAs($name,$ext);
	}
	
	public function __destruct(){
		imagedestroy($this->sIm);
		imagedestroy($this->dIm);
	}
}
?>