<?php

class VO_Admin extends VO {
	
	private $id,$admin,$pwd;
	private $inTime,$lastTime,$lastIp;
	
	/**
	 * @return unknown
	 */
	public function getAdmin() {
		return $this->admin;
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
	public function getLastIp() {
		return $this->lastIp;
	}
	
	/**
	 * @return unknown
	 */
	public function getLastTime() {
		return $this->lastTime;
	}
	
	/**
	 * @return unknown
	 */
	public function getPwd() {
		return $this->pwd;
	}
	
	/**
	 * @param unknown_type $admin
	 */
	public function setAdmin($admin) {
		$this->admin = $admin;
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
	 * @param unknown_type $lastIp
	 */
	public function setLastIp($lastIp) {
		$this->lastIp = $lastIp;
	}
	
	/**
	 * @param unknown_type $lastTime
	 */
	public function setLastTime($lastTime) {
		$this->lastTime = $lastTime;
	}
	
	/**
	 * @param unknown_type $pwd
	 */
	public function setPwd($pwd) {
		$this->pwd = md5($pwd);
	}
	
}

?>
