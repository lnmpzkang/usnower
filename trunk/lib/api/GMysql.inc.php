<?php

class GMysql {
	public static $conn = null;
	
	/**
	 * 取得数据库持久连接
	 *
	 * @param string $dbHost
	 * @param string $dbUser
	 * @param string $dbPwd
	 * @param string $dbName
	 * @param string $dbCode
	 * @return link_identifier
	 */
	public static function getConn($dbHost,$dbUser,$dbPwd,$dbName,$dbCharset = null){
		$conn = @mysql_pconnect($dbHost,$dbUser,$dbPwd);
		if(!$conn){
			$msg = sprintf("Time:%s\t Msg:数据库连接出错！",date("Ymd H:i:s"));
			GLoger::logToFile($msg);
			GLoger::reportToAdmin($msg,GConfig::ADMIN_EMAIL);
			throw new Exception($msg);
		}
		
		mysql_select_db($dbName,$conn);
		if(null != $dbCharset)
			mysql_query("SET NAMES ".$dbCharset);
		
		return $conn;
	}
	
	/**
	 * 取得默认数据库连接，参数取至：GConfig
	 *
	 * @return link_identifier
	 */
	public static function getDefaultConn(){
		return self::getConn(GConfig::DB_HOST,GConfig::DB_USER,GConfig::DB_PWD,GConfig::DB_NAME,GConfig::DB_CHARSET);
	}
	
	/**
	 * 执行SQL语句，如果出错，抛出SQLException
	 *
	 * @exception SQLException
	 * @param string $sql
	 * @return resource
	 */
	public static function query($sql){
		$result = mysql_query($sql);
		if(mysql_errno() != 0){
			throw new GAppException(mysql_error(self::$conn).SYMBOL_NEWLINE.$sql);
		}else 
			return $result;
	}

	public static function getInsertId($conn = null){
		$conn == null ? $conn = self::$conn : $conn;
		return mysql_insert_id($conn);
	}
	
	public static function getAffectedRows($conn = null){
		$conn == null ? $conn = self::$conn : $conn;
		return mysql_affected_rows($conn);
	}	
	
	/**
	 * 将rst 的内容取出
	 *
	 * @param resource $rst
	 * @param string $case
	 * @return mixed if success then array else false.
	 */
	public static function fetchArray($rst,$case = CASE_LOWER){
		if(false != ($arr = mysql_fetch_array($rst))){
			$arr = array_change_key_case($arr,$case);
			return $arr;
		}else 
			return false;
	}

	public static function fetchRow($rst){
		if(false != ($arr = mysql_fetch_row($rst))){
			return $arr;
		}else{
			return false;
		}
	}
}


//只有调用这个类的时候，取Conn,不放到 common.inc.php的原因是，有些页面不用连接数据库
GMysql::$conn = GMysql::getDefaultConn();
?>
