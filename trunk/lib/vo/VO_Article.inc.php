<?php

class VO_Article {
	private $id,$title,$author,$comeFrom,$inTime,$content;
	private $titleColor,$titleB = false,$titleI = false,$titleU = false;
	private $showAble = true,$commentAble = true;
	private $category,$albums;
	private $keywords;
	
	private $autoUploadOtherSItePic;
	private $preventCopy;
	
	/**
	 * @return unknown
	 */
	public function getAutoUploadOtherSItePic() {
		return $this->autoUploadOtherSItePic;
	}
	
	/**
	 * @param unknown_type $autoUploadOtherSItePic
	 */
	public function setAutoUploadOtherSItePic($autoUploadOtherSItePic) {
		$this->autoUploadOtherSItePic = $autoUploadOtherSItePic;
	}
	/**
	 * @return unknown
	 */
	public function getPreventCopy() {
		return $this->preventCopy;
	}
	
	
	/**
	 * @param unknown_type $preventCopy
	 */
	public function setPreventCopy($preventCopy) {
		$this->preventCopy = $preventCopy;
	}
	/**
	 * @return unknown
	 */
	public function getKeywords() {
		return $this->keywords;
	}
	
	/**
	 * @param unknown_type $keywords
	 */
	public function setKeywords($keywords) {
		//删除符号
		$keywords = preg_replace('/[^a-z0-9\x{4e00}-\x{9fa5}\|]/isu',"",$keywords);
		$this->keywords = $keywords;
	}
	/**
	 * @return unknown
	 */
	public function getAuthor() {
		return $this->author;
	}
	
	/**
	 * @return unknown
	 */
	public function getCategory() {
		return $this->category;
	}
	
	/**
	 * @return unknown
	 */
	public function getComeFrom() {
		return $this->comeFrom;
	}
	
	/**
	 * @return unknown
	 */
	public function getCommentAble() {
		return $this->commentAble;
	}
	
	/**
	 * @return unknown
	 */
	public function getContent() {
		return $this->content;
	}
	
	/**
	 * @return unknown
	 */
	public function getId() {
		return $this->id;
	}
	
	/**
	 * @return unknown
	 */
	public function getInTime() {
		return $this->inTime;
	}
	
	/**
	 * @return unknown
	 */
	public function getShowAble() {
		return $this->showAble;
	}
	
	/**
	 * @return unknown
	 */
	public function getTitle() {
		return $this->title;
	}
	
	/**
	 * @return unknown
	 */
	public function getTitleB() {
		return $this->titleB;
	}
	
	/**
	 * @return unknown
	 */
	public function getTitleColor() {
		return $this->titleColor;
	}
	
	/**
	 * @return unknown
	 */
	public function getTitleI() {
		return $this->titleI;
	}
	
	/**
	 * @param unknown_type $author
	 */
	public function setAuthor($author) {
		if(!GValidate::checkString(trim($author),array("required"=>false,"max"	=>	30))){
			throw new GDataException("Author is not required.And it's length must less than 30.(One Chinese Character ".MUTI_CHAR_LEN." length)");
		}
		$this->author = trim($author);
	}
	
	/**
	 * @param unknown_type $category
	 */
	public function setCategory($category) {
		$this->category = $category;
	}
	
	/**
	 * @param unknown_type $comeFrom
	 */
	public function setComeFrom($comeFrom) {
		$comeFrom = trim($comeFrom);
		if(!GValidate::checkString($comeFrom,array("required"=>false,"max"=>300))){
			throw new GDataException("Come from is not required.And it's length must less than 300.(One Chinese Character ".MUTI_CHAR_LEN." length)");
		}
		$this->comeFrom = $comeFrom;
	}
	
	/**
	 * @param unknown_type $commentAble
	 */
	public function setCommentAble($commentAble) {
		$this->commentAble = $commentAble;
	}
	
	/**
	 * @param unknown_type $content
	 */
	public function setContent($content) {
		if(!GValidate::checkString(trim($content),array("required"=>true,"min"=>10))){
			throw new GDataException("Article Content is required.And content length must more than 10");
		}
		$this->content = $content;
	}
	
	/**
	 * @param unknown_type $id
	 */
	public function setId($id) {
		$this->id = $id;
	}
	
	/**
	 * @param unknown_type $inTime
	 */
	public function setInTime($inTime) {
		$this->inTime = $inTime;
	}
	
	/**
	 * @param unknown_type $showAble
	 */
	public function setShowAble($showAble) {
		$this->showAble = $showAble;
	}
	
	/**
	 * @param unknown_type $title
	 */
	public function setTitle($title) {
		$title = trim($title);
		if(!GValidate::checkString($title,array("required"=>true,"max"=>300))){
			throw new GDataException("Title is required.And it's length must less than 300.(One Chinese Character ".MUTI_CHAR_LEN." length)");
		}
		$this->title = $title;
	}
	
	/**
	 * @param unknown_type $titleB
	 */
	public function setTitleB($titleB) {
		$this->titleB = $titleB;
	}
	
	/**
	 * @param unknown_type $titleColor
	 */
	public function setTitleColor($titleColor) {
		$this->titleColor = $titleColor;
	}
	
	/**
	 * @param unknown_type $titleI
	 */
	public function setTitleI($titleI) {
		$this->titleI = $titleI;
	}
	/**
	 * @return unknown
	 */
	public function getTitleU() {
		return $this->titleU;
	}
	
	/**
	 * @param unknown_type $titleU
	 */
	public function setTitleU($titleU) {
		$this->titleU = $titleU;
	}
	/**
	 * @return unknown
	 */
	public function getAlbums() {
		return $this->albums;
	}
	
	/**
	 * @param unknown_type $albums
	 */
	public function setAlbums($albums) {
		$this->albums = $albums;
	}


	
}

?>