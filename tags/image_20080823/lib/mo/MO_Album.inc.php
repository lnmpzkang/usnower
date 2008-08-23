<?php

class MO_Album extends MO {
	
	/**
	 * 
	 *
	 * @param VO_Album $vo
	 * @return int
	 */
	public static function add($vo){
		self::checkVO($vo,"VO_Album");
		
		$sql = sprintf("INSERT INTO %sALBUM (NAME,DESCRIPTION) VALUES ('%s','%s')",
												GConfig::DB_PREFIX,
												$vo->getName(),
												$vo->getDescription()
												);
		return GMysql::query($sql);
	}
	
	/**
	 * Enter description here...
	 *
	 * @param VO_Album $vo
	 * @return int
	 */
	public static function edit($vo){
		self::checkVO($vo,"VO_Album");
		$sql = sprintf("UPDATE %sALBUM SET NAME = '%s',DESCRIPTION = '%s' WHERE ID = %d",
										GConfig::DB_PREFIX,
										$vo->getName(),
										$vo->getDescription(),
										$vo->getId()
										);
		GMysql::query($sql);
		return GMysql::getAffectedRows();
	}
	
	
	/**
	 * Enter description here...
	 *
	 * @param VO_Album $vo
	 * @return int
	 */
	public static function delete($vo){
		self::checkVO($vo,"VO_Album");
		$sql = sprintf("DELETE FROM %sALBUM WHERE ID = %d",
							GCOnfig::DB_PREFIX,
							$vo->getId()
							);
		GMysql::query($sql);
		return GMysql::getAffectedRows();
	}
	
	/**
	 * Enter description here...
	 *
	 * @return resource
	 */
	public static function getList(){
		$sql = sprintf("SELECT * FROM %sALBUM",GConfig::DB_PREFIX);
		$rst = GMysql::query($sql);
		return $rst;	
	}
}

?>
