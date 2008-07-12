<?php

class VO_ArtKeyword {
	private $id,$art,$keyword;
	
	/**
	 * @return unknown
	 */
	public function getArt() {
		return $this->art;
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
	public function getKeyword() {
		return $this->keyword;
	}
	
	/**
	 * @param unknown_type $art
	 */
	public function setArt($art) {
		$this->art = $art;
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
		$this->keyword = $keyword;
	}
	
}

?>
