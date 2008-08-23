<?php

class MO_ArtKeyword extends MO {
	
	/**
	 * Enter description here...
	 *
	 * @param VO_ArtKeyword $vo
	 * @return int
	 */
	public static function add($vo){
		self::checkVO($vo,"VO_ArtKeyword");
		$sql = sprintf("INSERT INTO %sART_KEYWORD (ART,KEYWORD) VALUES (%d,%d)",
											GConfig::DB_PREFIX,
											$vo->getArt(),
											$vo->getKeyword()
											);
		GMysql::query($sql);
		return GMysql::getInsertId();											
	}
}

?>
