JObj.use("ui");

JObj.UI.JDrag = {};

/*
2147483647
模拟拖动时，最大的问题是当当前有内容被选中，这时的模拟就会变成真的拖动，所以，要把选中的内容取消。
*/

//=======================================================================================

(function($){
	var mAWidth = 200;
	var mAHeight = 200;

	var lastObj = null;

	var mArea = JObj.$c("DIV");

	if(!document.body){
		document.write("<body></body>");
	}

	document.body.appendChild(mArea);
	//有颜色和无颜色的运行表现是不一样的！为了看见鼠标下面的东东，设置透明度为0
	mArea.style.cssText = "width:" + mAWidth + "px;height:" + mAHeight + "px;position:absolute;background-color:#000;filter: Alpha(Opacity=0);-moz-opacity:0;opacity: 0;display:none;z-index:2147483647;display:none;";

	var x,y;

	var changeZIndex = function(obj){
		if(lastObj){
            JObj.$(lastObj).style.zIndex = 2147483645;
			JObj.$(obj).style.zIndex = 2147483646;
		}
		lastObj = obj;
	}

	var mArea_mouseup = function(){
		with(mArea.style){
			width = "1px";
			height = "1px";
			display = "none";
		}
	}

	var mArea_mousemove = function(obj){
		var evt = JObj.getEvent();
		var oPos = JObj.UI.JPos.getAbsPos(obj);
		var mPos = JObj.UI.JPos.getEventPos(evt);

		mArea.style.left = mPos.x - mAWidth / 2 + "px";
		mArea.style.top = mPos.y - mAHeight / 2 + "px";

		obj.style.left = oPos.x + mPos.x - x + "px";
		obj.style.top = oPos.y + mPos.y - y + "px";

		x = mPos.x;
		y = mPos.y;
	}


	var dragArea_mousedown = function(obj,dragArea){
        try{
			if(document.selection){//IE ,Opera
				if(document.selection.empty)
					document.selection.empty();//IE
				else{//Opera
					document.selection = null;
				}
			}else if(window.getSelection){//FF,Safari
				window.getSelection().removeAllRanges();
			}
		}catch(e){}

		var evt = JObj.getEvent();
		var mPos = JObj.UI.JPos.getEventPos(evt);

		x = mPos.x;
		y = mPos.y;

		with(mArea.style){
			width = mAWidth + "px";
			height = mAHeight + "px";
			left = mPos.x - mAWidth / 2 + "px";
			top = mPos.y - mAHeight / 2 + "px";
			cursor = "move";
			display = "";
		}

		mArea.onmousemove = JObj.doFunction(mArea_mousemove,obj,dragArea);
		mArea.onmouseout = mArea.onmouseup = mArea_mouseup;
	}    

    $.setDrag = function(obj){
        obj = JObj.$(obj);
        obj.style.position = "absolute";

		var dragArea = JObj.$(arguments[1]) || obj;

		if(arguments[2] !== false){
			//dragArea.onmousedown = JObj.doFunction(dragArea_mousedown,obj,dragArea);
			//obj.onmousedown = JObj.doFunction(changeZIndex,obj);

            JObj.addEvent(dragArea,'mousedown',JObj.doFunction(dragArea_mousedown,obj,dragArea));
            JObj.addEvent(obj,'mousedown',JObj.doFunction(changeZIndex,obj));
        }else{
			dragArea.onmousedown = "";
			obj.onmousedown = "";
		}
	}
    
})(JObj.UI.JDrag)