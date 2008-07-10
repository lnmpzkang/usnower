/*=================================================
程序名：JMenuTab(所谓的滑动门)
作者：xling
Blog:http://xling.blueidea.com
日期：2007/05/23

2007/05/25
把24日加入的自定义事件：onTabChange完善了一下，加入两个参数。
onTabChange(oldTab,self.activedTab);
oldTab:上次点击的那个tab
newTab:本次点击的tab
tab有三个属性：
index:
label:
tabPage:那addTab方法中的第二个参数。

加入方法:setSkin(pSkinName)
pSkinName是CSS文件中的。
示例：
#JMenuTabBlue {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 12px;
	padding: 2px;
	border-right-width: 1px;
	border-bottom-width: 1px;
	border-left-width: 1px;
}
#JMenuTabBlue .oInnerline {
	background-color: #FFFFFF;
}
...
...
要想使用这个skin,要先引用这个css文件，然后：
setSkin("JMenuTabBlue");
具体见示例文件：Demo2.htm


2007/05/28
加入功能：设定最小高度
setFixHeight = function(pHeight,pAutoExpend,pXScroll,pYScroll)
第一个参数为数值。其余参数为boolean值，可不写(解析为false)。
如果pAutoExpend值为true，就是自动扩展，即内容高度大于设定的高度，自动扩展高度。
pXScroll:如果超出，是否显示横向滚动条。
pYScroll:垂直滚动条，同上。


2007/08/21
修正在FF,Opera,Safari下的表现不一致的若干问题。

2007年8月底
加入：setDefaultPage，目的是展示一个不带标签页的普通面板。详情及效果请见压缩包中的示例。

2007/09/03
加入onmouseover响应。如压缩包里的demo5的示例。
===================================================*/

function Browser(){
	var d_ = document,n_ = navigator,t_ = this,s_= screen;
	
    var b = n_.appName;
    var ua = n_.userAgent.toLowerCase();
    
	t_.name = "Unknow";
	
	t_.safari = ua.indexOf("safari")>-1;  // always check for safari & opera
    t_.opera = ua.indexOf("opera")>-1;    // before ns or ie
	t_.firefox = !t_.safari && ua.indexOf('firefox')>-1; // check for gecko engine	
    t_.ns = !t_.firefox && !t_.opera && !t_.safari && (b=="Netscape");
    t_.ie = !t_.opera && (b=="Microsoft Internet Explorer");
	
	t_.name = (t_.ie ? "IE" : (t_.firefox ? "Firefox" : (t_.ns ? "Netscape" : (t_.opera ? "Opera" : (t_.safari ? "Safari" : "Unknow")))));
}

