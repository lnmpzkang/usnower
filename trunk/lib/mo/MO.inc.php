<?php

class MO {
	protected static function checkVO($vo,$className){
		if(get_class($vo) != $className)
			throw new GAppException("Invalid Parameter VO .Except $className");
	}
}

?>
