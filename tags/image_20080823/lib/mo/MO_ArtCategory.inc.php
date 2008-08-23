<?php

class MO_ArtCategory extends MO {
	public static function add($vo){
		//$vo = new VO_ArtCategory();
		self::checkVO($vo,"VO_ArtCategory");
		
		$sql = sprintf("INSERT INTO %sART_CAT (NAME,FA_ID) VALUES ('%s',%d)",
										GConfig::DB_PREFIX,
										$vo->getName(),
										$vo->getFatherId()
										);

		GMysql::query($sql);
		return GMysql::getInsertId();
	}
	
	/**
	 * 修改
	 * 在数据库里执行 UPDATE Trigger,检查FA＿ID是否发生了循环。
	 *
	 * @param VO_ArtCategory $vo
	 * @return int 被修改的条数
	 */
	public static function edit($vo){
		//$vo = new VO_ArtCategory();
		self::checkVO($vo,"VO_ArtCategory");
		
		$sql = sprintf("UPDATE %sART_CAT SET NAME = '%s' , FA_ID = %d WHERE ID = %d",
										GConfig::DB_PREFIX,
										$vo->getName(),
										$vo->getFatherId(),
										$vo->getId()
										);
		GMysql::query($sql);
		return GMysql::getAffectedRows();										
	}
	
	/**
	 * 删除
	 *在数据库里执行 DELETE Trigger,检查是否有子分类，和之下的文章。
	 * @param unknown_type $vo
	 * @return unknown
	 */
	public static function delete($vo){
		//$vo = new VO_ArtCategory();
		$sql = sprintf("DELETE FROM %sART_CAT WHERE ID = %d",
												GConfig::DB_PREFIX,
												$vo->getId()
												);
		GMysql::query($sql);
		return GMysql::getAffectedRows();
	}
	
	public static function getList($vo){
		self::checkVO($vo,"VO_ArtCategory");
		//$vo = new VO_ArtCategory();
		if($vo->getId() != null){
			$sql = sprintf("SELECT *, %sF_ART_CAT_PATH(ID) AS CAT_PATH FROM %sV_ART_CAT WHERE ID = %d",
											GConfig::DB_PREFIX,
											GConfig::DB_PREFIX,
											$vo->getId()
								);
								
		}else if($vo->getFatherId() != null){
			$sql = sprintf("SELECT *, %sF_ART_CAT_PATH(ID) AS CAT_PATH FROM %sV_ART_CAT WHERE FA_ID = %d",
												GConfig::DB_PREFIX,
												GConfig::DB_PREFIX,
												$vo->getFatherId()
								);
								
		}else if($vo->getName() != null || $vo->getFatherName() != null){
			$sql = sprintf("SELECT *, %sF_ART_CAT_PATH(ID) AS CAT_PATH FROM %sV_ART_CAT WHERE NAME LIKE '%s' OR FA_NAME LIKE '%s'",
													GConfig::DB_PREFIX,
													GConfig::DB_PREFIX,
													$vo->getName(),
													$vo->getFatherName()
								);
		}else{
			$sql = sprintf("SELECT *, %sF_ART_CAT_PATH(ID) AS CAT_PATH FROM %sV_ART_CAT",
												GConfig::DB_PREFIX,
												GConfig::DB_PREFIX
								);
		}
		return GMysql::query($sql);
	}
	
	/**
	 * 输出 ArtCategory 的 XML 目录结构
	 *
	 * @param DOMElement $faNode
	 * @param int $parentId
	 * @return DOMDocument
	 */
	public static function exportTree($faNode = null,$parentId = 0){
		static $dom = null;
		if($dom == null) $dom = new DOMDocument('1.0', 'utf-8');
		
		if($faNode == null){
			$faNode = $dom->createElement("l");
			$faNode->setAttribute("name","Article Category");
			$faNode->setAttribute("id",0);
			$dom->appendChild($faNode);
		}
		
		$sql = sprintf("SELECT *,%sF_ART_CAT_PATH(ID) AS CAT_PATH FROM %sV_ART_CAT WHERE FA_ID = %d",
										GConfig::DB_PREFIX,
										GConfig::DB_PREFIX,
										$parentId
										);
																		
		$rst = GMysql::query($sql);
		while(false != ($arr = GMysql::fetchArray($rst))){
			$node = $dom->createElement("l");
			$faNode->appendChild($node);
			$node->setAttribute("id",$arr["id"]);
			$node->setAttribute("name",$arr["name"]);
			$node->setAttribute("faId",$arr["fa_id"]);
			$node->setAttribute("faName",$arr["fa_name"]);
			$node->setAttribute("catPath",$arr["cat_path"]);
			$node->setAttribute("subNum",$arr["sub_num"]);
			
			self::exportTree($node,$arr["id"]);
		}
		
		return $dom;
	}
}

?>
