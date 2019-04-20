{{* $Id: class_health_influenza_form.tpl 9240 2018-05-04 03:25:04Z igogo $ *}}
{{include file="$SFS_TEMPLATE/header.tpl"}}
{{include file="$SFS_TEMPLATE/menu.tpl"}}
{{php}}
if (count($this->_tpl_vars['rowdata'])>0) {
	$this->_tpl_vars['sym']=explode("@@@",$this->_tpl_vars['rowdata']['sym_str']);
	$tmp_arr=explode("@@@",$this->_tpl_vars['rowdata']['oth_txt']);
	foreach($tmp_arr as $v) {
		$tmp_arr2=explode("###",$v);
		$this->_tpl_vars['oth_txt'][$tmp_arr2[0]]=$tmp_arr2[1];
	}
}
{{/php}}

{{dhtml_calendar_init src="`$SFS_PATH_HTML`javascripts/calendar.js" setup_src="`$SFS_PATH_HTML`javascripts/calendar-setup.js" lang="`$SFS_PATH_HTML`javascripts/calendar-tw.js" css="`$SFS_PATH_HTML`javascripts/calendar-brown.css"}}
<script>
<!--
function showCalendar(id, format, showsTime, showsOtherMonths) {
	var el = document.getElementById(id);
	if (_dynarch_popupCalendar != null) {
		_dynarch_popupCalendar.hide();
	} else {
		var cal = new Calendar(1, null, selected, closeHandler);
		cal.weekNumbers = false;
		cal.showsTime = false;
		cal.time24 = (showsTime == "24");
		if (showsOtherMonths) {
			cal.showsOtherMonths = true;
		}
		_dynarch_popupCalendar = cal;
		cal.setRange(2000, 2030);
		cal.create();
	}
	_dynarch_popupCalendar.setDateFormat(format);
	_dynarch_popupCalendar.parseDate(el.value);
	_dynarch_popupCalendar.sel = el;
	_dynarch_popupCalendar.showAtElement(el.nextSibling, "Br");

	return false;
}
function closeHandler(cal) {
	cal.hide();
	_dynarch_popupCalendar = null;
}
function selected(cal, date) {
	cal.sel.value = date;
}
function go(){
	if (document.myform.student_sn.value=='')
		alert("未選學生");
	else {
		document.getElementById('inf_date').disabled=false;
		document.getElementById('diag_date').disabled=false;
		document.getElementById('chk_date').disabled=false;
		document.myform.submit();
	}
}
-->
</script>

<table border="0" cellspacing="1" cellpadding="2" width="100%" bgcolor="#cccccc">
<form name="myform" method="post" action="{{$smarty.server.SCRIPT_NAME}}">
<tr>
<td bgcolor="white">
<input type="button" value="確定{{if $rowdata}}修改{{else}}新增{{/if}}" OnClick="go();">
<input type="hidden" name="act" value="sure">
{{if $rowdata}}<input type="hidden" name="student_sn" value="{{$smarty.post.student_sn}}">{{/if}}
<br>
<table bgcolor="#9ebcdd" cellspacing="1" cellpadding="3" class="small">
<tr style="background-color:white;text-align:center;">
<td style="background-color:#E6E9F9;">姓名</td>
<td style="text-align:left;">{{$stud_menu}}</td>
</tr>
<tr style="background-color:white;text-align:center;">
<td style="background-color:#E6E9F9;">發病日期</td>
<td style="text-align:left;">
<input type="text" name="inf_date" id="inf_date" size="10" value="{{if $rowdata.dis_date}}{{$rowdata.dis_date}}{{else}}{{$smarty.now|date_format:"%Y-%m-%d"}}{{/if}}" disabled style="color:black;">{{if $rowdata.dis_date==""}}<input type="reset" value="選擇日期" onclick="return showCalendar('inf_date', '%Y-%m-%d', '12');">{{/if}}
</td>
</tr>
<tr style="background-color:white;text-align:center;">
<td style="background-color:#E6E9F9;">症　　狀</td>
<td style="text-align:left;">
<input type="checkbox" name="sym[1]" {{if (in_array(1,$sym))}}checked{{/if}}>發高燒
<input type="checkbox" name="sym[2]" {{if (in_array(2,$sym))}}checked{{/if}}>肌肉酸痛
<input type="checkbox" name="sym[3]" {{if (in_array(3,$sym))}}checked{{/if}}>頭痛
<input type="checkbox" name="sym[4]" {{if (in_array(4,$sym))}}checked{{/if}}>極度倦怠
<input type="checkbox" name="sym[5]" {{if (in_array(5,$sym))}}checked{{/if}}>咳嗽
<input type="checkbox" name="sym[6]" {{if (in_array(6,$sym))}}checked{{/if}}>呼吸喘
<input type="checkbox" name="sym[7]" {{if (in_array(7,$sym))}}checked{{/if}}>喉嚨痛
</td>
</tr>
<tr style="background-color:white;text-align:center;">
<td style="background-color:#E6E9F9;">請假狀況</td>
<td style="text-align:left;">
<input type="radio" name="status" value="A" {{if $rowdata.status=="A"}}checked{{/if}}>生病仍上課
<input type="radio" name="status" value="B" {{if $rowdata.status=="B"}}checked{{/if}}>生病在家休息
<input type="radio" name="status" value="C" {{if $rowdata.status=="C"}}checked{{/if}}>生病住院
</td>
</tr>
<tr style="background-color:white;text-align:center;">
<td style="background-color:#E6E9F9;">就診狀況</td>
<td style="text-align:left;">
就診日 <input type="text" name="diag_date" id="diag_date" size="10" value="{{if $rowdata.diag_date!="0000-00-00"}}{{$rowdata.diag_date}}{{/if}}" disabled style="color:black;"><input type="reset" value="選擇日期" onclick="return showCalendar('diag_date', '%Y-%m-%d', '12');">
就診醫院 <input type="text" name="diag_hos" value="{{$rowdata.diag_hos}}">
醫師診斷病名 <input type="text" name="diag_name" value="{{$rowdata.diag_name}}">
</td>
</tr>
<tr style="background-color:white;text-align:center;">
<td style="background-color:#E6E9F9;">檢驗狀況</td>
<td style="text-align:left;">
採檢日 <input type="text" name="chk_date" id="chk_date" size="10" value="{{if $rowdata.chk_date!="0000-00-00"}}{{$rowdata.chk_date}}{{/if}}" disabled style="color:black;"><input type="reset" value="選擇日期" onclick="return showCalendar('chk_date', '%Y-%m-%d', '12');">
檢驗報告 <input type="text" name="chk_report" value="{{$rowdata.chk_report}}">
</td>
</tr>
<tr style="background-color:white;text-align:center;">
<td style="background-color:#E6E9F9;">其他</td>
<td style="text-align:left;"><input type="checkbox" name="oth_chk[1]">有無施打流感疫苗 家屬有感冒症狀者<input type="text" name="oth_txt[0]" value="{{$oth_txt.0}}"> <input type="checkbox" name="oth_chk[2]">家屬是否出國</td>
</tr>
<tr style="background-color:white;text-align:center;">
<td style="background-color:#E6E9F9;">備註</td>
<td style="text-align:left;"><textarea name="oth_txt[1]" rows="3" cols="70">{{$oth_txt.1|br2nl}}</textarea></td>
</tr>
</table>
</td>
</tr>
</form>
</table>

{{include file="$SFS_TEMPLATE/footer.tpl"}}
