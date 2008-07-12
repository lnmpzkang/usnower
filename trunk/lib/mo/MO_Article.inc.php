<?php

class MO_Article extends MO {
	
	/**
	 * Enter description here...
	 *
	 * @param VO_Article $vo
	 * @return int
	 */
	public static function add($vo){
		self::checkVO($vo,"VO_Article");
		$sql = sprintf("INSERT INTO %sART (TITLE,AUTHOR,COME_FROM,CONTENT,TITLE_COLOR,TITLE_B,TITLE_I,SHOW_ABLE,COMMENT_ABLE,CATEGORY) VALUES 
													  ('%s','%s','%s','%s','%s','%s','%s','%s','%s')",
											GConfig::DB_PREFIX,
											$vo->getTitle(),
											$vo->getComeFrom(),
											$vo->getContent(),
											$vo->getTitleColor(),
											$vo->getTitleB(),
											$vo->getTitleI(),
											$vo->getShowAble(),
											$vo->getCommentAble(),
											$vo->getCategory()
											);
		GMysql::query($sql);
		$vo->setId(GMysql::getInsertId());
		
		$akVo = new VO_ArtKeyword();
		$akVo->setArt($vo->getId());//设置文章ID
		
		$keywords = split("|",$vo->getKeywords());//将关键字们折分
		$kVo = new VO_Keyword();
		
		foreach ($keywords as $key){
			if(trim($key) == "") continue;
			$kVo->setKeyword($key);
			$akVo->setKeyword(MO_Keyword::getId($kVo));//设置KEYWORD ID
			MO_ArtKeyword::add($akVo);
		}
	}
	
}

?>
