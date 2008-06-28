<?php

class MO_Admin extends MO {
	/**
	 * 新增一个管理员,返回新增记录的ID
	 *
	 * @param VO_Admin $vo
	 * @return int
	 */
	public static function add($vo){
		
		self::checkVO($vo,"VO_Admin");
		
		$sql = sprintf("INSERT INTO %sADMIN(ADMIN,PWD) VALUES ('%s','%s')",
							GConfig::DB_PREFIX,
							$vo->getAdmin(),
							$vo->getPwd()
							);
		
		GMysql::query($sql);
		return GMysql::getInsertId();
	}
	
	/**
	 * 修改密码，跟据VO中的id,admin来自动选择，ID优先于ADMIN
	 *
	 * @param VO_Admin $vo
	 * @return int
	 */
	public static function password($vo){
		self::checkVO($vo,"VO_Admin");
		
		//$vo = new VO_Admin();
		if($vo->getId() != null){
			$sql = sprintf("UPDATE %sADMIN SET PWD = '%s' WHERE ID = %d",
											GConfig::DB_PREFIX,
											$vo->getPwd(),
											$vo->getId());
		}else if($vo->getAdmin() != null){
			$sql = sprintf("UPDATE %sADMIN SET PWD = '%s' WHERE ADMIN = %s",
											GConfig::DB_PREFIX,
											$vo->getPwd(),
											$vo->getAdmin());
		}else{
			throw new GDataException("Please specify admin's id or admins name.");
		}
			
		GMysql::query($sql);
		return GMysql::getAffectedRows();
	}
	
	/**
	 * 登陆
	 *
	 * @param VO_Admin $vo
	 */
	public static function login($vo){
		self::checkVO($vo,"VO_Admin");
		//$vo = new VO_Admin();
		$sql = sprintf("SELECT %sF_ISADMIN('%s','%s')",
										GConfig::DB_PREFIX,
										$vo->getAdmin(),
										$vo->getPwd());
										
		$rst = GMysql::query($sql);
		//mysql_fetch_row($rst);
		$arr = GMysql::fetchRow($rst);
		if($arr[0] == "1"){
			$sql = sprintf("CALL %sP_ADMIN_LOGIN('%s','%s')",
								GConfig::DB_PREFIX,
								$vo->getAdmin(),
								$_SERVER['REMOTE_ADDR']);
			
			GMysql::query($sql);
								
			GSession::set(GConfig::SSN_KEY_ADMIN_ENCRYPT_NAME,GEncrypt::encrypt($vo->getAdmin(),GConfig::ENCRYPT_KEY));
			GSession::set(GConfig::SSN_KEY_ADMIN_NAME,$vo->getAdmin());
		}else{
			throw new GDataException("Invalid Admin name or password!");
		}
	}
	
	public static function logout(){
		session_unregister(GConfig::SSN_KEY_ADMIN_ENCRYPT_NAME);
		session_unregister(GConfig::SSN_KEY_ADMIN_NAME);
	}
	
	/**
	 * 检查是否以登陆，
	 *
	 * @param string $url 默认为null.如果不为NULL，就跳转到 $url上。否则向上抛出异常。
	 */
	public static function checkLoginStatus($url=null){
		//if($_SESSION[GConfig::SSN_KEY_ADMIN_NAME] != GEncrypt::decrypt($_SESSION[GConfig::SSN_KEY_ADMIN_ENCRYPT_NAME],GConfig::ENCRYPT_KEY)){
		if(self::isLogined()){
			if(null == $url){
				throw new GDataException("Please login!");
			}else{
				header("location:$url");
			}
		}
	}
	
	/**
	 * 检查是否以登陆
	 *
	 * @return boolean
	 */
	public static function isLogined(){
		return ($_SESSION[GConfig::SSN_KEY_ADMIN_NAME] === GEncrypt::decrypt($_SESSION[GConfig::SSN_KEY_ADMIN_ENCRYPT_NAME],GConfig::ENCRYPT_KEY));
	}
}

?>