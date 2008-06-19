<?php
/**
 * 配置
 *
 */
class GConfig {
	/*
	 * 以下信息可以修改
	*/
	const ADMIN_EMAIL = "nickr@copacast.com";
	const ENCRYPT_KEY = "fae15pw4qvz";
	
	/*
	 * 以下信息不要修改
	*/
	
	const SSN_KEY_ERROR_INFO 	= "SSN_KEY_ERROR_INFO";
	
	const FILE_APP_LOG	= "logs/app.log";//logs 必须存在，可读写	
}

?>
