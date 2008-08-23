<?php

class VO_ArtCategory {
	private $id,$name,$inTime;
	private $fatherId,$fatherName;
	private $subNum,$artNum;
	
	/**
	 * @return unknown
	 */
	public function getArtNum() {
		return $this->artNum;
	}
	
	/**
	 * @return unknown
	 */
	public function getFatherId() {
		return $this->fatherId;
	}
	
	/**
	 * @return unknown
	 */
	public function getFatherName() {
		return $this->fatherName;
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
	public function getSubNum() {
		return $this->subNum;
	}
	
	/**
	 * @param unknown_type $artNum
	 */
	public function setArtNum($artNum) {
		$this->artNum = $artNum;
	}
	
	/**
	 * @param unknown_type $fatherId
	 */
	public function setFatherId($fatherId) {
		$this->fatherId = $fatherId;
	}
	
	/**
	 * @param unknown_type $fatherName
	 */
	public function setFatherName($fatherName) {
		$this->fatherName = $fatherName;
	}
	
	/**
	 * @param unknown_type $id
	 */
	public function setId($id) {
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
		$rule = array(
			"required"	=>	true,
			"type"	=>	GValidate::TYPE_STRING,
			"min"		=>	1,
			"max"		=>	30
		);
		if(!GValidate::checkString(trim($name),$rule)){
			throw new GDataException("Article Category Name required.And it's length must between 1 and 30 (one Chinese character ".MUTI_CHAR_LEN." length).");
		}
		
		$this->name = trim($name);
	}
	
	/**
	 * @param unknown_type $subNum
	 */
	public function setSubNum($subNum) {
		$this->subNum = $subNum;
	}
	
}

?>
