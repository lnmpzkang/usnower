<?php

class MO_ArtAlbum extends MO {
	
	/**
	 * 
	 *
	 * @param VO_ArtAlbum $vo
	 * @return int
	 */
	public static function add($vo){
		self::checkVO($vo,"VO_ArtAlbum");
		
		$sql = sprintf("INSERT INTO %sART_ALBUM (NAME,DESCRIPTION) VALUES ('%s','%s')",
												GConfig::DB_PREFIX,
												$vo->getName(),
												$vo->getDescription()
												);
		return GMysql::query($sql);
	}
	
	/**
	 * Enter description here...
	 *
	 * @param VO_ArtAlbum $vo
	 * @return int
	 */
	public static function edit($vo){
		self::checkVO($vo,"VO_ArtAlbum");
		$sql = sprintf("UPDATE %sART_ALBUM SET NAME = '%s',DESCRIPTION = '%s' WHERE ID = %d",
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
	 * @param VO_ArtAlbum $vo
	 * @return int
	 */
	public static function delete($vo){
		self::checkVO($vo,"VO_ArtAlbum");
		$sql = sprintf("DELETE FROM %sART_ALBUM WHERE ID = %d",
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
		$sql = sprintf("SELECT * FROM %sART_ALBUM",GConfig::DB_PREFIX);
		$rst = GMysql::query($sql);
		return $rst;	
	}
}

?>
