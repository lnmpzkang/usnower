JObj.UI = {};
(function($,$$){
	
	$.JPos = {};
	(function($){
	
		$.getAbsPos = function(pTarget){
			var x_ = y_ = 0;
	
			if(pTarget.style.position != "absolute"){
				while(pTarget.offsetParent){
						x_ += pTarget.offsetLeft;
						y_ += pTarget.offsetTop;
						pTarget = pTarget.offsetParent;
				}
			}
				x_ += pTarget.offsetLeft;
				y_ += pTarget.offsetTop;
			return {x:x_,y:y_};
		}
	
		$.getEventPos = function(evt){
				var _x,_y;
				evt = JObj.getEvent(evt);
				if(evt.pageX || evt.pageY){
					_x = evt.pageX;
					_y = evt.pageY;
				}else if(evt.clientX || evt.clientY){
					_x = evt.clientX + (document.body.scrollLeft || document.documentElement.scrollLeft) - (document.body.clientLeft || document.documentElement.clientLeft);
					_y = evt.clientY + (document.body.scrollTop || document.documentElement.scrollTop) - (document.body.clientTop || document.documentElement.clientTop);
				}else{
					return $.getAbsPos(evt.target);
				}
				return {x:_x,y:_y};
		}
	
	})($.JPos)	
	
})(JObj.UI,JObj);