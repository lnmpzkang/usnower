JObj.UI == undefined && (JObj.UI = {});

JObj.UI.JShortcut = {};

(function($) {

    var KEYS = {
        48:"0", 49:"1", 50:"2", 51:"3", 52:"4", 53:"5", 54:"6", 55:"7", 56:"8",
        57:"9", 59:";", 61:"=", 65:"a", 66:"b", 67:"c", 68:"d",
        69:"e", 70:"f", 71:"g", 72:"h", 73:"i", 74:"j", 75:"k", 76:"l", 77:"m",
        78:"n", 79:"o", 80:"p", 81:"q", 82:"r", 83:"s", 84:"t", 85:"u", 86:"v",
        87:"w", 88:"x", 89:"y", 90:"z", 107:"+", 109:"-", 110:".", 188:",",
        190:".", 191:"/", 192:"'", 219:"[", 220:"\\", 221:"]", 222:"\"",

        8:"backspace",  9:"tab",   13:"return",    19:"pause",  27:"esc",  32:"space",
        33:"pageup",  34:"pagedown",  35:"end",     36:"home",   37:"left",   38:"up",
        39:"right",  40:"down",   44:"printscreen",   45:"insert",  46:"delete",
        112:"f1",   113:"f2",   114:"f3", 115:"f4",  116:"f5",   117:"f6",   118:"f7",
        119:"f8",   120:"f9",   121:"f10", 122:"f11",  123:"f12",
        144:"numlock",  145:"scrolllock"
    };

    var map = {};

        var dispatch = function (evt) {
            evt = evt || event;
            var keyname;
            //alert(evt.srcElement || evt.target) ;

            if (evt.type == "keydown") {
                var code = evt.keyCode;
                if (code == 16 || code == 17 || code == 18) return;
                keyname = KEYS[code];

                var modifiers = "";

                if (keyname) {
                    if (evt.altKey) modifiers += "alt_";
                    if (evt.ctrlKey) modifiers += "ctrl_";
                    if (evt.shiftKey) modifiers += "shift_";
                } else {
                    return;
                }
            } else if (evt.type == "keypress") {
                if (evt.altKey || evt.ctrlKey) return;
                if (evt.charCode != undefined && evt.charCode == 0) return;
                var code = evt.charCode || evt.keyCode;

                keyname = String.fromCharCode(code);
                var lowercase = keyname.toLowerCase();
                if (keyname != lowercase) {
                    keyname = lowercase;    // Use the lowercase form of the name
                    modifiers = "shift_";   // and add the shift modifier.
                }
            }

            var shortCut = (modifiers + keyname).toUpperCase();
            //alert(shortCut)
            var target = evt.srcElement || evt.target;
            //alert(target);
            var id = JObj.getUniqueId(target);

            if(!(id in map) || !(shortCut in map[id])) return ;
            
            var func = map[id][shortCut].fun;

            if (JObj.isFunction(func)) {
                func(target, shortCut, evt);
                if (map[id][shortCut].doSysShortCut === false) {
                    switch (JObj.Browser.name) {
                        case "IE":
                            evt.returnValue = false;
                            evt.keyCode = 0;
                            break;
                        case "Firefox":
                            evt.preventDefault();
                            break;
                        case "Opera":
                            evt.returnValue = false;
                            evt.preventDefault();
                            break;
                        case "Safari":
                            evt.preventDefault();
                            evt.returnValue = false;
                    }
                }
            }
        }    

    $.bind = function(obj,sc,func,doSysSc){
        obj = JObj.$(obj);

        if(JObj.Browser.ie && ( obj.onkeypress === undefined || obj.onkeydown === undefined)){
            alert("Not Support " + sc + " on binding object");
            return;
        }

        sc = sc.toUpperCase();
        
        var id = JObj.getUniqueId(obj);

        if(!(id in map)){
            map[id] = {};
        }

        map[id][sc] = {};
        map[id][sc].fun = func;
        doSysSc === false && (map[id][sc].doSysShortcut = false);

        JObj.addEvent(obj,"onkeydown",dispatch);
        JObj.addEvent(obj,"onkeypress",dispatch);        
    }

    $.unBind = function(obj,sc){
        if(!obj){
            map = {};
            return;
        };
        
        var id = JObj.getUniqueId(obj);
        if(!(id in map)) return;

        if(!sc)
            delete map[id];
        else
            delete map[id][sc];

        JObj.removeEvent(obj,"onkeydown",dispatch)
        JObj.removeEvent(obj,"onkeypress",dispatch)
    }
    
})(JObj.UI.JShortcut);