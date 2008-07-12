<?php

class GSQLException extends GAppException {
	
	private $errSql = null;
	private $errCode = null;
	private $errMsg = null;
	
	/**
	 * @return unknown
	 */
	public function getErrMsg() {
		return $this->errMsg;
	}
	
	/**
	 * @param unknown_type $errMsg
	 */
	public function setErrMsg($errMsg) {
		$this->errMsg = $errMsg;
	}
	/**
	 * @return unknown
	 */
	public function getErrCode() {
		return $this->errCode;
	}
	
	/**
	 * @return unknown
	 */
	public function getErrSql() {
		return $this->errSql;
	}
	
	/**
	 * @param unknown_type $errCode
	 */
	public function setErrCode($errCode) {
		$this->errCode = $errCode;
	}
	
	/**
	 * @param unknown_type $errSql
	 */
	public function setErrSql($errSql) {
		$this->errSql = $errSql;
	}
	
}

?>
