<?php

class VO_Bag {
	private $id,$name,$no,$unit,$fabric;
	private $sizeL,$sizeW,$sizeH;
	private $description,$cat,$inTime;	
	
	/**
	 * @return unknown
	 */
	public function getCat() {
		return $this->cat;
	}
	
	/**
	 * @return unknown
	 */
	public function getDescription() {
		return $this->description;
	}
	
	/**
	 * @return unknown
	 */
	public function getFabric() {
		return $this->fabric;
	}
	
	/**
	 * @return unknown
	 */
	public function getId() {
		return $this->id;
	}
	
	/**
	 * @return unknown
	 */
	public function getInTime() {
		return $this->inTime;
	}
	
	/**
	 * @return unknown
	 */
	public function getName() {
		return $this->name;
	}
	
	/**
	 * @return unknown
	 */
	public function getNo() {
		return $this->no;
	}
	
	
	/**
	 * @return unknown
	 */
	public function getUnit() {
		return $this->unit;
	}
	
	/**
	 * @param unknown_type $cat
	 */
	public function setCat($cat) {
		$this->cat = $cat;
	}
	
	/**
	 * @param unknown_type $description
	 */
	public function setDescription($description) {
		$this->description = $description;
	}
	
	/**
	 * @param unknown_type $fabric
	 */
	public function setFabric($fabric) {
		$this->fabric = $fabric;
	}
	
	/**
	 * @param unknown_type $id
	 */
	public function setId($id) {
		if(!GValidate::checkNumber($id,array("type"=>GValidate::TYPE_INT,'min'=>0))){
			throw new GDataException("Invalid param id");
		}
		$this->id = $id;
	}
	
	/**
	 * @param unknown_type $inTime
	 */
	public function setInTime($inTime) {
		$this->inTime = $inTime;
	}
	
	/**
	 * @param unknown_type $name
	 */
	public function setName($name) {
		$name = trim($name);
		if(!GValidate::checkString($name,array("required"=>true,"max"=>30))){
			throw new GDataException("Bag's Name required.It's length must less than 30.One Chinese character ".MUTI_CHAR_LEN." length.");
		}
		$this->name = $name;
	}
	
	/**
	 * @param unknown_type $no
	 */
	public function setNo($no) {
		$no = trim($no);
		if(!GValidate::checkString($no,array("required"=>true,"max"=>30))){
			throw new GDataException("Bag's Name required.It's length must less than 30.One Chinese character ".MUTI_CHAR_LEN." length.");
		}		
		$this->no = $no;
	}
	
	
	/**
	 * @param unknown_type $unite
	 */
	public function setUnit($unit) {
		$this->unit = $unit;
	}
	/**
	 * @return unknown
	 */
	public function getSizeH() {
		return $this->sizeH;
	}
	
	/**
	 * @return unknown
	 */
	public function getSizeL() {
		return $this->sizeL;
	}
	
	/**
	 * @return unknown
	 */
	public function getSizeW() {
		return $this->sizeW;
	}
	
	/**
	 * @param unknown_type $sizeH
	 */
	public function setSizeH($sizeH) {
		if(!GValidate::checkNumber($sizeH,array("required"=>false,"min"=>0)))
			throw new GDataException("Size H(height) must be a number,and greater than zero!");
		$this->sizeH = $sizeH;
	}
	
	/**
	 * @param unknown_type $sizeL
	 */
	public function setSizeL($sizeL) {
		$this->sizeL = $sizeL;
	}
	
	/**
	 * @param unknown_type $sizeW
	 */
	public function setSizeW($sizeW) {
		$this->sizeW = $sizeW;
	}

}

?>
