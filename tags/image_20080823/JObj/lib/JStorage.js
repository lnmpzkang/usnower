JObj.JStorage = {};

(function($){
	
	var support = true;
    var storage = null;

    if(JObj.Browser.ie && JObj.Browser.version >= 5){
		storage = JObj.$c("JObjStorage");
		document.documentElement.insertBefore(storage,document.body);
		storage.addBehavior("#default#userData");
	}else if(window.globalStorage){	
        storage = window.globalStorage[location.hostname];		     
	}else{
		support = false;
	}
	
	$.getSupport = function(){
		return support;
	}
	
	$.save = function(name){
		if(JObj.Browser.ie)
			storage.save(name);
	}

	$.load = function(name){
		if(JObj.Browser.ie)
            storage.load(name);
    }
	
	$.setAttribute = function(k,v){
        JObj.Browser.ie ? storage.setAttribute(k,v) : storage.setItem(k,v);
    }
	
	$.getAttribute = function(k){
		return JObj.Browser.ie ? storage.getAttribute(k) : storage.getItem(k);	
	}
	
	$.removeAttribute = function(k){
		JObj.Browser.ie ? storage.removeAttribute(k) : storage.removeItem(k);
	}
	
	$.setExpires = function(){
			
	}
})(JObj.JStorage);