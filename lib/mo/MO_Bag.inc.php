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
	 * @param VO_Bag $vo
	 */
	public static function edit($vo){
		$sql = sprintf("UPDATE %sBAG SET NAME='%s',NO='%s',SIZE_L=%d,SIZE_W=%d,SIZE_H=%d,UNIT='%s',FABRIC='%s',DESCRIPTION='%s',CAT=%d WHERE ID = %d",
							GConfig::DB_PREFIX,
							$vo->getName(),
							$vo->getNo(),
							$vo->getSizeL(),
							$vo->getSizeW(),
							$vo->getSizeH(),
							$vo->getUnit(),
							$vo->getFabric(),
							$vo->getDescription(),
							$vo->getCat(),
							$vo->getId()
		);
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
		$sql = sprintf("SELECT * FROM %sV_BAG WHERE ID = %d",GConfig::DB_PREFIX,$vo->getId());
		$map = array(
			"SIZE_H"		=>	"sizeH",
			"SIZE_W"		=>	"sizeW",
			"SIZE_L"		=>	"sizeL",
			"IN_TIME"	=>	"inTime",
			"CAT_NAME"	=>	"catName",
			"CAT_PATH"	=>	"catPath",
			"PIC_NUM"	=>	"picNum"
		);
		$rst = GMysql::query($sql);
		return GMysql::fetchFirstWithMap($rst,$map);
	}
	
	public static function getTopList($cat,$num){
		$sql = sprintf("
			SELECT 
				A.*,
				%sF_BAG_PICS(A.ID) AS RES,
				(SELECT ICON FROM %sBAG_PIC WHERE BAG = A.ID ORDER BY IN_TIME DESC LIMIT 1) AS ICON,
				(SELECT NORMAL FROM %sBAG_PIC WHERE BAG = A.ID ORDER BY IN_TIME DESC LIMIT 1) AS NORMAL,
				(SELECT BIG FROM %sBAG_PIC WHERE BAG = A.ID ORDER BY IN_TIME DESC LIMIT 1) AS BIG
			FROM 
				%sV_BAG A
			WHERE 
				FIND_IN_SET(A.CAT, %sF_BAG_SUB_CAT(%d)) 
			ORDER BY A.IN_TIME LIMIT %d
		",
		GConfig::DB_PREFIX,
		GConfig::DB_PREFIX,
		GConfig::DB_PREFIX,
		GConfig::DB_PREFIX,
		GConfig::DB_PREFIX,
		GConfig::DB_PREFIX,
		$cat,
		$num);

		
		$rst = GMysql::query($sql);
		
		$map = array(
			"SIZE_H"		=>	"sizeH",
			"SIZE_W"		=>	"sizeW",
			"SIZE_L"		=>	"sizeL",
			"IN_TIME"	=>	"inTime",
			"CAT_NAME"	=>	"catName",
			"CAT_PATH"	=>	"catPath",
			"PIC_NUM"	=>	"picNum"
		);
		$list = array();
		while ($arr = GMysql::fetchAssocWithMap($rst,$map)){
			array_push($list,$arr);
		}

		mysql_free_result($rst);
		return $list;
	}
}

?>
