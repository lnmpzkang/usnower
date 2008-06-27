<?php
/**
 * GTpl
 *  {:key}		: 程序执行时就进行关键字替换
 *  {@fun()} 	: 执行时替换为：<?php echo fun() ?>
 *
 */
class GTpl {
	private $debugMode = true;

	private $baseDir = "";
	private $logFile = "";

	private $tpl = array();
	/*
	$tpl[] = array(
	name => "header",
	file => "header.html",
	source => "...",//原始信息。
	content	=> "..."//解析后的内容
	)
	*/
	private $key = array();		//键
	private $block = array();	//块，
	//private $cond = array();	//条件，只能在block中，如果条件没有名称，则为默认
	/*
	关系：
	tpl	->	key
	tpl	->	block	->	cond
	*/
	
	/**
	 * 构造函数,默认没有参数;可接受两个参数，第一个是模板地址，第二个是日志文件。
	 *
	 * @param string $tplDir 模板文件的目录,以BASEDIR为基础。默认为空
	 * @param string $logFile 日志文件，以BASEDIR为基础。默认为空
	 * @return void;
	 */
	public function __construct($baseDir = "",$logFile = "tplLog.log"){
		$this->baseDir = $baseDir;
		$this->logFile = $logFile;
	}

	/*
	example:
	$name = array(
	"header" => "header.html",
	"footer" => "footer.html"
	);
	*/
	/**
	 * 载入模板文件
	 *
	 * @param unknown_type $name
	 * @example $tpl->load(array("header"=>"header.html","footer"=>"footer.html"));
	 * @example $tpl->load("header","header.html");
	 */
	public function load($name){
		if(is_array($name)){
			while (list($k,$v) = each($name)) {
				$this->load($k,$v);
			}

			return;
		}
		
		$name = strtoupper($name);//转换为大写
		
		if(isset($this->tpl[$name])){
			self::log("重复的模板名称 : $name");
		}
		
		$tplFile = func_get_arg(1);
		$file = $this->baseDir.DIRECTORY_SEPARATOR.$tplFile;
		$source = "";

		if(!is_file( $file )){
			self::log("在文件夹 ： $this->baseDir 中，找不到文件 ： $tplFile");
			return;
		}else{
			//$source = join("",@file($file));
			$source = file_get_contents($file);
		}


		$this->tpl[$name] = array(
			"name"=>$name,
			"file"=>$file,
			"source"=>$source,//原始信息
			"content"=>$source//解析后的内容，这里是初始化
		);		
		
		$this->extractBlock($this->tpl[$name]);
	}
	
	protected function extractBlock($tpl){
		
		/*
		$reg = "/<tpl(.*)>(.*)<\/tpl>/iUs";
		$s = preg_match_all($reg,"<tpl aa>aaaa<tpl>fffff</tpl></tpl>dddddddddd<tpl bb>bbbb</tpl>eeeeeeee<tpl cc>cccc</tpl>",$su);

		var_dump($su);
		*/		
		
		$blockReg = "~<!--block(.*)-->(.*)<!--/block-->~iUs";
		$ma = array();
		if( preg_match_all( $blockReg, $tpl["source"] ,$ma ) ){
			while (list($k,$v) = each($ma[1])) {
				
				$blockName = strtoupper( trim($v) );	//以完全被转换成大写了！

				if (isset($this->block[$blockName])) {
					self::log("以载入的模板中，有重复的块名称 : $blockName");
				}

				$this->block[$blockName] = array(
					"name"			=>$blockName,
					"parentName" 	=> $tpl["name"],
					"file"			=> $tpl["file"],
					"source"		=> $ma[2][$k],//原始信息
					"content"		=> "",	//解析后的内容，初始化为空
					"default"		=> "",	//块的默认内容，即没有名字的条件 cond
					"cond"			=> array()
				);
				
				$this->extractCond($this->block[$blockName]);
			}
		}
				
	}
	
	protected function extractCond(&$block){
		$condReg = "~<!--cond(.*)-->(.*)<!--/cond-->~iUs";
		$ma = array();
		if(preg_match_all($condReg,$block["source"],$ma)){
			while (list($k,$v) = each($ma[1])) {
				$condName = strtoupper( trim($v));
				if($condName == ""){//默认，即没有条件名，
					$block["default"] = $ma[2][$k];
					//echo $ma[2][$k];
				}else if(isset($block["cond"][$condName])){
					self::log("在块：".$block["name"]."中，重在重复的条件：$condName");
				}else{
					$block["cond"][$condName] = array(
						"name"		=>	$condName,
						"source"	=> $ma[2][$k]
					);
				}
			}
		}
	}
	
