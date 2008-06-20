<?php

/**
 * 数据库操作类
 *
 */

class GDb {
	const TYPE_MYSQL = 0;
	const TYPE_MSSQL = 1;
	const TYPE_ORACLE = 3;
	const TYPE_SQLLET = 4;
	
	
	public static $conn;
	
	public static function getConn($dbType,$dbHost,$dbUser,$dbPwd,$dbName,$dbCharset){
		
		switch($dbType){
			case TYPE_MYSQL:
				$conn = @mysql_pconnect($dbHost,$dbUser,$dbPwd);
				if($conn){
					@mysql_select_db($dbName,$conn);
					if(null != $dbCharset && "" != $dbCharset)
						mysql_query("SET NAMES .$dbCharset");
				}
				break;
			case TYPE_MSSQL:
				break;
			case TYPE_ORACLE:
				break;
		}
		
		return conn;
	}
	
	public static function getDefaultConn(){
		return self::getConn(GConfig::DB_TYPE,GConfig::DB_HOST,GConfig::DB_USER,GConfig::DB_PWD,GConfig::DB_CHARSET);
	}
}

?>