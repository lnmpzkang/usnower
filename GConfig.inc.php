<?php
/**
 * 配置
 *
 */
class GConfig {
	/*----------------------------------------------------
	 * 以下信息可以修改
	----------------------------------------------------*/
	const WEBSITE_NAME="uSnower!";
	const TIMEZONE = 'PRC';
	
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
	const DB_TIMEZONE = "+8:00";
	
	const DIR_TPL			= "tpl/default";
	const DIR_TPL_CACHED = "data/cached";//必须存在，可读写
	const DIR_TPL_COMPILE= "data/compile";//必须存在，可读写
	const DIR_UPLOAD		= "data/upload";
	
	const DIR_XML_STORE  = "data/xml";
	
	///////////////////////////////////////////////////////////////////////
	const DIR_BAG_ORG 	= "data/bag/org";//bag文件必须存在，可读写。原图，不加水印，不缩放，不用于显示
	const DIR_BAG_BIG		= "data/bag/big";//原图，加水印。
	const DIR_BAG_NORMAL = "data/bag/normal";//经过缩放，加水印，适用于显示。
	const DIR_BAG_ICON	= "data/bag/icon";//小图
	const BAG_WATER_MARK_FILE	= "res/logo.gif";
	const BAG_WATER_MARK_ALPHA	= 50;
	const BAG_WATER_MARK_POS	= GImage::POS_TOP_LEFT;
	const BAG_SIZE_NORMAL_W	= 500;
	const BAG_SIZE_NORMAL_H = 500;
	const BAG_SIZE_ICON_W	= 200;
	const BAG_SIZE_ICON_H	= 200;
	
	/*---------------------------------------------------
	 * 以下信息不要修改
	----------------------------------------------------*/
	
	//const SSN_KEY_ERROR_INFO 	= "SSN_KEY_ERROR_INFO";
	
	const FILE_APP_LOG	= "data/logs/app.log";//data/logs 必须存在，可读写	
	const FILE_TPL_LOG	= "data/logs/tpl.log";
	
	/*------------------------------------------------------
	 * session key
	-------------------------------------------------------*/
	
	const SSN_KEY_ADMIN_NAME 				= "SSN_A10";
	const SSN_KEY_ADMIN_ENCRYPT_NAME		= "SSN_A20";
	const SSN_KEY_TOKEN						= "SSN_Z10";
}

?>