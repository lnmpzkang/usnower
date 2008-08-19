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
		
		//如果以生成XML文件，则删除。
		$artXMLPath = self::getArtPath($vo->getId());
		if(is_file($artXMLPath))
			unlink($artXMLPath);
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
	
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $catId
	 * @param unknown_type $num
	 * @return array
	 */
	
	public static function getTopList($catId,$num,$orderBy = 'IN_TIME DESC'){
		$map = array(
			'COME_FROM'	=>	'comeFrom',
			'IN_TIME'	=>	'inTime',
			'TITLE_COLOR'	=>	'titleColor',
			'TITLE_B'		=>	'titleB',
			'TITLE_I'		=>	'titleI',
			'TITLE_U'		=>	'titleU',
			'SHOW_ABLE'		=>	'showAble',
			'COMMENT_ABLE'	=>	'commentAble',
			'CAT_ID'			=>	'catId',
			'CAT_NAME'		=>	'catName',
			'CAT_PATH'		=>	'catPath'
		);
		$sql = sprintf("SELECT * FROM %sV_ART WHERE SHOW_ABLE = TRUE AND FIND_IN_SET(CAT_ID,%sF_ART_SUB_CAT(%d)) ORDER BY %s LIMIT 0,%d",
												GConfig::DB_PREFIX,
												GConfig::DB_PREFIX,
												$catId,
												$orderBy,
												$num
												);
		$rst = GMysql::query($sql);
		
		$list = array();
		while($arr = GMysql::fetchAssocWithMap($rst,$map)){
			array_push($list,$arr);
		}
		
		return $list;
	}
	
	/**
	 * 取得article 的 xml文件在哪个目录下。100个文件一个子目录
	 *
	 * @param int $id
	 * @return string
	 */
	public static function getArtPath($id){
		$id = intval($id);
		if($id <= 0)
			throw new GDataException("Invalid Param!");
		
		$subDir = intval($id / 100);
		return PATH_ROOT_ABS."/".GConfig::DIR_XML_ART_STORE."/$subDir/$id.xml";
	}
	
	/**
	 * Enter description here...
	 *
	 * @param int $id
	 * @return DOMDocument
	 */
	public static function exportXML($id){
		
		$map = array(
			'COME_FROM'	=>	'comeFrom',
			'IN_TIME'	=>	'inTime',
			'TITLE_COLOR'	=>	'titleColor',
			'TITLE_B'		=>	'titleB',
			'TITLE_I'		=>	'titleI',
			'TITLE_U'		=>	'titleU',
			'SHOW_ABLE'		=>	'showAble',
			'COMMENT_ABLE'	=>	'commentAble',
			'CAT_ID'			=>	'catId',
			'CAT_NAME'		=>	'catName',
			'CAT_PATH'		=>	'catPath',
			'PRE_ID'			=>	'preId',
			'NEXT_ID'		=>	'nextId',
			'NEXT_TITLE'	=>	'nextTitle',
			'PRE_TITLE'		=>	'preTitle'
		);		
		
		$sql = sprintf("SELECT * FROM %sV_ART WHERE ID = %d",GConfig::DB_PREFIX,$id);
		$rst = GMysql::query($sql);
		$arr = GMysql::fetchFirstWithMap($rst,$map);
		mysql_free_result($rst);
		if($arr === false)
			return false;
		
		$artXMLPath = self::getArtPath($id);
		if(!is_dir( dirname($artXMLPath) ))
			GDir::mkpath( dirname($artXMLPath) );
		
		$dom = new DOMDocument('1.0', 'utf-8');
		$root = $dom->createElement("article");
		$dom->appendChild($root);

		$root->appendChild($dom->createElement('id',$id));
		
		if(sizeof($arr) == 0)
			return;
		
		$root->appendChild($dom->createElement("title",$arr["title"]));
		$root->appendChild($dom->createElement("inTime",$arr["inTime"]));
		$root->appendChild($dom->createElement("author",$arr["author"]));
		$root->appendChild($dom->createElement("comeFrom",$arr["comeFrom"]));
		$root->appendChild($dom->createElement("titleColor",$arr["titleColor"]));
		$root->appendChild($dom->createElement("titleB",$arr["titleB"]));
		$root->appendChild($dom->createElement("titleI",$arr["titleI"]));
		$root->appendChild($dom->createElement("titleU",$arr["titleU"]));
		$root->appendChild($dom->createElement("showAble",$arr["showAble"]));
		$root->appendChild($dom->createElement("commentAble",$arr["commentAble"]));
		$root->appendChild($dom->createElement("catId",$arr["catId"]));
		$root->appendChild($dom->createElement("catName",$arr["catName"]));
		$root->appendChild($dom->createElement("catPath",$arr["catPath"]));
		$root->appendChild($dom->createElement("albums",$arr["albums"]));
		$root->appendChild($dom->createElement("keywords",$arr["keywords"]));
		
		$root->appendChild($dom->createElement("preId",$arr["preId"]));
		$root->appendChild($dom->createElement("nextId",$arr["nextId"]));
		$root->appendChild($dom->createElement("preTitle",$arr["preTitle"]));
		$root->appendChild($dom->createElement("nextTitle",$arr["nextTitle"]));
		
		$content = $dom->createElement("content");
		$content->appendChild($dom->createCDATASection(self::getContent($id)));
		$root->appendChild($content);
		
		$dom->save($artXMLPath);
		unset($content);
		unset($root);
		return $dom;
	}
	
	public static function updateAndGetClick($id){
		$sql = sprintf("SELECT %sF_ART_CLICK(%d)",GConfig::DB_PREFIX, $id);
		$rst = GMysql::query($sql);
		$arr = GMysql::fetchRow($rst);
		mysql_free_result($rst);
		return $arr[0];
	}
	
	public static function getPreAndNext($id){
		//myql 不支持全连接，所以只好这样写了！
		$sql = sprintf("
			SELECT
			  (SELECT ID FROM %sART WHERE SHOW_ABLE = TRUE AND ID > A.ID ORDER BY ID ASC LIMIT 1) AS NEXT_ID,
			  (SELECT TITLE FROM %sART WHERE SHOW_ABLE = TRUE AND ID > A.ID ORDER BY ID ASC LIMIT 1 ) AS NEXT_TITLE,
			  (SELECT ID FROM %sART WHERE SHOW_ABLE = TRUE AND ID < A.ID ORDER BY ID DESC LIMIT 1) AS PRE_ID,
			  (SELECT TITLE FROM %sART WHERE SHOW_ABLE = TRUE AND ID < A.ID ORDER BY ID DESC LIMIT 1) AS PRE_TITLE
			FROM
			  %sART A
			WHERE A.ID = %d",
			GConfig::DB_PREFIX,
			GConfig::DB_PREFIX,
			GConfig::DB_PREFIX,
			GConfig::DB_PREFIX,
			GConfig::DB_PREFIX,
			$id
		);
		
		$map = array(
			'NEXT_ID'	=>	'nextId',
			'NEXT_TITLE'=>	'nextTitle',
			'PRE_ID'		=>	'preId',
			'PRE_TITLE'	=>	'preTitle'
		);
		
		$rst = GMysql::query($sql);
		$arr = GMysql::fetchAssocWithMap($rst,$map);
		
		mysql_free_result($rst);
		return $arr;
	}
}

?>
