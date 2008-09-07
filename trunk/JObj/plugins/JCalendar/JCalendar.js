JObj.Plugin == undefined && (JObj.Plugin = {});
JObj.Plugin.JCalendar = {};

(function($){

    var Calendar = function(body,width){

        !width && (width = 200);

        var $ = this;

        var objs = {
            outline:null,
            body:( JObj.$(body) || document.body),
            table:null,
            yearLabel:null,
            monthLabel:null,
            yearSelect:null,
            monthSelect:null
        };

        $.onclick = null;
        $.onmonthchange = null;

        var callBack = {
            eventDateClick : null
        };

        var today = new Date();

        var vars = {
            tpl:'<table width="100%" border="0" cellspacing="0" cellpadding="5"><tbody class="tbHeader"><tr><td><a href="javascript:void(0)" class="pre"></a></td><td colspan="5"><div class="showYear"><label></label><select style="display:none"></select>&nbsp;/&nbsp;</div><div class="showMonth"><label></label><select style="display:none"></select></div></td><td style="cursor:pointer"><a href="javascript:void(0)" class="next"></a></td></tr><tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr></tbody><tbody class="tbBody"><tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr><tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr><tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr><tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr><tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr><tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr><tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr></tbody></table>',
            weekDayLabel:['M','T','W','T','F','S','S'],
            monthLabel:['1','2','3','4','5','6','7','8','9','10','11','12'],
            year:today.getFullYear(),
            month:today.getMonth(),
            date:today.getDate,
            eventDates:[],
            skin:"",
            width: !isNaN(width) ? width + "px" : (JObj.isRate(width) ? width : "auto")
        };

        var preMonth = function(){
            $.setMonth(vars.month - 1);
        }

        var nextMonth = function(){
            $.setMonth(vars.month + 1);
        }

        var checkEvent = function(dateStr){
            if(dateStr in vars.eventDates){
                return true;
            }else return false;
        }

        $.setMonth = function(month){
            var y = vars.year,m=vars.month;
            month = parseInt(month);
            vars.month = month;
            month < 0 && (vars.month = 11 ,vars.year--);
            month > 12 && (vars.month = 1 ,vars.year++);

            JObj.isFunction($.onmonthchange) && $.onmonthchange(vars.year,vars.month + 1,y,m + 1);

            //objs.yearLabel.innerHTML = vars.year + "<a href='javascript:void(0)' class='drop'></a>";
            //objs.monthLabel.innerHTML = vars.month + 1 + "<a href='javascript:void(0)' class='drop'></a>";
			objs.yearLabel.innerHTML = "<a href='javascript:void(0)' class='select'>" + vars.year + "<span class='drop'></span></a>";
			objs.monthLabel.innerHTML = "<a href='javascript:void(0)' class='select'>" + (vars.month + 1) + "<span class='drop'></span></a>";

            var startDate = new Date(vars.year,month,1);
            var start = startDate.getDay() - 1 + startDate.getDate() + 7;
            var end = (new Date(vars.year,month + 1,0)).getDate() + startDate.getDay() - 1 + 7;
            var preEnd = (new Date(vars.year, month,0)).getDate();

            var pre = {
                month:month - 1,
                year:vars.year
            };
            var next = {
                month:month + 1,
                year:vars.year
            };
            pre.month < 0 && (pre.month = 11 ,pre.year--);
            next.month > 11 && (next.month = 0 , next.year++);

            var cells = JObj.$tag("TD",objs.table.tBodies[1]),j = 1,cell;

            for(var i=start - 1;i<end /*&& (cell = cells[i])*/ ;i++){
                cell = cells[i];
                cell.innerHTML = "";
                cell.appendChild(document.createTextNode(j++));
                cell.className = j == today.getDate() + 1 && vars.month == today.getMonth() && today.getFullYear() == vars.year ? "today" : "currMonth";
                if(checkEvent(vars.year + "-" + (vars.month + 1) + "-" + (j - 1))){
                    cell.className += " haveEvent";
                }
            }

            for(i=start - 2;i>=0/* && (cell = cells[i])*/ ;i--){
                cell = cells[i];
                cell.innerHTML = "";
                cell.appendChild(document.createTextNode(preEnd --));
                cell.className = "preMonth";

                if(checkEvent(pre.year + "-" + (pre.month + 1) + "-" + (preEnd + 1))){
                    cell.className = "preMonth haveEvent";
                }
            }

            j = 1;
            for(i=end;i<cells.length /*cell = cells[i]*/ ;i++){
                cell = cells[i];
                cell.innerHTML = "";
                cell.appendChild(document.createTextNode(j++));
                cell.className = "nextMonth";
            }
        }

        var table_click = function(evt){
            evt = window.event || evt;
            var obj = evt.srcElement || evt.target;
            
            if( obj.tagName == "TD" && obj.parentNode.parentNode.className=="tbBody"){
                if(JObj.isFunction($.onclick)) {
                    var y = vars.year;
                    var m = obj.className.indexOf("preMonth") != -1 ? vars.month : ( obj.className.indexOf("nextMonth") != -1 ? vars.month + 2 : vars.month + 1);
                    m > 12 && (m = 1,y++);
                    m < 0 && (m = 12,y--);
                    $.onclick(y,m,obj.innerHTML,obj.className.indexOf("haveEvent") != -1);
                }
            }
        }

        var on_yearlabel_click = function(){
            this.style.display = "none";
            objs.yearSelect.style.display = "";
        }

        var on_yearselect_change = function(){
            objs.yearLabel.innerHTML = this.value;
            this.style.display = "none";
            objs.yearLabel.style.display = "";
            vars.year = parseInt(this.value) ;
            //alert(vars.year);
            $.setMonth(vars.month);
        }

        var on_monthlabel_click = function(){
            this.style.display = "none";
            objs.monthSelect.style.display = "";
        }

        var on_monthselect_change = function(){
            objs.monthLabel.innerHTML = this.value;
            this.style.display = "none";
            objs.monthLabel.style.display = "";

            $.setMonth( parseInt(this.value));
        }

        $.create = function(){
            objs.outline = JObj.$c("DIV");
            objs.body.insertBefore(objs.outline,objs.body.lastChild);
            objs.outline.style.width = vars.width;
            objs.outline.className = vars.skin;

            objs.outline.innerHTML = vars.tpl;
            objs.table = JObj.$tag("TABLE",objs.outline)[0];
            objs.table.onclick = table_click;

            var tr,td;
            tr = objs.table.rows[0];
            td = tr.cells[0];
            td.onclick = preMonth;
            td = tr.cells[2];
            td.onclick = nextMonth;

            var tmp = JObj.$tag("LABEL",objs.table.rows[0].cells[1]);
            objs.yearLabel = tmp[0];
            objs.monthLabel = tmp[1];
            objs.yearLabel.onclick = on_yearlabel_click;
            objs.monthLabel.onclick = on_monthlabel_click;

            tmp = JObj.$tag("SELECT",objs.table.rows[0].cells[1]);
            objs.yearSelect = tmp[0];
            objs.monthSelect = tmp[1];
            objs.yearSelect.onchange = on_yearselect_change;
            objs.monthSelect.onchange = on_monthselect_change;            

            tr = objs.table.rows[1];
            for(var i=0;td = tr.cells[i];i++){
                td.appendChild( document.createTextNode( vars.weekDayLabel[i] ));
            }

            $.setMonth(vars.month);

            for(i=1971;i<2050;i++){
                JObj.Browser.ie && objs.yearSelect.add(new Option(i,i));
                !JObj.Browser.ie && (objs.yearSelect[objs.yearSelect.length] = new Option(i,i));
            }

            var label;
            for(i=0;i<12 && (label = vars.monthLabel[i]);i++){
                JObj.Browser.ie && objs.monthSelect.add(new Option(label,i));
                !JObj.Browser.ie && (objs.monthSelect[objs.monthSelect.length] = new Option(label,i)) 
            }
        }
        
        $.setSkin = function(skin){
            vars.skin = skin;
            if(objs.outline)
                objs.outline.className = skin;
        }

        /**
         * dateArr 的元素格式定义为：2008-9-16 不补两位
         * @param dates object {'2008-9-16':1,'2008-9-1':0},是1是0都无所谓，可以是任何其它的东东，比如字符串 true 或 false，只要日期出现，就说明这一天有事件
         * @param fun function
         */
        $.setEventDate = function(dates,fun){
            vars.eventDates = dates;
            callBack.eventDateClick = fun;
        }
    }

    $.getInstance = function(body,width){
        JObj.Loader.loadCss(JObj.path + "/plugins/JCalendar/skinDefault/skin.css");
        var calendar = new Calendar(body,width);
        calendar.setSkin("JCalendarDefault");
        return calendar;
    }

})(JObj.Plugin.JCalendar);