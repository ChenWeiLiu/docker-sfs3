{{* $Id: health_input_accident.tpl 5310 2009-01-10 07:57:56Z hami $ *}}

{{dhtml_calendar_init src="`$SFS_PATH_HTML`javascripts/calendar.js" setup_src="`$SFS_PATH_HTML`javascripts/calendar-setup.js" lang="`$SFS_PATH_HTML`javascripts/calendar-tw.js" css="`$SFS_PATH_HTML`javascripts/calendar-brown.css"}}
<script>
function flatSelected(cal, date) {
  var el = document.getElementById("show_time");
  el.value = date;
  var el = document.getElementById("sign_time");
  el.value = date;
}
function showFlatCalendar() {
  var parent = document.getElementById("display");
  var cal = new Calendar(0, null, flatSelected);

  cal.weekNumbers = false;
  cal.showsTime = true;
  cal.setDateFormat("%Y-%m-%d %H:%M:%S");
  cal.create(parent);
  cal.show();
}
function sel_input(i) {
	var r = document.getElementById("r");
	switch(i){
		case 0:
			r.innerHTML = '<div id="display" style="width: 200; float: right; clear: both;"></div>';
			showFlatCalendar();
			break;
	}
}
function chk() {
	var pla=0;
	var par=0;
	var sta=0;
	var att=0;
	for (var i=0, len=document.myform.elements.length; i< len; i++) {
		a=document.myform.elements[i];
		if (a.checked==true) {
			b=a.id.substr(0,7);
			if (b=="sel_pla") pla=1;
			else if (b=="sel_par") par=1;
			else if (b=="sel_sta") sta=1;
			else if (b=="sel_att") att=1;
		}
		if (a.name=="temp") {
			if (a.value!="" && (a.value<14 || a.value>47)) {
				alert("人類體溫範圍應於攝氏 14 ~ 47 度之間");
				a.value="";
				a.focus();
				return;
			}
		}
	}
	if (pla==0) {
		alert("未選地點");
		return;
	}
	if (par==0) {
		alert("未選部位");
		return;
	}
	if (sta==0) {
		alert("未選狀況");
		return;
	}
	if (att==0) {
		alert("未選處置方式");
		return;
	}
	document.getElementById("sure").value=1;
	document.myform.submit();
}
function cal_min() {
	a=document.getElementById("hour").value * 60 + document.getElementById("min").value * 1;
	if (a<0) {
		alert("休息觀察時間不可為負值");
		document.getElementById("hour").value="";
		document.getElementById("min").value="";
		document.getElementById("hour").focus();
		return;
	}
	b=a % 60;
	document.getElementById("min").value = b;
	document.getElementById("hour").value = (a-b) / 60;
	document.getElementById("mins").value=a;
}
function del(a) {
	if (confirm('確定刪除此筆資料?')) {
		document.getElementById('act').value='del';
		document.getElementById('act_id').value=a;
		document.myform.submit();
	}
}
function edit(a) {
	document.getElementById('act').value='edit';
	document.getElementById('act_id').value=a;
	document.myform.submit();
}
</script>

