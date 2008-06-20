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
}

//只有调用这个类的时候，取Conn,不放到 common.inc.php的原因是，有些页面不用连接数据库
GMysql::$conn = GMysql::getDefaultConn();
?>
