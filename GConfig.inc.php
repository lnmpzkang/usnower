<?php
/**
 * 配置
 *
 */
class GConfig {
	/*----------------------------------------------------
	 * 以下信息可以修改
	----------------------------------------------------*/
	const ADMIN_EMAIL = "nickr@copacast.com";
	const ENCRYPT_KEY = "vpqdt234b";
	
	/**
	 * 数据库配置
	 *
	 */
	const DB_PREFIX	= "USNOWER_";
	const DB_HOST 		= "127.0.0.1";
	const DB_PORT		= 3306;
	const DB_USER		= "usnower";
	const DB_PWD		= "usnower";
	const DB_NAME		= "usnower";
	const DB_CHARSET	= "utf8";
	
	/*---------------------------------------------------
	 * 以下信息不要修改
	----------------------------------------------------*/
	
	//const SSN_KEY_ERROR_INFO 	= "SSN_KEY_ERROR_INFO";
	
	const FILE_APP_LOG	= "logs/app.log";//logs 必须存在，可读写	
	
	/*------------------------------------------------------
	 * session key
	-------------------------------------------------------*/
	
	const SSN_KEY_ADMIN_NAME 				= "SSN_A10";
	const SSN_KEY_ADMIN_ENCRYPT_NAME		= "SSN_A20";
}

?>