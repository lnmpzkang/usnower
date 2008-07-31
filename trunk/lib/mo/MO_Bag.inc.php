<?php

class MO_Bag extends MO {
	/**
	 * Enter description here...
	 *
	 * @param VO_Bag $vo
	 * @return int
	 */
	public static function add($vo){
		self::checkVO($vo,"VO_Bag");
		$sql = sprintf("INSERT INTO %sBAG (NAME,NO,SIZE_L,SIZE_W,SIZE_H,UNIT,FABRIC,DESCRIPTION,CAT) VALUES ('%s','%s',%d,%d,%d,'%s','%s','%s',%d)",
												GConfig::DB_PREFIX,
												$vo->getName(),
												$vo->getNo(),
												$vo->getSizeL(),
												$vo->getSizeW(),
												$vo->getSizeH(),
												$vo->getUnit(),
												$vo->getFabric(),
												$vo->getDescription(),
												$vo->getCat()
												);
		GMysql::query($sql);
		return GMysql::getInsertId();
	}
}

?>
