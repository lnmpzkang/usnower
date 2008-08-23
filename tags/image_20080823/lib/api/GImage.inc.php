<?php
class GImage {	
	
	const POS_MIDDLE = 0;
	const POS_TOP_LEFT = 1;
	const POS_TOP_CENTER = 2;
	const POS_TOP_RIGHT = 3;
	const POS_CENTER_LEFT = 4;
	const POS_CENTER_RIGHT = 5;
	const POS_BOTTOM_LEFT = 6;
	const POS_BOTTOM_CENTER = 7;
	const POS_BOTTOM_RIGHT = 8;
	const POS_RANDOM = 10;	
	
	protected $saveDir = "";//保存的目录
	
	private $im = null;//
	private $logoIm = null;//图标
	
	private $sImg;//源图
	private $logoImg;
	
	private $logoAlpha = 100;
	//private $currW = 0,$currH = 0;//$im 的大小
	private $currSize = null;
	
	/**
	 * Enter description here...
	 *
	 * @param string $pic 要操作的图片的地址。
	 */
	public function __construct($pic = null){
		if(null != $pic && "" != trim($pic)){
			self::setSourceImg($pic);
		}
	}
	
	public function __destruct(){
		if(null != $this->im)
			imagedestroy($this->im);
		if(null != $this->logoIm)
			imagedestroy($this->logoIm);
	}
	
	/**
	 * 设置要处理的图片
	 *
	 * @param string $pic 图片地址
	 */
	public function setSourceImg($pic){
		if(is_file($pic)){
			$this->sImg = $pic;
			$this->currSize = null;
			$this->im = self::createImFromFile($pic);
		}else 
			throw new Exception("File : $pic Not exist!");
	}
	
	/**
	 * 重置，取消所有缩放，水印操作。
	 *
	 */
	public function reset(){
		if($this->im != null) imagedestroy($this->im);
		$this->currSize = null;
		
		self::setSourceImg($this->sImg);
	}
	
	/**
	 * 设置保存目录
	 *
	 * @param string $path
	 */
	public function setSaveDir($path){
		if (!is_dir($path)) {
			GDir::mkpath($path);
		}
		
		$this->saveDir = $path;
	}
	
	protected function checkSaveDir(){
		if (!is_dir($this->saveDir)) {
			GDir::mkpath($this->saveDir);
		}
	}
	
	/**
	 * 是否支持GD库
	 *
	 * @return bool
	 */
	public static function isSupport(){
		return function_exists("gd_info");
	}
	
	/**
	 * 从文件创建 image resource
	 *
	 * @param string $path
	 * @return resource
	 */
	protected function createImFromFile($path){
		if(!is_file($path))
			throw new Exception("File: $path not found!");
			
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
				throw new Exception("Not support file type.File:$path");
		}
		
