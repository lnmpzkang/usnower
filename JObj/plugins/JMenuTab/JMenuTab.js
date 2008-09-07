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

 2008/09/07 改成 JObj.Plugin
 ===================================================*/

!JObj.Plugin && (JObj.Plugin = {});
JObj.Plugin.JMenuTab = {};

(function($) {

    var JMenuTab = function(pWidth, pHeight, pBody) {
        var self = this;
        //________________________________________________
        var vars = {
            width : pWidth || "100%",
            height : pHeight,
            pageAreaMinHeight : 15,
            skin : ""
        };

        this.titleHeight = 24;
        //________________________________________________

        var objs = {
            outline : null,
            titleOutline : null,
            pageOutline : null,
            titleArea : null,
            titleAreaOuter : null,
            titleAreaInner : null,
            pageAreaOutline : null,
            pageArea : null,
            body : JObj.$(pBody) || document.body
        };

        var tabArray = new Array();
        this.activedTab = null;
        //________________________________________________
        this.onTabChange = null;
        this.onmouseover = null;
        //________________________________________________

        var outline_mouseover = function(evt){
            evt = evt || window.event;
            var obj = evt.srcElement || evt.target;

            //try {
                if (obj.className == "oTabBtnCenter" && JObj.isFunction(self.onmouseover)) {
                    self.onmouseover(obj.parentNode);
                }
            //} catch(e) {
                //
            //}
        }

        var createOutline = function() {

            var width_ = JObj.isRate(vars.width) ? vars.width : (!isNaN(vars.width) ? vars.width + "px" : "100%");

            objs.outline = JObj.$c("DIV");
            objs.body.appendChild(objs.outline);
			objs.outline.className = vars.skin;
            objs.outline.style.width = width_;

            objs.outline.onmouseover = outline_mouseover;
        }

        //________________________________________________

        /*这个方法是为了解决外观问题，比如：
         setClassId("JMenuTab");
         在CSS里就要这样写：
         #JMenuTab {...}
         #JMenuTab .oTitleHeight{...}
         */
        this.setSkin = function(pSkin) {
            objs.outline && (objs.outline.className = pSkin);
            vars.skin = pSkin;
        }
        //________________________________________________

        var moveBtnRight_mouseover = function() {
            this.className = "oMoveRightBtnActive";
        }

        var moveBtnRight_mouseout = function() {
            this.className = "oMoveRightBtnNormal";
        }

        var moveBtnRight_click = function() {
            self.setActiveTab(getNext(self.activedTab.index));
        }

        var moveBtnLeft_mouseover = function() {
            this.className = "oMoveLeftBtnActive";
        }

        var moveBtnLeft_mouseout = function() {
            this.className = "oMoveLeftBtnNormal";
        }

        var moveBtnLeft_click = function() {
            self.setActiveTab(getPrev(self.activedTab.index));
        }

        var createTitleOutline = function() {
            objs.titleOutline = JObj.$c("DIV");
            objs.outline.appendChild(objs.titleOutline);
            objs.titleOutline.className = "oTitleOutline";

            var vTable = JObj.$c("TABLE");
            objs.titleOutline.appendChild(vTable);
            vTable.style.width = "100%";
            vTable.style.height = "100%";
            vTable.style.borderCollapse = "collapse";
            vTable.style.tableLayout = "fixed";
            vTable.border = 0;
            vTable.cellSpacing = 0;
            vTable.cellPadding = 0;

            var vTBody = JObj.$c("TBODY");
            vTable.appendChild(vTBody);

            var vTr1 = JObj.$c("TR");
            vTBody.appendChild(vTr1);

            var vTdTopLeft = JObj.$c("TD");
            vTr1.appendChild(vTdTopLeft);
            vTdTopLeft.height = self.titleHeight;
            vTdTopLeft.className = "oTopLeft";

            objs.titleArea = JObj.$c("TD");/////////////////////////////////
            vTr1.appendChild(objs.titleArea);
            vTr1.style.width = "auto";
            objs.titleArea.className = "oTitleArea";

            objs.titleAreaOuter = JObj.$c("DIV");
            objs.titleArea.appendChild(objs.titleAreaOuter);
            objs.titleAreaOuter.style.width = "100%";
            objs.titleAreaOuter.style.overflow = "hidden";

            objs.titleAreaInner = JObj.$c("DIV");
            objs.titleAreaOuter.appendChild(objs.titleAreaInner);
            objs.titleAreaInner.style.width = "10000px";

            var vTdMoveArea = JObj.$c("TD");
            vTr1.appendChild(vTdMoveArea);
            vTdMoveArea.className = "oMoveBtnArea";

            var vMoveBtnLeft = JObj.$c("DIV");
            vTdMoveArea.appendChild(vMoveBtnLeft);
            vMoveBtnLeft.className = "oMoveLeftBtnNormal";
            vMoveBtnLeft.onmouseover = moveBtnLeft_mouseover;
            vMoveBtnLeft.onmouseout = moveBtnLeft_mouseout;
            vMoveBtnLeft.onclick = moveBtnLeft_click;

            var vMoveBtnRight = JObj.$c("DIV");
            vTdMoveArea.appendChild(vMoveBtnRight);
            vMoveBtnRight.className = "oMoveRightBtnNormal";
            vMoveBtnRight.onmouseover = moveBtnRight_mouseover;
            vMoveBtnRight.onmouseout = moveBtnRight_mouseout;
            vMoveBtnRight.onclick = moveBtnRight_click;

            var vTdTopRight = JObj.$c("TD");
            vTr1.appendChild(vTdTopRight);
            vTdTopRight.className = "oTopRight";
        }

        //________________________________________________
        this.setTitleHeight = function(pHeight) {
            //设置标题区域的高度
        }

        //________________________________________________

        var tabBtn_click = function() {
            self.setActiveTab(this.index);
        }

        var tabBtn_mouseover = function() {
            if (this.className == "oTabBtnActive")
                return;

            this.className = "oTabBtnHover";
        }

        var tabBtn_mouseout = function() {
            if (this.className == "oTabBtnActive")
                return;
            this.className = "oTabBtn";
        }

        var closeBtn_mouseover = function() {
            this.className = "oCloseBtnActive";
        }

        var closeBtn_mouseout = function() {
            this.className = "oCloseBtnNormal";
        }

        var closeBtn_click = function(evt) {
            evt = window.event || evt;
            evt.cancelBubble = true;
            self.closeTab(this.index)
        }

        this.closeTab = function(idx) {
            if (!(idx in tabArray)) return;

            var obj = tabArray[idx];
            var page = JObj.$(obj.parentNode.parentNode.tabPage);

            objs.titleAreaInner.removeChild(obj);

            delete tabArray[idx];
            if (page) page.parentNode.removeChild(page);

            if (typeof self.onTabClosed == "function") {
                self.onTabClosed(idx);
            }

            self.setActiveTab(getNextOrPrev(idx));
        }

        var scrollIntoView = function(obj) {
            var parent = obj.parentNode.parentNode;
            var scrollLeft = parent.scrollLeft;
            var offsetLeft = obj.offsetLeft;
            var width = obj.clientWidth;
            var totalWidth = parent.clientWidth;

            var dis = scrollLeft - offsetLeft;
            if (dis > 0) {
                parent.scrollLeft -= dis;
            } else if (-dis + width < totalWidth) {
                // in view;
            } else {
                parent.scrollLeft += -dis + width - totalWidth;
            }
        }

        //________________________________________________

        var getNextOrPrev = function(idx) {
            var next = getNext(idx);
            if (next) return next;
            else return getPrev(idx);
        }

        var getNext = function(idx) {
            for (var i = idx + 1; i < tabArray.length; i++) {
                if (i in tabArray)
                    return i;
            }
            return 0;
        }

        var getPrev = function(idx) {
            for (i = idx - 1; i >= 0; i--) {
                if (i in tabArray)
                    return i;
            }
            return 0;
        }

        var createCloseBtn = function(idx) {
            var vBtn = JObj.$c("DIV");
            vBtn.index = idx;
            vBtn.className = "oCloseBtnNormal";
            vBtn.onmousemove = closeBtn_mouseover;
            vBtn.onmouseout = closeBtn_mouseout;
            vBtn.onclick = closeBtn_click;
            return vBtn;
        }

        var createTabBtn = function(pLabel, pTabPage, pMouseAction, pCloseAble) {
            var vTabBtn = JObj.$c("DIV");
            objs.titleAreaInner.appendChild(vTabBtn);
            vTabBtn.className = "oTabBtn";
            //////////////////////////////////
            vTabBtn.index = tabArray.length;
            vTabBtn.label = pLabel;
            vTabBtn.tabPage = pTabPage;
            //////////////////////////////////

            if (pMouseAction !== false) {
                vTabBtn.onclick = tabBtn_click;
                vTabBtn.onmouseover = tabBtn_mouseover;
                vTabBtn.onmouseout = tabBtn_mouseout;

                tabArray.push(vTabBtn);
            }

            var vTabBtnL = JObj.$c("DIV");
            vTabBtn.appendChild(vTabBtnL);
            vTabBtnL.className = "oTabBtnLeft";

            vTabBtnC = JObj.$c("DIV");
            vTabBtn.appendChild(vTabBtnC);
            vTabBtnC.className = "oTabBtnCenter";
            vTabBtnC.innerHTML = pLabel;

            if (pCloseAble === true) {
                var vCloseArea = JObj.$c("DIV");
                vTabBtn.appendChild(vCloseArea);
                vCloseArea.className = "oTabBtnCenter";
                vCloseArea.appendChild(createCloseBtn(vTabBtn.index));
            }

            vTabBtnR = JObj.$c("DIV");
            vTabBtn.appendChild(vTabBtnR);
            vTabBtnR.className = "oTabBtnRight";

            return vTabBtn.index;
        }


        var createPageOutline = function() {
            objs.pageOutline = JObj.$c("DIV");
            objs.outline.appendChild(objs.pageOutline);
            objs.pageOutline.className = "oPageOutline";

            var vTable = JObj.$c("TABLE");
            objs.pageOutline.appendChild(vTable);
            vTable.width = "100%";
            vTable.border = 0;
            vTable.cellSpacing = 0;
            vTable.cellPadding = 0;
            vTable.style.borderCollapse = "collapse";
            vTable.style.tableLayout = "fixed";

            var vTBody = JObj.$c("TBODY");
            vTable.appendChild(vTBody);

            var vTr1 = JObj.$c("TR");
            vTBody.appendChild(vTr1);

            var vTdBottomLeft = JObj.$c("TD");
            vTr1.appendChild(vTdBottomLeft);
            vTdBottomLeft.className = "oBodyLeft";
            //vTdBottomLeft.rowSpan = 2;

            objs.pageAreaOutline = JObj.$c("TD");///////////////////////////////////////
            vTr1.appendChild(objs.pageAreaOutline);
            objs.pageAreaOutline.className = "oPageAreaOutline";
            objs.pageAreaOutline.style.overflow = "hidden";
            if (objs.pageAreaOutline.filters)
                objs.pageAreaOutline.style.cssText = "FILTER: progid:DXImageTransform.Microsoft.Wipe(GradientSize=1.0,wipeStyle=0, motion='forward');";

            objs.pageArea = JObj.$c("DIV");
            objs.pageAreaOutline.appendChild(objs.pageArea);
            objs.pageArea.className = "oPageArea";

            var vTdBottomRight = JObj.$c("TD");
            vTr1.appendChild(vTdBottomRight);
            vTdBottomRight.className = "oBodyRight";
            //vTdBottomRight.rowSpan = 2;

            var vTr2 = JObj.$c("TR");
            vTBody.appendChild(vTr2);

            ///////
            var vTdBottomLeft = JObj.$c("TD");
            vTr2.appendChild(vTdBottomLeft);
            vTdBottomLeft.className = "oBottomLeft";            

            var vTdBottomCenter = JObj.$c("TD");
            vTr2.appendChild(vTdBottomCenter);
            vTdBottomCenter.className = "oBottomCenter";

            //////
            var vTdBottomRight = JObj.$c("TD");
            vTr2.appendChild(vTdBottomRight);
            vTdBottomRight.className = "oBottomRight";            
        }
        //________________________________________________
        this.setFixHeight = function(pHeight, pAutoExpend, pXScroll, pYScroll) {
            objs.pageArea.style.width = "100%";
            if (pAutoExpend) {
                objs.pageArea.style.minHeight = pHeight + "px";
            } else {
                objs.pageArea.style.height = pHeight + "px";
                objs.pageArea.style.overflowY = pYScroll === true ? "auto" : "hidden";
                objs.pageArea.style.overflowX = pXScroll === true ? "auto" : "hidden";

                if (pXScroll == undefined) objs.pageArea.style.overflowX = "auto";
                if (pYScroll == undefined) objs.pageArea.style.overflowY = "auto";

                if (JObj.Browser.opera) objs.pageArea.style.overflow = "auto";
            }
        }
        //________________________________________________

        this.addTab = function (pLabel, pPageBodyId, pCloseAble) {
            var i = createTabBtn(pLabel, pPageBodyId, true, pCloseAble);
            if (JObj.$(pPageBodyId)) {
                objs.pageArea.appendChild(JObj.$(pPageBodyId));
                JObj.$(pPageBodyId).style.display = "none";
            }

            return i;
        }

        //________________________________________________

        this.setDefaultPage = function(pPageBodyId) {
            createTabBtn("", pPageBodyId, false);
            objs.pageArea.appendChild(JObj.$(pPageBodyId));
            JObj.$(pPageBodyId).style.display = "";
        }

        //________________________________________________

        this.setActiveTab = function(pIndex) {

            if (!(pIndex in tabArray)) return;

            if (objs.pageAreaOutline.filters)
                objs.pageAreaOutline.filters[0].apply();

            if (self.activedTab != null) {
                self.activedTab.className = "oTabBtn";
                if (JObj.$(self.activedTab.tabPage))
                    JObj.$(self.activedTab.tabPage).style.display = "none";
            }

            var oldTab = self.activedTab;
            self.activedTab = tabArray[pIndex];

            scrollIntoView(self.activedTab);

            JObj.isFunction(self.onTabChange) && self.onTabChange(oldTab, self.activedTab);//自定义事件,两个参数分别是先前的活动页签和现在活动的页签的index。
            self.activedTab.className = "oTabBtnActive";
            if (JObj.$(self.activedTab.tabPage))
                JObj.$(self.activedTab.tabPage).style.display = "";


            // 20070820更正在IE下的小问题。
            if (objs.pageAreaOutline.offsetHeight < vars.pageAreaMinHeight) {
                objs.pageAreaOutline.style.height = vars.pageAreaMinHeight + "px";
                //objs.pageAreaOutline.style.overflow = "visible";
            }
            //

            if (objs.pageAreaOutline.filters)
                objs.pageAreaOutline.filters[0].play(duration = 1);
        };

        //________________________________________________


        this.create = function() {
            createOutline();
            createTitleOutline();
            createPageOutline();
        }
    }

    $.getInstance = function(pWidth, pHeight, pBody){
        JObj.Loader.loadCss(JObj.path + "plugins/JMenuTab/skinDefault/JMenuTab.css")
        var jmt = new JMenuTab(pWidth, pHeight, pBody);
        jmt.setSkin("JMenuTabDefault");
        return jmt;
    }

})(JObj.Plugin.JMenuTab);