{{assign var=sn value=$smarty.post.student_sn}}
{{assign var=year_seme value=$smarty.post.year_seme}}
{{if $rowdata}}
<input type="button" value="確定修改" OnClick="chk();">
<input type="button" value="重新輸入" OnClick="this.form.submit();">
{{else}}
<input type="button" value="確定新增" OnClick="chk();">
{{/if}}
<input type="submit" value="取消">
<input type="hidden" id="sure" name="save">
<input type="hidden" id="act" name="act">
<input type="hidden" id="act_id" name="update[del][0]">
<input type="hidden" name="update[new][{{$sn}}][{{$year_seme}}][health_accident_record][update_id]" value="{{$rowdata.id}}">
<input type="hidden" id="sign_time" name="update[new][{{$sn}}][{{$year_seme}}][health_accident_record][sign_time]" value="{{if $rowdata.sign_time}}{{$rowdata.sign_time}}{{else}}{{$smarty.now|date_format:"%Y-%m-%d %H:%M:%S"}}{{/if}}">
<input type="hidden" id="mins" name="update[new][{{$sn}}][{{$year_seme}}][health_accident_record][obs_min]" value="{{$rowdata.obs_min}}">
<input type="checkbox" id="ser1" name="serious" OnChange="document.getElementById('ser2').checked=!document.getElementById('ser2').checked;"><span style="font-size:10pt;">列入重大傷病</span>
<table cellspacing="0" cellpadding="0"><tr><td style="vertical-align:top">
<table bgcolor="#9ebcdd" cellspacing="1" cellpadding="2" class="small" style="font-size:10pt;">
{{assign var=hour value=$rowdata.obs_min/60|intval}}
{{assign var=minute value=$rowdata.obs_min%60}}
<tr style="background-color:#c4d9ff;text-align:center;">
<td nowrap>處理時間</td>
<td style="background-color:#f4feff;"><input type="text" size="19" maxlength="19" id="show_time" value="{{if $rowdata.sign_time!="0000-00-00 00:00:00" and $rowdata.sign_time!=""}}{{$rowdata.sign_time}}{{else}}{{if $default_c_date}}{{$default_c_date}}{{else}}{{$smarty.now|date_format:"%Y-%m-%d %H:%M:%S"}}{{/if}}{{/if}}" disabled style="color:black;text-align:center;width:110px;"></td>
<td>休息觀察時間</td>
<td style="background-color:#f4feff;"><input type="text" id="hour" maxlength="4" style="width:30px;" OnChange="cal_min();" value="{{if $hour}}{{$hour}}{{/if}}">小時 <input type="text" id="min" maxlength="2" style="width:30px;" OnChange="cal_min();" value="{{if $minute}}{{$minute}}{{/if}}">分鐘</td>
<td>體溫</td>
<td style="background-color:#f4feff;"><input type="text" name="update[new][{{$sn}}][{{$year_seme}}][health_accident_record][temp]" maxlength="4" style="width:30px;" value="{{if $rowdata.temp!="0.0" && $rowdata.temp!=""}}{{$rowdata.temp}}{{/if}}"><sup>o</sup>C</td>
</tr>
<tr style="background-color:#c4d9ff;text-align:center;">
<td>地　　點</td>
<td colspan="5" style="background-color:#f4feff;">
<table style="width:100%;">{{assign var=i value=0}}{{foreach from=$aplace item=d key=k}}{{if $i % 5 == 0}}<tr>{{/if}}<td><input type="radio" id="sel_pla_{{$k}}" name="update[new][{{$sn}}][{{$year_seme}}][health_accident_record][place_id]" value="{{$k}}" {{if $rowdata.place_id==$k}}checked{{/if}}>{{$d}}</td>{{assign var=i value=$i+1}}{{if $i % 5 == 0}}</tr>{{/if}}{{/foreach}}</table>
</td>
</tr>
<tr style="background-color:#c4d9ff;text-align:center;">
<td>原　　因</td>
<td colspan="5" style="background-color:#f4feff;">
<table style="width:100%;">{{assign var=i value=0}}{{foreach from=$areason item=d key=k}}{{if $i % 5 == 0}}<tr>{{/if}}<td><input type="radio" id="sel_rea_{{$k}}" name="update[new][{{$sn}}][{{$year_seme}}][health_accident_record][reason_id]" value="{{$k}}" {{if $rowdata.reason_id==$k}}checked{{/if}}>{{$d}}</td>{{assign var=i value=$i+1}}{{if $i % 5 == 0}}</tr>{{/if}}{{/foreach}}</table>
</td>
</tr>
<tr style="background-color:#c4d9ff;text-align:center;">
<td>部　　位</td>
<td colspan="5" style="background-color:#f4feff;">
<table style="width:100%;">{{assign var=i value=0}}{{foreach from=$apart item=d key=k}}{{if $i % 5 == 0}}<tr>{{/if}}<td><input type="checkbox" id="sel_par_{{$k}}" name="update[new][{{$sn}}][{{$year_seme}}][health_accident_part_record][part_id][{{$k}}]" {{php}} if (in_array($this->_tpl_vars['k'],$this->_tpl_vars['rowdata']['part_id'])) echo "checked";{{/php}}>{{$d}}</td>{{assign var=i value=$i+1}}{{if $i % 5 == 0}}</tr>{{/if}}{{/foreach}}</table></td>
</tr>
<tr style="background-color:#c4d9ff;text-align:center;">
<td>狀　　況</td>
<td colspan="5" style="background-color:#f4feff;">
<table style="width:100%;">{{assign var=i value=0}}{{foreach from=$astatus item=d key=k}}{{if $i % 5 == 0}}<tr>{{/if}}<td><input type="checkbox" id="sel_sta_{{$k}}" name="update[new][{{$sn}}][{{$year_seme}}][health_accident_status_record][status_id][{{$k}}]" {{php}} if (in_array($this->_tpl_vars['k'],$this->_tpl_vars['rowdata']['status_id'])) echo "checked";{{/php}}>{{$d}}</td>{{assign var=i value=$i+1}}{{if $i % 5 == 0}}</tr>{{/if}}{{/foreach}}</table></td>
</tr>
<tr style="background-color:#c4d9ff;text-align:center;">
<td>處置方式</td>
<td colspan="5" style="background-color:#f4feff;">
<table style="width:100%;">{{assign var=i value=0}}{{foreach from=$aattend item=d key=k}}{{if $i % 5 == 0}}<tr>{{/if}}<td><input type="checkbox" id="sel_att_{{$k}}" name="update[new][{{$sn}}][{{$year_seme}}][health_accident_attend_record][attend_id][{{$k}}]" {{php}} if (in_array($this->_tpl_vars['k'],$this->_tpl_vars['rowdata']['attend_id'])) echo "checked";{{/php}}>{{$d}}</td>{{assign var=i value=$i+1}}{{if $i % 5 == 0}}</tr>{{/if}}{{/foreach}}</table></td>
</tr>
<tr style="background-color:#c4d9ff;text-align:center;">
<td>其他說明</td>
<td colspan="5" style="background-color:#f4feff;">
<textarea rows="3" cols="60" name="update[new][{{$sn}}][{{$year_seme}}][health_accident_record][memo]">{{$rowdata.memo}}</textarea>
</td>
</tr>
</table>
</td><td>&nbsp;</td><td style="vertical-align:top">
<table bgcolor="#9ebcdd" cellspacing="1" cellpadding="2" class="small">
<tr style="background-color:#c4d9ff;text-align:center;">
<td><div id="r"></div></td>
</tr>
</table>
</td>
<tr>
<td colspan="3" style="text-align:center;">
<div style="text-align:left;">
{{if $rowdata}}
<input type="button" value="確定修改" OnClick="chk();">
<input type="button" value="重新輸入" OnClick="this.form.submit();">
{{else}}
<input type="button" value="確定新增" OnClick="chk();">
{{/if}}
<input type="submit" value="取消">
<input type="checkbox" id="ser2"  OnChange="document.getElementById('ser1').checked=!document.getElementById('ser1').checked;"><span style="font-size:10pt;">列入重大傷病</span>
</div>
<table cellspacing="1" cellpadding="2" class="small" style="background-color:#126CCC;font-size:10pt;width:100%;">
<tr style="color:white;text-align:center;"><td colspan="9">本學期記錄</td></tr>
<tr style="background-color:white;text-align:center;">
<td>處理時間</td>
<td>觀察時間</td>
<td>體溫</td>
<td>地點</td>
<td>原因</td>
<td>部位</td>
<td>狀況</td>
<td>處置方式</td>
<td>功能選項</td>
</tr>
{{foreach from=$health_data->health_data.$sn.$year_seme.accident item=d}}
<tr style="background-color:{{cycle values="#C0CAEC,white"}};text-align:center;">
<td>{{$d.sign_time}}</td>
<td>{{if $d.obs_min>0}}{{$d.obs_min}}分{{else}}未記錄{{/if}}</td>
<td>{{if $d.temp>0}}{{$d.temp}}<sup>o</sup>C{{else}}未記錄{{/if}}</td>
{{assign var=i value=$d.place_id}}
<td>{{$aplace.$i}}</td>
{{assign var=i value=$d.reason_id}}
<td>{{$areason.$i}}</td>
<td>{{foreach from=$d.part_id item=dd}}{{$apart.$dd}}<br>{{/foreach}}</td>
<td>{{foreach from=$d.status_id item=dd}}{{$astatus.$dd}}<br>{{/foreach}}</td>
<td>{{foreach from=$d.attend_id item=dd}}{{$aattend.$dd}}<br>{{/foreach}}</td>
<td><a href="#" OnClick="edit({{$d.id}});">編修</a> <a href="#" OnClick="del({{$d.id}});">刪除</a> <span title="{{if $d.memo}}{{$d.memo}}{{else}}無說明內容{{/if}}" style="cursor:pointer;color:blue;">說明</span></td>
</tr>
{{foreachelse}}
<tr style="background-color:#C0CAEC;text-align:center;">
<td colspan="9">本學期無記錄</td>
</tr>
{{/foreach}}
</table>
</td>
</tr>
</tr></table>
