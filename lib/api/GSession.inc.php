<?php

class GSession {
	/**
	 * 设置session，如果键不存在，就创建一个．
	 *
	 * @param string $k 键
	 * @param mixed $v 值
	 * @return void
	 */
	public static function set($k,$v){
		if (!session_is_registered($k)) {
			session_register($k);
		}
		$_SESSION[$k] = $v;
	}
	
	public static function get($k){
		return $_SESSION[$k];
	}
}

?>
