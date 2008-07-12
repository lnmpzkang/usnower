<?php

class MO_Keyword extends MO {
	
	/**
	 * 如果存在，就返回以存在的ID，如果不存在，就插入，并返回ID
	 *
	 * @param VO_Keyword $vo
	 * @return int
	 */
	public static function getId($vo){
		self::checkVO($vo,"VO_Keyword");
		//$sql = sprintf("INSERT INTO %KEYWORD (KEYWORD) VALUES ('%s')",GConfig::DB_PREFIX,$vo->getKeyword());
		//$sql = sprintf("CALL %sP_RECORD_KEYWORD('%s',@ID)",GConfig::DB_PREFIX,$vo->getKeyword());
		$sql = sprintf("SELECT %sF_RECORD_KEYWORD('%s') AS ID",GConfig::DB_PREFIX,$vo->getKeyword());
		$rst = GMysql::query($sql);
		
		$arr = GMysql::fetchRow($rst);
		return $arr[0];
	}
	
	/**
	 * Enter description here...
	 *
	 * @param VO_Keyword $vo
	 * @return int
	 */
	public static function edit($vo){
		self::checkVO($vo,"VO_Keyword");
		$sql = sprintf("UPDATE %KEYWORD SET KEYWORD = '%s' WEHRE ID = %d",GConfig::DB_PREFIX,$vo->getKeyword(),$vo->getId());
		GMysql::query($sql);
		return GMysql::getAffectedRows();
	}
	
	/**
	 * Enter description here...
	 *
	 * @param VO_Keyword $vo
	 * @return int
	 */
	public static function delete($vo){
		self::checkVO($vo,"VO_Keyword");
		$sql = sprintf("DELETE FROM %KEYWORD WHERE ID = %d",GConfig::DB_PREFIX,$vo->getId());
		GMysql::query($sql);
		return GMysql::getAffectedRows();
	}
}

?>
