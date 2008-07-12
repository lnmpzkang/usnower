<?php

class VO_ArtAlbum {
	private $id,$name,$description,$artNum;
	
	/**
	 * @return unknown
	 */
	public function getArtNum() {
		return $this->artNum;
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
	public function getId() {
		return $this->id;
	}
	
	/**
	 * @return unknown
	 */
	public function getName() {
		return $this->name;
	}
	
	/**
	 * @param unknown_type $artNum
	 */
	public function setArtNum($artNum) {
		$this->artNum = $artNum;
	}
	
	/**
	 * @param unknown_type $description
	 */
	public function setDescription($description) {
		if(!GValidate::checkString($description,array("required"=>false,"max"=>1000))){
			throw new GDataException("Article Album Description is Optional.And it's length must less than 1000.(One Chinese Character ".MUTI_CHAR_LEN." length.) ");
		}
		$this->description = $description;
	}
	
	/**
	 * @param unknown_type $id
	 */
	public function setId($id) {
		$this->id = $id;
	}
	
	/**
	 * @param unknown_type $name
	 */
	public function setName($name) {
		if(!GValidate::checkString(trim($name),array("required"=>true,"min"=>1,"max"=>30))){
			throw new GDataException("Article Album Name required.And it's length must between 1 and 30.(One Chinese Character ".MUTI_CHAR_LEN." length)");
		}
		$this->name = trim($name);
	}
	
}

?>
