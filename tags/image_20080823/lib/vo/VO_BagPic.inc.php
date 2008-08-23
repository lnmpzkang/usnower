<?php

class VO_BagPic {
	private $id,$bag,$color,$inTime,$description;
	private $big,$normal,$icon;
	
	/**
	 * @return unknown
	 */
	public function getBig() {
		return $this->big;
	}
	
	/**
	 * @return unknown
	 */
	public function getIcon() {
		return $this->icon;
	}
	
	/**
	 * @return unknown
	 */
	public function getNormal() {
		return $this->normal;
	}
	
	/**
	 * @param unknown_type $big
	 */
	public function setBig($big) {
		$this->big = $big;
	}
	
	/**
	 * @param unknown_type $icon
	 */
	public function setIcon($icon) {
		$this->icon = $icon;
	}
	
	/**
	 * @param unknown_type $normal
	 */
	public function setNormal($normal) {
		$this->normal = $normal;
	}
	/**
	 * @return unknown
	 */
	public function getDescription() {
		return $this->description;
	}
	
	/**
	 * @param unknown_type $description
	 */
	public function setDescription($description) {
		$this->description = $description;
	}
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