function JMenuTab(pWidth,pHeight,pBody){
	var self = this;
	var brw = new Browser();
	
	//________________________________________________
	var width = pWidth || "100%";
	var height = pHeight;
	
	this.titleHeight = 24;
	//________________________________________________
	var oOutline = null;
	var oTitleOutline = null;
	var oPageOutline = null;
	var oTitleArea = null;
	var oTitleAreaOuter = null;
	var oTitleAreaInner = null;
	var oPageAreaOutline = null;
	var oPageArea = null;
	var oPageAreaMinHeight = 15;////////////////
	
	var tabArray = new Array();
	this.activedTab = null;
	//________________________________________________
	this.onTabChange = new Function();
	this.onmouseover = new Function();
	//________________________________________________
	
	var $ = function(pObjId){
		if(typeof pObjId == "object") return pObjId;
		return document.getElementById(pObjId);	
	}
	
	//________________________________________________
	
	var body = $(pBody) || document.body;
	
	//________________________________________________
	
	var htmlObject = function(pTagName){
		return document.createElement(pTagName);
	}
	//________________________________________________	
	
	var isRate = function(pRateString){
		if(!isNaN(pRateString)) return false;
		if(pRateString.substr(pRateString.length-1,1) != "%")
			return false;
		if(isNaN(pRateString.substring(0,pRateString.length - 1)))
			return false;
		return true;
	}	
	
	//________________________________________________
	
	var createOutline = function(){
		
		var width_ = isRate(width) ? width : (!isNaN(width) ? width + "px" : "100%");
		
		oOutline = htmlObject("DIV");
		body.appendChild(oOutline);
		oOutline.style.width = width_;
		
		oOutline.onmouseover = function(evt){
			evt = evt || window.event;
			var obj = evt.srcElement || evt.target;
			
			try{
				if(obj.className == "oTabBtnCenter"){
					self.onmouseover(obj.parentNode);
				}
			}catch(e){
				//	
			}
		}
	}
	
	//________________________________________________
	
	/*这个方法是为了解决外观问题，比如：
	setClassId("JMenuTab");
	在CSS里就要这样写：
	#JMenuTab {...}
	#JMenuTab .oTitleHeight{...}
	*/
	this.setSkin = function(pSkin){
		oOutline.id = pSkin;
	}
	//________________________________________________	
	
	var moveBtnRight_mouseover = function(){
		this.className = "oMoveRightBtnActive";	
	}
	
	var moveBtnRight_mouseout = function(){
		this.className = "oMoveRightBtnNormal";	
	}
	
	var moveBtnRight_click = function(){
		self.setActiveTab(getNext(self.activedTab.index));
	}
	
	var moveBtnLeft_mouseover = function(){
		this.className = "oMoveLeftBtnActive";	
	}
	
	var moveBtnLeft_mouseout = function(){
		this.className = "oMoveLeftBtnNormal";	
	}
	
	var moveBtnLeft_click = function(){
		self.setActiveTab(getPrev(self.activedTab.index));
	}	
	
	var createTitleOutline = function(){
		oTitleOutline = htmlObject("DIV");
		oOutline.appendChild(oTitleOutline);
		oTitleOutline.className = "oTitleOutline";
		
		var vTable = htmlObject("TABLE");
		oTitleOutline.appendChild(vTable);
		vTable.style.width = "100%";
		vTable.style.height = "100%";
		vTable.style.borderCollapse = "collapse";
		vTable.style.tableLayout="fixed";		
		vTable.border = 0;
		vTable.cellSpacing = 0;
		vTable.cellPadding = 0;
		
		var vTBody = htmlObject("TBODY");
		vTable.appendChild(vTBody);
		
		var vTr1 = htmlObject("TR");
		vTBody.appendChild(vTr1);
		
		var vTdTopLeft = htmlObject("TD");
		vTr1.appendChild(vTdTopLeft);
		vTdTopLeft.height = self.titleHeight;
		vTdTopLeft.className = "oTopLeft";
		
		oTitleArea = htmlObject("TD");/////////////////////////////////
		vTr1.appendChild(oTitleArea);
		vTr1.style.width = "auto";
		oTitleArea.className = "oTitleArea";
		
		oTitleAreaOuter = htmlObject("DIV");
		oTitleArea.appendChild(oTitleAreaOuter);
		oTitleAreaOuter.style.width = "100%";
		oTitleAreaOuter.style.overflow = "hidden";
		
		oTitleAreaInner = htmlObject("DIV");
		oTitleAreaOuter.appendChild(oTitleAreaInner);
		oTitleAreaInner.style.width = "10000px";
		
		var vTdMoveArea = htmlObject("TD");
		vTr1.appendChild(vTdMoveArea);
		vTdMoveArea.className = "oMoveBtnArea";
		
		var vMoveBtnLeft = htmlObject("DIV");
			vTdMoveArea.appendChild(vMoveBtnLeft);
			vMoveBtnLeft.className = "oMoveLeftBtnNormal";
			vMoveBtnLeft.onmouseover = moveBtnLeft_mouseover;
			vMoveBtnLeft.onmouseout = moveBtnLeft_mouseout;
			vMoveBtnLeft.onclick = moveBtnLeft_click;
		
		var vMoveBtnRight = htmlObject("DIV");
			vTdMoveArea.appendChild(vMoveBtnRight);
			vMoveBtnRight.className = "oMoveRightBtnNormal";
			vMoveBtnRight.onmouseover = moveBtnRight_mouseover;
			vMoveBtnRight.onmouseout = moveBtnRight_mouseout;
			vMoveBtnRight.onclick = moveBtnRight_click;
		
		var vTdTopRight = htmlObject("TD");
		vTr1.appendChild(vTdTopRight);
		vTdTopRight.className = "oTopRight";
	}
	
	//________________________________________________
	this.setTitleHeight = function(pHeight){
		//设置标题区域的高度
	}
	
	//________________________________________________
	
	var tabBtn_click = function(){
		self.setActiveTab(this.index);
	}
	
	var tabBtn_mouseover = function(){
		if(this.className =="oTabBtnActive")
			return;
		
		this.className = "oTabBtnHover";
	}
	
	var tabBtn_mouseout = function(){
		if(this.className =="oTabBtnActive")
			return;
		this.className = "oTabBtn";
	}	
	
	var closeBtn_mouseover = function(){
		this.className = "oCloseBtnActive";
	}
	
	var closeBtn_mouseout = function(){
		this.className = "oCloseBtnNormal";
	}
	
	var closeBtn_click = function(evt){
		evt = window.event || evt;
		evt.cancelBubble = true;
		self.closeTab(this.index)
	}
	
	this.closeTab = function(idx){
		if(!(idx in tabArray)) return ;
		
		var obj = tabArray[idx];
		var page = $(obj.parentNode.parentNode.tabPage);

		oTitleAreaInner.removeChild(obj);
		
		delete tabArray[idx];
		if(page) page.parentNode.removeChild(page);
		
		if(typeof self.onTabClosed == "function"){
			self.onTabClosed(idx);	
		}
		
		self.setActiveTab(getNextOrPrev(idx));
	}
	
	var scrollIntoView = function(obj){
		var parent = obj.parentNode.parentNode;
		var scrollLeft = parent.scrollLeft;
		var offsetLeft = obj.offsetLeft;
		var width = obj.clientWidth;
		var totalWidth = parent.clientWidth;
		
		var dis = scrollLeft - offsetLeft;
		if(dis > 0){
			parent.scrollLeft -= dis;
		}else if( -dis + width < totalWidth){
			// in view;
		}else{
			parent.scrollLeft += -dis + width - totalWidth;
		}
	}
	
	//________________________________________________
	
	var getNextOrPrev = function(idx){
		var next = getNext(idx);
		if(next) return next;
		else return getPrev(idx);
	}
	
	var getNext = function(idx){
		for(var i=idx + 1;i<tabArray.length;i++){
			if(i in tabArray)
				return i;
		}
		return 0;
	}
	
	var getPrev = function(idx){
		for(i=idx-1;i>=0;i--){
			if(i in tabArray)
				return i;
		}
		return 0;
	}
	
	var createCloseBtn = function(idx){
		var vBtn = htmlObject("DIV");
		vBtn.index = idx;
		vBtn.className = "oCloseBtnNormal";
		vBtn.onmousemove = closeBtn_mouseover;
		vBtn.onmouseout = closeBtn_mouseout;
		vBtn.onclick = closeBtn_click;
		return vBtn;
	}
	
	var createTabBtn = function(pLabel,pTabPage,pMouseAction,pCloseAble){
		var vTabBtn = htmlObject("DIV");
		oTitleAreaInner.appendChild(vTabBtn);
		vTabBtn.className = "oTabBtn";
		//////////////////////////////////
		vTabBtn.index = tabArray.length;
		vTabBtn.label = pLabel;
		vTabBtn.tabPage = pTabPage;
		//////////////////////////////////
		
		if(pMouseAction !== false){
			vTabBtn.onclick = tabBtn_click;
			vTabBtn.onmouseover = tabBtn_mouseover;
			vTabBtn.onmouseout = tabBtn_mouseout;
			
			tabArray.push(vTabBtn);
		}
		
		var vTabBtnL = htmlObject("DIV");
		vTabBtn.appendChild(vTabBtnL);
		vTabBtnL.className = "oTabBtnLeft";
		
		vTabBtnC = htmlObject("DIV");
		vTabBtn.appendChild(vTabBtnC);
		vTabBtnC.className = "oTabBtnCenter";
		vTabBtnC.innerHTML = pLabel;
		
		if(pCloseAble === true){
			var vCloseArea = htmlObject("DIV");
			vTabBtn.appendChild(vCloseArea);
			vCloseArea.className = "oTabBtnCenter";
			vCloseArea.appendChild(createCloseBtn(vTabBtn.index));
		}		
		
		vTabBtnR = htmlObject("DIV");
		vTabBtn.appendChild(vTabBtnR);
		vTabBtnR.className = "oTabBtnRight";
		
		return vTabBtn.index;
	}
	
	
	var createPageOutline = function(){
		oPageOutline = htmlObject("DIV");
		oOutline.appendChild(oPageOutline);
		oPageOutline.className = "oPageOutline";
		
		var vTable = htmlObject("TABLE");
		oPageOutline.appendChild(vTable);
		vTable.width = "100%";
		vTable.border = 0;
		vTable.cellSpacing = 0;
		vTable.cellPadding = 0;
		vTable.style.borderCollapse = "collapse";
		vTable.style.tableLayout="fixed";
		
		var vTBody = htmlObject("TBODY");
		vTable.appendChild(vTBody);
		
		var vTr1 = htmlObject("TR");
		vTBody.appendChild(vTr1);
		
		var vTdBottomLeft = htmlObject("TD");
		vTr1.appendChild(vTdBottomLeft);
		vTdBottomLeft.className = "oBottomLeft";
		vTdBottomLeft.rowSpan = 2;
		
		oPageAreaOutline = htmlObject("TD");///////////////////////////////////////
		vTr1.appendChild(oPageAreaOutline);
		oPageAreaOutline.className = "oPageAreaOutline";
		oPageAreaOutline.style.overflow = "hidden";
		if(oPageAreaOutline.filters)
			oPageAreaOutline.style.cssText = "FILTER: progid:DXImageTransform.Microsoft.Wipe(GradientSize=1.0,wipeStyle=0, motion='forward');";
		
		oPageArea = htmlObject("DIV");
		oPageAreaOutline.appendChild(oPageArea);
		oPageArea.className = "oPageArea";
		
		var vTdBottomRight = htmlObject("TD");
		vTr1.appendChild(vTdBottomRight);
		vTdBottomRight.className = "oBottomRight";
		vTdBottomRight.rowSpan = 2;
		
		var vTr2 = htmlObject("TR");
		vTBody.appendChild(vTr2);
		
		var vTdBottomCenter = htmlObject("TD");
		vTr2.appendChild(vTdBottomCenter);
		vTdBottomCenter.className = "oBottomCenter";
	}
	//________________________________________________
	this.setFixHeight = function(pHeight,pAutoExpend,pXScroll,pYScroll){
		oPageArea.style.width = "100%";
		if(pAutoExpend){
			oPageArea.style.minHeight = pHeight + "px";
		}else{
			oPageArea.style.height = pHeight + "px";
			oPageArea.style.overflowY = pYScroll === true ? "auto" : "hidden";
			oPageArea.style.overflowX = pXScroll === true ? "auto" : "hidden";
			
			if(pXScroll == undefined) oPageArea.style.overflowX = "auto"; 
			if(pYScroll == undefined) oPageArea.style.overflowY = "auto"; 
			
			if(brw.opera) oPageArea.style.overflow = "auto";
		}
	}
	//________________________________________________
	
	this.addTab = function (pLabel,pPageBodyId,pCloseAble){
		var i = createTabBtn(pLabel,pPageBodyId,true,pCloseAble);
		if($(pPageBodyId)){
			oPageArea.appendChild($(pPageBodyId));
			$(pPageBodyId).style.display = "none";
		}
		
		return i;
	}
		
	//________________________________________________
	
	this.setDefaultPage = function(pPageBodyId){
		createTabBtn("",pPageBodyId,false);
		oPageArea.appendChild($(pPageBodyId));
		$(pPageBodyId).style.display = "";
	}
	
	//________________________________________________
	
	this.setActiveTab = function(pIndex){
		
		if(!(pIndex in tabArray)) return;
		
		if(oPageAreaOutline.filters)
			oPageAreaOutline.filters[0].apply();
		
		if(self.activedTab != null){
			self.activedTab.className = "oTabBtn";
			if($(self.activedTab.tabPage))
				$(self.activedTab.tabPage).style.display = "none";
		}
		
		var oldTab = self.activedTab;
		self.activedTab = tabArray[pIndex];

		scrollIntoView(self.activedTab);
		
		self.onTabChange(oldTab,self.activedTab);//自定义事件,两个参数分别是先前的活动页签和现在活动的页签的index。
		self.activedTab.className = "oTabBtnActive";
		if($(self.activedTab.tabPage))
			$(self.activedTab.tabPage).style.display = "";
		
				
		// 20070820更正在IE下的小问题。
		if(oPageAreaOutline.offsetHeight < oPageAreaMinHeight){
			oPageAreaOutline.style.height = oPageAreaMinHeight + "px";
			//oPageAreaOutline.style.overflow = "visible";
		}
		//
		
		if(oPageAreaOutline.filters)
			oPageAreaOutline.filters[0].play(duration=1);
	};
	
	//________________________________________________
	
	
	this.create = function(){
		createOutline();
		createTitleOutline();
		createPageOutline();
	}
}