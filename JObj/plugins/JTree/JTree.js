if (typeof(JObj.Plugin) != "object") JObj.Plugin = {};

JObj.Plugin.JTree = {};

(function($, $$) {

    var treeNode = function() {
        var self = this;
        this.obj = null;	//指caption所在的标签：<span>。。</span>
        this.caption = null;	//显示的文字
        this.level = null;	//节点的层次
        this.value = null;	//这个值暂时没有用到。预感到会有用，因为做Delphi的树时，就因为缺少相关的东东，不得不用其它的办法来取代

        this.xml = null;
		
		//----------------------------------
        this.treeNodes = new Array();	//子树集合

        this.parentTreeNode = null;			//当前“树枝”父树枝，就像树叶和树枝的关系一样。
        this.expand = function(pFlag) {        //如果是树枝，就收缩或展开。要重定位一下要展开或收缩的对象。
            try {
                self.obj.parentNode.expand(pFlag);//pFlag只能为false或true
            } catch(e) {
            }
            ;
        }
        this.click = function() {
            self.obj.onclick();
        }
        this.dblclick = function() {
            self.obj.ondblclick();
        }
    }

    var JTree = function(pParent) {
        //this.PICPATH = "JTree/"	//图片文件所在的文件夹，可见public，可改变。
        this.PICPATH = $$.path + "plugins/JTree/JTreePic/";

        var self = this;	//相当于一个引用，指向自己。JTree.
        //-----------------------------------------------------------------------------

        var JOIN = this.PICPATH + "join.gif";
        var JOINBOTTOM = this.PICPATH + "joinbottom.gif";
        var MINUS = this.PICPATH + "minus.gif";
        var MINUSBOTTOM = this.PICPATH + "minusbottom.gif";
        var PLUS = this.PICPATH + "plus.gif";
        var PLUSBOTTOM = this.PICPATH + "plusbottom.gif";
        var EMPTY = this.PICPATH + "empty.gif";
        var LINE = this.PICPATH + "line.gif";

        var LEAFICON = this.PICPATH + "page.gif";
        var NODEICON = this.PICPATH + "folder.gif";

        var OPEN = new Array();
        OPEN[true] = MINUS;
        OPEN[false] = PLUS;

        var OPENBOTTOM = new Array();
        OPENBOTTOM[true] = MINUSBOTTOM;
        OPENBOTTOM[false] = PLUSBOTTOM;

        this.setPicPath = function(pPath) {
            self.PICPATH = pPath;

            JOIN = self.PICPATH + "join.gif";
            JOINBOTTOM = self.PICPATH + "joinbottom.gif";
            MINUS = self.PICPATH + "minus.gif";
            MINUSBOTTOM = self.PICPATH + "minusbottom.gif";
            PLUS = self.PICPATH + "plus.gif";
            PLUSBOTTOM = self.PICPATH + "plusbottom.gif";
            EMPTY = self.PICPATH + "empty.gif";
            LINE = self.PICPATH + "line.gif";

            OPEN[true] = MINUS;
            OPEN[false] = PLUS;

            OPENBOTTOM[true] = MINUSBOTTOM;
            OPENBOTTOM[false] = PLUSBOTTOM;

            LEAFICON = self.PICPATH + "page.gif";
            NODEICON = self.PICPATH + "folder.gif";
        }

        this.CAPTIONATT = "caption";//标题属性是哪一个属性
        this.ICONATT = "icon";//图标属性
        this.EXPANDALL = true;//是否全部扩展。

        //this.clickItem=new treeNode;
        //this.clickItem = new treeNode;//用于点击时，返回值。
        this.clickItem = null;
        this.selectNode = null;//同上
        //----------------------------------------------------
        this.treeNodes = new Array();//树节点的集合。
        this.treeNodes.push(null);
        this.root = this.treeNodes[0] = new treeNode;//树的根

        this.onclick = null;
        this.onmouseover = null;
        this.onmouseout = null;
        this.ondblclick = null;
		//----------------------------------------------------------------------------

        //----------------------------------------------------------------------------
        //2006/12/05修改，目的是为了做全展开和全缩。
        var oOutLine = null; //轮廓
        var leafAreas = new Array();
		//-----------------------------------------------------------------------------
        this.body = JObj.$(pParent) || document.body;
		//-----------------------------------------------------------------------------

        var xmlDom = null;
        var DOMRoot = null;

        this.loadFromFile = function(xmlFile) {
            $$.Ajax.send({
                url:xmlFile,
                method:"GET",
                async:false,

                onSuccess:function(xmlHttp, status, rule, dataRule) {
                    xmlDom = xmlHttp.responseXML;
                    DOMRoot = xmlDom.documentElement;
                }
            });
        }

        this.loadFromString = function(xmlString) {
            xmlDom = $$.Xml.loadXML(xmlString);
            DOMRoot = xmlDom.documentElement;
        }

		//-----------------------------------------------------------------------------
        var createImg = function(pSrc) {
            var tmp = $$.$c("IMG");
            tmp.align = "absmiddle";
            tmp.src = pSrc;
            tmp.onerror = function() {
                try {
                    this.parentNode.removeChild(this);
                } catch(e) {
                }
            }
            return tmp;
        }

       var caption_onclick = function(e){
                e = window.event || e;
                try {
                    self.clickItem.className = "caption";
                } catch(e) {}
                this.className = "captionHighLight";

                self.clickItem = this;
                self.selectNode = this.xmlNode;
                try {
                    if(e.type == "click")
                        self.onclick();
                    else
                        self.ondblclick();
                } catch(e) {
                }//必须加上，如果self没有对onclick赋值的话，会引发错误。
       }

        var caption_mousemove = function(){
               if (this.className != "captionHighLight")
                    this.className = "captionActive";
                try {
                    self.onmouseover()
                } catch(e) {}            
        }

        var caption_mouseout = function(){
                if (this.className != "captionHighLight")
                    this.className = "caption";
                try {
                    self.onmouseout()
                } catch(e) {}            
        }

        var createCaption = function(pNode, pLevel) {
            var tmp = $$.$c("SPAN");
            tmp.xmlNode = pNode;
            tmp.level = pLevel;
            tmp.innerHTML = $$.Xml.getNodeAtt(pNode, self.CAPTIONATT);
            tmp.className = "caption";
            tmp.onmouseover = caption_mousemove;
            tmp.onmouseout = caption_mouseout;
            tmp.onclick = caption_onclick;
            tmp.ondblclick = caption_onclick;
            return tmp;
        }


        var childShowBtn_onclick = function(){
          var isExpand = this.parentNode.expand();

            if (!this.parentArea.isLastChild) {
                this.src = OPEN[isExpand];
            } else {
                this.src = OPENBOTTOM[isExpand];
            }
        }

        var createTreeLine = function(pNode, pParentArea) {
            var hasChildren = pNode.hasChildNodes();//是否有孩子。
            for (var i = 0; i < pParentArea.level; i++) {
                var tmpArea = pParentArea;
                for (var j = pParentArea.level; j > i; j--) {
                    //tmpArea=tmpArea.parentNode;
                    tmpArea = tmpArea.parentNode.parentNode;
                }

                if (tmpArea.isLastChild)
                    appendTo(createImg(EMPTY), pParentArea);
                else
                    appendTo(createImg(LINE), pParentArea);
            }

            if (hasChildren) {//有孩子
                var childShowBtn;
                if (!pParentArea.isLastChild) {
                    childShowBtn = createImg(OPEN[true]);
                    appendTo(childShowBtn, pParentArea);
                } else {
                    childShowBtn = createImg(OPENBOTTOM[true]);
                    appendTo(childShowBtn, pParentArea);
                }
                childShowBtn.parentArea = pParentArea;
                childShowBtn.onclick = childShowBtn_onclick;
                pParentArea.expandBtn = childShowBtn;//新增的
            } else {//无孩子。
                if (!pParentArea.isLastChild)
                    appendTo(createImg(JOIN), pParentArea);
                else
                    appendTo(createImg(JOINBOTTOM), pParentArea);
            }
        }

        var createIcon = function(pNode, pParentArea) {
            var hasChildren = pNode.hasChildNodes();//是否有孩子
            var tmpIcon = $$.Xml.getNodeAtt(pNode, self.ICONATT);
            //alert(NODEICON)
            if (tmpIcon == false) {
                if (hasChildren)
                    appendTo(createImg(NODEICON), pParentArea);
                else
                    appendTo(createImg(LEAFICON), pParentArea);
            } else {
                appendTo(createImg(tmpIcon), pParentArea);
            }
        }
		//-----------------------------------------------------------------------------
        //将指定OBJ追加到某个OBJ的最后面。
        var appendTo = function(pObj, pTargetObj) {
            try {
                pTargetObj.appendChild(pObj);
            } catch(e) {
                alert(e.message);
            }
        }
		//-----------------------------------------------------------------------------
        var isFirstChild = function(pNode) {
            //除了空白节点之外，是否是第一个节点
            var tmpNode = pNode.previousSibling;
            try {
                while (tmpNode.previousSibling != null && tmpNode.nodeType != 1)
                    tmpNode = tmpNode.previousSibling;
                if (tmpNode.nodeType == 3)//是空节点
                    return true;
                else
                    return false;
            } catch(e) {
                return true;
            }
        }
        var isLastChild = function(pNode) {
            var tmpNode = pNode.nextSibling;
            try {
                while (tmpNode.nextSibling != null && tmpNode.nodeType != 1)
                    tmpNode = tmpNode.nextSibling;
                if (tmpNode.nodeType == 3)//是空节点
                    return true;
                else
                    return false;
            } catch(e) {
                return true;
            }
        }
		//-----------------------------------------------------------------------------
        //循环绘制各节点。从下面这些起，这些节点具有收缩功能，所以，下面的这些不应该被oRoot所包含，而应该是oOutLine的孩子。
        var createSubTree = function(pNode, pLevel, pNodeArea, pTreeNode) {
            var subNode;
            for (var i = 0; subNode = pNode.childNodes[i]; i++) {
                if (subNode.nodeType != 1) continue;//由于默认了把空白也当着一个节点来处理，所以，这里要判断一下。

                var subNodeItem = $$.$c("DIV")

                if (subNode.hasChildNodes()) {
                    var subNodeSubArea = $$.$c("DIV");
                    leafAreas.push(subNodeSubArea);
                }

                subNodeItem.level = pLevel + 1;
                subNodeItem.isFirstChild = isFirstChild(subNode);
                subNodeItem.isLastChild = isLastChild(subNode);
					//subNodeItem.parentTreeNode	=pTreeNode;//新增属性				

                //下面的这个位置不能变动，因为createTreeLine里用到了它的parentNode
                appendTo(subNodeItem, pNodeArea);

                createTreeLine(subNode, subNodeItem);
                createIcon(subNode, subNodeItem);
                var subNodeCaption = createCaption(subNode, pLevel + 1);
                subNodeItem.caption = subNodeCaption.innerHTML;

                subNodeItem.tree = new treeNode();
                subNodeItem.tree.obj = subNodeCaption;
                subNodeItem.tree.caption = subNodeItem.caption;
                subNodeItem.tree.level = subNodeItem.level;
                subNodeItem.tree.xml = subNode;
                subNodeItem.tree.parentTreeNode = pTreeNode;

                pTreeNode.treeNodes.push(subNodeItem.tree);

                appendTo(subNodeCaption, subNodeItem);

                if (subNode.hasChildNodes()) {
                    //createSubTree(subNode,pLevel+1,subNodeItem);
                    appendTo(subNodeSubArea, subNodeItem);
                    createSubTree(subNode, pLevel + 1, subNodeSubArea, pTreeNode.treeNodes[pTreeNode.treeNodes.length - 1]);
                    subNodeItem.subNodeSubArea = subNodeSubArea;

                    subNodeItem.expand = function(pFlag) {
                        //如果状态是展开，返回真，否则返回假。
                        //this.subNodeSubArea.style.display=="" ? this.subNodeSubArea.style.display="none" : this.subNodeSubArea.style.display="";

                        if (pFlag == null) {
                            if (this.subNodeSubArea.style.display == "") {
                                this.subNodeSubArea.style.display = "none";
                                return false;
                            } else {
                                this.subNodeSubArea.style.display = "";
                                return true;
                            }
                        } else {
                            //alert(this.expandBtn.tagName);
                            if (pFlag)
                                this.subNodeSubArea.style.display = "";
                            else this.subNodeSubArea.style.display = "none";

                            if (!this.isLastChild)
                                this.expandBtn.src = OPEN[pFlag];
                            else
                                this.expandBtn.src = OPENBOTTOM[pFlag];

                        }

                    };
                }
            }
        }
		
		
		//--------------------------------------------------------------------------------
        //2006/12/05新增功能
        this.expandAll = function(pExpandAll) {
            var oLeafArea;
            for (i = 0; oLeafArea = leafAreas[i]; i++) {
                //oLeafArea.style.display = (pExpandAll == false ? "none" : "");
                oLeafArea.parentNode.expand(pExpandAll);
            }
        }
		//--------------------------------------------------------------------------------

        this.create = function() {
            //-----------------------------------------------------------------------------
            //绘制轮廓
            oOutLine = $$.$c("DIV");
            oOutLine.className = "JTree";
            appendTo(oOutLine, this.body);
			//oOutLine.onclick=this.onclick;
            //-----------------------------------------------------------------------------
            //绘制根。这个根不具备收缩的功能。
            var oRoot = $$.$c("DIV");

            oRoot.level = -1;//级别。根的级别为-1;

            var oRootIcon = createImg($$.Xml.getNodeAtt(DOMRoot, self.ICONATT));
			//var oRootCaption=createCaption($$.Xml.getNodeAtt(DOMRoot,self.CAPTIONATT),-1);
            var oRootCaption = createCaption(DOMRoot, -1);
            oRoot.caption = oRootCaption.innerHTML;
			
			//================================================
            //子树
            //================================================
            oRoot.tree = new treeNode();
            oRoot.tree.obj = oRootCaption;
            oRoot.tree.caption = oRoot.caption;
            oRoot.tree.level = oRoot.level;
            oRoot.tree.xml = DOMRoot;
            oRoot.tree.parentTreeNode = self.treeNodes[0];

            self.root = self.treeNodes[0] = oRoot.tree;

            appendTo(oRootIcon, oRoot);
            appendTo(oRootCaption, oRoot);
            appendTo(oRoot, oOutLine);
			//------------------------------------------------------------------------------		
            createSubTree(DOMRoot, -1, oOutLine, self.treeNodes[0]);
            self.expandAll(self.EXPANDALL);
        }
    }

    $.getInstance = function(pParent) {
        var jtree = new JTree(pParent);
        //jtree.setPicPath(JObj.path + "plugins/JTree/JTreePic/");
        $$.Loader.loadCss(JObj.path + "plugins/JTree/JTree.css");
        return jtree;
    }

})(JObj.Plugin.JTree, JObj);