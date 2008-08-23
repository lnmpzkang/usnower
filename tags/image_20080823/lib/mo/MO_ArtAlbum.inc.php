<?php

class MO_ArtAlbum extends MO {
	
	/**
	 * Enter description here...
	 *
	 * @param VO_ArtAlbum $vo
	 * @return int
	 */
	public static function add($vo){
		self::checkVO($vo,"VO_ArtAlbum");
		$sql = sprintf("INSERT INTO %sART_ALBUM (ALBUM,ART) VALUES (%d,%d)",
											GConfig::DB_PREFIX,
											$vo->getAlbum(),
											$vo->getArt()
											);
		GMysql::query($sql);
		return GMysql::getInsertId();											
	}
}

?>
