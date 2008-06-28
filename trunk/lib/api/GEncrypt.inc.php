<?php
class GEncrypt {
	protected static function keyED($txt, $encrypt_key) {
		$encrypt_key = md5 ( $encrypt_key );
		$ctr = 0;
		$tmp = "";
		for($i = 0; $i < strlen ( $txt ); $i ++) {
			if ($ctr == strlen ( $encrypt_key ))
				$ctr = 0;
			$tmp .= substr ( $txt, $i, 1 ) ^ substr ( $encrypt_key, $ctr, 1 );
			$ctr ++;
		}
		return $tmp;
	}
	
	public static function encrypt($txt, $key) {
		$encrypt_key = md5 ( (( float ) date ( "YmdHis" ) + rand ( 10000000000000000, 99999999999999999 )) . rand ( 100000, 999999 ) );
		$ctr = 0;
		$tmp = "";
		for($i = 0; $i < strlen ( $txt ); $i ++) {
			if ($ctr == strlen ( $encrypt_key ))
				$ctr = 0;
			$tmp .= substr ( $encrypt_key, $ctr, 1 ) . (substr ( $txt, $i, 1 ) ^ substr ( $encrypt_key, $ctr, 1 ));
			$ctr ++;
		}
		return (base64_encode ( self::keyED ( $tmp, $key ) ));
	}
	
	public static function decrypt($txt, $key) {
		if($txt == ""){ return false;} 
		$txt = self::keyED ( (base64_decode ( $txt )), $key );
		$tmp = "";
		for($i = 0; $i < strlen ( $txt ); $i ++) {
			$md5 = substr ( $txt, $i, 1 );
			$i ++;
			$tmp .= (substr ( $txt, $i, 1 ) ^ $md5);
		}
		return $tmp;
	}
}

?>
