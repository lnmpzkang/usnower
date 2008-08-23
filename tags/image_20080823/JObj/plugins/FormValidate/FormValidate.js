if (typeof(JObj.Plugin) != "object") JObj.Plugin = {};

JObj.Plugin.FormValidate = {};
(function($) {
    var Require = /.+/;
    var Email = /^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/;
    var Phone = /^((\(\d{2,3}\))|(\d{3}\-))?(\(0\d{2,3}\)|0\d{2,3}-)?[1-9]\d{6,7}(\-\d{1,4})?$/;
    var Url = /^http:\/\/[A-Za-z0-9]+\.[A-Za-z0-9]+[\/=\?%\-&_~`@[\]\':+!]*([^<>\"\"])*$/;
    var QQ = /^[1-9]\d{4,8}$/;
    var Num = "$.checkNumber('Number',obj,rule)";
    var Int = "$.checkNumber('Int',obj,rule)";
    var Double = "$.checkNumber('Double',obj,rule)";
    var English = /^[A-Za-z]+$/;
    var Chinese = /^[\u0391-\uFFE5]+$/;
    var Username = "$.checkUsername(obj,rule)";
    var Filter = "$.doFilter(obj,rule)";
    var Date = "$.isDate(obj, rule)";
    var Repeat = "$.repeat(form,obj,rule)";
    var Range = "getAttribute('min') < (value|0) && (value|0) < getAttribute('max')";
    var Compare = "$.compare(value,getAttribute('operator'),getAttribute('to'))";
    var Custom = "$.regexp.test(obj.value)";
    var Group = "$.mustChecked(obj,rule)";

    $.extend = function(form, tag, rule) {
        //先取出以存在的规则，以免重复
        var names = {},tmp;
        for (var i = 0; tmp = rule[i]; i++) {
            names[tmp.name] = true;
        }
        
        var item,orgRule,newRule,o;
        for (var i = 0; item = form[i]; i++) {
            //如果 name 不存在于 以存在的规则里，并且设置了 tag 所指的属性
            if (!(item.name in names) && item.getAttribute(tag) != "" && item.getAttribute(tag) != null) {
                    if(rule[item.getAttribute(tag)] == undefined) continue;//如果要 copy 的规则不存在，

                    orgRule = rule[item.getAttribute(tag)];
                    newRule = {};//newRule = {} 不可放在 for 外面定义，否则在IE下还是地址引用
                    // copy rule,因为javascript 把对象赋值给一个变量 是 地址引用，所以，只有能用下面这个办法。
                    for(o in orgRule){
                        newRule[o] = orgRule[o];
                    }
                    newRule.name = item.name;
                    rule.push(newRule);
                    names[item.name] = true;
            }
        }
    }

    $.onPass = null;
    $.onUnpass = null;

    var errList = [];

    $.validate = function(form, rules, mode) {
        errList.length = 0;

        mode = mode || 1;
        /*----------------------------
          1,全部检查,并alert
          2,一个一个检查，并alert,focus
          3,调用自定义事件 onPassFun验证通过时调用，onUnpassFun未通过时调用。
          4,同3,但是当验证不通过时，立即停止向下检查。
          ----------------------------*/

        var f,rule;
        for (var i = 0; i < rules.length; i++) {
            rule = rules[i];
            if (rule == undefined) continue;

            f = $.check(form, rule);

            if (mode == 2 && f === false) {
                alert(rule.msg);
                try {
                    form[rule.name].select();
                } catch(e) {
                }
                ;
                try {
                    form[rule.name].focus();
                } catch(e) {
                    form[rule.name][0].focus();
                }
                return false;
            } else if (mode == 3 && f === true) {
                typeof($.onPass) == "function" ? $.onPass(form[rule.name], rule) : null;
            } else if (mode == 3 && f === false) {
                $.addError(rule);
                typeof($.onUnpass) == "function" ? $.onUnpass(form[rule.name], rule) : null;
            }else if(mode == 4 && f === false){
                $.addError(rule);
                if(typeof($.onUnpass) == "function"){
                    $.onUnpass(form[rule.name],rule);
                    return false;
                }
            }
        }

        if (mode == 1 && errList.length != 0) {
            alert($.errToString());
            return false;
        } else if (mode == 1) {
            return true;
        } else if (mode == 3 && errList.length != 0) {
            return false;
        } else if (mode == 3) {
            return true;
        }
    }


    $.check = function(form, rule) {
        if (typeof rule.name == "string")
            obj = form[rule.name];
        else
            return null;
		//alert(obj.name,obj.form);
        if (obj == undefined || obj.form == null) return null;// Note :not return false! ff 下，如果删除了一个表单项，form[itemname]还是存在的，但是 item.form 为 null,虽然 item.parentNode 也为 null,但是它却不能做判断条件，不明白！
		//如果不是必填项，radio,checkbox,option可不向下检查。
        //如果不是必填项，除radio,checkbox,option外，如果值为空的话，可不向下检查。
        if (!(rule.required == true || rule.type == "Require") && (obj.length != undefined || obj.value == "")) {
            //if(rule.required != true && (obj.length != undefined || obj.value == "")){
            //alert(rule.name + " " + rule.required + " " + obj.length);
            return true;
        } else if (rule.required == true && obj.length == undefined && !eval(Require.test(obj.value.trim()))) {
            $.addError(rule);
            return false;
        }

        if (rule.type == undefined && rule.required !== true) return;
        var type = rule.type

        switch (type) {
            case "Date" :
            case "Repeat" :
            case "Range" :
            case "Compare" :
            case "Custom" :
            case "Group" :
            case "Limit" :
            case "LimitB" :
            case "SafeString" :
            case "Filter" :
            case "Int":
            case "Double":
            case "Num":
            case "Username":
            case "Repeat":
                if (!eval(eval(type))) {
                    $.addError(rule);
                    return false;
                } else return true;
                break;
            default :
                if (type && !eval(type).test(obj.value)) {
                    $.addError(rule);
                    return false;
                } else {
                    if (type == "Require" || rule.required == true) {
                        var min = (typeof rule.min != "number") ? 1 : rule.min;
                        var max = (typeof rule.max != "number") ? "" : rule.max;
                        var reg = new RegExp("^.{" + min + "," + max + "}$");

						//Textarea 换行
                        //IE,opera /r
                        //FF,Safari /n
                        if (reg.test(obj.value.replace(/[\r|\n]/g, "").trim())) {
                            return true;
                        } else {
                            $.addError(rule);
                            return false;
                        }
                    } else
                        return true;
                }
                break;
        }
    }


    $.isDate = function(obj, rule) {
        var r = obj.value.match(/^(\d{1,4})(-|\/)(\d{1,2})\2(\d{1,2})$/);
        if (r == null) return false;
        var d = new window.Date(r[1], r[3] - 1, r[4]);
        return (d.getFullYear() == r[1] && (d.getMonth() + 1) == r[3] && d.getDate() == r[4]);
    }


    $.mustChecked = function(obj, rule) {
        var o;
        var min_ = rule.min || 1 ,max_ = rule.max || obj.length;

        var cn = 0;

        for (var i = 0; o = obj[i]; i++) {
            if (o.checked != undefined) {//Radio,CheckBox
                if (o.checked)
                    cn++;
            } else {
                var noSelected = rule.noSelected || "";
                if (o.selected && noSelected == o.value) return false;
                else if (o.selected) cn++;
            }
        }

        if (cn >= min_ && cn <= max_) return true;
        else return false;
    }


    $.checkNumber = function(type, obj, rule) {
        var f;
        switch (type) {
            case "Int":
                f = /^[-\+]?\d+$/.test(obj.value);
                break;
            case "Double":
                f = /^[-\+]?\d+(\.\d+)?$/.test(obj.value);
                break;
            case "Number":
                f = !isNaN(Number(obj.value));
                break;
        }

        if (!f) return false;

        var min = (typeof rule.min != "number") ? Number.NEGATIVE_INFINITY : rule.min;
        var max = (typeof rule.max != "number") ? Number.POSITIVE_INFINITY : rule.max;


		//var min = rule.min | Number.NEGATIVE_INFINITY, max = rule.max | Number.POSITIVE_INFINITY;
        // 0 | null = 0   'a' | -2 = -2   -2 | 'a' = -2    'a' | 'a' = 0
        //用这个来判断数字。还可以。

        if (obj.value >= min && obj.value <= max)
            return true;
        else
            return false;
    }


    $.checkUsername = function(obj, rule) {
        var min = rule.min || 3 ,max = rule.max || "";
        var reg = new RegExp("^\\w{" + min + "," + max + "}$", "i");
        return reg.test(obj.value);
    }

    $.doFilter = function(obj, rule) {
        return new RegExp("^.+\.(?=EXT)(EXT)$".replace(/EXT/g, rule.accept.split(/\s*,\s*/).join("|")), "gi").test(obj.value);
    }

    $.repeat = function(form, obj, rule) {
        return form[rule.to].value == obj.value;
    }

    $.addError = function(rule) {
        errList.push(rule);
    }

    $.errToString = function() {
        var t = "",rule;
        for (var i = 0; rule = errList[i]; i++)
            t += (i + 1) + ". " + rule.msg + "\n";

        return t;
    }
})(JObj.Plugin.FormValidate)