	/**
	 * 给关键字赋值
	 *
	 * @param unknown_type $key
	 * @example assign("name","nickr");
	 * @example assign(array("name"=>"nickr","sex"=>"Male"));
	 */
	public function assign($key){
		if(is_array($key)){
			while(list($k,$v) = each($key)){
				$this->assign($k,$v);
			}
			return;
		}
		
		$key = strtoupper($key);//转为大写
		
		$this->key[strtoupper($key)] = func_get_arg(1);
	}
	
	
	/**
	 * 解析 块，如果没有条件块名（condName1,condName2）,将不显示条件块的内容。
	 * parseBlock("blockName","condName1","condName2",...)
	 * 
	 * @param string $name 块名称.
	 * @param string condName1,条件块名1，可选。
	*/
	public function parseBlock($name){
		
		$name = strtoupper($name);
		
		if(!isset($this->block[$name])){
			self::log("没有找到块： $name 请确认该块是否存在于以载入的模板文件中!");
			return;
		}
		
		$source = $this->block[$name]["source"];
		
		if (func_num_args() > 1) {
			$conds = func_get_args();
			array_shift($conds);////第一个参数不是条件名，要删除。
			foreach ($conds as $cond){
				if(!isset($this->block[$name]["cond"])){
					self::log("在块：$name 中，没有发现条件：$cond ，请确认。");
					continue;
				}
				
				$condReg = "~<!--cond(\\s*)$cond-->(.*)<!--/cond-->~iUs";
				$source = preg_replace($condReg,"$2",$source);//删除以匹配的条件的外壳。
			}
		}
		
		//echo $source."\n===============================\n";
		$condReg = "~<!--cond(.*)-->(.*)<!--/cond-->~iUs";
		$source = preg_replace($condReg,"",$source);//删除所有没有匹配的条件。		
		
		$reg = array();
		$rep = array();
		reset($this->key);
		while (list($k,$v) = each($this->key)) {
			$reg[] = "/{:$k}/iUs";
			$rep[] = "$v";
		}
		
		$this->block[$name]["content"] .= preg_replace($reg,$rep,$source);
	}

	
	/**
	 * 替换模板中，指定的内容，
	 * 使用同 preg_replace
	 * $search $replace 接受数组
	 * 该函数应在 load 之后 parse 之前使用。
	 *
	 * @param string $name 模板名
	 * @param mixed $search 要替换的内容的正则表达式
	 * @param mixed $replace 要替换成的内容
	 */
	public function replaceSpcifyContent($name,$search,$replace = ""){
		$name = strtoupper($name);
		$this->tpl[$name]["content"] = preg_replace($search,$replace,$this->tpl[$name]["content"]);
	}
	
	
	private function parsePhpCall($match){
		$fun = substr($match[1],0,strpos($match[1],"("));
		if(function_exists($fun) || is_callable($fun)){
			return "<?php echo $match[1] ?>";
		}else{
			return $match[0];
		}
	}
	
	/**
	 * 解析模板,返回解析，填充关键字后的内容。
	 *
	 * @param string $name 模板名
	 * @param boolean $deleteEmptyLine 是否删除空白行,默认删除。
	 * @return string
	 */
	public function parse($name,$deleteEmptyLine = true){
		
		$name = strtoupper($name);//名字大小写统一，所以不区分大小写。
		
		if(!isset($this->tpl[$name])){
			self::log("没有找到模板: $name ，请确认是否载入!");
			return "";
		}

		/*替换block*/
		$blockReg = "~<!--block(.*)-->(.*)<!--/block-->~iUs";
		$ma = array();
		if(preg_match_all( $blockReg ,  $this->tpl[$name]["source"]  , $ma)){//从source中查找block
			//$ma[1]中存放所有block的名称
			//var_dump($ma[1]);
			while (list($k,$v) = each($ma[1])) {
				$blockName = strtoupper( trim($v) );
				
				if (isset($this->block[$blockName])) {
					
					
					$blockContent = $this->block[$blockName]["content"];
					
					if($deleteEmptyLine)
						$blockContent = trim($blockContent);
					
					if ($blockContent == "") {//注意，没有trim
						$blockContent = $this->block[$blockName]["default"];
					}
					
					//如果block的内容为空，就用block的default值代替，default为空，那就去球！
					
					$this->tpl[$name]["content"] = str_ireplace(// str_ireplace ....
														$ma[0][$k],////////////
														$blockContent,
														$this->tpl[$name]["content"]);
				}
			}
		}

		$reg = array();
		$rep = array();

		reset($this->key);
		while (list($k,$v) = each($this->key)) {
			$reg[] = "/{:$k}/iUs";
			$rep[] = "$v";
		}
		
		$this->tpl[$name]["content"] = preg_replace( $reg, $rep, $this->tpl[$name]["content"]);
		
		//-------------------------------------------------------------------------------------------------------------------------------------
		$reg = "/{@([^{}]*)}/iUs";
		$this->tpl[$name]["content"] = preg_replace_callback($reg,array(self,"parsePhpCall"),$this->tpl[$name]["content"]);
		//-------------------------------------------------------------------------------------------------------------------------------------
				
		if ($deleteEmptyLine) {
			//return preg_replace("/(\\s*)\r/iUs","",$this->tpl[$name]["content"]);
			return eval(" ?>".preg_replace("/(\\s*)\r/iUs","",$this->tpl[$name]["content"])."<?php ");
		}else{
			//return $this->tpl[$name]["content"];
			return eval(" ?>".$this->tpl[$name]["content"]."<?php ");
		}
	}		
	
	protected function log($msg){
		GLoger::logToFile($msg.SYMBOL_NEWLINE.__FILE__,$this->logFile);
	}
}
?>