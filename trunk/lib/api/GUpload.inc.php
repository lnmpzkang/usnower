<?php
class GUpload {
	private $accept = array("gif","png","jpg","txt","zip","rar","swf");
	private $maxSize = 200;//k
	private $autoname = true;
	
	protected $saveDir = "";
	
	/**
	 * @param bool $autoname
	 */
	public function setAutoname($autoname) {
		$this->autoname = $autoname;
	}
	
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
	 * 设置允许上传的最大大小。单位：K
	 *
	 * @param unknown_type $size
	 */
	public function setMaxSize($size){
		$this->maxSize = $size;
	}
	
	public function setAccept($extArray){
		$this->accept = explode("|||",strtolower(join("|||",$extArray)));
	}
	
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $file 文件表单
	 * @param unknown_type $name 如果autoname = false,且$name 有值的话，就以该值命名。
	 * @return unknown 如果上传成功，就返回文件路径（带名称），否则返回 false;
	 */
	public function uploadFile($file,$name=null){
		if($file["size"] == 0) return false;
		
		$pi = pathinfo($file["name"]);
		
		if(!$this->checkExt($file)){
			throw new GDataException("Not allowed file type:".$pi["extension"].". These types are allowed:".join(",",$this->accept));
		}
		
		if (strtolower(self::getRealExt($file)) != strtolower($pi["extension"])) {
			throw new GDataException("The file :".$file["name"]." may be not a ".$pi["extension"]." file!");
		}
		
		
		if (!$this->checkSize($file)) {
			throw new GDataException("File is too large:".($file["size"] / 1024)."k .Max Size:".$this->maxSize."k");
		}

		if($this->autoname){
			$t = tempnam($this->saveDir,"");
			$tpi = pathinfo($t);
			$fname = date("Ymd_His_").$tpi["filename"];
			unlink($t);
		}else{
			if (!empty($name))
				$fname = $name;
			else
				$fname = $pi["filename"];
		}

		$fname .= ".".$pi["extension"];
		$f = move_uploaded_file($file["tmp_name"], $this->saveDir."/".$fname);
		return $f ? $this->saveDir."/".$fname : false;
	}

	public function uploadAll(){
		$files = array();	
		foreach ($_FILES as $file){
			$path = $this->uploadFile($file);
			if(false != $path)
				array_push($files,$path);
		}
		return $files;
	}

	
	/**
	 * 检查文件是否是可接受的类型
	 *
	 * @param unknown_type $file
	 * @return bool
	 */
	protected function checkExt($file){
		$pi = pathinfo($file["name"]);
		if(!in_array(strtolower($pi["extension"]),$this->accept)){
			return false;
		}else{
			return true;
		}
	}	
	
	
	/**
	* 检查文件真实类型
	*
	* @access      public
	* @param       string      file            		文件表单
	* @param       string      limit_ext_types     允许的文件类型
	* @return      string
	*/
	private function getRealExt($file){
		$pi = pathinfo($file["name"]);
		$extname = strtolower($pi["extension"]);
		
		$str = $format = '';
		$fp = @fopen($file["tmp_name"], 'rb');
		if ($fp){
			$str = @fread($fp, 0x400); // 读取前 1024 个字节
			@fclose($fp);
		}
		/*
		else{
			if (stristr($filename, ROOT_PATH) === false){
				if ($extname == 'jpg' || $extname == 'jpeg' || $extname == 'gif' || $extname == 'png' || $extname == 'doc' ||
				$extname == 'xls' || $extname == 'txt'  || $extname == 'zip' || $extname == 'rar' || $extname == 'ppt' ||
				$extname == 'pdf' || $extname == 'rm'   || $extname == 'mid' || $extname == 'wav' || $extname == 'bmp' ||
				$extname == 'swf' || $extname == 'chm'  || $extname == 'sql' || $extname == 'cert'){
					$format = $extname;
				}
			}else{
				return '';
			}
		}
		*/
		
		if ($format == '' && strlen($str) >= 2 ){
			if (substr($str, 0, 4) == 'MThd' && $extname != 'txt'){
				$format = 'mid';
			}elseif (substr($str, 0, 4) == 'RIFF' && $extname == 'wav')	{
				$format = 'wav';
			}elseif (substr($str ,0, 3) == "\xFF\xD8\xFF"){
				$format = 'jpg';
			}elseif (substr($str ,0, 4) == 'GIF8' && $extname != 'txt'){
				$format = 'gif';
			}elseif (substr($str ,0, 8) == "\x89\x50\x4E\x47\x0D\x0A\x1A\x0A"){
				$format = 'png';
			}elseif (substr($str ,0, 2) == 'BM' && $extname != 'txt'){
				$format = 'bmp';
			}elseif ((substr($str ,0, 3) == 'CWS' || substr($str ,0, 3) == 'FWS') && $extname != 'txt'){
				$format = 'swf';
			}elseif (substr($str ,0, 4) == "\xD0\xCF\x11\xE0"){   // D0CF11E == DOCFILE == Microsoft Office Document
				if (substr($str,0x200,4) == "\xEC\xA5\xC1\x00" || $extname == 'doc'){
					$format = 'doc';
				}elseif (substr($str,0x200,2) == "\x09\x08" || $extname == 'xls'){
					$format = 'xls';
				} elseif (substr($str,0x200,4) == "\xFD\xFF\xFF\xFF" || $extname == 'ppt'){
					$format = 'ppt';
				}
			} elseif (substr($str ,0, 4) == "PK\x03\x04"){
				$format = 'zip';
			} elseif (substr($str ,0, 4) == 'Rar!' && $extname != 'txt'){
				$format = 'rar';
			} elseif (substr($str ,0, 4) == "\x25PDF"){
				$format = 'pdf';
			} elseif (substr($str ,0, 3) == "\x30\x82\x0a")	{
				$format = 'cert';
			} elseif (substr($str ,0, 4) == 'ITSF' && $extname != 'txt'){
				$format = 'chm';
			} elseif (substr($str ,0, 4) == "\x2ERMF"){
				$format = 'rm';
			} elseif ($extname == 'sql'){
				$format = 'sql';
			} elseif ($extname == 'txt'){
				$format = 'txt';
			}
		}
		
		return $format;
	}
	
	/**
	 * 检查文件大小
	 *
	 * @param unknown_type $file 文件表单
	 * @return bool
	 */
	private function checkSize($file){
		if ($file["size"] / 1024 > $this->maxSize) {
			return false;
		}else return true;
	}
}
?>