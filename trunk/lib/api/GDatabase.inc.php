<?php

class GDatabase {
	const DB_TYPE_MYSQL = 1;
	const DB_TYPE_SQLSERVER = 2;
	const DB_TYPE_ORACLE = 3;
	
	private $type = self::DB_TYPE_MYSQL;
	private $conn = null;
	
	/**
	 * @return int
	 */
	public function getType() {
		return $this->type;
	}
	
	/**
	 * @param int $type
	 */
	public function setType($type) {
		$this->type = $type;
	}
	
	
	/**
	 * @return link_identifier
	 */
	public function getConn() {
		
		switch($this->type){
			case self::DB_TYPE_MYSQL:
				$this->conn = mysql_pconnect(GConfig::DB_HOST,GConfig::DB_USER,GConfig::DB_PWD);
				mysql_select_db(GConfig::DB_NAME,$this->conn);
				if(null != GConfig::DB_CODE)
					mysql_query("SET NAMES ".GConfig::DB_CODE);
				break;
			case self::DB_TYPE_SQLSERVER:
				$this->conn = mssql_pconnect(GConfig::DB_HOST,GConfig::DB_USER,GConfig::DB_PWD);
				mssql_select_db(GConfig::DB_NAME,$this->conn);
				/**
				 * 未实现设定字符集
				 */
				break;
		}
		
		return $this->conn;
	}
}



?>
