JObj.ChainDom = {};
(function($,$$){
	
	var objs = {
		current : null	
	};
	
	$.save = function(varName,scope){
		scope = typeof(scope) != "object" ? window : scope;
		scope[varName] = objs.current;
		return $;
	}
	
	//doc 可为 null
	$.$ = function(p,doc){
		objs.current = $$.$(p,doc);
		return $;
	}
	
	$.$c = function(tag){
		objs.current = $$.$c(tag);
		return $;
	}
	
	//node 可为 null
	$.$name = function(name,node){
		objs.current = $$.$name(name,node);
		return $;
	}
	
	$.$tag = function(tag,node){
		objs.current = $$.$tag(tag,node);
		return $;
	}
	
	$.appendTo = function(p){
		var parent = $$.$(p);
		if($$.Browser.ie){
			var last = document.all.item(document.all.length - 2);
			if(last.outerHTML.replace(last.innerHTML,"") == "<\/" + last.tagName + ">")
				parent.appendChild(objs.current);
			else{
				parent.insertBefore(objs.current,parent.lastChild);
			}	
		}
		return $;
	}
	
	$.setAttributes = function(rules){
		var rule;
		for(rule in rules){
			objs.current.setAttribute(rule,rules[rule]);
		}
		
		return $;
	}

})(JObj.ChainDom,JObj);