JObj.ChainDom = {};
(function($,$$){
	
	/*
	. : className
	# : id
	无 : tagName
	> : 下级  
	, : 
	@ : name
	
	$(".aa,.bb") className 为 aa 和 bb 的
	$("#aa,.bb") id 为 aa 和 className 为 bb 的
	$("div>#aa") 所有 id 为 aa 的 div
	$(".aa>#bb") 所有 className 为 aa 的,并且 id 为 bb 的
	$("@href") 所有有 href 的
	$("
	*/
	
	var ChainDom = function(){
		
		var objs = {
			current : null
		};
		
		var _self = this;
		
		_self.save = function(varName,scope){
			scope = typeof(scope) != "object" ? window : scope;
			scope[varName] = objs.current;
			return _self;
		}		
		
		var get = function(parent,p){
			var flag = p.substring(0,1);
			key = p.split(">")[0].substr(1);
			switch(flag){
				case "."://className
					break;
				case "#"://id
					break;
				case "@"://name
					break;
				default://tagName
					
			}
		}
		
		_self.$ = function(p){
			
			if(typeof(p) == "object"){
				objs.current = p;
				return _self;
			}
			
			var arr1 = [],arr2=[],arr3=[];
			
			arr1 = p.split(",");
			var flag,key;
			for(var i=0;i<arr1.length;i++){
				
				
				
/*				arr2 = arr1[i].split(">");
				for(var j=0;j<arr2.length;j++){
					flag = arr2[j].substring(0,1);
					key = arr2[j].substr(1);
					switch(flag){
						case "."://className
							arr3.concat($$.$class($$.$(key)));
							break;
						case "#"://id
							arr3.push($$.$(key));
							break;
						case "@"://name
							arr3.concat($$.$name(key));
							break;
						default://tagName
							arr3.concat($$.$tag(arr2[j]));
					}
				}*/
			}
			
			objs.current = arr3;
			
			return _self;
		}
		
	}

	$.$ = function(p){
		var cdom = new ChainDom();
		cdom.$(p);
		return cdom;
	}

})(JObj.ChainDom,JObj);