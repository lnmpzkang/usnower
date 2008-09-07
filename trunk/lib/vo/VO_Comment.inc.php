<?php

class VO_Comment extends VO {
	private $id,$tag;
	private $ip,$name,$email;
	private $http,$content;
	private $inTime,$showAble = false ,$forAdmin = false;
	private $forId;
	private $title;
	
	/**
	 * @return unknown
	 */
	public function getTitle() {
		return $this->title;
	}
	
	/**
	 * @param unknown_type $title
	 */
	public function setTitle($title) {
		$title = trim($title);
		if(!GValidate::checkString($title,array('required'=>true,'max'=>100))){
			throw new GDataException('Title required.And it\'s length must between 1 and 100');
		}
		$this->title = $title;
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
	public function getEmail() {
		return $this->email;
	}
	
	/**
	 * @return unknown
	 */
	public function getForAdmin() {
		return $this->forAdmin;
	}
	
	/**
	 * @return unknown
	 */
	public function getHttp() {
		return $this->http;
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
	public function getIp() {
		return $this->ip;
	}
	
	/**
	 * @return unknown
	 */
	public function getName() {
		return $this->name;
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
	public function getTag() {
		return $this->tag;
	}
	
	/**
	 * @param unknown_type $content
	 */
	public function setContent($content) {
		//no trim,但是如果只有空格，返回错误。
		if(!GValidate::checkString($content,array('required'=>true,'max'=>1000,'min'=>5))){
			throw new GDataException('Please input you comment,and it\'s length must more than 5 and less than 1000.One Chinese character '.MUTI_CHAR_LEN.' length');	
		}
		$this->content = $content;
	}
	
	/**
	 * @param unknown_type $email
	 */
	public function setEmail($email) {
		
		$email = trim($email);
		if(!GValidate::checkEmail($email,array('required'=>false,'max'=>'100'))){
			throw new GDataException('Email is not required.But please input a valid email if you want.');
		}		
		
		$this->email = $email;
	}
	
	/**
	 * @param unknown_type $forAdmin
	 */
	public function setForAdmin($forAdmin) {
		$this->forAdmin = $forAdmin;
	}
	
	/**
	 * @param unknown_type $http
	 */
	public function setHttp($http) {
		$http = trim($http);
		if(!GValidate::checkURL($http,array('required'=>false,'max'=>100))){
			throw new GDataException('Website is not required.But please input a valid url if you want.');
		}
		$this->http = $http;
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
	 * @param unknown_type $ip
	 */
	public function setIp($ip) {
		$this->ip = $ip;
	}
	
	/**
	 * @param unknown_type $name
	 */
	public function setName($name) {
		$this->name = $name;
	}
	
	/**
	 * @param unknown_type $showAble
	 */
	public function setShowAble($showAble) {
		$this->showAble = $showAble;
	}
	
	/**
	 * @param unknown_type $tag
	 */
	public function setTag($tag) {
		$this->tag = $tag;
	}
	/**
	 * @return unknown
	 */
	public function getForId() {
		return $this->forId;
	}
	
	/**
	 * @param unknown_type $forId
	 */
	public function setForId($forId) {
		$this->forId = $forId;
	}

	
}

?>
