JObj.use("ui");
JObj.use("JShortcut");

JObj.Plugin == undefined && (JObj.Plugin = {});
JObj.Plugin.JSuggest = {};

(function($){

    var layers = {};

    var sc = JObj.UI.JShortcut;

    var on_press_up = function(obj,a){
        if(a.previousSibling) a.previousSibling.focus();
        else{
            layers[JObj.getUniqueId(obj)].style.display = "none";
            obj.focus();
        }
    }

    var on_press_down = function(obj,a){
        if(a.nextSibling) a.nextSibling.focus();
    }

    var on_press_esc = function(obj){
        //obj.focus();
        obj.select();// ie 下，如果只是 focus的话，下面的 display = none 不起作用。
        layers[JObj.getUniqueId(obj)].style.display = "none";
    }

    var sort = function(a,b){
        return a[2] - b[2];
    }

    var setValue = function(obj,text,value,onchange){
        obj.value = text;
        JObj.isFunction(onchange) && onchange(text,value);
        layers[JObj.getUniqueId(obj)].style.display = "none";
        obj.focus();
    }
    var listernFun = function(obj,arr,onchange){
        if(obj.value.trim() == "") return;
        var tmp = [],o;
        for(o in arr){
            if(o.indexOf(obj.value) > -1) {
                tmp.push([o, arr[o], o.indexOf(obj.value)]);
            }
        }

        tmp.sort(sort);

        var id = JObj.getUniqueId(obj);
        if(id in layers) layers[id].innerHTML = "";

        //layer.innerHTML = "";
        var i=0,item,li,a;
        for(i;item = tmp[i];i++){
            !(id in layers) &&
                    (
                    layers[id] = JObj.$c("DIV"),
                    layers[id].style.display = "none",
                    layers[id].style.position = "absolute",
                    layers[id].className = "JSuggestDefault",
                    document.body.appendChild(layers[id])
                    );
            
            a = JObj.$c("A");a.innerHTML = item[0].replace(obj.value,"<font color='#ff0000'>" + obj.value + "</font>");
            a.href = "javascript:void(0)";
            a.onclick = JObj.doFunction(setValue,obj,item[0],item[1],onchange);
            layers[id].appendChild(a);
            sc.bind(a,"DOWN",JObj.doFunction(on_press_down,obj,a));
            sc.bind(a,"UP",JObj.doFunction(on_press_up,obj,a));
            sc.bind(a,"ESC",JObj.doFunction(on_press_esc,obj),false);
        }

        var pos = JObj.UI.JPos.getAbsPos(obj);
        
        i > 0 && (
                layers[id].style.display = "",
                layers[id].style.top = pos.y + obj.offsetHeight + "px",
                layers[id].style.left = pos.x + "px",
                layers[id].style.width = obj.offsetWidth + "px"
        );
    } 

    var on_press_down1 = function(obj){
        var id = JObj.getUniqueId(obj);
        if(layers[id]){
        //if(layer.getElementsByTagName("A")[0]){
            layers[id].style.display = "";
            layers[id].getElementsByTagName("A")[0].focus();
        }
    }

    $.listen = function(obj,arr,onchange){
        var obj = JObj.$(obj);
        if(obj.type != "text") return false;

        JObj.Loader.loadCss(JObj.path + "plugins/JSuggest/default.css");
        //layer.className = "JSuggestDefault";

        JObj.Browser.ie && obj.attachEvent("onpropertychange",JObj.doFunction(listernFun,obj,arr,onchange));
        !JObj.Browser.ie && obj.addEventListener("input",JObj.doFunction(listernFun,obj,arr,onchange),false);

        sc.bind(obj,"DOWN",on_press_down1,obj);
    }

    $.setSkin = function(skin){
        layer.className = skin;
    }

})(JObj.Plugin.JSuggest);