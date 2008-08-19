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
	public static function getList(&$page){
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
					ORDER BY A.IN_TIME DESC
		",
		GConfig::DB_PREFIX,
		GConfig::DB_PREFIX,
		GConfig::DB_PREFIX);
		
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
}

?>
