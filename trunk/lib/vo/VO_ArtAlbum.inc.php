<?php

class VO_ArtAlbum {
	private $id,$art,$album;
	
	/**
	 * @return unknown
	 */
	public function getAlbum() {
		return $this->album;
	}
	
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
	 * @param unknown_type $album
	 */
	public function setAlbum($album) {
		$this->album = $album;
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
	
}

?>
