<?php

class GValidate {
	
	const TYPE_STRING = 1;
	const TYPE_NUMBER = 2;
	const TYPE_INT		= 3;
	const TYPE_FLOAT	= 4;
	const TYPE_DATE	= 5;
	
	/**
	 * Enter description here...
	 *
	 * @param mixed $rule
	 * @param array $value
	 * @return boolean
	 */
	public static function check($value,$rule){

		if($rule["required"] == true && (trim($value) == "" || $value == null)){
			return false;
		}
		
	}
	
	/**
	 * Enter description here...
	 *
	 * @param number $value
	 * @param number $min
	 * @param number $max
	 * @return boolean
	 */
	//public static function checkNumRange($value,$min = -INF,$max = INF){			
	public static function checkNumRange($value,$rule){
		if($rule["required"] == false && $value == null) return true;
		
		if($rule["min"] == null) $rule["min"] = -INF;
		if($rule["max"] == null) $rule["max"] = INF;
		
		$arr = array($rule["min"],$rule["max"]);
		$min = min($arr) != null ? min($arr) : -INF;
		$max = max($arr) != null ? max($arr) : INF;
		
		//echo $max." ".$min;
		
		$arr2 = array($min,$max,$value);
		if($value == max($arr2) && max($arr2) > $max) return false;
		if($value == min($arr2) && min($arr2) < $min) return false;
		return true;
	}
	
	/**
	 * 检查数字是否合规则
	 *
	 * @param number $value
	 * @param array $rule
	 * @return boolean
	 */
	public static function checkNumber($value,$rule){
		$type = $rule["type"];		
		if(($type == self::TYPE_NUMBER && !is_numeric($value)) || ($type == self::TYPE_FLOAT && !is_int($value)) || ($type==self::TYPE_FLOAT && !is_float($value))){
			return false;
		}else{
			return self::checkNumRange($value,$rule);
		}
	}
	
	public static function checkString($value,$rule){
		
		$min = $rule["min"];$max = $rule["max"];
		
		if($min == null) $min = 0;
		if($max == null) $max = INF;
		
		$arr = array($min,$max);
		$min = min($arr);
		$max = max($arr);
		
		if($min < 0 || $min == null || !is_numeric($min)) $min = 0;
		if($max < 0 || $max == null || !is_numeric($max)) $max = INF;
		
		if(( $rule["required"] == false && ( $value==null || $value == "")) || (strlen($value) >= $min && strlen($value) <= $max)) 
			return true;
		else 
			return false;
	}
}

?>
