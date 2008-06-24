(function($){
	
	$.trim = function(){
		return this.replace(/(^\s*)|(\s*$)/g, ""); 
	}
	
	$.isPercent = function(){
		return /^(\d*)(\.?)(\d*)%$/.test(this);
	}
	
})(String.prototype);

/*-------------------------------------------------------------------------------------------------*/

(function($){	
	var __alert__ = $.alert;
	
	$.alert = function(){
		var arg,s="";
		for(var i=0;arg = arguments[i];i++){
			s += arg + "\n"
		}
		__alert__(s);
	}
})(window);

/*-------------------------------------------------------------------------------------------------*/

var JObj = {};
(function($){
	
	if(!document.body)
		document.write("<body></body>");
	
	/*-------------------------------------------------------------------------------------------------*/
	
	//$.isObject = function(p){return p instanceof Object;}
	$.isObject = function(p){return "object" == typeof(p)}
	$.isFunction = function(p){return p instanceof Function;}
	$.toSource = function(obj){
		if(Object.toSource)
			return obj.toSource();
		else{
			var v = [],o;
			for(o in obj){
				if(o == "toSource")
					continue;
				if(typeof obj[o] == "object")
					v.push('"' + o + '":{}');
				else
					v.push('"' + o + '":' + obj[o]);
			}
			return "({" + v.join(",") + "})";				
		}
	}
	
	var _doFunction = function(fun,args){
		fun.apply(null,args);
	}
	
	$.doFunction = function(fun){
		var args = [];
		for(var i=1;arg = arguments[i];i++){
			args.push(arg);
		}
		return function(){fun.apply(null,args)};
		//return _doFunction(fun,args);
	}
	
	/*------------------------*/
	var LOADEDMODULE = {};
	
	$.getLoadedModules = function(){
		return eval($.toSource(LOADEDMODULE));
	}
	
	$.getLoadedModule = function(k){ return LOADEDMODULE[k]; }
	$.setLoadedModule = function(k,v){ LOADEDMODULE[k] = v }
	/*-------------------------------------------------------------------------------------------------*/		  
	
	$.Dom = {};
	(function($,$$){
		
		$$.$ = $.$ = function(p,doc){
			return $$.isObject(p) ? p : (doc || document).getElementById(p);
		}
		
		$$.$c = $.$c = function(tag){
			return document.createElement(tag);
		}
		
		$$.$tag = $.$tag = function(tag,node){
			return $$.$((undefined == node || null == node || "" == node.trim() ? document : node)).getElementsByTagName(tag);
		}
		
		$$.$name = $.$name = function(name,node){
			return $$.$((undefined == node || null == node || "" == node.trim() ? document : node)).getElementsByName(name);
		}
		
		$$.$class = $.$class = function(className){
			var objs = document.all || document.getElementsById("*");
			var o,i,arr = [];
			for(i=0;o=objs[i];i++){
				if(o.className == className){
					arr.push(o);
				}	
			}
			
			return arr;
		}
		
	})($.Dom,$);
	
	var scripts = $.$tag("SCRIPT"),script,i;
	for(i=0;script = scripts[i];i++){
		if((/JObj\.js$/i).test(script.src)){
			$.path = script.src.replace(/JObj\.js$/i,"");
			break;
		}	
	}
	delete scripts;
	delete script;
	
	/*-------------------------------------------------------------------------------------------------*/		  
		  
	$.Browser = {};
	(function($,$$){
		
		$.getFlashVersion = function(){
			var f="-1",n=navigator;
			if (n.plugins && n.plugins.length) {
				for (var ii=0;ii<n.plugins.length;ii++) {
					if (n.plugins[ii].name.indexOf('Shockwave Flash')!=-1) {
						f=n.plugins[ii].description.split('Shockwave Flash ')[1];
						break;
					}
				}
			} else if (window.ActiveXObject) {
				for (var ii=10;ii>=2;ii--) {
					try {
						var fl=eval("new ActiveXObject('ShockwaveFlash.ShockwaveFlash."+ii+"');");
						if (fl) { f=ii + '.0'; break; }
					}catch(e) {}
				}
			}
		
			if(f == "-1") return f;
			else return f.substring(0,f.indexOf(".")+2)			
		}		
		
		var n_ = navigator;
		
		var b = n_.appName;
		var ua = n_.userAgent.toLowerCase();
		
		$.name = "Unknow";
		$.safari = ua.indexOf("safari")>-1;  // always check for safari & opera
		$.opera = ua.indexOf("opera")>-1;    // before ns or ie
		$.firefox = ua.indexOf('firefox')>-1; // check for gecko engine	
		$.netscape = !$.firefox && !$.opera && !$.safari && (b=="Netscape");
		$.ie = !$.opera && (b=="Microsoft Internet Explorer");
		
		$.name = ($.ie ? "IE" : ($.firefox ? "Firefox" : ($.netscape? "Netscape" : ($.opera ? "Opera" : ($.safari ? "Safari" : "Unknow")))));
		
		switch($.name){
			case "Opera":
				$.fullVersion = ua.substr(ua.indexOf("opera") + 6);
				break;
			case "IE":
				$.fullVersion = ua.substr(ua.indexOf("msie") + 5).split(";")[0];
				break;
			case "Firefox":
				$.fullVersion = ua.substr(ua.indexOf("firefox") + 8);
				break;
			case "Safari":
				$.fullVersion = ua.substr(ua.indexOf("version") + 8).split(" ")[0];
				break;
			case "Netscape":
				$.fullVersion = ua.substr(ua.indexOf("netscape") + 9);
				break;
			default:
				$.fullVersion = "-1";
		}
		$.version = parseFloat($.fullVersion);
		
		$.cookieEnabled = n_.cookieEnabled;
		$.javaEnabled = n_.javaEnabled();
		
		
		$$.setLoadedModule("JObj.Browser",true);
	})($.Browser,$);
	
	/*-------------------------------------------------------------------------------------------------*/
	
	$.Xml = {};
	(function($,$$){
			  
		var vars = {
				ACTIVEXOBJECT_XMLHTTP : null
			};
			  
		if(!$$.Browser.ie){
			Element.prototype.__defineGetter__("xml",function(){
														return (new XMLSerializer).serializeToString(this);
													});
		}
		
		$.getXMLHttp = function(){
			var xmlHttp = null;
			//if($$.Browser.ie && $$.Browser.version < 7){ //用IE7内置的 XMLHttpRequest 对象，不能加载本地文件．
			if($$.Browser.ie){
				var v = ['MSXML2.XMLHTTP.8.0', 'MSXML2.XMLHTTP.7.0', 'MSXML2.XMLHTTP.6.0', 'MSXML2.XMLHTTP.5.0', 'MSXML2.XMLHTTP.4.0', 'MSXML2.XMLHTTP.3.0', 'MSXML2.XMLHTTP.2.6', 'MSXML2.XMLHTTP', 'Microsoft.XMLHTTP'];
				if(typeof(vars.ACTIVEXOBJECT_XMLHTTP) == "string")
					v[0] = vars.ACTIVEXOBJECT_XMLHTTP;
				
				var v_;
				for(var i=0;v_ = v[i];i++){
					try{
						xmlHttp = new ActiveXObject(v_);
						vars.ACTIVEXOBJECT_XMLHTTP = v_;
						break;
					}catch(e){}
				}
			}else{
				xmlHttp = new XMLHttpRequest();
			}
			
			if(xmlHttp == null){
				alert("你的系统不支持AJAX");
			}
			return xmlHttp;
		}
		
		
		$.getXMLDoc = function(){
			//DOMParser document.implementation
			if($$.Browser.ie){
				var doc = null;
				var v = ["Msxml2.DOMDocument.6.0","Msxml2.DOMDocument.5.0","Msxml2.DOMDocument.4.0","Msxml2.DOMDocument.3.0","MSXML2.DOMDocument"];
				if(typeof(vars.ACTIVEXOBJECT_DOMDOCUMENT) == "string")
					v[0] = vars.ACTIVEXOBJECT_DOMDOCUMENT;
				
				var v_;
				for(var i=0;v_ = v[i];i++){
					try{
						doc = new ActiveXObject(v_);
						vars.ACTIVEXOBJECT_DOMDOCUMENT = v_;
						break;
					}catch(e){}
				}
			}else if(document.implementation && document.implementation.createDocument){
				doc = document.implementation.createDocument("","doc",null)
			}
			
			return doc;
		}
		
		$.loadXML = $.parseXML = function(source){
			var doc;
			if(window.DOMParser){
				var parser = new DOMParser();
				doc = parser.parseFromString(source,"text/xml");
			}else{
				doc = $.getXMLDoc();
				doc.loadXML(source);
			}
			return doc;
		}
		
		$.getNodeAtt = function(pNode,pAtt){
			try{
				return pNode.attributes.getNamedItem(pAtt).nodeValue;
			}catch(e){
				//alert("前台调试错误：\n"+e.message+"\n当前节点不存在: "+pAtt+"这个属性");
			}
		}
		
		$.extractNodes = function(pNode){
			if(pNode.nodeType == 3)
				return null;
			var node,nodes = new Array();
			for(var i=0;node= pNode.childNodes[i];i++){
				if(node.nodeType == 1 || node.nodeType == 4)
					nodes.push(node);
			}
			return nodes;
		}
		
		$.getXML = $.serialize = function(pNode){
			if(!pNode) return null;
			if(pNode.xml)
				return pNode.xml;
			else if(window.XMLSerializer)
				return (new XMLSerializer()).serializeToString(pNode);
		}

		
		$$.setLoadedModule("JObj.Xml",true);
	})($.Xml,$);
	
	
	/*----------------------------------------------------------------------------------
	Ajax
	JObj.Ajax
	----------------------------------------------------------------------------------*/
	$.Ajax = {};
	(function($,$$){
		
		// AjaxObj
		var AjaxObj = function(rule,dataRule){
			var $ = this;
			
			var prepareData = function(){
				if(dataRule == null) return null;
				
				var o;
				var s = "";
				for(o in dataRule){
					s += encodeURIComponent(o) + "=" + encodeURIComponent(dataRule[o]) + "&";
					//必须要： encodeURIComponent,否则，post 不成功！
				}

				if($.method.toUpperCase() == "GET"){
					$.url += ($.url.indexOf("?") > -1 ? "&" : "?") + s;
					return null;
				}else
					return s;
			}			
			
			var ready = function(){
				$.url		= rule.url;
				$.method 	= rule.method || "GET";
				$.async		= rule.async || false;
				$.data		= prepareData();
				$.onSuccess	= rule.onSuccess;
				$.onUnsuccess= rule.onUnsuccess;
				$.onReady	= rule.onReady;			
			}	
			
			$.xmlHttp = $$.Xml.getXMLHttp();
			$.xmlHttp.onreadystatechange = function(){
				
				var http = $.xmlHttp;
				
				if(http.readyState == 4){
					switch(http.status){
						case 0:
						case 200:
							$$.isFunction($.onSuccess) ? $.onSuccess(http,200,rule,dataRule) : null;
							break;
						default :
							$$.isFunction($.onUnsuccess) ? $.onUnsuccess(http,http.status,rule,dataRule) : null;
					}
				}else
					$$.isFunction($.onWait) ? $.onWait(http,http.readyState,rule,dataRule) : null;					
			}
			
			$.send = function(){
				ready();
				$.xmlHttp.open($.method,$.url,$.async);
				$.xmlHttp.setRequestHeader( 'Content-type', 'application/x-www-form-urlencoded');
				$.xmlHttp.send($.data);
			}
		}
		
		$.send = function(rule,dataRule){
			var ajax = new AjaxObj(rule,dataRule);
			ajax.send();
		}		
		
		
		$$.setLoadedModule("JObj.Ajax",true);
	})($.Ajax,$)
	
	
	
	
	/*----------------------------------------------------------------------------------
	JObj.Loader
	----------------------------------------------------------------------------------*/
	
	$.Loader = {};
	(function($,$$){		
		/*
		QUEUE[url] = {
			beginTime:开始加载的时间,
			endTime:加载成功的时间,
			time:请求次数
		}
		*/
		
/*		if($$.Browser.ie)
			var fp = document.createDocumentFragment();
		else
			var fp = document.body;*/
			
		var fp = document.createDocumentFragment();
		
			
		var SUCCESS = {}; // 以载入，
		var FAILED = {}; // 加载出错
		var QUEUE = {}; // 队列，如果以载入，从 队列中删除，加入到 LOADED中
		
		$.getSuccess = function(){return eval($$.toSource(SUCCESS))};
		$.getFailed = function(){return eval($$.toSource(FAILED))};
		$.getQueue = function(){return eval($$.toSource(QUEUE))};
		
		$.getUrlHost = function(path){
			var a = document.createElement("A");
			a.href = path;
			return a.hostname;
		}
		
		$.getFullPath = function(path){			
			if(!$$.Browser.ie){
				var a = document.createElement("A");
				a.href = path;				
				return a.href;
			}else{
				// 不能用 appendChild(a), return a.href; 这样得到的 href 依然是 path
				var div = $$.$c("DIV");
				div.innerHTML = "<a href='" + path + "' />";
				//div.innerHTML   "<A href=\"http://blog/js/js.php\"></A>"
				return div.innerHTML.match(/href=\"(.*)\"/)[1];
			}
		}
		
		var script_onreadystatechange = function(script,path,callBack){
			// for ie,没有办法判断JS文件是否真的加载完成。
			if(script.readyState == "loaded"){
				SUCCESS[path] = QUEUE[path];
				SUCCESS[path].endTime = (new Date()).valueOf();
				delete QUEUE[path];
				$$.isFunction(callBack) ? callBack() : null;
			}
		}
		
		var script_onload = function(path,callBack){
			SUCCESS[path] = QUEUE[path];
			SUCCESS[path].endTime = (new Date()).valueOf();
			delete QUEUE[path];
			$$.isFunction(callBack) ? callBack() : null;
		}
		
		var script_onerror = function(path,callBack){
			FAILED[path] = QUEUE[path];
			FAILED[path].endTime = (new Date()).valueOf();
			delete QUEUE[path];
			$$.isFunction(callBack) ? callBack() : null;
		}
		
		var script_onSuccess = function(xmlHttp,status,rule,dataRule){
			SUCCESS[rule.url] = QUEUE[rule.url];
			SUCCESS[rule.url].endTime = (new Date()).valueOf();
			
			delete QUEUE[rule.url];
			
			window.eval(xmlHttp.responseText);
		}
		
		var script_onUnsuccess = function(xmlHttp,status,rule,dataRule){
			FAILED[rule.url] = QUEUE[rule.url];
			FAILED[rule.url].endTime = (new Date()).valueOf();
			delete QUEUE[rule.url];
			
			$$.isFunction(rule.unsuccessCallBack) ? rule.unsuccessCallBack() : null;
		}
		
		var script = function(path,async){
			if(async === null) async = false;// 默认为同步加载
			var p = $.getFullPath(path);
			var h = location.host != "" && location.host == $.getUrlHost( p );
			
			QUEUE[p] = {
				beginTime:(new Date().valueOf()),
				endTime:null,
				time:1
			};
			
			if(location.protocol == "file:" || h){
				$$.Ajax.send({
							 	async:async, // true 异步加载，false 同步加载
								method:"GET",
								url:p,
								
								onSuccess:script_onSuccess,
								onUnsuccess:script_onUnsuccess
							 });
			}else{
				var script = $$.$c("SCRIPT");
				script.src = p;
				
				if($$.Browser.ie){
					script.onreadystatechange = $$.doFunction(script_onreadystatechange,p,succCallBack);	
					//script.onreadystatechange = function(){script_onreadystatechange(script,p,succCallBack);};
				}else{
					script.onload = $$.doFunction(script_onload,p,succCallBack);
					script.onerror = $$.doFunction(script_onerror,p,unsuccCallBack);
				}

				fp.appendChild(script);
			}
		}
		
		$.include = function(path,succCallBack,unsuccCallBack,async){
			script(path,async);
		}
		
		$.includeOnce = function(path,succCallBack,unsuccCallBack,async){
			var p = $.getFullPath(path);
			if(p in QUEUE || p in SUCCESS) return;
			script(path,async);
		}
		
		$$.setLoadedModule("JObj.Loader",true);
		
	})($.Loader,$);
	
	$.plugin = function(pluginName,async){
		async = (async == undefined || async == null || async.toString() != true || async.toString() != false) ? async : false;
		$.Loader.includeOnce($.path + "plugins/" + pluginName + "/" + pluginName + ".js",null,null,async);
	}
	
	$.use = function(lib,async){
		async = (async == undefined || async == null || async.toString() != true || async.toString() != false) ? async : false;
		$.Loader.includeOnce($.path + "lib/" + lib + ".js",async);
	}
})(JObj)