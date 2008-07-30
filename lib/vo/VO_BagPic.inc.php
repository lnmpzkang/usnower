<?php

class VO_BagPic {
	private $id,$bag,$color,$file,$inTime;
	
	/**
	 * @return unknown
	 */
	public function getBag() {
		return $this->bag;
	}
	
	/**
	 * @return unknown
	 */
	public function getColor() {
		return $this->color;
	}
	
	/**
	 * @return unknown
	 */
	public function getFile() {
		return $this->file;
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
	 * @param unknown_type $bag
	 */
	public function setBag($bag) {
		$this->bag = $bag;
	}
	
	/**
	 * @param unknown_type $color
	 */
	public function setColor($color) {
		$this->color = $color;
	}
	
	/**
	 * @param unknown_type $file
	 */
	public function setFile($file) {
		$this->file = $file;
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
	
}

?>
