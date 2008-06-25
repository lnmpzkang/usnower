<?php

class VO {
	/**
	 * 
	 *
	 * @var unknown_type
	 */
	protected $verify = false;
	
	/**
	 * @return boolean
	 */
	public function getVerify() {
		return $this->verify;
	}
	
	/**
	 * 
	 * @param boolean $verify
	 */
	
	public function setVerify($verify) {
		$this->verify = $verify;
	}
	
	public static final function getInstance(){
		
	}

}

?>
