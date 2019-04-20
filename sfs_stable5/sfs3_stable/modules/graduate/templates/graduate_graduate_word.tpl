{{* $Id: graduate_graduate_word.tpl 5310 2009-01-10 07:57:56Z hami $ *}}
{{include file="$SFS_TEMPLATE/header.tpl"}}
{{include file="$SFS_TEMPLATE/menu.tpl"}}

{{dhtml_calendar_init}}
<script>
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
function tagall(name,status) {
  var i =0;

  while (i < document.myform.elements.length)  {
    if (document.myform.elements[i].id==name) {
      document.myform.elements[i].checked=status;
    }
    i++;
  }
}
function fillall(name,value) {
  var i =0;

  while (i < document.myform.elements.length)  {
    if (document.myform.elements[i].id==name) {
      document.myform.elements[i].value=value;
    }
    i++;
  }
}
</script>
<table border="0" cellspacing="1" cellpadding="2" width="100%" bgcolor="#cccccc">
<tr><td bgcolor="#FFFFFF">
<form name="myform" method="post" action="{{$smarty.server.PHP_SELF}}">
<table width="100%">
<tr>
<td>{{$year_seme_menu}} {{$class_year_menu}} {{if $smarty.post.year_seme}}{{$class_name_menu}} <select name="show_kind" onchange="this.form.submit()";>{{html_options options=$grad_kind_arr selected=$smarty.post.show_kind}}</select>{{/if}}</td>
</tr>
{{if $smarty.post.year_name}}
<tr>
<td>
<input type="submit" name="save" value="儲存">
<input type="button" value="全部畢業" onClick="javascript:tagall('grad',1);">
<input type="text" name="grad_date" id="grad_date" size="10" value={{$smarty.post.grad_date}}><input type="reset" value="選擇日期" onclick="return showCalendar('grad_date', '%Y-%m-%d', '12');"><input type="button" value="日期複製" onClick="javascript:fillall('P_date',document.myform.grad_date.value);">
<input type="text" name="grad_word" size="10" value={{$smarty.post.grad_word}}><input type="button" value="證書字複製" onClick="javascript:fillall('P_word',document.myform.grad_word.value);">
<input type="text" name="grad_num" size="10" value=""><input type="submit" name="start_num" value="證書號起始值">
</tr>
<tr>
<td>
<table border="0" cellspacing="1" cellpadding="4" bgcolor="#cccccc" class="main_body">
<tr bgcolor="#ddddff" align="center">
<td>班級<td>座號<td>姓名<td>成績<td>畢業<td>修業<td>證書日期<td>證書字<td>證書號</td>
</tr>
{{foreach from=$seme_class item=class_name key=sn name=sn}}
<tr bgcolor="#E1ECFF" align="left">
<td>{{$class_base.$class_name}}<input type="hidden" name="seme_class[{{$sn}}]" value={{$seme_class.$sn}}>
<td>{{$seme_num.$sn}}
<td>{{$stud_name.$sn}}
<td>{{$grad_score.$sn}}
<td><input type="radio" id="grad" name="sure_grad[{{$sn}}]" value="1" {{if $smarty.post.sure_grad.$sn=="1" || $grad_kind.$sn==1}}checked{{/if}}>
<input type="hidden" name="stud_id[{{$sn}}]" value={{$stud_id.$sn}}>
<td><input type="radio" name="sure_grad[{{$sn}}]" value="2"  {{if $smarty.post.sure_grad.$sn=="2" || $grad_kind.$sn==2}}checked{{/if}}>
<td><input type="text" id="P_date" name="P_date[{{$sn}}]" size="10" maxlength="10" value="{{if $smarty.post.P_date[$sn]}}{{$smarty.post.P_date[$sn]}}{{elseif $P_date.$sn}}{{$P_date.$sn}}{{/if}}">
<td><input type="text" id="P_word" name="P_word[{{$sn}}]" size="16" maxlength="40" value="{{if $smarty.post.P_word[$sn]}}{{$smarty.post.P_word[$sn]}}{{elseif $P_word.$sn}}{{$P_word.$sn}}{{/if}}">
<td><input type="text" id="P_num" name="P_num[{{$sn}}]" size="16" maxlength="40" value="{{if $smarty.post.grad_num!=""}}{{$smarty.post.grad_num+$smarty.foreach.sn.iteration-1|string_format:"%04d"}}{{elseif $smarty.post.P_num.$sn!=""}}{{$smarty.post.P_num.$sn}}{{elseif $P_num.$sn}}{{$P_num.$sn}}{{/if}}"></td>
</tr>
{{/foreach}}
</table>
</td>
</tr>
{{/if}}
</table>
</form>
</td></tr>
</table>
{{include file="$SFS_TEMPLATE/footer.tpl"}}