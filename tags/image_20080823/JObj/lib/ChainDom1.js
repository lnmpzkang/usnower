JObj.ChainDom = {};
(function($,$$){
	
	/*
	. : className
	# : id
	无 : tagName
	> : 下级  
	, : 
	@ : 属性
	
	$(".aa,.bb") className 为 aa 和 bb 的
	$("#aa,.bb") id 为 aa 和 className 为 bb 的
	$("div>#aa") 所有 id 为 aa 的 div
	$(".aa>#bb") 所有 className 为 aa 的,并且 id 为 bb 的
	$("@href") 所有有 href 的
	$("
	*/
	
	var ChainDom = function(){
		
		var objs = {
			current;	
		};
		
		var _self = this;
		
		
		_self.$ = function(p){
			
			p.split(",")
			
			return _self;
		}
		
	}

	$.$ = function(p){
		var cdom = new ChainDom();
		cdom.$(p);
		return cdom;
	}

})(JObj.ChainDom,JObj);