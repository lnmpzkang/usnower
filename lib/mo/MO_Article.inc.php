<?php

class MO_Article extends MO {
	
	private static function addKeywords($artId,$keywords){
		$vo = new VO_ArtKeyword();
		$vo->setArt($artId);
		$voKeyword = new VO_Keyword();
		$arr = array_unique( explode('|',$keywords) );
		foreach ($arr as $key){
			if(trim($key) == "") continue;
			$voKeyword->setKeyword($key);
			$vo->setKeyword( MO_Keyword::getId($voKeyword));
			MO_ArtKeyword::add( $vo );
		}
	}
	
	public static function addAlbum($artId,$albums){
		$vo = new VO_ArtAlbum();
		$vo->setArt($artId);
		if(is_array($albums)){
			foreach ($albums as $album){
				$vo->setAlbum($album);
				MO_ArtAlbum::add($vo);
			}
		}
	}
	
	/**
	 * Enter description here...
	 *
	 * @param VO_Article $vo
	 * @return int
	 */
	public static function add($vo){
		self::checkVO($vo,"VO_Article");
		$sql = sprintf("INSERT INTO %sART (TITLE,AUTHOR,COME_FROM,CONTENT,TITLE_COLOR,TITLE_B,TITLE_I,TITLE_U,SHOW_ABLE,COMMENT_ABLE,CATEGORY) VALUES 
													  ('%s','%s',  '%s',     '%s',   '%s',        %d,     %d,     %d,     %d,       %d,          %d)",
											GConfig::DB_PREFIX,
											$vo->getTitle(),
											$vo->getAuthor(),
											$vo->getComeFrom(),
											$vo->getContent(),
											$vo->getTitleColor(),
											$vo->getTitleB(),
											$vo->getTitleI(),
											$vo->getTitleU(),
											$vo->getShowAble(),
											$vo->getCommentAble(),
											$vo->getCategory()
											);
		GMysql::query($sql);
		$vo->setId(GMysql::getInsertId());
		
		//更新 art_keyword（文章－＞关键字对应，一对多） 表
/*		$artKeywordVo = new VO_ArtKeyword();
		$artKeywordVo->setArt($vo->getId());//设置文章ID
		
		$keywords = array_unique( explode("|",$vo->getKeywords()) );//将关键字们折分
		$keywordVo = new VO_Keyword();
		
		foreach ($keywords as $key){
			if(trim($key) == "") continue;
			$keywordVo->setKeyword($key);
			$artKeywordVo->setKeyword(MO_Keyword::getId($keywordVo));//设置KEYWORD ID
			MO_ArtKeyword::add($artKeywordVo);
		}*/
		self::addKeywords($vo->getId(),$vo->getKeywords());
		
		//更新文章－＞专辑表，一对多
/*		$albums = $vo->getAlbums();
		$albumVo = new VO_ArtAlbum();
		if(is_array($albums)){
			foreach ($albums as $album){
				$albumVo->setAlbum($album);
				$albumVo->setArt($vo->getId());
				MO_ArtAlbum::add($albumVo);
			}
		}*/
		self::addAlbum($vo->getId(),$vo->getAlbums());
	}
	
