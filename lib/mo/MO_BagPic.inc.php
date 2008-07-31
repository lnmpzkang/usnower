<?php

class MO_BagPic extends MO {
	/**
	 * Enter description here...
	 *
	 * @param $_File $file
	 */
	public static function upload($bag,$files,$colors,$descs){
		$subDir = date("Ym");

		$im = new GImage();
		$im->setLogoSourceImg(PATH_ROOT_ABS."/".GConfig::BAG_WATER_MARK_FILE , GConfig::BAG_WATER_MARK_ALPHA );
		
		$up = new GUpload();
		$up->setAccept(array("jpg","gif","png"));
		$up->setMaxSize(1024);
		$up->setSaveDir(PATH_ROOT_ABS."/".GConfig::DIR_BAG_ORG."/".$subDir);
		$up->setAutoname(true);
		
		$vo = new VO_BagPic();
		$vo->setBag($bag);
		
		$i = 0;
		//for($i=0;$i<sizeof($files);$i++){
		foreach ($files as $file){
			if(false != ($path = $up->uploadFile($file))){
				$im->setSourceImg($path);

				$name = preg_replace("/(.+)(\\.+)(jpg|png|gif)+$/i","$1",basename($path));

				$im->markLogo(10,10);
				$im->setSaveDir(PATH_ROOT_ABS."/".GConfig::DIR_BAG_BIG."/".$subDir);
				$im->saveAsPng($name);
				
				$im->reset();
				$im->zoomByMaxSize(GConfig::BAG_SIZE_NORMAL_W,GConfig::BAG_SIZE_NORMAL_H);
				$im->setSaveDir(PATH_ROOT_ABS."/".GConfig::DIR_BAG_NORMAL."/".$subDir);
				$im->markLogo(10,10);
				$im->saveAsPng($name);

				$im->reset();
				$im->zoomByMaxSize(GConfig::BAG_SIZE_ICON_W , GConfig::BAG_SIZE_ICON_H );
				$im->setSaveDir(PATH_ROOT_ABS."/".GConfig::DIR_BAG_ICON."/".$subDir);
				$im->saveAsPng($name);
				
				$vo->setColor($colors[$i]);
				$vo->setDescription($descs[$i]);
				$vo->setBig(GConfig::DIR_BAG_BIG."/".$subDir."/".$name.".png");
				$vo->setNormal(GConfig::DIR_BAG_NORMAL."/".$subDir."/".$name.".png");
				$vo->setIcon(GConfig::DIR_BAG_ICON."/".$subDir."/".$name.".png");
				
				self::add($vo);
				$i++;
			}
		}
	}
	
	/**
	 * Enter description here...
	 *
	 * @param VO_BagPic $vo
	 */
	public static function add($vo){
		self::checkVO($vo,"VO_BagPic");
		$sql = sprintf("INSERT INTO %sBAG_PIC (BAG,COLOR,BIG,NORMAL,ICON,DESCRIPTION) VALUES (%d,'%s','%s','%s','%s','%s')",
												GConfig::DB_PREFIX,
												$vo->getBag(),
												$vo->getColor(),
												$vo->getBig(),
												$vo->getNormal(),
												$vo->getIcon(),
												$vo->getDescription()
												);
		GMysql::query($sql);
		return GMysql::getInsertId();
	}
}

?>
