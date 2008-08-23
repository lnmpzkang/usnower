JObj.use("ui");

JObj.UI.JAnimate = {};

(function($) {

    var objs = {};
    var idTag = "__JObj_ANI_ID__";

    var Ani = function(obj) {
        var $ = this;
        var id = obj.getAttribute(idTag);

        $.onComplete = null;
        $.onAbort = null;
        $.onStart = null;
        $.onProcess = null;

        var vars = {
            cW : false,
            cH : false,
            cA : false,
            cL : false,
            cT : false,

            dW : 1,
            dH : 1,
            dA : 1,
            dL : 1,
            dT : 1
        };

        var ready_ = function(fromRule,toRule){
            vars.dW = fromRule.width < toRule.width ? 1 : -1;
            vars.dH = fromRule.height < toRule.height ? 1 : -1;
            vars.dA = fromRule.alpha < toRule.alpha ? 1 : -1;
            vars.dL = fromRule.left < toRule.left ? 1 : -1;
            vars.dT = fromRule.top < toRule.top ? 1 : -1;
        }

        var ready = function(fromRule,toRule){
            vars.cW = fromRule.width == toRule.width //|| fromRule.width != undefined && toRule.width != undefined;
            vars.cH = fromRule.height == toRule.height //|| fromRule.height != undefined && toRule.height != undefined;
            vars.cA = fromRule.alpha == toRule.alpha //|| fromRule.alpha != undefined && toRule.alpha != undefined;
            vars.cL = fromRule.left == toRule.left //|| fromRule.left != undefined && toRule.left != undefined;
            vars.cT = fromRule.top == toRule.top //|| fromRule.top != undefined && toRule.top != undefined;
        }

        var differ = function(from,to){
            var v = Math.abs( to - from );
            if(v >= 10) v /= 10;
            else if(v >= 5) v /= 5;
            else if(v >= 2) v /= 2;
            else v = 1;
            //return to - from >=0 ? v : -v;
            return v;
        }
        
        $.run = function(fromRule,toRule){
            var i = arguments[2];
            if(i == undefined){
                ready_(fromRule,toRule);
                JObj.isFunction($.onStart) ? $.onStart(obj) : null;
            }
            i++;

            ready(fromRule,toRule);
            
            if(!vars.cW){
                fromRule.width += vars.dW * differ(fromRule.width , toRule.width);
                if(fromRule.width < 0) fromRule.width = 0;

                if((vars.dW == 1 && fromRule.width > toRule.width) || (vars.dW == -1 && fromRule.width < toRule.width))
                    fromRule.width = toRule.width;

                obj.style.width = fromRule.width + "px";
            }

            if(!vars.cH){
                fromRule.height += vars.dH * differ(fromRule.height , toRule.height);
                if(fromRule.height < 0 ) fromRule.height = 0;

                if((vars.dH == 1 && fromRule.height > toRule.height) || (vars.dH == -1 && fromRule.height < toRule.height))
                    fromRule.height = toRule.height;

                obj.style.height = fromRule.height + "px";
            }

            if(!vars.cA){
                fromRule.alpha += vars.dA * differ(fromRule.alpha , toRule.alpha);
                if(fromRule.alpha < 0) fromRule.alpha = 0;
                if(fromRule.alpha > 100) fromRule.alpha = 100;

                if((vars.dA == 1 && fromRule.alpha > toRule.alpha) || (vars.dA == -1 && fromRule.alpha < toRule.alpha))
                    fromRule.alpha = toRule.alpha;

                if(JObj.Browser.ie){
                    obj.style.filter = "Alpha(Opacity=" + fromRule.alpha + ")";
                }else{
                    obj.style.opacity = fromRule.alpha / 100;
                }
            }

            if(!vars.cT){
                fromRule.top += vars.dT * differ(fromRule.top , toRule.top);
                if((vars.dT == 1 && fromRule.top > toRule.top) || (vars.dT == -1 && fromRule.top < toRule.top))
                    fromRule.top = toRule.top;

                obj.style.left = fromRule.top + "px";
            }

            if(!vars.cL){
                fromRule.left += vars.dL * differ(fromRule.left , toRule.left);
                if((vars.dL == 1 && fromRule.left > toRule.left) || (vars.dL == -1 && fromRule.left < toRule.left))
                    fromRule.left = toRule.left;

                obj.style.left = fromRule.left + "px";
            }

            if(vars.cW && vars.cH  && vars.cA && vars.cL && vars.cT){
                clearTimeout(vars[id]);
                JObj.isFunction($.onComplete) ? $.onComplete(obj) : null;
            }else{
                JObj.isFunction($.onProcess) ? $.onProcess(obj) : null;
                vars[id] = setTimeout(JObj.doFunction($.run,fromRule,toRule,i),33);
            }
        }

        $.abort = function(){
            clearTimeout(vars[id]);
            JObj.isFunction($.onAbort) ? $.onAbort(obj) : null;
        }
    }

    $.$ = function(obj){
        obj = JObj.$(obj);
        //if(!obj.hasAttribute(idTag)){
        if(obj.getAttribute(idTag) != null || obj.getAttribute(idTag) != ""){
            if(obj.uniqueID)
                var id = obj.uniqueID;
            else
                var id = (((new Date()).valueOf() * 100000) + Math.random() * 100000).toString(32);

            obj.setAttribute(idTag,id);
            objs[id] = new Ani(obj);
            return objs[id];
        }else{
            return objs[obj.getAttribute(idTag)];   
        }
    }

    $.unset = function(obj){
        if(obj.hasAttribute(idTag)){
            delete objs[obj.getAttribute(idTag)];
            obj.removeAttribute(idTag);
        }
    }

    $.getObjs = function(){
        return objs;
    }
})(JObj.UI.JAnimate);