	/**
	 * Enter description here...
	 *
	 * @param VO_Article $vo
	 * @return unknown
	 */
	public static function edit($vo){
		$sql = sprintf("UPDATE %sART SET TITLE = '%s', AUTHOR = '%s' , COME_FROM = '%s' , CONTENT = '%s' , TITLE_COLOR ='%s' , TITLE_B = %d , TITLE_I = %d , TITLE_U = %d ,SHOW_ABLE = %d ,COMMENT_ABLE = %d , CATEGORY = %d WHERE ID = %d",
										GConfig::DB_PREFIX,
										$vo->getTitle(),
										$vo->getAuthor(),
										$vo->getComeFrom(),
										$vo->getContent(),
										$vo->getTitleColor(),
										$vo->getTitleB(),
										$vo->getTitleI(),
										$vo->getTitleU(),
										$vo->getShowAble(),
										$vo->getCommentAble(),
										$vo->getCategory(),
										$vo->getId());
		GMysql::query($sql);
		

/*		//更新文章－＞关键字表，插入新的，删除不存在的，保留相同的
		function removeEmpty($var){
			if($var == null || trim($var) == "") return false;
			else return true;
		}		
		function addQuote($item){
			return "'".$item."'";
		}
		$keywords = array_unique( explode("|",$vo->getKeywords()) );
		$keywords = array_filter($keywords,"removeEmpty");
		$keywords = array_map("addQuote",$keywords);
		$keywordString = implode(",",$keywords);
		
		//删除不存在
		$sql = sprintf("DELETE FROM %sART_KEYWORD WHERE ID IN (
					SELECT C.ID FROM(
					      SELECT
						    A.ID
					      FROM
						    %sART_KEYWORD A LEFT JOIN
						    %sKEYWORD B  ON A.KEYWORD = B.ID
					      WHERE
						    A.ART = IN_ART AND
						    B.KEYWORD NOT IN (%s)
					) C
				)",GConfig::DB_PREFIX,GConfig::DB_PREFIX,GConfig::DB_PREFIX,$keywordString);
		GMysql::query($sql);*/
		
		//由于上面的计划删除以不存在的容易，但是新增新加的不容易实现，所以把原有记录全删了算了。
		$sql = sprintf("DELETE FROM %sART_KEYWORD WHERE ART = %d",GConfig::DB_PREFIX,$vo->getId());
		GMysql::query($sql);
		
		self::addKeywords($vo->getId(),$vo->getKeywords());
		
		//更新文章－＞专辑表，插入新的，删除不存的，保留相同的。
		$sql = sprintf("DELETE FROM %sART_ALBUM WHERE ART = %d",GConfig::DB_PREFIX,$vo->getId());
		GMysql::query($sql);
		
		self::addAlbum($vo->getId(),$vo->getAlbums());
	}
	
	/**
	 * Enter description here...
	 *
	 * @param VO_Article $vo
	 * @return int
	 */
	public static function delete($vo){
		$sql = sprintf("DELETE FROM %sART WHERE ID = %d",GConfig::DB_PREFIX,$vo->getId());
		GMysql::query($sql);
		
		$sql = sprintf("DELETE FROM %sART_KEYWORD WHERE ART = %d",GConfig::DB_PREFIX,$vo->getId());
		GMysql::query($sql);
		
		$sql = sprintf("DELETE FROM %sART_ALBUM WHERE ART = %d",GConfig::DB_PREFIX,$vo->getId());
		GMysql::query($sql);
	}
	
	/**
	 * 根据 id 集合，进行批量删除。
	 *
	 * @param String $ids 1,2,3
	 */
	public static function deleteInIds($ids){
		$sql = sprintf("DELETE FROM %sART WHERE ID IN (%s)",GConfig::DB_PREFIX,$ids);
		GMysql::query($sql);
		
		$sql = sprintf("DELETE FROM %sART_KEYWORD WHERE ART IN (%s)",GConfig::DB_PREFIX,$ids);
		GMysql::query($sql);
		$sql = sprintf("DELETE FROM %sART_ALBUM WHERE ART IN (%s)",GConfig::DB_PREFIX,$ids);
		GMysql::query($sql);
	}
	
	/**
	 * Enter description here...
	 *
	 * @param GPagination $page
	 * @return resource
	 */
	public static function getList(&$page){
		$sql = sprintf("SELECT * FROM %sV_ART ORDER BY IN_TIME DESC",GConfig::DB_PREFIX);
		$page->setQuery($sql);
		$rst = $page->process();
		return $rst;
	} 
	
	/**
	 * Enter description here...
	 *
	 * @param VO_Article $vo
	 */
	public static function get($vo){
		$sql = sprintf("SELECT * FROM %sV_ART WHERE ID = %d",GConfig::DB_PREFIX,$vo->getId());
		$rst = GMysql::query($sql);
		return $rst;
	}
	
	public static function getContent($id){
		$sql = sprintf("SELECT CONTENT FROM %sART WHERE ID = %d",GConfig::DB_PREFIX,$id);
		$rst = GMysql::query($sql);
		if(false != ($arr = GMysql::fetchRow($rst))){
			return $arr[0];
		}else return null;
	}
	
	public static function getListForBlock($catId,$num){
		$sql = sprintf("SELECT * FROM %sV_ART WHERE SHOW_ABLE = TRUE AND FIND_IN_SET(CAT_ID,%sF_ART_SUB_CAT(%d)) ORDER BY IN_TIME DESC LIMIT 0,%d",
												GConfig::DB_PREFIX,
												GConfig::DB_PREFIX,
												$catId,
												$num
												);
		return GMysql::query($sql);
	}
}

?>