		return $im;
	}
	
	/**
	 * Enter description here...
	 *
	 * @param string $path
	 * @return ImgSize
	 */
	protected function getImgSize($path){
		if(!is_file($path))
			throw new Exception("File not found! File:$path");
		$size = new ImgSize();
		
		list($size->width,$size->height) = getimagesize($path);
		return $size;
	}
	
	/**
	 * 取得im的大小，如果还没有设定，就取原图的大小
	 *
	 * @return ImgSize
	 */
	private function getSize(){
		if($this->currSize == null)
			return self::getImgSize($this->sImg);
		else
			return $this->currSize;
	}
	
	/**
	 * 按比例缩放 $scale = 1.1 就是缩放1.1倍
	 *@param float $scale (0<$scale)
	 */
	public function zoomByScale($scale){
		$scale = floatval($scale);
		if($scale <= 0)
			throw new Exception("Scale must more than zero!");
		
		$orgSize = self::getSize();		
		$this->currSize = new ImgSize($orgSize->width * $scale,$orgSize->height * $scale);
		
		$tIm = imagecreatetruecolor($this->currSize->width,$this->currSize->height);
		
		imagecopyresampled($tIm,$this->im,0,0,0,0,$this->currSize->width,$this->currSize->height,$orgSize->width,$orgSize->height);
		imagedestroy($this->im);
		$this->im = &$tIm;
		//imagedestroy($tIm);
	}
	
	/**
	 * 按固定大小缩放，不截取，保证高或宽等于指定大小。
	 *
	 */
	public function zoomByMaxSize($width,$height){
		$width = intval($width);
		$height = intval($height);
		if($width < 1 || $height < 1)
			throw new Exception("Width & Height must greater than 1");
		
		$orgSize = self::getSize();
		$scaleW = $width / $orgSize->width;
		$scaleH = $height / $orgSize->height;
		
		if($scaleW < $scaleH){
			$this->currSize = new ImgSize($width,$orgSize->height * $scaleW);
		}else{
			$this->currSize = new ImgSize($orgSize->width * $scaleH,$height );
		};
		
		$tIm = imagecreatetruecolor($this->currSize->width,$this->currSize->height);
		
		imagecopyresampled($tIm,$this->im,0,0,0,0,$this->currSize->width,$this->currSize->height,$orgSize->width,$orgSize->height);
		imagedestroy($this->im);
		$this->im = &$tIm;
		//imagedestroy($tIm);
	}
	
	/**
	 * 按指定尺寸进行缩放，可能有截取。
	 *
	 */
	public function zoomByFixedSize($width,$height){
		$width = intval($width);
		$height = intval($height);
		
		$orgSize = self::getSize();
		$scaleW = $width / $orgSize->width;
		$scaleH = $height / $orgSize->height;
		
		$scale = $scaleW < $scaleH ? $scaleH : $scaleW;
		
		self::zoomByScale($scale);
		
		$tIm = imagecreatetruecolor($width,$height);
		imagecopymerge($tIm,$this->im,0,0,0,0,$width,$height,100);
		imagedestroy($this->im);
		$this->im = &$tIm;
	}
	
	public function setLogoSourceImg($logo,$alpha = 100){
		if(!is_file($logo))
			throw new Exception("Logo File :$logo not exists!");
			
		$this->logoImg = $logo;
		$this->logoAlpha = $alpha;
		$this->logoIm = self::createImFromFile($logo);		
	}
	
	/**
	 * 加水印图片
	 * 如果没有设置 $param2的话，即只有一个参数，logo 的位置是根据calcLogoPos($param1)取得的。否则$param1和$param2指定坐标。
	 * 此函数必须放在 setSourceImg,setLogoSourceImg后面。
	 * 
	 * @param mixed $param1 可取 GImage::POS_** 或 整数。
	 * @param int $param2 
	 */
	public function markLogo($param1,$param2 = null){
		if($this->logoIm == null)
			throw new Exception("Please set logo source!");
		if($this->im == null)
			throw new Exception("Please set which image to be mark logo!");
			
		$logoSize = self::getImgSize($this->logoImg);
		
		if (!empty($param2) && is_numeric($param2)) {
			$logPos = array("x"=>$param1,"y"=>$param2);
		}else
			$logPos = $this->calcLogoPos($param1);
		
		imagecopymerge($this->im,$this->logoIm,$logPos["x"],$logPos["y"],0,0,$logoSize->width,$logoSize->height,$this->logoAlpha);
	}
	
	/**
	 * 计算水印图片的位置。
	 *
	 */
	private function calcLogoPos($pos){
		if($this->logoIm == null)
			throw new Exception("Please set logo source!");
			
		$bgW = $this->currSize->width;
		$bgH = $this->currSize->height;
		
		$logWH = self::getImgSize($this->logoImg);
		$logW = $logWH->width;
		$logH = $logWH->height;
		
		switch ($pos){
			case self::POS_MIDDLE :
				$x = ($bgW - $logW) / 2;
				$y = ($bgH - $logH) / 2;
				break;
			case self::POS_TOP_LEFT :
				$x = 0;
				$y = 0;
				break;
			case self::POS_TOP_CENTER :
				$x = ($bgW - $logW) / 2;
				$y = 0;
				break;
			case self::POS_TOP_RIGHT :
				$x = $bgW - $logW;
				$y = 0;
				break;
			case self::POS_CENTER_LEFT :
				$x = 0;
				$y = ($bgH - $logH) / 2;
				break;
			case self::POS_CENTER_RIGHT :
				$x = $bgW - $logW;
				$y = ($bgH - $logH) / 2;
				break;
			case self::POS_BOTTOM_LEFT :
				$x = 0;
				$y = $bgH - $logH;
				break;
			case self::POS_BOTTOM_CENTER :
				$x = ($bgW - $logW) / 2;
				$y = $bgH - $logH;
				break;
			case self::POS_BOTTOM_RIGHT :
				$x = $bgW - $logW;
				$y = $bgH - $logH;
				break;
			case self::POS_RANDOM :
				$x = rand(0,$bgW - $logW);
				$y = rand(0,$bgH - $logH);
				break;
			default:
				$x = 0;
				$y = 0;
				break;
		}
		return array("x"=>$x,"y"=>$y);		
	}
	
	
	private function saveAs($name,$ext){		
		$filePath = $this->saveDir."/".$name;
		$filePath = preg_replace("/(.+)(\\.+)(jpg|png|gif)+$/i","$1",$filePath);
		
		//$this->checkSaveDir();
		
		switch($ext){
			case "GIF";
				imagegif($this->im,$filePath.".gif");
				break;
			case "JPG":
				imagejpeg($this->im,$filePath.".jpg");
				break;
			case "PNG":
				imagepng($this->im,$filePath.".png");
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
}

class ImgSize{
	public $width = 0;
	public $height = 0;
	
	public function __construct($width = null,$height = null){
		$this->width = intval($width);
		$this->height = intval($height);
	}
}
?>