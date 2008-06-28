<?php
/**
 * Gloger
 * version 1.0.0
 * Genter Date : 2008/06/20
 *
 */

class GLoger {
	
	/**
	 * 把log 写入自定义文件
	 * 
	 *
	 * @param string $pFile
	 * @param string $pMsg
	 */
	public static function logToFile($pMsg,$pFile = GConfig::FILE_APP_LOG ){
		echo $pFile;
		error_log(date("Ymd H:i:s")."---------------------------".SYMBOL_NEWLINE.$pMsg.SYMBOL_NEWLINE,3,$pFile);
	}
	
	/**
	 * 将错误信息发送到指定的邮箱
	 *
	 * @param string $pMsg
	 * @param string $pAdminEmail
	 */
	public static function reportToAdmin($pMsg,$pAdminEmail = GConfig::ADMIN_EMAIL){
		if(null != $pAdminEmail)
			error_log($pMsg,1,$pAdminEmail);
	}
	
/*	public static function reportErrorPage(){
		
	}*/
	
}
?>
