<?php

class GFCKEditor {
	public function createForSmarty($params){
		$name = null;
		$width = null;
		$height = null;
		extract($params);
		$fck = new FCKeditor($name);
		$fck->Width = $width;
		$fck->Height = $height;
		$fck->Create();
	}
}

?>
