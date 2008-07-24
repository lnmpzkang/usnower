<?php
class GPagination extends GMysql {
	public $pageSize 	= 20;
	public $currPage 	= 1;
	public $recordNum	= 0;
	public $totalPage 	= 0;
	public $labelCount = 10;
	public $URLKey		= "PAGE";
	public $rangeS	= 0;
	public $rangeE	= 0;
	
	public $regPattern = null;
	public $regReplacement = null;
	
	private $baseURL = "";
	private $baseQueryString = "";
	
	private $query = "";
	
	protected function __isset($var){
		echo $var;
	}
	
	public function setQuery($query){
		$this->query = $query;
	}
	
	public function getQuery(){
		return $this->query;
	}
	
	private function nvl($pOrg,$pDefault){
		if(!is_null($pOrg) && $pOrg != ""){
			return $pOrg;
		}else{
			return $pDefault;
		}
	}
	
	function __construct(){
		$this->currPage = $this->nvl($_GET[$this->URLKey],1);		
		$this->baseURL = $_SERVER['PHP_SELF'];
		
		foreach($_GET as $k => $v)
			if($k != $this->URLKey)
				$this->baseQueryString .= $k."=".urlencode($v)."&";
		
		if($this->currPage < 1)
			$this->currPage = 1;		
	}
	
	function setBaseURL($pURL){
		$this->baseURL = $pURL;
	}	
	
	public function process(){
		if($this->pageSize <=0)
			return false;
			
		$result = GMysql::query("SELECT COUNT(1) FROM ($this->query) __PAGEINATION__");
		$arr = GMysql::fetchRow($result);
		$this->recordNum = $arr[0];
		
		$this->totalPage = ceil($this->recordNum / $this->pageSize);
		
		if($this->currPage > $this->totalPage)
				$this->currPage = $this->totalPage;	
		
		$rangeS = ($this->currPage - 1) * $this->pageSize;
		$this->rangeS = $rangeS + 1;
		
		if($this->rangeS + $this->pageSize > $this->recordNum)
			$this->rangeE = $this->recordNum;
		else
			$this->rangeE = $this->rangeS + $this->pageSize - 1;
			
		$this->query .= " limit $rangeS,$this->pageSize";		
		//$result = mysql_query($this->query);
		//echo $this->query;
		$result = GMysql::query($this->query);
		
		return $result;
	}
	
	
	public function exportPageLabel(){
		if($this->recordNum == 0)
			return "";
		$pageList = "";
		$startNum = floor(($this->currPage-1)/$this->labelCount)*$this->labelCount+1;

		if($this->currPage != 1)
			$pageList .="<span class='inactivePage'><a href='".$this->baseURL."?$this->URLKey=1&".$this->baseQueryString."' title='第一页'>|&lt;</a></span>";
		
		if($startNum - $this->labelCount > 0)
			$pageList .="<span class='inactivePage'><a href='$this->baseURL?$this->URLKey=".($startNum-1)."&$this->baseQueryString' title='前$this->labelCount页'>&lt;&lt</a></span>";
		
		for($i=$startNum;$i<$this->labelCount + $startNum;$i++){
			if($i == $this->currPage)
				$className="activePage";
			else
				$className="inactivePage";
			
			$pageList .= "<span class='$className'><a href='$this->baseURL?$this->URLKey=$i&$this->baseQueryString'>$i</a></span>";
			
			if($i > $this->totalPage - 1)
				break;
		}
		
		if($startNum + $this->labelCount < $this->totalPage){
			$pageList .= "<span class='inactivePage'><a href='$this->baseURL?$this->URLKey=".($startNum+$this->labelCount)."&$this->baseQueryString' title='后".$this->labelCount."页'>&gt;&gt</a></span>";
		}
		
		if($this->currPage != $this->totalPage)
			$pageList .= "<span class='inactivePage'><a href='$this->baseURL?$this->URLKey=$this->totalPage&$this->baseQueryString' title='最后一页'>&gt;|</a></span>";
		
		$pageList .="<input name=\"page\" type=\"text\" class=\"goInput\" id=\"page\" size=\"2\"/>";
		$pageList .="<span title='跳转' class=\"activePage\" style=\"cursor:pointer\" onclick=\"location.href='$this->baseURL?$this->baseQueryString&$this->URLKey=' + document.getElementById('page').value\">Go!</span>";
		return $pageList;	
	}
	
	public function exportPageLabelWithRegexp(){
		$pageList = $this->exportPageLabel();
		
		//exit($pageList);
		
		return preg_replace($this->regPattern,$this->regReplacement,$pageList);
	}
}
?>