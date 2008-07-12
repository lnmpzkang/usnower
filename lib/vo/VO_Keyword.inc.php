<?php

class VO_Keyword {
	private $id, $keyword;
	
	/**
	 * @return unknown
	 */
	public function getId() {
		return $this->id;
	}
	
	/**
	 * @return unknown
	 */
	public function getKeyword() {
		return $this->keyword;
	}
	
	/**
	 * @param unknown_type $id
	 */
	public function setId($id) {
		$this->id = $id;
	}
	
	/**
	 * @param unknown_type $keyword
	 */
	public function setKeyword($keyword) {
		$keyword = trim($keyword);
		if (GValidate::checkString ($keyword,array("required"=>true,"max"=>30))){
			throw new GDataException("Keyword's length must less than 30");
		}
		$this->keyword = $keyword;
	}

}

?>
