JObj.use("ui");
JObj.use("JDrag");

if (typeof(JObj.Plugin) != "object") JObj.Plugin = {};

JObj.Plugin.JWin = {};

(function($) {


    var JWin = function(pWidth, pHeight, pParent) {
        var _self = this;

        var tpl = '<TABLE style="BORDER-COLLAPSE: collapse" cellSpacing="0" cellPadding="0" width="100%" border="0"><TBODY><TR><TD class="title_left_top" style="CURSOR: nw-resize"></TD><TD class="title_center_top" style="CURSOR: n-resize"></TD><TD class="title_right_top" style="CURSOR: ne-resize"></TD></TR><TR><TD class="title_left_middle" style="CURSOR: nw-resize"></TD><TD class="title_center_middle"><table width="100%" border="0" cellpadding="0" cellspacing="0"><tr><td class="sysIconArea"></td><td class="winTitleArea"></td><td class="sysBtnArea"></td></tr></table></TD><TD class="title_right_middle" style="CURSOR: ne-resize"></TD></TR><TR><TD class="title_left_bottom"></TD><TD class="title_center_bottom"></TD><TD class="title_right_bottom"></TD></TR><TR><TD class="mainBody_left" style="CURSOR: w-resize"></TD><TD class="mainBody_center"><div></div></TD><TD class="mainBody_right" style="CURSOR: e-resize"></TD></TR><TR><TD class="status_left_top"></TD><TD class="status_center_top"></TD><TD class="status_right_top"></TD></TR><TR><TD class="status_left_middle" style="CURSOR: sw-resize"></TD><TD class="status_center_middle"></TD><TD class="status_right_middle" style="CURSOR: se-resize"></TD></TR><TR><TD class="status_left_bottom" style="CURSOR: sw-resize"></TD><TD class="status_center_bottom" style="CURSOR: s-resize"></TD><TD class="status_right_bottom" style="CURSOR: se-resize"></TD></TR></TBODY></TABLE>';
        //var tpl = '<TABLE style="BORDER-COLLAPSE: collapse" cellSpacing="0" cellPadding="0" width="100%" border="0"><TBODY><TR><TD class="title_left_top" style="CURSOR: nw-resize"></TD><TD class="title_center_top" style="CURSOR: n-resize"></TD><TD class="title_right_top" style="CURSOR: ne-resize"></TD></TR><TR><TD class="title_left_middle" style="CURSOR: nw-resize"></TD><TD class="title_center_middle"><div class="sysIconArea"></div><div class="winTitleArea"></div><div class="sysBtnArea"></div></TD><TD class="title_right_middle" style="CURSOR: ne-resize"></TD></TR><TR><TD class="title_left_bottom"></TD><TD class="title_center_bottom"></TD><TD class="title_right_bottom"></TD></TR><TR><TD class="mainBody_left" style="CURSOR: w-resize"></TD><TD class="mainBody_center"><div></div></TD><TD class="mainBody_right" style="CURSOR: e-resize"></TD></TR><TR><TD class="status_left_top"></TD><TD class="status_center_top"></TD><TD class="status_right_top"></TD></TR><TR><TD class="status_left_middle" style="CURSOR: sw-resize"></TD><TD class="status_center_middle"></TD><TD class="status_right_middle" style="CURSOR: se-resize"></TD></TR><TR><TD class="status_left_bottom" style="CURSOR: sw-resize"></TD><TD class="status_center_bottom" style="CURSOR: s-resize"></TD><TD class="status_right_bottom" style="CURSOR: se-resize"></TD></TR></TBODY></TABLE>';
        var body = JObj.$(pParent) || document.body;

        var width = pWidth;
        var height = pHeight;
        var skin = "";

        var objs = {
            oArea:null,
            oMask:null,
            oOutline:null,
            oWinTitle:null,
            oSysBtnArea:null,
            oMainBody:null,
            oStatus:null
        };

        var sysBtns = {
            oMinBtn:null,
            oMaxBtn:null,
            oCloseBtn:null
        };

        var vars = {
            original:null,
            status : "normal"
        };

        this.setSkin = function(pSkin) {
            skin = pSkin;
            if (objs.oOutline) {
                objs.oOutline.className = skin;
            }
            calcSize();
        }

        this.create = function() {

            objs.oArea = JObj.$c("DIV");
            body.appendChild(objs.oArea);

            objs.oOutline = JObj.$c("DIV");
            objs.oArea.appendChild(objs.oOutline);
            objs.oOutline.className = skin;
            objs.oOutline.style.width = width + "px";
            objs.oOutline.style.height = height + "px";
    //objs.oOutline.style.minHeight = height + "px";
            objs.oOutline.style.position = "relative";

            objs.oOutline.innerHTML = tpl;
            vars.table = objs.oOutline.firstChild;

            var tab = objs.oOutline.getElementsByTagName("TABLE")[1];

            objs.oWinTitle = tab.rows[0].cells[1];
            objs.oSysBtnArea = tab.rows[0].cells[2];

            objs.oMainBody = vars.table.rows[3].cells[1].firstChild;
            objs.oMainBody.style.overflow = "auto";
            objs.oStatus = vars.table.rows[5].cells[1];


            vars.otherHeight = vars.table.rows[0].clientHeight
                    + vars.table.rows[2].clientHeight
                    + vars.table.rows[4].clientHeight
                    + vars.table.rows[6].clientHeight;

            objs.oMask = JObj.$c("DIV");
            objs.oArea.appendChild(objs.oMask);
            objs.oMask.ondblclick = function(evt) {
                evt = window.event || evt;
                var obj = evt.srcElement || evt.target;
                var f = true;

                while (obj) {
                    if (obj == objs.oOutline) {
                        f = false;
                        break;
                    }
                    obj = obj.parentNode;
                }

                if (f) {
                    _self.hidden();
                }
            }

            calcSize();
        }

        var calcSize = function() {
            //var w = arguments[0];
            var h = arguments[1] || objs.oOutline.clientHeight;

            vars.otherHeight = vars.table.rows[0].clientHeight
                    + vars.table.rows[2].clientHeight
                    + vars.table.rows[4].clientHeight
                    + vars.table.rows[6].clientHeight;

		//Safari下,这句怎么不起作用?
            //vars.table.rows[3].style.height = h - vars.otherHeight - vars.table.rows[1].clientHeight - vars.table.rows[5].clientHeight + "px";
            //objs.oMainBody.style.height = vars.table.rows[3].style.height;
            objs.oMainBody.style.height = h - vars.otherHeight - objs.oWinTitle.clientHeight - objs.oStatus.clientHeight + "px";
        }

        this.getTitle = function() {
            return objs.oWinTitle;
        }

        this.getBody = function() {
            return objs.oMainBody;
        }

        this.getStatus = function() {
            return objs.oStatus;
        }

        this.setTitle = function(p) {
            typeof(p) == "object" ? objs.oWinTitle.appendChild(JObj.$(p)) : objs.oWinTitle.innerHTML = p;
            calcSize();
        }

        this.setBody = function(p) {
            typeof(p) == "object" ? objs.oMainBody.appendChild(JObj.$(p)) : objs.oMainBody.innerHTML = p;
            calcSize();
        }

        this.setStatus = function(p) {
            typeof(p) == "object" ? objs.oStatus.appendChild(JObj.$(p)) : objs.oStatus.innerHTML = p;
            calcSize();
        }

        this.setDrag = function(f) {
            JObj.UI.JDrag.setDrag(objs.oOutline, objs.oWinTitle, f);
        }

        var sysBtn_mouseover = function() {
            this.className = this.className.replace("normal", "active");
        }

        var sysBtn_mouseout = function() {
            this.className = this.className.replace("active", "normal");
        }

        var createSysBtn = function(className){
            var obj = JObj.$c("DIV");
            objs.oSysBtnArea.appendChild(obj);
            obj.className = className;
            obj.onmouseover = sysBtn_mouseover;
            obj.onmouseout = sysBtn_mouseout;
            return obj;
        }

        this.setSysButtons = function(btnMin, btnMax, btnClose) {
            if (btnClose !== false) {
                sysBtns.oCloseBtn = createSysBtn("closeBtn_normal");
                sysBtns.oCloseBtn.onclick = _self.hidden;
            }

            if (btnMax !== false) {
                sysBtns.oMaxBtn = createSysBtn("maxBtn_normal");
                sysBtns.oMaxBtn.onclick = maxSize;
            }

            if (btnMin !== false) {
                sysBtns.oMinBtn = createSysBtn("minBtn_normal");
            }
        }

        /*------------------------------------------------------
      我很疑惑:把maxSize,restoreSize改成public的,sysBtns就读不到该实例的了(如果有多个实例),研究........
      -------------------------------------------------------*/

        var maxSize = function() {
            vars.status = "max";
            if (vars.original == null)
                vars.original = {};
            vars.original.width = objs.oOutline.clientWidth + "px";
            vars.original.height = objs.oOutline.clientHeight + "px";
            vars.original.top = objs.oOutline.style.top;
            vars.original.left = objs.oOutline.style.left;

            var w = document.documentElement.clientWidth;
            var h = document.documentElement.clientHeight;

		//document.body.appendChild(objs.oOutline);
            with (objs.oOutline.style) {
                left = "0px";
                top = "0px";
                position = "absolute";
                width = w + "px";
                height = h + "px";
            }

            calcSize(w, h);

            sysBtns.oMaxBtn.className = "restoreBtn_normal";
            sysBtns.oMaxBtn.onclick = restoreSize;
        }

        var restoreSize = function() {
            vars.status = "normal";
            if (vars.original == null) return;
            try {
                with (objs.oOutline.style) {
                    width = vars.original.width;
                    height = vars.original.height;
                    top = vars.original.top;
                    left = vars.original.left;
                }

                calcSize();
                sysBtns.oMaxBtn.className = "maxBtn_normal";
                sysBtns.oMaxBtn.onclick = maxSize;

			//objs.oArea.appendChild(objs.oOutline);
            } catch(e) {
                //	alert(e.message)
            }
        }

        this.moveTo = function(x, y) {
            with (objs.oOutline.style) {
                left = x + "px";
                top = y + "px";
                position = "absolute";
            }
        }

        this.moveToCenter = function() {
            var x = (document.documentElement.clientWidth - objs.oOutline.clientWidth) / 2;
            var y = (document.documentElement.clientHeight - objs.oOutline.clientHeight) / 2;

            _self.moveTo(x, y);
        }

        this.show = function() {
            objs.oOutline.style.display = "";
        }

        this.showModal = function() {
            /*		objs.oMask.style.width = "auto";
           objs.oMask.style.height = "auto";*/
            //objs.oMask.style.width = document.documentElement.clientWidth + "px";
            //objs.oMask.style.height = document.documentElement.clientHeight + "px";
            objs.oMask.style.position = "absolute";
            objs.oMask.style.top = "0px";
            objs.oMask.style.left = "0px";
            objs.oMask.style.right = "0px";
            objs.oMask.style.bottom = "0px";
            objs.oMask.style.backgroundColor = "#999";
            objs.oMask.style.filter = "Alpha(Opacity=90)";
            objs.oMask.style.opacity = 0.9;
            objs.oMask.style.zIndex = 2147483645;

            objs.oOutline.style.display = "";
            objs.oOutline.style.zIndex = 2147483646;

            calcSize();
            _self.moveToCenter();
        }

        this.hidden = function() {
            objs.oOutline.style.display = "none";
            restoreSize();
            objs.oMask.style.cssText = "";
        }

        this.setBodyHeight = function(h) {
            if (vars.status != "max") {
                objs.oMainBody.style.height = h + "px";
                objs.oOutline.style.height = h + objs.oWinTitle.clientHeight + objs.oStatus.clientHeight + vars.otherHeight + "px";

                if (vars.original == null)
                    vars.original = {};

                vars.original.width = objs.oOutline.clientWidth + "px";
                vars.original.height = objs.oOutline.clientHeight + "px";
                vars.original.top = objs.oOutline.style.top;
                vars.original.left = objs.oOutline.style.left;
            } else {
                vars.original.height = h + objs.oWinTitle.clientHeight + objs.oStatus.clientHeight + vars.otherHeight + "px";
            }
        }
    }

    $.getInstance = function(pWidth,pHeight){
        JObj.Loader.loadCss(JObj.path + "plugins/JWin/JWinRes/JWin.css");
        var jwin = new JWin(pWidth,pHeight);
        jwin.create();
        jwin.setSkin("JWinDefault");
        return jwin;
    }
})(JObj.Plugin.JWin);