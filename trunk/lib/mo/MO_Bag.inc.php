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

	/**
	 * Enter description here...
	 *
	 * @param VO_Bag $vo
	 */
	public static function delete($vo){
		$pVo = new VO_BagPic();
		$pVo->setBag($vo->getId());
		MO_BagPic::delete($pVo);
		
		$sql = sprintf("DELETE FROM %sBAG WHERE ID = %d",GConfig::DB_PREFIX,$vo->getId());
		GMysql::query($sql);
	}
	/**
	 * Enter description here...
	 *
	 * @param GPagination $page
	 */
/*	public static function getList(&$page){
		$sql = sprintf("SELECT * FROM %sV_BAG",GConfig::DB_PREFIX);
		$page->setQuery($sql);
		return $page->process();
	}
	*/
	
	/**
	 * Enter description here...
	 *
	 * @param GPagination $page
	 * @return array
	 */
	public static function getList(&$page){
		$map = array(
			"SIZE_H"		=>	"sizeH",
			"SIZE_W"		=>	"sizeW",
			"SIZE_L"		=>	"sizeL",
			"IN_TIME"	=>	"inTime",
			"CAT_NAME"	=>	"catName",
			"CAT_PATH"	=>	"catPath",
			"PIC_NUM"	=>	"picNum",
		);
		$sql = sprintf("SELECT * FROM %sV_BAG",GConfig::DB_PREFIX);
		$page->setQuery($sql);
		$rst = $page->process();
		
		$list = array();
		while(false != ($arr = GMysql::fetchAssocWithMap($rst,$map))){
			array_push($list,$arr);
		}

		return $list;
	}
	
	/**
	 * Enter description here...
	 *
	 * @param VO_Bag $vo
	 */
	public static function get($vo){
		self::checkVO($vo,"VO_Bag");
		$sql = sprintf("SELECT * FROM %sBAG WHERE ID = %d",GConfig::DB_PREFIX,$vo->getId());
		$map = array(
			"SIZE_H"		=>	"sizeH",
			"SIZE_W"		=>	"sizeW",
			"SIZE_L"		=>	"sizeL",
			"IN_TIME"	=>	"inTime",
			"CAT_NAME"	=>	"catName",
			"CAT_PATH"	=>	"catPath",
		);
		$rst = GMysql::query($sql);
		return GMysql::fetchFirstWithMap($rst,$map);
	}
}

?>
