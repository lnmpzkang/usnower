JObj.use("ui");
JObj.use("ChainDom");

if(typeof(JObj.Plugin) != "object") JObj.Plugin = {};

JObj.Plugin.JGrid = {};

(function($,$$,$$$){
	
	var JGrid = function(pWidth,pHeight,pBody){
		
		var _self = this;
		
		var body = $$$.$(pBody) || document.body;
		
		var objs = {
			outline		: null,
			topLeftCell : null,
			titleArea	: null,
			rowNumArea	: null,
			dataArea	: null
		};
		
		var tmps = {};
		
		var createOutline = function(){
			$$$.ChainDom.$c("DIV")
					.appendTo(body)
					.save("outline",objs)
					.setAttributes({id:"jgrid1"});
			
			
			$$$.ChainDom.$c("TABLE")
						.$appendTo(objs.outline)
						.saveTo("table",tmps)
						
		}
		
		_self.create = function(){
			createOutline();
		}
	}
	
	$.getInstance = function(){
		var grid = new JGrid();
		grid.create();
		return grid;
	}
	
})(JObj.Plugin.JGrid,JObj.Plugin,JObj);