<?php

class MO {
	/**
	 * 检查输入的VO的类型
	 *
	 * @param mixed $vo
	 * @param string $className
	 */
	protected static function checkVO($vo,$className){
		if(get_class($vo) != $className)
			throw new GAppException("Invalid Parameter VO .Except $className");
	}
}

?>
