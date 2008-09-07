<?php

class MO_Comment extends MO {

	/**
	 * Enter description here...
	 *
	 * @param VO_Comment $vo
	 */
	public static function add($vo){
		$sql = sprintf("INSERT INTO %sCOMMENT (TAG,FOR_ID,IP,					NAME,EMAIL,HTTP,CONTENT,FOR_ADMIN) VALUES (
															'%s',%d,	  INET_ATON('%s'),	'%s','%s',	'%s','%s',	%d	)",
											GConfig::DB_PREFIX,
											$vo->getTag(),
											$vo->getForId(),
											$vo->getIp(),
											$vo->getName(),
											$vo->getEmail(),
											$vo->getHttp(),
											$vo->getContent(),
											//$vo->getShowAble(), 新增时,不能指定show_able
											$vo->getForAdmin()
		);
		echo $query;
		GMysql::query($sql);
		return GMysql::getInsertId();
	}
	
	public static function pass($id){
		$sql = sprintf("UPDATE %sCOMMENT SET SHOW_ABLE = TRUE WHERE ID = %d",GConfig::DB_PREFIX,$id);
		GMysql::query($sql);
		return GMysql::getAffectedRows();
	}
	
	public static function delete($id){
		$sql = sprintf("DELETE FROM %sCOMMENT WHERE ID = %d",GConfig::DB_PREFIX,$id);
		GMysql::query($sql);
		return GMysql::getAffectedRows();
	}
	
	public static function unpass($id){
		$sql = sprintf("UPDATE %sCOMMENT SET SHOW_ABLE = FALSE WHERE ID = %d",GConfig::DB_PREFIX,$id);
		GMysql::query($sql);
		return GMysql::getAffectedRows();
	}
	
	/**
	 * Enter description here...
	 *
	 * @param GPagination $page
	 */
	public static function getList(&$page,$where = 'TRUE'){
		$sql = sprintf("
					SELECT
					  A.*,
					  INET_NTOA(A.IP) AS IP_STRING,
					  CASE A.TAG
					    WHEN 'A' THEN B.TITLE
					    WHEN 'B' THEN C.NAME
					  END AS FOR_TITLE
					FROM
					  %sCOMMENT A LEFT JOIN
					  %sART B ON A.FOR_ID = B.ID LEFT JOIN
					  %sBAG C ON A.FOR_ID = C.ID
					WHERE
						%s	
					ORDER BY A.IN_TIME DESC
		",
		GConfig::DB_PREFIX,
		GConfig::DB_PREFIX,
		GConfig::DB_PREFIX,
		$where
		);
		
		$page->setQuery($sql);
		$rst = $page->process();
		$list = array();
		$map = array(
			'IN_TIME'	=>	'inTime',
			'SHOW_ABLE'	=>	'showAble',
			'FOR_ADMIN'	=>	'forAdmin',
			'FOR_ID'		=>	'forId',
			'FOR_TITLE'	=>	'forTitle',
			'IP_STRING'	=>	'ip'
		);
		while ($arr = GMysql::fetchAssocWithMap($rst,$map)){
			if($arr['tag'] == 'A')
				$arr['forUrl'] = PATH_ROOT_RELATIVE.'art.php?id='.$arr['forId'];
			elseif($arr['tag'] == 'B')
				$arr['forUrl'] = PATH_ROOT_RELATIVE.'bag.php?id='.$arr['forId'];
				
			array_push($list,$arr);
		}
		mysql_free_result($rst);
		return $list;
	}
	
	public static function getCommentPath($forId,$tag){
		$forId = intval($forId);
		if($forId <= 0){
			throw new GDataException("Invalid Param!");
		}
		$subDir = intval($forId / 100);
		return PATH_ROOT_ABS."/".GConfig::DIR_XML_CMT_STORE."/$tag/$subDir/$forId.xml";
	}
	
	
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $forId
	 * @param unknown_type $tag
	 * @return DOMDocument
	 */
	public static function exportXML($forId,$tag){
		$sql = sprintf("SELECT *,INET_NTOA(IP) AS IP_STRING FROM %sCOMMENT WHERE SHOW_ABLE = TRUE AND TAG = '%s' AND FOR_ID = %d",
												GConfig::DB_PREFIX,
												$tag,
												$forId
							);
		$rst = GMysql::query($sql);
		
		if(mysql_num_rows($rst) <= 0)
			return false;
		
		$map = array(
			'IN_TIME'	=>	'inTime',
			'IP_STRING'	=>	'ip',
			'FOR_ADMIN'	=>	'forAdmin'
		);
		
		
		$dom = new DOMDocument('1.0','utf-8');
		$root = $dom->createElement('comment');
		$dom->appendChild($root);
		$root->setAttribute('tag',$tag);
		$root->setAttribute('forId',$forId);
		
		while ($arr = GMysql::fetchAssocWithMap($rst,$map)){
			$cmt = $dom->createElement('cmt');
			$root->appendChild($cmt);
			//$cmt->setAttribute('IP',$arr['ip']);
			$cmt->setAttribute('title',$arr['title']);
			$cmt->setAttribute('name',$arr['name']);
			//$cmt->setAttribute('Email',$arr['email']);
			$cmt->setAttribute('http',$arr['http']);
			$cmt->setAttribute('forAdmin',$arr['forAdmin']);
			$cmt->setAttribute('inTime',$arr['inTime']);
			
			if($arr['forAdmin'] == true)
				$content = $dom->createCDATASection('This message is for admin!');
			else
				$content = $dom->createCDATASection($arr['content']);
			
			$cmt->appendChild($content);
		}
		
		$dir = self::getCommentPath($forId,$tag);
		if(!is_dir($dir))
			GDir::mkpath( dirname($dir));
			
		$dom->save($dir);
		unset($root);
		return $dom;
	}
	
	public static function refreshComment($tag,$forId){
		$path = self::getCommentPath($forId,$tag);
		if(is_file($path))
			unlink($path);
	}
}

?>
